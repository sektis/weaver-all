<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once dirname(__FILE__).'/lib.php';
if(!function_exists('file_get_html')){
    include_once dirname(__FILE__).'/simple_html_dom.php';
}

