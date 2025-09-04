<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$wv_layout_head_extend = $wv_layout_tail_extend = '';
$wv_layout_path = wv('layout')->get_layout_path();
if($wv_layout_path){
    ob_start();
    include_once($wv_layout_path);
    $wv_layout_content= ob_get_clean();
    if(strpos($wv_layout_content,'<!-->')!== false){
        preg_match_all("`(.*)?<!-->(.*?)<!-->(.*)?`isu",$wv_layout_content,$include_match);
        $wv_layout_head_extend = $include_match[1][0];
        $wv_layout_tail_extend = $include_match[3][0];
    }
}