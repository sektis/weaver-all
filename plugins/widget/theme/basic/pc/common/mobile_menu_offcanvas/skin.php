<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $is_member,$member;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget h-100" style="" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .menu-btn{--wv-hamburger-width:60;position: relative;z-index: 1046}
        <?php echo $skin_selector?> .menu-btn.is-active{--wv-hamburger-bg:#fff}
        <?php echo $skin_selector?> .offcanvas{--bs-offcanvas-width:auto;--bs-offcanvas-bg:transparent;--bs-offcanvas-border-width:0;--bs-offcanvas-transition: transform 0.5s  cubic-bezier(0.77,0.2,0.05,1.0);--bs-offcanvas-zindex:9999}
        <?php echo $skin_selector?> .offcanvas-top{width: var(--wv-md-800, revert-layer);position: relative;padding:var(--wv-md-37) var(--wv-md-29) var(--wv-md-37);background-color:  #000;border-radius: 0 20px 0 0}
        <?php echo $skin_selector?> .offcanvas-bottom{flex: 1 1 auto !important;padding:var(--wv-md-50) var(--wv-md-50)  ;background-color: #fff;}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="wv-hamburger-9 menu-btn" data-bs-toggle="offcanvas" href="<?php echo $skin_selector?> .offcanvas">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
    </div>

    <div class="offcanvas offcanvas-start" data-bs-scroll="true" >

        <div class="offcanvas-body p-0  overflow-y-auto overflow-x-hidden"   >

            <div class="min-vh-100 d-flex flex-column"  >
                <div class="offcanvas-top">
                    <img src="<?php echo $wv_layout_skin_url ?>/img/logo.png" style="width:var(--wv-md-240); " alt="">

                    <div class="position-relative mt-[50px] fs-[25//]"  >
                        <?php if ($is_member) {  ?>
                            <div class="text-white h-stack">
                                <p style="padding: var(--wv-30) 0">
                                    <?php echo $member['mb_name']?>님 반갑습니다.

                                    <?php if ($is_admin) {  ?>
                                        <a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>"><i class="fa fa-cog fa-spin fa-fw text-[red]"></i></a>
                                    <?php }  ?>
                                </p>

                            </div>
                            <div class=" hstack md:gap-[30px]">
                                <a href="<?php echo G5_BBS_URL?>/logout.php" class="text-white">LOGOUT <i class="fa-solid fa-chevron-right fs-08em"></i></a>
                            </div>
                        <?php } else {  ?>
                            <div class=" hstack md:gap-[30px]">
                                <a href="<?php echo G5_BBS_URL?>/login.php" class="text-white">LOGIN <i class="fa-solid fa-chevron-right fs-08em"></i></a>
                                <a href="<?php echo G5_BBS_URL?>/register.php" class="text-white">REGISTER <i class="fa-solid fa-chevron-right fs-08em"></i></a>
                            </div>
                        <?php }  ?>
                    </div>

                </div>
                <div class="offcanvas-bottom">
                    <?php echo wv('menu')->made()->displayMenu('common/collapse'); ?>
                </div>
            </div>

        </div>
    </div>


    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            var $menu_btn = $(".menu-btn",$skin);
            var $offcanvas = $('.offcanvas',$skin);

            $offcanvas.on('hide.bs.offcanvas',function () {
                setTimeout(function () {
                    if($menu_btn.hasClass('is-active')){
                        $menu_btn.removeClass('is-active')
                    }
                },50)
            })
        })

    </script>
</div>