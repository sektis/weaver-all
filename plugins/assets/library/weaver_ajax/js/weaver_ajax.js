$(function(){
    if($.fn.ajaxForm && $.fn.ajaxSubmit) {

        // ajaxForm 오버라이드
        var originalAjaxForm = $.fn.ajaxForm;
        $.fn.ajaxForm = function(options) {
            options = options || {};

            var originalBeforeSubmit = options.beforeSubmit;
            options.beforeSubmit = function(formData, jqForm, opts) {
                // 빈 파일 input 제거 (전역 처리)
                for (var i = formData.length - 1; i >= 0; i--) {
                    var item = formData[i];

                    if (item.type === 'file' && (!item.value || item.value === '')) {
                        formData.splice(i, 1);
                    }
                }

                // 기존 beforeSubmit 실행
                if (originalBeforeSubmit && typeof originalBeforeSubmit === 'function') {
                    return originalBeforeSubmit.call(this, formData, jqForm, opts);
                }

                return true;
            };

            return originalAjaxForm.call(this, options);
        };

        // ajaxSubmit 오버라이드
        var originalAjaxSubmit = $.fn.ajaxSubmit;
        $.fn.ajaxSubmit = function(options) {
            options = options || {};

            var originalBeforeSubmit = options.beforeSubmit;
            options.beforeSubmit = function(formData, jqForm, opts) {
                // 빈 파일 input 제거 (전역 처리)
                for (var i = formData.length - 1; i >= 0; i--) {
                    var item = formData[i];

                    if (item.type === 'file' && (!item.value || item.value === '')) {
                        formData.splice(i, 1);
                    }
                }

                // 기존 beforeSubmit 실행
                if (originalBeforeSubmit && typeof originalBeforeSubmit === 'function') {
                    return originalBeforeSubmit.call(this, formData, jqForm, opts);
                }

                return true;
            };

            return originalAjaxSubmit.call(this, options);
        };
    }

    $(document).ajaxError(function(event, jqxhr, settings){
        var msg,url;
        var code = jqxhr.status;
        var title_nodes = $($.parseHTML(jqxhr.responseText)).filter('title').text();
        var base64regex = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/;
        var hangulregex = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/;


        if(isset(jqxhr.responseJSON) && isset(jqxhr.responseJSON.confirm)){
            if(confirm(jqxhr.responseJSON.confirm)){

                var original_data;

                if(typeof settings.data == 'object'){
                    // FormData인 경우
                    original_data = new FormData();
                    for (var pair of settings.data.entries()) {
                        original_data.append(pair[0], pair[1]);
                    }
                    // confirm_data 추가
                    if(jqxhr.responseJSON.confirm_data){
                        for(var key in jqxhr.responseJSON.confirm_data){
                            console.log(key,jqxhr.responseJSON.confirm_data[key])
                            original_data.append(key, jqxhr.responseJSON.confirm_data[key]);
                        }
                    }
                } else if(typeof settings.data == 'string'){
                    // 문자열 데이터인 경우
                    original_data = settings.data;
                    if(jqxhr.responseJSON.confirm_data){
                        var confirm_params = new URLSearchParams();
                        for(var key in jqxhr.responseJSON.confirm_data){
                            confirm_params.append(key, jqxhr.responseJSON.confirm_data[key]);
                        }
                        original_data += '&' + confirm_params.toString();
                    }
                } else {
                    // data가 없는 경우
                    if(jqxhr.responseJSON.confirm_data){
                        original_data = new FormData();
                        for(var key in jqxhr.responseJSON.confirm_data){
                            original_data.append(key, jqxhr.responseJSON.confirm_data[key]);
                        }
                    }
                }

                // 원래 Ajax 요청을 다시 실행
                $.ajax({
                    url: settings.url,
                    type: settings.type || 'GET',
                    data: original_data,
                    processData: settings.processData,
                    contentType: settings.contentType,
                    cache: false,
                    dataType: settings.dataType,
                    headers: settings.headers,

                    success: settings.success,
                    error: settings.error,
                    complete: settings.complete
                });
            }
            return true;
        }


        if(!title_nodes){
            title_nodes = jqxhr.responseText;
        }

        if(!title_nodes){
            title_nodes = jqxhr.statusText;
        }

        title_nodes = title_nodes.replace(code+' ','').replace(/(<([^>]+)>)/ig,"");



        if(base64regex.test(title_nodes) && hangulregex.test(title_nodes)){
            title_nodes = Base64.decode(title_nodes);
        }

        var splits = title_nodes.split('@@@');
        msg = splits[0];
        url = splits[1];



        if(settings.use_redirect==true && url){
            const current_url =  window.location.href.split('?')[0].replace(/\/+$/,"");
            if(url !== current_url){
                location.href=url;
            }
        }

        alert(msg);

    });
    var $loader, timer;
    if($('#wv-ajax-loading').length){
        $loader = $('#wv-ajax-loading');
    }else{
        var loader_html = '<div id="wv-ajax-loading" class="position-fixed wh-100 text-white start-0 top-0 " style="display:none;font-size:3rem;z-index: 9999;background-color: rgba(0,0,0,0.5); "><div class="d-flex align-items-center justify-content-center wh-100"> <span class="spinner-border spinner-border me-3" style="--bs-spinner-border-width: 0.9em;"></span><span class="wv-ajax-status" role="status">Loading...</span> </div></div>';
        $('body').append(loader_html);
        $loader = $('#wv-ajax-loading');
    }



    $(document).ajaxSend(function(event, xhr, settings) {
        timer && clearTimeout(timer);
        timer = setTimeout(function()
            {
                $loader.show();
            },
            500);
        settings.use_redirect = false;
        var _orgAjax = settings.xhr;
        settings.xhr = function () {
            settings.wv_xhr = _orgAjax();
            settings.wv_xhr.upload.onprogress = function(e) {  //progress 이벤트 리스너 추가
                var percent =  Math.floor(e.loaded/e.total*100)+'%';

                $(".wv-ajax-status",$loader).text(percent);
            };
            return settings.wv_xhr;
        };
        if(typeof settings.data == 'object'){
            if(settings.data.get('use_redirect')){
                settings.use_redirect = true;
            }
        }else if(typeof settings.data == 'string'){
            let params = new URLSearchParams(settings.data);
            if(params.get("use_redirect")){
                settings.use_redirect = true;
            }
        }

        return true;
    });

    $(document).ajaxComplete(function(event, xhr, settings) {
        clearTimeout(timer);

        $loader.hide();
        var reload = false;


        try {
            var parsed_json = JSON.parse(xhr.responseText);

            if( isset(parsed_json.reload) && parsed_json.reload==true){
                reload = true;
            }
        } catch (e) {

        }

        if(reload==false){
            if(isset(settings.reload) && settings.reload===true ){
                reload=true;
            }
        }

        if(isset(settings.reload_ajax )){

            if(settings.reload_ajax===true){
                // ajax날라올때
            }else{
                // 타겟
            }

        }

        console.log(reload)

        if(msg){
            alert(msg);
        }
        if(reload){
            // location.reload()
        }



        if(settings.use_redirect==true && org_url!=last_url){
            var org_url = settings.url.replace(/\/+$/,"").replace(g5_url,'')
            var last_url = settings.wv_xhr.responseURL.replace(/\/+$/,"").replace(g5_url,'');
            location.replace(last_url)
        }
    });

})