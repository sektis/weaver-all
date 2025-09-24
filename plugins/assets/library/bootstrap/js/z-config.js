// bootstrap5

// 페이지 로드시 URL hash 확인

$(document).ready(function () {
    if (location.hash) {
        var tabElement = document.querySelector('[data-bs-target="' + location.hash + '"]');
        if (tabElement) {
            var tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }

    // 탭 변경시 URL hash 업데이트
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tabElement) {
        tabElement.addEventListener('shown.bs.tab', function(event) {
            // URL hash 업데이트 (페이지는 스크롤되지 않음)
            var target = event.target.getAttribute('data-bs-target');
            history.replaceState(null, null, target);
        });
    });
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => {new bootstrap.Popover(popoverTriggerEl);popoverTriggerEl.setAttribute('tabindex','-1')})
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {new bootstrap.Tooltip(tooltipTriggerEl);tooltipTriggerEl.setAttribute('tabindex','-1')})

    $(document).loaded('[data-bs-toggle="popover"]',function () {
        new bootstrap.Popover(this)
    })
    $(document).loaded('[data-bs-toggle="tooltip"]',function () {
        new bootstrap.Tooltip(this)
    })

    window.wv_viewport = ResponsiveBootstrapToolkit;

    const $siteWrapper = $('#site-wrapper');


    const wvPortalStore = new WeakMap();

    document.addEventListener('show.bs.modal', (e) => {
        const el = e.target;
        if (!el.classList.contains('wv-modal-portal')) return;

        let rec = wvPortalStore.get(el);
        if (!rec) {
            const originalParent = el.parentElement;
            const placeholder = document.createComment('modal placeholder');
            wvPortalStore.set(el, { originalParent, placeholder });
            originalParent && originalParent.replaceChild(placeholder, el);
        }
        document.body.appendChild(el);
    });

    document.addEventListener('hidden.bs.modal', (e) => {
        const el = e.target;
        if (!el.classList.contains('wv-modal-portal')) return;

        const rec = wvPortalStore.get(el);
        if (!rec) return;

        const { originalParent, placeholder } = rec;

        // body에서 제거
        if (el.parentElement === document.body) {
            document.body.removeChild(el);
        }

        // 원래 위치로 복원
        if (originalParent && placeholder && placeholder.parentElement === originalParent) {
            originalParent.replaceChild(el, placeholder);
        }

        // store에서 기록 정리 (중요!)
        wvPortalStore.delete(el);
    });
    document.addEventListener('show.bs.offcanvas', (e) => {
        const el = e.target;
        if (!el.classList.contains('wv-offcanvas-portal')) return;

        let rec = wvPortalStore.get(el);
        if (!rec) {
            const originalParent = el.parentElement;
            const placeholder = document.createComment('offcanvas placeholder');
            wvPortalStore.set(el, { originalParent, placeholder });
            originalParent && originalParent.replaceChild(placeholder, el);
        }
        document.body.appendChild(el);
    });

    document.addEventListener('hidden.bs.offcanvas', (e) => {
        const el = e.target;
        if (!el.classList.contains('wv-offcanvas-portal')) return;

        const rec = wvPortalStore.get(el);
        if (!rec) return;

        const { originalParent, placeholder } = rec;

        // body에서 제거
        if (el.parentElement === document.body) {
            document.body.removeChild(el);
        }

        // 원래 위치로 복원
        if (originalParent && placeholder && placeholder.parentElement === originalParent) {
            originalParent.replaceChild(el, placeholder);
        }

        // store에서 기록 정리
        wvPortalStore.delete(el);
    });



    function wv_getScrollbarWidth() {
        if ($(document).height() <= $(window).height()) {
            return 0;
        }

        var outer = document.createElement('div');
        var inner = document.createElement('div');
        var widthNoScroll;
        var widthWithScroll;

        outer.style.visibility = 'hidden';
        outer.style.width = '100px';
        document.body.appendChild(outer);

        widthNoScroll = outer.offsetWidth;

        // Force scrollbars
        outer.style.overflow = 'scroll';

        // Add inner div
        inner.style.width = '100%';
        outer.appendChild(inner);

        widthWithScroll = inner.offsetWidth;

        // Remove divs
        outer.parentNode.removeChild(outer);

        return widthNoScroll - widthWithScroll;
    }

    document.addEventListener('show.bs.offcanvas', function (e) {
        const offcanvasEl = e.target
        const scrollbarWidth = wv_getScrollbarWidth();

        // shown 시점이 아니라 show 시점에 미리 패딩 부여
        var move_x = scrollbarWidth/2*-1;
        // $(offcanvasEl).find('.offcanvas-body').css('transform',`translateX(${move_x}px)`);
        // offcanvasEl.style.paddingRight = `${scrollbarWidth}px`
    })






    $(document).on('hide.bs.modal', function(e) {
        var $modal = $(e.target);
        if ($modal.data('need-refresh')) {
            wv_handle_parent_reload($modal, true);  // true = 닫힘 이벤트에서 호출
        }
    });

