<?php
global $g5;
$result = wv()->store_manager->made('contract_item')->get_list(array( 'order_by'   => 'w.wr_id asc',));
$rows = $result['list'];


?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap  " style=" ">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .col-box{padding: var(--wv-30) 0}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class=" ">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if($is_list_item_mode){ ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 " style="row-gap: var(--wv-12)">



                        <?php foreach ($rows as $it){
                            echo wv()->store_manager->made('contract_item')->get($it['wr_id'])->contractitem->render_part('config','view',array('store_wr_id'=>$row['wr_id'], 'cont'=>reset(wv_get_keys_by_nested_value($row['contract'],$it['wr_id'],true,'contractitem_wr_id'))));
                        } ?>

                 


                    <div class="fs-[12/17/-0.48/500/#97989C] wv-flex-box justify-content-start h-[35px] bg-[#efefef] position-relative" style="padding: var(--wv-9) var(--wv-12)">
                        <div><img src="<?php echo $this->manager->plugin_url; ?>/img/vec1.png" class="w-[14px]" alt=""></div>
                        <p>고객이 좋아하는 서비스가 궁금하다면?</p>
                        <i class="fa-solid fa-angle-right ms-auto"></i>
                        <a href="#" class="stretched-link"></a>
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