<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';
?>
<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {--x-value:0}
        <?php echo $skin_selector?> .wv-hover-box{white-space: normal}
        <?php echo $skin_selector?> :has(+ .wv-hover-box){white-space: normal}

        <?php echo $skin_selector?> .depth-wrap-1{height: 100%}
        <?php echo $skin_selector?> .depth-ul-1{height: 100%;gap: var(--wv-20);flex-wrap: nowrap}
        <?php echo $skin_selector?> .depth-li-1{transition: all .1s }
        <?php echo $skin_selector?> .depth-link-1{transition: all .1s;font-size:var(--wv-18)}


        <?php echo $skin_selector?> .depth-ul-2{gap: var(--wv-20)}
        <?php echo $skin_selector?> .depth-li-2{}
        <?php echo $skin_selector?> .depth-link-2{transition: all .1s;font-size:var(--wv-18)}
        <?php echo $skin_selector?> .depth-wrap-2{position: absolute;width: auto;visibility: hidden;opacity: 0;transition: opacity .3s ease;transform: translateX(var(--x-value,0))}
        <?php echo $skin_selector?> .depth-wrap-2.show{   visibility: visible; opacity: 1;transition: opacity .3s ease}

        <?php echo $skin_selector?> .menu-background{transition: all .1s ease ;background:#fff;overflow: hidden;position:absolute;top:100%;width: 100%;left:50%;transform: translateX(-50%);z-index: 100;clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);visibility: hidden}
        <?php echo $skin_selector?> .menu-background.show{clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);visibility: visible;transition: all 1s ease}

    </style>
    <?php
    $sub_contents='';
    echo wv_depth_menu(function ($depth,$content,$curr_id) use(&$sub_contents){

        if($depth==2){
            $sub_contents.=$content;
            return false;
        }
    },$skin_id,$data,2);

    ?>

    <div class="menu-background" >
        <div class=" position-relative">
            <?php echo $sub_contents?>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var hovering = null;
            var $skin = $('<?php echo $skin_selector?>');
            var $depth1_link = $('.depth-link-1',$skin);
            var $menu_background = $('.menu-background',$skin);
            var $fallow_line = $('.fallow-line',$skin);
            var $on_depth1_link = '';

            var skin_right_end_x = $skin.position().left+$skin.outerWidth()


            $depth1_link.mouseenter(function() {
                clearTimeout(hovering);
                var $this = $(this);
                var menu_id = $this.data('menu-id');
                var $sub_content = $('[data-parent-menu-id="'+menu_id+'"]',$skin);
                var menu_index = $(".depth-wrap-2",$skin).index($sub_content);

                if($on_depth1_link!=='' && $on_depth1_link!==$this){
                    $on_depth1_link.parent().removeClass('on');
                    $on_depth1_link = '';
                }
                $on_depth1_link = $this;
                $this.parent().addClass('on');

                if($sub_content.length === 0){
                    menu_hide();
                    return false;
                }

                var depth1_left = $this.position().left;
                var sub_content_width = $sub_content.outerWidth();




                var parent_left = depth1_left;
                var over_x_length = parent_left+sub_content_width-skin_right_end_x;
                if(over_x_length>0){

                    parent_left-=over_x_length;
                }

                $sub_content[0].style.setProperty("--x-value", parent_left+'px');



                $(".show",$menu_background).removeClass('show');
                $sub_content.addClass('show')
                $menu_background.addClass('show').css('height',$sub_content.outerHeight());


            });

            $skin.mouseenter(function() {
                clearTimeout(hovering);
            });

            $skin.mouseleave(function(e) {
                hovering = setTimeout(function () {

                    $menu_background.find('.show').removeClass('show');
                    $menu_background.removeClass("show");
                    if($on_depth1_link){
                        $on_depth1_link.parent().removeClass('on');
                    }

                    $on_depth1_link = '';
                }, 400)
            });



        })
    </script>
</div>


