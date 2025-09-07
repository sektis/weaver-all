<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-ps-col" style="">

        <div class="container">
            <!-- 정기휴무 목록 -->
            <div class="row row-cols-5" style="--bs-gutter-x: var(--wv-14);--bs-gutter-y: var(--wv-12)">
                <div class="col">
                    <div class="text-center vstack justify-content-center" style="row-gap: var(--wv-2)">
                        <div class="w-[54px] h-[54px]">
                            <img src="<?php echo $this->manager->plugin_url; ?>/img/category_list/0.png" class="wh-100 object-fit-contain" alt="">
                        </div>
                        <p class="fs-[11/140%//500/] text-nowrap">전체</p>
                    </div>
                </div>
                <?php
                for($i=1;$i<=11;$i++) {

                    ?>
                    <div class="col">
                        <div class="text-center vstack justify-content-center " style="row-gap: var(--wv-2)">
                            <div class="w-[54px] h-[54px]">
                                <img src="<?php echo $this->manager->plugin_url; ?>/img/category_list/<?php echo $i; ?>.png" class="wh-100 object-fit-contain" alt="">
                            </div>
                            <p class="fs-[11/140%//500/] text-nowrap"><?php echo $this->category_arr[$i]; ?></p>
                        </div>
                    </div>
                    <?php
                } ?>
                <div class="col">
                    <div class="text-center vstack justify-content-center" style="row-gap: var(--wv-2)">
                        <div class="w-[54px] h-[54px]">
                            <img src="<?php echo $this->manager->plugin_url; ?>/img/category_list/99.png" class="wh-100 object-fit-contain" alt="">
                        </div>
                        <p class="fs-[11/140%//500/] text-nowrap">기타</p>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>