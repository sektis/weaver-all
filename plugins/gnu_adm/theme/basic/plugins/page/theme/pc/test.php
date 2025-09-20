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
    'where' => array(
        'and' => array(
            array('w.wr_subject' => "LIKE '%테스트%'"),
            array('w.wr_datetime' => "> '2023-01-01'"),
            'or' => array(
                array('w.mb_id' => "= 'admin'"),
                array('w.wr_hit' => "> 100")
            )
        )
    ),

    'where_location' =>    array(
        'and'=>array(
            array('lat'=>"<>''"),
            array('lng'=>"<>''"),
        )
    ),
    'where_menu'=> array(
        array('name'=>"=' (양념)소갈빗살한판(1kg)'")
    ),
    'with_list_part'=>true,
    'select_store'=>array('list_each'=>'','service'=>array('cont_pdt_type'=>1)),
    'page'=>$page,
    'rows'=>50,
    'list_url'=>wv_page_url($wv_page_id, $qstr)
);
$result = wv()->store_manager->made('sub01_01')->get_list($get_list_option);
dd($result['sql']);
$list = $result['list'];