// offcanvas 닫힐 때 이벤트
    $(document).on('hide.bs.offcanvas', function(e) {
        var $offcanvas = $(e.target);
        if ($offcanvas.data('need-refresh')) {
            wv_handle_parent_reload($offcanvas, true);  // true = 닫힘 이벤트에서 호출
        }
    });

})

// 부모 리로드 처리 함수
function wv_handle_parent_reload($currentElement, isFromCloseEvent = false) {

    if (isFromCloseEvent) {
        // modal/offcanvas 닫힐 때: parent-elem 사용
        var parentElemId = $currentElement.data('parent-elem');

        var reloadCount = parseInt($currentElement.attr('data-wv-reload-count') || '0');

        if(reloadCount==0){
            return false;
        }


        if (parentElemId) {
            var $parentElement = $('#' + parentElemId);

            if ($parentElement.length) {


                // 부모가 offcanvas인 경우
                if ($parentElement.hasClass('offcanvas') || $parentElement.hasClass('wv-offcanvas')) {
                    wv_reload_offcanvas(parentElemId);
                    return;
                }

                // 부모가 modal인 경우
                if ($parentElement.hasClass('modal') || $parentElement.hasClass('wv-modal')) {
                    wv_reload_modal(parentElemId);
                    return;
                }
            }
        }
    } else {
        // type이 없는 일반 ajax 요청: data-wv-reload-url 찾기
        var $parentElement = $currentElement.closest('[data-wv-reload-url]');


        if ($parentElement.length) {
            // 부모가 offcanvas인 경우
            if ($parentElement.hasClass('offcanvas') || $parentElement.hasClass('wv-offcanvas')) {
                var parentId = $parentElement.attr('id');
                if (parentId) {

                    wv_reload_offcanvas(parentId);
                    return;
                }
            }

            // 부모가 modal인 경우
            if ($parentElement.hasClass('modal') || $parentElement.hasClass('wv-modal')) {
                var parentId = $parentElement.attr('id');
                if (parentId) {
                    wv_reload_modal(parentId);
                    return;
                }
            }
        }
    }

    // 적절한 부모를 찾지 못하면 페이지 새로고침
    // alert(1)
    location.reload();
}

