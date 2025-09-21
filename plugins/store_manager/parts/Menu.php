<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Menu extends StoreSchemaBase implements StoreSchemaInterface{

    public $list_part  = true;

    protected $columns = array(
        'name' => "VARCHAR(255) DEFAULT NULL",
        'prices'=>'TEXT DEFAULT NULL',
        'images'=>'TEXT DEFAULT NULL',
        'desc'=>'TEXT DEFAULT NULL',
        'is_main' => "TINYINT(1) NOT NULL DEFAULT 0",
        'use_eum' => "TINYINT(1) NOT NULL DEFAULT 0",
        'inline_form'=>'',
    );

    protected $checkbox_fields = array('is_main','use_eum');

    protected $image_max_count=8;

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row){


        $arr = array();
//        $cate_item = wv()->store_manager->made('store_category')->get($row['category_wr_id'])->storecategory;

        if(isset($row['test'])){
            $arr['test'] = 'dasdsa';
        }



        return $arr;
    }


}
