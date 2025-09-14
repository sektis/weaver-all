<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$skin_id = wv_make_skin_id();
$skin_selector = wv_make_skin_selector($skin_id);
add_javascript('<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>', 0);
set_session("wv_cert_type",    '');
set_session("wv_cert_no",      '');
set_session("wv_cert_hash",    '');
set_session("wv_cert_adult",   '');
set_session("wv_cert_birth",   '');
set_session("wv_cert_sex",     '');
set_session('wv_cert_dupinfo', '');
switch($config['cf_cert_hp']) {
    case 'kcb':
        $cert_url = G5_OKNAME_URL.'/hpcert1.php';
        $cert_type = 'kcb-hp';
        break;
    case 'kcp':
        $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php';
        $cert_type = 'kcp-hp';
        break;
    case 'lg':
        $cert_url = G5_LGXPAY_URL.'/AuthOnlyReq.php';
        $cert_type = 'lg-hp';
        break;

}
?>
<div id="<?php echo $skin_id?>" class="position-relative d-flex-center flex-nowrap"  style="" >
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full vstack justify-content-center" style="min-height: 100dvh">
        <div class="container">
            <form name="fregisterform" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post" class="fs-[16//-0.64/600/#CFCFCF] wv-form-check" >
                <input type="hidden" name="cert_type" value="">
                <input type="hidden" name="cert_no" value="">
                <input type="hidden" name="mb_hp" value="">
                <input type="hidden" name="mb_name" value="">
                <div style="padding: 0 var(--wv-8)">
                    <p class="fs-[26/34/-1.04/600/#0D171B] text-center ">
                        <span class="text-[#FF5F5A]">덤이요</span>에 오신 걸 <br>
                        환영해요
                    </p>
                    <p class="mt-[10px] fs-[14//-0.56/500/#97989C] text-center" >밥 먹으러 나갔다가, 덤이요를 알고 서비스를 받았다!</p>
                    <div class="mt-[30px] ">
                        <input type="hidden" name="url" value="<?php echo $login_url ?>">
                        <div>
                            <label for="login_id" class="fs-[12//-0.48/600/#0D171B]">아이디<strong class="sound_only"> 필수</strong></label>
                            <input type="text" name="mb_id" id="login_id" required class="form-control fs-[14/20/-0.56/600/#0d171b]  border-bottom    mt-[6px]  " style="padding: var(--wv-17) var(--wv-16)"   maxLength="20" placeholder="휴대폰 번호 입력" autocomplete="new-password">
                        </div>

                        <div class="mt-[24px]">
                            <label for="login_pw" class="fs-[12//-0.48/600/#0D171B]">비밀번호<strong class="sound_only"> 필수</strong></label>
                            <input type="password" name="mb_password" id="login_pw" required class="form-control fs-[14/20/-0.56/600/#0d171b]  border-bottom mt-[6px]  " style="padding: var(--wv-17) var(--wv-16)"   maxLength="20" placeholder="영문/숫자 조합 6~16자리" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="mt-[12px] hstack justify-content-between">
                        <div class="form-check fs-[12//-0.48/500/#97989C]">
                            <input class="form-check-input" type="checkbox" value="" id="checkChecked" checked>
                            <label class="form-check-label" for="checkChecked">
                                자동 로그인하기
                            </label>
                        </div>
                        <a href="javascript:;"  data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/password_reset.php" data-wv-ajax-option="offcanvas,end,backdrop-static"    class="fs-[12//-0.48/500/#0D171B]">비밀번호 재설정하기</a>
                    </div>
                </div>

                <div class="mt-[40px]">
                    <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition hover:bg-[#0d171b]" style="border:0;background-color: #cfcfcf;border-radius: var(--wv-4)">로그인하기</button>
                </div>

                <div class="hstack justify-content-center mt-[16px]">
                    <img src="<?php echo WV_URL; ?>/img/icon_exclamation.png" class="w-[17px]" alt="">
                    <p class="fs-[12//-0.48/500/#97989C]">아이디 / 비밀번호를 잃어버리신 경우, 고객센터로 문의바랍니다.</p>
                </div>

                <div class="hstack mt-[90px]" style="gap:var(--wv-20)">
                    <div class="col" style="height: 1px;background-color: #efefef;width: 100%"></div>
                    <p class="fs-[12//-0.48/600/#CFCFCF]">덤이요가 처음이신가요?</p>
                    <div class="col" style="height: 1px;background-color: #efefef;width: 100%"></div>
                </div>
                <div>

                    <a href="javascript:;" id="win_hp_cert" class="wv-flex-box h-[54px] fs-[14//-0.56/600/#0D171B] w-full border mt-[10px]">본인인증으로 가입하기</a>
                </div>


            </form>


            <?php // 쇼핑몰 사용시 여기부터 ?>
            <?php if (isset($default['de_level_sell']) && $default['de_level_sell'] == 1) { // 상품구입 권한 ?>

                <!-- 주문하기, 신청하기 -->
                <?php if (preg_match("/orderform.php/", $url)) { ?>
                    <section id="mb_login_notmb">
                        <h2>비회원 구매</h2>
                        <p>비회원으로 주문하시는 경우 포인트는 지급하지 않습니다.</p>

                        <div id="guest_privacy">
                            <?php echo conv_content($default['de_guest_privacy'], $config['cf_editor']); ?>
                        </div>

                        <div class="chk_box">
                            <input type="checkbox" id="agree" value="1" class="selec_chk">
                            <label for="agree"><span></span> 개인정보수집에 대한 내용을 읽었으며 이에 동의합니다.</label>
                        </div>

                        <div class="btn_confirm">
                            <a href="javascript:guest_submit(document.flogin);" class="btn_submit">비회원으로 구매하기</a>
                        </div>

                        <script>
                            function guest_submit(f)
                            {
                                if (document.getElementById('agree')) {
                                    if (!document.getElementById('agree').checked) {
                                        alert("개인정보수집에 대한 내용을 읽고 이에 동의하셔야 합니다.");
                                        return;
                                    }
                                }

                                f.url.value = "<?php echo $url; ?>";
                                f.action = "<?php echo $url; ?>";
                                f.submit();
                            }
                        </script>
                    </section>

                <?php } else if (preg_match("/orderinquiry.php$/", $url)) { ?>
                    <div id="mb_login_od_wr">
                        <h2>비회원 주문조회 </h2>

                        <fieldset id="mb_login_od">
                            <legend>비회원 주문조회</legend>

                            <form name="forderinquiry" method="post" action="<?php echo urldecode($url); ?>" autocomplete="off">

                                <label for="od_id" class="od_id sound_only">주문서번호<strong class="sound_only"> 필수</strong></label>
                                <input type="text" name="od_id" value="<?php echo get_text($od_id); ?>" id="od_id" required class="frm_input required" size="20" placeholder="주문서번호">
                                <label for="od_pwd" class="od_pwd sound_only">비밀번호 <strong>필수</strong></label>
                                <input type="password" name="od_pwd" size="20" id="od_pwd" required class="frm_input required" placeholder="비밀번호">
                                <button type="submit" class="btn_submit">확인</button>

                            </form>
                        </fieldset>

                        <section id="mb_login_odinfo">
                            <p>메일로 발송해드린 주문서의 <strong>주문번호</strong> 및 주문 시 입력하신 <strong>비밀번호</strong>를 정확히 입력해주십시오.</p>
                        </section>

                    </div>
                <?php } ?>

            <?php } ?>
            <?php // 쇼핑몰 사용시 여기까지 반드시 복사해 넣으세요 ?>
        </div>

    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");


            $("#login_auto_login",$skin).click(function(){
                if (this.checked) {
                    this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
                }
            });

            if (typeof window.certify_win_open === 'function') {
                const originalCertify = window.certify_win_open;

                window.certify_win_open = function(type, url, event) {
                    originalCertify.call(this, type, url, event);

                    // 팝업 감지 시작
                    setTimeout(() => {
                        watchCertifyPopup(type);
                    }, 300);
                };
            }

            var params = "";
            var pageTypeParam = "pageType=register";
            $("#win_hp_cert").click(function() {
                if(!cert_confirm()) return false;
                params = "?" + pageTypeParam;
                certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>"+params);
                return;
            });

            function watchCertifyPopup(type) {
                const windowName = type === 'kcb-ipin' ? 'kcbPop' : 'auth_popup';
                const popup = window.open('', windowName);

                if (popup && !popup.closed) {
                    const checkInterval = setInterval(() => {
                        if (popup.closed) {
                            clearInterval(checkInterval);
                            onCertifyComplete(type);
                        }
                    }, 1000);
                }
            }

            var ajax_data = {
               agree:1,
               agree2:1,
               no_layout:true,
               pre_cert_no:321321,
            }
            //wv_ajax_offcanvas('<?php //echo wv_path_replace_url(dirname(__FILE__)) ?>///register_step2.php',"end,backdrop-static",ajax_data)
            function onCertifyComplete(type) {
// return false;
                // 인증 완료 후 서버에서 상태 확인
                if(!$("input[name=cert_no]",$skin).val()){

                    return false;
                }


                $.post("<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/login_cert.php",{mb_name:$("input[name=mb_name]",$skin).val(),mb_hp:$("input[name=mb_hp]",$skin).val()},function () {

                    var ajax_data = {
                        agree:1,
                        agree2:1,
                        no_layout:true,
                        pre_cert_no:$("input[name=cert_no]",$skin).val(),
                    }
                    wv_ajax_offcanvas('<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_step1.php','end,backdrop-static',ajax_data)
                })


            }
        })



        function flogin_submit(f)
        {
            if( $( document.body ).triggerHandler( 'login_sumit', [f, 'flogin'] ) !== false ){
                return true;
            }
            return false;
        }
    </script>
</div>