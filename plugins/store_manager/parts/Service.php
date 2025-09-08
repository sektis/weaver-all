<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Service extends StoreSchemaBase implements StoreSchemaInterface{

    public $list_part  = true;

    protected $columns = array(
        'desc'=> 'TEXT DEFAULT NULL',
        'cycle' => " VARCHAR(10) not null",
        'target' => " VARCHAR(10) not null",
    );


    public function get_indexes(){
        return array(
            array()
        );
    }

}
