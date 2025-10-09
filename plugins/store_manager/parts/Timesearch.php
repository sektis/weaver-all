<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Timesearch extends StoreSchemaBase implements StoreSchemaInterface {

    public $list_part = true;

    protected $columns = array(
        'type' => "ENUM('open','break','service') NOT NULL",
        'relation_wr_id' => "INT(11) DEFAULT NULL",
        'day_of_week' => "VARCHAR(10) NOT NULL",
        'start_time' => "TIME DEFAULT NULL",
        'end_time' => "TIME DEFAULT NULL"
    );

    public function get_indexes() {
        return array(
            array('type'),
            array('relation_wr_id'),
            array('day_of_week'),
            array(
                'name' => 'idx_type_day',
                'type' => 'INDEX',
                'cols' => array('type', 'day_of_week')
            ),
            array(
                'name' => 'idx_relation',
                'type' => 'INDEX',
                'cols' => array('relation_wr_id', 'type')
            ),
            array(
                'name' => 'idx_store_type',
                'type' => 'INDEX',
                'cols' => array('wr_id', 'type', 'day_of_week')
            )
        );
    }

    public function column_extend($row, $all_row = array()) {
        $arr = array();

        $day_names = array(
            'mon' => '월',
            'tue' => '화',
            'wed' => '수',
            'thu' => '목',
            'fri' => '금',
            'sat' => '토',
            'sun' => '일'
        );

        $type_names = array(
            'open' => '영업시간',
            'break' => '휴게시간',
            'service' => '서비스시간'
        );

        $arr['day_name'] = isset($day_names[$row['day_of_week']]) ? $day_names[$row['day_of_week']] : '';
        $arr['type_text'] = isset($type_names[$row['type']]) ? $type_names[$row['type']] : '';
        $arr['is_active'] = !empty($row['start_time']) && !empty($row['end_time']);

        return $arr;
    }



}