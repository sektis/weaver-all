<?php
$manager = wv()->store_manager->made('sub01_01');
$current_store_arr = ($manager->get_current_store());
$current_store_wr_id = $current_store_arr['wr_id'];
$current_store = $manager->get($current_store_wr_id);