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
                <p class="text1">사장님 공지</p>
                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                   data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"store","field":"ceo/notice","wr_id":"<?php echo $current_store_wr_id; ?>"}'
                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]"  class="fs-[14/100%/-0.56/600/#97989C]"> <img src="<?php echo $this->manager->plugin_url ?>/img/vec2.png" class="w-[14px]" alt=""> <span>변경</span></a>
            </div>
            <p class="fs-[16/22/-0.64/600/#0D171B] wv-line-clamp" style="--wv-line-clamp-length: 1"><?php echo $row['notice']; ?></p>
            <div class="h-[38px] hstack align-items-center" style="border-radius: var(--wv-4);background-color: #f9f9f9;gap:var(--wv-2);padding: 0 var(--wv-8)">

                <img src="<?php echo $this->manager->plugin_url ?>/img/vec1.png" class="w-[14px]" alt="">
                <p class="fs-[12/17/-0.48/500/#97989C]">고객들에게 내 매장에 대한 특징을 소개해보세요</p>

            </div>

        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>