// 옵션 파싱 공통 함수
function parseWvAjaxOptions(options,$from) {
    var processedOptions = {};

    if($from!=undefined){
        processedOptions.$clickElement = $from;
    }


    if (typeof options === 'string') {
        // 문자열 옵션 파싱
        var optionArray = options.split(',');
        for (var i = 0; i < optionArray.length; i++) {
            var option = optionArray[i].trim();

            if (option.indexOf('class:') === 0) {
                processedOptions.class = option.substring(6).trim();
            }
            else if (option.indexOf('id:') === 0) {
                processedOptions.id = option.substring(3).trim();
            }
            else if (option.indexOf('target:') === 0) {
                processedOptions.target = option.substring(7).trim();
            }
            else if (option.indexOf('append:') === 0) {
                processedOptions.append = option.substring(7).trim();
            }
            else if (option.indexOf('replace:') === 0) {
                processedOptions.replace = option.substring(8).trim();
            }
            else if (option.indexOf('replace_with:') === 0) {
                processedOptions.replace_with = option.substring(13).trim();
            }
            else if (option.indexOf('reload_ajax:') === 0) {
                processedOptions.reload_ajax = option.substring(12).trim();
                if(processedOptions.reload_ajax==='true'){
                    processedOptions.reload_ajax = true;
                }
            }
            else if (option.indexOf('ajax_option:') === 0) {
                try {
                    var ajaxOptionStr = option.substring(12).trim();
                    if (ajaxOptionStr.startsWith('{') && ajaxOptionStr.endsWith('}')) {
                        ajaxOptionStr = ajaxOptionStr.replace(/([{,]\s*)(\w+):/g, '$1"$2":');
                        ajaxOptionStr = ajaxOptionStr.replace(/:(\w+)([,}])/g, ':"$1"$2');
                    }
                    processedOptions.ajax = JSON.parse(ajaxOptionStr);
                } catch(e) {
                    console.error('ajax_option 파싱 오류:', e);
                }
            }
            else {
                if (!processedOptions.other) processedOptions.other = [];
                processedOptions.other.push(option);
            }
        }
    } else {
        // 객체면 그대로 사용
        processedOptions = options || {};
    }
    // type 찾기 (processedOptions.other에서 추출)
    var type;
    if (processedOptions.other) {
        for (var i = 0; i < processedOptions.other.length; i++) {
            var option = processedOptions.other[i];
            if (option === 'modal' || option === 'offcanvas') {
                type = option;
                // other 배열에서 제거
                processedOptions.other.splice(i, 1);
                break;
            }
        }
    }
    processedOptions.type = type;

    if(processedOptions.type && (processedOptions.target||processedOptions.append||processedOptions.replace||processedOptions.replace_with)){
        alert(processedOptions.type+'에서는 dom 변경 불가');
    }

    if(processedOptions.reload_ajax===true && !processedOptions.$clickElement){
        alert('reload element not found');
    }

    // reload_ajax 처리 추가
    if (processedOptions.reload_ajax === true) {
        if (!processedOptions.type) {
            // type이 없으면 클릭 이벤트 기준 부모 리로드
            processedOptions.reload_ajax = 'parent';
        } else if (processedOptions.type === 'offcanvas' || processedOptions.type === 'modal') {
            // offcanvas나 modal인 경우 닫힐 때 리로드 설정
            processedOptions.reload_ajax = 'on_close';
        }
    }

    return processedOptions;
}

// 데이터 파싱 공통 함수
function parseWvAjaxData(dataAttr) {
    var ajaxData = {};
    if (dataAttr) {
        try {
            if (typeof dataAttr === 'string') {
                ajaxData = JSON.parse(dataAttr);
            } else if (typeof dataAttr === 'object') {
                ajaxData = dataAttr;
            }
        } catch(e) {
            alert('data-wv-ajax-data 파싱 오류:', e);
            ajaxData = {};
        }
    }
    return ajaxData;
}

