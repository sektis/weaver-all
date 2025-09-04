<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 스마트에디터 이미지 업로드 시 경로만 입력되게
add_replace('get_editor_upload_url','wv_get_editor_upload_url',0,3);
function wv_get_editor_upload_url( $url, $file_path, $file){
    $parse = (parse_url($url));
    return $parse['path'];
}