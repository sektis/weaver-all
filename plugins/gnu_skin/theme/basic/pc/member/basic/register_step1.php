<?php
include_once '_common.php';
global $g5;


?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="flogin" action="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/password_reset2.php"   method="post" class="h-100"   >
                <div class="vstack h-100 ">
               <div class="wv-offcanvas-header col-auto">
                   <div class="row align-items-center">
                       <div class="col"><button type="button" class="btn"  data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-arrow-left-long" ></i></button></div>
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
                           <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] submit-btn transition hover:bg-[#0d171b]" style="border:0;background-color: #cfcfcf;border-radius: var(--wv-4)">확인</button>
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



        })

    </script>
</div>