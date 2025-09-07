<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$is_holiday_off = isset($row['is_holiday_off']) ? (int)$row['is_holiday_off'] : 0;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .char-counter.over-limit{color:#97989c !important;}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full" style="">
        <div class="  wv-vstack" style="row-gap: var(--wv-5)">

            <div class="wv-ps-subtitle">주차 여부 설정</div>
            <div class="form-floating position-relative" style="z-index: 10" data-max-char="<?php echo $this->parking_max_char; ?>">
                <input type="text" name="biz[parking]" value="<?php echo htmlspecialchars($row['parking']); ?>" id="biz[parking]" class="form-control" placeholder="ex) 주차 3대 가능, 지하 주차장 무료 이용 가능">
                <label for="biz[parking]" class="floatingInput">주차여부</label>
            </div>
            <p class="text-end fs-[11///600/#97989c]">
                <span class="char-counter" style="color:#0d171b" data-current="0">0</span> / <span class="char-limit"><?php echo $this->parking_max_char; ?></span>자
            </p>

        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var $input = $skin.find('input[name="biz[parking]"]');
            var $container = $skin.find('[data-max-char]');
            var $counter = $skin.find('.char-counter');
            var $limit = $skin.find('.char-limit');

            // 최대 글자수 가져오기
            var maxChar = parseInt($container.data('max-char')) || 20;

            // 초기 글자수 설정
            function updateCharCount() {
                var currentLength = $input.val().length;
                $counter.text(currentLength).attr('data-current', currentLength);

                // 글자수 초과 시 스타일 변경
                if (currentLength > maxChar) {
                    $counter.addClass('over-limit');
                } else {
                    $counter.removeClass('over-limit');
                }
            }

            // 글자수 제한 및 자르기
            function limitText() {
                var currentText = $input.val();
                var currentLength = currentText.length;

                if (currentLength > maxChar) {
                    // alert 표시
                    alert('최대 ' + maxChar + '자까지 입력 가능합니다.');

                    // 텍스트 자르기
                    var trimmedText = currentText.substring(0, maxChar);
                    $input.val(trimmedText);

                    // 글자수 업데이트
                    updateCharCount();
                }
            }

            // 이벤트 리스너 등록
            $input.on('input paste keyup', function() {
                // 짧은 지연 후 처리 (paste 이벤트 대응)
                setTimeout(function() {
                    limitText();
                    updateCharCount();
                }, 1);
            });

            // 초기 글자수 표시
            updateCharCount();
        });
    </script>
</div>