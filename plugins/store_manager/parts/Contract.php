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
    private static $is_syncing_service = false;

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
            $arr['service_time_list'] = generate_time_list($row['service_time']);
            $arr['service_time_group'] = generate_time_grouped($row['service_time'],1);
        } else {
            $arr['service_time_list'] = array();
            $arr['service_time_group'] = array();
        }





        return $arr;
    }

    public function is_new(&$data,$col) {
        if($col=='memo'){
            $data['date']=date('Y-m-d h:i:s');
        }
        if(!$data['start']){
            $data['start']=date('Y-m-d h:i:s');
        }

    }



    public function after_set(&$data) {
        if (self::$is_syncing_service) return;
        if (!isset($data['contract'])) return;

        $manager = $this->get_manager();

        foreach ($data['contract'] as $contract_id => $contract_item) {
            if (!isset($contract_item['id'])) {
                continue;
            }

            $service_time = null;

            if (isset($contract_item['service_time'])) {
                if (is_string($contract_item['service_time'])) {
                    $service_time = wv_base64_decode_unserialize($contract_item['service_time']);
                } elseif (is_array($contract_item['service_time'])) {
                    $service_time = $contract_item['service_time'];
                }
            }

            if (!$service_time || !is_array($service_time)) {
                continue;
            }

            self::$is_syncing_service = true;

            try {
                $wr_id = $data['wr_id'];
                $relation_wr_id = $contract_item['id'];

                $existing_list = $manager->get($wr_id)->timesearch->list;
                $updated_list = array();

                foreach ($existing_list as $item) {
                    if ($item['type'] !== 'service' ||
                        (int)$item['relation_wr_id'] !== (int)$relation_wr_id) {
                        $updated_list[] = $item;
                    }
                }

                $service_data = $manager->timesearch->convert_time_array_to_data(
                    $service_time,
                    'service',
                    true
                );

                foreach ($service_data as &$item) {
                    $item['relation_wr_id'] = $relation_wr_id;
                }

                $this->merge_timesearch_service_data(
                    $updated_list,
                    $existing_list,
                    $service_data,
                    $relation_wr_id
                );

                $post_data = array(
                    'wr_id' => $wr_id,
                    'timesearch' => $updated_list
                );

                $manager->set($post_data);

            } finally {
                self::$is_syncing_service = false;
            }
        }
    }

    private function merge_timesearch_service_data(&$updated_list, $existing_list, $new_data, $relation_wr_id) {
        foreach ($new_data as $new_item) {
            $matched = false;

            foreach ($existing_list as $existing_item) {
                if ($existing_item['type'] === 'service' &&
                    (int)$existing_item['relation_wr_id'] === (int)$relation_wr_id &&
                    $existing_item['day_of_week'] === $new_item['day_of_week']) {
                    $updated_list[] = array_merge($existing_item, $new_item);
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $updated_list[] = $new_item;
            }
        }
    }
}
