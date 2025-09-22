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

    public function get_indexes(){
        return array(
            array()
        );
    }

    public function column_extend($row,$all_row=array()){
        $arr = array();
        if(isset($row['open_time']) && !empty($row['open_time'])){
            $arr['open_time_summary'] = $this->generate_open_time_summary($row['open_time']);
            $arr['open_time_list'] = $this->generate_open_time_list($row['open_time']);
        } else {
            $arr['open_time_summary'] = array();
            $arr['open_time_list'] = array();
        }

        if(isset($row['break_time']) && !empty($row['break_time'])){
            $arr['break_time_summary'] = $this->generate_open_time_summary($row['break_time']);
            $arr['break_time_list'] = $this->generate_open_time_list($row['break_time']);
        } else {
            $arr['break_time_summary'] = array();
            $arr['break_time_list'] = array();
        }

        return $arr;
    }

    protected function generate_open_time_summary($open_time_data){
        if(!is_array($open_time_data)) {
            return '';
        }

        $summary = array();
        $days_kr = array(
            'mon' => '월요일', 'tue' => '화요일', 'wed' => '수요일',
            'thu' => '목요일', 'fri' => '금요일', 'sat' => '토요일', 'sun' => '일요일'
        );

        // 1. 기본값으로 daily 설정 사용
        $default_start = isset($open_time_data['daily']['start']) ? $open_time_data['daily']['start'] : null;
        $default_end = isset($open_time_data['daily']['end']) ? $open_time_data['daily']['end'] : null;

        // 2. 평일/주말 설정 확인 및 덮어씌우기
        $weekday_start = isset($open_time_data['weekday']['start']) ? $open_time_data['weekday']['start'] : $default_start;
        $weekday_end = isset($open_time_data['weekday']['end']) ? $open_time_data['weekday']['end'] : $default_end;

        $weekend_start = isset($open_time_data['weekend']['start']) ? $open_time_data['weekend']['start'] : $default_start;
        $weekend_end = isset($open_time_data['weekend']['end']) ? $open_time_data['weekend']['end'] : $default_end;

        // 평일 시간이 있으면 추가
        if($weekday_start && $weekday_end){
            $weekday_time = $this->format_time($weekday_start) . ' ~ ' . $this->format_time($weekday_end);
//            $summary[] = '(평일) ' . $weekday_time;
            $summary[] = array('name'=>'평일','time'=>$weekday_time);
        }

        // 주말 시간이 있으면 추가
        if($weekend_start && $weekend_end){
            $weekend_time = $this->format_time($weekend_start) . ' ~ ' . $this->format_time($weekend_end);
//            $summary[] = '(주말) ' . $weekend_time;
            $summary[] = array('name'=>'주말','time'=>$weekend_time);
        }

        // 3. 요일별 설정이 있으면 추가
        foreach($days_kr as $day_key => $day_name){
            if(isset($open_time_data[$day_key]['enabled']) && $open_time_data[$day_key]['enabled']){
                $day_start = isset($open_time_data[$day_key]['start']) ? $open_time_data[$day_key]['start'] : null;
                $day_end = isset($open_time_data[$day_key]['end']) ? $open_time_data[$day_key]['end'] : null;

                if($day_start && $day_end){
                    $day_time = $this->format_time($day_start) . ' ~ ' . $this->format_time($day_end);
//                    $summary[] = '(' . $day_name . ') ' . $day_time;
                    $summary[] = array('name'=>$day_name,'time'=>$day_time);
                }
            }
        }

        return $summary;
    }

    protected function generate_open_time_list($open_time_data){
        if(!is_array($open_time_data)) {
            return '';
        }

        $summary = array();
        $days_kr = array(
            'mon' => '월요일', 'tue' => '화요일', 'wed' => '수요일',
            'thu' => '목요일', 'fri' => '금요일', 'sat' => '토요일', 'sun' => '일요일'
        );

        // 1. 기본값으로 daily 설정 사용
        $default_start = isset($open_time_data['daily']['start']) ? $open_time_data['daily']['start'] : null;
        $default_end = isset($open_time_data['daily']['end']) ? $open_time_data['daily']['end'] : null;
        if($default_start && $default_end){
            $weekday_time = $this->format_time($default_start) . ' ~ ' . $this->format_time($default_end);
//            $summary[] = '(평일) ' . $weekday_time;
            $summary[] = array('name'=>'매일','time'=>$weekday_time);
        }

        // 2. 평일/주말 설정 확인 및 덮어씌우기
        $weekday_start = isset($open_time_data['weekday']['start']) ? $open_time_data['weekday']['start'] : null;
        $weekday_end = isset($open_time_data['weekday']['end']) ? $open_time_data['weekday']['end'] : null;
        if($weekday_start && $weekday_end){
            $weekday_time = $this->format_time($weekday_start) . ' ~ ' . $this->format_time($weekday_end);
//            $summary[] = '(평일) ' . $weekday_time;
            $summary[] = array('name'=>'평일','time'=>$weekday_time);
        }

        $weekend_start = isset($open_time_data['weekend']['start']) ? $open_time_data['weekend']['start'] : $default_start;
        $weekend_end = isset($open_time_data['weekend']['end']) ? $open_time_data['weekend']['end'] : $default_end;

        if($weekend_start && $weekend_end){
            $weekday_time = $this->format_time($weekend_start) . ' ~ ' . $this->format_time($weekend_end);
//            $summary[] = '(평일) ' . $weekday_time;
            $summary[] = array('name'=>'주말','time'=>$weekday_time);
        }


        // 3. 요일별 설정이 있으면 추가
        foreach($days_kr as $day_key => $day_name){
            if(isset($open_time_data[$day_key]['enabled']) && $open_time_data[$day_key]['enabled']){
                $day_start = isset($open_time_data[$day_key]['start']) ? $open_time_data[$day_key]['start'] : null;
                $day_end = isset($open_time_data[$day_key]['end']) ? $open_time_data[$day_key]['end'] : null;

                if($day_start && $day_end){
                    $day_time = $this->format_time($day_start) . ' ~ ' . $this->format_time($day_end);
//                    $summary[] = '(' . $day_name . ') ' . $day_time;
                    $summary[] = array('name'=>$day_name,'time'=>$day_time);
                }
            }
        }

        return $summary;
    }


    protected function format_time($time_array){
        if(!is_array($time_array) || !isset($time_array['period']) || !isset($time_array['hour']) || !isset($time_array['minute'])){
            return '';
        }

        $period = $time_array['period'] === 'am' ? '오전' : '오후';
        $hour = str_pad($time_array['hour'], 2, '0', STR_PAD_LEFT);
        $minute = str_pad($time_array['minute'], 2, '0', STR_PAD_LEFT);

        return $period . ' ' . $hour . ':' . $minute;
    }


    protected function wv_biz_time_select($name, $data, $type) {
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
}
