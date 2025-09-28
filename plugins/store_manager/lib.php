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
        'mon' => '월', 'tue' => '화', 'wed' => '수',
        'thu' => '목', 'fri' => '금', 'sat' => '토', 'sun' => '일'
    );

    $weekdays = array('mon', 'tue', 'wed', 'thu', 'fri'); // 평일
    $weekends = array('sat', 'sun'); // 주말
    $all_days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

    // 시간 데이터 유효성 검사 함수


    // 각 요일의 실제 시간 계산
    $day_times = array();

    foreach($all_days as $day_key){
        $start = null;
        $end = null;

        // 1. 개별 요일 설정 확인
        if($enable_check){
            if(isset($time_data[$day_key]['enabled']) && $time_data[$day_key]['enabled'] == 1){
                $start = isset($time_data[$day_key]['start']) && is_valid_time_data($time_data[$day_key]['start']) ? $time_data[$day_key]['start'] : null;
                $end = isset($time_data[$day_key]['end']) && is_valid_time_data($time_data[$day_key]['end']) ? $time_data[$day_key]['end'] : null;
            }
        } else {
            if(isset($time_data[$day_key]['start']) && isset($time_data[$day_key]['end']) &&
                is_valid_time_data($time_data[$day_key]['start']) && is_valid_time_data($time_data[$day_key]['end'])){
                $start = $time_data[$day_key]['start'];
                $end = $time_data[$day_key]['end'];
            }
        }

        // 2. 개별 설정이 없으면 그룹 설정 확인
        if(!$start || !$end){
            if(in_array($day_key, $weekdays)){
                // 평일인 경우
                if($enable_check){
                    if(isset($time_data['weekday']['enabled']) && $time_data['weekday']['enabled'] == 1){
                        $start = isset($time_data['weekday']['start']) && is_valid_time_data($time_data['weekday']['start']) ? $time_data['weekday']['start'] : null;
                        $end = isset($time_data['weekday']['end']) && is_valid_time_data($time_data['weekday']['end']) ? $time_data['weekday']['end'] : null;
                    } else if(isset($time_data['daily']['enabled']) && $time_data['daily']['enabled'] == 1){
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
                    if(isset($time_data['weekend']['enabled']) && $time_data['weekend']['enabled'] == 1){
                        $start = isset($time_data['weekend']['start']) && is_valid_time_data($time_data['weekend']['start']) ? $time_data['weekend']['start'] : null;
                        $end = isset($time_data['weekend']['end']) && is_valid_time_data($time_data['weekend']['end']) ? $time_data['weekend']['end'] : null;
                    } else if(isset($time_data['daily']['enabled']) && $time_data['daily']['enabled'] == 1){
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

    // 평일 완전성 및 일관성 체크
    $weekday_times = array();
    $weekday_count = 0;
    foreach($weekdays as $day){
        if(isset($day_times[$day])){
            $weekday_times[] = $day_times[$day];
            $weekday_count++;
        }
    }

    if($weekday_count > 0){
        // 평일 5개가 모두 있고 시간이 일관된 경우에만 "평일"로 묶기
        if($weekday_count === 5){
            $unique_weekday_times = array_unique($weekday_times);
            if(count($unique_weekday_times) === 1){
                // 평일 5개 모두 있고 시간이 일관됨
                $result[] = array(
                    'name' => '평일',
                    'time' => $unique_weekday_times[0]
                );
            } else {
                // 평일 5개 모두 있지만 시간이 다름 - 개별 표시
                foreach($weekdays as $day){
                    if(isset($day_times[$day])){
                        $result[] = array(
                            'name' => $days_kr[$day],
                            'time' => $day_times[$day]
                        );
                    }
                }
            }
        } else {
            // 평일이 5개 모두 없음 - 개별 표시
            foreach($weekdays as $day){
                if(isset($day_times[$day])){
                    $result[] = array(
                        'name' => $days_kr[$day],
                        'time' => $day_times[$day]
                    );
                }
            }
        }
    }

    // 주말 완전성 및 일관성 체크
    $weekend_times = array();
    $weekend_count = 0;
    foreach($weekends as $day){
        if(isset($day_times[$day])){
            $weekend_times[] = $day_times[$day];
            $weekend_count++;
        }
    }

    if($weekend_count > 0){
        // 주말 2개가 모두 있고 시간이 일관된 경우에만 "주말"로 묶기
        if($weekend_count === 2){
            $unique_weekend_times = array_unique($weekend_times);
            if(count($unique_weekend_times) === 1){
                // 주말 2개 모두 있고 시간이 일관됨
                $result[] = array(
                    'name' => '주말',
                    'time' => $unique_weekend_times[0]
                );
            } else {
                // 주말 2개 모두 있지만 시간이 다름 - 개별 표시
                foreach($weekends as $day){
                    if(isset($day_times[$day])){
                        $result[] = array(
                            'name' => $days_kr[$day],
                            'time' => $day_times[$day]
                        );
                    }
                }
            }
        } else {
            // 주말이 2개 모두 없음 - 개별 표시
            foreach($weekends as $day){
                if(isset($day_times[$day])){
                    $result[] = array(
                        'name' => $days_kr[$day],
                        'time' => $day_times[$day]
                    );
                }
            }
        }
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