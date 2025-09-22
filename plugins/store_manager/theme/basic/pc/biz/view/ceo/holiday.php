<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_store_wr_id;

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{font-size: var(--wv-16);font-weight: 600}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  w-full    " style="padding: var(--wv-16) 0; background-color: #fff">

        <div class="container vstack" style="row-gap: var(--wv-20)">
            <div class="hstack justify-content-between">
                <p class="text1">휴무일</p>
                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                   data-wv-ajax-data='{
                                               "made":"sub01_01",
                                               "part":"biz",
                                               "action":"render_part_ceo_form",
                                               "fields":"ceo/holiday",
                                               "wr_id":"<?php echo $current_store_wr_id; ?>"
                                               }'
                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"  class="fs-[14/100%/-0.56/600/#97989C]"> <img src="<?php echo $this->manager->plugin_url; ?>/img/vec2.png" class="w-[14px]" alt=""> <span>변경</span></a>
            </div>


        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>