function isset(val) {
    return typeof val !== "undefined" && val !== null;
}
function wv_rand_name() {
    let text = "";
    let first = "김이박최정강조윤장임한오서신권황안송류전홍고문양손배조백허유남심노정하곽성차주우구신임나전민유진지엄채원천방공강현함변염양변여추노도소신석선설마주연방위표명기반왕모장남탁국여진구";
    let last = "가강건경고관광구규근기길나남노누다단달담대덕도동두라래로루리마만명무문미민바박백범별병보사산상새서석선설섭성세소솔수숙순숭슬승시신아안애엄여연영예오옥완요용우원월위유윤율으은의이익인일자잔장재전정제조종주준중지진찬창채천철초춘충치탐태택판하한해혁현형혜호홍화환회효훈휘희운모배부림봉혼황량린을비솜공면탁온디항후려균묵송욱휴언들견추걸삼열웅분변양출타흥겸곤번식란더손술반빈실직악람권복심헌엽학개평늘랑향울련";

    for (var i = 0; i < 1; i++)
        text += first.charAt(Math.floor(Math.random() * first.length));
    for (var i = 0; i < 2; i++)
        text += last.charAt(Math.floor(Math.random() * last.length));

    return text;
}

function wv_rand_num(min, max) {
    var min = min || 0,
        max = max || Number.MAX_SAFE_INTEGER;

    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function wv_rand_str(min, max) {
    const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    let result = '';
    const charactersLength = characters.length;
    for (let i = 0; i < num; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    return result;
}

function wv_debounce(callback, limit = 100) {
    let timeout
    return function(...args) {
        clearTimeout(timeout)
        timeout = setTimeout(() => {
            callback.apply(this, args)
        }, limit)
    }
}

function wv_offset_check(){

    $(".wv-ms-contain,.wv-me-contain,.wv-ms-cover,.wv-me-cover").each(function (i,e) {

        var $elem = $(e);

        if($elem.is('.wv-ms-contain, .wv-ms-cover')){

            $elem[0].style.setProperty('float', 'right');
            if(!$elem.next().is('.clearfix')){
                $('<div class="clearfix"></div>').insertAfter($elem);
            }
        }
        if($elem.is('.wv-me-cover, .wv-ms-cover')){

            $elem[0].style.setProperty('min-width','100%');
            $elem[0].style.setProperty('max-width','100%');
        }


        var $parent = $elem.parent();
        // $parent[0].style.setProperty('position', 'relative');
        var pos = $parent.offset();
        var pos_left = pos.left+'px';
        var org_width = $elem.outerWidth()+'px';
        $elem[0].style.removeProperty('min-width');
        $elem[0].style.removeProperty('max-width');

        $elem[0].style.setProperty('--wv-offset-left', pos_left);
        $elem[0].style.setProperty('--wv-org-width', org_width);

    })

}

// $(document).on('change','.pdf-upload',function () {
//     var $file_elem = $(this);
//     var de_id = $file_elem.data('de-id');
//     var student_id = $file_elem.data('student-id');
//     var file_input = $file_elem[0];
//     var formData = new FormData();
//     var $td = $file_elem.closest('td');
//     var file_size_sum = 0;
//     for(var i=0;i<file_input.files.length;i++){
//         file_size_sum+=file_input.files[i].size;
//         formData.append("bf_file["+i+"]", file_input.files[i]);
//     }
//     formData.append('de_id',de_id);
//     formData.append('student_id',student_id);
//
//     if(file_size_sum > max_upload_size){
//         alert("최대파일사이즈는 "+max_upload_size_text+' 입니다.');
//         $file_elem.val('')
//         return false;
//     }
//
//     $.ajax({
//         type:"POST",
//         url: g5_admin_url+'/pdf_upload.php',
//         processData: false,
//         contentType: false,
//         data: formData,
//     })
// })

$.fn.loaded = function(selector, callback){
    var $root = this;

    if (typeof selector !== 'string' || !selector) return $root;
    if (typeof callback !== 'function') return $root;

    // mark helper: store a per-selector flag on the element
    function markOnce(el){
        var $el = $(el);
        var bag = $el.data('__wv_loaded__');
        if (!bag) { bag = {}; $el.data('__wv_loaded__', bag); }
        if (bag[selector]) return false;     // already initialized for this selector
        bag[selector] = true;
        return true;
    }

    // run callback only once per element+selector
    function run(el, idx){
        if (markOnce(el)) {
            callback.call(el, idx, el);
        }
    }

    // initialize existing matches under root
    $root.find(selector).each(function(i, el){ run(el, i); });

    // attach a single observer per root (reused across multiple .loaded calls)
    var obs = $root.data('__wv_loaded_observer__');
    if (!obs) {
        obs = new MutationObserver(function(mutations){
            for (var m=0; m<mutations.length; m++){
                var rec = mutations[m];
                // only process added element nodes
                for (var n=0; n<rec.addedNodes.length; n++){
                    var node = rec.addedNodes[n];
                    if (node.nodeType !== 1) continue; // ELEMENT_NODE only

                    var $node = $(node);
                    // if the node itself matches
                    if ($node.is(selector)) run(node, 0);
                    // descendants that match
                    $node.find(selector).each(function(i, el){ run(el, i); });
                }
            }
        });

        // observe once per root
        var rootNode = $root[0];
        if (rootNode) {
            obs.observe(rootNode, { childList: true, subtree: true });
            $root.data('__wv_loaded_observer__', obs);
        }
    }

    return $root;
};

// (선택) destroy helper
$.fn.unloaded = function(){
    return this.each(function(){
        var $root = $(this);
        var obs = $root.data('__wv_loaded_observer__');
        if (obs) { obs.disconnect(); $root.removeData('__wv_loaded_observer__'); }
    });
};

$.fn.board_list_ajax = function(){
    var $wrap = $(this);
    var ajax_url = $wrap.data('ajax-url');

    var page = parseInt($wrap.data('page'));
    var param = JSON.parse(Base64.decode( $wrap.data('param')));
    var sort = $wrap.data('sort');
    var list_selector = $wrap.data('list-selector');
    $wrap.data("page",page+1);

    if(sort=='wr_datetime'){
        sort='';
    }



    $.post(ajax_url,{param:param,page:page+1,list_selector:list_selector,sst:sort},function (data){
        $wrap.append(data.html.list_html)
        if($wrap.parent().is('.swiper-slide')){
            swiper2.updateAutoHeight(1000)
        }

    },'json')
};


$(function(){

    wv_offset_check();


    $(window).on('resize',wv_debounce(wv_offset_check,100))




    // function viewport_calc() {
    //     var scale = (window.screen.width/1920).toFixed(3);
    //     if(scale>1){
    //
    //     }
    //     var content = 'width=1920, initial-scale='+scale+', maximum-scale='+scale+', minimum-scale='+scale+', user-scalable=no';
    //     $("meta[name='viewport']").attr('content',content)
    // }
    // $(window).load(function () {
    //     viewport_calc();
    // })

    $('<span class="br"></span>').insertAfter($(".br-remove br:not(.n)"))
    $('<span class="br"></span>').insertAfter($(".br-md-remove br:not(.n):not(:has(+ span.br))"))
    $('<span class="br"></span>').insertAfter($(".br-sm-remove br:not(.n):not(:has(+ span.br))"))
    $('<span class="br"></span>').insertAfter($(".br-xs-remove br:not(.n):not(:has(+ span.br))"))

    $(".wv-last-letter-remove").each(function () {
        var $this = $(this);
        var text = $this.text();
        $this.html(text.slice(0,text.length-1)+'<span class="wv-last-letter-wrap">'+text.slice(-1)+'</span>')
    })
})

// extend
$(document).ready(function () {
    if($("#site-wrapper").length){
        function site_wrapper_chk() {
            var site_wrapper = $("#site-wrapper")[0];
            var info = site_wrapper.getBoundingClientRect()

            site_wrapper.style.setProperty("--wv-site-width", info.width+'px');
            site_wrapper.style.setProperty("--wv-site-left", info.left+'px');
            site_wrapper.style.setProperty("--wv-site-right", (window.innerWidth - info.right)+'px');

        }
        site_wrapper_chk();
        $(window).on('resize',wv_debounce( site_wrapper_chk,50))
    }

    $("[class*='wv-hamburger']").click(function(){
        $(this).toggleClass("is-active");
    });

// 전체 체크박스 클릭 이벤트 (패턴: .wv-check-{suffix})
    $(document).on('click', '[class*="wv-check-"]:not([class*="wv-check-"][class*="-list"])', function () {
        var $this = $(this);
        var $form = $this.closest('form');
        var all_checked = $this.is(":checked");

        // 클래스명에서 suffix 추출
        var className = $this.attr('class');
        var matches = className.match(/wv-check-([^-\s]+)(?:\s|$)/);

        if (matches && matches[1]) {
            var suffix = matches[1];
            var listClass = '.wv-check-' + suffix + '-list';

            // 해당 패턴의 개별 체크박스들 체크/해제
            $(listClass, $form).each(function (i, e) {
                $(e).attr('checked', all_checked);
            });
        }
    });

// 개별 체크박스 클릭 이벤트 (패턴: .wv-check-{suffix}-list)
    $(document).on('click', '[class*="wv-check-"][class*="-list"]', function () {
        var $this = $(this);
        var $form = $this.closest('form');

        // 클래스명에서 suffix 추출
        var className = $this.attr('class');
        var matches = className.match(/wv-check-([^-\s]+)-list/);

        if (matches && matches[1]) {
            var suffix = matches[1];
            var allCheckClass = '.wv-check-' + suffix;
            var listClass = '.wv-check-' + suffix + '-list';
            var $all_check = $(allCheckClass, $form);

            // 전체 개수와 체크된 개수 비교
            var total_count = $(listClass, $form).length;
            var checked_count = $(listClass + ':checked', $form).length;

            // 모든 개별 체크박스가 체크되어 있을 때만 전체 체크박스 체크
            var all_check_change = (total_count > 0 && total_count === checked_count) ? true : false;
            $all_check.attr('checked', all_check_change);
        }
    });

    $(document).on('input','.form-floating textarea',function () {
        var $this = $(this);
        if(!this.org_h){
            this.org_h = $this.outerHeight();
        }

        if(this.scrollHeight<this.org_h){
            return true;
        }

        $this[0].style.height = 'auto';
        $this[0].style.height = (this.scrollHeight) + 'px';
    })

    $("form").loaded('#wr_content',function () {
        var $this= $(this);
        var $parent = $this.closest('.wr_content');

        var $label = $parent.prev('label');
        if($label.length==0){
            $label = $parent.next('label');
        }
        if($label.length){
            if(!$this.prop('placeholder')){
                $this.prop('placeholder',' ')
            }

            $this.addClass('form-control');
            $parent.addClass('form-floating');
            $parent.append($label.removeClass('sr-only').addClass('floatingInput').detach())

        }
    })

    $(document).on('input','input.wv-only-number',function (e) {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    })
    $(document).on('keypress','input.wv-only-number',function (e) {
        if(  e.key >= 0 && e.key <= 9) {
            return true;
        }
        return false;
    })

    /**
     * wv-password-toggle 클래스 기반 비밀번호 토글 기능
     * loaded() 함수 사용, form-floating 환경 완벽 지원
     */
    $("body").loaded('.wv-password-toggle', function() {
        const $input = $(this);

        // 이미 초기화된 경우 스킵
        if ($input.hasClass('wv-password-toggle-initialized')) {
            return;
        }

        // 초기화 마크
        $input.addClass('wv-password-toggle-initialized');

        // form-floating 환경 체크
        const $formFloating = $input.closest('.form-floating');
        const isFormFloating = $formFloating.length > 0;

        let $toggleIcon;

        if (isFormFloating) {
            // form-floating 환경에서는 wrapper 없이 직접 form-floating에 추가
            $formFloating.addClass('wv-password-wrapper-floating');
            $toggleIcon = $('<i class="wv-password-toggle-icon fa-solid fa-eye-slash"></i>');
            $formFloating.append($toggleIcon);
        } else {
            // 일반 환경에서는 wrapper 사용
            const $wrapper = $('<div class="wv-password-wrapper"></div>');
            $input.wrap($wrapper);
            $toggleIcon = $('<i class="wv-password-toggle-icon fa-solid fa-eye-slash"></i>');
            $input.after($toggleIcon);
        }

        // 토글 아이콘 클릭 이벤트
        $toggleIcon.click(function() {
            const $icon = $(this);

            // form-floating 환경에서는 형제가 아닌 부모 안에서 찾기
            const $targetInput = isFormFloating
                ? $icon.parent().find('input.wv-password-toggle')
                : $icon.siblings('input.wv-password-toggle');

            if ($targetInput.length === 0) return;

            const isPassword = $targetInput.attr('type') === 'password';

            if (isPassword) {
                // 비밀번호 보이기
                $targetInput.attr('type', 'text');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                // 비밀번호 숨기기
                $targetInput.attr('type', 'password');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }

            // 포커스 유지 및 커서 끝으로 이동
            $targetInput.focus();
            const input = $targetInput[0];
            const length = input.value.length;
            input.setSelectionRange(length, length);
        });
    });
    $(document).loaded('.wv-form-check', function() {
        const $skin = $(this);
        var $btn = $("[type=submit]", $skin);
        var $requiredFields = $("input[required], textarea[required], select[required]", $skin);
        var isCheckingScheduled = false;

        function isFieldValid(field) {
            var type = field.type;
            var value = field.value;

            // checkbox, radio는 checked 확인
            if (type === 'checkbox' || type === 'radio') {
                return field.checked;
            }

            // 나머지는 값 확인
            return value && value.trim() !== '' && value !== '0';
        }

        function scheduleCheck() {
            if (isCheckingScheduled) return;

            isCheckingScheduled = true;
            requestAnimationFrame(function() {
                var allValid = Array.prototype.every.call($requiredFields, isFieldValid);
                $btn.toggleClass("active", allValid);
                isCheckingScheduled = false;
            });
        }

        $requiredFields.on("input change", scheduleCheck);
        scheduleCheck();
    });

})




$(".smarteditor2 iframe").ready( function() {

    var find_iframe =  setInterval(function () {
        let head = $(".smarteditor2>iframe").contents().find("head");
        if($(head).length && $(head).html()){
            let css =
                '<style>' +
                '#smart_editor2{min-width:unset!important;}' +
                '.se2_text_tool{display: flex; flex-wrap: wrap; gap: .3em; row-gap: .3em; align-items: center;}' +
                '#smart_editor2 .se2_text_tool .se2_font_type li:first-child{margin-left: 0!important;}' +

                '#smart_editor2 .se2_text_tool>*{order:2;}' +
                '#smart_editor2 .se2_text_tool >ul:has(.husky_seditor_ui_text_more){order:1;}' +
                '#smart_editor2 .se2_text_tool >ul.se2_multy{border:0!important;}' +
                '#smart_editor2 .se2_text_tool .se2_multy{position: relative}' +
                '</style>';
            $(head).append(css);
            clearInterval(find_iframe);

        }

    }, 100);

});

window.addEventListener('load', () => {
    document.querySelectorAll('img').forEach(img => {
        img.onerror = function () {
            if(img.src.includes('/maxresdefault')){
                img.src = img.src.replace('/maxresdefault', '/sddefault');
            }
        };
        // 이미지가 이미 실패 상태인 경우 수동으로 처리
        if (img.complete && img.naturalWidth <= 120) {
            img.onerror();
        }
    });
});