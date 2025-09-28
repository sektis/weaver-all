<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Invite extends StoreSchemaBase implements StoreSchemaInterface{

    protected $columns = array(
        'invite_member_wr_id' => "INT(11) NOT NULL",
        'invite_code' => "VARCHAR(50) DEFAULT NULL",
        'invite_date' => "DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );

    protected $checkbox_fields = array('reward_given');

    public function get_indexes(){
        return array(
            array(
                'name' => 'idx_invite_member_wr_id',
                'type' => 'INDEX',
                'cols' => array('invite_member_wr_id')
            ),
            array(
                'name' => 'idx_invite_date',
                'type' => 'INDEX',
                'cols' => array('invite_date')
            ),
            array(
                'name' => 'unique_invite_code',
                'type' => 'UNIQUE',
                'cols' => array('invite_code')
            )
        );
    }

    public function column_extend($row){
        $arr = array();

        // 초대한 사람 정보 확장 (mb_id 기반)
        if(isset($row['mb_id']) && !empty($row['mb_id'])){
            $member_manager = wv()->store_manager->made('member');
            $member_table = $member_manager->get_write_table_name();
            $inviter_row = sql_fetch("SELECT wr_id FROM {$member_table} WHERE mb_id='{$row['mb_id']}'");
            if($inviter_row){
                $inviter = $member_manager->get($inviter_row['wr_id']);
                if($inviter){
                    $arr['inviter_name'] = $inviter->member->mb_name;
                    $arr['inviter_nick'] = $inviter->member->mb_nick;
                }
            }
        }

        // 초대받은 사람 정보 확장
        if(isset($row['invitee_member_wr_id']) && $row['invitee_member_wr_id'] > 0){
            $invitee = wv()->store_manager->made('member')->get($row['invitee_member_wr_id']);
            if($invitee){
                $arr['invitee_name'] = $invitee->member->mb_name;
                $arr['invitee_nick'] = $invitee->member->mb_nick;
            }
        }

        // 상태 한글명
        $status_map = array(
            'pending' => '대기중',
            'accepted' => '수락됨',
            'rejected' => '거절됨',
            'expired' => '만료됨'
        );
        if(isset($row['invite_status'])){
            $arr['invite_status_ko'] = isset($status_map[$row['invite_status']]) ? $status_map[$row['invite_status']] : $row['invite_status'];
        }

        return $arr;
    }
}