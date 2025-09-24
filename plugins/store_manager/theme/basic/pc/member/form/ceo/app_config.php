<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .skin-box{position:relative;padding: var(--wv-21) 0;display: flex;justify-content: space-between;font-size: var(--wv-14);font-weight: 600;line-height: var(--wv-20);letter-spacing: calc(var(--wv-0_56) * -1);}
        <?php echo $skin_selector?> .skin-box .value{color: #ff5f5a;font-size: var(--wv-12);font-weight: 500;line-height: var(--wv-17);letter-spacing: calc(var(--wv-0_48) * -1);}

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
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">앱 설정</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>



                    <div class="wv-offcanvas-body col">
 


                        <div class="vstack   mt-[24px]" style="row-gap:var(--wv-31)">
                            <div>
                                <div class="hstack justify-content-between" >
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">접근 권한 설정</p>
                                </div>
                                <div class="mt-[10px] vstack" style="">
                                   <div class="skin-box  ">
                                        <p>사진/카메라  </p>
                                       <span class="value">전체 접근 허용</span>
                                   </div>
                                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                                    <div class="skin-box  ">
                                        <p>위치 서비스   </p>
                                        <span class="value">앱 사용 시 에만</span>
                                    </div>
                                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                                </div>
                            </div>

                            <div>
                                <div class="hstack justify-content-between" >
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">개인정보 동의 내역</p>
                                </div>
                                <div class="mt-[10px] vstack" style="">
                                    <div class="skin-box  ">
                                        <p>수집 및 이용 동의한 항목  </p>
                                        <a href="">
                                            <img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_right_gray.png" class="w-[18px]" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="hstack justify-content-between" >
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">약관 및 정책</p>
                                </div>
                                <div class="mt-[10px] vstack" style="">
                                    <div class="position-relative">
                                        <div class="skin-box  ">
                                            <p>서비스 이용약관 보기</p>
                                            <img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_right_gray.png" class="w-[18px]" alt="">
                                        </div>
                                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                           data-wv-ajax-data='{ "action":"view","made":"member","part":"member","field":"ceo/terms1","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="stretched-link"></a>
                                    </div>
                                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                                    <div class="position-relative">
                                        <div class="skin-box  ">
                                            <p>개인정보 처리방침 보기</p>
                                            <img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_right_gray.png" class="w-[18px]" alt="">
                                        </div>
                                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                           data-wv-ajax-data='{ "action":"view","made":"member","part":"member","field":"ceo/terms2","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="stretched-link"></a>
                                    </div>
                                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                                    <div class="position-relative">
                                        <div class="skin-box  ">
                                            <p>마케팅 목적의 개인정보 수집 및 이용 (선택)</p>
                                            <img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_right_gray.png" class="w-[18px]" alt="">
                                        </div>
                                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                           data-wv-ajax-data='{ "action":"view","made":"member","part":"member","field":"ceo/terms3","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="stretched-link"></a>
                                    </div>
                                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                                    <div class="position-relative">
                                        <div class="skin-box  ">
                                            <p>마케팅 정보 수신 (선택)</p>
                                            <img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_right_gray.png" class="w-[18px]" alt="">
                                        </div>
                                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                           data-wv-ajax-data='{ "action":"view","made":"member","part":"member","field":"ceo/terms4","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" class="stretched-link"></a>
                                    </div>
                                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>



                                </div>
                            </div>

                            <div>
                                <div class="hstack justify-content-between" >
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">앱 정보</p>
                                </div>
                                <div class="mt-[10px] vstack" style="">
                                    <div class="skin-box  ">
                                        <p>현재 버전  </p>
                                        <span class="value">v1.0.0</span>
                                    </div>


                                </div>
                            </div>
                        </div>



                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>


        $(document).ready(function () {
            $(".member_leaver",$skin).click(function (e) {
                if(confirm('정말 회원에서 탈퇴 하시겠습니까?')){
                    return true;
                }else{
                    e.stopImmediatePropagation(); // 다른 click 핸들러 실행 안됨
                    e.stopPropagation(); // 다른 click 핸들러 실행 안
                }
            })
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).ajaxForm({
                reload: false,
                // reload_ajax:true,
                success: function () {
                    var $offcanvas = $skin.closest('.wv-offcanvas');
                    $offcanvas.offcanvas('hide');
                }
            })
        })
    </script>
</div>