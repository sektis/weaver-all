<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_store_wr_id;

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{font-size: var(--wv-16);font-weight: 600}
        <?php echo $skin_selector?> .wv-mx-fit:last-of-type {display: none}



        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  w-full    " style=" ; background-color: #fff">

        <div class="  vstack menu-each-wrap" style="row-gap: var(--wv-20)">


                <?php foreach ($row['menu'] as $menu){
                    if(!$menu)continue;?>
                    <div  class="menu-each">
                        <div class="hstack fs-[14/20/-0.56/600/#0D171B] justify-content-between" style="padding: var(--wv-16) 0">
                            <p><?php echo $menu['name']; ?></p>
                            <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"menu","field":"ceo/edit","wr_id":"<?php echo $current_store_wr_id; ?>","menu_id":"<?php echo $menu['id']?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="fs-[14/100%/-0.56/600/#97989C]"> <img src="<?php echo $this->manager->plugin_url; ?>/img/vec2.png" class="w-[14px]" alt=""> <span>변경</span></a>

                        </div>
                        <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                        <div class="hstack align-items-start" style="padding: var(--wv-12) 0;gap:var(--wv-42)">
                            <div class="col">
                                <p class="fs-[12/17/-0.48/500/#97989C]"><?php echo $menu['desc']?get_text($menu['desc']):'미입력'; ?></p>
                                <div class="fs-[16/22/-0.64/700/#0D171B] mt-[4px]">
                                    <?php foreach ($menu['prices'] as $price){ ?>
                                        <div class="hstack" style="gap:var(--wv-5)">
                                            <?php if($price['name']){ ?><span><?php echo $price['name']; ?></span><?php } ?>
                                            <span><?php echo number_format($price['price']); ?>원</span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="w-[60px] h-[60px] col-auto">
                                <?php if($menu['images'][0]){ ?>
                                    <img src="<?php echo $menu['images'][0]['path']; ?>" alt="" class="wh-100 object-fit-cover" style="border-radius: var(--wv-4);overflow: hidden">
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="wv-mx-fit" style="height: 6px;background-color: #efefef"></div>
                <?php } ?>


        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>