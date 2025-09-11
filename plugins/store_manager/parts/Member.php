<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Member extends StoreSchemaBase{

    protected $cont_pdt_type_arr = array(1=>'매장',2=>'포장');
    protected $columns = array(

        'is_admin'=>"TINYINT(1) NOT NULL DEFAULT 0",
        'is_manager'=>"TINYINT(1) NOT NULL DEFAULT 0",
        'is_ceo'=>"TINYINT(1) NOT NULL DEFAULT 0",
        'mb_id' => "",
        'mb_id_form'=>''

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
        $sql = " SELECT {$fields} from {$g5['member_table']} where {$where} order by mb_no asc";

        $result = sql_query($sql);
        while($row = sql_fetch_array($result)){
            $options.= option_selected($row['mb_id'],$selected,"{$row['mb_name']}({$row['mb_id']})");
        }

        return $options;
    }

    public function before_set(&$data,  $wr_id,$pkey,$manager ) {
        // 좌표 유효성 검사
        global $config;
        if($config['cf_admin']==$data['mb_id']){
            alert('최고관리자 정보변경금지');
        }
        $write_row = $manager->fetch_write_row($wr_id);
        if($wr_id and ($data['mb_id']!=$write_row['mb_id'])){
            alert('id는 변경할 수 없습니다.');
        }




    }
}
