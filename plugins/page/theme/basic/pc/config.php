<?php
global $member;
$member_manager = wv()->store_manager->made('member');
$member_manager_table = $member_manager->get_write_table_name();
$row = sql_fetch("select wr_id from {$member_manager_table} where mb_id='{$member['mb_id']}'");
$current_member_wr_id = $row['wr_id'];
$current_member = $member_manager->get($current_member_wr_id);

