<?php
namespace weaver;

require __DIR__.'/php-sql-parser.php';

use PHPSQLParser;

class SqlParser extends Plugin {

    private $parser;


    protected function __construct() {
        $this->parser = new PHPSQLParser();
        $replace_hook_check = wv_get_code_from_file('run_replace(\'sql_query_before_replace\'',G5_LIB_PATH.'/common.lib.php');

        if(!count($replace_hook_check)){
            $this->error('sql_query_before_replace Hook이 없습니다. <br>'.PHP_EOL.G5_LIB_PATH.'/common.lib.php 파일  $sql = trim($sql); 코드 위에 아래 코드를 추가하세요. <br> $sql = run_replace(\'sql_query_before_replace\', $sql);',2);
        }
    }

    public function parse($sql){
        $this->parser->parse($sql);
        return $this->parser->parsed;
    }




    public function get_field($parsed,$type,$field){
        $type = strtoupper($type);

        if($type=='SET'){

            foreach ($parsed[$type] as $key=>$val){
                if($val['expr_type']!='expression')continue;
                if($val['sub_tree'][0]['base_expr']!=$field)continue;

                foreach ($val['sub_tree']  as $tree){
                    if($tree['expr_type']=='const'){
                        return trim($tree['base_expr'],"'");
                    }
                }

            }
        }elseif($type=='WHERE'){
            foreach ($parsed[$type] as $key=>$val){
                if(!($val['expr_type']=='colref' and  $val['base_expr']==$field))continue;
                for($i=1;$i<=4;$i++){
                    if($parsed[$type][$key+$i]['expr_type']!='const')continue;
                    return trim($parsed[$type][$key+$i]['base_expr'],"'");
                }
            }
        }



        return false;
    }

    public function diff($diff,$org,$ignore_sql=''){

        if(!is_array($diff)){
            $diff = $this->parse($diff);
        }


        if(!is_array($org)){
            $org = $this->parse($org);
        }

        $result = wv_array_recursive_diff($diff,$org,'',true);

        $ignore_sql = array_filter((array)$ignore_sql);
        if(count($ignore_sql)){

            foreach ($ignore_sql as $i_sql){
                $ignore_parsed = $this->parse($i_sql);
                $ignore_result = wv_array_recursive_diff($ignore_parsed,$org,',',true);
                if(empty($ignore_result) and empty($result))return false;
            }

        }
        return empty($result);
    }

    public function board_list_extend($sql,$bo_table,$add_where=array(),$add_join=array(),$add_order='',$group_filed=''){
        global $board,$g5,$total_count,$total_page,$page_rows,$page,$from_record,$sql_search,$sql_order,$is_search_bbs,$sst,$sod;

        $write_table = $g5['write_prefix'].$bo_table;
        if(!(wv_info('dir')=='bbs' and wv_info('file')=='board' and $board['bo_table']==$bo_table and strpos($sql,"from {$write_table}")!==false))return $sql;

        $diff = $this->diff("select  from {$write_table}",$sql,array("select MIN(wr_num) as min_wr_num","select wr_id, wr_subject, wr_datetime","where wr_parent = '1' and wr_is_comment = 1"));
        if(!$diff)return $sql;



        $sql_where_array = array();

        $sql_where = '';
        $sql_group = '';
        $sql_distinct = 'wr_parent';
        if($sql_search){
            $sql_search = trim($sql_search);
            $sql_search = ltrim($sql_search,'and');
            $sql_where_array[] = $sql_search;
        }


        $join_sql = '';
        $join_arr = array();
        if(count($add_join)){
            foreach ($add_join as $val){
                $join_arr[] = $val;
            }
            $join_sql = implode(" ",$join_arr);
        }

        $sql_common = " {$write_table} {$join_sql}";


        if($group_filed){
            $sql_group = " group by {$group_filed} ";
            $sql_distinct = $group_filed;
        }

        if(count($add_where)){
            $sql_where_array = array_merge_recursive($sql_where_array,$add_where);
        }

        if(count($sql_where_array)){
            $sql_where = " where ".implode(' and ',$sql_where_array);
        }

        $replace_sql = "SELECT COUNT(DISTINCT {$sql_distinct}) AS `cnt` FROM ".$sql_common.$sql_where;

        $row = sql_fetch($replace_sql,1);
        $total_count = $row['cnt'];
        $total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
        $from_record = ($page - 1) * $page_rows; //



        $sql_order = " order by {$sst} {$sod} ";

        if(!$_REQUEST['sst'] and $add_order){
            $sql_order = $add_order;
        }


        $replace_sql = "select *  from {$sql_common} {$sql_where} {$sql_group} {$sql_order}  limit {$from_record}, $page_rows";
        if(WV_DEBUG){
//                dd($replace_sql);
        }
        return $replace_sql;
    }



}
SqlParser::getInstance();