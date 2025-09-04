<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';
?>
<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .collapse-wrap{position: absolute;right: 0;top:50%;transform: translateY(-50%);z-index: 100;padding: var(--wv-md-30, revert-layer);padding-right: 0}
        <?php echo $skin_selector?> .collapse-icon i{transition:transform 0.5s  cubic-bezier(0.77,0.2,0.05,1.0);transform-origin: center;}
        <?php echo $skin_selector?> .collapse-icon i:before{content: "\2b"}
        <?php echo $skin_selector?> [aria-expanded="true"]>.collapse-icon i{transform: rotate(360deg);}
        <?php echo $skin_selector?> [aria-expanded="true"]>.collapse-icon i:before{content: "\f068"}
        <?php echo $skin_selector?> [class*=depth-ul-]{flex-direction: column;;font-size: var(--wv-md-27, revert-layer);}

        <?php echo $skin_selector?> .depth-wrap-1{height: 100%}
        <?php echo $skin_selector?> .depth-ul-1{}
        <?php echo $skin_selector?> .depth-li-1{border-bottom: 2px solid #F5F5F5;padding: var(--wv-md-55, revert-layer) var(--wv-50)}
        <?php echo $skin_selector?> .depth-link-1{}
        <?php echo $skin_selector?> .depth-li-1.on .depth-link-1{}

        <?php echo $skin_selector?> .depth-wrap-2 [class*=depth-link-]{font-size: calc((10 - var(--wv-menu-depth)/2) *  0.1em);color:#5F5F5F}
        <?php echo $skin_selector?> .depth-ul-2  {margin-top: var(--wv-md-14, revert-layer); }
        <?php echo $skin_selector?> .depth-li-2  {padding :var(--wv-18) 0!important;}
        <?php echo $skin_selector?> .depth-link-2{}
        <?php echo $skin_selector?> .depth-link-2:hover{}



    </style>
    <?php
    echo wv_collapse_menu('',$skin_id,$data);
    ?>


    <script>
        $(document).ready(function () {

            var $skin = $('<?php echo $skin_selector?>');

            $(".collapse",$skin).on('hide.bs.collapse',function (e) {
                $('.collapse',$(e.target)).collapse('hide')
            })
            $(".collapse",$skin).on('show.bs.collapse',function (e) {
                $('.collapse[data-depth="'+$(e.target).data('depth')+'"]',$skin).collapse('hide')
            })
        })
    </script>
</div>


