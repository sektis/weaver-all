<?php add_javascript('<script src="'.wv_path_replace_url(dirname(__FILE__)).'/js/weaver_location.js"></script>'); ?>
<script>

    $(document).ready(function () {
            wv_location_init('<?php echo $this->kakao_js_apikey?>');
    })

</script>