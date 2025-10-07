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
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if ($is_list_item_mode) { ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">(사장님) <?php echo $row['wr_id']?'수정':'신규등록' ?></p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col">


                        <input type="hidden" name="member[is_ceo]" value="1">
                        <?php echo $this->store->member->render_part('mb_id','form'); ?>
                        <?php echo $this->store->member->render_part('mb_password','form'); ?>
                        <?php echo $this->store->member->render_part('mb_name','form'); ?>
                        <?php echo $this->store->member->render_part('admin/mb_hp','form'); ?>
                        <?php echo $this->store->member->render_part('mb_email','form'); ?>
                    </div>


                    <div class="mt-auto pb-[50px]">
                        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition mt-[22px]" style="border:0; ;border-radius: var(--wv-4)">확인</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).ajaxForm({

            })
        })
    </script>
</div>