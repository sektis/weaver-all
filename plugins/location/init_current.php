
<span id="wv-location-init-current">
    <script>
        window.rawAjax = $.ajax;
        window.ajaxGate = $.Deferred();

        // 2) $.ajax를 얇게 감싸서 게이트가 열릴 때까지 대기
        (function(){
            var _ajax = $.ajax;
            $.ajax = function() {
                var args = arguments;
                return $.Deferred(function(dfd){
                    window.ajaxGate.then(function(){
                        _ajax.apply($, args).then(dfd.resolve, dfd.reject);
                    });
                }).promise();
            };
        })();
        $(document).ready(function () {



            wv_get_current_location(function (result) {


console.log(result)
                window.rawAjax({
                    url: '<?php echo wv()->location->ajax_url; ?>',
                    type: 'POST',
                    data: {'wv_location_action':'set','wv_location_name':'current','wv_location_data':result},
                    async: false, // 기본값 true (비동기)
                    success: function () {
                        window.ajaxGate.resolve();
                        $("#wv-location-init-current").remove()

                    }
                });
            })


        })

    </script>
</span>
