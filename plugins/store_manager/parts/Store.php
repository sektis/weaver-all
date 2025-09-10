<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Store extends StoreSchemaBase implements StoreSchemaInterface{
    protected $category_arr = array(1=>'한식',2=>'중식',3=>'일식',4=>'양식',5=>'아시아',6=>'패스트푸드',7=>'고깃집',8=>'분식',9=>'술집',10=>'카페',11=>'반찬',
        12=>'족발&보쌈',13=>'뷰티&헬스',14=>'체험'
    ,15=>'레저스포츠',16=>'학원',17=>'숙박',18=>'배달',19=>'기타',20=>'게시보류');
    protected $columns = array(

        'name' => "VARCHAR(255) DEFAULT NULL",
        'image'=>'TEXT DEFAULT NULL',
        'category' => "TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '카테고리'",
        'tel' => "VARCHAR(255) DEFAULT NULL",
        'notice' => "TEXT DEFAULT NULL",
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
        $arr['category_text'] = $this->category_arr[$row['category']];
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
