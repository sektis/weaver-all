<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
/**
 * open_time_list/break_time_list를 timesearch 형식으로 변환
 *
 * @param array $time_list generate_time_list() 결과
 * @param string $type 'open' 또는 'break'
 * @return array timesearch 형식 배열
 */
function wv_convert_time_list_to_timesearch($time_list, $type) {
    if (!is_array($time_list) || !count($time_list)) {
        return array();
    }

    $day_map = array(
        '월요일' => 'mon',
        '화요일' => 'tue',
        '수요일' => 'wed',
        '목요일' => 'thu',
        '금요일' => 'fri',
        '토요일' => 'sat',
        '일요일' => 'sun'
    );

    $result = array();

    foreach ($time_list as $item) {
        if (!isset($item['name']) || !isset($item['time'])) continue;

        $day_name = $item['name'];
        $time_str = $item['time'];

        // 요일명 → 영문 코드
        if (!isset($day_map[$day_name])) continue;
        $day_of_week = $day_map[$day_name];

        // 시간 파싱: "오전 09:00 ~ 오후 10:00"
        if (empty($time_str)) {
            // 빈 시간 → null
            $result[] = array(
                'type' => $type,
                'day_of_week' => $day_of_week,
                'start_time' => null,
                'end_time' => null
            );
            continue;
        }

        // "오전 09:00 ~ 오후 10:00" 파싱
        if (preg_match('/^(오전|오후)\s+(\d{2}):(\d{2})\s*~\s*(오전|오후)\s+(\d{2}):(\d{2})$/', $time_str, $matches)) {
            $start_period = $matches[1];
            $start_hour = (int)$matches[2];
            $start_minute = (int)$matches[3];
            $end_period = $matches[4];
            $end_hour = (int)$matches[5];
            $end_minute = (int)$matches[6];

            // 24시간 형식으로 변환
            if ($start_period === '오후' && $start_hour !== 12) {
                $start_hour += 12;
            } elseif ($start_period === '오전' && $start_hour === 12) {
                $start_hour = 0;
            }

            if ($end_period === '오후' && $end_hour !== 12) {
                $end_hour += 12;
            } elseif ($end_period === '오전' && $end_hour === 12) {
                $end_hour = 0;
            }

            $start_time = sprintf('%02d:%02d:00', $start_hour, $start_minute);
            $end_time = sprintf('%02d:%02d:00', $end_hour, $end_minute);

            $result[] = array(
                'type' => $type,
                'day_of_week' => $day_of_week,
                'start_time' => $start_time,
                'end_time' => $end_time
            );
        }
    }

    return $result;
}

/**
 * 시간 배열을 Timesearch 데이터 배열로 변환
 *
 * @param array $time_array 시간 배열 (daily/weekday/weekend/mon~sun)
 * @param string $type 타입 ('open', 'break', 'service')
 * @param bool $enabled_check enabled 체크 여부
 * @return array Timesearch 데이터 배열
 */
function wv_convert_time_array_to_timesearch_data($time_array, $type, $enabled_check = false) {
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
            $start = wv_convert_to_24h($time_array['daily']['start']);
            $end = wv_convert_to_24h($time_array['daily']['end']);

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
            $start = wv_convert_to_24h($time_array['weekday']['start']);
            $end = wv_convert_to_24h($time_array['weekday']['end']);

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
            $start = wv_convert_to_24h($time_array['weekend']['start']);
            $end = wv_convert_to_24h($time_array['weekend']['end']);

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
                $start = wv_convert_to_24h($time_array[$day]['start']);
                $end = wv_convert_to_24h($time_array[$day]['end']);

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
}

/**
 * 오전/오후 시간을 24시간 형식으로 변환
 *
 * @param array $time_arr array('period'=>'am/pm', 'hour'=>'01', 'minute'=>'20')
 * @return string|null HH:MM:SS 형식 또는 null
 */
