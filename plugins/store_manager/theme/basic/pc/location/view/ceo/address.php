<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_store_wr_id;
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
            <div class="hstack justify-content-between">
                <p class="text1">매장 주소</p>
                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                   data-wv-ajax-data='{
                                               "made":"sub01_01",
                                               "part":"location",
                                               "action":"render_part_ceo_form",
                                               "fields":"ceo/address",
                                               "wr_id":"<?php echo $current_store_wr_id; ?>"
                                               }'
                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"  class="fs-[14/100%/-0.56/600/#97989C]"> <img src="<?php echo $this->manager->plugin_url; ?>/img/vec2.png" class="w-[14px]" alt=""> <span>변경</span></a>
            </div>
            <div>
                <div>
                    <p class="fs-[16/22/-0.64/500/#97989C]">기본주소</p>
                    <p class="fs-[16/22/-0.64/500/#0D171B] mt-[12px]"><?php echo $row['address_name_full']; ?></p>
                    <div class="mt-[6px] hstack align-items-center" style="gap:var(--wv-2)">

                        <img src="<?php echo $this->manager->plugin_url; ?>/img/vec1.png" class="w-[14px]" alt="">
                        <p class="fs-[12/17/-0.48/500/#97989C]">매장 기본 주소 변경을 원하실 경우, 고객센터로 문의바랍니다</p>

                    </div>
                </div>
                <div class="mt-[20px]" style="height: 1px;width: 100%;background-color: #efefef"></div>
                <div class="mt-[20px]">
                    <p class="fs-[16/22/-0.64/500/#97989C]">상세주소</p>
                    <p class="fs-[16/22/-0.64/500/#0D171B] mt-[12px]"><?php echo $row['detail_address_name']?$row['detail_address_name']:'미등록'; ?></p>

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