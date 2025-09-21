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
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">

                <div class="vstack h-100 ">
                    <div class="wv-offcanvas-header col-auto">
                        <div class="row align-items-center">
                            <div class="col">
                                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-arrow-left-long"></i></button>
                            </div>
                            <div class="col-auto"><p class="wv-offcanvas-title">관심 동네 설정 • 알림</p></div>
                            <div class="col"></div>
                        </div>
                    </div>

                    <div class="wv-offcanvas-body col vstack">

                        <?php

                        echo   wv_widget('location/region/main_category');
                        ?>




                    </div>


                </div>


        </div>
    </div>

    <script>

        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");


        })

    </script>
</div>