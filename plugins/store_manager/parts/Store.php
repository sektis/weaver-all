<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Store extends StoreSchemaBase implements StoreSchemaInterface{

    protected $columns = array(

        'name' => "VARCHAR(255) DEFAULT NULL",
        'image'=>'TEXT DEFAULT NULL',
        'category_wr_id' => "int(11) not null",
        'tel' => "VARCHAR(255) DEFAULT NULL",
        'notice' => "TEXT DEFAULT NULL",
        'biz_num' => "char(10) NOT NULL DEFAULT '' COMMENT '사업자등록번호'",

        'mb_id' => "",
        'category_icon'=>'',
        'list_each'=>'',
        'service'=>'',
        'list_main'=>'',
        'ceo_main'=>'',
        'category_list'=>'',
    );


    protected $image_max_count=8;

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row){

        $arr = array();
        $cate_item = wv()->store_manager->made('store_category')->get($row['category_wr_id'])->storecategory;

        $arr['category_item'] = $cate_item->row;

//        $arr['category_icon'] =  $this->manager->plugin_url.'/img/category_list/small/'.$row['category'].'.png';
        $first_image = $row['image'][0];

        $arr['main_image'] =  $first_image['path'];

        $arr['list_each'] =  function () use($row){
            $this->store->store->render_part('list_each','view');
        };
        $arr['list_main'] =function ()use($row){
            $this->store->store->render_part('list_main','view',array('row'=>$row));
        };



        return $arr;
    }

    public function get_category_index($name){
        return array_search($name,$this->category_arr);
    }
}
