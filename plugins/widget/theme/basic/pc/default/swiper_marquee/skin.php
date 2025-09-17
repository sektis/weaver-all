<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
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
        <?php echo $skin_selector?> .swiper{overflow:revert;overflow-x: clip!important;--wv-swiper-gap: var(--wv-39);transition: opacity .5s ease}
        <?php echo $skin_selector?> .swiper:not(.swiper-initialized){opacity: 0}
        <?php echo $skin_selector?> .swiper-wrapper {transition-timing-function: linear !important;}
        <?php echo $skin_selector?> .swiper-button-next.swiper-button-disabled,  <?php echo $skin_selector?> .swiper-button-prev.swiper-button-disabled{opacity: 1}
        <?php echo $skin_selector?> .swiper-button-next:after, <?php echo $skin_selector?> .swiper-button-prev:after{content:''}
        <?php echo $skin_selector?> .swiper-button-next, <?php echo $skin_selector?> .swiper-button-prev{font-size:3em;position:relative;top:unset;left:unset;right: unset;pointer-events: auto}
        <?php echo $skin_selector?> .swiper-slide{height: auto;align-self: stretch;user-select: none;overflow:visible!important;}
        <?php echo $skin_selector?> .swiper-slide:not([style*=width]) {margin-right: var(--wv-swiper-gap)}
        <?php echo $skin_selector?> .swiper:has(.swiper-slide-fully-visible):has(.swiper-slide-visible.swiper-slide-next) .swiper-slide[style*=width]{padding-left: calc(var(--wv-swiper-gap) / 2);padding-right: calc(var(--wv-swiper-gap) / 2)}
        <?php echo $skin_selector?> .swiper:has(.swiper-slide-fully-visible):has(.swiper-slide[style*=width]){margin-left: calc(var(--wv-swiper-gap) / 2 *-1);margin-right: calc(var(--wv-swiper-gap) / 2 *-1)}
        <?php echo $skin_selector?> .swiper-slide:not(.swiper-slide-active) .aos-init{animation-name: none}

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="">

        <p class=""><?php echo $data['text1']; ?></p>

        <div id="<?php echo $skin_id?>-swiper" class="swiper h-100" >
                <div class="swiper-wrapper">
                    <?php if($data['arr']){ ?>
                        <?php for($i=0;$i<count($data['arr']);$i++){ ?>
                            <div class="swiper-slide position-relative w-auto"   >
                                <img src=" <?php echo $data['arr'][$i]['img']?>" style="width: 500px"   alt="">
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
            var before_translate;

            var swiper = new Swiper("<?php echo $skin_selector?>-swiper", {
                slidesPerView: 'auto',
                spaceBetween: 0,
                observer: true,
                observeParents: true,
                observeSlideChildren: true,
                watchSlidesProgress: true,
                grabCursor:true,
                loop:true,
                freeMode: {
                    enabled: true,
                    momentum:false,
                },
                speed: 6000,
                autoplay: {
                    delay: 1,
                },
                // slidesOffsetBefore:100,
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
                    beforeLoopFix: function(swiper) {
                        before_translate=this.getTranslate();
                    },
                    loopFix: function (swiper) {
                        if(this.getTranslate()!=before_translate){
                            if(this.getTranslate()>before_translate){
                                this.setTranslate(before_translate+this.snapGrid[1])
                            }else{
                                this.setTranslate(before_translate-this.snapGrid[1])
                            }
                        }
                    },
                    touchEnd:function (swiper) {
                        setTimeout(function () {
                            swiper.autoplay.start();
                        },100)
                    },
                    click:function (swiper) {
                        swiper.translateTo(swiper.getTranslate()-1,1)

                    },
                },
            })

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

            }


        })

    </script>
</div>




