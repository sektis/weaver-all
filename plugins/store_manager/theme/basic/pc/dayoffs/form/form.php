<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// StorePartProxy에서 자동 처리되므로 바로 사용
$dayoffs_list = $row['dayoffs'];
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .dayoff-item{display:flex;gap:var(--wv-8);height: var(--wv-48)}
        <?php echo $skin_selector?> .wv-ps-demo .dayoff-item{border-color:#f0f0f0;background:#fafafa;}
        <?php echo $skin_selector?> .dayoff-select{flex:1;font-size: var(--wv-16);background:#f9f9f9;padding:var(--wv-13) var(--wv-12);border-radius:var(--wv-4);cursor:pointer;user-select:none;position:relative;display:flex;align-items:center;justify-content:space-between;min-height:40px;}
        <?php echo $skin_selector?> .dayoff-select.empty{color:#999;background:#f8f9fa;}
        <?php echo $skin_selector?> .dayoff-select:hover{border-color:#007bff;}
        <?php echo $skin_selector?> .dayoff-select::after{content:'\f107';font-family:'Font Awesome 5 Free';font-weight:900;color:#666;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .delete-btn{background:#dc3545;color:white;border:none;padding:var(--wv-4) var(--wv-8);border-radius:var(--wv-4);cursor:pointer;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .delete-btn:hover{background:#c82333;}

        /* 날짜 룰렛 스타일 */
        <?php echo $skin_selector?> .date-roulette{position:relative;height: var(--wv-180); ;border:1px solid #ddd;border-radius:var(--wv-8);background:white;}
        <?php echo $skin_selector?> .date-roulette-container {
                                        position: relative;
                                        height: 100%;
                                        overflow-y: auto;
                                        scroll-behavior: smooth;
                                        touch-action: pan-y;
                                    }
        <?php echo $skin_selector?> .date-roulette-list {
                                        position: relative;
                                    }
        <?php echo $skin_selector?> .date-roulette-item{height:40px;display:flex;align-items:center;justify-content:center;font-size:var(--wv-16);color:#666;transition:all 0.3s ease;cursor:pointer;}
        <?php echo $skin_selector?> .date-roulette-item.empty{color:transparent;cursor:default;pointer-events:none;}
        <?php echo $skin_selector?> .date-roulette-item.active{background:#efefef;color:#fff;font-weight:600;font-size:var(--wv-18);border-radius:var(--wv-4);}
        <?php echo $skin_selector?> .date-roulette::before, <?php echo $skin_selector?> .date-roulette::after{content:'';position:absolute;left:0;right:0;height:80px;pointer-events:none;z-index:10;}
        <?php echo $skin_selector?> .date-roulette::before{top:0;background:linear-gradient(to bottom, rgba(255,255,255,0.9), rgba(255,255,255,0));}
        <?php echo $skin_selector?> .date-roulette::after{bottom:0;background:linear-gradient(to top, rgba(255,255,255,0.9), rgba(255,255,255,0));}

        /* 날짜 선택 버튼 */
        <?php echo $skin_selector?> .date-confirm-btn{background:#0d171b;color:white;border:none;padding:var(--wv-12) var(--wv-20);border-radius:var(--wv-4);width:100%;cursor:pointer;margin-top:var(--wv-12);}
        <?php echo $skin_selector?> .date-confirm-btn:hover{background:#0d171b;}
        <?php echo $skin_selector?> .date-confirm-btn:disabled{background:#ccc;cursor:not-allowed;}

        /* Offcanvas 내부 스타일 */
        <?php echo $skin_selector?> .option-list{display:flex;flex-direction:column;gap:var(--wv-8);}
        <?php echo $skin_selector?> .option-item{padding:var(--wv-12) var(--wv-16);border:1px solid #ddd;border-radius:var(--wv-4);cursor:pointer;display:flex;justify-content:space-between;align-items:center;background:white;}
        <?php echo $skin_selector?> .option-item:hover{background:#f8f9fa;border-color:#0d171b;}
        <?php echo $skin_selector?> .option-item.selected{background:#e3f2fd;border-color:#0d171b;}
        <?php echo $skin_selector?> .option-item i{color:#28a745;opacity:0.5;transition:opacity 0.2s;}
        <?php echo $skin_selector?> .option-item.selected i{opacity:1;}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-ps-col" style="">
        <div class="dayoffs-container">
            <p class="wv-ps-subtitle">정기휴무</p>

            <!-- 정기휴무 목록 -->
            <div class="wv-ps-list wv-vstack mt-[20px]" style="row-gap:var(--wv-20)">
                <?php
                foreach($dayoffs_list as $k => $dayoff) {
                    $id = isset($dayoff['id']) ? $dayoff['id'] : '';
                    $cycle = isset($dayoff['cycle']) ? $dayoff['cycle'] : '';
                    $target = isset($dayoff['target']) ? $dayoff['target'] : '';
                    $demo_class = ($id === '') ? 'wv-ps-demo' : '';
                    ?>
                    <div class="wv-ps-each <?php echo $demo_class; ?>" data-index="<?php echo $k; ?>">
                        <div class="dayoff-item">
                            <!-- 필수 hidden 필드들 -->
                            <input type="hidden" name="dayoffs[<?php echo $k; ?>][id]" value="<?php echo htmlspecialchars($id); ?>">

                            <div class="dayoff-select cycle-select <?php echo $cycle === '' ? 'empty' : ''; ?>" data-type="cycle">
                                <span><?php echo $cycle === '' ? '주기' : htmlspecialchars($cycle); ?></span>
                            </div>
                            <div class="dayoff-select target-select <?php echo $target === '' ? 'empty' : ''; ?>" data-type="target">
                                <span><?php echo $target === '' ? ($cycle === '매월' ? '날짜' : '요일/날짜') : htmlspecialchars($target); ?></span>
                            </div>

                            <!-- 일반 필드 hidden inputs -->
                            <input type="hidden" name="dayoffs[<?php echo $k; ?>][cycle]" value="<?php echo htmlspecialchars($cycle); ?>">
                            <input type="hidden" name="dayoffs[<?php echo $k; ?>][target]" value="<?php echo htmlspecialchars($target); ?>">

                         
                            <!-- 삭제 체크박스 (weaver 플러그인 규칙) -->
                            <label class="  wv-ps-delete-label-list " style="">
                                <input type="checkbox" class="d-none" name="dayoffs[<?php echo $k; ?>][delete]" value="1">
                                <span class="       " style="">  삭제</span>
                            </label>
                        </div>
                    </div>
                    <?php
                } ?>

                <button type="button" class="btn border w-100 wv-ps-new fs-[14////] fw-600 h-[40px]">
                    + 정기휴무일 추가
                </button>
            </div>
        </div>

        <!-- Bootstrap5 Offcanvas -->
        <div class="offcanvas offcanvas-bottom bg-white" tabindex="-1" id="dayoff-offcanvas" aria-labelledby="dayoff-offcanvas-label" style="height: auto;max-height: 80dvh">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="dayoff-offcanvas-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <!-- 옵션 리스트 -->
                <div class="option-list" id="option-list">
                    <!-- 동적으로 생성 -->
                </div>

                <!-- 날짜 룰렛 -->
                <div id="date-roulette-wrap" style="display:none;">
                <div class="date-roulette" id="date-roulette" >
                    <div class="date-roulette-container">
                        <div class="date-roulette-list" id="date-roulette-list">
                            <!-- 동적으로 생성 -->
                        </div>
                    </div>

                </div>
                <button type="button" class="date-confirm-btn" id="date-confirm-btn" disabled>날짜를 선택하세요</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var currentRow = null;
            var currentType = '';

            // 주기 옵션
            var cycleOptions = [
                '매주', '격주', '매월', '첫째주', '둘째주', '셋째주', '넷째주', '다섯째주'
            ];

            // 요일 옵션 (매월이 아닌 경우)
            var dayOptions = [
                '월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일'
            ];

            // 날짜 룰렛 관련 변수
            var dateRouletteData = [];
            for(var i = 1; i <= 31; i++) {
                dateRouletteData.push(i + '일');
            }
            var currentDateIndex = 0;
            var selectedDateIndex = -1; // 임시 선택된 날짜 인덱스

            // Bootstrap 5 Offcanvas 인스턴스
            var offcanvasElement = document.getElementById('dayoff-offcanvas');
            var offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);

            // 날짜 룰렛 초기화 (상단에 빈 행 2개 추가)
            function initDateRoulette() {
                var html = '';

                // 상단 빈 행 2개 추가
                html += '<div class="date-roulette-item empty">　</div>';
                html += '<div class="date-roulette-item empty">　</div>';

                // 실제 날짜 데이터
                for(var i = 0; i < dateRouletteData.length; i++) {
                    html += '<div class="date-roulette-item" data-index="' + i + '">' + dateRouletteData[i] + '</div>';
                }

                // 하단 빈 행 2개 추가
                html += '<div class="date-roulette-item empty">　</div>';
                html += '<div class="date-roulette-item empty">　</div>';

                $skin.find('#date-roulette-list').html(html);

                // active 클래스 업데이트
                updateDateRoulettePosition();
            }

            // 날짜 룰렛 위치 업데이트 (active 클래스만 처리)
            function updateDateRoulettePosition() {
                var $list = $skin.find('#date-roulette-list');

                // 모든 active 클래스 제거
                $list.find('.date-roulette-item').removeClass('active');

                // 현재 확정된 날짜에 active 클래스 추가 (offcanvas 열릴 때)
                if (currentDateIndex >= 0) {
                    var $currentItem = $list.find('.date-roulette-item').not('.empty').eq(currentDateIndex);
                    $currentItem.addClass('active');
                }

                // 임시 선택된 날짜가 있으면 그것에 active 클래스 추가 (클릭했을 때)
                if (selectedDateIndex >= 0) {
                    var $selectedItem = $list.find('.date-roulette-item').not('.empty').eq(selectedDateIndex);
                    $currentItem.removeClass('active');
                    $selectedItem.addClass('active');
                }

                // 선택된 항목으로 스크롤
                var targetIndex = selectedDateIndex >= 0 ? selectedDateIndex : currentDateIndex;
                if (targetIndex >= 0) {
                    var $targetItem = $list.find('.date-roulette-item').not('.empty').eq(targetIndex);
                    if ($targetItem.length) {
                        $targetItem[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center',
                            inline: 'nearest'
                        });
                    }
                }
            }

            // 날짜 선택 UI 업데이트
            function updateDateSelectUI() {
                var $btn = $skin.find('#date-confirm-btn');
                if (selectedDateIndex >= 0) {
                    var selectedDate = dateRouletteData[selectedDateIndex];
                    $btn.text(selectedDate + '로 설정하기').prop('disabled', false);
                } else {
                    $btn.text('날짜를 선택하세요').prop('disabled', true);
                }
            }

            // 선택 적용 함수
            function applySelection(value) {
                if (currentRow && currentType) {
                    var $div = currentRow.find('[data-type="' + currentType + '"]');
                    var $hidden = currentRow.find('input[name*="[' + currentType + ']"]');

                    // div 내용 업데이트
                    $div.find('span').text(value);
                    $div.removeClass('empty');

                    // hidden input 업데이트
                    $hidden.val(value);

                    // demo 클래스 제거 및 id 설정
                    if (currentRow.hasClass('wv-ps-demo')) {
                        currentRow.removeClass('wv-ps-demo');
                        // data-index 제거 (parts.js가 DOM 순서로 처리하도록)
                        currentRow.removeAttr('data-index');
                        // 새로운 항목이므로 id는 비워둠 (저장 시 자동 생성)
                        currentRow.find('input[name*="[id]"]').val('');
                    }

                    // 주기가 변경되면 target 초기화 및 라벨 변경
                    if (currentType === 'cycle') {
                        var $targetDiv = currentRow.find('.target-select');
                        var $targetHidden = currentRow.find('input[name*="[target]"]');

                        var placeholder = (value === '매월') ? '날짜' : '요일';
                        $targetDiv.find('span').text(placeholder);
                        $targetDiv.addClass('empty');
                        $targetHidden.val('');

                        // cycle hidden input도 즉시 업데이트 (target 클릭 시 최신 값 참조하도록)
                        currentRow.find('input[name*="[cycle]"]').val(value);
                    }
                }

                // offcanvas 닫기
                offcanvasInstance.hide();
                currentRow = null;
                currentType = '';
                selectedDateIndex = -1; // 임시 선택 초기화
            }

            // div 클릭 시 offcanvas 열기
            $skin.on('click', '.dayoff-select', function(e){
                e.preventDefault();
                e.stopPropagation();

                currentRow = $(this).closest('.wv-ps-each');
                currentType = $(this).data('type');

                var currentValue = currentRow.find('input[name*="[' + currentType + ']"]').val();
                var options = [];
                var title = '';

                if (currentType === 'cycle') {
                    options = cycleOptions;
                    title = '주기를 선택해주세요.';

                    // 일반 옵션 리스트 표시
                    $skin.find('#option-list').show();
                    $skin.find('#date-roulette-wrap').hide();

                } else if (currentType === 'target') {
                    var cycle = currentRow.find('input[name*="[cycle]"]').val();
                    if (!cycle) {
                        alert('먼저 주기를 선택하세요.');
                        return;
                    }

                    if (cycle === '매월') {
                        // 날짜 룰렛 표시
                        title = '날짜를 선택해주세요.';
                        $skin.find('#option-list').hide();
                        $skin.find('#date-roulette-wrap').show();

                        // 현재 선택된 날짜로 룰렛 위치 설정 (정확한 매칭)
                        if (currentValue && currentValue.endsWith('일')) {
                            var dayNum = parseInt(currentValue.replace('일', ''));
                            if (dayNum >= 1 && dayNum <= 31) {
                                currentDateIndex = dayNum - 1; // 1일 = index 0
                            } else {
                                currentDateIndex = 0;
                            }
                        } else {
                            currentDateIndex = 0; // 기본적으로 1일 선택
                        }

                        initDateRoulette();

                        // 임시 선택 초기화 및 UI 업데이트
                        selectedDateIndex = -1;
                        updateDateSelectUI();

                    } else {
                        // 요일 옵션
                        options = dayOptions;
                        title = '요일을 선택해주세요.';
                        $skin.find('#option-list').show();
                        $skin.find('#date-roulette-wrap').hide();
                    }
                }

                // 일반 옵션 리스트 처리
                if (options.length > 0) {
                    // offcanvas 제목과 옵션 설정
                    $skin.find('#dayoff-offcanvas-label').text(title);
                    var optionHtml = '';
                    for(var i = 0; i < options.length; i++) {
                        var option = options[i];
                        var isSelected = (option === currentValue);
                        var selectedClass = isSelected ? 'selected' : '';
                        optionHtml += '<div class="option-item ' + selectedClass + '" data-value="' + option + '">' +
                            '<span>' + option + '</span>' +
                            '<i class="fas fa-check"></i>' +
                            '</div>';
                    }
                    $skin.find('#option-list').html(optionHtml);
                } else {
                    $skin.find('#dayoff-offcanvas-label').text(title);
                }

                // offcanvas 열기
                offcanvasInstance.show();
            });

            // 옵션 선택
            $skin.on('click', '.option-item', function(){
                var value = $(this).data('value');
                applySelection(value);
            });

            // 날짜 룰렛 클릭 선택 (빈 행 제외) - 선택만 하고 적용은 버튼으로만
            $skin.on('click', '.date-roulette-item:not(.empty)', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var clickedText = $(this).text();
                var clickedIndex = dateRouletteData.indexOf(clickedText);
                if (clickedIndex !== -1) {
                    selectedDateIndex = clickedIndex;
                    updateDateRoulettePosition();
                    updateDateSelectUI();
                }
                // 여기서 applySelection() 호출하지 않음 - 버튼으로만 적용
            });

            // 날짜 확인 버튼 클릭 - 여기서만 실제 적용
            $skin.on('click', '#date-confirm-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (selectedDateIndex >= 0) {
                    var selectedValue = dateRouletteData[selectedDateIndex];
                    applySelection(selectedValue);
                }
            });

            // parts.js에서 처리하는 공통 기능들은 제거 (wv-ps-del, wv-ps-new)
            // parts.js가 자동으로 처리함
        });
    </script>
</div>