<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?>
        {
        }

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
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">계정 설정</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>



                    <div class="wv-offcanvas-body col">

                        <p class="fs-[14/20/-0.56/600/#97989C]">계정 정보</p>


                        <div class="vstack   mt-[19px]" style="row-gap:var(--wv-20)">
                            <div>
                                <div class="hstack justify-content-between" >
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">아이디 변경 </p>
                                </div>
                                <div class="fs-[12/17/-0.48/500/#97989C] mt-[12px] vstack" style="row-gap:var(--wv-8)">
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>아이디는 한번만 변경이 가능하며, 고객센터를 통해 변경이 가능합니다.</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>브랜드 변경, 오타 등 불가피한 경우, 고객센터로 문의해주세요</p></div>
                                </div>
                            </div>

                            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                            <div>
                                <div class="hstack justify-content-between">
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">비밀번호 변경 </p>
                                    <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                       data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"ceo/mb_password","wr_id":"<?php echo $wr_id; ?>"}'
                                       data-wv-ajax-option="offcanvas,end,backdrop,class: w-[360px]" >
                                        <i class="fa-solid fa-angle-right ms-auto"></i>
                                    </a>
                                </div>
                                <div class="fs-[12/17/-0.48/500/#97989C] mt-[12px] vstack" style="row-gap:var(--wv-8)">
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>비밀번호는 90일 주기로 변경을 권장합니다.</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>비밀번호를 잃어버린 경우, 고객센터를 문의해주세요.</p></div>
                                </div>
                            </div>

                            <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                        </div>

                        <div class="hstack justify-content-center mt-[60px] fs-[12/17/-0.48/600/#97989C]" style="gap:var(--wv-8)">
                            <a href="<?php echo G5_BBS_URL; ?>/logout.php">로그아웃</a>
                            <span>|</span>
                            <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                               data-wv-ajax-data='{ "action":"delete","made":"member","wr_id":"<?php echo $current_member_wr_id; ?>"}'
                               class="member_leaver  ">회원탈퇴</a>
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