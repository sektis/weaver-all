<?php
global $g5;

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
                <?php if($is_list_item_mode){ ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">

                        <div class="wv-offcanvas-header col-auto">
                            <div class=" ">
                                <div class="row align-items-center g-0"  >


                                    <div class="col">
                                        <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>

                                    </div>

                                    <div class="col-auto text-center">
                                        <p class="fs-[14/20/-0.56/600/#0D171B]"> </p>
                                    </div>
                                    <div class="col"></div>
                                </div>

                            </div>

                        </div>
                        <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                        <div class="wv-offcanvas-body col"   >

                            <div class="col-box" style="row-gap: var(--wv-20)">
                                <p class="fs-[16/22/-0.64/600/#0D171B]" style="line-height: var(--wv-31)">메뉴명</p>
                                <div class="mt-[20px]" >
                                    <?php echo $this->store->menu->render_part('name','form',array('menu_id'=>$row['id'])); ?>
                                </div>

                                <div class="fs-[12/17/-0.48/500/#97989C] mt-[20px] vstack" style="row-gap:var(--wv-8)">
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴의 이름은 고객이 쉽게 이해할 수 있도록 간결하고 명확하게 <br>작성해 주세요.</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴와 관련된 이름만 사용해주세요.</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴명에 특수문자(예: !, @, # 등)는 사용하지 말아주세요.</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>메뉴명은 간결하게 작성되어야 하므로 20자 이내로 제한해주세요.</p></div>
                                </div>
                            </div>

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
                    $offcanvas.offcanvas('hide');
                }
            })
        })
    </script>
</div>