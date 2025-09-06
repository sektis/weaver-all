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
        <div class="w-[500px] wv-vstack" style="row-gap: var(--wv-20)">

            <!-- 공휴일 휴무 설정 -->
            <div class="hstack justify-content-between">
                <div class="wv-ps-subtitle">공휴일</div>
                <div class="form-check form-switch <?php echo $is_holiday_off?'':'disabled'; ?>">
                    <input class="form-check-input"
                           type="checkbox"
                           role="switch"
                           name="<?php echo $field_name; ?>"
                           value="1"
                        <?php echo $is_holiday_off?'checked':''; ?>
                           id="holiday-off-switch">
                    <label class="form-check-label" for="holiday-off-switch">
                        <?php echo $is_holiday_off?'설정 함':'설정 안함'; ?>
                    </label>
                </div>
            </div>



            <!-- 정기휴무 설정 -->
            <div class="">
                <div class="wv-ps-subtitle">정기휴무</div>
                <div>
                <?php
                echo $this->manager->get($row['wr_id'])->dayoffs->render_part('form');
                ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");

            // 공휴일 휴무 스위치 이벤트
            $skin.on('change', '#holiday-off-switch', function(){
                var checked = $(this).is(':checked');
                var $switch = $(this).closest('.form-switch');
                var $label = $switch.find('label');

                $switch.toggleClass('disabled', !checked);
                $label.text(checked ? '설정 함' : '설정 안함');
            });
        });
    </script>
</div>