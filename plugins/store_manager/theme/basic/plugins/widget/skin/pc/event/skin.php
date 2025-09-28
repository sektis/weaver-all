<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_member,$member_manager,$current_member_wr_id;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .event-toggle-tab{position: relative;transition: .4s}
        <?php echo $skin_selector?> .event-toggle-tab:not(.active){color: #cfcfcf!important;}
        <?php echo $skin_selector?> .event-toggle-tab .cont-under-line{opacity:0}
        <?php echo $skin_selector?> .event-toggle-tab.active .cont-under-line{opacity: 1}
        <?php echo $skin_selector?> .ranking-toggle-tab:not(.active){color:#97989C!important;}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="">
        <div class="hstack menu-tab-top wv-mx-fit" role="tablist">
            <a href="#"   class="active  fs-[14/20/-0.56/600/] col transition h-[42px] d-flex-center event-toggle-tab" data-bs-toggle="tab" data-bs-target="#event-1" style="<?php echo $cont['item']['color_type_text'];?>">
                친구 초대
                <span class="cont-under-line position-absolute   start-0 w-100 h-[2px]" style="bottom:-1px;background-color: #0d171b" ></span>
            </a>
            <a href="#"   class="  fs-[14/20/-0.56/600/] col transition h-[42px] d-flex-center event-toggle-tab" data-bs-toggle="tab" data-bs-target="#event-2" style="<?php echo $cont['item']['color_type_text'];?>">
                매장 방문 인증
                <span class="cont-under-line position-absolute   start-0 w-100 h-[2px]" style="bottom:-1px;background-color: #0d171b" ></span>
            </a>
        </div>
        <div class="wv-mx-fit" style="height: 1px;background-color: #efefef"></div>
        <div class="tab-content menu-tab-content wv-mx-fit" id="myTabContent">
            <div class="tab-pane fade show active" id="event-1" >
                <div class="tab-pane-inner container  ">
                   <div class="wv-mx-fit">
                       <img src="<?php echo $wv_skin_url; ?>/ban1.png" class="w-100" alt="">
                   </div>
                    <div class="mt-[20px]">
                        <p class="fs-[14/20/-0.56/600/#0D171B]">나의 초대 코드</p>
                        <?php echo $current_member->member->render_part('my_code','view'); ?>
                    </div>
                    <div class="wv-mx-fit mt-[20px]" style="height: 1px;background-color: #efefef"></div>
                    <div class="h-[45px] hstack position-relative" style="gap:var(--wv-4)">
                        <img src="<?php echo $member_manager->plugin_url; ?>/img/peoples.png" class="w-[18px]" alt="">
                        <p class="fs-[12/17/-0.48/600/#0D171B]">나의 초대현황 확인하기 </p>
                        <img src="<?php echo $member_manager->plugin_url; ?>/img/arrow_right_gray_big.png" class="w-[24px] ms-auto" alt="">
                        <a href=""  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                           data-wv-ajax-data='{ "action":"view","made":"invite","part":"invite","field":"my_invite","current_member_wr_id":"<?php echo $current_member_wr_id; ?>"}'
                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px],reload_ajax:true" class="stretched-link"></a>
                    </div>
                    <div class="wv-mx-fit " style="height: var(--wv-6);background-color: #efefef"></div>
                    <div class="mt-[30px]">
                        <div class="hstack">
                            <div class="event-tab-title">
                                <p class="fs-[18/25/-0.72/600/#0D171B]"><span class="text-[#6c7df3]"><?php echo date('m',time()); ?>월</span> 실시간랭킹</>
                            </div>


                            <div class="hstack ms-auto fs-[14/20/-0.56/500/#0D171B]" role="tablist" style="gap:var(--wv-14)">
                                <a href="#"   class="active   ranking-toggle-tab" data-bs-toggle="tab" data-bs-target="#ranking-1" data-title-html='<p class="fs-[18/25/-0.72/600/#0D171B]"><span class="text-[#6c7df3]"><?php echo date('m',time()); ?>월</span> 실시간랭킹</>' style="<?php echo $cont['item']['color_type_text'];?>">
                                    이번 달 랭킹
                                </a>
                                <a href="#"   class="   ranking-toggle-tab" data-bs-toggle="tab" data-bs-target="#ranking-2" data-title-html='<p class="fs-[18/25/-0.72/600/#0D171B]"><span class="text-[#6c7df3]">누적</span> 실시간랭킹</>' style="<?php echo $cont['item']['color_type_text'];?>">
                                    누적 랭킹
                                </a>
                            </div>
                        </div>

                        <div class="tab-content menu-tab-content " id="myTabContent">
                            <div class="tab-pane fade show active" id="ranking-1" >
                                <div class="tab-pane-inner  pt-[20px]">
                                    <?php echo wv()->store_manager->made('invite')->invite->render_part('ranking_month','view'); ?>
                                </div>
                            </div>
                            <div class="tab-pane  " id="ranking-2" >
                                <div class="tab-pane-inner  pt-[20px]">
                                    <?php echo wv()->store_manager->made('invite')->invite->render_part('ranking_all','view'); ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="mt-[30px] wv-mx-fit">
                        <?php echo wv_widget('content/invite_notice'); ?>
                    </div>

                    <div class="wv-mx-fit">
                        <?php echo wv_widget('content/copyright'); ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane  " id="event-2" >
                <div class="tab-pane-inner  container">
                    <div class="wv-mx-fit">
                        <img src="<?php echo $wv_skin_url; ?>/ban2.png" class="w-100" alt="">
                    </div>

                    <div class="mt-[30px]">
                        <p class="fs-[18/25/-0.72/600/#0D171B]">
                            어느 가게를 <br>방문 및 이용하셨나요?
                        </p>
                        <div class="mt-[16px] position-relative">
                            <div class="  h-[43px] rounded-[4px] border-[1px] border-solid border-[#19BBC0] hstack justify-content-between" style="padding: 0 var(--wv-12)">
                                <p class="fs-[14/20/-0.56/500/#97989C]">인증 가능 가게를 확인해보세요</p>
                                <img src="<?php echo wv()->store_manager->plugin_url; ?>/img/search_green.png" class="w-[20px]" alt="">

                            </div>
                            <a href="#"  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"view","made":"sub01_01","part":"store","field":"cert_enable_stores","wr_id":"<?php echo $current_member_wr_id; ?>" }'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="stretched-link"></a>
                        </div>

                    </div>
                    <div class="mt-[30px] wv-mx-fit">
                        <?php echo wv_widget('content/cert_notice'); ?>
                    </div>

                    <div class="wv-mx-fit">
                        <?php echo wv_widget('content/copyright'); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
                $skin.on('show.bs.tab','[data-bs-toggle="tab"]',function (e) {
                    var html = $(e.target).data('title-html');
                    $(".event-tab-title",$skin).html(html)
                })


        })

    </script>
</div>