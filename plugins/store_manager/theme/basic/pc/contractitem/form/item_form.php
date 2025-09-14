<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
?>
<div class="vstack h-100 ">
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
        <input type="hidden" name="member[is_manager]" value="1">
        <?php echo $this->store->contractitem->render_part('name','form'); ?>
        <?php echo $this->store->contractitem->render_part('icon','form'); ?>
    </div>

    <div class="mt-auto mb-[50px]">
        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition mt-[22px]" style="border:0; ;border-radius: var(--wv-4)">확인</button>
    </div>
</div>