<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="">
        <div class="container">

            <div class="border rounded-[4px] p-[14px] vstack  " style="row-gap: var(--wv-16)">
                <div class="hstack gap-[8px] justify-content-between">
                    <p class="fs-[16/22/-0.64/600/#0D171B]">매장정보</p>
                    <a href=""><img src="<?php echo $wv_skin_url; ?>/ceo_main/arrow.png" class="w-[20px]" alt=""></a>
                </div>
                <div class="mt-[20px] vstack" style="row-gap: var(--wv-12)">
                    <div class="hstack" style="gap:var(--wv-16)">
                        <div><img src="<?php echo $wv_skin_url; ?>/ceo_main/map.png" class="w-[38px]" alt=""></div>
                        <div>
                            <p class="fs-[11/15/-0.44/500/#97989C]">매장 주소</p>
                            <p class="fs-[13/17/-0.52/500/#0D171B] mt-[4px]"><?php echo $this->store->location->address_name_full; ?></p>
                        </div>
                    </div>
                    <div class="hstack" style="gap:var(--wv-16)">
                        <div><img src="<?php echo $wv_skin_url; ?>/ceo_main/time.png" class="w-[38px]" alt=""></div>
                        <div>
                            <p class="fs-[11/15/-0.44/500/#97989C]">영업시간</p>
                            <p class="fs-[13/17/-0.52/500/#0D171B] mt-[4px]"><?php echo $this->biz->open_time->address_name_full; ?></p>
                        </div>
                    </div>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">생년월일</p>
                    <p class="text3">2001 / 01 / 01</p>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">휴대폰번호</p>
                    <p class="text3">010-****-1463</p>
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