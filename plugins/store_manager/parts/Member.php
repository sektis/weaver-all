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
        'bank_name'=>"VARCHAR(255) DEFAULT NULL",
        'ban_account_number'=>"VARCHAR(255) DEFAULT NULL",
        'mb_id' => "",
        'mb_password' => "",
        'mb_password_init' => "",
        'mb_name' => "",
        'mb_hp' => "",
        'mb_email' => "",
        'mb_id_form'=>'',
        'member_form'=>'',
        'ceo_form'=>'',
        'manager_form'=>'',
    );

    public function get_indexes(){
        return array(
            array()
        );
    }


    public function column_extend($row,$all_row=array()){
        $arr = array();
        $arr['bank_info'] = $row['bank_name'].' | '.$row['bank_account_number'];
        $arr['active_text'] = $this->active_arr[$row['active']];
        $mb = get_member($row['mb_id']);
        $arr['is_cert'] = ($mb['mb_dupinfo']   && $mb['mb_certify']);
//        $arr['is_cert'] = true;
        return $arr;
    }




    public function before_set(&$data) {
        // 좌표 유효성 검사
        global $config,$is_admin,$member;


        $can_changer_member_password=false;
        if($is_admin){
            $can_changer_member_password=true;
        }

       if(!$data['mb_id'])return;
        $wr_id = $data['wr_id'];
       if($wr_id){
           $write_row = $this->manager->fetch_write_row($wr_id);

           if( $data['mb_id']!=$write_row['mb_id']){
//            dd($data['mb_id'].'++'.$write_row['mb_id']);
               alert('id가 일치하지 않습니다.');
           }
           if($data['mb_id']==$config['cf_admin']){
               alert('최고관리자 정보 변경 금지');
           }

           if($data['mb_password_new']){
                //
               if(!$can_changer_member_password){
                   if(!check_password($data['mb_password'],$write_row['mb_mb_password'])){
                       alert('현재 비밀번호 불일치');
                   }
                   if($data['mb_password_new']!==$write_row['mb_password_re']){
                       alert('새 비밀번호 확인 불일치');
                   }

               }


               $data['mb_password'] = $data['mb_password_new'];
           }


       }else{
           $data['mb_password'] = $data['mb_password_new'];
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






    }

    public function before_delete(&$data) {
        member_delete($data['mb_id']);
    }
}
