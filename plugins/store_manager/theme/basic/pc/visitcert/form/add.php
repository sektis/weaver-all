<?php
global $g5;
global $member;
if(!$member['mb_id']){
    alert('로그인 후 이용가능합니다.',G5_BBS_URL.'/login.php',true);
}
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .add-image-popup:not(.active){display: none}
        <?php echo $skin_selector?> .add-image-result:not(.active){display: none}
        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <input type="hidden" name="mb_id" value="<?php echo $member['mb_id']; ?>">
                <input type="hidden" name="part" value="visitcert">
                <input type="hidden" name="visitcert[store_wr_id]" value="<?php echo $row['id']['store_wr_id']?$row['id']['store_wr_id']:$store_wr_id; ?>">
          
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">

                        <div class="wv-offcanvas-header col-auto">
                            <div class=" ">
                                <div class="row align-items-center g-0"  >


                                    <div class="col">
                                        <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>

                                    </div>

                                    <div class="col-auto text-center">
                                    </div>
                                    <div class="col"></div>
                                </div>

                            </div>

                        </div>
                        <div class="wv-offcanvas-body col"  >

                            <p class="fs-[20/28/-0.8/600/#0D171B]">
                                인증 캡쳐 시, 다음 항목이 <br>
                                반드시 모두 보이도록 <br>
                                촬영해주세요.
                            </p>

                            <p class="fs-[14/22/-0.56/500/#97989C]">
                                제출해주신 정보는 확인 절차를 거쳐 <br>
                                인증 결과를 안내해드립니다.
                            </p>

                            <div class="fs-[14/20/-0.56/600/#0D171B] mt-[34px]">
                                <div class="hstack" style="gap:var(--wv-4)">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/check_green.png" class="w-[20px]" alt="">
                                    <p>준비물 </p>
                                    <p class="ms-auto text-[#19BBC0]">종이 영수증 (전자영수증 불가)</p>
                                </div>
                                <div class="wv-mx-fit mt-[20px]" style="height: 1px;background-color: #efefef"></div>
                                <div class="hstack mt-[20px] align-items-start" style="gap:var(--wv-4)">
                                    <img src="<?php echo $this->manager->plugin_url; ?>/img/check_green.png" class="w-[20px]" alt="">
                                    <p>필요한 정보 </p>
                                    <div class="ms-auto vstack align-items-end" style="row-gap: var(--wv-6)">
                                        <p>결제 날짜 / 시간</p>
                                        <p>결제 매장명</p>
                                        <p>결제 금액</p>
                                        <p>카드사명 및 일부 카드번호</p>
                                    </div>
                                </div>
                                <p class="mt-[20px] fs-[12/17/-0.48/500/#0D171B]"><span class="text-[#FF2424]">(필수)</span> 결제 내역 영수증</p>
                               
                                <div class=" mt-[6px]">
                                    <a href="#" class="wv-flex-box h-[51px] fs-[14/20/-0.56/600/#19BBC0] w-full add-image-popup active" style="border: var(--wv-1) solid #19bbc0;background: #fff; ">
                                        <img src="<?php echo $this->manager->plugin_url; ?>/img/camera_green.png" class="w-[16px]" alt="">
                                        <span>사진등록</span>
                                    </a>
                                    
                                    <div class="add-image-result w-[140px]">
                                        <?php echo $this->store->visitcert->render_part('image','form'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-[40px] wv-mx-fit">
                                <?php echo wv_widget('content/cert_notice'); ?>
                            </div>

                            <div class="wv-mx-fit">
                                <?php echo wv_widget('content/copyright'); ?>
                            </div>
                        </div>

                        <div class="mt-auto col-auto pb-[50px] hstack gap-[6px] wv-mx-fit">

                            <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">제출하기</button>
                        </div>


                </div>
            </form>
        </div>

        <div class="offcanvas offcanvas-bottom bg-white" tabindex="-1" id="visit-cert-image-offcanvas"   style="height: auto;max-height: 50dvh;border-top-left-radius: var(--wv-4);border-top-right-radius: var(--wv-4);overflow: hidden">
            <div class="offcanvas-header justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body pb-[40px]">

                <div class="container">
                    <p class="fs-[20/28/-0.8/600/#0D171B]">영수증으로 방문을 인증해주세요</p>
                    <p class="fs-[14/20/-0.56/500/#97989C] mt-[6px]">
                        매장에서 결제 후 받은 영수증을 촬영하거나 불러와 주세요. <br>
                        방문 인증이 완료되면 적립금이 지급돼요.
                    </p>
                    <a href="#" class="wv-flex-box h-[54px] fs-[16/22/-0.64/600/#0D171B] w-full mt-[35px]  border do-camera" style=" ">
                        <img src="<?php echo $this->manager->plugin_url; ?>/img/camera-fill.png" class="w-[18px]" alt="">
                        <span>영수증 촬영하기</span>
                    </a>

                    <a href="#" class="wv-flex-box h-[54px] fs-[16/22/-0.64/600/#0D171B] w-full mt-[12px]  border do-album" style=" ">
                        <img src="<?php echo $this->manager->plugin_url; ?>/img/image-fill.png" class="w-[18px]" alt="">
                        <span>앨범에서 선택하기</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");

            var offcanvasElement = document.getElementById('visit-cert-image-offcanvas');
            var offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
            $(".add-image-popup",$skin).click(function () {
                offcanvasInstance.show();
            })

            $(".do-camera",$skin).click(function () {
                offcanvasInstance.hide()
                $(".add-image-result input[type=file]",$skin)[0].setAttribute('capture','camera');
                $(".add-image-result label",$skin).click();
                $(".add-image-result input[type=file]",$skin).on('change',function () {
                    $(".add-image-popup",$skin).removeClass('active');
                    $(".add-image-result",$skin).addClass('active')
                })
            })

            $(".do-album",$skin).click(function () {
                offcanvasInstance.hide()
                $(".add-image-result input[type=file]",$skin)[0].removeAttribute('capture');
                $(".add-image-result label",$skin).click();
                $(".add-image-result input[type=file]",$skin).on('change',function () {
                    $(".add-image-popup",$skin).removeClass('active');
                    $(".add-image-result",$skin).addClass('active')
                })
            })


            $("form", $skin).ajaxForm({
                // reload:true,
                // success: function () {
                //
                //     var offcanvasId = $skin.closest('.wv-offcanvas').attr('id');
                //     if(offcanvasId){
                //         wv_reload_offcanvas(offcanvasId);
                //         return true;
                //     }
                //
                // }
            })
        })
    </script>
</div>


