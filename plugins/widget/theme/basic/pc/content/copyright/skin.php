<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="background-color: #f9f9f9;padding: var(--wv-20) 0">
        <div class="container">

            <p class="fs-[11/17/-0.44/600/#575757]">덤이요 고객센터 : 032 - 326 -1018</p>
            <div class="vstack mt-[10px] fs-[11/17/-0.44/500/#97989C]" style="row-gap: var(--wv-2)">
                <div class="hstack">
                    <p>상호명  덤이요</p>
                    <p>|</p>
                    <p>대표  장철민</p>
                    <p>|</p>
                    <p>사업자번호  558-86-03456</p>
                </div>
                <div class="hstack">
                    <p>주소  인천광역시 서구 청라커낼로 270, 2층 208호 - 2172호 </p>
                </div>
                <div class="hstack">
                    <p>(청라동, 커낼힐스빌)</p>
                    <p>|</p>
                    <p>팩스  032-326-1017</p>
                </div>
                <div class="hstack">
                    <p>이메일 dum2yo@naver.com</p>
                    <p>|</p>
                    <p>개인정보관리책임자   장철민</p>
                </div>
            </div>
            <p class="fs-[11/17/-0.44/700/#97989C] mt-[10px]">Copyright © 주식회사 덤이요 All rights reserved</p>

        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

        })

    </script>
</div>