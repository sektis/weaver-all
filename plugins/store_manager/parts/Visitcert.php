<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Visitcert extends StoreSchemaBase implements StoreSchemaInterface{

    protected $columns = array(
        'store_wr_id' => "INT(11) NOT NULL",
        'image' => "text DEFAULT NULL",
    );



    public function get_indexes(){
        return array(
            array(
                'name' => 'idx_store_wr_id',
                'type' => 'INDEX',
                'cols' => array('store_wr_id')
            ),
        );
    }

    public function column_extend($row){
        $arr = array();



        return $arr;
    }
}