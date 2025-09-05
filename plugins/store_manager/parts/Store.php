<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Store extends StoreSchemaBase implements StoreSchemaInterface{

    protected $category_arr = array(1=>'한식',2=>'중식',3=>'일식',4=>'양식',5=>'아시아',6=>'패스트푸드',7=>'고깃집',8=>'분식',9=>'술집',10=>'카페',11=>'반찬');
    protected $columns = array(

        'name' => "VARCHAR(255) DEFAULT NULL",
        'image'=>'TEXT DEFAULT NULL',
        'category' => "TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '카테고리'",
        'tel' => "VARCHAR(255) DEFAULT NULL",
        'notice' => "TEXT DEFAULT NULL",
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
        return $arr;
    }
}
