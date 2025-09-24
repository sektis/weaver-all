<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?>
        {
        }

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
                <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">비밀번호 변경</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-offcanvas-body col" style="padding-top: var(--wv-30)">

                        <p class="fs-[24/32/-0.96/600/#0D171B]">비밀번호 변경하기</p>
                        <p class="fs-[14/20/-0.56/500/#97989C] mt-[8px]">새로운 비밀번호를 입력해주세요</p>

                        <div class="vstack  mt-[36px] fs-[16/22/-0.64/600/#CFCFCF]" style="row-gap:var(--wv-30)">

                            <div class="position-relative">
                                <label for="mb_id" class="fs-[12/17/-0.48/600/#0D171B]">아이디</label>
                                <div class="mt-[12px]">
                                    <input type="password" name="mb_id" id="mb_id" value="" class="form-control  bg-[#fff] h-[22px] px-0  outline-none wv-password-toggle" required placeholder="아이디를 입력해주세요" autocomplete="new-password">
                                </div>
                                <div class="mt-[6px]" style="height: 2px;background-color: #efefef"></div>
                            </div>

                            <div class="position-relative">
                                <label for="mb_password" class="fs-[12/17/-0.48/600/#0D171B]">현재 비밀번호</label>
                                <div class="mt-[12px]">
                                    <input type="password" name="mb_password" id="mb_password" value="" class="form-control  bg-[#fff] h-[22px] px-0  outline-none wv-password-toggle" required placeholder="현재 비밀번호를 입력해주세요" autocomplete="new-password">
                                </div>
                                <div class="mt-[6px]" style="height: 2px;background-color: #efefef"></div>
                            </div>

                            <div class="position-relative">
                                <label for="mb_password_new" class="fs-[12/17/-0.48/600/#0D171B]">새 비밀번호</label>
                                <div class="mt-[12px]">
                                    <input type="password" name="mb_password_new" id="mb_password_new" value="" class="form-control  bg-[#fff] h-[22px] px-0  outline-none wv-password-toggle" required placeholder="영문/숫자 조합 6~16자리" autocomplete="new-password">
                                </div>
                                <div class="mt-[6px]" style="height: 2px;background-color: #efefef"></div>
                            </div>

                            <div class="position-relative">
                                <label for="mb_password_re" class="fs-[12/17/-0.48/600/#0D171B]">새 비밀번호 확인</label>
                                <div class="mt-[12px]">
                                    <input type="password" name="mb_password_re" id="mb_password_re" value="" class="form-control  bg-[#fff] h-[22px] px-0  outline-none wv-password-toggle" required placeholder="영문/숫자 조합 6~16자리" autocomplete="new-password">
                                </div>
                                <div class="mt-[6px]" style="height: 2px;background-color: #efefef"></div>
                            </div>



                        </div>
                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">완료</button>
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
                // // reload_ajax:true,
                success: function (data) {
                   alert('변경완료');
                    var $offcanvas = $skin.closest('.wv-offcanvas');
                    $offcanvas.offcanvas('hide');
                }
            })
        })
    </script>
</div>