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

        if (el.parentElement === document.body) {
            document.body.removeChild(el);
        }
        if (originalParent && placeholder) {
            originalParent.replaceChild(el, placeholder);
        }
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

    $(document).on('click', '[data-wv-ajax-url]', function (e) {
        e.preventDefault();

        const $el = $(this);
        const url = $el.data('wv-ajax-url');
        const type = $el.data('wv-ajax-type') || 'offcanvas';
        const optionsStr = $el.data('wv-ajax-options') || '';
        const options = optionsStr.split(',').map(opt => opt.trim()).filter(opt => opt);

        // ID는 명시적으로 지정되거나, 자동 생성됨 (트리거 고유 기준)
        let id = $el.data('wv-ajax-id');


        if (!id) {
            // 트리거 자체를 기반으로 고유 ID 생성
            const triggerKey = url;
            id = (type === 'modal' ? 'wv-modal-' : 'wv-offcanvas-') +
                btoa(triggerKey).replace(/[^a-zA-Z0-9]/g, '').slice(0, 12)+$el.index();
        }

        const selector = {
            id: id,
            class: $el.data('wv-ajax-class') || '',
            target: $el.data('wv-ajax-target') || ((type=='modal' || type=='offcanvse')?'#site-wrapper':'')
        };


        // ajaxData 구성
        const ajaxData = {};
        $.each(this.attributes, function () {
            if (!this.name.startsWith('data-wv-ajax-')) return;
            const raw = this.name.slice('data-wv-ajax-'.length);
            if (['url', 'type', 'options', 'id', 'class', 'target'].includes(raw)) return;
            const key = raw.replace(/-/g, '_');
            ajaxData[key] = this.value;
        });

        // 해당 트리거에서 이전에 띄운 인스턴스가 있다면 제거
        const prevInstanceId = $el.data('wv-ajax-instance');
        if (prevInstanceId && $(`#${prevInstanceId}`).length) {
            return;
        }

        // 새로운 인스턴스 ID 저장
        $el.attr('data-wv-ajax-instance', id);

        ajaxData['no_layout']=1;

        // 실행
        if (type === 'modal') {
            wv_ajax_modal(url, options, selector, ajaxData);
        } else if (type === 'offcanvas') {
            wv_ajax_offcanvas(url, options, selector, ajaxData);
        } else {
            $.ajax({
                url: url,
                method: 'POST',
                data: ajaxData,
                success: function (res) {
                    if (selector.target) {
                        $(selector.target).html(res);
                    }
                }
            });
        }
    });

})





function wv_ajax_modal(url,options=[],selector={},ajax_data={}){
    const $modal_target = selector.target?$(selector.target):$("#site-wrapper");
    const modal_id = selector.id || '';
    const modal_class = selector.class || '';
    const modal_options = {};
    const modal_data_attr = ['backdrop', 'keyboard', 'focus'];
    const modal_dialog_class = ['centered', 'scrollable'];
    var dialog_class='';

    options.forEach(opt => {
        if (modal_data_attr.includes(opt)) {
            modal_options[opt] = options[opt] ?? (opt==='backdrop'?'static':true);
        } else {
            dialog_class += modal_dialog_class.includes(opt)?` modal-dialog-${opt}`:` modal-${opt}`;
        }
    });

    const modalEl = $(`
                <div id="${modal_id}" class="modal wv-modal fade ${modal_class}"   >
                    <div class="modal-dialog ${dialog_class}">
                        <div class="modal-content"></div>
                    </div>
                </div>
            `);

    $modal_target.append(modalEl);

    const modal = new bootstrap.Modal(modalEl[0],modal_options);
    modal.show();

    $(modalEl).on("hidden.bs.modal", function () {
        modalEl.remove();
    });

    $.ajax({
        url: url,
        method: "POST",
        data:ajax_data,
        success: function (html) {
            $(".modal-content",modalEl).html(html)
        },
        error:function () {
            modal.hide();
            modalEl.remove();
        }
    });
}

function wv_ajax_offcanvas(url, options = [], selector = {}, ajax_data = {}) {
    const $offcanvas_target = selector.target ? $(selector.target) : $('#site-wrapper');
    const offcanvas_id = selector.id || '';
    const offcanvas_class = selector.class || '';
    const placement = options.includes('end') ? 'offcanvas-end'
        : options.includes('top') ? 'offcanvas-top'
            : options.includes('bottom') ? 'offcanvas-bottom'
                : 'offcanvas-start'; // 기본은 왼쪽

    const backdrop = options.includes('backdrop-static') ? 'static' : (options.includes('backdrop') ? true : false);
    const scroll = options.includes('scroll') ? true : false;

    const offcanvasEl = $(`
                <div id="${offcanvas_id}" class="offcanvas wv-offcanvas bg-white ${placement} ${offcanvas_class}" tabindex="-1">
                
                    <div class="offcanvas-body">
                         
                    </div>
                </div>
            `);

    $offcanvas_target.append(offcanvasEl);

    const offcanvas = new bootstrap.Offcanvas(offcanvasEl[0], {
        backdrop: backdrop,
        scroll: scroll
    });
    offcanvas.show();


    $(offcanvasEl).on("hidden.bs.offcanvas", function () {
        offcanvasEl.remove();
    });

    $.ajax({
        url: url,
        method: 'POST',
        data: ajax_data,
        success: function (html) {

            $(".offcanvas-body",offcanvasEl).html(html)
        },
        error:function () {
            offcanvas.hide();
            offcanvasEl.remove();
        }
    });
}