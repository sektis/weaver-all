<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;

?>
<style>
    <?php echo $skin_selector?> {}
    <?php echo $skin_selector?> .cont-box{padding: var(--wv-12);border-radius: var(--wv-4);border: var(--wv-1) solid #efefef;background: #fff;}
    <?php echo $skin_selector?> .status-box{;background-color: #f9f9f9;color:#97989c;padding: var(--wv-4) var(--wv-9)}
    <?php echo $skin_selector?> .status-box.active{background-color: #0d171b!important;color:#fff}


</style>

<div id="<?php echo $skin_id; ?>">
   <div class="cont-box">
       <div class="hstack justify-content-between align-items-start">
           <div>
               <p class="fs-[16/22/-0.64/700/#0D171B] ff-montserrat"><?php echo $row['item_name_montserrat']; ?></p>
               <p class="fs-[12/17/-0.48/500/#97989C] mt-[2px]"><?php echo $row['desc_option']; ?></p>
           </div>
           <div>
               <p class="wv-flex-box status-box fs-[12/17/-0.48/500/] ff-pretendard <?php echo $cont['status']==1?'active':''; ?>" style="background-color: #f9f9f9"><?php echo $cont['status_text_ceo']?$cont['status_text_ceo']:'미이용 중'; ?></p>

           </div>
       </div>
       <div class="mt-[12px] fs-[12/17/-0.48/600/#97989C] w-full">
           <?php if($cont['status']==1){ ?>
               <div class="wv-flex-box h-[39px] w-full justify-content-start" style="background-color: #f9f9f9">
                   <span>시작일</span>
                   <span class="ff-montserrat fw-600"><?php echo date('Y.m.d',strtotime($cont['start'])); ?></span>
               </div>
           <?php }else{ ?>
               <?php if($row['is_free']){ ; ?>
                   <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                       <?php if($row['use_intro']){ ?>
                           data-wv-ajax-data='{ "action":"view","made":"contract_item","part":"contractitem","field":"intro","wr_id":"<?php echo $row['wr_id']; ?>","contract_id":"<?php echo $cont['id']?>","store_wr_id":"<?php echo $store_wr_id; ?>"}'

                       <?php }else{ ?>
                           data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"contract","field":"ceo/service_form","wr_id":"<?php echo $store_wr_id; ?>","contract_id":"<?php echo $cont['id']?>","contractitem_wr_id":"<?php echo $row['wr_id']; ?>"}'
                       <?php } ?>
                      data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="wv-flex-box w-full border h-[39px]">
                       시작하기
                   </a>
                   <?php }elseif($cont['call_num']){ ?>
                       <a href="tel:<?php echo $cont['call_num']; ?>" class="wv-flex-box w-full border h-[39px]">
                           <span><img src="<?php echo $this->manager->plugin_url; ?>/img/phone.png" class="w-[14px]" alt=""></span>
                           <span>문의하기</span>
                       </a>
                   <?php } ?>
           <?php } ?>
       </div>
   </div>
</div>