<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .skin-title{align-self: stretch;
            color: #73757d;
            font-family: Pretendard;
            font-size: var(--wv-14);
            font-style: normal;
            font-weight: 600;
            line-height: var(--wv-20);
            letter-spacing: calc(var(--wv-0_56) * -1);}
        <?php echo $skin_selector?> .skin-desc{align-self: stretch;
                                        color: #97989c;
                                        font-family: Pretendard;
                                        font-size: var(--wv-14);
                                        font-style: normal;
                                        font-weight: 500;
                                        line-height: var(--wv-24);
                                        letter-spacing: calc(var(--wv-0_56) * -1);margin-top: var(--wv-20)}

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
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">서비스 이용 약관</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>



                    <div class="wv-offcanvas-body col" style="padding-top: var(--wv-30)">

                        <p class="fs-[24/32/-0.96/600/#0D171B]">덤이요 사장님 서비스 이용 약관</p>

                        <p class="fs-[14/20/-0.56/500/#73757D] mt-[8px]">
                            본 약관은 (주)덤이요(이하 “회사”라 함)가 제공하는 덤이요 사장님 애플리케이션(이하 “사장님 앱”)과 관련된 제반 서비스(이하 “서비스”)를 이용함에 있어 회사와 이용자 간의 권리, 의무 및 책임사항, 기타 필요한 사항을 규정함을 목적으로 합니다.
                        </p>

                        <div class="vstack mt-[40px]" style="row-gap: var(--wv-24)">

                            <div>
                                <p class="skin-title">제1조 (목적)</p>
                                <p class="skin-desc">
                                    이 약관은 회사가 운영·제공하는 “사장님 앱” 서비스를 사업자(이하 “이용자”)가 이용함에 있어 회사와 이용자 간의 권리,  의무 및 책임사항 등을 규정함을 목적으로 합니다..
                                </p>
                            </div>

                            <div>
                                <p class="skin-title">제1조 (목적)</p>
                                <p class="skin-desc">
                                    이 약관은 회사가 운영·제공하는 “사장님 앱” 서비스를 사업자(이하 “이용자”)가 이용함에 있어 회사와 이용자 간의 권리,  의무 및 책임사항 등을 규정함을 목적으로 합니다..
                                </p>
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