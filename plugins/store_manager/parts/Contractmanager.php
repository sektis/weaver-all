<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Contractmanager extends StoreSchemaBase{

    protected $cont_pdt_type_arr = array(1=>'매장',2=>'포장');
    protected $columns = array(

        'mb_id' => "",
        'manager_id_form'=>''

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

    public function before_set($data,  $wr_id, $part_key, $manager) {
        // 좌표 유효성 검사

       if(!$wr_id){
           $write_table= $this->manager->get_write_table_name();
           $row = sql_fetch("select mb_id from $write_table where mb_id='{$data['mb_id']}'");
           if($row['mb_id']){
               alert('이미 등록된 아이디입니다.');
           }
       }

    }
}
