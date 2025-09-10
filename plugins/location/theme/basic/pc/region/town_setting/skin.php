<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative   flex-nowrap h-100"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100" style="background:#fff;border-radius: var(--wv-4) var(--wv-4) 0 0;;;">

        <div  class="vstack  h-100"  >

            <p class="fs-[24/32/-0.96/600/#0D171B]">
                관심 동네를 설정해주세요
            </p>
            <p class="fs-[14/20/-0.56/500/#97989C] mt-[8px]">
                설정한 지역을 기준으로 가게를 볼 수 있어요. <br>
                (최소 3개 동네 설정 필수)
            </p>
            <div class="mt-[30px] "  >



                <a href="#" class="wv-flex-box w-full h-[43px] rounded-[4px] bg-[#F9F9F9] position-relative justify-content-start px-[12px] cursor-pointer"
                   data-wv-ajax-url="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/search.php"   data-wv-ajax-options="end,static">
                    <p class="fs-[14/20/-0.56/500/#CFCFCF]">지역을 검색해보세요 (강남구, 논현동 등)</p>
                    <div class="btn p-0 outline-none position-absolute top-50 translate-middle-y end-[12px]"><img src="<?php echo WV_URL; ?>/img/search.png" class="w-[20px]" alt=""></div>
                </a>

                <div class="  mt-[26px] wv-mx-fit" style="height: var(--wv-8);background-color: #f7f7f7"></div>
                <div style="padding:var(--wv-30) var(--wv-16)">

                </div>
            </div>


            <div class="mt-auto mb-[50px]">

                <button type="button" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] submit-btn transition hover:bg-[#0d171b] mt-[22px]" style="border:0;background-color: #cfcfcf;border-radius: var(--wv-4)">관심 동네 설정하기</button>
            </div>






        </div>
    </div>

    <script>

        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");


        })

    </script>






</div>