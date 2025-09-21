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

    <span class="hstack" style="gap:var(--wv-6);filter: brightness(0) saturate(100%) invert(62%) sepia(1%) saturate(1638%) hue-rotate(204deg) brightness(97%) contrast(93%);">
         <img src="<?php echo WV_URL.'/img/icon_location.png'; ?>" class="w-[16px]" style="">
            <span class="location-name fs-[16//-0.64/600/#97989C]">&nbsp;</span>
           <img src="<?php echo WV_URL.'/img/icon_arrow_down.png'; ?>" class="w-[16px]" alt="">
    </span>


    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            $.post('<?php echo wv()->location->ajax_url()?>',{'wv_location_action':'favorite_title' },function (data) {
                $(".location-name",$skin).text(data)
            },'html')
        })

    </script>
</span>

