<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;

?>
<style>
    <?php echo $skin_selector?>
    {
    }
    <?php echo $skin_selector?>
    .cont-box {
        padding: var(--wv-16);
        border-radius: var(--wv-4);
        border: var(--wv-1) solid #efefef;
        background: #fff;
    }

    <?php echo $skin_selector?>
    .status-box {
        padding: var(--wv-4) var(--wv-9)
    }

    <?php echo $skin_selector?>
    .cont-box:not(.active) .color-type * {
        filter: grayscale(1)
    }


</style>

<div id="<?php echo $skin_id; ?>">
    <div class="cont-box <?php echo $cont['status'] == 1 ? 'active' : ''; ?>">
        <?php ob_start(); ?>
        <div class="hstack justify-content-between align-items-start color-type" style="gap:var(--wv-12)">
            <div class="col-auto w-[42px] h-[42px] ">
                <img src="<?php echo $row['icon']['path']; ?>" alt="" class="wh-100 object-fit-cover">
            </div>
            <div class="col">
                <p class="fs-[16/22/-0.64/700/#0D171B]  "><?php echo $row['item_name_montserrat']; ?></p>
                <p class="fs-[11/15/-0.44/500/#97989C]"><?php echo $row['desc_list']; ?></p>
            </div>
            <div class="col-auto">
                <p class="wv-flex-box status-box fs-[12/17/-0.48/500/] " style="<?php echo $row['color_type_bg']; ?><?php echo $row['color_type_text']; ?>"><?php echo $cont['status_text_config'] ? $cont['status_text_config'] : '미제공'; ?></p>
            </div>
        </div>
        <?php $html = ob_get_clean(); ?>
        <?php if($cont['status']==1){
            echo $html;
        }else if($row['use_intro']==0){
            echo $html;
        }else{
        // 인트로사용
        ?>
            <p class="fs-[12/22/-0.48/700/]" style="<?php echo $row['color_type_text']; ?>"><?php echo $row['item_name_montserrat']; ?></p>
            <p class="fs-[16/22/-0.64/600/#0D171B]"><?php echo nl2br($row['intro']['desc']); ?></p>
            <div class="mt-[16px]">
                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                    <?php if ($row['use_intro'] and !$cont['id']) { ?>
                        data-wv-ajax-data='{ "action":"view","made":"contract_item","part":"contractitem","field":"intro","wr_id":"<?php echo $row['wr_id']; ?>","contract_id":"<?php echo $cont['id'] ?>","store_wr_id":"<?php echo $store_wr_id; ?>"}'
                    <?php } else { ?>
                        data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"contract","field":"ceo/service_form","wr_id":"<?php echo $store_wr_id; ?>","contract_id":"<?php echo $cont['id'] ?>","contractitem_wr_id":"<?php echo $row['wr_id']; ?>"}'
                    <?php } ?>
                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="hstack justify-content-between border w-full" style="padding: var(--wv-14) var(--wv-12);border-radius: var(--wv-4);gap:var(--wv-10)">
                   <span>
                       <span class="fs-[11/15/-0.44/500/#97989C] d-block"><?php echo $row['intro']['button_label']; ?></span>
                       <span class="fs-[14/20/-0.56/500/#0D171B] d-block mt-[2px]"><?php echo $row['item_name_montserrat']; ?> 등록하기</span>
                   </span>
                    <span><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_right_gray.png" class="w-[18px]" alt=""></span>
                </a>
            </div>
        <?php } ?>



        <?php if ($cont['status'] == 1) { ?>
            <div class="mt-[12px]" style="height: 1px;background-color: #efefef"></div>
            <?php if ($cont['service_content']) { ?>
                <div class="mt-[12px] fs-[12/17/-0.48/600/] w-full" >
                    <p style="<?php echo $row['color_type_text']; ?>">제공 <?php echo $row['is_free'] ? '특가혜택' : '서비스'; ?></p>

                    <div class="border justify-content-start w-full mt-[4px]" style="padding: var(--wv-10) var(--wv-12);gap:var(--wv-4); ">
                        <div class="hstack align-items-start " style="<?php echo $row['color_type_text']; ?>">
                            <?php if ($row['icon_small']['path']) { ?>
                                <div><img src="<?php echo $row['icon_small']['path']; ?>" class="w-[16px]" alt=""></div>
                            <?php } ?>
                            <p><?php echo $cont['service_content']; ?></p>
                        </div>


                        <?php if (array_filter($cont['service_time'])) { ?>
                            <div class="my-[8px]" style="height: 1px;background-color: #efefef"></div>
                            <div class="fs-[12/17/-0.48/500/#0D171B] hstack align-items-start" style="gap:var(--wv-4)"  >
                                <div class="col-auto"><img src="<?php echo $this->manager->plugin_url; ?>/img/clock.png" class="w-[14px]" alt=""></div>
                                <p class="col-auto">이용 가능한 시간 :</p>
                                <div class="vstack col " style="row-gap: var(--wv-5)">

                                    <?php $i=0; foreach ($cont['service_time_group'] as $each){ ?>
                                        <p class="hstack flex-wrap" style="gap:var(--wv-5);row-gap: 0"><span>(<?php echo $each['name']; ?>)</span><span><?php echo $each['time']; ?></span></p>
                                        <?php $i++;} ?>
                                    <?php if($i==0){ ?>
                                        <p>미등록</p>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
            <?php }?>


            <div class="text-end mt-[8px]">
                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                    <?php if ($row['use_intro'] and !$cont['id']) { ?>
                        data-wv-ajax-data='{ "action":"view","made":"contract_item","part":"contractitem","field":"intro","wr_id":"<?php echo $row['wr_id']; ?>","contract_id":"<?php echo $cont['id'] ?>","store_wr_id":"<?php echo $store_wr_id; ?>"}'
                    <?php } else { ?>
                        data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"contract","field":"ceo/service_form","wr_id":"<?php echo $store_wr_id; ?>","contract_id":"<?php echo $cont['id'] ?>","contractitem_wr_id":"<?php echo $row['wr_id']; ?>"}'
                    <?php } ?>
                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true" class="wv-flex-box fs-[12/17/-0.48/500/#97989C]"  >
                    <span><img src="<?php echo $this->manager->plugin_url; ?>/img/pen.png" class="w-[12px]" alt=""></span>
                    <span>서비스 관리</span>
                </a>
            </div>
        <?php } ?>
    </div>
</div>