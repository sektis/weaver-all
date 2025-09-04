<?php
$page_title='타이틀';
?>

<div class="wv-vstack">
    <div class="hstack justify-content-between">
        <p class="fw-600"><?php echo $wr_id?'수정':'등록'; ?> 등록</p>
    </div>

    <div class="content-inner-wrapper">

        <form name="wv-data-form" id="wv-data-form"  method="post" class="" action="">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <div class="wv-vstack">

            </div>
            <div class="page-top-menu"  >
                <a href="<?php echo wv_page_url('0201'); ?>" class="top-menu-btn bg-[#BBBFCF]">취소</a>
                <button class="top-menu-btn bg-[#FC5555]" id="wv-data-form-submit"><?php echo $wr_id?'수정':'등록'; ?></button>
            </div>
        </form>
    </div>
</div>



<script>
    $(document).ready(function (){

    })
</script>

