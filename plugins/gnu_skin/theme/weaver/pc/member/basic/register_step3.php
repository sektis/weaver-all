<?php
include_once '_common.php';
global $g5;
if(!$pre_cert_no or get_session('wv_cert_no')!=$pre_cert_no){
//    alert('본인 인증 후 가입가능합니다.');
}
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .login-sns{display: none}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>
    <form    method="post" class="temp-form">
        <input type="hidden" name="pre_cert_no" value="<?php echo $pre_cert_no; ?>">
        <input type="hidden" name="agree" value="<?php echo $agree; ?>">
        <input type="hidden" name="agree2" value="<?php echo $agree2; ?>">
        <input type="hidden" name="agree3" value="<?php echo $agree3; ?>">
        <input type="hidden" name="agree4" value="<?php echo $agree4; ?>">
        <input type="hidden" name="agree5" value="<?php echo $agree5; ?>">
        <input type="hidden" name="agree6" value="<?php echo $agree6; ?>">
        <input type="hidden" name="no_layout" value="1">
    </form>
    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">

            <div class="vstack h-100 ">
                <div class="wv-offcanvas-header col-auto">
                    <div class="row align-items-center">
                        <div class="col">
                            <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-arrow-left-long"></i></button>
                        </div>
                        <div class="col-auto"><p class="wv-offcanvas-title">회원가입</p></div>
                        <div class="col"></div>
                    </div>
                </div>

                <div class="wv-offcanvas-body col vstack">
                    <p class="fs-[24/32/-0.96/600/#0D171B]">
                        덤이요 첫 가입을 <br>
                        환영해요!
                    </p>
                    <div class="form-ajax-content"></div>
                </div>
            </div> 
        </div>
    </div>

    <script>

        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");
            var formArray = $(".temp-form",$skin).serializeArray();

            var formObj = {};
            $.each(formArray, function(i, field) {
                formObj[field.name] = field.value;
            });

            $.post(g5_bbs_url+'/register_form.php',formObj,function (data) {
                $(".form-ajax-content",$skin).html(data);
            },'html')

        })

    </script>
</div>