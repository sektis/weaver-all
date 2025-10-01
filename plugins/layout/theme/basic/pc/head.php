

<?php echo wv_widget('common/fixed_quick');?>

<div id="header-wrapper" class="">
    <div id="header-menu" class=""  >
        <div class="container">
            <div class="hstack  flex-lg-nowrap justify-content-center" style="gap: var(--wv-md-31,var(--wv-30));padding: var(--wv-7) 0"   >
                <div class="col-auto align-self-center">
                    <?php if($wv_page_id=='main'){ ?>
                        <a href="javascript:;"  class="hstack  "   data-wv-ajax-url="<?php echo wv()->location->ajax_url().'?wv_location_action=region'; ?>" data-wv-ajax-option="offcanvas,bottom,backdrop-static"  >

                            <p class="fs-[20/130%/-0.8/700/#0D171B]"><?php echo wv_widget('location/favorite_title') ?></p>

                        </a>
                    <?php }else{ ?>
                        <p class="fs-[22/130%/-0.88/700/#0D171B]"><?php echo str_replace('DUM ','',strip_tags(wv('menu')->made('fixed_bottom')->getMenu(wv('menu')->made('fixed_bottom')->getActiveMenuId())['name'])); ?></p>
                    <?php } ?>
                </div>




                <div class="col-auto ms-auto  ">

                    <div class="hstack" style="gap:var(--wv-12)">
                        <a href=""><img src="<?php echo WV_URL.'/img/icon_alarm.png'; ?>" class="w-[28px]" alt=""></a>
                        <a href="#"  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                           data-wv-ajax-data='{ "action":"view","made":"favorite_store","part":"favorite","field":"list","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true" ><img src="<?php echo WV_URL.'/img/icon_heart.png'; ?>" class="w-[28px]" alt=""></a>
                    </div>

                </div>
            </div>
            <?php if($wv_page_id!='main'){ ?>
                <a href="javascript:;"  class="hstack  " style="padding: var(--wv-6) 0 var(--wv-12);"  data-wv-ajax-url="<?php echo wv()->location->ajax_url().'?wv_location_action=region'; ?>" data-wv-ajax-option="offcanvas,bottom,backdrop-static"  >
                    <?php echo wv_widget('location/favorite_title_sub') ?>
                </a>
            <?php }  ?>
        </div>

    </div>

</div>






