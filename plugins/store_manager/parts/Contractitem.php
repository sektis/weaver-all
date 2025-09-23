<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Contractitem extends StoreSchemaBase{


    protected $columns = array(
        'is_free' => "TINYINT(1) NOT NULL DEFAULT 0",
        'use_schedule' => "TINYINT(1) NOT NULL DEFAULT 0",
        'name' => "VARCHAR(255) DEFAULT NULL",
        'icon'=>'TEXT DEFAULT NULL',
        'desc_option' => "TEXT DEFAULT NULL",
        'desc_list' => "VARCHAR(255) DEFAULT NULL",
        'call_num'=>"VARCHAR(255) DEFAULT NULL",
        'color_type'=>"VARCHAR(255) DEFAULT NULL",
        'use_intro' => "TINYINT(1) NOT NULL DEFAULT 0",

        'intro'=>'TEXT DEFAULT NULL',
        'item_form'=>'',

    );

    protected $checkbox_fields = array('is_free','use_schedule','use_intro');

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row){
        $arr = array();
        $arr['item_name_montserrat'] = $this->montserrat_change($row['name']);
        $arr['color_type_bg'] =  "background-color:{$row['color_type']['bg']};";
        $arr['color_type_text'] =  "color:{$row['color_type']['text']};";
        return $arr;
    }
    public function before_set(&$data) {
//        dd($data);
    }

    public function montserrat_change($text){
        $text = str_replace('DUM','<span class="ff-montserrat fw-700">DUM</span>',$text);
        $text = str_replace('&','<span class="ff-montserrat fw-700">&</span>',$text);
        return $text;
    }


}
