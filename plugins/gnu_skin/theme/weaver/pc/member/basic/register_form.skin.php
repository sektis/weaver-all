<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_javascript('<script src="'.G5_JS_URL.'/jquery.register_form.js"></script>', 0);
if ($config['cf_cert_use'] && ($config['cf_cert_simple'] || $config['cf_cert_ipin'] || $config['cf_cert_hp']))
    add_javascript('<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>', 0);
if($w==''){
    if(!$pre_cert_no or get_session('wv_cert_no')!=$pre_cert_no){
//        alert('본인 인증 후 가입가능합니다.'.$pre_cert_no.'//'.get_session('wv_cert_no'));
    }
    $member['mb_certify']=$pre_cert_type;
    $member['mb_hp']=get_session("wv_cert_mb_hp");
    $member['mb_name']=get_session("wv_cert_mb_name");

    set_session("ss_cert_type",    get_session("wv_cert_type"));
    set_session("ss_cert_no",      get_session("wv_md5_cert_no"));
    set_session("ss_cert_hash",    get_session("wv_hash_data"));
    set_session("ss_cert_adult",   get_session("wv_adult"));
    set_session("ss_cert_birth",   get_session("wv_birth_day"));
    set_session("ss_cert_sex",     (get_session("wv_sex_code")=="01"?"M":"F"));
    set_session('ss_cert_dupinfo', get_session("wv_mb_dupinfo"));

}

?>

