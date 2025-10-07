<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<span id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?>  "    >
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <span class="hstack" style="gap:var(--wv-6)">
        <p class="fs-[20/130%/-0.8/700/#0D171B] store-name"></p>
        <img src="<?php echo WV_URL.'/img/icon_arrow_down.png'; ?>" class="w-[16px]" alt="">
    </span>


    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            wv_ajax('<?php echo wv()->store_manager->ajax_url()?>',"replace_in:.store-name",{"action":"get_current_store"});
            //$.post('<?php //echo wv()->store_manager->ajax_url()?>//',{'action':'selected_store' },function (data) {
            //    $(".location-name",$skin).text(data)
            //},'html')
        })

    </script>
</span>

