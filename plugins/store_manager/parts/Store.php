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
        'category_list'=>''
    );

    protected $image_max_count=8;

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row){
        $arr = array();
        $arr['category_text'] = $this->category_arr[$row['category']];
        $arr['category_icon'] =  $this->manager->plugin_url.'/img/category_list/small/'.$row['category'].'.png';
        $arr['main_image'] =  $row['images'][0]['path'];
        return $arr;
    }

    public function get_category_index($name){
        return array_search($name,$this->category_arr);
    }
}
