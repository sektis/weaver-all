<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget" style="position: fixed;height: 100%;pointer-events: none;max-width:inherit;width: inherit; z-index: 1000;top:0" >
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?>:has(.modal.show){ ;position: static!important;}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {
            <?php echo $skin_selector?> .left-wrap{right: unset;left:0;}
            <?php echo $skin_selector?> .right-wrap{left: unset;right:0;}
        }
    </style>


        <?php include_once $wv_skin_path.'/left.php'?>

        <?php include_once $wv_skin_path.'/right.php'?>

        <?php include_once $wv_skin_path.'/bottom.php'?>


    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

        })

    </script>
</div>