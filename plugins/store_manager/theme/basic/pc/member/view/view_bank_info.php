<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100   bg-white overflow-x-hidden" <?php echo wv_display_reload_data($reload_ajax_data);?> style="">
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
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">출금 계좌 설정</p>
                                </div>
                                <div class="col text-end">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col">
                        <?php if($row['is_bank_register'] ){ ?>
                                <p class="fs-[14/20/-0.56/600/#97989C]">등록된 계좌</p>
                            <div class="mt-[12px]   border" style="border-radius: var(--wv-4)"  >
                                <div style="padding: var(--wv-12)" class="hstack">
                                    <div class="w-[56px] h-[56px]">
                                        <img src="<?php echo $this->store->member->bank_image; ?>" class="wh-100 object-fit-contain" alt="">
                                    </div>
                                    <div class="col">
                                        <div class="hstack col">
                                            <p class="fs-[16/22/-0.64/700/#0D171B] col"><?php echo $this->store->member->bank_name; ?></p>
                                            <p class="col-auto fs-[11/15/-0.44/500/#97989C]">등록한 날짜 <span class="ff-Roboto"><?php echo date('Y.m.d' ,strtotime($this->store->member->bank_datetime)); ?></span></p>
                                        </div>
                                        <p class="fs-[12/17/-0.48/500/#0D171B] mt-[2px]"><?php echo $this->store->member->bank_account_number; ?></p>
                                    </div>
                                </div>
                                <div class=" " style="height: var(--wv-1);background-color: #efefef"></div>
                                <div class="fs-[11/18/-0.44/500/#97989C]" style="padding: var(--wv-12)">
                                    입력하신 계좌 정보는 본인 인증을 통해 확인된 이름 및 정보와의 일치 <br>
                                    여부를 기준으로 출금 승인이 되며, 예금주 이름과 본인 인증 정보가   <br>
                                    일치하지 않을 경우 출금이 제한될 수 있습니다.
                                </div>
                            </div>

                            <a href="" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"form_bank","wr_id":"<?php echo $row['wr_id']; ?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="wv-flex-box h-[45px] border fs-[12/17/-0.48/600/#0D171B] w-100 mt-[20px]" >
                                <img src="<?php echo wv_store_manager_img_url(); ?>/u_pen.png" class="w-[18px]" alt="">
                                계좌 변경하기
                            </a>
                        <?php }else{ ?>
                        <div>
                            <p class="fs-[14/20/-0.56/600/#97989C]">등록된 계좌가 없습니다</p>
                            <a href="" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"form_bank","wr_id":"<?php echo $row['wr_id']; ?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true"  class="wv-flex-box h-[45px] border fs-[12/17/-0.48/600/#0D171B] w-100 mt-[20px]" >
                                <img src="<?php echo wv_store_manager_img_url(); ?>/u_plus.png" class="w-[18px]" alt="">
                                계좌 등록하기
                            </a>
                        </div>
                        <?php } ?>


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