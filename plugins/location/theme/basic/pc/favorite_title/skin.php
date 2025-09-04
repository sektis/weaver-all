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

    <span class="location-name">&nbsp;</span>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            $.post('<?php echo wv()->location->ajax_url()?>',{'wv_location_action':'favorite_title' },function (data) {
                $(".location-name",$skin).text(data)
            },'html')
        })

    </script>
</span>


