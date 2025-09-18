// bootstrap5
$(document).ready(function () {

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
    function getScrollbarWidth() {
        const scrollDiv = document.createElement('div')
        scrollDiv.style.position = 'absolute'
        scrollDiv.style.top = '-9999px'
        scrollDiv.style.width = '50px'
        scrollDiv.style.height = '50px'
        scrollDiv.style.overflow = 'scroll'
        document.body.appendChild(scrollDiv)

        const scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
        document.body.removeChild(scrollDiv)
        return scrollbarWidth
    }
    const getLiveScrollbarWidth = () =>
        Math.max(0, window.innerWidth - document.documentElement.clientWidth);
    document.addEventListener('show.bs.offcanvas', function (e) {
        const offcanvasEl = e.target
        const scrollbarWidth = getLiveScrollbarWidth()

        // shown 시점이 아니라 show 시점에 미리 패딩 부여
        var move_x = scrollbarWidth/2*-1;
        $(offcanvasEl).find('.offcanvas-body').css('transform',`translateX(${move_x}px)`);
        // offcanvasEl.style.paddingRight = `${scrollbarWidth}px`
    })


})
// 옵션 파싱 공통 함수
function parseWvAjaxOptions(options) {
    var processedOptions = {};

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
                alert(1)
                processedOptions.replace = option.substring(8).trim();
            }
            else if (option.indexOf('replace_with:') === 0) {
                processedOptions.replace_with = option.substring(13).trim();
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
    var type = '';
    var processedOptions = parseWvAjaxOptions(optionAttr);

    // type 찾기 (processedOptions.other에서 추출)
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

    // target 기본값 설정 (원래 코드 로직 유지)
    if (!processedOptions.target) {
        processedOptions.target = (type === 'modal' || type === 'offcanvas') ? '#site-wrapper' : '';
    }

    // modal이나 offcanvas인 경우 중복 방지 처리
    if (type === 'modal' || type === 'offcanvas') {
        // ID 생성 (트리거 고유 기준)
        if (!processedOptions.id) {
            var triggerKey = url;
            processedOptions.id = (type === 'modal' ? 'wv-modal-' : 'wv-offcanvas-') +
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
    if (type === 'modal') {
        wv_ajax_modal(url, processedOptions, ajaxData, true);
    } else if (type === 'offcanvas') {
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
        success: function(response) {
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
            } //
            if (processedOptions.replace_with) {

                $(processedOptions.replace_with).replaceWith(response);
                return false;
            } // console.log('AJAX 성공:', response);
        }
    };

    // ajax_option으로 기본값 오버라이딩
    if (processedOptions.ajax) {
        $.extend(defaultAjaxSettings, processedOptions.ajax);
    }

    $.ajax(defaultAjaxSettings);
}

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

    $modal_target.append(modalEl);

    var modal = new bootstrap.Modal(modalEl[0], modal_options);
    modal.show();

    $(modalEl).on("hidden.bs.modal", function () {
        modalEl.remove();
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

    $offcanvas_target.append(offcanvasEl);

    var offcanvas = new bootstrap.Offcanvas(offcanvasEl[0], {
        backdrop: backdrop,
        scroll: scroll
    });
    offcanvas.show();

    $(offcanvasEl).on("hidden.bs.offcanvas", function () {
        offcanvasEl.remove();
    });

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