<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{font-size: var(--wv-16);font-weight: 600}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  w-full    " style="padding: var(--wv-16) 0; background-color: #fff">

        <div class="container vstack" style="row-gap: var(--wv-20)">
            <p class="text1">매장 이름</p>
            <div>
                <p class="fs-[16/22/-0.64/600/#0D171B]"><?php echo $row['name']; ?></p>
                <div class="hstack mt-[6px]" style="gap:var(--wv-2)">
                    <img src="<?php echo $this->manager->plugin_url ?>/img/vec1.png" class="w-[14px]" alt="">
                    <p class="fs-[12/17/-0.48/500/#97989C]">매장 이름 변경을 원하실 경우, 고객센터로 문의바랍니다</p>
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