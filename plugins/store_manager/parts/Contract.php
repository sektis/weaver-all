<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Contract extends StoreSchemaBase implements StoreSchemaInterface{

    public $list_part  = true;

    protected $columns = array(
        'contractmanager_wr_id'=> 'int(11) not null',
        'contractitem_wr_id'=> 'int(11) not null',
        'start' => "DATE NOT NULL",
        'first_manager_wr_id'=>'',
        'first_item_wr_id'=>'',
    );


    public function get_indexes(){
        return array(
            array()
        );
    }

}
