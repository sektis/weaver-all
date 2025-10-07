$(document).on('click','.wv-data-list-delete-btn',function (e) {


    if(confirm('정말로 삭제하시겠습니까?')){
        return true;
    }else{
        e.stopImmediatePropagation(); // 다른 click 핸들러 실행 안됨
        e.stopPropagation(); // 다른 click 핸들러 실행 안
        return false;
    }

})
$(document).ready(function () {

    $("body").loaded('.wv-ps-file', function (i, e) {
        var $ps_file = $(e);

        // 초기 상태 설정
        updateFileState($ps_file);

        // 파일 변경 이벤트
        $ps_file.on('change', 'input[type=file]:not([multiple])', function () {
            var $input = $(this);
            var $wrapper = $input.closest('.wv-ps-file');
            var $label = $input.closest('label');
            var $deleteLabel = $wrapper.find('.wv-ps-file-delete');
            var $deleteCheckbox = $deleteLabel.find('input[type=checkbox]');
            var file = this.files[0];

            // 파일이 변경/추가되면 삭제 체크 해제
            $deleteCheckbox.prop('checked', false);

            if (!file) {
                // 파일이 선택되지 않은 경우 - 초기 상태로 복원
                resetSingleFilePreview($wrapper);
                $deleteLabel.css('display', 'none');
                return;
            }

            // 삭제 버튼 표시 (원래 flex로 복원)
            $deleteLabel.css('display', 'flex');

            // 파일 타입에 따른 미리보기 처리
            if (file.type.match('image.*')) {
                // 이미지 파일인 경우 미리보기 표시
                var reader = new FileReader();
                reader.onload = function(e) {
                    var $img = $wrapper.find('img');

                    if ($img.length === 0) {
                        // img 태그가 없으면 생성
                        $img = $('<img alt="" class="wh-100 object-fit-contain">');
                        $label.prepend($img);
                    }

                    // 기존 파일명 텍스트 제거
                    $wrapper.find('.wv-ps-file-name').remove();

                    // 이미지 미리보기 설정
                    $img.attr('src', e.target.result);
                    $img.show();

                    // 상태 업데이트
                    updateFileState($wrapper);
                };

                reader.readAsDataURL(file);
            } else {
                // 이미지가 아닌 파일의 경우
                var $img = $wrapper.find('img');

                // 기존 이미지 숨기기
                if ($img.length > 0) {
                    $img.hide();
                }

                // 기존 파일명 텍스트 제거
                $wrapper.find('.wv-ps-file-name').remove();

                // 파일명 텍스트 추가
                var $fileName = $('<div class="wv-ps-file-name position-absolute inset-0 d-flex-center text-center p-3"></div>');
                $fileName.html('<div class="vstack" style="row-gap: var(--wv-6);"><i class="fa-solid fa-file fs-[30///700/] d-block"></i><p class="fs-[12//400/] text-break">' + file.name + '</p></div>');
                $label.append($fileName);

                // 상태 업데이트 (파일이 있다는 것을 표시)
                updateFileState($wrapper, true);
            }
        });

        // 삭제 버튼 클릭 이벤트
        $ps_file.on('click', '.wv-ps-file-delete', function (e) {
            // 이벤트 전파 중단
            e.stopPropagation();
            e.preventDefault();

            var $deleteLabel = $(this);
            var $wrapper = $deleteLabel.closest('.wv-ps-file');
            var $deleteCheckbox = $deleteLabel.find('input[type=checkbox]');

            // 무조건 파일 input 비우기
            $wrapper.find('input[type=file]').val('');

            // 체크박스 true로 설정
            $deleteCheckbox.prop('checked', true);

            // 삭제 버튼 숨기기
            $deleteLabel.css('display', 'none');

            // 미리보기 제거
            resetSingleFilePreview($wrapper);

            return false;
        });
    });

// 파일 상태 업데이트 함수
    function updateFileState($wrapper, hasFile) {
        var $img = $wrapper.find('img');
        var $fileName = $wrapper.find('.wv-ps-file-name');
        var $deleteLabel = $wrapper.find('.wv-ps-file-delete');
        var $newPlaceholder = $wrapper.find('.wv-ps-file-new');
        var imgSrc = $img.attr('src');
        var $fileInput = $wrapper.find('input[type=file]');

        // 파일이 있는지 확인 (이미지 src, 파일명 표시, 또는 file input의 files)
        var hasValidFile = hasFile ||
            (imgSrc && imgSrc.trim() !== '' && imgSrc !== 'undefined' && $img.is(':visible')) ||
            $fileName.length > 0 ||
            ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0);

        if (hasValidFile) {
            // 파일이 있는 경우
            $newPlaceholder.addClass('d-none');
            // 삭제 버튼 표시 여부는 별도 로직에서 처리
        } else {
            // 파일이 없는 경우
            $newPlaceholder.removeClass('d-none');
            $deleteLabel.css('display', 'none');
        }
    }

// 단독 파일 미리보기 초기화 함수
    function resetSingleFilePreview($wrapper) {
        var $img = $wrapper.find('img');
        var $fileName = $wrapper.find('.wv-ps-file-name');

        // 이미지 숨기기 및 src 제거
        if ($img.length > 0) {
            $img.attr('src', '');
            $img.hide();
        }

        // 파일명 텍스트 제거
        $fileName.remove();

        // 상태 업데이트
        updateFileState($wrapper);
    }

    $(document).loaded('.wv-ps-col',function (i,e) {

        var $ps_col = $(e);
        $ps_col.find('.wv-ps-each').each(function(){
            var $row = $(this);
            var $id = $row.find('input[name$="[id]"]').first();
            if ($id.length && $id.val() === 'skeleton') {
                $row.addClass('wv-ps-demo');
            }
        });
        renumberPsList($(">.wv-ps-list",$ps_col));
        updateMultiCounter($(">.wv-ps-list",$ps_col));



        // 삭제 체크박스 이벤트도 함께 추가
        $($ps_col).on('change', '.wv-ps-delete-label input[type=checkbox]', function () {
            var $checkbox = $(this);
            var $wrapper = $checkbox.closest('.wv-ps-each');
            var $deleteLabel = $checkbox.closest('.wv-ps-delete-label');

            if ($checkbox.is(':checked')) {
                // 삭제 체크 시 - 미리보기 제거 및 파일 input 초기화
                $wrapper.find('input[type=file]').val('');
                $deleteLabel.addClass('active');
            } else {
                // 삭제 해제 시
                $deleteLabel.removeClass('active');
                // 기존 이미지가 있다면 다시 표시 (원본 경로가 있는 경우)
                var $hiddenId = $wrapper.find('input[name$="[id]"]');
                if ($hiddenId.length && $hiddenId.val()) {
                    // 기존 이미지가 있으므로 복원 처리는 서버에서 처리
                }
            }
        });

        $($ps_col).on('change', "> .wv-ps-list input[multiple]", function (e) {
            e.stopPropagation(); // ✅ 추가: 상위로 이벤트 전파 차단
            var $multi   = $(this);
            var file_list_arr=[];
            var files = this.files || [];
            if (!files.length) return;
            var $ps_list = $multi.closest('.wv-ps-list');
            var $ps_each = $multi.closest('.wv-ps-each');



            // 0) 최대 개수 검사 (data-max-count 있으면)
            var maxAttr = $multi.data('max-count');
            if (maxAttr !== undefined) {
                var max = parseInt(maxAttr, 10);
                var active = countActiveRows($ps_list);
                if (!isNaN(max) && (active + files.length) > max) {
                    alert('최대 ' + max + '개까지 첨부할 수 있습니다.');
                    $multi.val('');
                    return;
                }
            }

            // 1) 행 인덱스 위치/다음 인덱스 산출(.wv-ps-new와 동일)
            var meta = findMaxRowIndexAndPos($ps_list);
            var pos  = meta.pos;
            var next = (meta.max >= 0 ? meta.max + 1 : 0);
            if (pos < 0) return; // [id] 패턴이 없으면 중단

            // 2) multiple의 name에서 마지막 []만 숫자로 채울 base 문자열 준비 (예: store[image] + [IDX])
            var info    = bracketTokens($multi.attr('name') || '');
            var tokens  = info.tokens.slice();              // ex) ['image', '']
            if (tokens.length && tokens[tokens.length - 1] === '') tokens.pop();
            var baseStr = info.root + tokens.map(function(t){ return '[' + t + ']'; }).join(''); // 'store[image]'

            // 3) 마지막 "실제 행"(id 필드 보유)을 베이스로 사용
            // var $base = $ps_list.children('.wv-ps-each').filter(function(){
            //     return $(this).find('input[name$="[id]"]').val() === 'skeleton';
            // }).first();
            var $base = $ps_list.children('.wv-ps-demo');

            if (!$base.length) return;


            // 4) 파일별로 새 행 생성
            for (var i = 0; i < files.length; i++) {
                var idx   = next++;
                var $row  = $base.clone(true, true);
                file_list_arr.push(files[i].name);
                // 이름 재인덱싱 + 값 초기화(.wv-ps-new 동일)

                reindexRow($row, pos, idx);

                // 파일 input 확보(없으면 생성) + 이름: store[image][IDX]
                var $file = $row.find('input[type="file"]').first();
                if (!$file.length) {
                    $file = $('<input type="file" class="d-none" accept="image/*">');
                    $row.append($file);
                }
                $file.attr('name', baseStr + '[' + idx + ']');

                // 파일 주입
                var dt = new DataTransfer();
                dt.items.add(files[i]);
                $file[0].files = dt.files;

                // 미리보기: 복제본에 img가 있으면 거기에만 세팅
                var $img = $row.find('img').first();
                if ($img.length) {
                    var url = URL.createObjectURL(files[i]);
                    $img.attr('src', url).show();
                }

                $row.removeClass('wv-ps-demo')
                // 표시 보장
                $row.show();
                // 마지막 실제 행 뒤에 삽입(.wv-ps-new와 동일)
                var $lastReal = $ps_list.children('.wv-ps-each').filter(function(){
                    return idField($(this)).length;
                }).last();
                ($lastReal.length ? $lastReal : $base).after($row);
            }

            // 5) 멀티 선택 UI 정리 + 번호 리프레시
            $multi.val('');

            renumberPsList($ps_list);
            updateMultiCounter($ps_list);
            var $multiple_list = $(".wv-ps-multiple-list",$ps_each);
            if($multiple_list.length){
                var file_lsit = file_list_arr.join(', ');
                $multiple_list.text(file_lsit).addClass('active');

            }
        })


        $("input[name*=\"delete\"]",$ps_col).on('change',function () {
            var $del = $(this);
            var $ps_list = $del.closest('.wv-ps-list');
            $del.val(1);
            var $ps_each = $del.closest('.wv-ps-each');
            if ($del.is(':checked')){

                $ps_each.fadeOut(100, function(){
                    if (!$ps_each.find(':input[name$="[id]"]').val()){

                        if($('.wv-ps-each:has(:input[name$="[id]"])',$ps_each.closest('.wv-ps-list')).length>1){
                            $ps_each.remove();
                        }

                    }
                });
            } else {
                $ps_each.show();
            }
            renumberPsList($ps_list);
            updateMultiCounter($ps_list);
        })

        $("> .wv-ps-list > .wv-ps-new", $ps_col).on('click',function (e) {
            // e.preventDefault();

            var $ps_list = $(this).parent('.wv-ps-list');
            if (!$ps_list.length) return false;

            var meta = findMaxRowIndexAndPos($ps_list);

            var pos  = meta.pos;
            var next = (meta.max >= 0 ? meta.max + 1 : 0);
            if (pos < 0) return false; // id 패턴이 없으면 인덱싱 불가

            // 마지막 “실제 행”(id 필드 보유)을 베이스로 선택

            var $base = $ps_list.children('.wv-ps-demo');

            // 없으면(모두 타일/빈 상태) 안전하게 중단 — 템플릿을 쓰지 않으므로 무분별 복제 방지
            if (!$base.length) return false;

            var $newRow = $base.clone(true, true);
            reindexRow($newRow, pos, next);

            // 추가: 새 행에서 미리보기 이미지 제거
            $newRow.find('img').attr('src','').hide();
            $newRow.find('.wv-ps-file-count').text('0');
            $newRow.find('.wv-ps-each:not(.wv-ps-demo):has([name*="[id]"][value=""])').remove()

            $newRow.show();
            // 마지막 실제 행 뒤에 삽입 (업로더/타일 앞이 아닌, 실제 컨텐츠 끝에 추가)
            $newRow.removeClass('wv-ps-demo')
            // $base.after($newRow);
            var $lastReal = $ps_list.children('.wv-ps-each').filter(function(){
                return idField($(this)).length;
            }).last();
            ($lastReal.length ? $lastReal : $base).after($newRow);

            $newRow.trigger('wv-ps-row-created');

            // UX: 첫 입력 포커스
            $newRow.find(':input:visible:first').focus();
            renumberPsList($ps_list);
            updateMultiCounter($ps_list);
            return false;
        })



    })



})

