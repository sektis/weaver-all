<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .divider-line{height: 2px;background-color: #efefef}
        <?php echo $skin_selector?> .divider-line:last-child{display: none}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="">
        <div class="container">

            <div class="vstack" style="row-gap: var(--wv-12)">
                <div class="border rounded-[4px] py-[12px] px-[16px] vstack  " style="row-gap: var(--wv-16)">
                    <div class="hstack gap-[8px] justify-content-between">
                        <p class="fs-[16/22/-0.64/600/#0D171B]">매장정보</p>
                        <a href="<?php echo wv_page_url('0101'); ?>"><img src="<?php echo $wv_skin_url; ?>/ceo_main/arrow.png" class="w-[20px]" alt=""></a>
                    </div>
                    <div class=" vstack" style="row-gap: var(--wv-12)">
                        <div class="hstack align-items-start" style="gap:var(--wv-16)">
                            <div><img src="<?php echo $wv_skin_url; ?>/ceo_main/map.png" class="w-[38px]" alt=""></div>
                            <div>
                                <p class="fs-[11/15/-0.44/500/#97989C]">매장 주소</p>
                                <p class="fs-[13/17/-0.52/500/#0D171B] mt-[4px]"><?php echo $this->store->location->address_name_full; ?></p>
                            </div>
                        </div>
                        <div class="divider-line"></div>
                        <div class="hstack align-items-start" style="gap:var(--wv-16)">
                            <div><img src="<?php echo $wv_skin_url; ?>/ceo_main/time.png" class="w-[38px]" alt=""></div>
                            <div>
                                <p class="fs-[11/15/-0.44/500/#97989C]">영업시간</p>
                                <p class="fs-[13/19/-0.13/500/#0D171B] mt-[4px]"><?php echo $this->store->biz->open_time_summary; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border rounded-[4px] py-[12px] px-[16px] vstack  " style="row-gap: var(--wv-16)">
                    <div class="hstack gap-[8px] justify-content-between">
                        <p class="fs-[16/22/-0.64/600/#0D171B]">진행 중인 DUM 서비스</p>
                        <a href="<?php echo wv_page_url('0201'); ?>"><img src="<?php echo $wv_skin_url; ?>/ceo_main/arrow.png" class="w-[20px]" alt=""></a>
                    </div>
                    <div class="  vstack" style="row-gap: var(--wv-12)">
                        <?php foreach ($this->store->contract->list as $cont){ ?>
                            <div class="hstack align-items-start" style="gap:var(--wv-12)">
                                <div><img src="<?php echo $cont['item']['icon']['path']; ?> " class="w-[42px]" alt=""></div>
                                <div class="hstack justify-content-between col">
                                   <div>
                                       <p class="fs-[16/22/-0.64/700/#0D171B]"><?php echo $cont['item_name']; ?></p>
                                       <p class="fs-[11/15/-0.44/500/#97989C]"><?php echo $cont['item']['desc_list']; ?></p>
                                   </div>
                                    <div>
                                        <?php echo $cont['status_service_html']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="divider-line"></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="border rounded-[4px] py-[12px] px-[16px] vstack  " style="row-gap: var(--wv-16)">
                    <div class="hstack gap-[8px] justify-content-between">
                        <div>
                            <p class="fs-[16/22/-0.64/600/#0D171B]">영업 일시중지</p>
                            <p class="fs-[12/17/-0.48/500/#97989C] mt-[2px]">갑작스런 휴무도 간편하게 설정하세요</p>
                        </div>
                        <div class="form-check form-switch disabled" style="gap:var(--wv-6);pointer-events: none">
                            <label class="form-check-label" for="holiday-off-switch"> </label>
                            <input class="form-check-input" type="checkbox" role="switch" name="biz[is_holiday_off]" value="1" id="holiday-off-switch">

                        </div>
                    </div>
                    <div class="" style="row-gap: var(--wv-12)">
                        <div class="flex   h-[33px] px-[88px] py-[8px] justify-center items-center gap-[10px] rounded-[6px] fs-[12/17/-0.48/500/#97989C] bg-[#f6f6f6]">
                            상세 날짜 및 시간 설정하기
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