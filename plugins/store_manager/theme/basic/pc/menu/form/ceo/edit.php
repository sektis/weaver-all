<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .form-switch .form-check-label{display: none}
        <?php echo $skin_selector?> .form-switch .form-check-input{zoom:1.3}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if($is_list_item_mode){ ?>
                <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">

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
                                    <?php if($row['images'][0]){ ?>
                                    <div class="w-[80px] h-[80px] col-auto">
                                        <img src="<?php echo $row['images'][0]['path']; ?>" alt="" class="wh-100 object-fit-cover">
                                    </div>
                                    <?php } ?>
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

                            <div class="hstack col-box" style="gap:var(--wv-8)">
                                <a href="#"  class="wv-flex-box border h-[40px] col" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                                   data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"menu","field":"ceo/name","wr_id":"<?php echo $row['wr_id']; ?>","menu_id":"<?php echo $menu_id?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  >
                                    <i class="fa-solid fa-plus"></i>
                                    <p class="fs-[14/17/-0.56/500/#0D171B]">메뉴명 변경</p>
                                </a>
                                <a href="#"  class="wv-flex-box border h-[40px] col" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                                   data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"menu","field":"ceo/prices","wr_id":"<?php echo $row['wr_id']; ?>","menu_id":"<?php echo $menu_id?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  >
                                    <i class="fa-solid fa-plus"></i>
                                    <p class="fs-[14/17/-0.56/500/#0D171B]">가격 변경</p>
                                </a>
                            </div>
                            <div class="wv-mx-fit" style="height: 10px;background-color: #efefef"></div>

                            <div class="col-box  hstack justify-content-between" style="row-gap: var(--wv-20)">
                                <div>
                                    <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">대표메뉴</p>
                                    <p class="fs-[12/17/-0.48/500/#97989C]">이 메뉴를 대표메뉴로 설정해요</p>
                                </div>
                                <div>
                                    <div class="form-check form-switch align-self-center"  data-on-value="대표메뉴"  data-off-value="대표메뉴"
                                         data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php'
                                         data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"menu","field":"ceo/is_main","wr_id":"<?php echo $row['wr_id']; ?>","menu_id":"<?php echo $menu_id?>"}'
                                         data-wv-ajax-option="offcanvas,bottom,backdrop,class: w-[360px] rounded-t-[4px],reload_ajax:true" >
                                        <input class="form-check-input" type="checkbox"     <?php echo $row['is_main'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>


                            </div>
                            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                            <div class="col-box" style="row-gap: var(--wv-20)">
                                <div class="hstack justify-content-between">
                                    <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">메뉴 부가설명</p>
                                    <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                       data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"menu","field":"ceo/desc","wr_id":"<?php echo $row['wr_id']; ?>","menu_id":"<?php echo $menu_id?>"}'
                                       data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="fs-[14/100%/-0.56/600/#97989C]"> <img src="<?php echo $this->manager->plugin_url; ?>/img/vec2.png" class="w-[14px]" alt=""> <span>변경</span></a>
                                </div>

                                <div class="mt-[20px]" >
                                    <p class="fs-[14/20/-0.56/500/#0D171B]"><?php echo $row['desc']?get_text($row['desc']):'미입력'; ?></p>
                                </div>


                            </div>
                            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                            <div class="col-box" style="row-gap: var(--wv-20)">
                                <div class="hstack justify-content-between">
                                    <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">메뉴 이미지</p>
                                    <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                       data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"menu","field":"ceo/images","wr_id":"<?php echo $row['wr_id']; ?>","menu_id":"<?php echo $menu_id?>"}'
                                       data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="fs-[14/100%/-0.56/600/#97989C]"> <img src="<?php echo $this->manager->plugin_url; ?>/img/vec2.png" class="w-[14px]" alt=""> <span>변경</span></a>
                                </div>

                                <div class="mt-[20px]" >
                                    <div class="row" style="--bs-gutter-x: var(--wv-10);--bs-gutter-y: var(--wv-10)">
                                        <?php foreach ($row['images'] as $img){ ?>
                                            <div class="col-4">
                                                <div class=" ratio-1x1 ratio">
                                                    <div class=" ">
                                                        <span class="wv-ps-num">1</span>
                                                        <img src="<?php echo $img['path']; ?>" class="wh-100 object-fit-cover " style="border-radius: var(--wv-4);overflow: hidden" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>


                            </div>
                            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                        </div>
                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]  ">


                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' style="border:1px solid #fc5555;border-radius: var(--wv-4);background-color: #fff"
                           class="w-full h-[54px] fs-[16/22/-0.64/700/#FC5555] wv-flex-box  transition menu-delete  "
                           data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"menu","field":"ceo/delete","wr_id":"<?php echo $row['wr_id']; ?>","menu_id":"<?php echo $menu_id?>"}'
                           data-wv-ajax-option="offcanvas,bottom,backdrop,class: w-[360px] rounded-t-[4px],reload_ajax:true" >메뉴 삭제하기</a>

                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");

            $("form", $skin).ajaxForm({
                reload:true,

            })
        })
    </script>
</div>
