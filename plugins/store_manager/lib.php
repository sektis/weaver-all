<?php
function is_valid_time_data($time_data) {
    if(!is_array($time_data)) return false;
    if(!isset($time_data['period']) || !isset($time_data['hour']) || !isset($time_data['minute'])) return false;
    return !empty($time_data['period']) && !empty($time_data['hour']) && !empty($time_data['minute']);
}

function generate_time_grouped($time_data, $enable_check = false){
    if(!is_array($time_data)) {
        return array();
    }

    $days_kr = array(
        'mon' => '월요일', 'tue' => '화요일', 'wed' => '수요일',
        'thu' => '목요일', 'fri' => '금요일', 'sat' => '토요일', 'sun' => '일요일'
    );

    $weekdays = array('mon', 'tue', 'wed', 'thu', 'fri'); // 평일
    $weekends = array('sat', 'sun'); // 주말
    $all_days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

    // 각 요일의 실제 시간 계산
    $day_times = array();

    foreach($all_days as $day_key){
        $start = null;
        $end = null;

        // 1. 개별 요일 설정 확인 (항상 enabled 체크 필요)
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

        if($start && $end){
            $day_times[$day_key] = wv_store_manager_format_time($start) . ' ~ ' . wv_store_manager_format_time($end);
        }
    }

    if(empty($day_times)){
        return array();
    }

    $result = array();

    // 평일 시간 수집 및 일관성 체크
    $weekday_times = array();
    foreach($weekdays as $day){
        if(isset($day_times[$day])){
            $weekday_times[$day] = $day_times[$day];
        }
    }

    // 평일 처리
    if(!empty($weekday_times)){
        $unique_weekday_times = array_unique($weekday_times);
        if(count($unique_weekday_times) === 1 && count($weekday_times) > 1){
            // 평일이 2개 이상 있고 모두 시간이 같음 - "평일"로 묶기
            $result[] = array(
                'name' => '평일',
                'time' => array_values($unique_weekday_times)[0]
            );
        } else {
            // 평일이 1개뿐이거나 시간이 다름 - 개별 표시
            foreach($weekday_times as $day => $time){
                $result[] = array(
                    'name' => $days_kr[$day],
                    'time' => $time
                );
            }
        }
    }

    // 주말 시간 수집 및 일관성 체크
    $weekend_times = array();
    foreach($weekends as $day){
        if(isset($day_times[$day])){
            $weekend_times[$day] = $day_times[$day];
        }
    }

    // 주말 처리
    if(!empty($weekend_times)){
        $unique_weekend_times = array_unique($weekend_times);
        if(count($unique_weekend_times) === 1 && count($weekend_times) > 1){
            // 주말이 2개 모두 있고 시간이 같음 - "주말"로 묶기
            $result[] = array(
                'name' => '주말',
                'time' => array_values($unique_weekend_times)[0]
            );
        } else {
            // 주말이 1개뿐이거나 시간이 다름 - 개별 표시
            foreach($weekend_times as $day => $time){
                $result[] = array(
                    'name' => $days_kr[$day],
                    'time' => $time
                );
            }
        }
    }

    return $result;
}

