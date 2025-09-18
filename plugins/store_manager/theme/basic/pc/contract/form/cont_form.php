<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
$vars['form_selector']=$skin_selector;

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
                    <p><?php echo $this->store->store->name; ?></p>
                    <p class="fs-[13/17/-0.52/500/#97989C]">업종 카테고리 | <?php echo $this->store->store->category_text; ?></p>
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



            <div class="hstack">
                <p class="text1 w-[75px]  " style="line-height: var(--wv-31)">매장운영자</p>
                <div class="col">
                    <div class="col"><?php echo $this->store->contract->render_part('mb_name','form'); ?></div>
                </div>
            </div>

            <div class="hstack align-items-start" >
                    <p class="text1 w-[75px] " style="line-height: var(--wv-31)">계약 상태</p>
                <div class="col">
                    <div class="col"><?php echo $this->store->contract->render_part('status','form',$vars); ?></div>
                </div>
            </div>

            <div class="hstack">
                <p class="text1 w-[75px]" style="line-height: var(--wv-31)">계약상품</p>
                <div class="col">
                    <div class="col"><?php echo $this->store->contract->render_part('contract_item','form',$vars); ?></div>
                </div>
            </div>

            <div class="hstack">
                <p class="text1 w-[75px]" style="line-height: var(--wv-31)">계약기간</p>
                <div class="col">
                    <div class="col"><?php echo $this->store->contract->render_part('contract_item','form',$vars); ?></div>
                </div>
            </div>

            <div class="hstack">
                <p class="text1 w-[75px]" style="line-height: var(--wv-31)">계약 담당자</p>
                <div class="col">
                    <div class="col"><?php echo $this->store->contract->render_part('contractmanager_wr_id','form',$vars); ?></div>
                </div>
            </div>

        </div>
    </div>

    <div class="mt-auto pb-[50px]">
        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition mt-[22px]" style="border:0;border-radius: var(--wv-4)">확인</button>
    </div>
</div>