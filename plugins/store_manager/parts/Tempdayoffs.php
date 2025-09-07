<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Tempdayoffs extends StoreSchemaBase implements StoreSchemaInterface{

    public $list_part  = true;

    protected $columns = array(
        'start_date' => "DATE NOT NULL",
        'end_date' => "DATE NOT NULL",
    );


    public function get_indexes(){
        return array(
            array()
        );
    }

}
