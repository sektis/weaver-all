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
        'tel_add' => "TEXT DEFAULT NULL",
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
        $first_image = array_values($row['image'])[0];

        $arr['main_image'] =  $first_image['path'];

        $arr['list_each'] =  function ($data) use($row) {
            return $this->store->store->render_part('list_each','view',array_merge($row,$data));
        };
        $arr['cert_each'] =  function ($data) use($row) {
            return $this->store->store->render_part('cert_each','view',array_merge($row,$data));
        };
        $arr['list_main'] =function ($data) {
            return $this->store->store->render_part('list_main','view',$data);
        };

        $arr['contract_non_free_list'] = function ()use($row) {

            $non_free_items = wv_get_keys_by_nested_value($this->store->contract->list,0,true,'item','is_free');
            uasort($non_free_items, function($a, $b) {
                return $a['contractitem_wr_id'] - $b['contractitem_wr_id'];
            });
            return $non_free_items;
        };

        return $arr;
    }

    public function get_category_index($name){
        return array_search($name,$this->category_arr);
    }
}
