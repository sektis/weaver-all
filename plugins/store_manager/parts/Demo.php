<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Demo extends StoreSchemaBase implements StoreSchemaInterface{

    protected $columns = array(

        'name' => "VARCHAR(255) DEFAULT NULL",
        'image'=>'TEXT DEFAULT NULL',
        'is_on' => "TINYINT(1)  DEFAULT 0",
        'form' => "",
    );


    protected $image_max_count=8;

    public function get_indexes(){
        return array(
            array(
                'name' => 'index_name',
                'type' => 'INDEX',
                'cols' => array('name')
            ),
            array(
                'name' => 'unique_name',
                'type' => 'UNIQUE',
                'cols' => array('name')
            )
        );
    }

    public function column_extend($row,$all_row=array()){
        $arr = array();
        $arr['image_first'] = '';
        return $arr;
    }

    public function before_set(&$data) {

    }
}
