<?php
function generate_time_grouped($time_data, $enable_check = false){
    if(!is_array($time_data)) {
        return array();
    }

    $days_kr = array(
        'mon' => '월', 'tue' => '화', 'wed' => '수',
        'thu' => '목', 'fri' => '금', 'sat' => '토', 'sun' => '일'
    );

    $weekdays = array('mon', 'tue', 'wed', 'thu', 'fri'); // 평일
    $weekends = array('sat', 'sun'); // 주말
    $all_days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

    // 각 요일의 실제 시간 정보 수집
    $day_times = array();
    $individual_days = array(); // 개별 설정된 요일들

    foreach($all_days as $day_key){
        $start = null;
        $end = null;

        // 1. 해당 요일의 개별 설정이 있는지 먼저 확인
        if(isset($time_data[$day_key]['enabled']) && $time_data[$day_key]['enabled']){
            $start = isset($time_data[$day_key]['start']) ? $time_data[$day_key]['start'] : null;
            $end = isset($time_data[$day_key]['end']) ? $time_data[$day_key]['end'] : null;
            $individual_days[] = $day_key; // 개별 설정된 요일로 기록
        }
        // 2. 개별 설정이 없으면 평일/주말/매일 설정 확인 (enable_check가 false인 경우만)
        else if(!$enable_check){
            if(in_array($day_key, $weekdays)){
                // 평일인 경우
                if(isset($time_data['weekday']['enabled']) && $time_data['weekday']['enabled']){
                    $start = isset($time_data['weekday']['start']) ? $time_data['weekday']['start'] : null;
                    $end = isset($time_data['weekday']['end']) ? $time_data['weekday']['end'] : null;
                } else if(isset($time_data['daily']['enabled']) && $time_data['daily']['enabled']){
                    $start = isset($time_data['daily']['start']) ? $time_data['daily']['start'] : null;
                    $end = isset($time_data['daily']['end']) ? $time_data['daily']['end'] : null;
                }
            } else if(in_array($day_key, $weekends)){
                // 주말인 경우
                if(isset($time_data['weekend']['enabled']) && $time_data['weekend']['enabled']){
                    $start = isset($time_data['weekend']['start']) ? $time_data['weekend']['start'] : null;
                    $end = isset($time_data['weekend']['end']) ? $time_data['weekend']['end'] : null;
                } else if(isset($time_data['daily']['enabled']) && $time_data['daily']['enabled']){
                    $start = isset($time_data['daily']['start']) ? $time_data['daily']['start'] : null;
                    $end = isset($time_data['daily']['end']) ? $time_data['daily']['end'] : null;
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

    // 유니크한 시간들 찾기
    $unique_times = array_unique($day_times);

    // 1. 모든 요일(7일)이 같은 시간이고, 개별 설정이 없는 경우만 "매일"
    if(count($unique_times) == 1 && count($day_times) == 7 && empty($individual_days)){
        return array(
            array('name' => '매일', 'time' => reset($unique_times))
        );
    }

    // 2. 평일/주말 패턴 확인 - 개별 설정이 없는 경우만
    if(empty($individual_days)){
        $weekday_times = array();
        $weekend_times = array();

        foreach($weekdays as $day){
            if(isset($day_times[$day])){
                $weekday_times[$day] = $day_times[$day];
            }
        }

        foreach($weekends as $day){
            if(isset($day_times[$day])){
                $weekend_times[$day] = $day_times[$day];
            }
        }

        $weekday_unique = array_unique($weekday_times);
        $weekend_unique = array_unique($weekend_times);

        // 평일 5일이 모두 같고, 주말 2일이 모두 같고, 서로 다른 경우
        if(count($weekday_times) == 5 && count($weekday_unique) == 1 &&
            count($weekend_times) == 2 && count($weekend_unique) == 1 &&
            reset($weekday_unique) !== reset($weekend_unique)){

            return array(
                array('name' => '평일', 'time' => reset($weekday_unique)),
                array('name' => '주말', 'time' => reset($weekend_unique))
            );
        }

        // 평일만 5일 모두 같은 경우
        if(count($weekday_times) == 5 && count($weekday_unique) == 1 && count($weekend_times) == 0){
            return array(
                array('name' => '평일', 'time' => reset($weekday_unique))
            );
        }

        // 주말만 2일 모두 같은 경우
        if(count($weekend_times) == 2 && count($weekend_unique) == 1 && count($weekday_times) == 0){
            return array(
                array('name' => '주말', 'time' => reset($weekend_unique))
            );
        }
    }

    // 3. 개별 요일들끼리 같은 시간 그룹핑
    $result = array();
    $processed_days = array();

    foreach($all_days as $day_key){
        if(!isset($day_times[$day_key]) || in_array($day_key, $processed_days)){
            continue;
        }

        $current_time = $day_times[$day_key];
        $same_time_days = array($day_key);
        $processed_days[] = $day_key;

        // 같은 시간을 가진 다른 요일들 찾기
        foreach($all_days as $other_day){
            if($other_day != $day_key &&
                isset($day_times[$other_day]) &&
                !in_array($other_day, $processed_days) &&
                $day_times[$other_day] === $current_time){

                $same_time_days[] = $other_day;
                $processed_days[] = $other_day;
            }
        }

        // 요일명들을 한국어로 변환해서 콤마로 연결
        $day_names = array();
        foreach($same_time_days as $day){
            $day_names[] = $days_kr[$day];
        }

        $result[] = array(
            'name' => implode(',', $day_names),
            'time' => $current_time
        );
    }

    return $result;
}


function generate_time_summary($time_data, $enable_check = false){
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
        // enabled 체크하는 경우 - 매일도 enabled 확인
        if(isset($time_data['daily']['enabled']) && $time_data['daily']['enabled'] && $default_start && $default_end){
            $daily_time = wv_store_manager_format_time($default_start) . ' ~ ' . wv_store_manager_format_time($default_end);
            $summary[] = array('name'=>'매일','time'=>$daily_time);
        }
    } else {
        // 기존 방식 - 시간 데이터만 있으면 추가
        if($default_start && $default_end){
            $daily_time = wv_store_manager_format_time($default_start) . ' ~ ' . wv_store_manager_format_time($default_end);
            $summary[] = array('name'=>'매일','time'=>$daily_time);
        }
    }

    // 2. 평일/주말 설정
    $weekday_start = isset($time_data['weekday']['start']) ? $time_data['weekday']['start'] : $default_start;
    $weekday_end = isset($time_data['weekday']['end']) ? $time_data['weekday']['end'] : $default_end;

    $weekend_start = isset($time_data['weekend']['start']) ? $time_data['weekend']['start'] : $default_start;
    $weekend_end = isset($time_data['weekend']['end']) ? $time_data['weekend']['end'] : $default_end;

    // 평일 처리
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

    // 주말 처리
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