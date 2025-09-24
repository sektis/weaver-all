<?php
global $g5; 
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white " style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
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
                <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[is_main]" value="<?php echo $row['is_main']?0:1; ?>">
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">



                        <div class="wv-offcanvas-body col"   >

                            <div class="col-box" style="row-gap: var(--wv-20)">
                                <?php if($row['is_main']){ ?>
                                <p class="fs-[20/28/-0.8/600/]" style="line-height: var(--wv-31)">일반 메뉴로 변경할까요?</p>
                                <p class="fs-[14/20/-0.56/500/#97989C] mt-[6px]">대표메뉴 표시를 해제하면 이 메뉴는 일반메뉴로 노출됩니다. <br>계속하시겠어요?</p>
                                <?php }else{ ?>
                                    <p class="fs-[20/28/-0.8/600/]" style="line-height: var(--wv-31)">대표 메뉴로 변경할까요?</p>
                                    <p class="fs-[14/20/-0.56/500/#97989C] mt-[6px]">대표메뉴 표시를 선택하면 이 메뉴는 대표메뉴로 노출됩니다. <br>계속하시겠어요?</p>
                                <?php } ?>

                                 
                            </div>

                            <div  style="padding: var(--wv-12) var(--wv-16);background-color: #f9f9f9;border-radius: var(--wv-4)">
                                <p class="fs-[14/20/-0.56/500/#97989C]">변경하려는 메뉴 :</p>
                                <p class="fs-[16/22/-0.64/600/#0D171B] mt-[2px]"><?php echo $row['name']; ?></p>
                            </div>

                        </div>

                        <div class="mt-[29px] col-auto pb-[50px] hstack gap-[6px]">
                            <button type="button" data-bs-dismiss="offcanvas" class="  h-[54px] fs-[16/22/-0.64/700/#FFF] w-[130px] col-auto  transition " style="border:0;border-radius: var(--wv-4);background-color: #cfcfcf">취소</button>
                            <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] col wv-submit-btn  transition " style="border:0;border-radius: var(--wv-4); ">변경하기</button>
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