
<span id="wv-ceo-init-current">
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



            window.rawAjax({
                url: '<?php echo wv()->store_manager->ajax_url(); ?>',
                type: 'POST',
                data: {'action':'get_current_store'},
                async: false, // 기본값 true (비동기)
                success: function () {

                    window.ajaxGate.resolve();
                    $("#wv-ceo-init-current").remove()

                }
            });

        })

    </script>
</span>
