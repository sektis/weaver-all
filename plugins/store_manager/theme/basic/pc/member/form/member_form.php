<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
if($row['mb_id']){
    $mb = get_member($row['mb_id']);
    $row = array_merge($row,$mb);
}

?>
<style>
    <?php echo $skin_selector?> {}
    <?php echo $skin_selector?> .text1{font-size: var(--wv-14);font-weight: 600;}
    <?php echo $skin_selector?> .text3{font-size: var(--wv-14);font-weight: 400;}
    <?php echo $skin_selector?> .text2{font-size: var(--wv-14);font-weight: 500;color:#97989C;width: var(--wv-60)}
    <?php echo $skin_selector?> input, <?php echo $skin_selector?> select{height: var(--wv-39)}
</style>
<div class="vstack h-100 wmember-form" id="<?php echo $skin_id; ?>">
    <div class="wv-offcanvas-header col-auto">
        <div class="row align-items-center">

            <div class="col-auto">
                <div class="vstack" style="row-gap: var(--wv-5)">
                    <p><?php echo $row['mb_name'] ?> (<?php echo $row['mb_id']; ?>)</p>
                    <p class="fs-[13/17/-0.52/500/#97989C]">최근 접속일 | <?php echo $row['mb_today_login']; ?></p>
                </div>
            </div>

            <div class="col text-end">
                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>

    </div>
    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
    <div class="wv-offcanvas-body col"  >

        <div class="vstack" style="row-gap: var(--wv-16)">

            <div class="border rounded-[4px] p-[14px] vstack  " style="row-gap: var(--wv-16)">
                <div class="hstack gap-[8px]">
                    <p class="text1 left-text1">개인정보</p>
                    <?php if($row['is_cert']){ ?>
                        <div class="hstack gap-[4px]">
                            <div class="wv-ratio-circle w-[14px] bg-[#29cc6a] text-white">
                                <div class="d-flex-center"><i class="fa-solid fa-check fs-06em"></i></div>
                            </div>
                            <p class="fs-[14/20/-0.56/500/#29CC6A]">본인인증 확인</p>
                        </div>
                    <?php } ?>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">이름</p>
                    <p class="text3"><?php echo $row['mb_name']; ?></p>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">생년월일</p>
                    <p class="text3"><?php echo $row['mb_birth']?date('Y / m / d',strtotime($row['mb_birth'])):''; ?></p>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">휴대폰번호</p>
                    <p class="text3"><?php echo wv_mask_number($row['mb_hp']); ?></p>
                </div>
            </div>

            <div class="border rounded-[4px] p-[14px] vstack  " style="row-gap: var(--wv-16)">
                <div class="hstack gap-[8px]">
                    <p class="text1 left-text1">이용 정보</p>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">가입일자</p>
                    <p class="text3"><?php echo date('Y / m / d',strtotime($row['mb_datetime'])); ?></p>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">닉네임</p>
                    <p class="text3"><?php echo $row['mb_nick']; ?></p>
                </div>
                <div class="hstack gap-[33px]">
                    <p class="text2 ">계좌번호</p>
                    <p class="text3"> </p>
                </div>
            </div>

            <div class="hstack">
                <p class="text1 w-[75px]">계정 관리</p>
                <div class="col"><?php echo $this->store->member->render_part('mb_password_init','form',array('row'=>$row)); ?></div>
            </div>

            <div class="hstack">
                <p class="text1 w-[75px]">계정 상태</p>
                <div class="col"><?php echo $this->store->member->render_part('active','form',array('row'=>$row)); ?></div>
            </div>

            <div class="hstack">
                <p class="text1 w-[75px]">관리자 메모</p>
                <div class="col"><?php echo $this->store->member->render_part('admin_memo','form',array('row'=>$row)); ?></div>
            </div>

        </div>
    </div>

    <div class="mt-auto pb-[50px]">
        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition mt-[22px]" style="border:0;border-radius: var(--wv-4)">확인</button>
    </div>
</div>