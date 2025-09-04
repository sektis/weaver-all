<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
wv('assets')->add_plugin(array('twentytwenty'));
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> {
            --swiper-navigation-color: #fff;
            --swiper-pagination-color: #000;
            --swiper-pagination-bottom:0;
            --swiper-pagination-bullet-size: var(--wv-10);
            --swiper-pagination-bullet-horizontal-gap: var(--wv-10);
            --swiper-pagination-bullet-border-radius:50%;
            --swiper-pagination-bullet-inactive-opacity:1;
            --swiper-pagination-bullet-inactive-color:#ccc;
            --swiper-navigation-size:auto;
            --swiper-pagination-progressbar-bg-color:#E2E2E2;
            --swiper-pagination-progressbar-size: var(--wv-16);
        }
        <!--        --><?php //echo $skin_selector?>/* .swiper-pagination-progressbar{border-radius: 50rem;overflow: hidden} */
        <?php echo $skin_selector?> .swiper{overflow:revert;overflow-x: clip!important;--wv-swiper-gap: var(--wv-30);transition: opacity .5s ease}
        <?php echo $skin_selector?> .swiper:not(.swiper-initialized){opacity: 0}
        <?php echo $skin_selector?> .swiper-button-next.swiper-button-disabled,  <?php echo $skin_selector?> .swiper-button-prev.swiper-button-disabled{opacity: 1}
        <?php echo $skin_selector?> .swiper-button-next:after, <?php echo $skin_selector?> .swiper-button-prev:after{content:''}
        <?php echo $skin_selector?> .swiper-button-next, <?php echo $skin_selector?> .swiper-button-prev{font-size:3em;position:relative;top:unset;left:unset;right: unset;pointer-events: auto}
        <?php echo $skin_selector?> .swiper-slide{height: auto;align-self: stretch;overflow:visible!important;}
        <?php echo $skin_selector?> .swiper-slide:not([style*=width]) {margin-right: var(--wv-swiper-gap)}
        <?php echo $skin_selector?> .swiper:has(.swiper-slide-fully-visible):has(.swiper-slide-visible.swiper-slide-next) .swiper-slide[style*=width]{padding-left: calc(var(--wv-swiper-gap) / 2);padding-right: calc(var(--wv-swiper-gap) / 2)}
        <?php echo $skin_selector?> .swiper:has(.swiper-slide-fully-visible):has(.swiper-slide-visible.swiper-slide-next):has(.swiper-slide[style*=width]){margin-left: calc(var(--wv-swiper-gap) / 2 *-1);margin-right: calc(var(--wv-swiper-gap) / 2 *-1)}

        <?php echo $skin_selector?> .swiper-slide:not(.swiper-slide-active) .aos-init{animation-name: none}

        <?php echo $skin_selector?> .twentytwenty-container{--wv-twentytwenty-handle-size: var(--wv-60)}
        <?php echo $skin_selector?> .twentytwenty-container *{box-shadow: unset}
        <?php echo $skin_selector?> .twentytwenty-horizontal .twentytwenty-handle:after, <?php echo $skin_selector?> .twentytwenty-horizontal .twentytwenty-handle:before {background: #fff!important;width: 1px;margin: 0!important;}
        <?php echo $skin_selector?> .twentytwenty-handle {width: var(--wv-twentytwenty-handle-size);height: var(--wv-twentytwenty-handle-size);margin-left:calc(var(--wv-twentytwenty-handle-size) * -.57);margin-right:calc(var(--wv-twentytwenty-handle-size) * -.57);border: 0;background-color: rgba(229, 229, 229, 0.4);}
        <?php echo $skin_selector?> .twentytwenty-horizontal .twentytwenty-handle:before {bottom:100%;transform: translate(-50%,-50%);margin-bottom: 0!important;-webkit-box-shadow: 0 3px 0 #350566, 0 0 12px rgb(51 51 51 / 50%);-moz-box-shadow: 0 3px 0 #350566,0 0 12px rgba(51,51,51,.5);}
        <?php echo $skin_selector?> .twentytwenty-horizontal .twentytwenty-handle:after {top:100%;transform: translate(-50%,-50%);margin-top: 0!important;-webkit-box-shadow: 0 -3px 0 <?php echo $data['custom_color']?$data['custom_color']:'#350566'?>, 0 0 12px rgb(51 51 51 / 50%);-moz-box-shadow: 0 -3px 0 <?php echo $data['custom_color']?$data['custom_color']:'#350566'?>,0 0 12px rgba(51,51,51,.5);                               }
        <?php echo $skin_selector?> .twentytwenty-horizontal .twentytwenty-handle:before,<?php echo $skin_selector?> .twentytwenty-horizontal .twentytwenty-handle:after{box-shadow:unset;}
        <?php echo $skin_selector?> .twentytwenty-text{opacity: 0;}
        <?php echo $skin_selector?> .twentytwenty-wrapper ~ .twentytwenty-text{opacity: 1;}
        <?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-left-arrow,<?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-right-arrow{all: revert;}
        <?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-left-arrow,<?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-right-arrow{position:absolute;top: 50%;transform: translate(-50%,-50%)}
        <?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-left-arrow>*,<?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-right-arrow>*{position:absolute;top: 50%;left:50%;transform: translate(-50%,-50%)}
        <?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-left-arrow{left:25%;}
        <?php echo $skin_selector?> .twentytwenty-wrapper .twentytwenty-right-arrow{left: 75%;}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <?php ob_start(); ?>
    <svg class="w-[9px]" viewBox="0 0 9 11" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0.0136719 5.48693L8.76817 0.486328V10.4869L0.0136719 5.48693Z" fill="white"/>
    </svg>
    <?php $tw_left_arrow_html = ob_get_clean(); ?>
    <?php ob_start(); ?>
    <svg class="w-[9px]" viewBox="0 0 9 11" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.98619 5.5132L0.231689 10.5133V0.512695L8.98619 5.5132Z" fill="white"/>
    </svg>

    <?php $tw_right_arrow_html = ob_get_clean(); ?>

    <div class="position-relative col col-lg-auto w-[940px] md:w-full  " style="">

        <div class="text-center">
            <p class="fs-[18////] ff-NanumSquareRound fw-700 "><?php echo $data['text1']; ?></p>
            <p class="fs-[33///700/] mt-[9px] "><?php echo $data['text2']; ?></p>
            <p class="fs-[17///400/#5F5F5F] mt-[24px] "><?php echo $data['text3']; ?></p>
        </div>

        <div id="<?php echo $skin_id?>-swiper" class="swiper h-100 mt-[59px]" >
            <div class="swiper-wrapper">
                <?php if($data['arr']){ ?>
                    <?php for($i=0;$i<count($data['arr']);$i++){ ?>
                        <div class="swiper-slide position-relative"   >
                            <div class="position-relative " style="">
                                <div class="twentytwenty-container w-100 position-relative">
                                    <img src="<?php echo $data['arr'][$i]['before']?>"  class="w-100" />
                                    <img src="<?php echo $data['arr'][$i]['after']?>" class="w-100" />
                                </div>
                                <div class="position-absolute fs-[18///400/#FFF] twentytwenty-text" style="left:var(--wv-34);bottom:var(--wv-21)">BEFORE</div>
                                <div class="position-absolute fs-[18///700/#111] twentytwenty-text" style="right:var(--wv-34);bottom:var(--wv-21)">AFTER</div>
                            </div>
                        </div>
                    <?php }?>
                <?php }?>
            </div>

        </div>

    </div>

    <script>

        $(document).ready(function (){
            var $skin = $("<?php echo $skin_selector?>");
            var latest_slide_index = '';
            var tw_left_arrow_html = <?php echo json_encode($tw_left_arrow_html);?>;
            var tw_right_arrow_html = <?php echo json_encode($tw_right_arrow_html);?>;


            var swiper = new Swiper("<?php echo $skin_selector?>-swiper", {
                slidesPerView: 1,
                spaceBetween: 0,
                observer: true,
                observeParents: true,
                observeSlideChildren: true,
                watchSlidesProgress: true,
                grabCursor:true,
                loop:1,
                noSwipingClass:'twentytwenty-container',
                // slidesOffsetBefore:100,
                // autoplay: {
                //     delay: 4000,
                // },
                // breakpoints: {
                //     992: {
                //         slidesPerView: 4,
                //         spaceBetween: 40
                //     }
                // },
                // centeredSlides:true,
                // centeredSlidesBounds:true,
                navigation: {
                    nextEl: "<?php echo $skin_selector?> .swiper-button-next",
                    prevEl: "<?php echo $skin_selector?> .swiper-button-prev",
                },
                pagination: {
                    el: "<?php echo $skin_selector?> .swiper-pagination",
                    clickable:true,
                    // dynamicBullets: true,
                    // type: "progressbar",
                },
                on: {
                    afterInit:function (swiper) {
                        latest_slide_index = swiper.realIndex;
                        slide_first_ready(swiper);
                        slide_ready(swiper);


                    },
                    slideChangeTransitionStart:function (swiper) {
                        latest_slide_index = swiper.realIndex;
                    },
                    slideChangeTransitionEnd: function (swiper) {
                        slide_ready(swiper);
                    },
                    realIndexChange: function (swiper) {
                        slide_leave(swiper);
                        slide_before_ready(swiper);
                    },
                    resize:function () {
                        $(window).trigger("resize.twentytwenty")
                    }
                }
            });




            function slide_first_ready(swiper){
                var $slide = $(swiper.slides[swiper.activeIndex]);

            }

            function slide_leave(swiper){
                var $slide;
                if(swiper.loopedSlides){
                    $slide = $("[data-swiper-slide-index='"+latest_slide_index+"']",$skin);
                }else{
                    $slide = $(swiper.slides[latest_slide_index]);
                }
            }

            function slide_before_ready(swiper){
                var $slide = $(swiper.slides[swiper.activeIndex]);

            }

            function slide_ready(swiper){
                var $slide = $(swiper.slides[swiper.activeIndex]);
                var $twenty_container = $(".twentytwenty-container",$slide);
                var twenty_chk = setInterval(function () {
                    if($(".twentytwenty-wrapper",$slide).length){
                        clearInterval(twenty_chk);
                        $(".twentytwenty-left-arrow",$twenty_container).html(tw_left_arrow_html);
                        $(".twentytwenty-right-arrow",$twenty_container).html(tw_right_arrow_html);
                        $(window).trigger("resize.twentytwenty")
                        return true;
                    }
                    $(".twentytwenty-container",$slide).twentytwenty({
                        default_offset_pct: 0.5,
                        orientation: 'horizontal',
                        no_overlay: true,
                        move_with_handle_only: true,
                        click_to_move: false
                    });

                },100)


            }


        })

    </script>
</div>




