<div class="container" style="background-color: #f9f9f9">
    <div style="padding: var(--wv-16) 0">
        <div class="hstack justify-content-between">
            <p class="fs-[14/20/-0.56/500/#0D171B]">진행 중인 <span class="fs-[14/17/-0.56/700/#0D171B] ff-montserrat">DUM</span> 서비스</p>
            <a href="#"  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
               data-wv-ajax-data='{ "action":"view","made":"sub01_01","part":"contract","field":"service_option_list","wr_id":"<?php echo $current_store_wr_id; ?>"}'
               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="fs-[11/15/-0.44/500/#97989C] hstack" style="gap:var(--wv-4)">서비스 옵션 <i class="fa-solid fa-chevron-right fs-08em"></i></a>
        </div>
        <div class="mt-[12px]">
            <?php echo wv()->store_manager->made('sub01_01')->get($current_store_wr_id)->contract->render_part('service_config_list','view'); ?>
        </div>
    </div>
</div>