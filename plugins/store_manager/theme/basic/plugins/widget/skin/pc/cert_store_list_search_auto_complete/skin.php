<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $member;
if($member['mb_id']){

    $member_manager = wv()->store_manager->made('member');
    $member_manager_table = $member_manager->get_write_table_name();
    $row = sql_fetch("select wr_id from {$member_manager_table} where mb_id='{$member['mb_id']}'");
    $current_member_wr_id = $row['wr_id'];
    $current_member = $member_manager->get($current_member_wr_id);


}
// Store Manager에서 매장 목록 조회
$manager = wv()->store_manager->made('sub01_01');

// WHERE 조건 구성 (확장테이블 s의 물리키 사용)

// get_list 옵션
$options = array(
    'select'=>array(),
    'where' =>    array(
        " location_lat  <>'' ",
        " location_lng <>'' "
    ),
    'select_store'=>array('search_auto_each'),
    'order_by' => 'w.wr_datetime DESC',
    'rows' => $rows?$rows:15,
    'with_list_part'=>false
);

$center = wv()->location->get('current');


$options = wv_make_distance_options($center['lat'],$center['lng'],$options, 30);

if($order and $order!='near'){
    $options['order_by'] = $order;

}

if($data['q']){
    $data['q']=trim($data['q']);
    $q_arr = array(
        'or'=>array(
            'where_store'=>array(
                array('name'=>" like '%{$data['q']}%'"),
            ),
            'where_menu'=>array(
                array('name'=>" like '%{$data['q']}%'")
            )
        )
    );
    $options['where'] = array_merge_recursive($options['where'],$q_arr);


   ;
}


$result = $manager->get_list($options);
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget store-list-widget h-100" style="">
    <style>
        <?php echo $skin_selector?> {}
    </style>

    <div class="position-relative col col-lg-auto w-full  h-100 " style="overflow-y: auto;overflow-x: hidden">

        <div class="vstack  " style=" ">
            <?php foreach ($result['list'] as $each){?>
                <div style="padding: var(--wv-12) 0 var(--wv-12)">
                    <?php echo $each['store']['search_auto_each'];; ?>

                </div>
                <div class="wv-mx-fit" style="border-top: var(--wv-1) solid #efefef"></div>
            <?php } ?>



            <?php if(!count($result['list'])){ ?>
                <div class="store-empty" style=" text-align: center;padding: var(--wv-40);color: #666;">
                    <i class="fa-solid fa-store-slash" style="font-size: 48px; color: #ddd; margin-bottom: 16px;"></i>
                    <p>등록된 매장이 없습니다.</p>
                </div>
            <?php } ?>

        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");


        });
    </script>
</div>