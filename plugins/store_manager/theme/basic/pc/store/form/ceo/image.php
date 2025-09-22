<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
$vars['form_selector']=$skin_selector;

?>
<style>
    <?php echo $skin_selector?> {}
</style>
<div class="vstack h-100 " id="<?php echo $skin_id; ?>">
    <div class="wv-offcanvas-header col-auto">
        <div class=" ">
            <div class="row align-items-center g-0"  >


                <div class="col">
                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $wv_skin_url; ?>/vec3.png" class="w-[28px]" alt=""></div>

                </div>

                <div class="col-auto text-center">
                    <p class="fs-[14/20/-0.56/600/#0D171B]">매장 소개 이미지</p>
                </div>
                <div class="col"></div>
            </div>
            <div class="mt-[8px]" style="padding: var(--wv-16) 0">
                <p class="fs-[12/17/-0.48/600/#0D171B]">매장 사진 업로드</p>
                <div class="h-[38px] mt-[12px] hstack align-items-center" style="border-radius: var(--wv-4);background-color: #f9f9f9;gap:var(--wv-2);padding: 0 var(--wv-8)">

                    <img src="<?php echo $wv_skin_url; ?>/vec1.png" class="w-[14px]" alt="">
                    <p class="fs-[12/17/-0.48/500/#97989C]">매장 이름 변경을 원하실 경우, 고객센터로 문의바랍니다</p>

                </div>
            </div>
        </div>

    </div>
    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
    <div class="wv-offcanvas-body col"   >

        <div class=" ">
            <?php echo $this->store->store->render_part('image','form');; ?>
        </div>

        <div class="fs-[12/17/-0.48/500/#97989C] mt-[20px] vstack" style="row-gap:var(--wv-8)">
            <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>이미지는 순서대로 고객에게 보여집니다</p></div>
            <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>매장 소개 이미지는 <span class="text-[#0D171B]">매장 외부, 내부, 대표 메뉴 등</span> 내 매장을 <br>잘 나타낼 수 있는 이미지가 좋아요</p></div>
            <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>JPG / PNG 이미지만 업로드 가능합니다</p></div>
        </div>


    </div>

    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
        <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">완료</button>
    </div>
</div>