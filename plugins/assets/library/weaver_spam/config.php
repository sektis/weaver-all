<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

add_event('tail_sub','wv_spam_honey_spot_add');
add_event('write_update_before','wv_spam_honey_spot_check');
add_event('register_form_update_before','wv_spam_honey_spot_check');

function wv_spam_honey_spot_add(){
    global $bo_table;

    $form_id='';

    if((wv_info('dir')=='bbs' and wv_info('file')=='write' and $bo_table  )){
        $form_id='fwrite';
    }

    if((wv_info('dir')=='bbs' and (wv_info('file')=='register' or wv_info('file')=='register_form')   )){
        $form_id='fregisterform';
    }

    if(!$form_id){
        return;
    }

    include_once dirname(__FILE__).'/spam_check.php';
}

function wv_spam_honey_spot_check(){

    if(!isset($_POST['wv_h_spot']) or $_POST['wv_h_spot']!='wv'){
        alert('스팸으로 의심되어 등록이 불가합니다.');
    }

}