<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $member;

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap"  style="">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full   " style=" ">

        <div >
            <div class="row" style="--bs-gutter-x: var(--wv-12)">
                <div class="col-auto">
                    <div class="w-[80px] h-[80px] overflow-hidden" style="border-radius: var(--wv-4);">
                        <img src="<?php echo $row['main_image']; ?>" class="object-fit-cover" alt="">
                    </div>
                </div>
                <div class="col">
                    <div class="vstack  h-100" style="padding: var(--wv-3) 0">
                        <div class="hstack" style="gap:var(--wv-10)">
                            <div>
                                <div class="hstack">

                                    <p class="fs-[14//-0.56/700/#0D171B] col text-truncate"><?php echo $row['name']; ?></p>
                                    <?php if($row['distance_km']){ ?>
                                        <p class="fs-[12//-0.48/500/#0D171B]  col-auto">(<?php echo number_format($row['distance_km'],2); ?>km)</p>
                                    <?php } ?>
                                </div>

                                <p class="fs-[12//-0.48/500/#0D171B] mt-[2px]"><?php echo $row['category_item']['name']; ?></p>

                            </div>

                            <div class="ms-auto align-self-start">
                                <?php
                                // 현재 로그인한 회원의 찜 여부 조회 (간단하게!)
                                $favorite_manager = wv()->store_manager->made('favorite_store');
                                $wr_ids = $favorite_manager->get_wr_id_of_member($member['mb_id'], array('favorite' => array('store_wr_id' => $row['wr_id'])));

                                // 찜하기 버튼 렌더링
                                echo $favorite_manager->get($wr_ids['wr_id'])->favorite->render_part('favorite','view',array(
                                    'store_wr_id' => $row['wr_id']
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="hstack mt-auto" style="gap:var(--wv-2);filter: brightness(0) saturate(100%) invert(62%) sepia(1%) saturate(1638%) hue-rotate(204deg) brightness(97%) contrast(93%);">
                            <img src="<?php echo WV_URL.'/img/icon_location.png'; ?>"   class="w-[12px]" alt="">
                            <p class="fs-[12//-0.48/500/#97989C]"><?php echo $this->store->location->address_name_full; ?></p>
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
        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
           data-wv-ajax-data='{ "action":"view","made":"sub01_01","part":"store","field":"detail","wr_id":"<?php echo $row['wr_id']; ?>","contractitem_wr_id":"<?php echo $contractitem_wr_id?>"}'
           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true" class="stretched-link"></a>

    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>