<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Storecategory extends StoreSchemaBase{


    protected $columns = array(
        'name' => "VARCHAR(255) DEFAULT NULL",
        'icon' => "TEXT DEFAULT NULL",
        'icon_main' => "TEXT DEFAULT NULL",

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
