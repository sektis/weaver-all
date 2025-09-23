<?php
global $g5;
if(!$row['id']){
    if(!$contractitem_wr_id){
        alert('contractitem id 누락');
    }
    $cont_item = wv()->store_manager->made('contract_item')->get($contractitem_wr_id)->contractitem->row;

}else{
    $cont_item = $row['item'];
}

?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[contractitem_wr_id]" value="<?php echo $cont_item['wr_id']; ?>">
                <?php if($is_list_item_mode){ ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 " style="padding-top:var(--wv-10)">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0"  >


                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                                </div>

                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]"><?php echo $cont_item['item_name_montserrat']; ?> <?php echo $row['service_content']?'관리':'등록'; ?></p>
                                </div>
                                <div class="col"></div>
                            </div>

                        </div>

                    </div>

                    <div class="wv-offcanvas-body col"   >
                        <div class="col-box">
                            <div class="" style="padding: var(--wv-16) 0">
                                <p class="fs-[16/22/-0.64/600/#0D171B]"><?php echo $cont_item['item_name_montserrat']; ?> 서비스</p>
                                <div class="hstack mt-[8.5px]" style="gap:var(--wv-2)">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/vec1.png" class="w-[14px]" alt="">
                                    <p class="fs-[12/17/-0.48/500/#97989C]">한정된 조건으로 제공하는 특별한 서비스</p>
                                </div>
                            </div>
                            <div class=" ">
                                <?php echo $this->store->contract->render_part('service_content','form',array('contract_id'=>$row['id']));; ?>
                            </div>

                            <div class="fs-[12/17/-0.48/500/#97989C] mt-[20px] vstack" style="row-gap:var(--wv-8)">
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>서비스 메뉴명만 간단히 입력해 주세요. (문장 X)</p></div>
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>고객이 이해하기 쉽게 메뉴판에 등록된 메뉴명을 사용해주세요.</p></div>
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>고객이 혜택을 한눈에 이해할 수 있도록 입력해 주세요. <br>ex) 계란찜 서비스 , 음료 제공, 김치찌개 or 된장찌개....</p></div>
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>특수문자(예: !, @, # 등)는 사용하지 말아주세요.</p></div>
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>제공되는 서비스는 간결하게 작성되어야 하므로 50자 이내로  제한해주세요.</p></div>
                            </div>
                        </div>

                        <?php if($cont_item['use_schedule']){ ?>
                            <div class="wv-mx-fit" style="height: 10px;background-color: #efefef"></div>
                            <div class="col-box">
                                <div class="" style="padding: var(--wv-16) 0">
                                    <p class="fs-[16/22/-0.64/600/#0D171B]">방문 가능한 요일 및 시간</p>
                                    <div class="hstack mt-[8.5px]" style="gap:var(--wv-2)">
                                        <img src="<?php echo $this->manager->plugin_url; ?>/img/vec1.png" class="w-[14px]" alt="">
                                        <p class="fs-[12/17/-0.48/500/#97989C]">한정된 조건으로 제공하는 특별한 서비스</p>
                                    </div>
                                </div>
                                <div class=" ">
                                    <?php echo $this->store->contract->render_part('service_time','form');; ?>
                                </div>


                            </div>
                        <?php } ?>
                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">완료</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");

            $("form", $skin).ajaxForm({
                reload:false,
                success: function () {
                    var $offcanvas =  $skin.closest('.wv-offcanvas');
                    $offcanvas.attr('data-need-refresh', 'true');
                    var parent_id = $offcanvas.data('parent-elem');
                    if(parent_id){
                        $("#"+parent_id).attr('data-need-refresh', 'true');
                        wv_reload_offcanvas(parent_id);
                    }
                    alert('완료');

                    $offcanvas.offcanvas('hide');

                }
            })
        })
    </script>
</div>