<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Biz extends StoreSchemaBase implements StoreSchemaInterface{


    protected $columns = array(
        'open_time'=>'TEXT DEFAULT NULL',
        'break_time' => "TEXT DEFAULT NULL",
        'is_holiday_off' => "TINYINT(1)  DEFAULT 0",
        'parking'=>'varchar(255) not null'
    );
    protected $checkbox_fields = array('is_holiday_off');

    protected $parking_max_char=20;

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row,$all_row=array()){
        $arr = array();
        if(isset($row['open_time']) && !empty($row['open_time'])){
            $arr['open_time_summary'] = generate_time_summary($row['open_time']);
            $arr['open_time_list'] = generate_time_list($row['open_time']);
        } else {
            $arr['open_time_summary'] = array();
            $arr['open_time_list'] = array();
        }

        if(isset($row['break_time']) && !empty($row['break_time'])){
            $arr['break_time_summary'] = generate_time_summary($row['break_time']);
            $arr['break_time_list'] = generate_time_list($row['break_time']);
        } else {
            $arr['break_time_summary'] = array();
            $arr['break_time_list'] = array();
        }

        return $arr;
    }


}
