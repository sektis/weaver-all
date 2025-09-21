<?php
$sfl_options = array(
    'jm.mb_id'    => '아이디',
    'jm.mb_name'  => '대표자이름',
    'store_name' =>'매장이름'
);
$sfl_whitelist = array_keys($sfl_options);

dd(wv()->store_manager->made('sub01_01')->get(20680)->store->list_each);

$get_list_where = array();
if($stx !== '') {
    $stx_esc = sql_escape_string($stx);
    if ($sfl && in_array($sfl, $sfl_whitelist)) {
        $get_list_where[] = "   {$sfl} LIKE '%{$stx_esc}%' ";
    }
}

$get_list_option = array(


    'where_location' =>    array(
        'and'=>array(
            array('lat'=>"<>''"),
            array('lng'=>"<>''"),
        )
    ),
    'with_list_part'=>true,
    'select_store'=>array('list_each'=>'','service'=>array('cont_pdt_type'=>1)),
    'page'=>$page,
    'rows'=>1,
    'list_url'=>wv_page_url($wv_page_id, $qstr)
);
$result = wv()->store_manager->made('sub01_01')->get_list($get_list_option);

$list = $result['list'];
dd($result);