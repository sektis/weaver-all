<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div id="<?php echo $skin_id; ?>">
    <style>
        <?php echo $skin_selector?> .date-input-wrap {
            gap: var(--wv-8);
        }
        <?php echo $skin_selector?> .date-separator {
                                        font-size: var(--wv-14);
                                        font-weight: 500;
                                        color: #97989C;
                                        line-height: var(--wv-39);
                                    }
        <?php echo $skin_selector?> input[type="date"] {
                                        height: var(--wv-39);
                                    }
    </style>

    <div class="hstack date-input-wrap">
        <div class="form-floating">
            <input type="date"
                   name="<?php echo str_replace('[start_end]', '[start]', $field_name); ?>"
                   value="<?php echo $row['start'] ? date('Y-m-d', strtotime($row['start'])) : ''; ?>"
                   id="<?php echo $part_key; ?>_start_<?php echo $skin_id; ?>"
                   class="form-control required"
                   required>
            <label for="<?php echo $part_key; ?>_start_<?php echo $skin_id; ?>">시작일</label>
        </div>

        <span class="date-separator">~</span>

        <div class="form-floating">
            <input type="date"
                   name="<?php echo str_replace('[start_end]', '[end]', $field_name); ?>"
                   value="<?php echo $row['end'] ? date('Y-m-d', strtotime($row['end'])) : ''; ?>"
                   id="<?php echo $part_key; ?>_end_<?php echo $skin_id; ?>"
                   class="form-control required"
                   required>
            <label for="<?php echo $part_key; ?>_end_<?php echo $skin_id; ?>">종료일</label>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("#<?php echo $skin_id; ?>");
            var $startInput = $("#<?php echo $part_key; ?>_start_<?php echo $skin_id; ?>", $skin);
            var $endInput = $("#<?php echo $part_key; ?>_end_<?php echo $skin_id; ?>", $skin);

            // 종료일 유효성 검사
            function validateDateRange(changedInput){
                var startDate = $startInput.val();
                var endDate = $endInput.val();

                if(startDate && endDate){
                    if(new Date(endDate) <= new Date(startDate)){
                        alert('종료일은 시작일보다 나중이어야 합니다.');

                        // 잘못 선택한 날짜 초기화 (폼 제출 시에는 초기화하지 않음)
                        if(changedInput === 'start'){
                            $startInput.val('').focus();
                        } else if(changedInput === 'end'){
                            $endInput.val('').focus();
                        }
                        return false;
                    }
                }
                return true;
            }

            // 시작일 변경 시 종료일 최소값 설정
            $startInput.on('change', function(){
                var startDate = $(this).val();
                if(startDate){
                    // 종료일의 최소값을 시작일 다음날로 설정
                    var nextDay = new Date(startDate);
                    nextDay.setDate(nextDay.getDate() + 1);
                    $endInput.attr('min', nextDay.toISOString().split('T')[0]);
                }
                validateDateRange('start');
            });

            // 종료일 변경 시 유효성 검사
            $endInput.on('change', function(){
                validateDateRange('end');
            });

            // 폼 제출 시 최종 검사
            $skin.closest('form').on('submit', function(e){
                if(!validateDateRange(null)){
                    e.preventDefault();
                    return false;
                }
            });

            // 초기 로드 시 시작일이 있으면 종료일 최소값 설정
            if($startInput.val()){
                $startInput.trigger('change');
            }
        });
    </script>
</div>