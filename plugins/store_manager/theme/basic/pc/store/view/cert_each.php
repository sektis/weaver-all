<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full   " style=" ">

        <div >
            <div class="row align-items-center" style="--bs-gutter-x: var(--wv-12)">
                <div class="col-auto">
                    <div class="w-[50px] h-[50px] overflow-hidden" style="border-radius: var(--wv-4);">
                        <img src="<?php echo $row['main_image']; ?>" class="object-fit-cover" alt="">
                    </div>
                </div>
                <div class="col">
                    <div class="vstack  h-100" style="padding: var(--wv-3) 0">
                        <div class="hstack align-items-center">
                            <div>
                                <div class="hstack">
                                    <p class="fs-[14//-0.56/700/#0D171B]"><?php echo $row['name']; ?></p>
                                    <?php if($row['distance_km']){ ?>
                                        (<p class="fs-[12//-0.48/500/#0D171B] mt-[2px]"><?php echo number_format($row['distance_km'],2); ?>km</p>)
                                    <?php } ?>
                                </div>

                                <div class="hstack fs-[12/17/-0.48/500/#989898]" style="gap:var(--wv-4)">
                                    <p class="text-[#0d171b]"><?php echo $row['category_item']['name']; ?></p>
                                    <span>•</span>
                                    <p ><?php echo $this->store->location->region_2depth_name; ?> <?php echo $this->store->location->region_3depth_name; ?></p>
                                </div>


                            </div>

                            <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"visit_cert","part":"visitcert","field":"add","wr_id":"","store_wr_id":"<?php echo $row['wr_id']?>"}'
                               data-wv-ajax-option='offcanvas,end,backdrop,class: w-[360px],ajax_option:{"use_redirect":"true"}' class="ms-auto wv-flex-box h-[29px] fs-[12/17/-0.48/600/#0D171B]" style="background-color: #f9f9f9">인증하기</a>
                        </div>


                    </div>

                </div>
            </div>

            <?php if($contractitem_wr_id) {
                $contract = wv_get_keys_by_nested_value($this->store->contract->list,$contractitem_wr_id,true,'contractitem_wr_id');
                $contract_key = key($contract);
                $contract_row = reset($contract);
                if($contract_row['service_content']){
                    ?>
                    <div class="mt-[8px]" style="border-bottom: 1px solid #efefef"></div>
                    <div class="mt-[12px]"><?php  echo $this->store->contract->render_part('service','view',array('contract_id'=>$contract_key)) ?></div>
                <?php }
            }?>

        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>