// === 헬퍼: 업로더 타일, 활성행 카운트, 카운터 갱신 ===
function getUploaderTile($ps_list){
    return $ps_list.children('.wv-ps-each').filter(function(){
        return $(this).find('input[multiple]').length > 0;
    }).first();
}

function countActiveRows($ps_list){
    var cnt = 0;
    $ps_list.children('.wv-ps-each').each(function(){
        var $row = $(this);
        if ($row.hasClass('wv-ps-demo')) return;

        // 현재 행에서만 [id], [delete]를 본다(하위 뎁스 제외)
        var $excludeDeep = $row.find('.wv-ps-list :input');
        var $id  = $row.find(':input[name$="\\[id\\]"]').not($excludeDeep).first();
        if (!$id.length) return; // id 필드가 없는 타일/빈 행은 제외

        var $del = $row.find(':input[name$="\\[delete\\]"]').not($excludeDeep).first();
        if ($del.length && $del.is(':checked')) return; // 삭제될 행 제외

        cnt++;
    });
    return cnt;
}

function updateMultiCounter($ps_list){
    var $tile = getUploaderTile($ps_list);
    if (!$tile.length) return;

    var cur = countActiveRows($ps_list);
    var max = parseInt($tile.find('input[multiple]').data('max-count'), 10);
    var $fc = $tile.find('.wv-ps-file-count');

    if ($fc.length){
        if (!isNaN(max)) $fc.text(cur);
        else $fc.text(String(cur));
    }
}


