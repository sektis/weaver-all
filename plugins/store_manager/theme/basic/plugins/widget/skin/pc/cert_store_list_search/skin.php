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
    'select_store'=>array('cert_search_each'),
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
    $set_data = array(
        'wr_id'=>$current_member_wr_id,
        'member'=>array(
            'search_store_history'
        )
    );


    $next_index = (max(array_keys($current_member->member->search_store_history)))+1;

    $post_data=array(
        'wr_id'=>$current_member_wr_id,
    );
    $new_data = array(
        $next_index=>array('text'=>$data['q'])
    );
    $post_data['member']['search_store_history'] = $current_member->member->search_store_history?$current_member->member->search_store_history:array();
    $post_data['member']['search_store_history'] = $post_data['member']['search_store_history']+$new_data;
    $post_data['member']['search_store_history'] = array_slice($post_data['member']['search_store_history'], -10,null, true);


    $member_manager->set($post_data);
}


$result = $manager->get_list($options);
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget store-list-widget h-100" style="">
    <style>
        <?php echo $skin_selector?> {}
    </style>

    <div class="position-relative col col-lg-auto w-full  h-100 " >
        <div class="container">
            <div class="hstack justify-content-between h-[48px]" style="">
                <div class="hstack fs-[14/20/-0.56/600/#0D171B]" style="gap:var(--wv-4)">
                    <span>검색 결과 </span>
                    <span class="text-[#19bbc0]"><?php echo number_format($result['total_count']); ?></span>
                </div>
                <div class="wv-dropdown-select">
                    <button type="button" class=" btn" data-bs-toggle="dropdown"  >
                      <span class="hstack" style="gap:var(--wv-6)">
                           <span class="dropdown-label fs-[14/100%/-0.56/500/#0D171B]"></span>
                        <img src="<?php echo $wv_skin_url; ?>/arrow_down.png" class="w-[13px]" alt="">
                      </span>
                    </button>
                    <ul class="dropdown-menu bg-white" style="width: max-content;">
                        <div class="vstack align-items-start" style="padding: var(--wv-15) var(--wv-15);row-gap: var(--wv-10)">
                            <a class="  fs-[14/100%/-0.56/500/#0D171B] px-0 text-center" data-order-value="near" href="#"> <img src="<?php echo $wv_skin_url; ?>/near.png" class="w-[13px]" alt=""> 가까운 순</a>
                        </div>
                    </ul>
                </div>
            </div>
            <div class="vstack wv-mx-fit " style=" ">
                <?php foreach ($result['list'] as $each){?>
                    <div style="padding: var(--wv-12) 0 0;overflow-y: auto;overflow-x: hidden"  >
                        <?php echo $each['store']['cert_search_each'];; ?>

                    </div>
                    <div class="wv-mx-fit" style="border-top: var(--wv-6) solid #efefef"></div>
                <?php } ?>



                <?php if(!count($result['list'])){ ?>
                    <div class="store-empty" style=" text-align: center;padding: var(--wv-40);color: #666;">
                        <i class="fa-solid fa-store-slash" style="font-size: 48px; color: #ddd; margin-bottom: 16px;"></i>
                        <p>등록된 매장이 없습니다.</p>
                    </div>
                <?php } ?>

            </div>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");

        });
    </script>
</div>