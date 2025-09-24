<?php
global $g5,$current_member_wr_id;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .skin-box{padding: var(--wv-30) 0}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if($is_list_item_mode){ ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">
                    <div>
                        <div class="hstack" style="gap:var(--wv-1)">
    
                            <p class="fs-[18/25/-0.72/700/#0D171B] "><?php echo $row['mb_mb_name']; ?></p>
                            <span class="fs-[16/22/-0.64/600/#0D171B] col">님</span>
    
                            <a href=""  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"ceo/account_config","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]"  class="fs-[12/17/-0.48/500/#97989C] ms-auto    ">
                                계정설정  <i class="fa-solid fa-angle-right ms-auto"></i>
                            </a>
                        </div>
                        <p class="fs-[12/17/-0.48/500/#97989C] ff-montserrat">@<?php echo $row['mb_mb_id']; ?></p>
                    </div>

                    <div class="wv-mx-fit fs-[12/17/-0.48/500/#0D171B] mt-[12px]">
                        <div class="row" style="--bs-gutter-x: var(--wv-8)">
                            <div class="col">
                                <div class="vstack align-items-center h-[90px] position-relative justify-content-center" style="row-gap: var(--wv-5)">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/customer.png" class="w-[24px]" alt="">
                                    <p>고객센터</p>
                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <div style="width: 1px;height: var(--wv-40);background-color: #efefef"></div>
                            </div>
                            <div class="col">
                                <div class="vstack align-items-center h-[90px] position-relative justify-content-center" style="row-gap: var(--wv-5)">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/horn.png" class="w-[24px]" alt="">
                                    <p>시스템 공지</p>
                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <div style="width: 1px;height: var(--wv-40);background-color: #efefef"></div>
                            </div>
                            <div class="col">
                                <div class="vstack align-items-center h-[90px] position-relative justify-content-center" style="row-gap: var(--wv-5)">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/book.png" class="w-[24px]" alt="">
                                    <p>이용가이드</p>
                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit mt-[4px]" style="height: 10px;background-color: #efefef"></div>

                    <div class="skin-box">
                        <p class="fs-[14/20/-0.56/600/#97989C]">관리자 관리</p>
                        <div class=" mt-[20px]">
                            <a href="#"   data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"ceo/account_info","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]"  class="fs-[16/22/-0.64/600/#0D171B]">
                                관리자 정보
                            </a>
                        </div>
                    </div>
                    <div class="skin-box">
                        <p class="fs-[14/20/-0.56/600/#97989C]">서비스 설정</p>
                        <div class="mt-[20px]">
                            <a href="#"  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"ceo/app_config","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]"  class="fs-[16/22/-0.64/600/#0D171B]">
                                앱 설정
                            </a>
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
                // reload:false,
                // // reload_ajax:true,
                // success: function () {
                //     var $offcanvas =  $skin.closest('.wv-offcanvas');
                //     $offcanvas.offcanvas('hide');
                // }
            })
        })
    </script>
</div>