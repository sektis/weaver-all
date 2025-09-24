<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Contract extends StoreSchemaBase implements StoreSchemaInterface{

    public $list_part  = true;

    protected $columns = array(
        'contractmanager_wr_id'=> 'int(11) not null',
        'contractitem_wr_id'=> 'int(11) not null',
        'start' => "DATETIME NOT NULL",
        'end' => "DATETIME NOT NULL",
        'last' => "DATETIME NOT NULL",
        'status' => "ENUM('1','2','3') NOT NULL Default 1", // 1:진행,2:일시중지,3:종료
        'memo'=>'TEXT DEFAULT NULL',
        'service_content'=>'TEXT DEFAULT NULL',
        'service_time'=>'TEXT DEFAULT NULL',

    );
    protected $checkbox_fields = array('enabled');

    protected $status_text_array = array(
        '1'=>'진행 중',
        '2'=>'일시중지',
        '3'=>'종료',
    );

    protected $status_text_option_array = array(
        '1'=>'이용 중',
        '2'=>'미이용 중',
        '3'=>'미이용 중',
    );

    protected $status_text_config_array = array(
        '1'=>'제공중',
        '2'=>'미제공',
        '3'=>'미제공',
    );

    protected $status_style_array = array(
        '1'=>'background: #09f;',
        '2'=>'background: #fc5555;',
        '3'=>'background: #97989c;',
    );
    protected $status_change_style_array = array(
        '1'=>'background: #fc5555;',
        '2'=>'background: #29cc6a;',
        '3'=>'background: #29cc6a;',
    );
    protected $status_change_icon_array = array(
        '1'=>'<i class="fa-solid fa-pause"></i>',
        '2'=>'<i class="fa-solid fa-play"></i>',
        '3'=>'<i class="fa-solid fa-play"></i>',
    );
    protected $status_change_text_array = array(
        '1'=>'일시중지',
        '2'=>'재개',
        '3'=>'재개',
    );
    protected $status_change_value_array = array(
        '1'=>2,
        '2'=>1,
        '3'=>1,
    );
    protected $status_service_text_array = array(
        '1'=>'제공 중',
        '2'=>'일시중지',
        '3'=>'미제공',
    );


    public function get_indexes(){
        return array(
            array(
                'name' => 'unique_cont',
                'type' => 'UNIQUE',
                'cols' => array('wr_id','contractitem_wr_id')
            )
        );
    }

    public function column_extend($row,$all_row=array()){

        $arr = array();
        $cont_item = wv()->store_manager->made('contract_item')->get($row['contractitem_wr_id'])->contractitem;

//        dd(wv()->store_manager->made('member')->get($row['contractmanager_wr_id'])->wr_id);
        $arr['manager_name'] =  wv()->store_manager->made('member')->get($row['contractmanager_wr_id'])->mb_name;
        $arr['item_name'] =  $cont_item->item_name_montserrat;
        $arr['item'] =  $cont_item->row;
        $arr['status_text'] =  $this->status_text_array[$row['status']];
        $arr['status_text_option'] =  $this->status_text_option_array[$row['status']];
        $arr['status_text_config'] =  $this->status_text_config_array[$row['status']];
        $arr['status_html'] =  '<div class="fs-[14/22/-0.56/500/] wv-flex-box" style="height:var(--wv-31);padding:0 var(--wv-10);color:#fff;border-radius:var(--wv-4);'.$this->status_style_array[$row['status']].'">'.$arr['status_text'].'</div>';
        $arr['status_service_html'] =  '<div class="flex px-[9px] py-[4px] justify-center items-center gap-[10px] rounded-[4px] fs-[12/17/-0.48/500/]"
 style="height:var(--wv-25);padding:var(--wv-4) var(--wv-9);border-radius:var(--wv-4);
 color:'.($row['status']==1?$cont_item->row['color_type']['text']:'#97989C').';
 background-color:'.($row['status']==1?$cont_item->row['color_type']['bg']:'#f9f9f9').'" >'.$this->status_service_text_array[$row['status']].'</div>';
        $arr['memo_list'] =  implode('<br>',array_column($row['memo'],'text'));

        if(isset($row['service_time']) && !empty($row['service_time'])){
            $arr['service_time_summary'] = generate_time_summary($row['service_time']);
            $arr['service_time_list'] = generate_time_list($row['service_time']);
        } else {
            $arr['service_time_summary'] = array();
            $arr['service_time_list'] = array();
        }


        return $arr;
    }

    public function is_new(&$data,$pkey,$col) {
        if($col=='memo'){
            $data['date']=date('Y-m-d h:i:s');
        }
        if(!$data['start']){
            $data['start']=date('Y-m-d h:i:s');
        }

    }
}
