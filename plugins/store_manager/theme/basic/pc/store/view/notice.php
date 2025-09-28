<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_store_wr_id;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {--wv-line-clamp-length: 1}
        <?php echo $skin_selector?> .all-view{transform-origin: center;transition: all .4s}
        <?php echo $skin_selector?> .all-view.active{transform: rotate(-180deg)}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  w-full    " style="padding: var(--wv-10); background-color: #f9f9f9">

        <div class="  hstack align-items-start" >
            <div class="hstack align-items-start" style="gap:var(--wv-4)">
                <img src="<?php echo $this->manager->plugin_url; ?>/img/notice.png" class="w-[16px]" alt="">
                <p class="fs-[12/17/-0.48/500/#0D171B] wv-line-clamp" style=""><?php echo $row['notice']; ?></p>
            </div>
            <a href="#" class="all-view  ms-[18px]" ><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_down_gray.png" class="w-[16px]" alt=""></a>
        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
            $(".all-view",$skin).click(function () {
                var $this = $(this);
                if($this.hasClass('active')){
                    $this.removeClass('active');
                    $skin[0].style.setProperty("--wv-line-clamp-length", "1");
                }else{
                    $this.addClass('active');
                    $skin[0].style.setProperty("--wv-line-clamp-length", "999");
                }
            })
        });
    </script>
</div>