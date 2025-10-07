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

            <p class="fs-[11/17/-0.44/600/#575757]">개인정보 수정 유의사항</p>
            <div class="vstack mt-[10px] fs-[11/17/-0.44/500/#97989C]" style="row-gap: var(--wv-2)">
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>타인의 명의 및 정보로 회원가입 및 로그인이 불가합니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>이름, 휴대폰 번호 등 본인 인증 정보가 변경된 경우, 반드시 업데이트를 진행해 주세요.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>개명 등의 사유로 이름을 변경한 경우, 정확한 이름을 반영하기 위해 본인 인증을 다시 진행해야 합니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>본인 인증 정보를 업데이트하지 않으면, 일부 서비스 (가입, 로그인, 포인트 출금 등) 에 제한이 있을 수 있습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>본인 인증 과정에서 입력한 정보는 서비스 제공 외의 용도로 사용되지 않습니다.</p>
                </div>
            </div>


        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

        })

    </script>
</div>