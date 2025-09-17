<?php
$common_file_path = '../../common.php';
for ($i=1;$i<=17;$i++){
    if(file_exists($common_file_path)){
        include_once $common_file_path;
        break;
    }
    $common_file_path = '../'.$common_file_path;
}
if (!defined('_GNUBOARD_')) die('not found gnuboard'); // 개별 페이지 접근 불가

