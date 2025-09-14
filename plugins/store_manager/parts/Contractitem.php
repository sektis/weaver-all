<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Contractitem extends StoreSchemaBase{


    protected $columns = array(
        'name' => "VARCHAR(255) DEFAULT NULL",
        'desc' => "VARCHAR(255) DEFAULT NULL",
        'icon'=>'TEXT DEFAULT NULL',
        'item_form'=>''
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
