<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
// 처리 페이지: 사장님(레벨3) 등록/수정

$back_url = wv_page_url('0101');
$form_url = wv_page_url('0101_c');

// 입력값 수집
$mb_id   = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
$pk_mb_id= isset($_POST['pk_mb_id']) ? trim($_POST['pk_mb_id']) : '';
$mb_name = isset($_POST['mb_name']) ? trim($_POST['mb_name']) : '';
$mb_email= isset($_POST['mb_email']) ? trim($_POST['mb_email']) : '';
$pw      = isset($_POST['mb_password']) ? (string)$_POST['mb_password'] : '';
$pw_re   = isset($_POST['mb_password_re']) ? (string)$_POST['mb_password_re'] : '';
// 닉네임은 폼에 없으므로 서버에서 생성
$mb_nick = isset($_POST['mb_nick']) ? trim($_POST['mb_nick']) : '';



if($w == ''){
    if($mb_id === ''){
        alert('아이디를 입력하세요.', $form_url);
    }
    if($pw === '' || $pw_re === ''){
        alert('비밀번호와 비밀번호 확인을 입력하세요.', $form_url);
    }
    if($pw !== $pw_re){
        alert('비밀번호가 일치하지 않습니다.', $form_url);
    }
}

if($mb_name === ''){
    alert('대표자이름을 입력하세요.', $form_url);
}

if($mb_email !== '' && !filter_var($mb_email, FILTER_VALIDATE_EMAIL)){
    alert('이메일 형식이 올바르지 않습니다.', $form_url);
}

// 서버 강제값
$mb_level = '3';

// SQL 이스케이프
$q_mb_id    = sql_escape_string($mb_id);
$q_pk_mb_id = sql_escape_string($pk_mb_id);
$q_mb_name  = sql_escape_string($mb_name);
$q_mb_email = sql_escape_string($mb_email);

// 닉네임 생성(없으면 mb_id_YYYYMMDDHHIISS)
if($mb_nick === ''){
    $mb_nick = $mb_id . '_' . date('YmdHis');
}
$q_mb_nick = sql_escape_string($mb_nick);

// 닉네임 유니크 보장(최대 5회 시도)
for($i=0; $i<5; $i++){
    $cnt = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['member_table']} WHERE mb_nick = '{$q_mb_nick}' ".($mode==='update' && $q_pk_mb_id !== '' ? " AND mb_id <> '{$q_pk_mb_id}' " : '')." ");
    if( (int)$cnt['cnt'] === 0 ){
        break;
    }
    // 중복이면 짧은 랜덤 suffix 추가
    $q_mb_nick = sql_escape_string($mb_nick.'_'.substr(md5(uniqid('', true)), 0, 4));
}

// 모드별 처리
if($w == ''){
    // 아이디 중복 체크
    $dup = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['member_table']} WHERE mb_id = '{$q_mb_id}' ");
    if( (int)$dup['cnt'] > 0 ){
        alert('이미 존재하는 아이디입니다.', $form_url);
    }

    // 패스워드 암호화
    $enc_pw = get_encrypt_string($pw);

    // INSERT
    $sql = "
        INSERT INTO {$g5['member_table']}
        SET mb_id         = '{$q_mb_id}',
            mb_password   = '{$enc_pw}',
            mb_name       = '{$q_mb_name}',
            mb_nick       = '{$q_mb_nick}',
            mb_email      = '{$q_mb_email}',
            mb_level      = '{$mb_level}',
            mb_datetime   = '".G5_TIME_YMDHIS."',
            mb_ip         = '{$_SERVER['REMOTE_ADDR']}'
    ";
    sql_query($sql);
    alert('등록되었습니다.', wv_page_url('0101_c','w=u&mb_id='.$q_mb_id));

}else{ // update
    if($q_pk_mb_id === ''){
        alert('수정 대상이 없습니다.');
    }

    // 존재 확인
    $ex = sql_fetch(" SELECT mb_id FROM {$g5['member_table']} WHERE mb_id = '{$q_pk_mb_id}' LIMIT 1 ");
    if(!$ex || !$ex['mb_id']){
        alert('존재하지 않는 회원입니다.');
    }

    // 비밀번호 변경 요청 시 검증 및 세팅
    $set_pw = '';
    if($pw !== '' || $pw_re !== ''){
        if($pw === '' || $pw_re === '' || $pw !== $pw_re){
            alert('비밀번호 변경값이 올바르지 않습니다.');
        }
        $enc_pw = get_encrypt_string($pw);
        $set_pw = " , mb_password = '{$enc_pw}' ";
    }

    // UPDATE (아이디는 pk 기준 고정)
    $sql = "
        UPDATE {$g5['member_table']}
           SET mb_name  = '{$q_mb_name}',
               mb_nick  = '{$q_mb_nick}',
               mb_email = '{$q_mb_email}',
               mb_level = '{$mb_level}'
               {$set_pw}
         WHERE mb_id    = '{$q_pk_mb_id}'
         LIMIT 1
    ";
    sql_query($sql);

    alert('수정되었습니다.', wv_page_url('0101_c','w=u&mb_id='.$q_pk_mb_id));
}
