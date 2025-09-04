<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>
<!-- 로그인 시작 { -->

<div class="login-from-wrap d-flex-center wv-wrap h-100" style="min-height: 100vh">

        <div class="w-[350px] md:w-full mx-auto">
                <div class="container">
                    <div class="mt-[7px] text-center">
                        <svg class="w-[191px]" viewBox="0 0 191 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.94 0.839996V38.88H30.3V45.84H0.78V0.839996H8.94ZM54.2733 39.24C63.1533 39.24 68.7933 31.62 68.7933 23.34C68.7933 15.12 63.1533 7.44 54.2733 7.44C45.3933 7.44 39.7533 15.12 39.7533 23.34C39.7533 31.62 45.3933 39.24 54.2733 39.24ZM31.5933 23.34C31.5933 10.74 40.8333 0.359999 54.2733 0.359999C67.7133 0.359999 76.9533 10.74 76.9533 23.34C76.9533 36 67.7133 46.32 54.2733 46.32C40.8333 46.32 31.5933 36 31.5933 23.34ZM124.563 21.96C124.803 23.52 124.503 26.76 124.503 26.76C123.123 39.18 115.563 46.32 103.023 46.32C88.6833 46.32 80.3433 36.18 80.3433 23.34C80.3433 10.56 88.6833 0.419998 103.023 0.419998C117.243 0.419998 122.343 11.22 122.343 11.22L115.503 15C115.503 15 111.963 7.5 102.963 7.5C93.3033 7.5 88.5033 15.06 88.5033 23.34C88.5033 31.68 93.4233 39.24 102.963 39.24C109.323 39.18 115.983 36.18 116.343 28.5H104.103V21.96H124.563ZM133.071 0.839996H141.231V45.84H133.071V0.839996ZM190.529 0.839996V45.84H181.409L158.789 13.62V45.84H150.629V0.839996H159.749L182.369 33.24V0.839996H190.529Z" fill="#111111"/>
                        </svg>

                    </div>

                    <div class="mt-[52px]">
                        <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
                            <input type="hidden" name="url" value="<?php echo $login_url ?>">


                            <div class="px-[20px]">
                                <label for="login_id" class="sound_only">회원아이디<strong class="sound_only"> 필수</strong></label>
                                <input type="text" name="mb_id" id="login_id" required class="form-control border-0 border-bottom rounded-0 px-0" size="20" maxLength="20" placeholder="아이디 입력">
                            </div>

                            <div class="mt-[20px] px-[20px]">
                                <label for="login_pw" class="sound_only">비밀번호<strong class="sound_only"> 필수</strong></label>
                                <input type="password" name="mb_password" id="login_pw" required class="form-control border-0 border-bottom rounded-0 px-0" size="20" maxLength="20" placeholder="비밀번호 입력">
                            </div>

                            <button type="submit" class="w-100 mt-[30px]   fs-[18/32//700/#fff]   py-[14px]" style="border:0;background-color: #565656;border-radius: var(--wv-10)">로그인</button>

                            <div>
                                <?php @include_once(get_social_skin_path().'/social_login.skin.php'); // 소셜로그인 사용시 소셜로그인 버튼 ?>
                            </div>

                            <div class="row justify-content-between mt-[30px]">

                                <div class="col-auto">
                                    <a href="<?php echo G5_BBS_URL ?>/password_lost.php">아이디 / 비밀번호 찾기</a>
                                </div>
                                <div class="col-auto">
                                    <a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

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
    jQuery(function($){
        $("#login_auto_login").click(function(){
            if (this.checked) {
                this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
            }
        });
    });

    function flogin_submit(f)
    {
        if( $( document.body ).triggerHandler( 'login_sumit', [f, 'flogin'] ) !== false ){
            return true;
        }
        return false;
    }
</script>
<!-- } 로그인 끝 -->
