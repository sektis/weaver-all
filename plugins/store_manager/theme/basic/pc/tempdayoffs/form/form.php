<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// StorePartProxy에서 자동 처리되므로 바로 사용
$tempdayoffs_list = $row['tempdayoffs'];
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .tempdayoff-item{display:flex;align-items:center;gap:var(--wv-8);}
        <?php echo $skin_selector?> .wv-ps-demo .tempdayoff-item{border-color:#f0f0f0;background:#fafafa;}
        <?php echo $skin_selector?> .tempdayoff-display{flex:1; ; ;padding:var(--wv-8) var(--wv-12);border-radius:var(--wv-4); ;display:flex;align-items:center;cursor:pointer;height: var(--wv-48);background:#f8f9fa}
        <?php echo $skin_selector?> .tempdayoff-display.empty{color:#999;background:#f8f9fa;}
        <?php echo $skin_selector?> .tempdayoff-display:hover{border-color:#007bff;}
        <?php echo $skin_selector?> .add-btn{background:#007bff;color:white;border:none;padding:var(--wv-12) var(--wv-16);border-radius:var(--wv-4);width:100%;cursor:pointer;}
        <?php echo $skin_selector?> .add-btn:hover{background:#0056b3;}
        <?php echo $skin_selector?> .delete-btn{background:#dc3545;color:white;border:none;padding:var(--wv-4) var(--wv-8);border-radius:var(--wv-4);cursor:pointer;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .delete-btn:hover{background:#c82333;}

        /* 달력 스타일 */
        <?php echo $skin_selector?> .calendar-container{background:white;border-radius:var(--wv-8);padding:var(--wv-20);}
        <?php echo $skin_selector?> .calendar-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--wv-20);}
        <?php echo $skin_selector?> .calendar-nav{background:none;border:none;font-size:var(--wv-18);cursor:pointer;padding:var(--wv-8);}
        <?php echo $skin_selector?> .calendar-title{font-size:var(--wv-18);font-weight:600;}
        <?php echo $skin_selector?> .calendar-grid{display:grid;grid-template-columns:repeat(7,1fr);}
        <?php echo $skin_selector?> .calendar-day-header{text-align:center;padding:var(--wv-8);font-weight:600;color:#666;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .calendar-day{
                                        text-align:center;
                                        cursor:pointer;
                                        border-radius:var(--wv-4);
                                        position:relative;
                                        font-size:var(--wv-14);
                                        height:var(--wv-47);
                                        display:flex;
                                        flex-direction:column;
                                        justify-content:center;
                                        align-items:center;
                                        padding:var(--wv-4);
                                    }
        <?php echo $skin_selector?> .calendar-day:hover{background:#f0f0f0;}
        <?php echo $skin_selector?> .calendar-day.other-month{color:#ccc;}
        <?php echo $skin_selector?> .calendar-day.selected{background:#000;color:#fff;}
        <?php echo $skin_selector?> .calendar-day.in-range{background:#efefef;border-radius: 0!important;}
        <?php echo $skin_selector?> .calendar-day.today{font-weight:600;color:#007bff;}
        <?php echo $skin_selector?> .calendar-day .day-number{
                                        flex:1;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:var(--wv-14);
                                    }
        <?php echo $skin_selector?> .calendar-day .day-label{
                                        font-size:var(--wv-9);
                                        line-height:1;
                                        margin-top:auto;
                                        height:var(--wv-12);
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
            max-height: 0;overflow: hidden;
            transition: max-height .4s ease;
                                    }
        <?php echo $skin_selector?> .calendar-day.selected .day-label{
                                        max-height: 100px;
                                    }

        /* 기간 선택 버튼 */
        <?php echo $skin_selector?> .period-buttons{display:flex;gap:var(--wv-8);margin:var(--wv-20) 0;justify-content: end}
        <?php echo $skin_selector?> .period-btn{ ;padding:var(--wv-12);border:1px solid #efefef;background:white;border-radius:var(--wv-4);cursor:pointer;text-align:center;color:#97989c}
        <?php echo $skin_selector?> .period-btn.active{color:#0d171b;border:1px solid #0d171b}

        /* 날짜 표시 */
        <?php echo $skin_selector?> .date-display{display:flex;gap:var(--wv-20);margin:var(--wv-20) 0;}
        <?php echo $skin_selector?> .date-item{flex:1;text-align:center;}
        <?php echo $skin_selector?> .date-label{font-size:var(--wv-12);color:#666;margin-bottom:var(--wv-4);}
        <?php echo $skin_selector?> .date-value{font-size:var(--wv-14);font-weight:500;}
        <?php echo $skin_selector?> .date-value.empty{color:#999;}

        /* 선택완료 버튼 */
        <?php echo $skin_selector?> .confirm-btn{width:100%;padding:var(--wv-16);border:none;border-radius:var(--wv-4);font-size:var(--wv-16);font-weight:600;cursor:pointer;margin-top:var(--wv-20);}
        <?php echo $skin_selector?> .confirm-btn.disabled{background:#cfcfcf;color:#999;cursor:not-allowed;}
        <?php echo $skin_selector?> .confirm-btn.enabled{background:#0d171b;color:white;}

        /* Offcanvas 내부 스타일 */
        <?php echo $skin_selector?> .offcanvas-body{padding:var(--wv-20);}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-ps-col" style="">
        <div class="tempdayoffs-container">
            <p class="wv-ps-subtitle">임시휴무</p>

            <!-- 임시휴무 목록 -->
            <div class="wv-ps-list  wv-vstack mt-[20px]" style="row-gap:var(--wv-20)">
                <?php
                foreach($tempdayoffs_list as $k => $tempdayoff) {
                    $id = isset($tempdayoff['id']) ? $tempdayoff['id'] : '';
                    $start_date = isset($tempdayoff['start_date']) ? $tempdayoff['start_date'] : '';
                    $end_date = isset($tempdayoff['end_date']) ? $tempdayoff['end_date'] : '';
                    $demo_class = ($id === '') ? 'wv-ps-demo' : '';

                    // 날짜 표시 포맷
                    $display_text = '';
                    if ($start_date && $end_date) {
                        $start_obj = DateTime::createFromFormat('Y-m-d', $start_date);
                        $end_obj = DateTime::createFromFormat('Y-m-d', $end_date);
                        if ($start_obj && $end_obj) {
                            $start_formatted = $start_obj->format('Y.m.d') . ' (' . ['일','월','화','수','목','금','토'][$start_obj->format('w')] . ')';
                            $end_formatted = $end_obj->format('Y.m.d') . ' (' . ['일','월','화','수','목','금','토'][$end_obj->format('w')] . ')';
                            $display_text = $start_formatted . ' ~ ' . $end_formatted;
                        }
                    }
                    ?>
                    <div class="wv-ps-each <?php echo $demo_class; ?>" data-index="<?php echo $k; ?>">
                        <div class="tempdayoff-item">
                            <!-- 필수 hidden 필드들 -->
                            <input type="hidden" name="tempdayoffs[<?php echo $k; ?>][id]" value="<?php echo htmlspecialchars($id); ?>">

                            <div class="tempdayoff-display <?php echo empty($display_text) ? 'empty' : ''; ?>">
                                <span><?php echo empty($display_text) ? '임시휴무일 선택' : htmlspecialchars($display_text); ?></span>
                            </div>

                            <!-- 날짜 필드 hidden inputs -->
                            <input type="hidden" name="tempdayoffs[<?php echo $k; ?>][start_date]" value="<?php echo htmlspecialchars($start_date); ?>">
                            <input type="hidden" name="tempdayoffs[<?php echo $k; ?>][end_date]" value="<?php echo htmlspecialchars($end_date); ?>">

                            <!-- 삭제 체크박스 (weaver 플러그인 규칙) -->
                            <label class="  wv-ps-delete-label-list " style="">
                                <input type="checkbox" class="d-none" name="tempdayoffs[<?php echo $k; ?>][delete]" value="1">
                                <span class="       " style="">  삭제</span>
                            </label>
                        </div>
                    </div>
                    <?php
                } ?>

                <button type="button" class="btn border w-100 wv-ps-new fs-[14////] fw-600 h-[40px]">
                    + 임시휴무일 추가
                </button>
            </div>
        </div>

        <!-- Bootstrap5 Offcanvas -->
        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="tempdayoff-offcanvas" aria-labelledby="tempdayoff-offcanvas-label" style="height: auto;max-height: 80dvh">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="tempdayoff-offcanvas-label">임시휴무일 선택</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <!-- 달력 -->
                <div class="calendar-container">
                    <div class="calendar-header">
                        <button type="button" class="calendar-nav" id="prev-month">&lt;</button>
                        <div class="calendar-title" id="calendar-title">2025년 1월</div>
                        <button type="button" class="calendar-nav" id="next-month">&gt;</button>
                    </div>
                    <div class="calendar-grid" id="calendar-grid">
                        <!-- 동적으로 생성 -->
                    </div>
                </div>

                <!-- 기간 선택 버튼 -->
                <div class="period-buttons">
                    <div class="period-btn" id="multi-days">며칠 쉬어요</div>
                    <div class="period-btn" id="single-day">하루만 쉬어요</div>

                </div>

                <!-- 날짜 표시 -->
                <div class="date-display">
                    <div class="date-item">
                        <div class="date-label">시작일</div>
                        <div class="date-value empty" id="start-date-display">선택안함</div>
                    </div>
                    <div class="date-item">
                        <div class="date-label">마지막일</div>
                        <div class="date-value empty" id="end-date-display">선택안함</div>
                    </div>
                </div>

                <!-- 선택완료 버튼 -->
                <button type="button" class="confirm-btn disabled" id="confirm-selection">선택완료</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var currentRow = null;
            var currentYear = new Date().getFullYear();
            var currentMonth = new Date().getMonth();
            var selectedStartDate = null;
            var selectedEndDate = null;

            // Bootstrap 5 Offcanvas 인스턴스
            var offcanvasElement = document.getElementById('tempdayoff-offcanvas');
            var offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);

            // 요일 헤더
            var dayHeaders = ['일', '월', '화', '수', '목', '금', '토'];

            // 달력 초기화
            function initCalendar() {
                renderCalendar(currentYear, currentMonth);
            }

            // 달력 렌더링
            function renderCalendar(year, month) {
                var $grid = $skin.find('#calendar-grid');
                var $title = $skin.find('#calendar-title');

                // 제목 업데이트
                $title.text(year + '년 ' + (month + 1) + '월');

                var html = '';

                // 요일 헤더
                for (var i = 0; i < dayHeaders.length; i++) {
                    html += '<div class="calendar-day-header">' + dayHeaders[i] + '</div>';
                }

                // 첫째 날과 마지막 날
                var firstDay = new Date(year, month, 1);
                var lastDay = new Date(year, month + 1, 0);
                var startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - firstDay.getDay());

                var today = new Date();
                today.setHours(0, 0, 0, 0);

                // 6주 표시
                for (var week = 0; week < 6; week++) {
                    for (var day = 0; day < 7; day++) {
                        var currentDate = new Date(startDate);
                        currentDate.setDate(startDate.getDate() + (week * 7) + day);

                        var isOtherMonth = currentDate.getMonth() !== month;
                        var isToday = currentDate.getTime() === today.getTime();
                        var dateStr = formatDateString(currentDate);

                        var classes = ['calendar-day'];
                        if (isOtherMonth) classes.push('other-month');
                        if (isToday) classes.push('today');

                        html += '<div class="' + classes.join(' ') + '" data-date="' + dateStr + '">';
                        html += '<div class="day-number">' + currentDate.getDate() + '</div>';
                        html += '<div class="day-label"></div>';
                        html += '</div>';
                    }
                }

                $grid.html(html);
                updateCalendarSelection();
            }

            // 날짜 문자열 포맷 (YYYY-MM-DD)
            function formatDateString(date) {
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                return year + '-' + month + '-' + day;
            }

            // 날짜 표시 포맷 (YYYY.MM.DD (요일))
            function formatDisplayDate(dateStr) {
                var date = new Date(dateStr);
                var dayNames = ['일', '월', '화', '수', '목', '금', '토'];
                var formatted = date.getFullYear() + '.' +
                    String(date.getMonth() + 1).padStart(2, '0') + '.' +
                    String(date.getDate()).padStart(2, '0');
                return formatted + ' (' + dayNames[date.getDay()] + ')';
            }

            // 달력 선택 상태 업데이트
            function updateCalendarSelection() {
                var $days = $skin.find('.calendar-day');

                $days.removeClass('selected in-range');
                $days.find('.day-label').text('');

                if (selectedStartDate) {
                    var $startDay = $days.filter('[data-date="' + selectedStartDate + '"]');
                    $startDay.addClass('selected').find('.day-label').text('시작');
                }

                if (selectedEndDate) {
                    var $endDay = $days.filter('[data-date="' + selectedEndDate + '"]');
                    $endDay.addClass('selected').find('.day-label').text('마지막');

                    // 범위 표시
                    if (selectedStartDate && selectedEndDate) {
                        var startTime = new Date(selectedStartDate).getTime();
                        var endTime = new Date(selectedEndDate).getTime();

                        $days.each(function() {
                            var dayTime = new Date($(this).data('date')).getTime();
                            if (dayTime > startTime && dayTime < endTime) {
                                $(this).addClass('in-range');
                            }
                        });
                    }
                }
            }

            // 기간 버튼 업데이트
            function updatePeriodButtons() {
                var $singleBtn = $skin.find('#single-day');
                var $multiBtn = $skin.find('#multi-days');

                $singleBtn.removeClass('active');
                $multiBtn.removeClass('active');

                if (selectedStartDate && selectedEndDate) {
                    if (selectedStartDate === selectedEndDate) {
                        $singleBtn.addClass('active');
                    } else {
                        $multiBtn.addClass('active');
                    }
                }
            }

            // 날짜 표시 업데이트
            function updateDateDisplay() {
                var $startDisplay = $skin.find('#start-date-display');
                var $endDisplay = $skin.find('#end-date-display');

                if (selectedStartDate) {
                    $startDisplay.text(formatDisplayDate(selectedStartDate)).removeClass('empty');
                } else {
                    $startDisplay.text('선택안함').addClass('empty');
                }

                if (selectedEndDate) {
                    $endDisplay.text(formatDisplayDate(selectedEndDate)).removeClass('empty');
                } else {
                    $endDisplay.text('선택안함').addClass('empty');
                }
            }

            // 선택완료 버튼 상태 업데이트
            function updateConfirmButton() {
                var $btn = $skin.find('#confirm-selection');

                if (selectedStartDate && selectedEndDate) {
                    $btn.removeClass('disabled').addClass('enabled');
                } else {
                    $btn.removeClass('enabled').addClass('disabled');
                }
            }

            // 모든 UI 업데이트
            function updateUI() {
                updateCalendarSelection();
                updatePeriodButtons();
                updateDateDisplay();
                updateConfirmButton();
            }

            // 선택 적용 함수
            function applySelection() {
                if (currentRow && selectedStartDate && selectedEndDate) {
                    var $display = currentRow.find('.tempdayoff-display');
                    var $startHidden = currentRow.find('input[name*="[start_date]"]');
                    var $endHidden = currentRow.find('input[name*="[end_date]"]');

                    // hidden input 업데이트
                    $startHidden.val(selectedStartDate);
                    $endHidden.val(selectedEndDate);

                    // 표시 텍스트 생성
                    var displayText = formatDisplayDate(selectedStartDate) + ' ~ ' + formatDisplayDate(selectedEndDate);
                    $display.find('span').text(displayText);
                    $display.removeClass('empty');

                    // demo 클래스 제거
                    if (currentRow.hasClass('wv-ps-demo')) {
                        currentRow.removeClass('wv-ps-demo');
                        currentRow.removeAttr('data-index');
                        currentRow.find('input[name*="[id]"]').val('');
                    }

                    // 행 보이기 (새로 만든 행인 경우)
                    currentRow.show();
                }

                // offcanvas 닫기
                offcanvasInstance.hide();
                currentRow = null;
                selectedStartDate = null;
                selectedEndDate = null;
            }

            // 이벤트 리스너들

            // parts.js에서 새 행 생성 후 커스텀 이벤트 발생시 처리
            $skin.on('wv-ps-row-created', '.wv-ps-each', function() {
                // tempdayoff 컨테이너 내의 새 행인 경우에만 처리
                if ($(this).closest('.tempdayoffs-container').length) {
                    currentRow = $(this);
                    currentRow.hide(); // 일단 숨김

                    // 초기값 설정
                    selectedStartDate = null;
                    selectedEndDate = null;

                    initCalendar();
                    updateUI();
                    offcanvasInstance.show();
                }
            });

            // 기존 행 클릭 (선택된 날짜 수정)
            $skin.on('click', '.tempdayoff-display', function(e) {
                e.preventDefault();
                e.stopPropagation();

                currentRow = $(this).closest('.wv-ps-each');

                // 기존 값 로드
                var startDate = currentRow.find('input[name*="[start_date]"]').val();
                var endDate = currentRow.find('input[name*="[end_date]"]').val();

                selectedStartDate = startDate || null;
                selectedEndDate = endDate || null;

                initCalendar();
                updateUI();
                offcanvasInstance.show();
            });

            // 달력 네비게이션
            $skin.on('click', '#prev-month', function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar(currentYear, currentMonth);
            });

            $skin.on('click', '#next-month', function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar(currentYear, currentMonth);
            });

            // 날짜 클릭 - 3단계 로직 (첫 클릭: 시작일, 두 번째 클릭: 마지막일, 세 번째 클릭: 초기화)
            $skin.on('click', '.calendar-day:not(.other-month)', function() {
                var dateStr = $(this).data('date');

                if (!selectedStartDate) {
                    // 1단계: 시작일 선택
                    selectedStartDate = dateStr;
                    selectedEndDate = null;
                } else if (!selectedEndDate) {
                    // 2단계: 종료일 선택
                    var startTime = new Date(selectedStartDate).getTime();
                    var endTime = new Date(dateStr).getTime();

                    if (endTime >= startTime) {
                        selectedEndDate = dateStr;
                    } else {
                        // 시작일보다 이전 날짜 선택시 시작일로 재설정
                        selectedStartDate = dateStr;
                        selectedEndDate = null;
                    }
                } else {
                    // 3단계: 시작일과 종료일이 모두 선택된 상태 → 초기화하고 새 시작일 설정
                    selectedStartDate = dateStr;
                    selectedEndDate = null;
                }

                updateUI();
            });

            // 선택완료 버튼
            $skin.on('click', '#confirm-selection', function() {
                if (!$(this).hasClass('disabled')) {
                    applySelection();
                }
            });

            // 초기 달력 로드
            initCalendar();
        });
    </script>
</div>