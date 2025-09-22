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
                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                </div>

                <div class="col-auto text-center">
                    <p class="fs-[14/20/-0.56/600/#0D171B]">매장 주소</p>
                </div>
                <div class="col"></div>
            </div>

        </div>

    </div>
    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
    <div class="wv-offcanvas-body col"   >

        <div class=" h-100 vstack" style="row-gap:var(--wv-20)">
            <div>
                <p class="fs-[16/22/-0.64/600/#0D171B] col-auto">기본 주소</p>
                <p class="fs-[16/22/-0.64/500/#0D171B] mt-[12px]"><?php echo $row['address_name_full']; ?></p>
                <div class="mt-[6px] hstack align-items-center" style="gap:var(--wv-2)">

                    <img src="<?php echo $this->manager->plugin_url; ?>/img/vec1.png" class="w-[14px]" alt="">
                    <p class="fs-[12/17/-0.48/500/#97989C]">매장 기본 주소 변경을 원하실 경우, 고객센터로 문의바랍니다</p>

                </div>
            </div>

            <div class="wv-mx-fit" style="height: 10px;background-color: #efefef"></div>

            <div>
                <p class="fs-[16/22/-0.64/600/#0D171B]  ">상세 주소</p>
                <div class="mt-[20px]" style="z-index: 10">
                    <input type="text" name="location[detail_address_name]"   id="location[detail_address_name]"   class="form-control h-[48px]    " style="background-color: #f9f9f9"  placeholder="상세주소를 입력하세요."
                           value="<?php echo htmlspecialchars($row['detail_address_name']); ?>">
                    <label for="contract[biz_num]" class="visually-hidden">상세주소</label>
                </div>
            </div>

        </div>



    </div>

    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
        <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">완료</button>
    </div>
</div>