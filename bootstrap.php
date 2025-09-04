<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (PHP_VERSION < '5.3.0') {
    //if (function_exists("date_default_timezone_set")) date_default_timezone_set("Asia/Seoul");
    die('php버전이 5.3이상이어야합니다.');
}
$php_disable_functions = array_map('trim', explode(',',ini_get('disable_functions')));

if(count(array_intersect($php_disable_functions, array('eval','show_source')))){
    die('php disable_functions에 eval, show_source이 포함되어있습니다.');
}
if(!function_exists('add_event')){
    include_once(dirname(__FILE__).'/Hook/hook.lib.php');    // hook 함수 파일
}

include_once dirname(__FILE__).'/lib/common.lib.php';
include_once dirname(__FILE__).'/class/Weaver.php';
include_once dirname(__FILE__).'/setting.php';
include_once dirname(__FILE__).'/eval_action.php';