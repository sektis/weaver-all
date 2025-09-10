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
        <?php echo $skin_selector?> .submit-btn.active{background-color:#000!important;}
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
                            <div class="col"></div>
                            <div class="col-auto"><p class="wv-offcanvas-title">지역 검색</p></div>

                            <div class="col text-end">
                                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark fs-12em"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="wv-offcanvas-body col vstack">

                        <form action="">
                            <div class="position-relative fs-[14//-0.56/500/#97989C]">
                                <input type="text" name="q" class="form-control h-[43px] rounded-[4px] border-[1px] border-solid border-[#EFEFEF] outline-none p-[12px] bg-[#f9f9f9]" placeholder="지역을 검색해보세요 (강남구, 논현동 등)">
                                <button class="btn p-0 outline-none position-absolute top-50 translate-middle-y end-[12px]"><img src="<?php echo WV_URL; ?>/img/search.png" class="w-[20px]" alt=""></button>
                            </div>
                        </form>



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