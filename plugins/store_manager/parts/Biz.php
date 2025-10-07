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
    protected $checkbox_fields = array('is_holiday_off');

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

    public function after_set(&$data) {
        if (self::$is_syncing) return;
        if (!isset($data['biz'])) return;

        $has_open = isset($data['biz']['open_time']);
        $has_break = isset($data['biz']['break_time']);

        if (!$has_open && !$has_break) return;

        self::$is_syncing = true;

        try {
            $wr_id = $data['wr_id'];
            $manager = $this->get_manager();

            $existing_list = $manager->get($wr_id)->timesearch->list;

            $updated_list = array();

            // 기존 데이터 중 유지할 것 (service 등)
            foreach ($existing_list as $item) {
                if ($item['type'] !== 'open' && $item['type'] !== 'break') {
                    $updated_list[] = $item;
                }
            }

            // open 처리
            if ($has_open) {
                $open_data = $manager->timesearch->convert_time_array_to_data($data['biz']['open_time'], 'open',1);

                foreach ($open_data as $new_item) {
                    $matched = false;

                    // 기존 데이터에서 매칭되는 것 찾기
                    foreach ($existing_list as $existing_item) {
                        if ($existing_item['type'] === 'open' &&
                            $existing_item['day_of_week'] === $new_item['day_of_week']) {
                            // id 유지하면서 업데이트
                            $updated_list[] = array_merge($existing_item, $new_item);
                            $matched = true;
                            break;
                        }
                    }

                    // 매칭 안 되면 신규 추가
                    if (!$matched) {
                        $updated_list[] = $new_item;
                    }
                }
            } else {
                // open_time 없으면 기존 open 유지
                foreach ($existing_list as $item) {
                    if ($item['type'] === 'open') {
                        $updated_list[] = $item;
                    }
                }
            }

            // break 처리 (동일 로직)
            if ($has_break) {
                $break_data = $manager->timesearch->convert_time_array_to_data($data['biz']['break_time'], 'break',1);

                foreach ($break_data as $new_item) {
                    $matched = false;

                    foreach ($existing_list as $existing_item) {
                        if ($existing_item['type'] === 'break' &&
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
            } else {
                foreach ($existing_list as $item) {
                    if ($item['type'] === 'break') {
                        $updated_list[] = $item;
                    }
                }
            }

            $post_data = array(
                'wr_id' => $wr_id,
                'timesearch' => $updated_list
            );

            $manager->set($post_data);

        } finally {
            self::$is_syncing = false;
        }
    }
}