function generate_time_list($time_data, $enable_check = false){
    if(!is_array($time_data)) {
        return '';
    }

    $summary = array();
    $days_kr = array(
        'mon' => '월요일', 'tue' => '화요일', 'wed' => '수요일',
        'thu' => '목요일', 'fri' => '금요일', 'sat' => '토요일', 'sun' => '일요일'
    );

    // 1. 매일 설정
    $default_start = isset($time_data['daily']['start']) ? $time_data['daily']['start'] : null;
    $default_end = isset($time_data['daily']['end']) ? $time_data['daily']['end'] : null;

    if($enable_check){
        // enabled 체크하는 경우
        if(isset($time_data['daily']['enabled']) && $time_data['daily']['enabled'] && $default_start && $default_end){
            $daily_time = wv_store_manager_format_time($default_start) . ' ~ ' . wv_store_manager_format_time($default_end);
            $summary[] = array('name'=>'매일','time'=>$daily_time);
        }
    } else {
        // 기존 방식
        if($default_start && $default_end){
            $daily_time = wv_store_manager_format_time($default_start) . ' ~ ' . wv_store_manager_format_time($default_end);
            $summary[] = array('name'=>'매일','time'=>$daily_time);
        }
    }

    // 2. 평일/주말 설정
    $weekday_start = isset($time_data['weekday']['start']) ? $time_data['weekday']['start'] : null;
    $weekday_end = isset($time_data['weekday']['end']) ? $time_data['weekday']['end'] : null;

    if($enable_check){
        // enabled 체크하는 경우
        if(isset($time_data['weekday']['enabled']) && $time_data['weekday']['enabled'] && $weekday_start && $weekday_end){
            $weekday_time = wv_store_manager_format_time($weekday_start) . ' ~ ' . wv_store_manager_format_time($weekday_end);
            $summary[] = array('name'=>'평일','time'=>$weekday_time);
        }
    } else {
        // 기존 방식
        if($weekday_start && $weekday_end){
            $weekday_time = wv_store_manager_format_time($weekday_start) . ' ~ ' . wv_store_manager_format_time($weekday_end);
            $summary[] = array('name'=>'평일','time'=>$weekday_time);
        }
    }

    $weekend_start = isset($time_data['weekend']['start']) ? $time_data['weekend']['start'] : $default_start;
    $weekend_end = isset($time_data['weekend']['end']) ? $time_data['weekend']['end'] : $default_end;

    if($enable_check){
        // enabled 체크하는 경우
        if(isset($time_data['weekend']['enabled']) && $time_data['weekend']['enabled'] && $weekend_start && $weekend_end){
            $weekend_time = wv_store_manager_format_time($weekend_start) . ' ~ ' . wv_store_manager_format_time($weekend_end);
            $summary[] = array('name'=>'주말','time'=>$weekend_time);
        }
    } else {
        // 기존 방식
        if($weekend_start && $weekend_end){
            $weekend_time = wv_store_manager_format_time($weekend_start) . ' ~ ' . wv_store_manager_format_time($weekend_end);
            $summary[] = array('name'=>'주말','time'=>$weekend_time);
        }
    }

    // 3. 요일별 설정 (enabled는 항상 체크)
    foreach($days_kr as $day_key => $day_name){
        if(isset($time_data[$day_key]['enabled']) && $time_data[$day_key]['enabled']){
            $day_start = isset($time_data[$day_key]['start']) ? $time_data[$day_key]['start'] : null;
            $day_end = isset($time_data[$day_key]['end']) ? $time_data[$day_key]['end'] : null;

            if($day_start && $day_end){
                $day_time = wv_store_manager_format_time($day_start) . ' ~ ' . wv_store_manager_format_time($day_end);
                $summary[] = array('name'=>$day_name,'time'=>$day_time);
            }
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
    elseif($type=='hour') $default = strpos($name, 'end') !== false ? '10' : '09';
    else $default = '00';

    $val = isset($data[$type]) ? $data[$type] : $default;
    $opts = '';

    if($type=='period') {
        // 종료시간이면 오후를 기본값으로
        if(strpos($name, 'end') !== false && !isset($data[$type])) $val = 'pm';
        foreach(array('am'=>'오전','pm'=>'오후') as $k=>$v)
            $opts.="<option value='$k'".($val==$k?' selected':'').">$v</option>";
    }
    elseif($type=='hour') {
        for($i=1;$i<=12;$i++) {
            $h=sprintf('%02d',$i);
            $opts.="<option value='$h'".($val==$h?' selected':'').">$h</option>";
        }
    }
    else {
        for($i=0;$i<=59;$i++) {
            $m=sprintf('%02d',$i);
            $opts.="<option value='$m'".($val==$m?' selected':'').">$m</option>";
        }
    }
    return "<select name='$name' class='form-select'>$opts</select>";
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


