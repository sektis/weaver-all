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
                <div class="vstack h-100 " id="<?php echo $skin_id; ?>">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0"  >


                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                                </div>

                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">사장님 공지</p>
                                </div>
                                <div class="col"></div>
                            </div>

                        </div>

                    </div>
                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
                    <div class="wv-offcanvas-body col"   >

                        <div class=" h-100 vstack">
                            <p class="fs-[16/22/-0.64/600/#0D171B] col-auto">사장님 공지</p>
                            <div class="mt-[20px] col">
                                <?php echo $this->store->store->render_part('notice','form'); ?>
                            </div>
                            <div class="fs-[12/17/-0.48/500/#97989C] mt-[20px] " style="row-gap:var(--wv-8)">
                                <div class="vstack col-auto">
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>내 매장만의 특별한 점을 소개해보세요!</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>매장의 분위기, 인기 메뉴, 조리 방식, 특별한 서비스 등을 간단히 <br>소개해 보세요.</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>너무 길지 않게, 핵심만 담으면 좋아요!</p></div>
                                    <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>작성한 내용은 고객에게 직접 보이며, 매장명 바로 아래에 노출됩니다.</p></div>
                                </div>
                            </div>

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
