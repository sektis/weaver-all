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

            <p class="fs-[11/17/-0.44/600/#575757]">매장 방문 인증 유의사항</p>
            <div class="vstack mt-[10px] fs-[11/17/-0.44/500/#97989C]" style="row-gap: var(--wv-2)">
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>전자영수증이 아닌 종이영수증으로 제출해주시길 바랍니다. 실물 종이 영수증만 유효한 인증 자료로 인정됩니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>흔들린 사진, 영수증이 잘린 사진은 정보 확인이 어렵다고 판단되어 재업로드 요구가 행해질 수 있습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>위의 정보가 불일치하거나 누락된 경우, 신청이 어렵습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>도용 및 재사용으로 확인되면 통보없이 무효 처리되며, 패널티가 부과됩니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>영수증 조작은 기회없이 해당 계정의 모든 이벤트에 참여 불가 처리됩니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>매장 이용 인증에 대한 보상은 1개 계정으로만 받으실 수 있습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>영수증 정보 및 이미지 수집 • 보관 동의에 거부 시, 이벤트 참여 및 보상 지급이  제한됩니다.</p>
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