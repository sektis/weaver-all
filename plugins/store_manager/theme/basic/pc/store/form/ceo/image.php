<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php echo $this->store->basic->render_part('wr_id','form');; ?>
                <div class="vstack h-100 " style="padding-top:var(--wv-10)">
                        <div class="wv-offcanvas-header col-auto">
                            <div class=" ">
                                <div class="row align-items-center g-0"  >


                                    <div class="col">
                                        <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                                    </div>

                                    <div class="col-auto text-center">
                                        <p class="fs-[14/20/-0.56/600/#0D171B]">매장 소개 이미지</p>
                                    </div>
                                    <div class="col"></div>
                                </div>
                                <div class="mt-[8px]" style="padding: var(--wv-16) 0">
                                    <p class="fs-[12/17/-0.48/600/#0D171B]">매장 사진 업로드</p>
                                    <div class="h-[38px] mt-[12px] hstack align-items-center" style="border-radius: var(--wv-4);background-color: #f9f9f9;gap:var(--wv-2);padding: 0 var(--wv-8)">

                                        <img src="<?php echo $this->manager->plugin_url ?>/img/vec1.png" class="w-[14px]" alt="">
                                        <p class="fs-[12/17/-0.48/500/#97989C]">매장 이름 변경을 원하실 경우, 고객센터로 문의바랍니다</p>

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                        <div class="wv-offcanvas-body col"   >

                            <div class=" ">
                                <?php echo $this->store->store->render_part('image','form');; ?>
                            </div>

                            <div class="fs-[12/17/-0.48/500/#97989C] mt-[20px] vstack" style="row-gap:var(--wv-8)">
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>이미지는 순서대로 고객에게 보여집니다</p></div>
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>매장 소개 이미지는 <span class="text-[#0D171B]">매장 외부, 내부, 대표 메뉴 등</span> 내 매장을 <br>잘 나타낼 수 있는 이미지가 좋아요</p></div>
                                <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>JPG / PNG 이미지만 업로드 가능합니다</p></div>
                            </div>


                        </div>

                        <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                            <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">완료</button>
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