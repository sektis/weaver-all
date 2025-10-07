<?php
include_once '_common.php';
if(!$mb_id){
    alert('파라메터 누락');
}
sql_query("update  {$g5['member_table']} set mb_dupinfo='' where mb_id='{$mb_id}'");