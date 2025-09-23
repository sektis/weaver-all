<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;

?>
<style>
    <?php echo $skin_selector?> {}
    <?php echo $skin_selector?> .cont-box{padding: var(--wv-16);border-radius: var(--wv-4);border: var(--wv-1) solid #efefef;background: #fff;}
    <?php echo $skin_selector?> .status-box{padding: var(--wv-4) var(--wv-9)}
    <?php echo $skin_selector?> .status-box:not(.active){}


</style>

<div id="<?php echo $skin_id; ?>">
   <div class="cont-box">
       <div class="hstack justify-content-between align-items-start" style="gap:var(--wv-12)">
           <div class="col-auto w-[42px] h-[42px]">
               <img src="<?php echo $row['icon']['path']; ?>" alt="" class="wh-100 object-fit-cover">
           </div>
           <div class="col">
               <p class="fs-[16/22/-0.64/700/#0D171B]  "><?php echo $row['item_name_montserrat']; ?></p>
               <p class="fs-[11/15/-0.44/500/#97989C]"><?php echo $row['desc_list']; ?></p>
           </div>
           <div class="col-auto">
               <p class="wv-flex-box status-box fs-[12/17/-0.48/500/] <?php echo $row['status']==1?'active':''; ?>" style="<?php echo $row['color_type_bg']; ?><?php echo $row['color_type_text']; ?>"><?php echo $cont['status_text_config']?$cont['status_text_config']:'미제공'; ?></p>

           </div>
       </div>
       <?php if($cont['status']==1){ ?>
       <div class="mt-[12px]" style="height: 1px;background-color: #efefef"></div>
       <div class="mt-[12px] fs-[12/17/-0.48/600/] w-full" style="<?php echo $row['color_type_text']; ?>">
            <p>제공 서비스</p>
       </div>
       <?php } ?>
   </div>
</div>