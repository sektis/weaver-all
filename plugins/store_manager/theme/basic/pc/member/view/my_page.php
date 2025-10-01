<?php
global $g5,$current_member_wr_id;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .skin-box{position:relative;padding: var(--wv-12);display: flex;flex-direction: column;background-color: #fff;border-radius: var(--wv-4);height: 100% }
        <?php echo $skin_selector?> .skin-box img{margin-top: auto }

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if($is_list_item_mode){ ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]",'',$field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">
                    <div>
                        <div class="hstack" style="gap:var(--wv-12)">

                            <div class="col-auto ratio ratio-1x1 wv-ratio-circle w-[60px] overflow-hidden">
                                <div>
                                    <img src="https://picsum.photos/1920/700" class="wh-100 object-fit-cover" alt="">
                                </div>

                            </div>
                            <div class="col">
                                <div class="hstack">
                                    <p class="fs-[18/25/-0.72/700/#0D171B] "><?php echo $row['mb_mb_name']; ?></p>
                                    <span class="fs-[16/22/-0.64/600/#0D171B] col">님</span>
                                </div>
                                <p class="fs-[12/17/-0.48/500/#97989C] ff-montserrat">@<?php echo $row['mb_mb_id']; ?></p>
                            </div>
                            <a href=""  data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                               data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"edit","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]"  class="fs-[12/17/-0.48/500/#97989C] ms-auto  col-auto  ">
                                개인정보 수정  <i class="fa-solid fa-angle-right ms-auto"></i>
                            </a>
                        </div>

                    </div>

                    <div class="wv-flex-box fs-[12/17/-0.48/600/#0D171B] w-full h-[34px] mt-[12px]" style="background-color: #f8f8f8;padding:0 var(--wv-8)">
                        <img src="<?php echo $this->manager->plugin_url; ?>/img/icon_coin.png" class="w-[15px]" alt="">
                        <p>친구 초대 시, 1명당 1,000원  +  최대 30만원 보너스까지 지급</p>
                    </div>

                    <div class="mt-[17px] hstack" style="gap:var(--wv-6)">
                        <p class="fs-[14/20/-0.56/600/#0D171B]">MY 적립금</p>
                        <a href=""><img src="<?php echo $this->manager->plugin_url; ?>/img/u_exclamation-circle.png" class="w-[12px]" alt=""></a>
                    </div>

                    <div class="mt-[10px] hstack justify-content-between" style="gap:var(--wv-6)">
                        <p class="fs-[14/20/-0.56/600/#0D171B]"><?php echo $this->store->member->saving_price; ?></p>
                        <a href="" class="wv-flex-box h-[28px]" style="background-color: #f9f9f9;padding: 0 var(--wv-14)">출금신청</a>
                    </div>

                    <div class="wv-mx-fit" style="height: var(--wv-1);background-color: #efefef"></div>
                    <div class="hstack fs-[12/17/-0.48/600/#0D171B] wv-mx-fit ">
                        <a href="" class="col hstack justify-content-center" style="gap:var(--wv-4);padding: var(--wv-13)">
                            <img src="<?php echo $this->manager->plugin_url; ?>/img/bank-card-2-fill.png" class="w-[14px]" alt="">
                            출금 계좌 설정
                        </a>
                        <span class="col-auto" style="width: 1px;height: var(--wv-30);background-color: #efefef"></span>
                        <a href="" class="col hstack justify-content-center" style="gap:var(--wv-4);padding: var(--wv-13)">
                            <img src="<?php echo $this->manager->plugin_url; ?>/img/exchange-alt.png" class="w-[14px]" alt="">
                            적립/출금 내역
                        </a>
                    </div>
                    <div class="wv-mx-fit" style="height: var(--wv-2);background-color: #efefef"></div>


                    <div class="wv-mx-fit  " style="padding: var(--wv-20) var(--wv-16) var(--wv-120);background-color: #f9f9f9">
                         
                        <p class="fs-[18/25/-0.72/600/#0D171B]">나의 서비스</p>
                        <div class="row mt-[12px]" style="--bs-gutter-x: var(--wv-11)">
                            <div class="col-4">
                                <div class="skin-box  " style=" ">
                                    <span class="fs-[12/17/-0.48/600/#97989C]">나의</span>
                                    <p class="fs-[16/22/-0.64/600/#0D171B] mt-[4px]">찜한 <br>가게</p>
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/my_pgae_service_1.png" class="w-[44px] align-self-end" alt="">

                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="skin-box  " style=" ">
                                    <span class="fs-[12/17/-0.48/600/#97989C]">가게에 대한</span>
                                    <p class="fs-[16/22/-0.64/600/#0D171B] mt-[4px]">나만의<br>메모</p>
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/my_pgae_service_2.png" class="w-[44px] align-self-end" alt="">

                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="skin-box  " style=" ">
                                    <span class="fs-[12/17/-0.48/600/#97989C]">특가서비스</span>
                                    <p class="fs-[16/22/-0.64/600/#0D171B] mt-[4px]">덤&덤</p>
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/my_pgae_service_3.png" class="w-[44px] align-self-end" alt="">

                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="skin-box  " style=" ">
                                    <span class="fs-[12/17/-0.48/600/#97989C]">매장 방문</span>
                                    <p class="fs-[16/22/-0.64/600/#0D171B] mt-[4px]">인증 <br>내역</p>
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/my_pgae_service_4.png" class="w-[44px] align-self-end" alt="">

                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="skin-box  " style=" ">
                                    <span class="fs-[12/17/-0.48/600/#97989C]">친구</span>
                                    <p class="fs-[16/22/-0.64/600/#0D171B] mt-[4px]">초대 <br>현황</p>
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/my_pgae_service_5.png" class="w-[44px] align-self-end" alt="">

                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="skin-box  " style=" ">
                                    <span class="fs-[12/17/-0.48/600/#97989C]">관심</span>
                                    <p class="fs-[16/22/-0.64/600/#0D171B] mt-[4px]">동네 <br>소식</p>
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/my_pgae_service_6.png" class="w-[44px] align-self-end" alt="">

                                    <a href="#" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>

                        <p class="fs-[14/20/-0.56/600/#97989C] mt-[30px]">고객센터</p>
                        <div class="mt-[24px] vstack fs-[16/22/-0.64/600/#0D171B]" style="row-gap: var(--wv-24)">
                            <a href="#">자주 묻는 질문 (FAQ)</a>
                            <a href="#">1:1 문의</a>
                            <a href="#">덤이요 이용가이드</a>
                        </div>

                        <div class="mt-[30px]" style="height: var(--wv-2);background-color: #efefef"></div>

                        <p class="fs-[14/20/-0.56/600/#97989C] mt-[30px]">서비스 설정</p>
                        <div class="mt-[24px] vstack fs-[16/22/-0.64/600/#0D171B]" style="row-gap: var(--wv-24)">
                            <a href="#">앱 설정</a>
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
                // reload:false,
                // // reload_ajax:true,
                // success: function () {
                //     var $offcanvas =  $skin.closest('.wv-offcanvas');
                //     $offcanvas.offcanvas('hide');
                // }
            })
        })
    </script>
</div>