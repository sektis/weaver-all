<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Basic extends StoreSchemaBase implements StoreSchemaInterface{

    protected $columns = array(
        'wr_id' => "",
    );

    public function get_indexes(){
        return array(
            array()
        );
    }
}
