<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget store-list-widget h-100" style="">
    <style>
        <?php echo $skin_selector?> {}
    </style>

    <div class="position-relative col col-lg-auto w-full  h-100 " style="overflow-y: auto;overflow-x: hidden">
        <?php if($data['contractitem_wr_id']==2){ ?>
        <a class="hstack h-[52px]" style="padding: 0 var(--wv-16);background-color: #ff5f5a;gap:var(--wv-6)">
            <span class="fs-[14/20/-0.56/600/#FFF]">매장 방문 식사 서비스 이용방법</span>
            <span class="fs-[12/17/-0.48/600/]   ms-auto" style="color:rgba(255, 255, 255, 0.6);">보러가기</span>
            <img src="<?php echo $wv_skin_url; ?>/arrow_right.png" class="w-[4px]" alt="">
        </a>
        <?php } ?>
        <?php if($data['contractitem_wr_id']==3){ ?>
            <a class="hstack h-[52px]" style="padding: 0 var(--wv-16);background-color: #ff774d;gap:var(--wv-6)">
                <span class="fs-[14/20/-0.56/600/#FFF]">포장 서비스 이용방법</span>
                <span class="fs-[12/17/-0.48/600/]   ms-auto" style="color:rgba(255, 255, 255, 0.6);">보러가기</span>
                <img src="<?php echo $wv_skin_url; ?>/arrow.png" class="w-[4px]" alt="">
            </a>
        <?php } ?>
        <div class="hstack justify-content-between h-[48px]" style="">
            <div>
                <input type="text" class="d-none" name="q" value="<?php echo $data['q']; ?>">
            </div>
            <div class="wv-dropdown-select">
                <button type="button" class=" btn" data-bs-toggle="dropdown"  >
                  <span class="hstack" style="gap:var(--wv-6)">
                       <span class="dropdown-label fs-[14/100%/-0.56/500/#0D171B]"></span>
                    <img src="<?php echo $wv_skin_url; ?>/arrow_down.png" class="w-[13px]" alt="">
                  </span>
                </button>
                <ul class="dropdown-menu " style="width: max-content;">
                    <div class="vstack align-items-start" style="padding: var(--wv-15) var(--wv-15);row-gap: var(--wv-10)">
                        <a class="  fs-[14/100%/-0.56/500/#0D171B] px-0 text-center" data-order-value="near" href="#"> <img src="<?php echo $wv_skin_url; ?>/near.png" class="w-[13px]" alt=""> 가까운 순</a>
                    </div>
                </ul>
            </div>
        </div>
        <div class="vstack  " style="padding-bottom: var(--wv-100)">
            <?php foreach ($data['content']['lists'] as $each){?>
                <div style="padding: var(--wv-12) var(--wv-10) var(--wv-20)">
                    <?php echo $each['list_each'];; ?>

                </div>
                <div class="wv-mx-fit" style="border-top: 6px solid #efefef"></div>
            <?php } ?>



            <?php if(!count($data['content']['lists'])){ ?>
                <div class="store-empty" style=" text-align: center;padding: var(--wv-40);color: #666;">
                    <i class="fa-solid fa-store-slash" style="font-size: 48px; color: #ddd; margin-bottom: 16px;"></i>
                    <p>등록된 매장이 없습니다.</p>
                </div>
            <?php } ?>

        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
            var center = <?php echo json_encode($data['center']) ?>;
            var searchData = {
                q: '<?php echo addslashes($data['q']); ?>',
                category_wr_id: '<?php echo (int) $data['category_wr_id']; ?>',
                contractitem_wr_id: '<?php echo (int) $data['contractitem_wr_id']; ?>',
                limit_km: '<?php echo (int) $data['limit_km']?$data['limit_km']:0; ?>',
                center:center,
                action:'<?php echo $data['action']?>',
                widget:'<?php echo $data['widget']?>',
            };
            $(".wv-dropdown-select",$skin).on('wv.dropdown.change',function (e) {
                var order_value = $("a.selected",$(this)).data('order-value');

                var listData = $.extend({}, searchData, {order: order_value});
                wv_ajax('<?php echo wv()->store_manager->ajax_url?>','offcanvas,end,backdrop,class: w-[360px],replace_width:<?php echo $skin_selector?>',listData)
            })
        });
    </script>
</div>