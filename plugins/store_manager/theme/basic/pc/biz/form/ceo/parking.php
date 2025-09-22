<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
$vars['form_selector']=$skin_selector;

?>
<style>
    <?php echo $skin_selector?> {}
    <?php echo $skin_selector?> textarea{background-color: #f9f9f9;padding: var(--wv-13) var(--wv-12)}
</style>
<div class="vstack h-100 " id="<?php echo $skin_id; ?>">
    <div class="wv-offcanvas-header col-auto">
        <div class=" ">
            <div class="row align-items-center g-0"  >


                <div class="col">
                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $wv_skin_url; ?>/vec3.png" class="w-[28px]" alt=""></div>

                </div>

                <div class="col-auto text-center">
                    <p class="fs-[14/20/-0.56/600/#0D171B]">주차</p>
                </div>
                <div class="col"></div>
            </div>

        </div>

    </div>
    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
    <div class="wv-offcanvas-body col"   >

        <div class=" h-100 vstack">
            <p class="fs-[16/22/-0.64/600/#0D171B] col-auto">주차 여부 설정</p>
            <div class="mt-[20px] col">
                <?php echo $this->store->biz->render_part('parking','form'); ?>
            </div>


        </div>




    </div>

    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
        <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">완료</button>
    </div>
</div>