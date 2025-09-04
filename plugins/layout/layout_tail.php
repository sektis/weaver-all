<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wv_layout_tail_path = wv('layout')->get_layout_tail_path();

if($wv_layout_tail_path){
    echo '</div>'; // #content-wrapper
    include_once $wv_layout_tail_path;
    echo '</div>'; // #site-wrapper
}else{
    @eval(wv('layout')->get_org_tail_code());
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}

include_once(G5_PATH."/tail.sub.php");
