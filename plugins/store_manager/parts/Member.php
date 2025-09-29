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
        'invite_code'=>"VARCHAR(7) DEFAULT NULL",
        'search_store_history'=>"text not null default ''",
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
            array(
                'name' => 'unique_invite_code',
                'type' => 'UNIQUE',
                'cols' => array('invite_code')
            )
        );
    }


    public function column_extend($row,$all_row=array()){
        $arr = array();
        $arr['bank_info'] = $row['bank_name'].' | '.$row['bank_account_number'];
        $arr['active_text'] = $this->active_arr[$row['active']];
        $mb = get_member($row['mb_id']);
        $arr['is_cert'] = ($mb['mb_dupinfo']   && $mb['mb_certify']);

        // invite_code가 없으면 생성
        if(empty($row['invite_code']) && isset($row['wr_id']) && $row['wr_id'] > 0){
            $invite_code = $this->generate_unique_invite_code();
            if($invite_code){
                $ext_table = $this->manager->get_ext_table_name();
                $physical_col = $this->manager->get_physical_col('member', 'invite_code');
                sql_query("UPDATE {$ext_table} SET {$physical_col} = '{$invite_code}' WHERE wr_id = '{$row['wr_id']}'");
                $arr['invite_code'] = $invite_code;
            }
        } else {
            $arr['invite_code'] = $row['invite_code'];
        }

//        $arr['is_cert'] = true;
        return $arr;
    }


    public function is_new($wr_id, $data){
        // 새 회원 생성시 invite_code 생성
        $invite_code = $this->generate_unique_invite_code();
        if($invite_code){
            $data['invite_code'] = $invite_code;
            $data['new_member'] = 1;
        }
    }
    public function after_set($wr_id, $data){
        if($data['new_member']){
            $ext_table = $this->manager->get_ext_table_name();;
            $check = sql_fetch("SELECT wr_id FROM {$ext_table} WHERE invite_code = '{$data['invite_code']}'");
            $data=array(
                'wr_id'=>'',
                'wr_content'=>'/',
                'mb_id'=>$row['mb_id'],
                'invite'=>array(
                        'invite_cote'=>$data['invite_code'],
                        'invite_member_wr_id'=>$check['wr_id']
                )
            );
            wv('store_manager')->made('invite')->set($data);
        }
    }


    /**
     * 유니크한 초대코드 생성 (영대문자+숫자 7자리)
     */
    protected function generate_unique_invite_code(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max_attempts = 100; // 무한루프 방지

        $ext_table = $this->manager->get_ext_table_name();
        $physical_col = $this->manager->get_physical_col('member', 'invite_code');

        for($attempt = 0; $attempt < $max_attempts; $attempt++){
            $code = '';
            for($i = 0; $i < 7; $i++){
                $code .= $chars[rand(0, strlen($chars) - 1)];
            }

            // 유니크 체크
            $check = sql_fetch("SELECT wr_id FROM {$ext_table} WHERE {$physical_col} = '{$code}'");
            if(!$check){
                return $code;
            }
        }

        return false; // 생성 실패
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
//               alert('최고관리자 정보 변경 금지');
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
