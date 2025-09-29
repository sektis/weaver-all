<?php
global $g5;
global $member;
if($member['mb_id']){

    $member_manager = wv()->store_manager->made('member');
    $member_manager_table = $member_manager->get_write_table_name();
    $row = sql_fetch("select wr_id from {$member_manager_table} where mb_id='{$member['mb_id']}'");
    $current_member_wr_id = $row['wr_id'];
    $current_member = $member_manager->get($current_member_wr_id);


}
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

                                    <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data" autocomplete="new-password">
                                        <div class="position-relative">
                                            <label for="q" class="visually-hidden"></label>
                                            <input type="text" name="q" id="q" class="h-[32px] form-control border-0" style="border-radius: var(--wv-4);background-color: #f9f9f9;padding:0 var(--wv-12)" placeholder="인증 가능 가게 확인하기" autocomplete="new-password">
                                            <button class="btn outline-none position-absolute top-50 translate-middle-y cursor-pointer" style="right: var(--wv-6);"><img src="<?php echo $this->manager->plugin_url; ?>/img/search_black.png" class="w-[20px]" alt=""></button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>



                    <div class="wv-offcanvas-body col">

                        <div>
                            <p class="fs-[14/20/-0.56/600/#0D171B]">최근 검색</p>
                            <div class="mt-[12px]">
                                <?php echo $current_member->member->render_part('search_store_history','form'); ?>
                            </div>
                        </div>

                        <div class="mt-[25px]">
                            <div class="hstack">
                                <img src="<?php echo $this->manager->plugin_url; ?>/img/location.png" class="w-[16px]" alt="">
                                <p class="fs-[14/20/-0.56/600/]"><?php echo $current_member->mb_name; ?>님 <span class="text-[#19BBC0]">주변</span> 인증 가능한 매장</p>
                            </div>
                            <div class="mt-[24px] cert_store_list_wrap">
                                <?php echo wv_widget('cert_store_list'); ?>
                            </div>
                        </div>

                    </div>


                </div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).submit(function (e) {
                e.preventDefault();
                wv_ajax('<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php','replace_in:.cert_store_list_wrap',{action:'widget',widget:'cert_store_list',q:$('[name=q]',$skin).val()})
            })
        })
    </script>
</div>