$(document).on('click', '[data-wv-ajax-url]', function (e) {

    e.preventDefault();

    var $this = $(this);

    // 기본 속성 값들 가져오기
    var url = $this.attr('data-wv-ajax-url');
    var dataAttr = $this.attr('data-wv-ajax-data');
    var dataAddAttr = $this.attr('data-wv-ajax-data-add'); // ✅ 추가
    var optionAttr = $this.attr('data-wv-ajax-option');

    // 데이터 처리
    var ajaxData = {};

    // 기본 데이터 파싱
    if (dataAttr) {
        ajaxData = parseWvAjaxData(dataAttr);
    }

    // 추가 데이터 파싱 및 병합
    if (dataAddAttr) {
        var additionalData = parseWvAjaxData(dataAddAttr);
        // 두 객체 병합 (additionalData가 우선순위)
        ajaxData = Object.assign({}, ajaxData, additionalData);
    }

    // 옵션 처리

    var processedOptions = parseWvAjaxOptions(optionAttr,$this);

    // 클릭 요소 정보 추가 (reload에서 사용)




    // target 기본값 설정 (원래 코드 로직 유지)
    if (!processedOptions.target) {
        processedOptions.target = (processedOptions.type === 'modal' || processedOptions.type === 'offcanvas') ? '#site-wrapper' : '';
    }

    // modal이나 offcanvas인 경우 중복 방지 처리
    if (processedOptions.type === 'modal' || processedOptions.type === 'offcanvas') {
        // ID 생성 (트리거 고유 기준)
        if (!processedOptions.id) {
            var triggerKey = url;
            processedOptions.id = (processedOptions.type === 'modal' ? 'wv-modal-' : 'wv-offcanvas-') +
                btoa(triggerKey).replace(/[^a-zA-Z0-9]/g, '').slice(0, 12) + $this.index();
        }

        // 해당 트리거에서 이전에 띄운 인스턴스가 있다면 제거
        var prevInstanceId = $this.data('wv-ajax-instance');
        if (prevInstanceId && $('#' + prevInstanceId).length) {
            return;
        }

        // 새로운 인스턴스 ID 저장
        $this.data('wv-ajax-instance', processedOptions.id);
    }


    // no_layout 추가 (기존 코드와 호환)
    ajaxData.no_layout = 1;

    // 실행
    if (processedOptions.type === 'modal') {
        wv_ajax_modal(url, processedOptions, ajaxData, true);
    } else if (processedOptions.type === 'offcanvas') {
        wv_ajax_offcanvas(url, processedOptions, ajaxData, true);
    } else {
        wv_ajax(url, processedOptions, ajaxData, true)
    }
});

