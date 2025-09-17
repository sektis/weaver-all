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
        'end' => "DATE NOT NULL",
        'last_modify' => "DATE NOT NULL",
        'status' => "ENUM('1','2','3') NOT NULL Default 1", // 1:진행,2:일시중지,3:종료
        'memo'=>'TEXT DEFAULT NULL',
        'first_manager_wr_id'=>'',
        'first_item_wr_id'=>'',
    );


    public function get_indexes(){
        return array(
            array()
        );
    }

}
