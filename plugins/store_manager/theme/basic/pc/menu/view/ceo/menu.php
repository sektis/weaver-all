<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_store_wr_id,$current_store;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{font-size: var(--wv-16);font-weight: 600}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  w-full    " style="padding: var(--wv-16) 0; background-color: #fff">

        <div class="container  "  >
            <div class="">
                <p class="text1">대표메뉴</p>
                <div class="hstack justify-content-between mt-[20px] fs-[14/100%/-0.56/600/#0D171B]" style="gap:var(--wv-8)">
                    <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                       data-wv-ajax-data='{
                                               "made":"sub01_01",
                                               "part":"menu",
                                               "action":"render_part_ceo_form",
                                               "fields":"ceo/menu_form",
                                               "wr_id":"<?php echo $current_store_wr_id; ?>"
                                               }'
                       data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]"  class="h-[40px] d-flex-center border col" style="border-radius:var(--wv-4) ">
                    <span>
                        <i class="fa-solid fa-plus me-1"></i>
                       메뉴 추가
                    </span>
                    </a>
                    <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                       data-wv-ajax-data='{
                                               "made":"sub01_01",
                                               "part":"menu",
                                               "action":"render_part_ceo_form",
                                               "fields":"ceo/image",
                                               "wr_id":"<?php echo $current_store_wr_id; ?>"
                                               }'
                       data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"  class="h-[40px] d-flex-center border col" style="border-radius:var(--wv-4) ">
                    <span>
                    <i class="fa-solid fa-arrows-up-down me-1"></i>
                    순서 변경
                    </span>
                    </a>
                </div>
            </div>
            <div class="wv-mx-fit mt-[16px]" style="height: 10px;background-color: #efefef"></div>
            <div class="row" style="--bs-gutter-x: var(--wv-10);--bs-gutter-y: var(--wv-10)">
                <?php echo $current_store->menu->render_part('ceo/menu_list','view');; ?>
            </div>


        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>