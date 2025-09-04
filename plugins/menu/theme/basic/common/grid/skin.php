<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .depth-ul-1{align-items: start;gap: var(--wv-30)}
        <?php echo $skin_selector?> .depth-li-1{transition: all .1s }
        <?php echo $skin_selector?> .depth-link-1{transition: all .1s;font-size: var(--wv-20);text-align: center}


        <?php echo $skin_selector?> .depth-wrap-2{margin-top: var(--wv-20)}
        <?php echo $skin_selector?> .depth-ul-2{gap: var(--wv-20);flex-direction: column;}
        <?php echo $skin_selector?> .depth-li-2{}
        <?php echo $skin_selector?> .depth-link-2{text-align: center;}

    </style>

    <div class="position-relative">
        <?php
        $sub_contents='';
        echo wv_depth_menu('',$skin_id,$data,2);
        ?>
    </div>

    <script>
        $(document).ready(function () {

            var $skin = $('<?php echo $skin_selector?>');


        })
    </script>
</div>


