<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Storecategory extends StoreSchemaBase{


    protected $columns = array(
        'name' => "VARCHAR(255) DEFAULT NULL",
        'icon' => "TEXT DEFAULT NULL",
        'icon_main' => "TEXT DEFAULT NULL",
        'use'=>'TINYINT(1) NOT NULL DEFAULT 1',
        'item_form'=>'',

    );

    protected $checkbox_fields = array('use');

    public function get_indexes(){
        return array(
            array(
                'name' => 'unique_name',
                'type' => 'UNIQUE',
                'cols' => array('name')
            )
        );
    }

    public function column_extend($row){
        $arr = array();

        return $arr;
    }


}
