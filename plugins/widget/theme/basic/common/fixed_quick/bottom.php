<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<style>
    <?php echo $skin_selector?> .fq-bottom-wrap{position: fixed;pointer-events: auto; bottom: 0;width: inherit;background-color: transparent;}
    <?php echo $skin_selector?> .bottom-menu{padding: var(--wv-6) 0;box-shadow: 0 calc(var(--wv-4) * -1) var(--wv-6) 0
    rgba(183, 183, 183, 0.25);}
</style>

<div class="fq-bottom-wrap">

    <div class="bottom-menu">
        <div class="container">
        <?php echo wv('menu')->made('fixed_bottom')->displayMenu('common/foot_menu'); ?>
        </div>
    </div>
</div>


<script>

    $(document).ready(function (){


        var $skin = $("<?php echo $skin_selector?>");
        var $wrap = $(".fq-bottom-wrap",$skin);

        if($("#footer-wrapper").length && $wrap.length){

            function footer_wrapper_mb_chk() {
                $("#footer-wrapper").css('margin-bottom',$wrap.outerHeight()+'px');
            }
            footer_wrapper_mb_chk();
            $(window).on('resize',wv_debounce( footer_wrapper_mb_chk,50))
        }
    })

</script>