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
<div class="vstack h-100 " id="<?php echo $skin_id; ?>">
    <div class="wv-offcanvas-header col-auto">
        <div class=" ">
            <div class="row align-items-center g-0"  >


                <div class="col">
                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                </div>

                <div class="col-auto text-center">
                    <p class="fs-[14/20/-0.56/600/#0D171B]"> </p>
                </div>
                <div class="col"></div>
            </div>

        </div>

    </div>
    <div class="wv-offcanvas-body col"  >


        <div class="vstack"  >



            <div class="col-box" style="row-gap: var(--wv-20)">
                <div class="hstack align-items-start" style="gap:var(--wv-12)">
                    <div class="w-[80px] h-[80px] col-auto">
                        <?php if($row['images'][0]){ ?>
                            <img src="<?php echo $row['images'][0]['path']; ?>" alt="" class="wh-100 object-fit-cover">
                        <?php } ?>
                    </div>
                    <div class="col">
                        <p class="fs-[16/22/-0.64/600/#0D171B]" id="<?php echo $skin_id; ?>-name"><?php echo $row['name']; ?></p>
                        <div class="fs-[14/20/-0.56/500/#0D171B] mt-[6px]">
                            <?php foreach ($row['prices'] as $price){ ?>
                                <div class="hstack" style="gap:var(--wv-5)">
                                    <span><?php echo number_format($price['price']); ?>원</span>
                                </div>
                            <?php } ?>
                        </div>
                        <div>
                            <?php if($row['is_main']){ ?>
                                <p class="fs-[12/17/-0.48/500/#97989C] wv-flex-box h-[21px] bg-[#efefef] mt-[11px]" style="padding: 0 var(--wv-6)">대표메뉴</p>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="hstack">
                <a href="#"  class="wv-flex-box border h-[40px] col" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                   data-wv-ajax-data='{
                                           "made":"sub01_01",
                                           "part":"menu",
                                           "action":"render_part_ceo_form",
                                           "fields":"ceo/name",
                                           "wr_id":"<?php echo $row['wr_id']; ?>",
                                           "menu_id":"<?php echo $row['id']; ?>",
                                           "replace_with":"<?php echo $skin_id; ?>-menu",
                                           "replace_field":"name"
                                           }'
                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" >
                    <i class="fa-solid fa-plus"></i>
                    <p class="fs-[14/17/-0.56/500/#0D171B]">메뉴명 변경</p>
                </a>
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