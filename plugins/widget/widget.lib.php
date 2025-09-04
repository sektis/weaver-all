<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!function_exists('wv_widget')){
    function wv_widget($skin,$data='',$make_name=''){
        return wv('widget')->make($make_name)->display_widget($skin,$data);
    }
}
