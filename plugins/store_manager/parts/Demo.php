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

        // column_extend 내 render_part는 메서드내에서 클로저로 변환
        $arr['test1'] =  $this->store->store->render_part('list_each','view');

        // 지연평가
        $arr['test2'] =  function() use ($row) {return $this->store->demo->test1;};
        return $arr;
    }

    public function is_new($col,&$curr,$prev,&$data,$node){}
    public function is_delete($col,&$curr,$prev,&$data,$node){}
    public function is_change($col,&$curr,$prev,&$data,$node){}
    public function on_save($col,&$curr,$prev,&$data,$node){}

    public function before_set(&$data) {}
    public function after_set(&$data) {}
    public function before_delete(&$data) {}
    public function after_delete(&$data) {}
}