function wv_ajax(url, options = {}, data = {}, isParsed = false){
    var processedOptions = isParsed ? options : parseWvAjaxOptions(options);

    // 일반 AJAX 요청
    var defaultAjaxSettings = {
        url: url,
        data: data,
        method: 'POST',
        success : function(response) {
            // target이 있으면 해당 엘리먼트에 결과 삽입 (원래 코드 로직)
            if (processedOptions.prepend) {
                $(processedOptions.prepend).prepend(response);
                return false;
            }
            if (processedOptions.append) {
                $(processedOptions.append).append(response);
                return false;
            }
            if (processedOptions.replace) {

                $(processedOptions.replace).html(response);
                return false;
            }
            if (processedOptions.replace_with) {

                $(processedOptions.replace_with).replaceWith(response);
                return false;
            }
            if (processedOptions.reload_ajax === 'parent') {
                // 즉시 부모 리로드
                wv_handle_parent_reload(processedOptions.$clickElement, false);
                return false;
            }
            if (processedOptions.reload_ajax === 'on_close') {
                // modal/offcanvas 닫힐 때 리로드하도록 마킹 (응답 처리는 계속)
                // 이 부분은 modal/offcanvas 생성 시점에서 처리
            }
        }

    };
    // if(processedOptions.prepend || processedOptions.append || processedOptions.replace || processedOptions.replace_with || processedOptions.reload){
    //     defaultAjaxSettings.success = function(response) {
    //         // target이 있으면 해당 엘리먼트에 결과 삽입 (원래 코드 로직)
    //         if (processedOptions.prepend) {
    //             $(processedOptions.prepend).prepend(response);
    //             return false;
    //         }
    //         if (processedOptions.append) {
    //             $(processedOptions.append).append(response);
    //             return false;
    //         }
    //         if (processedOptions.replace) {
    //
    //             $(processedOptions.replace).html(response);
    //             return false;
    //         }
    //         if (processedOptions.replace_with) {
    //             $(processedOptions.replace_with).replaceWith(response);
    //             return false;
    //         }
    //         if (processedOptions.reload_ajax) {
    //             wv_handle_reload(processedOptions.reload_ajax, processedOptions.$clickElement);
    //             return false;
    //         }
    //     }
    // }



    // ajax_option으로 기본값 오버라이딩
    if (processedOptions.ajax) {
        $.extend(defaultAjaxSettings, processedOptions.ajax);
        // ✅ 커스텀 속성들을 settings에 직접 설정
        if (processedOptions.ajax.reload !== undefined) {
            defaultAjaxSettings.reload = processedOptions.ajax.reload;
        }
        if (processedOptions.ajax.use_redirect !== undefined) {
            defaultAjaxSettings.use_redirect = processedOptions.ajax.use_redirect;
        }

    }
    if (processedOptions.reload_ajax) {
        defaultAjaxSettings.reload_ajax = processedOptions.reload_ajax;
    }

    $.ajax(defaultAjaxSettings);
}
// reload 처리 공통 함수
// function wv_handle_reload(reloadValue, $clickElement) {
//     var $target = null;
//
//     if (reloadValue === 'true') {
//         // 클릭 요소 기준으로 closest offcanvas 또는 modal 찾기
//         $target = $clickElement.closest('.offcanvas');
//         if (!$target.length) {
//             $target = $clickElement.closest('.modal');
//         }
//     } else {
//         // 셀렉터로 타겟 찾기
//         $target = $(reloadValue);
//     }
//
//     if (!$target.length) {
//         alert('Reload target not found:', reloadValue);
//         return false;
//     }
//
//     var targetId = $target.attr('id');
//     if (!targetId) {
//         alert('Target element has no ID for reload');
//         return false;
//     }
//
//     // offcanvas인지 modal인지 판단해서 적절한 reload 함수 호출
//     if ($target.hasClass('offcanvas')) {
//         $('#'+targetId).attr('data-need-refresh', 'true');
//         return wv_reload_offcanvas(targetId);
//     } else if ($target.hasClass('modal')) {
//         return wv_reload_modal(targetId);
//     } else {
//         console.warn('Target is neither offcanvas nor modal:', $target);
//         return false;
//     }
// }
function wv_ajax_modal(url, options = {}, data = {}, isParsed = false) {
    // 옵션 파싱
    var processedOptions = isParsed ? options : parseWvAjaxOptions(options);

    var $modal_target = processedOptions.target ? $(processedOptions.target) : $("#site-wrapper");
    var modal_id = processedOptions.id || '';
    var modal_class = processedOptions.class || '';
    var modal_options = {};
    var modal_data_attr = ['backdrop', 'keyboard', 'focus'];
    var modal_dialog_class = ['centered', 'scrollable'];
    var dialog_class = '';
    var other_options = processedOptions.other || [];

    // ID 자동 생성 (직접 호출시에만)
    if (!modal_id) {
        modal_id = 'wv-modal-' + btoa(url).replace(/[^a-zA-Z0-9]/g, '').slice(0, 12) + Date.now().toString().slice(-4);
    }

    // 옵션 처리
    other_options.forEach(function(opt) {
        if (modal_data_attr.includes(opt)) {
            modal_options[opt] = opt === 'backdrop' ? 'static' : true;
        } else if (modal_dialog_class.includes(opt)) {
            dialog_class += ' modal-dialog-' + opt;
        } else {
            dialog_class += ' modal-' + opt;
        }
    });

    var modalEl = $(`
        <div id="${modal_id}" class="modal wv-modal wv-modal-portal fade ${modal_class}">
            <div class="modal-dialog ${dialog_class}">
                <div class="modal-content"></div>
            </div>
        </div>
    `);

    var $parent = processedOptions.$clickElement.closest('.wv-modal');
    if($parent.length){
        modalEl.data('parent-elem',  $parent.attr('id'));
    }


    // 리로딩을 위한 정보 저장
    modalEl.attr('data-wv-reload-url', url);
    modalEl.data('wv-reload-options', processedOptions);
    modalEl.data('wv-reload-data', data);

    if (processedOptions.reload_ajax === 'on_close') {
        modalEl.attr('data-need-refresh', true);
    }
    modalEl.attr('data-wv-reload-count',0);


    $modal_target.append(modalEl);

    var modal = new bootstrap.Modal(modalEl[0], modal_options);
    modal.show();



    $(modalEl).on("hidden.bs.modal", function (e) {
        // 이벤트가 현재 요소에서 발생한 경우에만 제거
        if (e.target === this) {
            modalEl.remove();
        }
    });

    // AJAX 요청 설정
    var ajaxSettings = {
        url: url,
        method: "POST",
        data: data,
        success: function (html) {
            $(".modal-content", modalEl).html(html);
        },
        error: function () {
            modal.hide();
            modalEl.remove();
        }
    };

    // ajax 옵션으로 오버라이드
    if (processedOptions.ajax) {
        $.extend(ajaxSettings, processedOptions.ajax);
    }

    $.ajax(ajaxSettings);
}

