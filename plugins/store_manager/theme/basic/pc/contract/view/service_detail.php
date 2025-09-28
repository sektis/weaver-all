<?php
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap  " style=" ">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .col-box{padding: var(--wv-30) 0}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full h-100 " style="">

        <div class="border justify-content-start w-full mt-[4px]" style="padding: var(--wv-10) var(--wv-12);gap:var(--wv-4); ">
            <div class="hstack align-items-start " style="<?php echo $row['item']['color_type_text']; ?>;gap:var(--wv-4)">
                <?php if ($row['item']['icon_small']['path']) { ?>
                    <div><img src="<?php echo $row['item']['icon_small']['path']; ?>" class="w-[16px]" alt=""></div>
                <?php } ?>
                <p><?php echo $row['service_content']; ?></p>
            </div>


            <?php if (array_filter($row['service_time'])) { ?>
                <div class="my-[8px]" style="height: 1px;background-color: #efefef"></div>
                <div class="fs-[12/17/-0.48/500/#0D171B] hstack align-items-start" style="gap:var(--wv-4)"  >
                    <div class="col-auto"><img src="<?php echo $this->manager->plugin_url; ?>/img/clock.png" class="w-[14px]" alt=""></div>
                    <p class="col-auto">이용 가능한 시간 :</p>
                    <div class="vstack col " style="row-gap: var(--wv-5)">

                        <?php $i=0; foreach ($row['service_time_group'] as $each){ ?>
                            <p class="hstack flex-wrap" style="gap:var(--wv-5);row-gap: 0"><span>(<?php echo $each['name']; ?>)</span><span><?php echo $each['time']; ?></span></p>
                            <?php $i++;} ?>
                        <?php if($i==0){ ?>
                            <p>미등록</p>
                        <?php } ?>
                    </div>
                </div>
            <?php }?>
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