function wv_convert_to_24h($time_arr) {
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

function wv_store_manager_img_url(){
    return wv()->store_manager->plugin_url.'/img';
}
/**
 * 리로드 데이터 속성 출력
 *
 * @param array $reload_ajax_data 리로드 데이터 (url 포함)
 * @return string HTML 속성 문자열
 */
function wv_display_reload_data($reload_ajax_data = array()) {
    if (!is_array($reload_ajax_data) || !count($reload_ajax_data)) {
        return '';
    }

    // url 추출
    $url = isset($reload_ajax_data['url']) ? $reload_ajax_data['url'] : '';
    if (!$url) {
        return '';
    }

    // url은 data-wv-reload-data에서 제외
    $data = $reload_ajax_data;
    unset($data['url']);

    if (!count($data)) {
        return '';
    }

    $attrs = array();

    // data-wv-reload-url
    $attrs[] = 'data-wv-reload-url="' . htmlspecialchars($url, ENT_QUOTES) . '"';

    // data-wv-reload-data (url 제외)
    $attrs[] = "data-wv-reload-data='" . htmlspecialchars(json_encode($data), ENT_QUOTES) . "'";

    // data-wv-reload-options (replace_with 셀렉터 포함)
    global $skin_selector;
    $reload_options = array(
        'replace_with' => $skin_selector ? $skin_selector : ''
    );
    $attrs[] = "data-wv-reload-options='" . htmlspecialchars(json_encode($reload_options), ENT_QUOTES) . "'";

    return ' ' . implode(' ', $attrs);
}


function is_valid_time_data($time_data) {
    if(!is_array($time_data)) return false;
    if(!isset($time_data['period']) || !isset($time_data['hour']) || !isset($time_data['minute'])) return false;
    return !empty($time_data['period']) && !empty($time_data['hour']) && !empty($time_data['minute']);
}

function generate_time_grouped($time_data, $enable_check = false){
    // generate_time_list()로 각 요일 시간 계산
    $time_list = generate_time_list($time_data, $enable_check);

    if(empty($time_list)){
        return array();
    }

    $days_kr = array('월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일');
    $weekdays = array('월요일', '화요일', '수요일', '목요일', '금요일');
    $weekends = array('토요일', '일요일');

    $result = array();

    // 평일 시간 수집
    $weekday_times = array();
    foreach($time_list as $item){
        if(in_array($item['name'], $weekdays) && !empty($item['time'])){
            $weekday_times[$item['name']] = $item['time'];
        }
    }

    // 평일 그룹핑
    $weekday_grouped = null;
    if(!empty($weekday_times)){
        $unique_weekday_times = array_unique($weekday_times);
        if(count($unique_weekday_times) === 1 && count($weekday_times) === 5){
            $weekday_grouped = array(
                'name' => '평일',
                'time' => array_values($unique_weekday_times)[0]
            );
            $result[] = $weekday_grouped;
        } else {
            foreach($weekday_times as $day => $time){
                $result[] = array('name' => $day, 'time' => $time);
            }
        }
    }

    // 주말 시간 수집
    $weekend_times = array();
    foreach($time_list as $item){
        if(in_array($item['name'], $weekends) && !empty($item['time'])){
            $weekend_times[$item['name']] = $item['time'];
        }
    }

    // 주말 그룹핑
    $weekend_grouped = null;
    if(!empty($weekend_times)){
        $unique_weekend_times = array_unique($weekend_times);
        if(count($unique_weekend_times) === 1 && count($weekend_times) === 2){
            $weekend_grouped = array(
                'name' => '주말',
                'time' => array_values($unique_weekend_times)[0]
            );
            $result[] = $weekend_grouped;
        } else {
            foreach($weekend_times as $day => $time){
                $result[] = array('name' => $day, 'time' => $time);
            }
        }
    }

    // 평일+주말이 같으면 "매일"로 통합
    if($weekday_grouped && $weekend_grouped && $weekday_grouped['time'] === $weekend_grouped['time']){
        $result = array_filter($result, function($item) {
            return $item['name'] !== '평일' && $item['name'] !== '주말';
        });

        array_unshift($result, array(
            'name' => '매일',
            'time' => $weekday_grouped['time']
        ));
    }

    return $result;
}
function generate_time_list($time_data, $enable_check = false){
    if(!is_array($time_data)) {
        return array();
    }

    $days_kr = array(
        'mon' => '월요일', 'tue' => '화요일', 'wed' => '수요일',
        'thu' => '목요일', 'fri' => '금요일', 'sat' => '토요일', 'sun' => '일요일'
    );

    $weekdays = array('mon', 'tue', 'wed', 'thu', 'fri');
    $weekends = array('sat', 'sun');
    $all_days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

    $summary = array();

    // 각 요일별로 시간 계산
    foreach($all_days as $day_key){
        $start = null;
        $end = null;

        // 1. 개별 요일 설정 확인
        if(isset($time_data[$day_key]['enabled']) && ($time_data[$day_key]['enabled'] === true || $time_data[$day_key]['enabled'] == 1)){
            $start = isset($time_data[$day_key]['start']) && is_valid_time_data($time_data[$day_key]['start']) ? $time_data[$day_key]['start'] : null;
            $end = isset($time_data[$day_key]['end']) && is_valid_time_data($time_data[$day_key]['end']) ? $time_data[$day_key]['end'] : null;
        }

        // 2. 개별 설정이 없으면 그룹 설정 확인
        if(!$start || !$end){
            if(in_array($day_key, $weekdays)){
                // 평일인 경우
                if($enable_check){
                    if(isset($time_data['weekday']['enabled']) && ($time_data['weekday']['enabled'] === true || $time_data['weekday']['enabled'] == 1)){
                        $start = isset($time_data['weekday']['start']) && is_valid_time_data($time_data['weekday']['start']) ? $time_data['weekday']['start'] : null;
                        $end = isset($time_data['weekday']['end']) && is_valid_time_data($time_data['weekday']['end']) ? $time_data['weekday']['end'] : null;
                    } else if(isset($time_data['daily']['enabled']) && ($time_data['daily']['enabled'] === true || $time_data['daily']['enabled'] == 1)){
                        $start = isset($time_data['daily']['start']) && is_valid_time_data($time_data['daily']['start']) ? $time_data['daily']['start'] : null;
                        $end = isset($time_data['daily']['end']) && is_valid_time_data($time_data['daily']['end']) ? $time_data['daily']['end'] : null;
                    }
                } else {
                    if(isset($time_data['weekday']['start']) && isset($time_data['weekday']['end']) &&
                        is_valid_time_data($time_data['weekday']['start']) && is_valid_time_data($time_data['weekday']['end'])){
                        $start = $time_data['weekday']['start'];
                        $end = $time_data['weekday']['end'];
                    } else if(isset($time_data['daily']['start']) && isset($time_data['daily']['end']) &&
                        is_valid_time_data($time_data['daily']['start']) && is_valid_time_data($time_data['daily']['end'])){
                        $start = $time_data['daily']['start'];
                        $end = $time_data['daily']['end'];
                    }
                }
            } else if(in_array($day_key, $weekends)){
                // 주말인 경우
                if($enable_check){
                    if(isset($time_data['weekend']['enabled']) && ($time_data['weekend']['enabled'] === true || $time_data['weekend']['enabled'] == 1)){
                        $start = isset($time_data['weekend']['start']) && is_valid_time_data($time_data['weekend']['start']) ? $time_data['weekend']['start'] : null;
                        $end = isset($time_data['weekend']['end']) && is_valid_time_data($time_data['weekend']['end']) ? $time_data['weekend']['end'] : null;
                    } else if(isset($time_data['daily']['enabled']) && ($time_data['daily']['enabled'] === true || $time_data['daily']['enabled'] == 1)){
                        $start = isset($time_data['daily']['start']) && is_valid_time_data($time_data['daily']['start']) ? $time_data['daily']['start'] : null;
                        $end = isset($time_data['daily']['end']) && is_valid_time_data($time_data['daily']['end']) ? $time_data['daily']['end'] : null;
                    }
                } else {
                    if(isset($time_data['weekend']['start']) && isset($time_data['weekend']['end']) &&
                        is_valid_time_data($time_data['weekend']['start']) && is_valid_time_data($time_data['weekend']['end'])){
                        $start = $time_data['weekend']['start'];
                        $end = $time_data['weekend']['end'];
                    } else if(isset($time_data['daily']['start']) && isset($time_data['daily']['end']) &&
                        is_valid_time_data($time_data['daily']['start']) && is_valid_time_data($time_data['daily']['end'])){
                        $start = $time_data['daily']['start'];
                        $end = $time_data['daily']['end'];
                    }
                }
            }
        }

        // 요일별 시간 추가
        if($start && $end){
            $summary[] = array(
                'name' => $days_kr[$day_key],
                'time' => wv_store_manager_format_time($start) . ' ~ ' . wv_store_manager_format_time($end)
            );
        } else {
            // 시간이 없으면 "휴무" 표시
            $summary[] = array(
                'name' => $days_kr[$day_key],
                'time' => ''
            );
        }
    }

    return $summary;
}

function wv_store_manager_format_time($time_array){
    if(!is_array($time_array) || !isset($time_array['period']) || !isset($time_array['hour']) || !isset($time_array['minute'])){
        return '';
    }

    $period = $time_array['period'] === 'am' ? '오전' : '오후';
    $hour = str_pad($time_array['hour'], 2, '0', STR_PAD_LEFT);
    $minute = str_pad($time_array['minute'], 2, '0', STR_PAD_LEFT);

    return $period . ' ' . $hour . ':' . $minute;
}

function wv_store_manager_time_select($name, $data, $type) {
    // 종료시간 기본값: 오후 10시
    if($type=='period') $default = 'am';
    elseif($type=='hour') $default = strpos($name, '[end]') !== false ? '10' : '09';
    else $default = '00';

    $val = isset($data[$type]) ? $data[$type] : $default;
    $opts = '';

    if($type=='period') {
        // 종료시간이면 오후를 기본값으로
        if(strpos($name, '[end]') !== false && !isset($data[$type])) $val = 'pm';
        foreach(array('am'=>'오전','pm'=>'오후') as $k=>$v) {
            $is_default = ($k == ($val == 'pm' && strpos($name, 'end') !== false ? 'pm' : 'am')) ? ' data-default' : '';
            $opts.="<option value='$k'".($val==$k?' selected':'')."$is_default>$v</option>";
        }
    }
    elseif($type=='hour') {
        for($i=1;$i<=12;$i++) {
            $h=sprintf('%02d',$i);
            $is_default = ($h == $default) ? ' data-default' : '';
            $opts.="<option value='$h'".($val==$h?' selected':'')."$is_default>$h</option>";
        }
    }
    else {
        for($i=0;$i<=59;$i++) {
            $m=sprintf('%02d',$i);
            $is_default = ($m == '00') ? ' data-default' : '';
            $opts.="<option value='$m'".($val==$m?' selected':'')."$is_default>$m</option>";
        }
    }
    return "<select name='$name' class='form-select'  >$opts</select>";
}

function generate_dayoffs_grouped($dayoffs_data) {
    if(!is_array($dayoffs_data) || empty($dayoffs_data)) {
        return array();
    }

    $groups = array();

    // cycle과 filter_week을 기준으로 그룹핑
    foreach($dayoffs_data as $item) {
        if(!isset($item['cycle']) || !isset($item['target'])) continue;

        $cycle = $item['cycle'];
        $filter_week = isset($item['filter_week']) ? $item['filter_week'] : '';
        $target = $item['target'];

        // 그룹 키 생성 (cycle + filter_week 조합)
        $group_key = $cycle . '|' . $filter_week;

        if(!isset($groups[$group_key])) {
            $groups[$group_key] = array(
                'cycle' => $cycle,
                'filter_week' => $filter_week,
                'targets' => array()
            );
        }

        $groups[$group_key]['targets'][] = $target;
    }

    $result = array();

    foreach($groups as $group) {
        // 타겟들을 정렬하고 중복 제거
        $targets = array_unique($group['targets']);
        sort($targets);

        // 요일을 간단하게 표시 (월요일 → 월)
        $simplified_targets = array_map(function($target) {
            return str_replace('요일', '', $target);
        }, $targets);

        $result[] = array(
            'name' => $group['cycle'],
            'targets' => implode(',', $simplified_targets) . '요일'
        );
    }

    return $result;
}

/**
 * 휴무 데이터를 개별 목록으로 표시
 */
function generate_dayoffs_list($dayoffs_data) {
    if(!is_array($dayoffs_data) || empty($dayoffs_data)) {
        return array();
    }

    $result = array();

    foreach($dayoffs_data as $item) {
        if(!isset($item['cycle']) || !isset($item['target'])) continue;

        $result[] = array(
            'name' => $item['cycle'] . ' ' . $item['target']
        );
    }

    return $result;
}

/**
 * 휴무 데이터를 더 상세하게 그룹핑 (주차별 분리)
 */
function generate_dayoffs_detailed_grouped($dayoffs_data) {
    if(!is_array($dayoffs_data) || empty($dayoffs_data)) {
        return array();
    }

    $groups = array();

    foreach($dayoffs_data as $item) {
        if(!isset($item['cycle']) || !isset($item['target'])) continue;

        $cycle = $item['cycle'];
        $target = $item['target'];
        $filter_week = isset($item['filter_week']) ? $item['filter_week'] : '';

        // 더 세분화된 그룹 키
        $group_key = $cycle;

        if(!isset($groups[$group_key])) {
            $groups[$group_key] = array(
                'cycle' => $cycle,
                'patterns' => array()
            );
        }

        // filter_week별로 패턴 분류
        $pattern_key = $filter_week ?: 'all';

        if(!isset($groups[$group_key]['patterns'][$pattern_key])) {
            $groups[$group_key]['patterns'][$pattern_key] = array(
                'filter_week' => $filter_week,
                'targets' => array()
            );
        }

        $groups[$group_key]['patterns'][$pattern_key]['targets'][] = $target;
    }

    $result = array();

    foreach($groups as $group) {
        foreach($group['patterns'] as $pattern) {
            $targets = array_unique($pattern['targets']);
            sort($targets);

            // 타겟 간소화
            $simplified_targets = array_map(function($target) {
                return str_replace('요일', '', $target);
            }, $targets);

            $name = $group['cycle'];
            $targets_str = implode(',', $simplified_targets) . '요일';

            $result[] = array(
                'name' => $name,
                'targets' => $targets_str,
                'filter_week' => $pattern['filter_week']
            );
        }
    }

    return $result;
}

/**
 * 휴무 요약 (한 줄로 간단히)
 */
function generate_dayoffs_summary($dayoffs_data) {
    if(!is_array($dayoffs_data) || empty($dayoffs_data)) {
        return '';
    }

    $grouped = generate_dayoffs_grouped($dayoffs_data);

    if(empty($grouped)) return '';

    $summaries = array();
    foreach($grouped as $group) {
        $summaries[] = $group['name'] . ' ' . $group['targets'];
    }

    return implode(', ', $summaries);
}

/**
 * 다음 휴무일까지의 일수 계산
 */
function get_days_until_next_dayoff($dayoffs_data, $from_date = null) {
    if(!$from_date) $from_date = date('Y-m-d');

    $current_date = $from_date;

    // 최대 365일까지 체크
    for($i = 1; $i <= 365; $i++) {
        $check_date = date('Y-m-d', strtotime($current_date . " +$i days"));

        if(is_dayoff_from_data($dayoffs_data, $check_date)) {
            return array(
                'days' => $i,
                'date' => $check_date,
                'day_name' => date('Y-m-d (D)', strtotime($check_date))
            );
        }
    }

    return null; // 1년 내 휴무일 없음
}

/**
 * 휴무 데이터로부터 특정 날짜가 휴무인지 확인
 */
function is_dayoff_from_data($dayoffs_data, $date) {
    if(!is_array($dayoffs_data) || empty($dayoffs_data)) {
        return false;
    }

    $timestamp = strtotime($date);
    $day_of_week = date('w', $timestamp);
    $day_of_month = date('j', $timestamp);
    $week_of_month = get_week_of_month($date);

    foreach($dayoffs_data as $item) {
        if(!isset($item['filter_type']) || !isset($item['filter_index'])) continue;

        $filter_type = $item['filter_type'];
        $filter_index = $item['filter_index'];
        $filter_week = isset($item['filter_week']) ? $item['filter_week'] : '';

        // 주간 휴무 체크
        if($filter_type == 'w' && $filter_index == $day_of_week) {
            if(empty($filter_week)) {
                // 매주
                return true;
            } else {
                // 특정 주차들
                $weeks = explode(',', $filter_week);
                if(in_array($week_of_month, $weeks)) {
                    return true;
                }
            }
        }

        // 월간 휴무 체크
        if($filter_type == 'm' && $filter_index == $day_of_month) {
            return true;
        }
    }

    return false;
}

/**
 * 휴무 패턴별 통계
 */
function get_dayoffs_statistics($dayoffs_data) {
    if(!is_array($dayoffs_data) || empty($dayoffs_data)) {
        return array();
    }

    $stats = array(
        'total_patterns' => count($dayoffs_data),
        'weekly_patterns' => 0,
        'monthly_patterns' => 0,
        'cycles' => array(),
        'most_common_day' => null
    );

    $day_counts = array();

    foreach($dayoffs_data as $item) {
        if(!isset($item['filter_type'])) continue;

        // 타입별 카운트
        if($item['filter_type'] == 'w') {
            $stats['weekly_patterns']++;
        } elseif($item['filter_type'] == 'm') {
            $stats['monthly_patterns']++;
        }

        // 사이클별 카운트
        if(isset($item['cycle'])) {
            $cycle = $item['cycle'];
            if(!isset($stats['cycles'][$cycle])) {
                $stats['cycles'][$cycle] = 0;
            }
            $stats['cycles'][$cycle]++;
        }

        // 요일별 카운트 (주간 패턴만)
        if($item['filter_type'] == 'w' && isset($item['target'])) {
            $target = $item['target'];
            if(!isset($day_counts[$target])) {
                $day_counts[$target] = 0;
            }
            $day_counts[$target]++;
        }
    }

    // 가장 많은 휴무 요일 찾기
    if(!empty($day_counts)) {
        $stats['most_common_day'] = array_search(max($day_counts), $day_counts);
    }

    return $stats;
}

function wv_store_manager_mask_number($number, $show_last = 2, $mask_char = '*'){
    $number = (string)$number;
    $length = strlen($number);

    // 숫자 길이가 보여줄 자릿수보다 작거나 같으면 그대로 반환
    if($length <= $show_last){
        return $number;
    }

    // 마스킹할 자릿수 계산
    $mask_count = $length - $show_last;

    // 마스킹 문자열 생성
    $mask_string = str_repeat($mask_char, $mask_count);

    // 뒤에서 보여줄 부분 추출
    $show_part = substr($number, -$show_last);

    return $mask_string . $show_part;
}

