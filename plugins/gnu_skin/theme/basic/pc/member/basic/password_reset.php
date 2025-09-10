<?php
include_once '_common.php';
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .next-btn.active{background-color:#000!important;}

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100 ajax-cont">
            <form name="freset" action="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/password_reset2.php"   method="post"   >
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
                       비밀번호 재설정을 위해 <br>
                       개인정보 입력이 필요해요
                   </p>
                   <p class="fs-[14//-0.56/500/#97989C] mt-[8px]">
                       등록하신 계정의 <br>
                       휴대폰 번호와 생년월일을 입력해주세요
                   </p>

                   <div class="mt-[40px] vstack" style="row-gap: var(--wv-30)">
                       <div class=""  >
                           <label for="login_id" class="fs-[12//-0.48/600/#0D171B]">휴대폰 번호<strong class="sound_only"> 필수</strong></label>
                           <input type="text" name="mb_id" id="mb_id" required class="mt-[12px] form-control fs-[16//-0.64/600/#0D171B] border-0 border-bottom " style="padding: 0 0 var(--wv-6)" size="20" maxlength="20" placeholder="휴대폰 번호 입력" autocomplete="new-password">
                       </div>

                       <div class=""  >
                           <label for="login_pw" class="fs-[12//-0.48/600/#0D171B]">생년월일<strong class="sound_only"> 필수</strong></label>
                           <input type="text" name="mb_birth" id="mb_birth" required class="mt-[12px] form-control fs-[16//-0.64/600/#0D171B] border-0 border-bottom wv-only-number " style="padding: 0 0 var(--wv-6)"  maxlength="8"   placeholder="생년월일 8자리 입력" autocomplete="new-password">
                       </div>
                       <div class="mt-auto">
                           <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] submit-btn transition hover:bg-[#0d171b]" style="border:0;background-color: #cfcfcf;border-radius: var(--wv-4)">다음</button>
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


            var $skin = $("<?php echo $skin_selector?>");

            var $input1 = $("#mb_id", $skin);
            var $input2 = $("#mb_birth", $skin);
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

            $("form",$skin).ajaxForm({

                    success:function (data){
                        $skin.replaceWith(data)
                    }

            })


        })

    </script>
</div>