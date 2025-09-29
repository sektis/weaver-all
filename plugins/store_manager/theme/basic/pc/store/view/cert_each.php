<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style=" ">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full   " style=" ">

        <div >
            <div class="row" style="--bs-gutter-x: var(--wv-12)">
                <div class="col-auto">
                    <div class="w-[50px] h-[50px] overflow-hidden" style="border-radius: var(--wv-4);">
                        <img src="<?php echo $row['main_image']; ?>" class="object-fit-cover" alt="">
                    </div>
                </div>
                <div class="col align-self-center">
                    <div class="vstack  h-100" style=" ">
                        <div class="hstack align-items-center" style="gap:var(--wv-4)">
                            <div>
                                <div class="hstack">
                                    <p class="fs-[14//-0.56/700/#0D171B]"><?php echo $row['name']; ?></p>
                                </div>

                                <div class="hstack align-items-center" style="gap:var(--wv-4)">
                                    <p class="fs-[12/17/-0.48/500/#0D171B]"><?php echo $row['category_item']['name']; ?></p>
                                    <span class="fs-[12/17/-0.48/500/#989898]">•</span>
                                    <p class="fs-[12/17/-0.48/500/#989898]"><?php echo $this->store->location->region_2depth_name; ?> <?php echo $this->store->location->region_3depth_name; ?></p>
                                </div>
                            </div>

                            <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"view","made":"sub01_01","part":"store","field":"detail","wr_id":"<?php echo $row['wr_id']; ?>","contractitem_wr_id":"<?php echo $contractitem_wr_id?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true" class="wv-flex-box ms-auto" style="padding: var(--wv-6) var(--wv-10);background-color: #f9f9f9">인증하기</a>
                        </div>

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