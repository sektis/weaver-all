<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';
?>
<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> a{white-space: nowrap}

        <?php echo $skin_selector?> .menu-btn{--wv-hamburger-width:40;position: relative;z-index: 1046}
        <?php echo $skin_selector?> .offcanvas{--bs-offcanvas-width:auto;--bs-offcanvas-bg:transparent;--bs-offcanvas-border-width:0;--bs-offcanvas-transition: transform 0.5s  cubic-bezier(0.77,0.2,0.05,1.0)}

        <?php echo $skin_selector?> .depth-ul-1{align-items: start;gap: var(--wv-30)}
        <?php echo $skin_selector?> .depth-li-1{}{transition: all .1s;}
        <?php echo $skin_selector?> .depth-link-1{transition: all .1s;font-size: var(--wv-20);text-align: center}


        <?php echo $skin_selector?> .depth-wrap-2{margin-top: var(--wv-20)}
        <?php echo $skin_selector?> .depth-ul-2{gap: var(--wv-20);flex-direction: column;}
        <?php echo $skin_selector?> .depth-li-2{}
        <?php echo $skin_selector?> .depth-link-2{text-align: center;}


        <?php echo $skin_selector?> .menu-background{display: flex;justify-content: center;box-shadow: 3.5px 8.3px 13px 0 rgba(0, 0, 0, 0.29);background-color: rgba(255, 255, 255, .8);padding: var(--wv-103) var(--wv-50);border-bottom-left-radius: 40px 40px;}
        <?php echo $skin_selector?> .menu-background .menu-background-inner{}


    </style>
    <div class="wv-hamburger-9 menu-btn" data-bs-toggle="offcanvas" href="<?php echo $skin_selector?> .offcanvas">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
    </div>

    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false">

        <div class="offcanvas-body p-0">
            <div class="menu-background" >
                <div class="menu-background-inner">
                    <?php
                    $sub_contents='';
                    echo wv_depth_menu('',$skin_id,$data,2);
                    ?>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function () {
            var $skin = $('<?php echo $skin_selector?>');
            var $menu_btn = $(".menu-btn",$skin);
            var $menu_background = $('.menu-background',$skin);


        })
    </script>
</div>