function wv_ajax_offcanvas(url, options = {}, data = {}, isParsed = false) {
    // 옵션 파싱
    var processedOptions = isParsed ? options : parseWvAjaxOptions(options);

    var $offcanvas_target = processedOptions.target ? $(processedOptions.target) : $('#site-wrapper');
    var offcanvas_id = processedOptions.id || '';
    var offcanvas_class = processedOptions.class || '';
    var other_options = processedOptions.other || [];

    // ID 자동 생성 (직접 호출시에만)
    if (!offcanvas_id) {
        offcanvas_id = 'wv-offcanvas-' + btoa(url).replace(/[^a-zA-Z0-9]/g, '').slice(0, 12) + Date.now().toString().slice(-4);
    }

    var placement = 'offcanvas-start'; // 기본값
    var backdrop = true;
    var scroll = false;

    // 옵션 처리
    other_options.forEach(function(opt) {
        if (opt === 'end') placement = 'offcanvas-end';
        else if (opt === 'top') placement = 'offcanvas-top';
        else if (opt === 'bottom') placement = 'offcanvas-bottom';
        else if (opt === 'backdrop-static') backdrop = 'static';
        else if (opt === 'backdrop') backdrop = true;
        else if (opt === 'scroll') scroll = true;
    });

    var offcanvasEl = $(`
        <div id="${offcanvas_id}" class="offcanvas wv-offcanvas bg-white ${placement} ${offcanvas_class}" tabindex="-1">
            <div class="offcanvas-body"></div>
        </div>
    `);
    var $parent = processedOptions.$clickElement.closest('.wv-offcanvas');
    if($parent.length){
        offcanvasEl.attr('data-parent-elem',  $parent.attr('id'));
    }
    offcanvasEl.attr('data-wv-reload-url', url);
    offcanvasEl.data('wv-reload-options', processedOptions);
    offcanvasEl.data('wv-reload-data', data); // offcanvas가 아니라 offcanvasEl

    if (processedOptions.reload_ajax === 'on_close') {

        offcanvasEl.attr('data-need-refresh', true);
    }
    offcanvasEl.attr('data-wv-reload-count',0);

    $offcanvas_target.append(offcanvasEl);

    var offcanvas = new bootstrap.Offcanvas(offcanvasEl[0], {
        backdrop: backdrop,
        scroll: scroll
    });

    // 리로딩을 위한 정보 저장


    offcanvas.show();

    $(offcanvasEl).on("hidden.bs.offcanvas", function (e) {
        // 이벤트가 현재 요소에서 발생한 경우에만 제거
        if (e.target === this) {
            offcanvasEl.remove();
        }
    });

    // if(processedOptions.reload_ajax=='current'){
    //     var $current = processedOptions.$clickElement.closest('.wv-offcanvas');
    //     if($current.length){
    //         $current.attr('data-need-refresh', 'true');
    //         $.extend(data, {'reload_ajax_target':$current.attr('id')});
    //     }
    // }

    // AJAX 요청 설정
    var ajaxSettings = {
        url: url,
        method: 'POST',
        data: data,
        success: function (html) {

            $(".offcanvas-body", offcanvasEl).html(html);
        },
        error: function () {
            offcanvas.hide();
            offcanvasEl.remove();
        }
    };

    // ajax 옵션으로 오버라이드
    if (processedOptions.ajax) {
        $.extend(ajaxSettings, processedOptions.ajax);
    }

    $.ajax(ajaxSettings);
}

