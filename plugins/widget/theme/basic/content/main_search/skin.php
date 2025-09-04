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

    <div class="position-relative col col-lg-auto w-full md:w-full " style="">
        <div class="container">

            <form action="">
                <div class="position-relative fs-[14//-0.56/500/#97989C]">
                <input type="text" name="q" class="form-control h-[43px] rounded-[4px] border-[1px] border-solid border-[#EFEFEF] outline-none p-[12px] bg-[#f9f9f9]" placeholder="<?php echo $data['text1']; ?>">
                <button class="btn p-0 outline-none position-absolute top-50 translate-middle-y end-[12px]"><img src="<?php echo $wv_skin_url; ?>/search.png" class="w-[20px]" alt=""></button>
                </div>
            </form>
                 

        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

        })

    </script>
</div>