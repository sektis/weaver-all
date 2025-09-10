<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<div class="register-from-wrap">

</div>
<script>
    $(document).ready(function () {
        $.post(g5_bbs_url+'/register_form.php',{agree:1,agree2:1,no_layout:true},function (data) {
            $(".register-from-wrap").html(data);
        },'html')
    })
</script>