<!-- 회원정보 입력/수정 시작 { -->
<style>
    #fregisterform #daum_juso_pagemb_zip{z-index: 50;position: absolute;background: #fff}
    #fregisterform .col-form-label{width: var(--wv-120)}
    #fregisterform .row{--bs-gutter-y: var(--wv-1)}
    @media (max-width: 991.98px) {
        #fregisterform .col-form-label{width: 100vw;padding-top: var(--wv-md-3, revert-layer);padding-bottom: var(--wv-md-3, revert-layer)}
    }
</style>
<div class="register   mx-auto md:w-full"  >



    <?php
    // 소셜로그인 사용시 소셜로그인 버튼
    if($w==''){
        @include_once(get_social_skin_path().'/social_register.skin.php');
    }

    ?>
    <form id="fregisterform" name="fregisterform" action="<?php echo G5_BBS_URL ?>/register_form_update.php" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="">
        <input type="hidden" name="w" value="<?php echo $w ?>">
        <input type="hidden" name="url" value="<?php echo $urlencode ?>">
        <input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
        <input type="hidden" name="cert_no" value="<?php echo $pre_cert_no;?>">
        <?php if (isset($member['mb_sex'])) {  ?><input type="hidden" name="mb_sex" value="<?php echo $member['mb_sex'] ?>"><?php }  ?>
        <?php if (isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400))) { // 닉네임수정일이 지나지 않았다면  ?>
            <input type="hidden" name="mb_nick_default" value="<?php echo get_text($member['mb_nick']) ?>">
            <input type="hidden" name="mb_nick" value="<?php echo get_text($member['mb_nick']) ?>">
        <?php }  ?>






        <div class="vstack" style="padding: var(--wv-15) 0;row-gap: var(--wv-md-30,var(--wv-15))">

            <div class="hstack" style="padding: var(--wv-20) 0;gap:var(--wv-18);border-bottom: var(--wv-3) solid #111">
                <span class="fs-[30/33.15//700/]">사이트 이용정보 입력</span>
            </div>

            <div class="form-floating">
                <input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>" id="reg_mb_id" <?php echo $required ?> <?php echo $readonly ?> class="form-control <?php echo $required ?> <?php echo $readonly ?>" minlength="3" maxlength="20" placeholder="">
                <label for="reg_mb_id" class="floatingInput">아이디</label>
            </div>

            <div class="form-floating">
                <input type="password" name="mb_password" id="reg_mb_password" <?php echo $required ?> class="form-control <?php echo $required ?>" minlength="3" maxlength="20" placeholder="비밀번호" autocomplete="new-password">
                <label for="reg_mb_password" class="floatingInput">비밀번호</label>
            </div>

            <div class="form-floating">
                <input type="password" name="mb_password_re" id="reg_mb_password_re" <?php echo $required ?> class="form-control <?php echo $required ?>" minlength="3" maxlength="20" placeholder="비밀번호 확인" autocomplete="new-password">
                <label for="reg_mb_password_re" class="floatingInput">비밀번호 확인</label>
            </div>

            <div class="hstack" style="padding: var(--wv-20) 0;gap:var(--wv-18);border-bottom: var(--wv-3) solid #111">
                <span class="fs-[30/33.15//700/]">개인정보 입력</span>
            </div>

            <?php if ($config['cf_cert_use']) { ?>
                <div class="row">
                    <div class="col-auto">
                        <label for="reg_mb_password_re" class="col-form-label">본인인증</label>
                    </div>
                    <div class="col-auto ">
                        <?php
                        $desc_name = '';
                        $desc_phone = '';
                        if ($config['cf_cert_use']) {
                            $desc_name = '본인확인 시 자동입력';
                            $desc_phone = '본인확인 시 자동입력';

                            if (!$config['cf_cert_simple'] && !$config['cf_cert_hp'] && $config['cf_cert_ipin']) {
                                $desc_phone = '';
                            }

                            if ($config['cf_cert_simple']) {
                                echo '<button type="button" id="win_sa_kakao_cert" class="btn border win_sa_cert mb-1" data-type="">간편인증</button>'.PHP_EOL;
                            }
                            if ($config['cf_cert_hp'])
                                echo '<button type="button" id="win_hp_cert" class="btn border mb-1">휴대폰 본인확인</button>'.PHP_EOL;
                            if ($config['cf_cert_ipin'])
                                echo '<button type="button" id="win_ipin_cert" class="btn border mb-1">아이핀 본인확인</button>'.PHP_EOL;

                            echo '<noscript>본인확인을 위해서는 자바스크립트 사용이 가능해야합니다.</noscript>'.PHP_EOL;
                        }
                        ?>
                    </div>
                    <?php
                    if ($config['cf_cert_use'] && $member['mb_certify']) {
                        switch  ($member['mb_certify']) {
                            case "simple":
                                $mb_cert = "간편인증";
                                break;
                            case "ipin":
                                $mb_cert = "아이핀";
                                break;
                            case "hp":
                                $mb_cert = "휴대폰";
                                break;
                        }
                        ?>
                        <div class="alert alert-secondary mt-3 mb-0 text-center">
                            <strong><?php echo $mb_cert; ?> 본인확인</strong><?php if ($member['mb_adult']) { ?> 및 <strong>성인인증</strong><?php } ?> 완료
                        </div>
                    <?php } ?>
                </div>
            <?php }?>

            <div class="form-floating  ">
                <input type="text" id="reg_mb_name" name="mb_name" value="<?php echo get_text($member['mb_name']) ?>" <?php echo $required ?> <?php echo $name_readonly; ?> class="form-control  <?php echo $required ?> <?php echo $name_readonly; ?>" size="10" placeholder="이름" >
                <label for="reg_mb_name" class="floatingInput">이름</label>
            </div>



            <?php if ($req_nick) {  ?>
                <div class="form-floating">
                    <input type="hidden" name="mb_nick_default" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>">
                    <input type="text" name="mb_nick" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>" id="reg_mb_nick" required class="form-control  nospace required" size="10" maxlength="20" placeholder="닉네임">
                    <label for="reg_mb_nick" class="floatingInput">닉네임</label>
                    <button type="button" class="btn position-absolute end-0 bottom-0  " data-bs-container="body"   data-bs-toggle="popover" tabindex="-1" data-bs-placement="left" data-bs-trigger="hover focus" data-bs-html="true" data-bs-content="공백없이 한글,영문,숫자만 입력 가능 (한글2자, 영문4자 이상) <br> 닉네임을 바꾸시면 앞으로 <?php echo (int)$config['cf_nick_modify'] ?>일 이내에는 변경 할 수 없습니다.">
                        <i class="fa-regular fa-circle-question"></i>
                    </button>
                </div>
            <?php }?>

            <div class="form-floating">
                <input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
                <input type="text" name="mb_email" value="<?php echo isset($member['mb_email'])?$member['mb_email']:''; ?>" id="reg_mb_email" required class="form-control email required" size="70" maxlength="100" placeholder="E-mail" >
                <label for="reg_mb_email" class="floatingInput">이메일</label>
                <?php if ($config['cf_use_email_certify']) {
                    $email_tooltip = 'E-mail 로 발송된 내용을 확인한 후 인증하셔야 회원가입이 완료됩니다.';
                    if ($w=='u') { $email_tooltip = "E-mail 주소를 변경하시면 다시 인증하셔야 합니다."; }
                    ?>


                    <button type="button" class="btn position-absolute end-0 bottom-0  " data-bs-container="body"   data-bs-toggle="popover" tabindex="-1" data-bs-placement="left" data-bs-trigger="hover focus" data-bs-html="true" data-bs-content="<?php echo $email_tooltip?>">
                        <i class="fa-regular fa-circle-question"></i>
                    </button>
                <?php }  ?>
            </div>

            <?php if ($config['cf_use_homepage']) {  ?>
                <div class="form-floating">
                    <input type="text" name="mb_homepage" value="<?php echo get_text($member['mb_homepage']) ?>" id="reg_mb_homepage" <?php echo $config['cf_req_homepage']?"required":""; ?> class="form-control <?php echo $config['cf_req_homepage']?"required":""; ?>" size="70" maxlength="255" placeholder="홈페이지">
                    <label for="mb_homepage" class="floatingInput">홈페이지</label>
                </div>
            <?php }  ?>

            <?php if ($config['cf_use_tel']) {  ?>
                <div class="form-floating">
                    <input type="text" name="mb_tel" value="<?php echo get_text($member['mb_tel']) ?>" id="reg_mb_tel" <?php echo $config['cf_req_tel']?"required":""; ?> class="form-control <?php echo $config['cf_req_tel']?"required":""; ?>" maxlength="20" placeholder="전화번호">
                    <label for="mb_tel" class="floatingInput">전화번호</label>
                </div>
            <?php }  ?>

            <?php if ($config['cf_use_hp'] || ($config["cf_cert_use"] && ($config['cf_cert_hp'] || $config['cf_cert_simple']))) {  ?>

                <div class="form-floating">
                    <?php if ($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple'])) { ?>
                        <input type="hidden" name="old_mb_hp" value="<?php echo get_text($member['mb_hp']) ?>">
                    <?php } ?>
                    <input type="text" name="mb_hp" value="<?php echo get_text($member['mb_hp']) ?>" id="reg_mb_hp" <?php echo $hp_required; ?> <?php echo $hp_readonly; ?> class="form-control <?php echo $hp_required; ?> <?php echo $hp_readonly; ?>" maxlength="20" placeholder="휴대폰번호">
                    <label for="reg_mb_hp" class="floatingInput">휴대폰번호</label>
                </div>
            <?php }  ?>

            <?php if ($config['cf_use_addr']) { ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating  ">
                                <input type="text" name="mb_zip" value="<?php echo $member['mb_zip1'].$member['mb_zip2']; ?>" id="reg_mb_zip" <?php echo $config['cf_req_addr']?"required":""; ?> class="form-control    <?php echo $config['cf_req_addr']?"required":""; ?>" maxlength="6" placeholder="우편번호">
                                <label for="reg_mb_zip" class="floatingInput">우편번호</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn border mb-3 h-100 w-100" onclick="win_zip('fregisterform', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소 검색</button>
                        </div>
                    </div>

                    <div class="form-floating  ">
                        <input type="text" name="mb_addr1" value="<?php echo get_text($member['mb_addr1']) ?>" id="reg_mb_addr1" <?php echo $config['cf_req_addr']?"required":""; ?> class="form-control <?php echo $config['cf_req_addr']?"required":""; ?>" placeholder="기본주소">
                        <label for="reg_mb_addr1" class="floatingInput">기본주소</label>
                    </div>

                    <div class="form-floating  ">
                        <input type="text" name="mb_addr2" value="<?php echo get_text($member['mb_addr2']) ?>" id="reg_mb_addr2" class="form-control" placeholder="상세주소">
                        <label for="reg_mb_addr2" class="floatingInput">상세주소</label>
                    </div>

                    <div class="form-floating  ">
                        <input type="text" name="mb_addr3" value="<?php echo get_text($member['mb_addr3']) ?>" id="reg_mb_addr3" class="form-control" readonly="readonly"  placeholder="참고항목">
                        <label for="reg_mb_addr3" class="floatingInput">참고항목</label>
                    </div>

                    <input type="hidden" name="mb_addr_jibeon" value="<?php echo get_text($member['mb_addr_jibeon']); ?>">

            <?php }  ?>


                <?php if ($config['cf_use_signature']) {  ?>
                    <div class="form-floating">
                        <textarea name="mb_signature" id="reg_mb_signature" <?php echo $config['cf_req_signature']?"required":""; ?> class="form-control <?php echo $config['cf_req_signature']?"required":""; ?>"   placeholder="서명"><?php echo $member['mb_signature'] ?></textarea>
                        <label for="reg_mb_signature" class="floatingInput">서명</label>
                    </div>
                <?php }  ?>

                <?php if ($config['cf_use_profile']) {  ?>
                    <div class="form-floating">
                        <textarea name="mb_profile" id="reg_mb_profile" <?php echo $config['cf_req_profile']?"required":""; ?> class="form-control <?php echo $config['cf_req_profile']?"required":""; ?>" placeholder="자기소개"><?php echo $member['mb_profile'] ?></textarea>
                        <label for="reg_mb_profile" class="floatingInput">자기소개</label>
                    </div>
                <?php }  ?>

                <?php if ($w == "" && $config['cf_use_recommend']) {  ?>
                    <div class="form-floating  ">
                        <input type="text" name="mb_recommend" id="reg_mb_recommend" class="form-control" placeholder="추천인아이디">
                        <label for="mb_recommend" class="floatingInput">추천인아이디</label>
                    </div>

                <?php }  ?>

                <?php if ($config['cf_use_member_icon'] && $member['mb_level'] >= $config['cf_icon_level']) {  ?>

                    <div class="row">
                        <div class="col-auto">
                            <label for="reg_mb_icon" class="col-form-label">회원아이콘</label>
                        </div>
                        <div class="col-auto">
                            <div class="position-relative">
                                <input type="file" name="mb_icon" id="" class="form-control">
                                <button type="button" class="btn position-absolute start-100 top-50 translate-middle-y  " data-bs-container="body"   data-bs-toggle="popover"  tabindex="-1"data-bs-placement="top" data-bs-trigger="hover focus" data-bs-html="true" data-bs-content="이미지 크기는 가로 <?php echo $config['cf_member_icon_width'] ?>픽셀, 세로 <?php echo $config['cf_member_icon_height'] ?>픽셀 이하로 해주세요.<br>
gif, jpg, png파일만 가능하며 용량 <?php echo number_format($config['cf_member_icon_size']) ?>바이트 이하만 등록됩니다.">
                                    <i class="fa-regular fa-circle-question"></i>
                                </button>
                            </div>

                            <?php if ($w == 'u' && file_exists($mb_icon_path)) {  ?>
                               <div class="mt-[10px]">
                                   <img src="<?php echo $mb_icon_url ?>" alt="회원아이콘">
                                   <div class="form-check form-check-inline">
                                       <input class="form-check-input" type="checkbox" name="del_mb_icon" id="del_mb_icon" value="1">
                                       <label class="form-check-label" for="del_mb_icon">삭제</label>
                                   </div>
                               </div>
                            <?php }  ?>

                        </div>
                    </div>


                <?php }  ?>

                <?php if ($member['mb_level'] >= $config['cf_icon_level'] && $config['cf_member_img_size'] && $config['cf_member_img_width'] && $config['cf_member_img_height']) {  ?>
                    <div class="row  ">
                        <div class="col-auto">
                            <label for="reg_mb_img" class="col-form-label">회원이미지</label>
                        </div>
                        <div class="col-auto">
                            <div class="position-relative">
                                <input type="file" name="mb_img" id="" class="form-control">
                                <button type="button" class="btn position-absolute start-100 top-50 translate-middle-y  " data-bs-container="body"   data-bs-toggle="popover" tabindex="-1" data-bs-placement="top" data-bs-trigger="hover focus" data-bs-html="true" data-bs-content="이미지 크기는 가로 <?php echo $config['cf_member_img_width'] ?>픽셀, 세로 <?php echo $config['cf_member_img_height'] ?>픽셀 이하로 해주세요.<br>
	                    gif, jpg, png파일만 가능하며 용량 <?php echo number_format($config['cf_member_img_size']) ?>바이트 이하만 등록됩니다.">
                                    <i class="fa-regular fa-circle-question"></i>
                                </button>
                            </div>

                            <?php if ($w == 'u' && file_exists($mb_img_path)) {  ?>
                                <div class="mt-[10px]">
                                    <img src="<?php echo $mb_img_url ?>" alt="회원이미지">
                                    <input type="checkbox" name="del_mb_img" value="1" id="del_mb_img">
                                    <label for="del_mb_img" class="inline">삭제</label>
                                </div>
                            <?php }  ?>
                        </div>
                    </div>
                <?php } ?>

            <div class="hstack" style="padding: var(--wv-20) 0;gap:var(--wv-18);border-bottom: var(--wv-3) solid #111">
                <span class="fs-[30/33.15//700/]">기타 개인설정</span>
            </div>

            <div class="row align-items-center">
                <div class="col-auto">
                    <label for="reg_mb_mailling" class="col-form-label">메일링서비스</label>
                </div>
                <div class="col-auto">
                    <div class="form-check  ">
                        <input class="form-check-input" type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" <?php echo ($w=='' || $member['mb_mailling'])?'checked':''; ?>>
                        <label class="form-check-label" for="reg_mb_mailling">정보 메일을 받겠습니다.</label>
                    </div>
                </div>
            </div>

            <?php if ($config['cf_use_hp']) { ?>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <label for="reg_mb_sms"  class="col-form-label">SMS 수신여부</label>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" <?php echo ($w=='' || $member['mb_sms'])?'checked':''; ?>>
                            <label class="form-check-label" for="reg_mb_sms">휴대폰 문자메세지를 받겠습니다.</label>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (isset($member['mb_open_date']) && $member['mb_open_date'] <= date("Y-m-d", G5_SERVER_TIME - ($config['cf_open_modify'] * 86400)) || empty($member['mb_open_date'])) { // 정보공개 수정일이 지났다면 수정가능 ?>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <label for="reg_mb_open" class="col-form-label">정보공개</label>
                    </div>
                    <div class="col-auto position-relative">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mb_open" value="1" id="reg_mb_open" <?php echo ($w=='' || $member['mb_open'])?'checked':''; ?>>
                            <label class="form-check-label" for="reg_mb_open">다른분들이 나의 정보를 볼 수 있도록 합니다.</label>
                        </div>
                        <button type="button" class="btn position-absolute start-100 top-50 translate-middle-y  " data-bs-container="body"   data-bs-toggle="popover" tabindex="-1" data-bs-placement="top" data-bs-trigger="hover focus" data-bs-html="true" data-bs-content=" 정보공개를 바꾸시면 앞으로 <?php echo (int)$config['cf_open_modify'] ?>일 이내에는 변경이 안됩니다.">
                            <i class="fa-regular fa-circle-question"></i>
                        </button>

                    </div>

                </div>


            <?php } else { ?>
                <input type="hidden" name="mb_open" value="<?php echo $member['mb_open'] ?>">
                <div class="row">
                    <div class="col-auto">
                        <label for="" class="col-form-label" >정보공개</label>
                    </div>
                    <div class="col ">
                        <div class="alert alert-danger" role="alert">
                            정보공개는 수정후 <?php echo (int)$config['cf_open_modify'] ?>일 이내, <?php echo date("Y년 m월 j일", isset($member['mb_open_date']) ? strtotime("{$member['mb_open_date']} 00:00:00")+$config['cf_open_modify']*86400:G5_SERVER_TIME+$config['cf_open_modify']*86400); ?> 까지는 변경이 안됩니다.<br>
                            이렇게 하는 이유는 잦은 정보공개 수정으로 인하여 쪽지를 보낸 후 받지 않는 경우를 막기 위해서 입니다.
                        </div>
                    </div>
                </div>
            <?php }  ?>


            <?php
            //회원정보 수정인 경우 소셜 계정 출력
            if( $w == 'u' && function_exists('social_member_provider_manage') ){?>

                <div class="row align-items-center">
                    <div class="col-auto">
                        <label for="" class="col-form-label" >SNS 로그인 관리</label>
                    </div>
                    <div class="col ">
                        <?php social_member_provider_manage();?>
                    </div>
                </div>

            <?php }?>



            <div class="row">
                <div class="col-lg-auto">
                    <label for="" class="col-form-label"  >자동등록방지</label>
                </div>
                <div class="col ">
                    <div class="captcha-wrap"><?php echo captcha_html(); ?></div>
                </div>
            </div>

            <?php if($w==''){ ?>
                <div class="row" >
                    <div class="col-lg-auto">
                        <label for="reg_mb_mailling" class="col-form-label"  >약관동의</label>
                    </div>
                    <div class="col ">
                        <div class="form-check ">
                            <input class="form-check-input" type="checkbox" name="agree" id="agree" required >
                            <label class="form-check-label" for="agree">
                                회원가입약관에 동의합니다.
                            </label>
                            <a href="<?php echo short_url_clean('/index.php?wv_page_id=etc1')?>" style="color:#766df4" target="_blank">[전문보기]</a>
                        </div>
                        <div class="form-check ">
                            <input class="form-check-input" type="checkbox" name="agree2" id="agree2" required>
                            <label class="form-check-label" for="agree2">
                                개인정보 수집 및 이용에 동의합니다. </a>
                            </label>
                            <a href="<?php echo short_url_clean('/index.php?wv_page_id=etc2')?>" style="color:#766df4" target="_blank">[전문보기]</a>
                        </div>
                    </div>
                </div>

            <?php }?>



            <div class="mt-[40px] text-center hstack" style="gap:var(--wv-15)">
                <a href="<?php echo G5_URL ?>" class="  btn border col">취소</a>
                <button type="submit" id="btn_submit" class="btn btn-primary col" accesskey="s"  ><?php echo $w==''?'회원가입':'정보수정'; ?></button>
            </div>
        </div>









    </form>
</div>
<script>
$(function() {
    $("#reg_zip_find").css("display", "inline-block");
    var pageTypeParam = "pageType=register";

	<?php if($config['cf_cert_use'] && $config['cf_cert_simple']) { ?>
	// 이니시스 간편인증
	var url = "<?php echo G5_INICERT_URL; ?>/ini_request.php";
	var type = "";
    var params = "";
    var request_url = "";

	$(".win_sa_cert").click(function() {
		if(!cert_confirm()) return false;
		type = $(this).data("type");
		params = "?directAgency=" + type + "&" + pageTypeParam;
        request_url = url + params;
        call_sa(request_url);
	});
    <?php } ?>
    <?php if($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
    // 아이핀인증
    var params = "";
    $("#win_ipin_cert").click(function() {
		if(!cert_confirm()) return false;
        params = "?" + pageTypeParam;
        var url = "<?php echo G5_OKNAME_URL; ?>/ipin1.php"+params;
        certify_win_open('kcb-ipin', url);
        return;
    });

    <?php } ?>
    <?php if($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
    // 휴대폰인증
    var params = "";
    $("#win_hp_cert").click(function() {
		if(!cert_confirm()) return false;
        params = "?" + pageTypeParam;
        <?php
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
            default:
                echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오");';
                echo 'return false;';
                break;
        }
        ?>

        certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>"+params);
        return;
    });
    <?php } ?>
});

// submit 최종 폼체크
function fregisterform_submit(f)
{
    // 회원아이디 검사
    if (f.w.value == "") {
        var msg = reg_mb_id_check();
        if (msg) {
            alert(msg);
            f.mb_id.select();
            return false;
        }
    }

    if (f.w.value == "") {
        if (f.mb_password.value.length < 3) {
            alert("비밀번호를 3글자 이상 입력하십시오.");
            f.mb_password.focus();
            return false;
        }
    }

    if (f.mb_password.value != f.mb_password_re.value) {
        alert("비밀번호가 같지 않습니다.");
        f.mb_password_re.focus();
        return false;
    }

    if (f.mb_password.value.length > 0) {
        if (f.mb_password_re.value.length < 3) {
            alert("비밀번호를 3글자 이상 입력하십시오.");
            f.mb_password_re.focus();
            return false;
        }
    }

    // 이름 검사
    if (f.w.value=="") {
        if (f.mb_name.value.length < 1) {
            alert("이름을 입력하십시오.");
            f.mb_name.focus();
            return false;
        }

        /*
        var pattern = /([^가-힣\x20])/i;
        if (pattern.test(f.mb_name.value)) {
            alert("이름은 한글로 입력하십시오.");
            f.mb_name.select();
            return false;
        }
        */
    }

    <?php if($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) { ?>
    // 본인확인 체크
    if(f.cert_no.value=="") {
        alert("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
        return false;
    }
    <?php } ?>

    // 닉네임 검사
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick.defaultValue != f.mb_nick.value)) {
        var msg = reg_mb_nick_check();
        if (msg) {
            alert(msg);
            f.reg_mb_nick.select();
            return false;
        }
    }

    // E-mail 검사
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
        var msg = reg_mb_email_check();
        if (msg) {
            alert(msg);
            f.reg_mb_email.select();
            return false;
        }
    }

    <?php if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {  ?>
    // 휴대폰번호 체크
    var msg = reg_mb_hp_check();
    if (msg) {
        alert(msg);
        f.reg_mb_hp.select();
        return false;
    }
    <?php } ?>

    if (typeof f.mb_icon != "undefined") {
        if (f.mb_icon.value) {
            if (!f.mb_icon.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
                alert("회원아이콘이 이미지 파일이 아닙니다.");
                f.mb_icon.focus();
                return false;
            }
        }
    }

    if (typeof f.mb_img != "undefined") {
        if (f.mb_img.value) {
            if (!f.mb_img.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
                alert("회원이미지가 이미지 파일이 아닙니다.");
                f.mb_img.focus();
                return false;
            }
        }
    }

    if (typeof(f.mb_recommend) != "undefined" && f.mb_recommend.value) {
        if (f.mb_id.value == f.mb_recommend.value) {
            alert("본인을 추천할 수 없습니다.");
            f.mb_recommend.focus();
            return false;
        }

        var msg = reg_mb_recommend_check();
        if (msg) {
            alert(msg);
            f.mb_recommend.select();
            return false;
        }
    }

    <?php echo chk_captcha_js();  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}

jQuery(function($){
	//tooltip
    $(document).on("click", ".tooltip_icon", function(e){
        $(this).next(".tooltip").fadeIn(400).css("display","inline-block");
    }).on("mouseout", ".tooltip_icon", function(e){
        $(this).next(".tooltip").fadeOut();
    });
});

</script>

<!-- } 회원정보 입력/수정 끝 -->