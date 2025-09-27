<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

extract($data);
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget store-list-widget h-100" style="">
    <style>
        <?php echo $skin_selector?> {}
    </style>

    <div class="position-relative col col-lg-auto w-full  h-100 " style="overflow-y: auto;overflow-x: hidden">

        <div class="vstack  " style="padding-bottom: var(--wv-100)">
            <?php foreach ($content['lists'] as $each){?>
                <div style="padding: var(--wv-12) var(--wv-10) var(--wv-20)">
                    <?php echo $each['list_each'];; ?>

                </div>
                <div class="wv-mx-fit" style="border-top: 6px solid #efefef"></div>
            <?php } ?>



            <?php if(!count($content['lists'])){ ?>
                <div class="store-empty" style=" text-align: center;padding: var(--wv-40);color: #666;">
                    <i class="fa-solid fa-store-slash" style="font-size: 48px; color: #ddd; margin-bottom: 16px;"></i>
                    <p>이 지역에 등록된 매장이 없습니다.</p>
                </div>
            <?php } ?>

        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");

        });
    </script>
</div>