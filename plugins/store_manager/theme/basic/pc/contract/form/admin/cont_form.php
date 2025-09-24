<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{font-size: var(--wv-14);font-weight: 600;}
        <?php echo $skin_selector?> .text3{font-size: var(--wv-14);font-weight: 400;}
        <?php echo $skin_selector?> .text2{font-size: var(--wv-14);font-weight: 500;color:#97989C;width: var(--wv-60)}

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
                        <div class="row align-items-center">

                            <div class="col-auto">
                                <div class="vstack" style="row-gap: var(--wv-5)">
                                    <p><?php echo $this->store->store->name; ?></p>
                                    <p class="fs-[13/17/-0.52/500/#97989C]">업종 카테고리 | <?php echo $this->store->store->category_item['name']; ?></p>
                                </div>
                            </div>

                            <div class="col text-end">
                                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>

                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col"  >

                        <div class="vstack" style="row-gap: var(--wv-16)">



                            <div class="hstack">
                                <p class="text1 w-[75px]  " style="line-height: var(--wv-31)">매장운영자</p>
                                <div class="col">
                                    <?php echo $this->store->contract->render_part('mb_name','form'); ?>
                                </div>
                            </div>

                            <?php if($contract_id){ ?>
                                <div class="hstack align-items-start" >
                                    <p class="text1 w-[75px] " style="line-height: var(--wv-31)">계약 상태</p>
                                    <div class="col">
                                        <?php echo $this->store->contract->render_part('status','form',$vars); ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="hstack">
                                <p class="text1 w-[75px]" style="line-height: var(--wv-31)">계약상품</p>
                                <div class="col">
                                    <?php echo $this->store->contract->render_part('contractitem_wr_id','form',$vars); ?>
                                </div>
                            </div>

                            <div class="hstack">
                                <p class="text1 w-[75px]" style="line-height: var(--wv-31)">계약기간</p>
                                <div class="col">
                                    <?php echo $this->store->contract->render_part('start_end','form',$vars); ?>
                                </div>
                            </div>

                            <div class="hstack">
                                <p class="text1 w-[75px]" style="line-height: var(--wv-31)">계약 담당자</p>
                                <div class="col">
                                    <?php echo $this->store->contract->render_part('contractmanager_wr_id','form',$vars); ?>
                                </div>
                            </div>

                            <?php if($contract_id){ ?>
                                <a href="#"  class="wv-flex-box border h-[39px]"
                                   data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px],reload_ajax:true" data-wv-ajax-data='<?php echo json_encode($ajax_data); ?>' >
                                    <i class="fa-solid fa-plus"></i>
                                    <p class="fs-[14/17/-0.56/500/#0D171B]">계약추가</p>
                                </a>
                            <?php } ?>
                            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                            <div class="vstack" style="row-gap:var(--wv-10)">
                                <p class="text1">메모</p>
                                <?php echo $this->store->contract->render_part('memo','form',$vars); ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <?php if($row['id']) {?>
                            <?php
                            $post_data=array(
                                'wr_id'=>$row['wr_id'],
                            );
                            $post_data['contract'][$contract_id]['delete']=1;
                            $post_data['contract'][$contract_id]['id']=$row['id'];
                            ?>
                            <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php'
                               class="w-[125px] h-[46px] px-[19px] py-[10px] flex justify-center items-center gap-[4px] rounded-[4px] border-[1px] border-solid border-[#FC5555] fs-[14/20/-0.56/600/#FC5555] wv-data-list-delete-btn  "
                               data-wv-ajax-data='{"action":"update","made":"sub01_01","part":"contract"}'
                               data-wv-ajax-option='reload:ajax:true' data-wv-ajax-data-add='<?php echo json_encode($post_data); ?>'>계약 해지</a>
                        <?php } ?>
                        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">확인</button>
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

 v>