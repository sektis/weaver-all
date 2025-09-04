<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';
?>
<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .wv-hover-box{white-space: normal}
        <?php echo $skin_selector?> :has(+ .wv-hover-box){white-space: normal}

        <?php echo $skin_selector?> .depth-wrap-1{height: 100%}
        <?php echo $skin_selector?> .depth-ul-1{height: 100%;gap: var(--wv-50);flex-wrap: nowrap}
        <?php echo $skin_selector?> .depth-li-1{transition: all .1s }
        <?php echo $skin_selector?> .depth-link-1{transition: all .1s;font-size:var(--wv-18)}


        <?php echo $skin_selector?> .depth-wrap-2{position: absolute;width:var(--w-value,0);left:var(--x-value,0);transform: translateX(-50%);padding: 2em 0;display: flex;justify-content: center}
        <?php echo $skin_selector?> .depth-ul-2{gap: var(--wv-20);flex-direction: column;}
        <?php echo $skin_selector?> .depth-li-2{}
        <?php echo $skin_selector?> .depth-link-2{text-align: center;padding: 0 var(--wv-10)}


        <?php echo $skin_selector?> .menu-background{transition: all .54s ease ;background:#fff;overflow: hidden;position:absolute;top:100%;width: 100%;left:50%;transform: translateX(-50%);z-index: 100;;clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);}
        <?php echo $skin_selector?> .menu-background.show{clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);}
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
            var $on_depth1_link = '';

            var depth1_ul_gap = parseInt($(".depth-ul-1").css('gap').replace('px'));

            $depth1_link.mouseenter(function() {
                clearTimeout(hovering);
                var maxHeight = Math.max.apply(null, $(".depth-link-1",$skin).map(function (){

                    var $this = $(this);
                    var menu_id = $this.data('menu-id');
                    var $sub_content = $('[data-parent-menu-id="'+menu_id+'"]',$skin);
                    if(!$sub_content.length)return 0;
                    var depth1_left = $this.position().left;
                    var depth1_width = $this.outerWidth();
                    var parent_left = depth1_left+($this.outerWidth()/2);


                    $sub_content[0].style.setProperty("--w-value", depth1_width+depth1_ul_gap+'px');
                    $sub_content[0].style.setProperty("--x-value", parent_left+'px');


                    return $sub_content.outerHeight();
                }).get());
                $menu_background.css('height',(maxHeight)+'px');


                $menu_background.addClass('show');

            });

            $skin.mouseenter(function() {
                clearTimeout(hovering);
            });

            $skin.mouseleave(function(e) {
                hovering = setTimeout(function () {
                    $menu_background.removeClass("show");
                    if($on_depth1_link){
                        $on_depth1_link.parent().removeClass('on');
                    }

                    $on_depth1_link = '';
                }, 200);
            });

        })
    </script>
</div>


