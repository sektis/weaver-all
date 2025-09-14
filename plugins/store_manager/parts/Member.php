<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Member extends StoreSchemaBase{

    protected $active_arr = array(1=>'활성화',2=>'비활성화');
    protected $columns = array(
        'active'=>"ENUM('1','2') NOT NULL Default 1",
        'is_admin'=>"TINYINT(1) NOT NULL DEFAULT 0",
        'is_manager'=>"TINYINT(1) NOT NULL DEFAULT 0",
        'is_ceo'=>"TINYINT(1) NOT NULL DEFAULT 0",
        'admin_memo'=>"text not null default ''",
        'mb_id' => "",
        'mb_password' => "",
        'mb_password_init' => "",
        'mb_name' => "",
        'mb_hp' => "",
        'mb_email' => "",
        'mb_id_form'=>''

    );

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row){
        $arr = array();
        $arr['active_text'] = $this->active_arr[$row['active']];
        $mb = get_member($row['mb_id']);
        $arr['is_cert'] = ($mb['mb_dupinfo']   && $mb['mb_certify']);
//        $arr['is_cert'] = true;
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

       if(!$data['mb_id'])return;

        if($config['cf_admin']==$data['mb_id']){
            alert('최고관리자 정보변경금지');
        }

        $mb= get_member($data['mb_id']);

        if($mb['mb_id']){
            if(!$wr_id){

                $write_table = $this->manager->get_write_table_name();
                $chk = sql_fetch("select wr_id from $write_table where mb_id='{$mb['mb_id']}'");

                if($chk['wr_id']){
                    alert('이미 존재하는 아이디입니다.');
//                    $data['wr_id'] = $wr_id= $chk['wr_id'];
                }

            }
            if(!$wr_id and $data['force']!=1 and ($data[$pkey]['is_admin']==1 or $data[$pkey]['is_manager']==1 or $data[$pkey]['is_ceo']==1)){
                alert('이미 존재하는 회원 아이디입니다.');
//                wv_json_exit(array('confirm'=>'회원테이블에 존재하는 아이디입니다.권한만 추가하시겠습니까?','confirm_data'=>array('force'=>1)));
            }
            $data['mw']='u';
        }else{
            if(!$data['mb_nick']){
                $data['mb_nick']=$data['mb_id']. date('YmdHis');
            }
        }

        $result = wv_write_member($data);
        if($result!==true){
            alert($result);
        }


        $mb= get_member($data['mb_id']);
        $data['mb_id']=$mb['mb_id'];
        $data['wr_name']=$mb['mb_name'];
        $data['wr_password']=$mb['mb_password'];


        $write_row = $manager->fetch_write_row($wr_id);
        if($wr_id and ($data['mb_id']!=$write_row['mb_id'])){
            alert('id는 변경할 수 없습니다.');
        }



    }

    public function before_delete(&$data,  $wr_id,$pkey,$manager ) {
        member_delete($data['mb_id']);
    }
}
