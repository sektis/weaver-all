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
            $arr['service_time_list'] = generate_time_list($row['service_time'],1);
            $arr['service_time_group'] = generate_time_grouped($row['service_time'],1);
        } else {
            $arr['service_time_list'] = array();
            $arr['service_time_group'] = array();
        }





        return $arr;
    }

    public function is_new($col,&$curr,$prev,&$data,$node) {
        if($col=='contract/memo/n'){
            $curr['date']=date('Y-m-d h:i:s');
        }
        if($col=='contract/n'  ){

            $curr['start']=date('Y-m-d h:i:s');
        }

    }



    public function after_set(&$all_data) {
        // service_time이 수정된 contract가 있는지 확인
        if (!isset($all_data['contract']) || !is_array($all_data['contract'])) {
            return;
        }

        $wr_id = $all_data['wr_id'];
        $manager = $this->manager;

        // ✅ 1. DB에서 기존 timesearch의 service 데이터 조회
        $table = $manager->get_list_table_name('timesearch');
        $result = sql_query("SELECT * FROM `{$table}` WHERE wr_id = '{$wr_id}' AND type = 'service'");

        $existing_by_id = array();
        $existing_by_key = array();
        while ($row = sql_fetch_array($result)) {
            $id = $row['id'];
            $relation_wr_id = $row['relation_wr_id'];
            $day_of_week = $row['day_of_week'];
            $key = 'service_' . $relation_wr_id . '_' . $day_of_week;

            $existing_by_id[$id] = $row;
            $existing_by_key[$key] = $id;
        }

        // ✅ 2. 저장된 데이터를 다시 조회해서 확장 컬럼 가져오기
        $store = $manager->get($wr_id);

        $new_timesearch = array();

        // ✅ 3. 각 contract 항목 처리
        foreach ($all_data['contract'] as $contract_data) {
            if (!isset($contract_data['id']) || !isset($contract_data['service_time'])) {
                continue;
            }

            $contract_id = $contract_data['id'];

            // 해당 contract의 확장 컬럼 가져오기
            $contract_row = null;
            foreach ($store->contract->list as $item) {
                if ($item['id'] == $contract_id) {
                    $contract_row = $item;
                    break;
                }
            }

            if (!$contract_row || empty($contract_row['service_time_list'])) {
                continue;
            }

            // service_time_list를 timesearch 형식으로 변환
            $service_time_list = $contract_row['service_time_list'];
            $service_data = wv_convert_time_list_to_timesearch($service_time_list, 'service');

            // relation_wr_id 추가 및 기존 id 매핑
            foreach ($service_data as $item) {
                $item['relation_wr_id'] = $contract_id;
                $key = 'service_' . $contract_id . '_' . $item['day_of_week'];

                // 기존 데이터 있으면 id 유지
                if (isset($existing_by_key[$key])) {
                    $id = $existing_by_key[$key];
                    $item['id'] = $id;
                    $new_timesearch[$id] = $item;
                } else {
                    // 신규 데이터
                    $new_timesearch[] = $item;
                }
            }
        }

        // ✅ 4. timesearch 저장
        if (count($new_timesearch)) {
            $manager->set(array(
                'wr_id' => $wr_id,
                'timesearch' => $new_timesearch
            ));
        }
    }

 }
