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
                                    <p class="fs-[14/20/-0.56/600/#0D171B]"></p>
                                </div>
                                <div class="col"></div>
                            </div>

                        </div>

                    </div>
                    <div class="wv-offcanvas-body col">
                        <?php if($row['intro']['text1']){ ?>
                            <p class="fs-[24/25/-0.96/600/#0D171B]"><?php echo nl2br($this->montserrat_change($row['intro']['text1'])); ?></p>
                        <?php } ?>
                        <?php if($row['intro']['text2']){ ?>
                            <p class="fs-[14/20/-0.56/500/#97989C] mt-[12px]"><?php echo nl2br($this->montserrat_change($row['intro']['text2'])); ?></p>
                        <?php } ?>

                        <?php if($row['intro']['image']){ ?>
                            <div class="text-center">
                                <img src="<?php echo $row['intro']['image']['path']; ?>" alt="" style="width: 80%" class="object-fit-cover">
                            </div>
                        <?php } ?>
                        <?php if(count($row['intro']['point'])){ ?>
                            <div class="vstack fs-[14/20/-0.56/500/#97989C] mt-[6px]" style="row-gap: var(--wv-6)">
                                <?php $i=1; foreach ($row['intro']['point'] as $point){?>
                                    <div class="h-[47px] wv-flex-box bg-[#f9f9f9]">
                                        <span class="ff-montserrat"><?php echo $i; ?></span>
                                        <span><?php echo $point['text']; ?></span>
                                    </div>
                                <?php $i++;} ?>
                            </div>
                        <?php } ?>
                    </div>


                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                           data-wv-ajax-data='{ "action":"form","made":"sub01_01","part":"contract","field":"ceo/service_form","wr_id":"<?php echo $store_wr_id; ?>","contract_id":"<?php echo $cont['id']?>","contractitem_wr_id":"<?php echo $row['wr_id']; ?>"}'
                           data-wv-ajax-option="replace_with:#<?php echo $skin_id; ?>" class="wv-flex-box w-full   h-[54px]  bg-[#0d171b] fs-[16/22/-0.64/700/#FFF]">
                            등록하러 가기
                        </a>
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