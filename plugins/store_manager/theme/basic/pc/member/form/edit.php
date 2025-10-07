<?php
global $g5;
global $current_member_wr_id;
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
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">개인정보 수정</p>
                                </div>
                                <div class="col text-end">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col py-0">

                        <?php echo $this->store->member->render_part('profile_image','form'); ?>

                        <div class="fs-[14/20/-0.56/600/#97989C]">
                        <!-- 닉네임 -->
                        <div class="wv-mx-fit" style="height: var(--wv-1);background-color: #efefef"></div>
                            <div class="position-relative" style="padding: var(--wv-16) var(--wv-20);">
                                <div class="hstack" style="gap:var(--wv-8)">
                                    <p class="fs-[16/22/-0.64/600/#0D171B]">닉네임</p>
                                    <p class="ms-auto"><?php echo get_text($this->store->member->mb_mb_nick); ?></p>
                                    <i class="fa-solid fa-chevron-right lh-0 fs-07em"></i>
                                </div>
                                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                   data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"edit_mb_nick","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="stretched-link" data-wv-offcanvas="member_nick_edit"> </a>
                            </div>
                            <div class="wv-mx-fit" style="height: var(--wv-1);background-color: #efefef"></div>
                            <div class="position-relative" style="padding: var(--wv-16) var(--wv-20);">
                                <div class="hstack" style="gap:var(--wv-8)">
                                    <p class="fs-[16/22/-0.64/600/#0D171B]">이름</p>
                                    <p class="ms-auto"><?php echo get_text($this->store->member->mb_mb_name); ?></p>
                                    <i class="fa-solid fa-chevron-right lh-0 fs-07em"></i>
                                </div>
                                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                   data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"edit_mb_name","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="stretched-link" data-wv-offcanvas="member_nick_edit"> </a>
                            </div>
                            <div class="wv-mx-fit" style="height: var(--wv-1);background-color: #efefef"></div>
                            <div class="position-relative" style="padding: var(--wv-16) var(--wv-20);">
                                <div class="hstack" style="gap:var(--wv-8)">
                                    <p class="fs-[16/22/-0.64/600/#0D171B]">휴대폰 번호 변경</p>
                                     <i class="fa-solid fa-chevron-right lh-0 fs-07em ms-auto"></i>
                                </div>
                                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                   data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"edit_mb_hp","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="stretched-link" data-wv-offcanvas="member_nick_edit"> </a>
                            </div>
                            <div class="wv-mx-fit" style="height: var(--wv-1);background-color: #efefef"></div>
                        </div>

                    </div>
                    <div class="wv-mx-fit">
                        <?php echo wv_widget('content/privacy_notice'); ?>
                    </div>

                    <div class="wv-mx-fit" style="background-color: #f9f9f9"><div class="container"><div  style="height: 2px;background-color: #efefef"></div></div></div>

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
        })
    </script>
</div>
