<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
// menu 파트처럼 $row['list'] 처리

$dayoffs_list = $row['list'];

// 데이터가 없으면 빈 배열로 초기화 (foreach가 최소 1번은 돌도록)
if (!count($dayoffs_list)) {
    $dayoffs_list = array(array('cycle'=>'','target'=>''));
}
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .dayoff-item{display:flex;align-items:center;gap:var(--wv-8);margin-bottom:var(--wv-8);padding:var(--wv-8);border:1px solid #e9ecef;border-radius:var(--wv-4);}
        <?php echo $skin_selector?> .dayoff-item.wv-ps-demo{border-color:#f0f0f0;background:#fafafa;}
        <?php echo $skin_selector?> .dayoff-select{flex:1;border:1px solid #ddd;background:white;padding:var(--wv-8) var(--wv-12);border-radius:var(--wv-4);cursor:pointer;user-select:none;position:relative;display:flex;align-items:center;justify-content:space-between;min-height:40px;}
        <?php echo $skin_selector?> .dayoff-select.empty{color:#999;background:#f8f9fa;}
        <?php echo $skin_selector?> .dayoff-select:hover{border-color:#007bff;}
        <?php echo $skin_selector?> .dayoff-select::after{content:'\f107';font-family:'Font Awesome 5 Free';font-weight:900;color:#666;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .add-btn{background:#007bff;color:white;border:none;padding:var(--wv-12) var(--wv-16);border-radius:var(--wv-4);width:100%;cursor:pointer;}
        <?php echo $skin_selector?> .add-btn:hover{background:#0056b3;}
        <?php echo $skin_selector?> .delete-btn{background:#dc3545;color:white;border:none;padding:var(--wv-4) var(--wv-8);border-radius:var(--wv-4);cursor:pointer;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .delete-btn:hover{background:#c82333;}
        <?php echo $skin_selector?> .offcanvas{position:fixed;bottom:0;left:0;right:0;background:white;border-radius:var(--wv-12) var(--wv-12) 0 0;box-shadow:0 -4px 20px rgba(0,0,0,0.3);transform:translateY(100%);transition:transform 0.3s ease;z-index:1050;max-height:60vh;}
        <?php echo $skin_selector?> .offcanvas.show{transform:translateY(0);}
        <?php echo $skin_selector?> .offcanvas-header{padding:var(--wv-16) var(--wv-20);border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;}
        <?php echo $skin_selector?> .offcanvas-body{padding:var(--wv-20);max-height:50vh;overflow-y:auto;}
        <?php echo $skin_selector?> .option-list{display:flex;flex-direction:column;gap:var(--wv-8);}
        <?php echo $skin_selector?> .option-item{padding:var(--wv-12) var(--wv-16);border:1px solid #ddd;border-radius:var(--wv-4);cursor:pointer;display:flex;justify-content:space-between;align-items:center;}
        <?php echo $skin_selector?> .option-item:hover{background:#f8f9fa;border-color:#007bff;}
        <?php echo $skin_selector?> .option-item.selected{background:#e3f2fd;border-color:#007bff;}
        <?php echo $skin_selector?> .option-item i{color:#28a745;opacity:0.5;transition:opacity 0.2s;}
        <?php echo $skin_selector?> .option-item.selected i{opacity:1;}
        <?php echo $skin_selector?> .btn-close{background:none;border:none;font-size:var(--wv-24);cursor:pointer;}

        /* 날짜 룰렛 스타일 */
        <?php echo $skin_selector?> .date-roulette{position:relative;height:200px;overflow:hidden;border:1px solid #ddd;border-radius:var(--wv-8);background:white;}
        <?php echo $skin_selector?> .date-roulette-container{position:relative;height:100%;touch-action:pan-y;}
        <?php echo $skin_selector?> .date-roulette-list{position:absolute;top:50%;left:0;right:0;transform:translateY(-50%);transition:transform 0.3s ease;}
        <?php echo $skin_selector?> .date-roulette-item{height:40px;display:flex;align-items:center;justify-content:center;font-size:var(--wv-16);color:#666;transition:all 0.3s ease;}
        <?php echo $skin_selector?> .date-roulette-item.active{color:#007bff;font-weight:600;font-size:var(--wv-18);}
        <?php echo $skin_selector?> .date-roulette::before, <?php echo $skin_selector?> .date-roulette::after{content:'';position:absolute;left:0;right:0;height:80px;pointer-events:none;z-index:10;}
        <?php echo $skin_selector?> .date-roulette::before{top:0;background:linear-gradient(to bottom, rgba(255,255,255,0.9), rgba(255,255,255,0));}
        <?php echo $skin_selector?> .date-roulette::after{bottom:0;background:linear-gradient(to top, rgba(255,255,255,0.9), rgba(255,255,255,0));}
        <?php echo $skin_selector?> .date-roulette-center{position:absolute;top:50%;left:0;right:0;height:40px;transform:translateY(-50%);border-top:1px solid #007bff;border-bottom:1px solid #007bff;background:rgba(0,123,255,0.05);pointer-events:none;z-index:5;}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-ps-col" style="">
        <div class="dayoffs-container">

            <!-- 정기휴무 목록 -->
            <div class="wv-ps-list">
                <?php
                $idx = 0;
                foreach($dayoffs_list as $k => $dayoff) {
                    $id = isset($dayoff['id']) ? $dayoff['id'] : '';
                    $ord = isset($dayoff['ord']) ? (int)$dayoff['ord'] : ($idx + 1);
                    $cycle = isset($dayoff['cycle']) ? $dayoff['cycle'] : '';
                    $target = isset($dayoff['target']) ? $dayoff['target'] : '';
                    $demo_class = ($id === '') ? 'wv-ps-demo' : '';
                    ?>
                    <div class="wv-ps-each dayoff-item <?php echo $demo_class; ?>" data-index="<?php echo $k; ?>">
                        <!-- 필수 hidden 필드들 -->
                        <input type="hidden" name="dayoffs[<?php echo $k; ?>][id]" value="<?php echo htmlspecialchars($id); ?>">
                        <input type="hidden" name="dayoffs[<?php echo $k; ?>][ord]" value="<?php echo $ord; ?>">

                        <div class="dayoff-select cycle-select <?php echo $cycle === '' ? 'empty' : ''; ?>" data-type="cycle">
                            <span><?php echo $cycle === '' ? '주기 선택' : htmlspecialchars($cycle); ?></span>
                        </div>
                        <div class="dayoff-select target-select <?php echo $target === '' ? 'empty' : ''; ?>" data-type="target">
                            <span><?php echo $target === '' ? ($cycle === '매월' ? '날짜 선택' : '요일 선택') : htmlspecialchars($target); ?></span>
                        </div>
                        <button type="button" class="delete-btn wv-ps-del" data-index="<?php echo $k; ?>">삭제</button>

                        <!-- 일반 필드 hidden inputs -->
                        <input type="hidden" name="dayoffs[<?php echo $k; ?>][cycle]" value="<?php echo htmlspecialchars($cycle); ?>">
                        <input type="hidden" name="dayoffs[<?php echo $k; ?>][target]" value="<?php echo htmlspecialchars($target); ?>">

                        <!-- 삭제 체크박스 (weaver 플러그인 규칙) -->
                        <input type="checkbox" name="dayoffs[<?php echo $k; ?>][delete]" value="1" style="display:none;">
                    </div>
                    <?php
                    $idx++;
                } ?>

                <button type="button" class="add-btn wv-ps-new">
                    + 정기휴무일 추가
                </button>
            </div>

        </div>

        <!-- Offcanvas -->
        <div class="offcanvas" id="dayoff-offcanvas">
            <div class="offcanvas-header">
                <h5 id="offcanvas-title">주기 선택</h5>
                <button type="button" class="btn-close">&times;</button>
            </div>
            <div class="offcanvas-body">
                <div class="option-list" id="option-list">
                    <!-- 동적으로 생성 -->
                </div>
                <div class="date-roulette" id="date-roulette" style="display:none;">
                    <div class="date-roulette-center"></div>
                    <div class="date-roulette-container">
                        <div class="date-roulette-list" id="date-roulette-list">
                            <!-- 동적으로 생성 -->
                        </div>
                    </div>
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
            var isDateRouletteDragging = false;
            var dateRouletteStartY = 0;
            var dateRouletteStartTransform = 0;

            // 날짜 룰렛 초기화
            function initDateRoulette() {
                var html = '';
                // 앞뒤로 여유분 추가
                for(var i = 0; i < 5; i++) {
                    html += '<div class="date-roulette-item">' + dateRouletteData[dateRouletteData.length - 5 + i] + '</div>';
                }
                for(var i = 0; i < dateRouletteData.length; i++) {
                    html += '<div class="date-roulette-item">' + dateRouletteData[i] + '</div>';
                }
                for(var i = 0; i < 5; i++) {
                    html += '<div class="date-roulette-item">' + dateRouletteData[i] + '</div>';
                }
                $skin.find('#date-roulette-list').html(html);
            }

            // 날짜 룰렛 위치 업데이트
            function updateDateRoulettePosition() {
                var offset = -(currentDateIndex + 5) * 40; // 40px per item
                $skin.find('#date-roulette-list').css('transform', 'translateY(calc(-50% + ' + offset + 'px))');

                // active 클래스 업데이트
                $skin.find('.date-roulette-item').removeClass('active');
                $skin.find('.date-roulette-item').eq(currentDateIndex + 5).addClass('active');
            }

            // 날짜 룰렛 이벤트
            function bindDateRouletteEvents() {
                var $roulette = $skin.find('#date-roulette');

                // 마우스 휠
                $roulette.off('wheel').on('wheel', function(e) {
                    e.preventDefault();
                    if (e.originalEvent.deltaY > 0) {
                        currentDateIndex = Math.min(currentDateIndex + 1, dateRouletteData.length - 1);
                    } else {
                        currentDateIndex = Math.max(currentDateIndex - 1, 0);
                    }
                    updateDateRoulettePosition();
                });

                // 터치/마우스 이벤트
                $roulette.off('mousedown touchstart').on('mousedown touchstart', function(e) {
                    isDateRouletteDragging = true;
                    dateRouletteStartY = e.type === 'touchstart' ? e.originalEvent.touches[0].clientY : e.clientY;
                    dateRouletteStartTransform = currentDateIndex;
                    e.preventDefault();
                });

                $(document).off('mousemove.dateRoulette touchmove.dateRoulette').on('mousemove.dateRoulette touchmove.dateRoulette', function(e) {
                    if (!isDateRouletteDragging) return;

                    var currentY = e.type === 'touchmove' ? e.originalEvent.touches[0].clientY : e.clientY;
                    var deltaY = dateRouletteStartY - currentY;
                    var steps = Math.round(deltaY / 40);

                    currentDateIndex = Math.max(0, Math.min(dateRouletteData.length - 1, dateRouletteStartTransform + steps));
                    updateDateRoulettePosition();
                    e.preventDefault();
                });

                $(document).off('mouseup.dateRoulette touchend.dateRoulette').on('mouseup.dateRoulette touchend.dateRoulette', function(e) {
                    isDateRouletteDragging = false;

                    // 선택된 값 적용
                    if (currentRow && currentType === 'target') {
                        var selectedValue = dateRouletteData[currentDateIndex];
                        applySelection(selectedValue);
                    }
                });

                // 클릭으로 선택
                $roulette.off('click', '.date-roulette-item').on('click', '.date-roulette-item', function(e) {
                    if (isDateRouletteDragging) return;

                    var clickedText = $(this).text();
                    var clickedIndex = dateRouletteData.indexOf(clickedText);
                    if (clickedIndex !== -1) {
                        currentDateIndex = clickedIndex;
                        updateDateRoulettePosition();

                        // 선택된 값 적용
                        if (currentRow && currentType === 'target') {
                            applySelection(clickedText);
                        }
                    }
                });
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
                        // 새로운 항목이므로 id는 비워둠 (저장 시 자동 생성)
                        currentRow.find('input[name*="[id]"]').val('');
                    }

                    // 주기가 변경되면 target 초기화 및 라벨 변경
                    if (currentType === 'cycle') {
                        var $targetDiv = currentRow.find('.target-select');
                        var $targetHidden = currentRow.find('input[name*="[target]"]');

                        var placeholder = (value === '매월') ? '날짜 선택' : '요일 선택';
                        $targetDiv.find('span').text(placeholder);
                        $targetDiv.addClass('empty');
                        $targetHidden.val('');
                    }
                }

                // offcanvas 닫기
                closeOffcanvas();
            }

            // offcanvas 닫기 함수
            function closeOffcanvas() {
                $skin.find('.offcanvas').removeClass('show');
                currentRow = null;
                currentType = '';
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
                    title = '주기 선택';

                    // 일반 옵션 리스트 표시
                    $skin.find('#option-list').show();
                    $skin.find('#date-roulette').hide();

                } else if (currentType === 'target') {
                    var cycle = currentRow.find('input[name*="[cycle]"]').val();
                    if (!cycle) {
                        alert('먼저 주기를 선택하세요.');
                        return;
                    }

                    if (cycle === '매월') {
                        // 날짜 룰렛 표시
                        title = '날짜 선택';
                        $skin.find('#option-list').hide();
                        $skin.find('#date-roulette').show();

                        // 현재 선택된 날짜로 룰렛 위치 설정
                        if (currentValue && currentValue.endsWith('일')) {
                            var dayNum = parseInt(currentValue.replace('일', ''));
                            currentDateIndex = Math.max(0, Math.min(dayNum - 1, dateRouletteData.length - 1));
                        } else {
                            currentDateIndex = 0;
                        }

                        initDateRoulette();
                        updateDateRoulettePosition();
                        bindDateRouletteEvents();

                    } else {
                        // 요일 옵션
                        options = dayOptions;
                        title = '요일 선택';
                        $skin.find('#option-list').show();
                        $skin.find('#date-roulette').hide();
                    }
                }

                // 일반 옵션 리스트 처리
                if (options.length > 0) {
                    // offcanvas 제목과 옵션 설정
                    $skin.find('#offcanvas-title').text(title);
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
                    $skin.find('#offcanvas-title').text(title);
                }

                // offcanvas 열기
                $skin.find('.offcanvas').addClass('show');
            });

            // 옵션 선택
            $skin.on('click', '.option-item', function(){
                var value = $(this).data('value');
                applySelection(value);
            });

            // 삭제 버튼 클릭
            $skin.on('click', '.wv-ps-del', function(e){
                e.preventDefault();
                e.stopPropagation();

                var $row = $(this).closest('.wv-ps-each');
                var $deleteInput = $row.find('input[name*="[delete]"]');
                var hasId = $row.find('input[name*="[id]"]').val() !== '';

                if (hasId) {
                    // 기존 데이터면 delete 체크박스 체크하고 숨김
                    $deleteInput.prop('checked', true);
                    $row.hide();
                } else {
                    // 새 데이터면 바로 제거
                    $row.remove();
                }
            });

            // 추가 버튼 클릭
            $skin.on('click', '.wv-ps-new', function(e){
                e.preventDefault();

                var $list = $skin.find('.wv-ps-list');
                var $lastRow = $list.find('.wv-ps-each').last();
                var newIndex = 0;

                // 새 인덱스 찾기
                $list.find('.wv-ps-each').each(function(){
                    var name = $(this).find('input[name*="[id]"]').attr('name') || '';
                    var match = name.match(/dayoffs\[(\d+)\]/);
                    if (match) {
                        var idx = parseInt(match[1]);
                        if (idx >= newIndex) newIndex = idx + 1;
                    }
                });

                // 새 행 HTML 생성
                var newRowHtml = '<div class="wv-ps-each dayoff-item wv-ps-demo" data-index="' + newIndex + '">' +
                    '<input type="hidden" name="dayoffs[' + newIndex + '][id]" value="">' +
                    '<input type="hidden" name="dayoffs[' + newIndex + '][ord]" value="' + (newIndex + 1) + '">' +
                    '<div class="dayoff-select cycle-select empty" data-type="cycle">' +
                    '<span>주기 선택</span>' +
                    '</div>' +
                    '<div class="dayoff-select target-select empty" data-type="target">' +
                    '<span>요일 선택</span>' +
                    '</div>' +
                    '<button type="button" class="delete-btn wv-ps-del" data-index="' + newIndex + '">삭제</button>' +
                    '<input type="hidden" name="dayoffs[' + newIndex + '][cycle]" value="">' +
                    '<input type="hidden" name="dayoffs[' + newIndex + '][target]" value="">' +
                    '<input type="checkbox" name="dayoffs[' + newIndex + '][delete]" value="1" style="display:none;">' +
                    '</div>';

                // 추가 버튼 앞에 삽입
                $(this).before(newRowHtml);
            });

            // 닫기 버튼 클릭
            $skin.on('click', '.btn-close', function(){
                closeOffcanvas();
            });

            // 배경 클릭 시 닫기
            $skin.on('click', '.offcanvas', function(e){
                if (e.target === this) {
                    closeOffcanvas();
                }
            });
        });
    </script>
</div>