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

            <p class="fs-[11/17/-0.44/600/#575757]">친구 초대 이벤트 유의사항</p>
            <div class="vstack mt-[10px] fs-[11/17/-0.44/500/#97989C]" style="row-gap: var(--wv-2)">
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>초대는 신규 가입자에 한해 인정됩니다. <br>- 기존 회원이 초대코드를 입력한 경우에는 보상이 지급되지 않습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>초대한 친구가 회원가입을 완료해야 초대가 인정됩니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>1인이 다수 계정을 생성하거나 부정한 방법으로 참여한 경우, <br>이벤트 참여가 제한되며, 지급된 보상은 회수될 수 있습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>초대코드는 개인별 1개만 발급됩니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>초대한 친구 수는 시스템 기준으로 실시간 집계되며, 지연이 발생할 수 있습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>랭킹은 매달 1일 기준으로 초기화되며, 당월 내 누적 초대 수로 순위가 결정됩니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>1위 순위 진입을 위해서는 최소 30명 초대 조건이 충족되어야 합니다. <br>- 30명 미만 초대 시, 1위 랭킹이더라도 2위로 처리될 수 있습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>출금은 리워드 누적 금액이 30,000원을 초과한 경우에만 가능합니다. <br>(친구 초대 리워드 및 기타 이벤트 보상 금액이 모두 합산됩니다.)</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>이벤트 운영 및 보상 기준은 내부 정책에 따라 사전고지 없이 변경될 수 있습니다.</p>
                </div>
                <div class="hstack align-items-start" style="gap:var(--wv-2)">
                    <p>*</p>
                    <p>이벤트와 관련된 문의는 고객센터 또는 앱 내 문의하기를 이용해주세요.</p>
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