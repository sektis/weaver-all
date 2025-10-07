<?php
include_once '_common.php';
if(!$member_wr_id or !$mb_id){
    alert('픽수파라메터누락');
}
set_session("wv_cert_type",    get_session("ss_cert_type"));
set_session("wv_cert_no",      get_session("ss_cert_no"));
set_session("wv_cert_hash",    get_session("ss_cert_hash"));
set_session("wv_cert_adult",   get_session("ss_cert_adult"));
set_session("wv_cert_birth",   get_session("ss_cert_birth"));
set_session("wv_cert_sex",     get_session("ss_cert_sex"));
set_session('wv_cert_dupinfo', get_session("ss_cert_dupinfo"));
set_session('wv_cert_mb_name', $mb_name);
set_session('wv_cert_mb_hp', $mb_hp);
$data = array(
    'wr_id'=>$member_wr_id,
    'mb_id'=>$mb_id,
    'mb_hp'=>$mb_hp,
    'mb_name'=>$mb_name,
    'mb_certify'=>get_session("ss_cert_type"),
    'mb_adult'=>get_session("ss_cert_adult"),
    'mb_birth'=>get_session('ss_cert_birth'),
    'mb_sex'=>get_session('ss_cert_sex'),
    'mb_dupinfo'=>get_session('ss_cert_dupinfo')

);

wv()->store_manager->made('member')->set($data);
insert_member_cert_history($mb_id, $mb_name, $mb_hp, get_session('ss_cert_birth'), get_session('ss_cert_type') ); // 본인인증 후 정보 수정 시 내역 기록