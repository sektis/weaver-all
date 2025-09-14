<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
if($row['mb_id']){
    $mb = get_member($row['mb_id']);
    $row = array_merge($row,$mb);
}
?>
<div class="vstack h-100 ">
    <div class="wv-offcanvas-header col-auto">
        <div class="row align-items-center">
            <div class="col"></div>
            <div class="col-auto"><p>(사장님) <?php echo $row['wr_id']?'수정':'신규등록' ?></p></div>

            <div class="col text-end">
                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    </div>

    <div class="wv-offcanvas-body col vstack">
        <input type="hidden" name="member[is_ceo]" value="1">
        <?php echo $this->store->member->render_part('mb_id','form',array('row'=>$row)); ?>
        <?php echo $this->store->member->render_part('mb_password','form',array('row'=>$row)); ?>
        <?php echo $this->store->member->render_part('mb_name','form',array('row'=>$row)); ?>
        <?php echo $this->store->member->render_part('mb_hp','form',array('row'=>$row)); ?>
        <?php echo $this->store->member->render_part('mb_email','form',array('row'=>$row)); ?>
    </div>

    <div class="mt-auto mb-[50px]">
        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition mt-[22px]" style="border:0;border-radius: var(--wv-4)">확인</button>
    </div>
</div>