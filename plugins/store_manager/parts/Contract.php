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
        'cont_form'=>'',
    );

    protected $status_text_array = array(
        '1'=>'진행 중',
        '2'=>'일시중지',
        '3'=>'종료',
    );
    protected $status_style_array = array(
        '1'=>'background: #09f;',
        '2'=>'background: #fc5555;',
        '3'=>'background: #97989c;',
    );

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row,$all_row=array()){

        $arr = array();
//        dd(wv()->store_manager->made('member')->get($row['contractmanager_wr_id'])->wr_id);
        $arr['manager_name'] =  wv()->store_manager->made('member')->get($row['contractmanager_wr_id'])->mb_name;
        $arr['item_name'] =  wv()->store_manager->made('contract_item')->get($row['contractitem_wr_id'])->contractitem->name;
        $arr['status_text'] =  $this->status_text_array[$row['status']];
        $arr['status_html'] =  '<div class="fs-[14/22/-0.56/500/] wv-flex-box" style="height:var(--wv-31);padding:0 var(--wv-10);color:#fff;border-radius:var(--wv-4);'.$this->status_style_array[$row['status']].'">'.$arr['status_text'].'</div>';



        return $arr;
    }
}
