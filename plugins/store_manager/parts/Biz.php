<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Biz extends StoreSchemaBase implements StoreSchemaInterface{


    protected $columns = array(
        'open_time'=>'TEXT DEFAULT NULL',
        'break_time' => "TEXT DEFAULT NULL",
        'is_holiday_off' => "TINYINT(1)  DEFAULT 0",
        'parking'=>'varchar(255) not null'
    );

    protected $parking_max_char=20;
    private static $is_syncing = false;
    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row,$all_row=array()){
        $arr = array();
        if(isset($row['open_time']) && !empty($row['open_time'])){

            $arr['open_time_list'] = generate_time_list($row['open_time'],1);
            $arr['open_time_group'] = generate_time_grouped($row['open_time'],1);
        } else {
            $arr['open_time_list'] = array();
            $arr['open_time_group'] = array();
        }

        if(isset($row['break_time']) && !empty($row['break_time'])){
            $arr['break_time_list'] = generate_time_list($row['break_time'],1);
            $arr['break_time_group'] = generate_time_grouped($row['break_time'],1);
        } else {
            $arr['break_time_list'] = array();
            $arr['break_time_group'] = array();
        }

        return $arr;
    }



    public function after_set(&$all_data) {
        // open_time 또는 break_time이 수정되었으면
        if (isset($all_data['biz']['open_time']) || isset($all_data['biz']['break_time'])) {

            $wr_id = $all_data['wr_id'];

            // ✅ 1. DB에서 기존 timesearch 데이터 조회
            $table = $this->manager->get_list_table_name('timesearch');
            $result = sql_query("SELECT * FROM `{$table}` WHERE wr_id = '{$wr_id}' AND type IN ('open', 'break')");

            $existing_by_id = array();
            $existing_by_key = array();
            while ($row = sql_fetch_array($result)) {
                $id = $row['id'];
                $key = $row['type'] . '_' . $row['day_of_week'];

                $existing_by_id[$id] = $row;
                $existing_by_key[$key] = $id;
            }

            // ✅ 2. 저장된 데이터를 다시 조회해서 확장 컬럼 가져오기
            $store = $this->manager->get($wr_id);

            $new_timesearch = array();

            // open_time 처리
            if (isset($all_data['biz']['open_time'])) {
                $open_time_list = $store->biz->open_time_list;

                $open_data = wv_convert_time_list_to_timesearch($open_time_list, 'open');

                foreach ($open_data as $item) {
                    $key = 'open_' . $item['day_of_week'];

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

            // break_time 처리
            if (isset($all_data['biz']['break_time'])) {
                $break_time_list = $store->biz->break_time_list;
                $break_data = wv_convert_time_list_to_timesearch($break_time_list, 'break');

                foreach ($break_data as $item) {
                    $key = 'break_' . $item['day_of_week'];

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

            // ✅ 3. timesearch 저장
            if (count($new_timesearch)) {
                $this->manager->set(array(
                    'wr_id' => $wr_id,
                    'timesearch' => $new_timesearch
                ));
            }
        }
    }
  }
