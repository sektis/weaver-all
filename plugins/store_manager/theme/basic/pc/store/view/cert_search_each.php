<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full   " style=" ">

        <div class="container" >
            <div class="row  " style="--bs-gutter-x: var(--wv-10)">
                <div class="col-auto">
                    <div class="w-[80px] h-[80px] overflow-hidden" style="border-radius: var(--wv-4);">
                        <img src="<?php echo $row['main_image']; ?>" class="object-fit-cover" alt="">
                    </div>
                </div>
                <div class="col align-self-center">
                    <div class="hstack">
                        <p class="fs-[16/22/-0.64/700/#0D171B]"><?php echo $row['name']; ?></p>
                        <?php if($row['distance_km']){ ?>
                            (<p class="fs-[12//-0.48/500/#0D171B] mt-[2px]"><?php echo number_format($row['distance_km'],2); ?>km</p>)
                        <?php } ?>
                    </div>

                    <div class="hstack fs-[12/17/-0.48/500/#989898]" style="gap:var(--wv-4)">
                        <p class="text-[#0d171b]"><?php echo $row['category_item']['name']; ?></p>
                        <span>•</span>
                        <p ><?php echo $this->store->location->region_2depth_name; ?> <?php echo $this->store->location->region_3depth_name; ?></p>
                    </div>
                    <a href="#"  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                       data-wv-ajax-data='{ "action":"view","made":"sub01_01","part":"store","field":"detail","wr_id":"<?php echo $row['wr_id']; ?>","contractitem_wr_id":"<?php echo $contractitem_wr_id?>"}'
                       data-wv-ajax-option='offcanvas,end,backdrop,class: w-[360px],reload_ajax:true,ajax_option:{"use_redirect":"true"}' class="mt-[12px] d-block fs-[12/17/-0.48/500/#97989C]" >업체 보러가기 <img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_right_gray.png" class="w-[12px]" alt=""></a>
                </div>
            </div>


        </div>

        <div class="wv-mx-fit mt-[8px]" style="height: 2px;background-color: #efefef"></div>
        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
           data-wv-ajax-data='{ "action":"form","made":"visit_cert","part":"visitcert","field":"add","store_wr_id":"<?php echo $row['wr_id']?>"}'
           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="wv-flex-box d-flex-center h-[45px]" style="gap:var(--wv-4)">
            <img src="<?php echo $this->manager->plugin_url; ?>/img/check_green.png" class="w-[18px]" alt="">
            <p class="fs-[12/17/-0.48/600/#0D171B]">매장 방문 인증하기</p>

        </a>
    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>