function bracketTokens(name){
    var root = name.split('[')[0] || '';
    var tokens = [];
    var re = /\[([^\]]*)\]/g, m;
    while ((m = re.exec(name))) tokens.push(m[1]);
    return { root: root, tokens: tokens };
}

function isNumericToken(t){
    return t !== '' && /^-?\d+$/.test(t); // -1, 0, 1, 2 등 음수/양수 모두 허용
}

// .wv-ps-each 내부에서 name이 [id]로 끝나는 필드
function idField($row){
    return $row.find(':input[name$="[id]"]').first();
}

// name 토큰 배열에서 "id" 바로 앞 토큰의 위치 = 행 인덱스 위치
function rowIndexPosFromIdName(idName){
    var tokens = bracketTokens(idName).tokens;
    for (var i = tokens.length - 1; i >= 0; i--){
        if (tokens[i] === 'id') return i - 1;
    }
    return -1;
}

// name 의 특정 토큰(pos)을 nextIndex 로 교체
function replaceRowIndexInName(name, pos, nextIndex){
    var parsed = bracketTokens(name);

    // console.log(pos,parsed)
    if (pos < 0 || pos >= parsed.tokens.length) return name;

    // ✅ 개선: -1 키 (demo)도 숫자로 인식하여 교체 가능하게 수정
    var targetToken = parsed.tokens[pos];
    var isReplaceable = (targetToken === '' ||
        /^-?\d+$/.test(targetToken) || // -1, 0, 1, 2 등 음수/양수 모두 허용
        targetToken === 'id' ||
        targetToken === 'ord' ||
        targetToken === 'delete');

    if (isReplaceable) {
        parsed.tokens[pos] = String(nextIndex);
        return parsed.root + parsed.tokens.map(function(t){ return '[' + t + ']'; }).join('');
    }
    return name;
}

