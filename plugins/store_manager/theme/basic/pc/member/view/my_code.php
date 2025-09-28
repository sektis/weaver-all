<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_store_wr_id;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  w-full    " style=" ">

        <div class="hstack mt-[12px] justify-content-between">
            <p class="fs-[20/28/-0.8/600/#0D171B]"><?php echo $row['invite_code']; ?></p>
            <a href="#"     class="hstack copy_code" style="gap:var(--wv-3)" data-code="<?php echo $row['invite_code']; ?>">
                <img src="<?php echo wv()->store_manager->plugin_url; ?>/img/copy_gray.png" class="w-[14px]" alt="">
                <span class="fs-[12/17/-0.48/600/#97989C]">내 코드 복사하기</span>
            </a>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");

            $(".copy_code",$skin).click(function (e) {
                e.preventDefault()
                copy_invite_code($(this).data('code'));
            })
            function copy_invite_code(code) {
                wv_copy_to_clipboard(code, {
                    success_message: '초대코드가 복사되었습니다!',
                    error_message: '복사에 실패했습니다.',
                    success_callback: function() {
                        console.log('복사 성공!');
                    }
                });
            }
        });


    </script>
</div>