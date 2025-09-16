<?php
include_once '_common.php';
global $g5;
if(!$pre_cert_no or get_session('wv_cert_no')!=$pre_cert_no){
    alert('본인 인증 후 가입가능합니다.');
}
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="flogin" action="<?php echo G5_BBS_URL ?>/register_form.php" method="post" class="h-100 wv-form-check">
                <input type="hidden" name="pre_cert_no" value="<?php echo $pre_cert_no; ?>">
                <input type="hidden" name="agree" value="<?php echo $agree; ?>">
                <input type="hidden" name="agree2" value="<?php echo $agree2; ?>">
                <input type="hidden" name="agree3" value="<?php echo $agree3; ?>">
                <input type="hidden" name="agree4" value="<?php echo $agree4; ?>">
                <input type="hidden" name="agree5" value="<?php echo $agree5; ?>">
                <input type="hidden" name="agree6" value="<?php echo $agree6; ?>">
                <input type="hidden" name="no_layout" value="1">
                <div class="vstack h-100 ">
                    <div class="wv-offcanvas-header col-auto">
                        <div class="row align-items-center">
                            <div class="col">
                                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-arrow-left-long"></i></button>
                            </div>
                            <div class="col-auto"><p class="wv-offcanvas-title">친구 추천 코드</p></div>
                            <div class="col"></div>
                        </div>
                    </div>

                    <div class="wv-offcanvas-body col vstack">
                        <p class="fs-[24/32/-0.96/600/#0D171B]">
                            친구 추천 코드가 <br>
                            있나요?
                        </p>
                        <div class="mt-[40px] "  >

                            <div class="border-bottom">
                                <label for="mb_recommend_code" class="fs-[12//-0.48/600/#0D171B]">친구 추천 코드<strong class="sound_only"> 필수</strong></label>
                                <input type="text" name="mb_recommend_code" id="mb_recommend_code" required  class="form-control fs-[14/20/-0.56/600/#0d171b]  border-0 px-0 py-[6px]    mt-[6px]  " style="padding: var(--wv-17) var(--wv-16)"  maxLength="20" placeholder="친구 추천 코드 6자리를 입력해주세요" autocomplete="new-password">
                            </div>

                        </div>


                    </div>

                    <div class="mt-auto pb-[50px]">

                        <button type="submit" class="w-full btn hstack justify-content-center skin-recommend" style="gap:var(--wv-6)">
                            <p class="fs-[14/20/-0.56/500/#97989C]">추천 코드가 없어요</p>
                            <p class="fs-[14/20/-0.56/500/#0D171B]">건너뛰기</p>
                        </button>
                        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition mt-[22px]" style="border:0;border-radius: var(--wv-4)">확인</button>
                    </div>
                </div>


            </form>
        </div>
    </div>

    <script>

        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");

            $(".skin-recommend",$skin).click(function () {
                $("#mb_recommend_code",$skin).removeAttr("required");
            })

            $("form", $skin).ajaxForm({
                success: function (data) {
                    $skin.replaceWith(data)
                }

            })

        })

    </script>
</div>