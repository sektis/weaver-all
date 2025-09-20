<?php
$sfl_options = array(
    'jm.mb_id'    => '아이디',
    'jm.mb_name'  => '대표자이름',
    'store_name' =>'매장이름'
);
$sfl_whitelist = array_keys($sfl_options);



$get_list_where = array();
if($stx !== '') {
    $stx_esc = sql_escape_string($stx);
    if ($sfl && in_array($sfl, $sfl_whitelist)) {
        $get_list_where[] = "   {$sfl} LIKE '%{$stx_esc}%' ";
    }
}

$get_list_option = array(
    'where'=>$get_list_where,

    'where_location' =>    array(
        'and'=>array(
            array('lat'=>"<>''"),
            array('lng'=>"<>''"),
        )
    ),
    'where_menu'=> array(
        array('name'=>"='항정살'")
    ),
    'with_list_part'=>true,
    'page'=>$page,
    'rows'=>20,
    'list_url'=>wv_page_url($wv_page_id, $qstr)
);
$result = wv()->store_manager->made('sub01_01')->get_list($get_list_option);
dd($result);
$list = $result['list'];