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
        <?php echo $skin_selector?> .form-check-label:has(.wv-check-all:checked) .wv-ratio-circle {background-color: #ff5f5a;color: #fff !important;border-color: #ff5f5a !important;}
        <?php echo $skin_selector?> .agree-list i {color: #cfcfcf;display: block;width: var(--wv-15);line-height: 0}
        <?php echo $skin_selector?> .agree-list .form-check-label:has(.wv-check-all-list:checked) i {color: #0d171b !important;}
        <?php echo $skin_selector?> .agree-list a {text-decoration: underline;text-underline-offset: .2em;padding-bottom: .2em;text-decoration-color: #97989c !important;margin-left: auto}

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="flogin" action="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_step2.php" method="post" class="h-100 wv-form-check">
                <input type="hidden" name="pre_cert_no" value="<?php echo $pre_cert_no; ?>">
                <div class="vstack h-100 ">
                    <div class="wv-offcanvas-header col-auto">
                        <div class="row align-items-center">
                            <div class="col">
                                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-arrow-left-long"></i></button>
                            </div>
                            <div class="col-auto"><p class="wv-offcanvas-title">이용약관 동의</p></div>
                            <div class="col"></div>
                        </div>
                    </div>

                    <div class="wv-offcanvas-body col vstack">
                        <p class="fs-[24/32/-0.96/600/#0D171B]">
                            서비스 이용을 위해 <br>
                            이용약관 동의가 필요해요
                        </p>
                        <p class="fs-[14/20/-0.56/500/#97989C] mt-[8px]">
                            원활한 서비스 이용 및 제공을 위해 약관 내용에 동의해주세요.
                        </p>
                        <div class="w-full h-[54px] rounded-sm-[4px] border-[1px] border-solid border-[#EFEFEF] mt-[40px] wv-flex-box justify-content-start" style="padding: 0 var(--wv-14)">

                            <label class="form-check-label fs-[16/22/-0.64/600/#0D171B] hstack  " style="gap:var(--wv-12)">
                                <input class="form-check-input  d-none wv-check-all" type="checkbox" name="agree_all">
                                <div class="wv-ratio-circle w-[20px] fw-900 fs-06em" style="border: 1px solid #cfcfcf">
                                    <div class="d-flex-center"><i class="fa-solid fa-check"></i></div>
                                </div>
                                <p>약관 전체 동의하기</p>
                            </label>

                        </div>

                        <div class="vstack agree-list mt-[16px] fs-[14/20/-0.56/500/#97989C]" style="padding: 0 var(--wv-16);row-gap: var(--wv-12)">
                            <label class="form-check-label   hstack  align-items-center" style="gap:var(--wv-9)">
                                <input class="form-check-input  d-none wv-check-all-list must" name="agree" value="1" type="checkbox" required>
                                <i class="fa-solid fa-check fs-08em must"></i>
                                <p>(필수) 서비스 이용약관 </p>
                                <a href="#" data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_terms_1.php" data-wv-ajax-option="offcanvas,end,static">보기</a>
                            </label>
                            <label class="form-check-label   hstack  align-items-center" style="gap:var(--wv-9)">
                                <input class="form-check-input  d-none wv-check-all-list must" name="agree2" value="1" type="checkbox" required>
                                <i class="fa-solid fa-check fs-08em must"></i>
                                <p>(필수) 개인정보 처리방침 </p>
                                <a href="#" data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_terms_2.php" data-wv-ajax-option="offcanvas,end,static">보기</a>
                            </label>
                            <label class="form-check-label   hstack  align-items-center" style="gap:var(--wv-9)">
                                <input class="form-check-input  d-none wv-check-all-list" name="agree3" value="1" type="checkbox">
                                <i class="fa-solid fa-check fs-08em"></i>
                                <p>(선택) 위치 기반 서비스 이용 </p>
                                <a href="#" data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_terms_3.php" data-wv-ajax-option="offcanvas,end,static">보기</a>
                            </label>
                            <label class="form-check-label   hstack  align-items-center" style="gap:var(--wv-9)">
                                <input class="form-check-input  d-none wv-check-all-list" name="agree4" value="1" type="checkbox">
                                <i class="fa-solid fa-check fs-08em"></i>
                                <p>(선택) 전자금융거래 이용</p>
                                <a href="#" data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_terms_4.php" data-wv-ajax-option="offcanvas,end,static">보기</a>
                            </label>
                            <label class="form-check-label   hstack  align-items-center" style="gap:var(--wv-9)">
                                <input class="form-check-input  d-none wv-check-all-list" name="agree5" value="1" type="checkbox">
                                <i class="fa-solid fa-check fs-08em"></i>
                                <p>(선택) 이벤트 참여 및 혜택 제공을 위한 <br>개인정보 수집 및 이용 동의 </p>
                                <a href="#" data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_terms_5.php" data-wv-ajax-option="offcanvas,end,static">보기</a>
                            </label>
                            <label class="form-check-label   hstack  align-items-center" style="gap:var(--wv-9)">
                                <input class="form-check-input  d-none wv-check-all-list " name="agree6" value="1" type="checkbox">
                                <i class="fa-solid fa-check fs-08em"></i>
                                <p>(선택) 마케팅 정보 수신</p>
                                <a href="#" data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_terms_6.php" data-wv-ajax-option="offcanvas,end,static">보기</a>
                            </label>
                        </div>
                    </div>

                    <div class="mt-auto pb-[50px]">
                        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition  " style="border:0;border-radius: var(--wv-4)">다음</button>
                    </div>
                </div>


            </form>
        </div>
    </div>

    <script>

        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");

            $("[type=checkbox]",$skin).on('change',)

            $("form", $skin).ajaxForm({
                beforeSubmit: function (formData, jqForm, options) {
                    if($("[type=checkbox].must:not(:checked)").length){
                        alert('필수 약관에 동의해주세요.')
                        return false;
                    }
                    return true;
                },
                success: function (data) {
                    $skin.replaceWith(data)
                }

            })

        })

    </script>
</div>