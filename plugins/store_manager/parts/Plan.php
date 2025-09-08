<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Plan extends StoreSchemaBase implements StoreSchemaInterface{

    protected $columns = array(

        'name' => "VARCHAR(255) DEFAULT NULL",
        'type'=>"TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=구독플랜,2=프리플랜'",
        'start' => "DATE NOT NULL",

    );



    public function get_indexes(){
        return array(
            array()
        );
    }
//
//    public function column_extend($row,$all_row=array()){
//        $arr = array();
//        $arr['category_text'] = $this->category_arr[$row['category']];
//        $arr['category_icon'] =  $this->manager->plugin_url.'/img/category_list/small/'.$row['category'].'.png';
//        $first_image = reset($row['image']);
//        $arr['main_image'] =  $first_image['path'];
//        if(isset($row['list_each'])){
//            $row['wr_id'] = $all_row['wr_id'];
//            $arr['list_each'] =  $this->manager->store->render_part('list_each','view',array('row'=>$row));
//        }
//
//
//        return $arr;
//    }

    public function get_category_index($name){
        return array_search($name,$this->category_arr);
    }
}
