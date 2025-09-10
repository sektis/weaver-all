<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>
<style>
    .sub-top-wrap{visibility: hidden;height: var(--wv-100);overflow: hidden;padding: 0!important;}
</style>
<!-- 로그인 시작 { -->
<div class="w-[400px] mx-auto">
    <div class=" ">
        <div class="mt-[7px] text-center">
            <svg class="w-[279px]" viewBox="0 0 277 89" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.236 11.462L1.16 2.354V14H0.512V1.004H1.16L6.236 10.256L11.33 1.004H11.978V14H11.33V2.354L6.236 11.462ZM18.6144 1.004H19.2624V14H18.6144V1.004ZM35.6438 1.004V14H34.9958L26.4998 2.12V14H25.8518V1.004H26.4998L34.9958 12.884V1.004H35.6438ZM48.0544 13.568C51.5284 13.568 53.6884 10.814 53.6884 7.502C53.6884 4.208 51.5284 1.436 48.0544 1.436C44.5624 1.436 42.4204 4.208 42.4204 7.502C42.4204 10.814 44.5624 13.568 48.0544 13.568ZM41.7724 7.502C41.7724 3.866 44.1304 0.86 48.0544 0.86C51.9784 0.86 54.3364 3.866 54.3364 7.502C54.3364 11.156 51.9784 14.144 48.0544 14.144C44.1304 14.144 41.7724 11.156 41.7724 7.502ZM73.0561 7.844C74.8921 7.844 76.2061 6.35 76.2061 4.694C76.2061 3.002 74.8921 1.58 73.0561 1.58H69.2401V7.844H73.0561ZM68.5921 14V1.004H73.1821C75.2341 1.004 76.8541 2.66 76.8541 4.694C76.8541 6.746 75.2341 8.42 73.1821 8.42H69.2401V14H68.5921ZM83.6326 1.004V13.424H90.7426V14H82.9846V1.004H83.6326ZM102.363 1.004L107.745 14H107.043L105.369 9.968H98.7273L97.0533 14H96.3513L101.751 1.004H102.363ZM98.9613 9.392H105.135L102.057 1.958L98.9613 9.392ZM112.727 11.498C114.077 13.226 115.553 13.532 116.471 13.532C118.541 13.532 120.017 12.416 120.017 10.598C120.017 6.944 112.853 8.546 112.853 4.154C112.853 2.156 114.851 0.932 116.651 0.932C118.217 0.932 119.387 1.67 120.269 2.858L119.783 3.218C118.973 2.138 118.019 1.508 116.651 1.508C115.067 1.508 113.501 2.552 113.501 4.154C113.501 7.952 120.665 6.35 120.665 10.598C120.665 12.758 118.829 14.108 116.471 14.108C115.481 14.108 113.753 13.82 112.241 11.876L112.727 11.498ZM135.319 1.004V1.58H130.999V14H130.351V1.58H126.085V1.004H135.319ZM141.419 1.004H142.067V14H141.419V1.004ZM154.587 14.144C150.663 14.144 148.179 11.138 148.179 7.502C148.179 3.866 150.663 0.86 154.587 0.86C156.351 0.86 157.971 1.58 159.123 2.768L158.655 3.128C157.593 2.03 156.135 1.436 154.533 1.436C150.987 1.436 148.827 4.298 148.827 7.502C148.827 10.706 150.987 13.568 154.533 13.568C156.135 13.568 157.521 12.956 158.655 11.876L159.123 12.236C157.971 13.424 156.351 14.144 154.587 14.144ZM173.125 11.498C174.475 13.226 175.951 13.532 176.869 13.532C178.939 13.532 180.415 12.416 180.415 10.598C180.415 6.944 173.251 8.546 173.251 4.154C173.251 2.156 175.249 0.932 177.049 0.932C178.615 0.932 179.785 1.67 180.667 2.858L180.181 3.218C179.371 2.138 178.417 1.508 177.049 1.508C175.465 1.508 173.899 2.552 173.899 4.154C173.899 7.952 181.063 6.35 181.063 10.598C181.063 12.758 179.227 14.108 176.869 14.108C175.879 14.108 174.151 13.82 172.639 11.876L173.125 11.498ZM192.185 14.126C189.305 14.126 187.073 11.876 187.073 9.014V1.004H187.721V9.014C187.721 11.606 189.773 13.55 192.185 13.55C194.597 13.55 196.649 11.606 196.649 9.014V1.004H197.297V9.014C197.297 11.876 195.065 14.126 192.185 14.126ZM208.804 7.97C210.622 7.97 211.774 6.404 211.774 4.748C211.774 3.02 210.208 1.58 208.606 1.58H204.574V7.97H208.804ZM203.926 14V1.004H208.372C210.856 1.004 212.422 2.804 212.422 4.784C212.422 6.584 211.126 8.528 209.02 8.528L213.016 14H212.224L208.21 8.546H204.574V14H203.926ZM230.593 7.502C230.611 7.952 230.575 8.492 230.575 8.492C230.287 12.218 227.893 14.036 224.563 14.144C220.783 14.144 218.281 11.21 218.281 7.502C218.281 3.812 220.783 0.877999 224.563 0.877999C228.379 0.877999 229.891 4.01 229.891 4.01L229.351 4.37C229.351 4.37 227.983 1.454 224.581 1.454C221.125 1.454 218.929 4.19 218.929 7.502C218.929 10.832 221.125 13.568 224.581 13.568C227.947 13.406 229.747 11.552 229.963 8.078H224.815V7.502H230.593ZM244.769 1.004V1.58H237.047V7.016H243.869V7.592H237.047V13.424H244.769V14H236.399V1.004H244.769ZM256.097 7.97C257.915 7.97 259.067 6.404 259.067 4.748C259.067 3.02 257.501 1.58 255.899 1.58H251.867V7.97H256.097ZM251.219 14V1.004H255.665C258.149 1.004 259.715 2.804 259.715 4.784C259.715 6.584 258.419 8.528 256.313 8.528L260.309 14H259.517L255.503 8.546H251.867V14H251.219ZM276.04 1.004L270.856 8.924V14H270.208V8.924L265.024 1.004H265.798L270.532 8.348L275.266 1.004H276.04Z" fill="#797979"/>
                <path d="M50.94 42.84V80.88H72.3V87.84H42.78V42.84H50.94ZM96.2733 81.24C105.153 81.24 110.793 73.62 110.793 65.34C110.793 57.12 105.153 49.44 96.2733 49.44C87.3933 49.44 81.7533 57.12 81.7533 65.34C81.7533 73.62 87.3933 81.24 96.2733 81.24ZM73.5933 65.34C73.5933 52.74 82.8333 42.36 96.2733 42.36C109.713 42.36 118.953 52.74 118.953 65.34C118.953 78 109.713 88.32 96.2733 88.32C82.8333 88.32 73.5933 78 73.5933 65.34ZM166.563 63.96C166.803 65.52 166.503 68.76 166.503 68.76C165.123 81.18 157.563 88.32 145.023 88.32C130.683 88.32 122.343 78.18 122.343 65.34C122.343 52.56 130.683 42.42 145.023 42.42C159.243 42.42 164.343 53.22 164.343 53.22L157.503 57C157.503 57 153.963 49.5 144.963 49.5C135.303 49.5 130.503 57.06 130.503 65.34C130.503 73.68 135.423 81.24 144.963 81.24C151.323 81.18 157.983 78.18 158.343 70.5H146.103V63.96H166.563ZM175.071 42.84H183.231V87.84H175.071V42.84ZM232.529 42.84V87.84H223.409L200.789 55.62V87.84H192.629V42.84H201.749L224.369 75.24V42.84H232.529Z" fill="#111111"/>
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
                        <a href="<?php echo G5_BBS_URL ?>/password_lost.php" class="fs-[12/13.26/-0.02em/400/#797979]">아이디 / 비밀번호 찾기 ></a>
                    </div>
                    <div class="col-auto">
                        <a href="<?php echo G5_BBS_URL ?>/register.php" class="fs-[12/13.26/-0.02em/400/#797979]">회원가입 ></a>
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
