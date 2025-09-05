<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Contract extends StoreSchemaBase{

    protected $cont_pdt_type_arr = array(1=>'매장',2=>'포장');
    protected $columns = array(
        'mb_id' => "",
        'cont_pdt_type' => "TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=매장,2=포장'",
        'biz_num' => "char(10) NOT NULL DEFAULT '' COMMENT '사업자등록번호'",

    );

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row){
        $arr = array();
        $arr['cont_pdt_type_text'] = $this->cont_pdt_type_arr[$row['cont_pdt_type']];
        return $arr;
    }


    protected function get_member_options($where='1',$selected='',$fields='mb_id,mb_name'){
        global $g5;
        $options = '';
        $sql = " SELECT {$fields} from {$g5['member_table']} where {$where}";

        $result = sql_query($sql);
        while($row = sql_fetch_array($result)){
            $options.= option_selected($row['mb_id'],$selected,"{$row['mb_name']}({$row['mb_id']})");
        }

        return $options;
    }
}
