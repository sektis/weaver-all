<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Contractitem extends StoreSchemaBase{


    protected $columns = array(
        'is_free' => "TINYINT(1) NOT NULL DEFAULT 0",
        'use_schedule' => "TINYINT(1) NOT NULL DEFAULT 0",
        'name' => "VARCHAR(255) DEFAULT NULL",
        'icon'=>'TEXT DEFAULT NULL',
        'desc_option' => "TEXT DEFAULT NULL",
        'desc_list' => "VARCHAR(255) DEFAULT NULL",
        'call_num'=>"VARCHAR(255) DEFAULT NULL",
        'color_type'=>"",
        'use_intro' => "TINYINT(1) NOT NULL DEFAULT 0",

        'intro'=>'TEXT DEFAULT NULL',
        'item_form'=>'',

    );

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row){
        $arr = array();

        return $arr;
    }


}
