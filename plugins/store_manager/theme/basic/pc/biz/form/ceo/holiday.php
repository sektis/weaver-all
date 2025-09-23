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
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">

                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0"  >


                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                                </div>

                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">휴무일</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>

                    </div>
                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                    <div class="wv-offcanvas-body col"   >


                        <div class=" h-100 vstack">
                            <?php echo $this->store->biz->render_part('is_holiday_off','form');  ?>

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
                // reload:true,
                // success: function () {
                //
                //     var offcanvasId = $skin.closest('.wv-offcanvas').attr('id');
                //     if(offcanvasId){
                //         wv_reload_offcanvas(offcanvasId);
                //         return true;
                //     }
                //
                // }
            })
        })
    </script>
</div>