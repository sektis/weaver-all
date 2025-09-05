<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Biz extends StoreSchemaBase implements StoreSchemaInterface{


    protected $columns = array(
        'open_time'=>'TEXT DEFAULT NULL',
        'break_time' => "TEXT DEFAULT NULL",
        'is_holiday_off' => "TINYINT(1) NOT NULL DEFAULT 0",

    );


    public function get_indexes(){
        return array(
            array()
        );
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
