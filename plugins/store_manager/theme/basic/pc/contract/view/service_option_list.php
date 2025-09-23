<?php
global $g5;
$result = wv()->store_manager->made('contract_item')->get_list(array( 'order_by'   => 'w.wr_id asc',));
$rows = $result['list'];

$free_items = (wv_get_keys_by_nested_value($rows,1,true,'contractitem','is_free'));
$non_free_items = (wv_get_keys_by_nested_value($rows,0,true,'contractitem','is_free'));
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .col-box{padding: var(--wv-30) 0}

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
                <div class="vstack h-100 " style="padding-top:var(--wv-10)">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0"  >


                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>

                                </div>

                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">서비스 옵션</p>
                                </div>
                                <div class="col"></div>
                            </div>

                        </div>

                    </div>

                    <div class="wv-offcanvas-body col"   >
                        <p class="fs-[14/20/-0.56/500/#97989C] mt-[24px]">등록 가능한 모든 상품을 확인해보세요</p>
                        <div class="col-box">
                            <p class="fs-[16/22/-0.64/600/#0D171B]">구독 플랜</p>
                            <div class="mt-[10px] vstack" style="row-gap: var(--wv-12)">
                                <?php foreach ($non_free_items as $it){
                                    echo wv()->store_manager->made('contract_item')->get($it['wr_id'])->contractitem->render_part('option','view',array('store_wr_id'=>$row['wr_id'], 'cont'=>reset(wv_get_keys_by_nested_value($row['contract'],$it['wr_id'],true,'contractitem_wr_id'))));
                                } ?>

                            </div>
                        </div>
                        <div class="wv-mx-fit" style="height: 10px;background-color: #efefef"></div>
                        <div class="col-box">
                            <p class="fs-[16/22/-0.64/600/#0D171B]">프리 플랜<span class="fs-[16/22/-0.64/600/#97989C]">(기본 제공)</span></p>
                            <div class="mt-[10px] vstack" style="row-gap: var(--wv-12)">
                                <?php foreach ($free_items as $it){

                                    echo wv()->store_manager->made('contract_item')->get($it['wr_id'])->contractitem->render_part('option','view',array('store_wr_id'=>$row['wr_id'], 'cont'=>reset(wv_get_keys_by_nested_value($row['contract'],$it['wr_id'],true,'contractitem_wr_id'))));
                                } ?>
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

            })
        })
    </script>
</div>