// 현재 리스트에서 "id 필드가 있는 실제 행"만 대상으로 최대 인덱스와 pos를 구함
function findMaxRowIndexAndPos($ps_list){
    var max = -1, pos = -1;

    $ps_list.children('.wv-ps-each').each(function(){
        var $row = $(this);
        var $id = idField($row);
        if (!$id.length) return; // 업로더 타일 등 제외

        var name = $id.attr('name') || '';
        var p = rowIndexPosFromIdName(name);
        if (p < 0) return;

        pos = p; // 모든 행에서 동일해야 함
        var tk = bracketTokens(name).tokens[p];
        if (isNumericToken(tk)) {
            var v = parseInt(tk, 10);
            if (v > max) max = v;
        }
    });
    if(max<0){
        max=0;
    }
    return { max: max, pos: pos };
}

function resetControl($el){
    var tag = ($el.prop('tagName') || '').toLowerCase();
    var type = ($el.attr('type') || '').toLowerCase();
    if (tag === 'select'){
        $el.prop('selectedIndex', 0);
    } else if (type === 'checkbox' || type === 'radio'){
        $el.prop('checked', false);
    } else if (type === 'file'){
        // 파일은 값 리셋이 불가 → 새 노드로 교체
        var $n = $el.clone();
        $n.val('');
        $el.replaceWith($n);
    } else {
        $el.val('');
    }
}

