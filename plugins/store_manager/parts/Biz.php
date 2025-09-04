<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Biz extends StoreSchemaBase implements StoreSchemaInterface{


    protected $columns = array(
        'open_time'=>'TEXT DEFAULT NULL',
        'break_time' => "TEXT DEFAULT NULL",
        'is_holiday_off' => "TINYINT(1) NOT NULL DEFAULT 0",
        'day_offs' => "TEXT DEFAULT NULL",
        'temp_day_off' => "TEXT DEFAULT NULL",
    );

    protected $image_max_count=8;

    public function get_indexes(){
        return array(
            array()
        );
    }
}
