<?php
$manager = wv()->store_manager->made('sub01_01');
$current_store_arr = ($manager->get_current_store());
$current_store_wr_id = $current_store_arr['wr_id'];
$current_store = $manager->get($current_store_wr_id);

//dd($current_store_wr_id);
//$ceo_member = wv()->store_manager->made('member');
//$ceo_member_table = $ceo_member->get_write_table_name();
//$row = sql_fetch("select wr_id from {$ceo_member_table} where mb_id='{$member['mb_id']}'");
//$current_member_wr_id = $row['wr_id'];
//$current_member = $ceo_member->get($current_member_wr_id);
