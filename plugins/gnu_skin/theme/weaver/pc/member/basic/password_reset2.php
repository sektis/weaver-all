<?php
include_once '_common.php';
global $g5;
$mb = sql_fetch("select * from {$g5['member_table']} where mb_id='{$mb_id}' and mb_birth='{$mb_birth}'");
if(!$mb['mb_id']){
//    alert('일치하는 회원이 없습니다.');
}


?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .submit-btn.active{background-color:#000!important;}

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="flogin" action="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/password_reset2.php"   method="post"   >
                <div class="vstack h-100 ">
               <div class="wv-offcanvas-header col-auto">
                   <div class="row align-items-center">
                       <div class="col"><button type="button" class="btn"  data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-arrow-left-long" ></i></button></div>
                       <div class="col-auto"><p class="wv-offcanvas-title">비밀번호 재설정</p></div>
                       <div class="col"></div>
                   </div>
               </div>

               <div class="wv-offcanvas-body col vstack">
                   <p class="fs-[24/32/-0.96/600/#0D171B]">
                       비밀번호 재설정하기
                   </p>
                   <p class="fs-[14//-0.56/500/#97989C] mt-[8px]">
                       새로운 비밀번호를 입력해주세요
                   </p>

                   <div class="mt-[40px] vstack" style="row-gap: var(--wv-30)">
                       <div class=""  >
                           <label for="login_id" class="fs-[12//-0.48/600/#0D171B]">새 비밀번호<strong class="sound_only"> 필수</strong></label>
                           <input type="password" name="mb_password" id="mb_password" required class="mt-[12px] form-control fs-[16//-0.64/600/#0D171B] border-0 border-bottom wv-password-toggle" style="padding: 0 0 var(--wv-6)" size="20" maxLength="20" placeholder="영문/숫자 조합 6~16자리" autocomplete="new-password">
                       </div>

                       <div class=""  >
                           <label for="login_pw" class="fs-[12//-0.48/600/#0D171B]">새 비밀번호 확인<strong class="sound_only"> 필수</strong></label>
                           <input type="password" name="mb_password2" id="mb_password2" required class="mt-[12px] form-control fs-[16//-0.64/600/#0D171B] border-0 border-bottom  wv-password-toggle" style="padding: 0 0 var(--wv-6)" size="20" maxLength="8" minlength="8" placeholder="영문/숫자 조합 6~16자리" autocomplete="new-password">
                       </div>
                       <div class="mt-auto">
                           <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] submit-btn transition hover:bg-[#0d171b]" style="border:0;background-color: #cfcfcf;border-radius: var(--wv-4)">재설정 하기</button>
                       </div>
                   </div>


               </div>
           </div>
            </form>
        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

            var $input1 = $("#mb_password", $skin);
            var $input2 = $("#mb_password2", $skin);
            var $btn = $(".submit-btn", $skin);

            $input1.on("keyup input", toggleLoginActive);
            $input2.on("keyup input", toggleLoginActive);


            function toggleLoginActive() {
                if ($input1.val().length > 0 && $input2.val().length > 0) {
                    $btn.addClass("active");
                } else {
                    $btn.removeClass("active");
                }
            }


        })

    </script>
</div>