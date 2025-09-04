<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';
$depth1_initial_slide_num='';
$depth2_initial_slide_num='';

?>

<div id="<?php echo $skin_id?>" class="h-100 position-relative">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .swiper-slide{position: relative;width: auto;}

        <?php echo $skin_selector?> .depth-swiper-1 {height: 100%;position: relative;opacity: 0;visibility: hidden;transition: opacity .4s;pointer-events: none;padding: var(--wv-5) 0}
        <?php echo $skin_selector?> .depth-wrap-1 {height: 100%;}
        <?php echo $skin_selector?> [class*=depth-li-]:not([style*=width]) {margin-right: var(--wv-30)}
        <?php echo $skin_selector?> [class*=sub-swiper-] {position: relative;}

        <?php echo $skin_selector?> [class*=depth-link-] + [class*=depth-wrap-]{display: none}

        <?php echo $skin_selector?> [class*=depth-swiper-]:not(.depth-swiper-1){position: absolute;z-index:1000;top:100%;left:0;width: 100%;background-color: rgba(0, 0, 0, 0.8);padding: var(--wv-5) 0;opacity: 0;visibility: hidden;transition: opacity .4s;pointer-events: none}
        <?php echo $skin_selector?> [class*=depth-swiper-].on{opacity: 1;visibility: visible;pointer-events: auto}
        <?php echo $skin_selector?> [class*=depth-swiper-]:not(.changing) .swiper{transition: opacity .3s!important;}
        <?php echo $skin_selector?> [class*=depth-swiper-].changing .swiper{opacity: 0;transition: opacity 0s}

        <?php echo $skin_selector?> [class*=depth-swiper-]:not(.depth-swiper-1) [class*=depth-link-] {color:#fff}
    </style>
    <div class="depth-swiper-1">

        <div class="container">
            <?php
            $depth_array=array();
            echo wv_swiper_menu(function ($depth,$content,$curr_id) use(&$depth_array){

                if($depth>1){
                    $depth_array[]=$depth;
                }
            },$skin_id,$data);
            echo wv_swiper_wrap_loop($depth_array);
            ?>
        </div>
    </div>


    <script>

        $(document).ready(function (){
            var $skin = $("<?php echo $skin_selector?>");
            var depth_array = Object.values(<?php echo json_encode($depth_array);?>);

            var max_depth  = 1;
            if(depth_array.length){
                max_depth  = Math.max(...depth_array);
            }
            var depth_swiper = [];
            var init_slide_num = [];

            for(var i=1;i<=max_depth;i++){
                var $depth_link = $('.depth-link-'+i,$skin);

                var $depth_link_active = $('.depth-link-'+i+'.active',$skin);
                init_slide_num[i] = $depth_link.index($depth_link_active);

            }



            depth_swiper[1] = new Swiper("<?php echo $skin_selector?> .depth-wrap-1", {
                slidesPerView: 'auto',
                spaceBetween: 0,
                centeredSlides:true,
                centeredSlidesBounds:true,
            })

            for(var i=0;i<depth_array.length;i++){
                var depth = depth_array[i];

                depth_swiper[depth] = new Swiper("<?php echo $skin_selector?> .sub-swiper-"+depth, {
                    slidesPerView: 'auto',
                    spaceBetween: 0,
                    centeredSlides:true,
                    centeredSlidesBounds:true,
                })
            }

            var interval = setInterval(function () {
                if($(".depth-wrap-1",$skin).hasClass('swiper-initialized')){
                    sub_menu_update($('.depth-link-1.active',$skin),true);
                    clearInterval(interval);
                    return true;
                }
            }, 100);



            $($skin).on('click','[class*=depth-link-]',function () {
                return sub_menu_update($(this));
            })


            function sub_menu_update($link,init) {
                var depth = $link.data('depth');
                if(depth==undefined){
                    depth=1;
                }
                var next_depth = depth+1;

                $("[class*=depth-link-"+depth+"]",$skin).removeClass('active');
                $("[class*=depth-swiper-"+depth+"]").addClass('on');

                if($link.length==0) {

                    return true;
                }
                var menu_id = $link.data('menu-id');
                $link.addClass('active');

                var $next_swiper_wrap = $("[class*=depth-swiper-"+(next_depth)+"]",$skin);
                if(init_slide_num[depth]>-1){
                    depth_swiper[depth].slideTo(init_slide_num[depth])
                    init_slide_num[depth]='';
                }
                if($next_swiper_wrap.length==0){
                    return true;
                }


                if( $next_swiper_wrap.hasClass('on')){
                    $next_swiper_wrap.addClass('changing');
                }

                var $sub_slides = $("[data-parent-menu-id="+menu_id+"]",$skin);

                if($sub_slides.length==0)return true;

                if(!init){
                    depth_swiper[depth].slideTo($link.closest('.swiper-wrapper').find('>.swiper-slide').index($link.closest('.swiper-slide')))

                }

                $(".sub-swiper-"+(next_depth)+" .swiper-wrapper",$next_swiper_wrap).html($(".swiper-wrapper",$sub_slides).html())


                $next_swiper_wrap.addClass('on');
                setTimeout(function () {
                    $next_swiper_wrap.removeClass('changing');
                },200);

                depth_swiper[next_depth].update();

                if(init_slide_num[next_depth]>-1){
                    depth_swiper[next_depth].slideTo(init_slide_num[next_depth])
                    init_slide_num[next_depth]='';
                }else{
                    depth_swiper[next_depth].slideTo(0)
                }

                if(!init){
                    $("[class*=depth-swiper-]",$next_swiper_wrap).removeClass('on');
                    $("[class*=depth-link-]",$next_swiper_wrap).removeClass('active');
                }
                sub_menu_update($('.depth-link-'+next_depth+'.active',$next_swiper_wrap),init);
                return false;
            }

        })

    </script>
</div>


