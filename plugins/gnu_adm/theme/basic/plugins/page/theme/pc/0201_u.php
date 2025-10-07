<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$res = wv()->store_manager->made('sub01_01')->set($_POST);

goto_url(wv_page_url('0201_c',$qstr.'&wr_id='.$res['wr_id']));