function wv_reload_offcanvas(offcanvasId) {
    var $offcanvas = $('#' + offcanvasId);
    if (!$offcanvas.length) return false;


    // 리로드 카운트 증가
    var currentCount = parseInt($offcanvas.attr('data-wv-reload-count') || '0');
    $offcanvas.attr('data-wv-reload-count', currentCount + 1);

    var url = $offcanvas.data('wv-reload-url');
    var options = $offcanvas.data('wv-reload-options');
    var data = $offcanvas.data('wv-reload-data');

    if (!url) return false;

    // 로딩 표시
    $offcanvas.find('.offcanvas-body').html('<div class="d-flex justify-content-center p-4"><div class="spinner-border" role="status"></div></div>');

    // AJAX 요청
    var ajaxSettings = {
        url: url,
        method: "POST",
        data: data,
        success: function (html) {
            $offcanvas.find('.offcanvas-body').html(html);
        },
        error: function () {
            $offcanvas.find('.offcanvas-body').html('<div class="alert alert-danger m-3">로딩 중 오류가 발생했습니다.</div>');
        }
    };

    if (options && options.ajax) {
        $.extend(ajaxSettings, options.ajax);
    }

    $.ajax(ajaxSettings);

    return true;
}

// 현재 활성화된 offcanvas 리로딩
function wv_reload_current_offcanvas() {
    var $activeOffcanvas = $('.offcanvas.show').last();
    if ($activeOffcanvas.length && $activeOffcanvas.attr('id')) {
        return wv_reload_offcanvas($activeOffcanvas.attr('id'));
    }
    return false;
}
function wv_reload_modal(modalId) {
    var $modal = $('#' + modalId);
    if (!$modal.length) return false;

    var currentCount = parseInt($modal.attr('data-wv-reload-count') || '0');
    $modal.attr('data-wv-reload-count', currentCount + 1);

    var url = $modal.data('wv-reload-url');
    var options = $modal.data('wv-reload-options');
    var data = $modal.data('wv-reload-data');

    if (!url) return false;

    // 로딩 표시
    $modal.find('.modal-content').html('<div class="d-flex justify-content-center p-4"><div class="spinner-border" role="status"></div></div>');

    // AJAX 요청
    var ajaxSettings = {
        url: url,
        method: "POST",
        data: data,
        success: function (html) {
            $modal.find('.modal-content').html(html);
        },
        error: function () {
            $modal.find('.modal-content').html('<div class="alert alert-danger m-3">로딩 중 오류가 발생했습니다.</div>');
        }
    };

    if (options && options.ajax) {
        $.extend(ajaxSettings, options.ajax);
    }

    $.ajax(ajaxSettings);
    return true;
}

// 현재 활성화된 modal 리로딩
function wv_reload_current_modal() {
    var $activeModal = $('.modal.show').last();
    if ($activeModal.length && $activeModal.attr('id')) {
        return wv_reload_modal($activeModal.attr('id'));
    }
    return false;
}