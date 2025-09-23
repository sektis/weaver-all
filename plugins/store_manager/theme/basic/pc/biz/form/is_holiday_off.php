<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$is_holiday_off = isset($row['is_holiday_off']) ? (int)$row['is_holiday_off'] : 0;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .form-check.form-switch{margin:0;}
        <?php echo $skin_selector?> .form-check.disabled{opacity:0.7;}
        <?php echo $skin_selector?> .holiday-section{background:#f8f9fa;padding:var(--wv-16);border-radius:var(--wv-8);margin-bottom:var(--wv-16);}
        <?php echo $skin_selector?> .dayoffs-section{border:1px solid #e9ecef;border-radius:var(--wv-8);padding:var(--wv-16);}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full" style="">
        <div class="w-[500px]   mw-100" style=" ">

            <!-- 공휴일 휴무 설정 -->
            <div style="padding-bottom: var(--wv-40)">
                <div class="hstack justify-content-between">
                    <div class="wv-ps-subtitle">공휴일</div>
                    <div class="form-check form-switch  <?php echo $is_holiday_off?'':'disabled'; ?>" style="gap:var(--wv-6)" data-on-value="설정 함" data-off-value="설정 안함">
                        <label class="form-check-label" for="holiday-off-switch">

                        </label>
                        <input class="form-check-input"
                               type="checkbox"
                               role="switch"
                               name="<?php echo $field_name; ?>"
                               value="1"
                            <?php echo $is_holiday_off?'checked':''; ?>
                               id="holiday-off-switch">

                    </div>
                </div>
                <div class="fs-[12/17/-0.48/500/#97989C] mt-[14px]  vstack" style="row-gap:var(--wv-8)">
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>‘공휴일' 에는 일요일이 포함되지 않습니다. <br>(예: 법정 공휴일인 설날, 추석, 어린이날 등만 해당)</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>일요일을 휴무로 설정하시려면 직접 별도로 지정해주세요.</p></div>
                </div>
            </div>



            <div class="wv-mx-fit" style="height: 10px;background-color: #efefef"></div>
            <!-- 정기휴무 설정 -->
            <div style="padding: var(--wv-16) 0 var(--wv-20)">
                <div>
                <?php
                echo $this->manager->get($row['wr_id'])->dayoffs->render_part('form','form');
                ?>
                </div>
            </div>
            <div class="wv-mx-fit" style="height: 10px;background-color: #efefef"></div>
            <!-- 임시휴무 설정 -->
            <div style="padding: var(--wv-16) 0 var(--wv-20)">
                <div>
                <?php
                echo $this->manager->get($row['wr_id'])->tempdayoffs->render_part('form','form');
                ?>
                </div>
                <div class="fs-[12/17/-0.48/500/#97989C] mt-[14px]  vstack" style="row-gap:var(--wv-8)">
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>설정하신 임시 휴무일이 지나면 자동으로 목록에서 사라지며, 별도로  삭제하지 않으셔도 됩니다</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>설정하신 임시 휴무는 마지막 날 다음 날부터 자동으로 해제됩니다.</p></div>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");


        });
    </script>
</div>