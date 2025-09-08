<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$store = $this->manager->get($row['wr_id']);

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="width: max-content">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-[150px]" style=" ;border-radius: var(--wv-4);;background-color: #fff;overflow:hidden;">


        <div class="h-[150px] overflow-hidden" style="border-radius: var(--wv-4);">
            <img src="<?php echo $store->store->main_image ?>" class="object-fit-cover" alt="">
        </div>
        <div class="mt-[12px]">
            <p class="fs-[14//-0.56/700/#0D171B] ff-Pretendard"><?php echo $store->store->name; ?></p>
            <div class="hstack" style="gap:var(--wv-4)">
                <p class="fs-[12//-0.48/500/#0D171B]"><?php echo $store->store->category_text; ?></p>
                <p class="fs-[12//-0.48/500/#989898]">· <?php echo $store->location->region_name_full; ?></p>
            </div>
        </div>





    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>