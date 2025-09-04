<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wr_id = wv()->store_manager->made()->set($_POST);
goto_url(wv_page_url('0201_c',$qstr.'&wr_id='.$wr_id));
