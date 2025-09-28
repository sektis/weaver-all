<?php
global $g5;

?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {
        }

        @media (max-width: 991.98px) {
        }
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="view">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if ($is_list_item_mode) { ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center " style="--bs-gutter-x: var(--wv-10)" >
                                <div class="col-auto">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col text-center">
                                    <div class="position-relative">
                                        <input type="text" class="h-[32px] form-control border-0" style="border-radius: var(--wv-4);background-color: #f9f9f9;padding:0 var(--wv-12)" placeholder="인증 가능 가게 확인하기">
                                        <button class="btn outline-none position-absolute top-50 translate-middle-y cursor-pointer" style="right: var(--wv-6);"><img src="<?php echo $this->manager->plugin_url; ?>/img/search_black.png" class="w-[20px]" alt=""></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>



                    <div class="wv-offcanvas-body col">

                        <div>
                            <p class="fs-[14/20/-0.56/600/#0D171B]">최근 검색</p>
                        </div>

                        <div class="mt-[25px]">
                            <div class="hstack">
                                <img src="<?php echo $this->manager->plugin_url; ?>/img/location.png" class="w-[16px]" alt="">
                                <p class="fs-[14/20/-0.56/600/]"><?php echo $current_member->memeber->mb_mb_name; ?>님 <span class="text-[#19BBC0]">주변</span> 인증 가능한 매장</p>
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