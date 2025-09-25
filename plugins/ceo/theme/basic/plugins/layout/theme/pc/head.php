

<?php echo wv_widget('common/fixed_quick');?>

<div id="header-wrapper" class="">
    <div id="header-menu" class=""  >
        <div class="container">
            <div class="hstack  flex-lg-nowrap justify-content-center" style="gap: var(--wv-md-31,var(--wv-30))"   >
                <div class="col-auto align-self-center">
                    <?php if($wv_page_id=='main'){ ?>


                            <div data-wv-ajax-url="<?php echo wv()->store_manager->ajax_url(); ?>" class="cursor-pointer"
                                 data-wv-ajax-data='{"action":"widget","widget":"ceo/select_store"}'
                                 data-wv-ajax-option="offcanvas,bottom,backdrop-static"  >
                                <?php echo wv_widget('ceo/stores_display');; ?>
                            </div>

                    <?php }else{ ?>
                        <p class="fs-[22/130%/-0.88/700/#0D171B]"><?php echo str_replace('DUM ','',strip_tags(wv('menu')->made('fixed_bottom')->getMenu(wv('menu')->made('fixed_bottom')->getActiveMenuId())['name'])); ?></p>
                    <?php } ?>
                </div>




                <div class="col-auto ms-auto  ">

                    <div class="hstack" style="gap:var(--wv-12)">
                        <a href=""><img src="<?php echo WV_URL.'/img/icon_alarm.png'; ?>" class="w-[28px]" alt=""></a>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>