// 새 행에 인덱스를 부여하고 값 초기화
// 새 행에 인덱스를 부여하고 값 초기화
function reindexRow($row, pos, nextIndex){
    // 1) name 속성 처리
    $row.find(':input[name]').each(function(){
        var $f = $(this);
        var oldName = $f.attr('name') || '';

        var newName = replaceRowIndexInName(oldName, pos, nextIndex);
        if (newName !== oldName) $f.attr('name', newName);

        // 값 초기화
        resetControl($f);

        // 보조 필드 규칙
        if (/\[id\]$/.test(newName))     $f.val('');             // 신규 id 비움
        if (/\[delete\]$/.test(newName)) $f.prop('checked', false).val('');
    });

    // 2) id 속성 처리 - input, select, textarea 등
    $row.find('[id]').each(function(){
        var $el = $(this);
        var oldId = $el.attr('id') || '';

        var newId = replaceRowIndexInName(oldId, pos, nextIndex);
        if (newId !== oldId) $el.attr('id', newId);
    });

    // 3) label for 속성 처리
    $row.find('label[for]').each(function(){
        var $label = $(this);
        var oldFor = $label.attr('for') || '';

        var newFor = replaceRowIndexInName(oldFor, pos, nextIndex);
        if (newFor !== oldFor) $label.attr('for', newFor);
    });
}
function renumberPsList($ps_list){
    var n = 1;

    $ps_list.children('.wv-ps-each').each(function(){
        var $row = $(this);

        // 하위 뎁스(.wv-ps-list 내부)의 필드는 제외하고 현재 행만 검사
        var $excludeDeep = $row.find('.wv-ps-list :input');
        var $idInput     = $row.find(':input[name$="\\[id\\]"]').not($excludeDeep).first();
        var $delInput    = $row.find(':input[name$="\\[delete\\]"]').not($excludeDeep).first();

        var hasId     = $idInput.length > 0;
        var isDeleted = $delInput.length ? $delInput.is(':checked') : false;
        var isDemo    = $row.hasClass('wv-ps-demo');

        // 번호 넣을 타겟: 직계 .wv-ps-num 우선, 없으면 하위 첫 .wv-ps-num
        var $num = $row.children('.wv-ps-num');
        if (!$num.length) $num = $row.find('.wv-ps-num').first();

        if (hasId && !isDeleted && !isDemo){
            $num.text(n++);
        }else{
            $num.text('');
        }
    });
}