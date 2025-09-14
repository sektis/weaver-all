<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
?>
<style>
    <?php echo $skin_selector?> {}
    <?php echo $skin_selector?> .wv-ps-each {
        position: relative;
        width: var(--wv-160);
        height: var(--wv-104);
        background-color: #f9f9f9;
        border-radius: var(--wv-4);
        overflow: hidden;
        border: 1px solid #dcdcdc
    }
</style>
<div class="vstack h-100 " id="<?php echo $skin_id; ?>">
    <div class="wv-offcanvas-header col-auto">
        <div class="row align-items-center">
            <div class="col"></div>
            <div class="col-auto"><p>계약 상품 관리</p></div>

            <div class="col text-end">
                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    </div>

    <div class="wv-offcanvas-body col vstack">
        <div class="vstack" style="row-gap: var(--wv-16)">
        <?php echo $this->store->contractitem->render_part('name','form'); ?>
        <?php echo $this->store->contractitem->render_part('icon','form'); ?>
        </div>
    </div>

    <div class="mt-auto mb-[50px]">
        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition mt-[22px]" style="border:0; ;border-radius: var(--wv-4)">확인</button>
    </div>
</div>