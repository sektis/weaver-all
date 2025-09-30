<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style=" ">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full   cursor-pointer" style=" " data-q-value="<?php echo $row['name']; ?>">

        <div >
            <div class="row align-items-center" style="--bs-gutter-x: var(--wv-12)">
                <div class="col-auto">
                    <img src="<?php echo $this->manager->plugin_url; ?>/img/search_gray.png" class="w-[20px]" alt="">
                </div>
                <div class="col align-self-center">
                    <div class="hstack" style="gap:var(--wv-6)">
                        <p class="fs-[16/22/-0.64/600/#0D171B]"><?php echo $row['name']; ?></p>
                        <p class="fs-[12/17/-0.48/600/#97989C]"><?php echo $row['category_item']['name']; ?></p>
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