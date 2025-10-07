<?php
global $g5;
$write_table = $this->manager->get_write_table_name();
sql_query(" update {$write_table} set wr_hit = wr_hit + 1 where wr_id = '{$row['wr_id']}' ",1);
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white overflow-x-hidden"  >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> [data-bs-toggle]{position: relative}
        <?php echo $skin_selector?> [data-bs-toggle]:not(.active){filter: grayscale(1)}
        <?php echo $skin_selector?> [data-bs-toggle] .cont-under-line{opacity:0}
        <?php echo $skin_selector?> [data-bs-toggle].active .cont-under-line{opacity: 1}
        <?php echo $skin_selector?> .tab-pane-inner{padding: var(--wv-29) var(--wv-16)}

        @media (min-width: 992px) {
        }

        @media (max-width: 991.98px) {
        }
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container  h-100" style="overflow-x: hidden;overflow-y: auto">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if ($is_list_item_mode) { ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
                <div class="h-100" style="">
                    <div class="position-relative">
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
                                    <span class="cont-under-line position-absolute   start-0 w-100 h-[2px]" style="bottom:-1px;background-color: <?php echo $cont['item']['color_type']['text']; ?>" ></span>
                                </a>
                            <?php $i++;}?>
                        </div> 
                        <div class="wv-mx-fit" style="height: 1px;background-color: #efefef"></div>
                        <div class="tab-content menu-tab-content " id="myTabContent">
                            <?php $i=0; foreach ($this->store->store->contract_non_free_list as $cont){
                                if(!$cont['service_content'])continue;
                                ?>
                            <div class="tab-pane fade <?php echo $i==0?'show active':''; ?> " id="cont-<?php echo $cont['id']; ?>" >
                                <div class="tab-pane-inner  ">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]"><?php echo $cont['item']['item_name_montserrat']; ?> 서비스</p>
                                    <div class="mt-[12px]">
                                        <?php  echo $this->store->contract->render_part('service_detail','view',array('contract_id'=>$cont['id'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php $i++;}?>

                        </div>
                  
                    </div>

                    <div class="wv-mx-fit" style="height: var(--wv-6);background-color: #efefef"></div>

                    <div class="mt-[30px]">
                        <p class="fs-[14/20/-0.56/600/#0D171B]">매장 정보</p>

                        <div class="vstack mt-[16px] fs-[13/18/-0.13/500/#0D171B]" style="row-gap: var(--wv-12)">
                            <div class="hstack align-items-start" style="gap:var(--wv-42)">
                                <p class="fs-[13/17/-0.52/600/#97989C] min-w-[45px]">영업시간</p>
                                <div class="vstack" style="row-gap: var(--wv-4)">
                                    <?php   foreach ($this->store->biz->open_time_group as $each){ ?>
                                        <p class="hstack flex-wrap" style="gap:var(--wv-5);row-gap: 0"><span>(<?php echo $each['name']; ?>)</span><span><?php echo $each['time']; ?></span></p>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="hstack align-items-start" style="gap:var(--wv-42)">
                                <p class="fs-[13/17/-0.52/600/#97989C] min-w-[45px]">정기휴무</p>
                                <div class="vstack" style="row-gap: var(--wv-4)">
                                    <?php   foreach (generate_dayoffs_detailed_grouped($this->store->dayoffs->list) as $each){ ?>
                                        <p class="hstack flex-wrap" style="gap:var(--wv-5);row-gap: 0"><span>(<?php echo $each['name']; ?>)</span><span><?php echo $each['targets']; ?></span></p>
                                    <?php } ?>

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

                    <div class="mt-[11px]">
                        <div class="border" style="border-radius: var(--wv-4);">
                            <div class="h-[112px]" >
                                <?php
                                $address_skin_data = array(
                                    'lat' => $this->store->location->lat,
                                    'lng' => $this->store->location->lng,
                                    'address_name' => 'dasdsa',
                                    'icon'=>$this->store->store->category_item['icon']['path'],
                                    'icon_wrap'=>wv()->store_manager->made('sub01_01')->plugin_url.'/img/category_icon_wrap.png'
                                );

                                echo wv_widget('location/address',$address_skin_data);
                                ?>
                            </div>
                            <div class="hstack fs-[12/17/-0.48/600/#0D171B]">
                                <a href="" class="col h-[42px] d-flex-center copy-address " data-address="<?php echo $this->store->location->address_name_full; ?>">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/copy.png" class="w-[14px] me-[4px]" alt="">
                                    주소복사
                                </a>
                                <a href="" class="col h-[42px] d-flex-center " onclick="window.open(open_kakaomap_with_fallback('<?php echo $this->store->store->name?>','<?php echo $this->store->location->lat?>','<?php echo $this->store->location->lng?>'))">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/map_view.png" class="w-[14px] me-[4px]" alt="">
                                    지도보기
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="" class="mt-[12px] hstack border d-flex-center h-[42px]" style="border-radius: var(--wv-4);gap:var(--wv-4)">
                        <img src="<?php echo $this->manager->plugin_url; ?>/img/comment_dot.png" class="w-[14px]" alt="">
                        <span>이 가게에 대한 ‘나만의 메모' 남기기</span>
                    </a>

                    <div class="wv-mx-fit mt-[30px]" style="height: var(--wv-6);background-color: #efefef"></div>

                    <div class="mt-[30px]">
                        <p class="fs-[14/20/-0.56/600/#0D171B]">대표 메뉴</p>
                        <div class="mt-[25px]">
                            <?php echo $this->store->menu->render_part('menu_list','view',array('only_main'=>1));; ?>
                        </div>
                    </div>

                    <div class="wv-mx-fit mt-[30px]" style="height: var(--wv-6);background-color: #efefef"></div>

                    <div class="mt-[30px]">
                        <p class="fs-[14/20/-0.56/600/#0D171B]">일반 메뉴</p>
                        <div class="mt-[25px]">
                            <?php echo $this->store->menu->render_part('menu_list','view',array('only_not_main'=>1));; ?>
                        </div>
                    </div>

                    <div class="wv-mx-fit  " style="height: var(--wv-2);background-color: #efefef"></div>



                    <div class="wv-mx-fit">
                        <?php echo wv_widget('content/copyright'); ?>
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
            $(".copy-address",$skin).click(function (e) {
                e.preventDefault()
                copy_invite_code($(this).data('address'));
            })
            function copy_invite_code(code) {
                wv_copy_to_clipboard(code, {
                    success_message: '주소가 복사되었습니다!',
                    error_message: '복사에 실패했습니다.',
                    success_callback: function() {
                        console.log('복사 성공!');
                    }
                });
            }
        })
    </script>
</div>