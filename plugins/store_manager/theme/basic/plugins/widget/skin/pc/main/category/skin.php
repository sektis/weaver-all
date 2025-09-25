<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$manager = wv()->store_manager->made('store_category');
$get_list_option = array(

    'where_storecategory' =>    array(
        array('icon_main'=>"<>'YToxOntzOjI6ImlkIjtzOjA6IiI7fQ=='"),
    ),

    'rows'=>999,
    'order_by'   => 'w.wr_id asc',
);
$result = $manager->get_list($get_list_option);
$rows = $result['list'];

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}




        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="">
        <div class="container">


                <!-- 정기휴무 목록 -->
                <div class="row row-cols-5" style="--bs-gutter-x: var(--wv-14);--bs-gutter-y: var(--wv-12)">
                    <div class="col">
                        <div class="text-center vstack justify-content-center position-relative" style="row-gap: var(--wv-2)">
                            <div class="w-[54px] h-[54px]">
                                <img src="<?php echo $manager->plugin_url; ?>/img/category_list/all.png" class="wh-100 object-fit-contain" alt="">
                            </div>
                            <p class="fs-[11/140%//500/] text-nowrap">전체</p>
                            <a href="<?php echo wv_page_url('0101',array('view_type'=>'list')); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                    <?php
                    foreach ($rows as $cate){

                        ?>
                        <div class="col">
                            <div class="text-center vstack justify-content-center position-relative" style="row-gap: var(--wv-2)">
                                <div class="w-[54px] h-[54px]">
                                    <img src="<?php echo $cate['storecategory']['icon_main']['path']; ?>" class="wh-100 object-fit-contain" alt="">
                                </div>
                                <p class="fs-[11/140%//500/] text-nowrap"><?php echo $cate['storecategory']['name']; ?></p>
                                <a href="<?php echo wv_page_url('0101',array('view_type'=>'list','category_wr_id'=>$cate['wr_id'])); ?>" class="stretched-link"></a>
                            </div>
                        </div>
                        <?php
                    } ?>
                    <div class="col">
                        <div class="text-center vstack justify-content-cente position-relative" style="row-gap: var(--wv-2)">
                            <div class="w-[54px] h-[54px]">
                                <img src="<?php echo $manager->plugin_url; ?>/img/category_list/other.png" class="wh-100 object-fit-contain" alt="">
                            </div>
                            <p class="fs-[11/140%//500/] text-nowrap">기타</p>
                            <a href="<?php echo wv_page_url('0101',array('view_type'=>'list','category_wr_id'=>'other')); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>




        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

        })

    </script>
</div>