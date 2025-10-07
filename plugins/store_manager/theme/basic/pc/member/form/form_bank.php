<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100   bg-white overflow-x-hidden" <?php echo wv_display_reload_data($reload_ajax_data);?> style="">
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {
        }

        @media (max-width: 991.98px) {
        }
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if ($is_list_item_mode) { ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id', 'form'); ?>
                <div class="vstack h-100 pt-[10px]" style="">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]"><?php echo $row['is_bank_register']?'계좌 변경':'계좌 등록'; ?></p>
                                </div>
                                <div class="col text-end">
                                    <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col">

                        <p class="fs-[20/28/-0.8/600/#0D171B]">
                            어떤 계좌를 <br>
                            등록하시나요?
                        </p>
                        <p class="fs-[14/20/-0.56/500/#97989C] mt-[6px]">본인 명의 계좌만 등록 가능해요.</p>

                        <div class="vstack mt-[40px]" style="row-gap: var(--wv-30)">
                            <div class="">
                                <p class="fs-[12/17/-0.48/600/#0D171B]">예금주명</p>
                                <div class="mt-[9px] hstack justify-content-between" style="padding: var(--wv-6) 0;border-bottom: 1px solid #efefef">
                                    <p class="fs-[16/22/-0.64/700/#0D171B]"><?php echo $row['mb_mb_name']; ?></p>
                                    <div class="fs-[12/17/-0.48/500/#0D171B] mt-[6px]">
                                        <span>인증한 날짜</span>
                                        <span class="ff-Roboto"><?php echo $this->store->member->cert_date; ?></span>
                                    </div>
                                </div>
                                <p class="fs-[12/17/-0.48/500/#CFCFCF] mt-[6px]">덤이요에서 확인된 이름이에요</p>
                            </div>
                            <?php echo $this->store->member->render_part('bank_datetime','form'); ?>
                            <div class="">
                                <p class="fs-[12/17/-0.48/600/#0D171B]">계좌번호 입력</p>
                                <div class="mt-[9px] " style="padding: var(--wv-6) 0;">
                                    <?php echo $this->store->member->render_part('bank_account_number','form'); ?>
                                </div>
                            </div>

                            <div class="">
                                <p class="fs-[12/17/-0.48/600/#0D171B]">은행 선택</p>
                                <div class="mt-[9px] " style="padding: var(--wv-6) 0;">
                                    <?php echo $this->store->member->render_part('bank_number','form'); ?>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">등록하기</button>
                    </div>
                    <div class="" style="background-color: #f9f9f9"><div class="m" style="height: 2px;background-color: #efefef"></div></div>

                    <div class="wv-mx-fit">
                        <?php echo wv_widget('content/copyright'); ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).ajaxForm({
                reload: false,
                // reload_ajax:true, 자기자신 리로드
                success: function () {
                    var $offcanvas = $skin.closest('.wv-offcanvas');
                    $offcanvas.offcanvas('hide');
                }
            })
        })
    </script>
</div>