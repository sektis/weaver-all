<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
$vars['form_selector']=$skin_selector;
 
?>
<style>
    <?php echo $skin_selector?> {}
    <?php echo $skin_selector?> .col-box{padding: var(--wv-16) 0}
    <?php echo $skin_selector?> input, <?php echo $skin_selector?> select{height: var(--wv-39)}
</style>
<input type="hidden" name="<?php echo str_replace('[menu_form]','',$field_name); ?>[id]" value="<?php echo $row['id']; ?>">
<div class="vstack h-100 " id="<?php echo $skin_id; ?>">
    <div class="wv-offcanvas-header col-auto">
        <div class=" ">
            <div class="row align-items-center g-0"  >


                <div class="col">
                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                </div>

                <div class="col-auto text-center">
                    <p class="fs-[14/20/-0.56/600/#0D171B]">(대표) 새 메뉴 추가</p>
                </div>
                <div class="col"></div>
            </div>

        </div>

    </div>
    <div class="wv-offcanvas-body col"  >

        <div class=" ">
            <div class="h-[38px] hstack align-items-center" style="border-radius: var(--wv-4);background-color: #f9f9f9;gap:var(--wv-2);padding: 0 var(--wv-8)">

                <img src="<?php echo $this->manager->plugin_url; ?>/img/vec1.png" class="w-[14px]" alt="">
                <p class="fs-[12/17/-0.48/500/#97989C]">실제 판매되고 있는 메뉴를 등록해주세요.</p>

            </div>
        </div>

        <div class="vstack mt-[12px]"  >



            <div class="col-box" style="row-gap: var(--wv-20)">
                <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">메뉴명</p>
                <div class="mt-[20px]" >
                    <?php echo $this->store->menu->render_part('name','form'); ?>
                </div>

                <div class="fs-[12/17/-0.48/500/#97989C] mt-[20px] vstack" style="row-gap:var(--wv-8)">
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴의 이름은 고객이 쉽게 이해할 수 있도록 간결하고 명확하게 <br>작성해 주세요.</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴와 관련된 이름만 사용해주세요.</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴명에 특수문자(예: !, @, # 등)는 사용하지 말아주세요.</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴명은 간결하게 작성되어야 하므로 20자 이내로 제한해주세요.</p></div>
                </div>
            </div>
            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

            <div class="col-box" style="row-gap: var(--wv-20)">
                <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">가격</p>
                <div class="mt-[20px]" >
                    <?php echo $this->store->menu->render_part('prices','form'); ?>
                </div>


            </div>
            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

            <div class="col-box" style="row-gap: var(--wv-20)">
                <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">메뉴 부가설명</p>
                <div class="mt-[20px]" >
                    <?php echo $this->store->menu->render_part('desc','form'); ?>
                </div>


            </div>
            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

            <div class="col-box" style="row-gap: var(--wv-20)">
                <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">메뉴 이미지</p>
                <div class="mt-[20px]" >
                    <?php echo $this->store->menu->render_part('images','form'); ?>
                </div>
                <div class="fs-[12/17/-0.48/500/#97989C] mt-[20px] vstack" style="row-gap:var(--wv-8)">
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>이미지는 순서대로 고객에게 보여집니다. 첫번째 이미지는 대표 이미지입니다.</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>음식만 확대되어 어떤 음식인지 알 수 없는 이미지, 화질이 깨져 음식 형태를 알아볼 수 없는 이미지, 메뉴와 관련 없는 이미지는  삭제될 수 있습니다.</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>과도하게 연출되었거나 제공되는 것과 다를 경우 고객의 신뢰도가 떨어질 수 있습니다.</p></div>
                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>JPG / PNG 이미지만 업로드 가능합니다.</p></div>
                </div>

            </div>
            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

        </div>
    </div>

    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px] wv-mx-fit">
      
        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">확인</button>
    </div>
</div>