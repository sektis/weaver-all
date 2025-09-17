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

    );

    protected $image_max_count=8;

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row,$all_row=array()){
        $arr = array();
        $arr['category_text'] = wv()->store_manager->made('store_category')->get($row['category_wr_id'])->storecategory->name;
        $arr['category_icon'] =  $this->manager->plugin_url.'/img/category_list/small/'.$row['category'].'.png';
        $first_image = reset($row['image']);
        $arr['main_image'] =  $first_image['path'];
        if(isset($row['list_each'])){
            $row['wr_id'] = $all_row['wr_id'];
            $arr['list_each'] =  $this->manager->store->render_part('list_each','view',array('row'=>$row));
        }
        if(isset($row['list_main'])){
            $row['wr_id'] = $all_row['wr_id'];
            $arr['list_main'] =  $this->manager->store->render_part('list_main','view',array('row'=>$row));
        }


        return $arr;
    }

    public function get_category_index($name){
        return array_search($name,$this->category_arr);
    }
}
