<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_LIB_PATH.'/latest.lib.php');

add_stylesheet('<link rel="stylesheet" href="'.$wv_page_skin_url.'/page.css?ver='.G5_CSS_VER.'">', 11);
add_javascript('<script src="'.$wv_page_skin_url.'/page.js?ver='.G5_JS_VER.'"></script>',11);

@include_once wv('page')->plugin_theme_path.'/config.php';
ob_start();
include wv('page')->get_page_path();
$page_content = ob_get_contents();
ob_end_clean();

include_once(G5_PATH.'/head.php');

echo $page_content;
//include wv('page')->get_page_path();

include_once(G5_PATH.'/tail.php');
