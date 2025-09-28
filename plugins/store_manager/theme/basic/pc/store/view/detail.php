<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="overflow: hidden">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> [data-bs-toggle]{position: relative}
        <?php echo $skin_selector?> [data-bs-toggle]:not(.active){filter: grayscale(1)}
        <?php echo $skin_selector?> [data-bs-toggle].active:after{content: '';width: 100%;bottom:0;left:0;height: var(--wv-2);background-color: red;position: absolute}
        <?php echo $skin_selector?> .tab-pane-inner{padding: var(--wv-29) var(--wv-16)}

        @media (min-width: 992px) {
        }

        @media (max-width: 991.98px) {
        }
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container  h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if ($is_list_item_mode) { ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
                <div class="h-100" style="">
                    <div class="   position-absolute   start-0 w-100" style="top:var(--wv-60);z-index: 10">
                        <div class="container">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left_white.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto"></div>
                            </div>
                        </div>
                    </div>

                    <div class="h-[276px] wv-mx-fit">
                        <?php echo $this->store->store->render_part('image','view'); ?>
                    </div>

                    <div class="mt-[20px] pb-[16px]">
                        <p class="fs-[20/28/-0.8/600/#000]"><?php echo $row['name']; ?></p>
                        <div class="hstack mt-[2px] fs-[12/17/-0.48/500/]" style="gap:var(--wv-4)">
                            <p class=""><?php echo $row['category_item']['name']; ?></p>
                            <span class="text-[#989898]">•</span>
                            <p class="text-[#989898]"><?php echo $this->store->location->region_2depth_name; ?> <?php echo $this->store->location->region_3depth_name; ?></p>
                        </div>
                        <div class="mt-[14px]">
                            <?php echo $this->store->store->render_part('notice','view'); ?>
                        </div>
                    </div>
                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>


                    <div class="wv-mx-fit">  
                        <div class="hstack menu-tab-top" role="tablist">
                            <?php $i=0; foreach ($this->store->store->contract_non_free_list as $cont){
                                if(!$cont['service_content'])continue;
                                ?>
                                <a href="#"   class="<?php echo $i==0?'active':''; ?> fs-[14/20/-0.56/600/] col transition h-[42px] d-flex-center" data-bs-toggle="tab" data-bs-target="#cont-<?php echo $cont['id']; ?>" style="<?php echo $cont['item']['color_type_text'];?>">
                                    <?php echo $cont['item']['item_name_montserrat']; ?>
                                    <span class="cont-under-line position-absolute   start-0 w-100 h-[2px]" style="bottom:-1px;<?php echo $cont['item']['color_type_bg']; ?>" ></span>
                                </a>
                            <?php $i++;}?>
                        </div> 
                        <div class="wv-mx-fit" style="height: 1px;background-color: #efefef"></div>
                        <div class="tab-content menu-tab-content " id="myTabContent">
                            <?php foreach ($this->store->store->contract_non_free_list as $cont){
                                if(!$cont['service_content'])continue;
                                ?>
                            <div class="tab-pane fade show active" id="cont-<?php echo $cont['id']; ?>" >
                                <div class="tab-pane-inner  ">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]"><?php echo $cont['item']['item_name_montserrat']; ?> 서비스</p>
                                    <div class="mt-[12px]">
                                        <?php  echo $this->store->contract->render_part('service_detail','view',array('contract_id'=>$cont['id'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php }?>

                        </div>
                  
                    </div>

                    <div class="wv-mx-fit" style="height: var(--wv-6);background-color: #efefef"></div>

                    <div class="mt-[30px]">
                        <p class="fs-[14/20/-0.56/600/#0D171B]">매장 정보</p>

                        <div class="vstack mt-[16px] fs-[13/18/-0.13/500/#0D171B]" style="row-gap: var(--wv-12)">
                            <div class="hstack align-items-start" style="gap:var(--wv-42)">
                                <p class="fs-[13/17/-0.52/600/#97989C] min-w-[45px]">영업시간</p>
                                <div class="vstack" style="row-gap: var(--wv-4)">
                                    <?php  ; foreach ($this->store->biz->open_time_group as $each){ ?>
                                        <p class="hstack flex-wrap" style="gap:var(--wv-5);row-gap: 0"><span>(<?php echo $each['name']; ?>)</span><span><?php echo $each['time']; ?></span></p>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="hstack align-items-start" style="gap:var(--wv-42)">
                                <p class="fs-[13/17/-0.52/600/#97989C] min-w-[45px]">정기휴무</p>
                                <div class="vstack" style="row-gap: var(--wv-4)">

                                </div>
                            </div>
                            
                            <div class="hstack align-items-start" style="gap:var(--wv-42)">
                                <p class="fs-[13/17/-0.52/600/#97989C] min-w-[45px]">전화번호</p>
                                <p><?php echo $row['tel']; ?></p>
                            </div>

                            <div class="hstack align-items-start" style="gap:var(--wv-42)">
                                <p class="fs-[13/17/-0.52/600/#97989C] min-w-[45px]">주차</p>
                                <p><?php echo $this->store->biz->parking; ?></p>
                            </div>

                            <div class="hstack align-items-start" style="gap:var(--wv-42)">
                                <p class="fs-[13/17/-0.52/600/#97989C] min-w-[45px]">위치안내</p>
                                <p><?php echo $this->store->location->address_name_full; ?></p>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).ajaxForm({
                // reload: false,
                // reload_ajax:true,
                // success: function () {
                //     var $offcanvas = $skin.closest('.wv-offcanvas');
                //     $offcanvas.offcanvas('hide');
                // }
            })
        })
    </script>
</div>