<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Point extends StoreSchemaBase implements StoreSchemaInterface {

    protected $columns = array(
        'point_amount' => "INT(11) NOT NULL",
        'point_type' => "VARCHAR(20) NOT NULL", // 'earn', 'withdraw'
        'point_status' => "VARCHAR(20) DEFAULT 'completed'", // 'completed', 'pending', 'cancelled'
        'ref_manager' => "VARCHAR(50) DEFAULT NULL", // 참조 매니저명 (invite, visitcert 등)
        'ref_id' => "INT(11) DEFAULT NULL", // 참조 ID
        'point_memo' => "TEXT DEFAULT NULL",
        'point_date' => "DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );

    public function get_indexes() {
        return array(
            array(
                'name' => 'idx_point_date',
                'type' => 'INDEX',
                'cols' => array('point_date')
            ),
            array(
                'name' => 'idx_ref',
                'type' => 'INDEX',
                'cols' => array('ref_manager', 'ref_id')
            )
        );
    }

    public function column_extend($row) {
        $arr = array();

        // 포인트 타입 한글명
        $type_map = array(
            'earn' => '적립',
            'withdraw' => '출금'
        );
        $arr['point_type_ko'] = isset($type_map[$row['point_type']]) ? $type_map[$row['point_type']] : $row['point_type'];

        // ref_manager 기반 한글명
        $manager_map = array(
            'invite' => '친구초대',
            'visitcert' => '방문인증',
            'withdraw' => '출금신청',
            'admin' => '관리자지급',
            'cancel' => '취소환급'
        );
        $arr['point_reason_ko'] = isset($manager_map[$row['ref_manager']]) ? $manager_map[$row['ref_manager']] : $row['ref_manager'];

        // 상태 한글명
        $status_map = array(
            'completed' => '완료',
            'pending' => '대기중',
            'cancelled' => '취소됨'
        );
        $arr['point_status_ko'] = isset($status_map[$row['point_status']]) ? $status_map[$row['point_status']] : $row['point_status'];

        // 금액 표시
        $sign = ($row['point_type'] === 'earn') ? '+' : '-';
        $arr['point_amount_display'] = $sign . number_format(abs($row['point_amount'])) . '원';

        return $arr;
    }

    // 잔액 조회
    public function get_balance($mb_id) {
        $sql = "
            SELECT 
                SUM(CASE WHEN point_type = 'earn' AND point_status = 'completed' THEN point_amount ELSE 0 END) as earn_sum,
                SUM(CASE WHEN point_type = 'withdraw' AND point_status = 'completed' THEN point_amount ELSE 0 END) as withdraw_sum
            FROM {$this->manager->get_ext_table_name()}
            WHERE mb_id = '" . sql_escape_string($mb_id) . "'
        ";

        $row = sql_fetch($sql);
        $earn = isset($row['earn_sum']) ? (int)$row['earn_sum'] : 0;
        $withdraw = isset($row['withdraw_sum']) ? (int)$row['withdraw_sum'] : 0;

        return $earn - $withdraw;
    }
}