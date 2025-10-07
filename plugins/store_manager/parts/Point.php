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
                'name' => 'idx_mb_id',
                'type' => 'INDEX',
                'cols' => array('mb_id')
            ),
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

    // 포인트 적립
    public function add_point($mb_id, $amount, $ref_manager, $memo = '', $ref_id = 0) {
        $data = array(
            'mb_id' => $mb_id,
            'point' => array(
                'point_amount' => abs($amount),
                'point_type' => 'earn',
                'point_status' => 'completed',
                'ref_manager' => $ref_manager,
                'ref_id' => $ref_id,
                'point_memo' => $memo,
                'point_date' => date('Y-m-d H:i:s')
            )
        );

        return $this->store_manager->set($data);
    }

    // 출금 신청
    public function request_withdraw($mb_id, $amount, $memo = '', $bank_info = array()) {
        $balance = $this->get_balance($mb_id);

        if ($balance < $amount) {
            return array('error' => '잔액이 부족합니다.');
        }

        $full_memo = $memo;
        if (is_array($bank_info) && count($bank_info)) {
            $full_memo .= "\n은행: " . ($bank_info['bank'] ?? '');
            $full_memo .= "\n계좌번호: " . ($bank_info['account'] ?? '');
            $full_memo .= "\n예금주: " . ($bank_info['holder'] ?? '');
        }

        $data = array(
            'mb_id' => $mb_id,
            'point' => array(
                'point_amount' => abs($amount),
                'point_type' => 'withdraw',
                'point_status' => 'pending',
                'ref_manager' => 'withdraw',
                'ref_id' => 0,
                'point_memo' => $full_memo,
                'point_date' => date('Y-m-d H:i:s')
            )
        );

        return $this->store_manager->set($data);
    }

    // 출금 승인
    public function approve_withdraw($wr_id) {
        $row = $this->store_manager->get($wr_id);

        if ($row->point->point_status !== 'pending') {
            return array('error' => '처리할 수 없는 상태입니다.');
        }

        $data = array(
            'wr_id' => $wr_id,
            'point' => array(
                'point_status' => 'completed'
            )
        );

        return $this->store_manager->set($data);
    }

    // 출금 취소
    public function cancel_withdraw($wr_id, $reason = '') {
        $row = $this->store_manager->get($wr_id);

        if ($row->point->point_status !== 'pending') {
            return array('error' => '처리할 수 없는 상태입니다.');
        }

        $data = array(
            'wr_id' => $wr_id,
            'point' => array(
                'point_status' => 'cancelled',
                'point_memo' => $row->point->point_memo . "\n취소사유: " . $reason
            )
        );

        return $this->store_manager->set($data);
    }

    // 잔액 조회
    public function get_balance($mb_id) {
        $sql = "
            SELECT 
                SUM(CASE WHEN point_type = 'earn' AND point_status = 'completed' THEN point_amount ELSE 0 END) as earn_sum,
                SUM(CASE WHEN point_type = 'withdraw' AND point_status = 'completed' THEN point_amount ELSE 0 END) as withdraw_sum
            FROM {$this->store_manager->get_ext_table_name()}
            WHERE mb_id = '" . sql_escape_string($mb_id) . "'
        ";

        $row = sql_fetch($sql);
        $earn = isset($row['earn_sum']) ? (int)$row['earn_sum'] : 0;
        $withdraw = isset($row['withdraw_sum']) ? (int)$row['withdraw_sum'] : 0;

        return $earn - $withdraw;
    }
}