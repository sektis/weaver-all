<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Timesearch extends StoreSchemaBase implements StoreSchemaInterface {

    public $list_part = true;

    protected $columns = array(
        'type' => "ENUM('open','break','service') NOT NULL",
        'relation_wr_id' => "INT(11) DEFAULT NULL",
        'day_of_week' => "VARCHAR(10) NOT NULL",
        'start_time' => "TIME DEFAULT NULL",
        'end_time' => "TIME DEFAULT NULL"
    );

    protected $checkbox_fields = array();

    public function get_indexes() {
        return array(
            array('type'),
            array('relation_wr_id'),
            array('day_of_week'),
            array(
                'name' => 'idx_type_day',
                'type' => 'INDEX',
                'cols' => array('type', 'day_of_week')
            ),
            array(
                'name' => 'idx_relation',
                'type' => 'INDEX',
                'cols' => array('relation_wr_id', 'type')
            ),
            array(
                'name' => 'idx_store_type',
                'type' => 'INDEX',
                'cols' => array('wr_id', 'type', 'day_of_week')
            )
        );
    }

    public function column_extend($row, $all_row = array()) {
        $arr = array();

        $day_names = array(
            'mon' => '월',
            'tue' => '화',
            'wed' => '수',
            'thu' => '목',
            'fri' => '금',
            'sat' => '토',
            'sun' => '일'
        );

        $type_names = array(
            'open' => '영업시간',
            'break' => '휴게시간',
            'service' => '서비스시간'
        );

        $arr['day_name'] = isset($day_names[$row['day_of_week']]) ? $day_names[$row['day_of_week']] : '';
        $arr['type_text'] = isset($type_names[$row['type']]) ? $type_names[$row['type']] : '';
        $arr['is_active'] = !empty($row['start_time']) && !empty($row['end_time']);

        return $arr;
    }

    /**
     * 시간 배열을 Timesearch 데이터 배열로 변환
     *
     * @param array $time_array 시간 배열 (daily/weekday/weekend/mon~sun)
     * @param string $type 타입 ('open', 'break', 'service')
     * @param bool $enabled_check enabled 체크 여부 (false면 무조건 생성)
     * @return array Timesearch 데이터 배열
     */
    /**
     * 시간 배열을 Timesearch 데이터 배열로 변환
     *
     * @param array $time_array 시간 배열 (daily/weekday/weekend/mon~sun)
     * @param string $type 타입 ('open', 'break', 'service')
     * @return array Timesearch 데이터 배열
     */
    /**
     * 시간 배열을 Timesearch 데이터 배열로 변환
     *
     * @param array $time_array 시간 배열 (daily/weekday/weekend/mon~sun)
     * @param string $type 타입 ('open', 'break', 'service')
     * @param bool $enabled_check enabled 체크 여부
     * @return array Timesearch 데이터 배열
     */
    public function convert_time_array_to_data($time_array, $type, $enabled_check = false) {
        if (empty($time_array) || !is_array($time_array)) {
            return array();
        }

        $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
        $weekdays = array('mon', 'tue', 'wed', 'thu', 'fri');
        $weekends = array('sat', 'sun');

        $result = array();

        // 1. 매일 설정 (기본값)
        if (isset($time_array['daily']['start']) && isset($time_array['daily']['end'])) {
            $daily_enabled = !$enabled_check || (isset($time_array['daily']['enabled']) && $time_array['daily']['enabled']);

            if ($daily_enabled) {
                $start = $this->convert_to_24h($time_array['daily']['start']);
                $end = $this->convert_to_24h($time_array['daily']['end']);

                if ($start && $end) {
                    foreach ($days as $day) {
                        $result[$day] = array(
                            'type' => $type,
                            'day_of_week' => $day,
                            'start_time' => $start,
                            'end_time' => $end
                        );
                    }
                }
            }
        }

        // 2. 평일 설정 (매일 덮어쓰기)
        if (isset($time_array['weekday']['start']) && isset($time_array['weekday']['end'])) {
            $weekday_enabled = !$enabled_check || (isset($time_array['weekday']['enabled']) && $time_array['weekday']['enabled']);

            if ($weekday_enabled) {
                $start = $this->convert_to_24h($time_array['weekday']['start']);
                $end = $this->convert_to_24h($time_array['weekday']['end']);

                if ($start && $end) {
                    foreach ($weekdays as $day) {
                        $result[$day] = array(
                            'type' => $type,
                            'day_of_week' => $day,
                            'start_time' => $start,
                            'end_time' => $end
                        );
                    }
                }
            }
        }

        // 3. 주말 설정 (매일 덮어쓰기)
        if (isset($time_array['weekend']['start']) && isset($time_array['weekend']['end'])) {
            $weekend_enabled = !$enabled_check || (isset($time_array['weekend']['enabled']) && $time_array['weekend']['enabled']);

            if ($weekend_enabled) {
                $start = $this->convert_to_24h($time_array['weekend']['start']);
                $end = $this->convert_to_24h($time_array['weekend']['end']);

                if ($start && $end) {
                    foreach ($weekends as $day) {
                        $result[$day] = array(
                            'type' => $type,
                            'day_of_week' => $day,
                            'start_time' => $start,
                            'end_time' => $end
                        );
                    }
                }
            }
        }

        // 4. 요일별 설정 (enabled=true만, 해당 요일 덮어쓰기)
        foreach ($days as $day) {
            if (isset($time_array[$day]['enabled']) && $time_array[$day]['enabled']) {
                if (isset($time_array[$day]['start']) && isset($time_array[$day]['end'])) {
                    $start = $this->convert_to_24h($time_array[$day]['start']);
                    $end = $this->convert_to_24h($time_array[$day]['end']);

                    if ($start && $end) {
                        $result[$day] = array(
                            'type' => $type,
                            'day_of_week' => $day,
                            'start_time' => $start,
                            'end_time' => $end
                        );
                    }
                }
            }
        }

        // 연관배열을 숫자 인덱스 배열로 변환
        return array_values($result);
    }    /**
     * 오전/오후 시간을 24시간 형식으로 변환
     *
     * @param array $time_arr array('period'=>'am/pm', 'hour'=>'01', 'minute'=>'20')
     * @return string|null HH:MM:SS 형식 또는 null
     */
    public function convert_to_24h($time_arr) {
        if (empty($time_arr) || !is_array($time_arr)) {
            return null;
        }

        $period = isset($time_arr['period']) ? $time_arr['period'] : 'am';
        $hour = isset($time_arr['hour']) ? (int)$time_arr['hour'] : 0;
        $minute = isset($time_arr['minute']) ? (int)$time_arr['minute'] : 0;

        // 오후(pm)이고 12시가 아니면 +12
        if ($period === 'pm' && $hour !== 12) {
            $hour += 12;
        }
        // 오전(am)이고 12시면 0시
        if ($period === 'am' && $hour === 12) {
            $hour = 0;
        }

        return sprintf('%02d:%02d:00', $hour, $minute);
    }


}