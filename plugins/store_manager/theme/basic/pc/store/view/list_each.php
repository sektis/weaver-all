<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
dd($row);
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-[328px]    " style="padding: var(--wv-12) var(--wv-10);border-radius: var(--wv-4);box-shadow: 0 0 var(--wv-4) 0 rgba(67, 67, 67, 0.25);background-color: #fff;overflow:hidden;">

        <div >
            <div class="row" style="--bs-gutter-x: var(--wv-12)">
                <div class="col-auto">
                    <div class="w-[80px] h-[80px] overflow-hidden" style="border-radius: var(--wv-4);">
                        <img src="<?php echo $row['main_image']; ?>" class="object-fit-cover" alt="">
                    </div>
                </div>
                <div class="col">
                    <div class="vstack  h-100" style="padding: var(--wv-3) 0">
                        <div class="hstack">
                            <div>
                                <p class="fs-[14//-0.56/700/#0D171B]"><?php echo $row['name']; ?></p>
                                <p class="fs-[12//-0.48/500/#0D171B] mt-[2px]"><?php echo $row['category_item']['name']; ?></p>
                            </div>

                            <a href=""></a>
                        </div>

                        <div class="hstack mt-auto" style="gap:var(--wv-2);filter: brightness(0) saturate(100%) invert(62%) sepia(1%) saturate(1638%) hue-rotate(204deg) brightness(97%) contrast(93%);">
                            <img src="<?php echo WV_URL.'/img/icon_location.png'; ?>"   class="w-[12px]" alt="">
                            <p class="fs-[12//-0.48/500/#97989C]"><?php echo $this->store->location->address_name_full; ?></p>
                        </div>
                    </div>
                    <div class="mt-[8px]" style="border-bottom: 1px solid #efefef"></div>

                </div>
            </div>
        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>