<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}

            border-radius: var(--wv-4);
            background: #f9f9f9;}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full   md:w-full " style="background-color: #fff;padding: var(--wv-24) 0 var(--wv-30)">

        <div class="container">
            <div class="hstack justify-content-between">
            <p class="fs-[18//-0.72/700/#0D171B]">우리동네 인기 가게</p>
                <a class="fs-[12//-0.48/600/#97989C]">더보기 <i class="fa-solid fa-chevron-right fs-09em"></i></a>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

        })

    </script>
</div>