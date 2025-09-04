<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');


$wv_layout_head_path = wv('layout')->get_layout_head_path();
if($wv_layout_head_path){
    if(wv_info('type')=='index') { // index에서만 실행
        include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
    }
    @include_once dirname(wv('layout')->get_layout_path()).'/config.php';
    echo '<div id="site-wrapper">';
    run_event('wv_hook_before_header_wrapper');
    include_once $wv_layout_head_path;
    echo '<div id="content-wrapper">';
}else{
    @eval(wv('layout')->get_org_head_code());
}


