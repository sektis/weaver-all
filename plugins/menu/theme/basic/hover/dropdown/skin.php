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
        <?php echo $skin_selector?> .depth-ul-1{height: 100%;gap: var(--wv-98_5);flex-wrap: nowrap}
        <?php echo $skin_selector?> .depth-li-1{transition: all .1s }
        <?php echo $skin_selector?> .depth-link-1{transition: all .1s;font-size:var(--wv-16);font-weight: 600;}
        <?php echo $skin_selector?> .depth-li-1.on .depth-link-1{color:#95756D}


        <?php echo $skin_selector?> .depth-ul-2{gap: var(--wv-20);flex-direction: column; ;padding: var(--wv-23) var(--wv-42);}
        <?php echo $skin_selector?> .depth-li-2{}
        <?php echo $skin_selector?> .depth-link-2{transition: all .1s;font-size:var(--wv-15)}
        <?php echo $skin_selector?> .depth-link-2:hover{color:#95756D}
        <?php echo $skin_selector?> .depth-wrap-2{position: absolute;width: auto;visibility: hidden;opacity: 0;transition: opacity .3s ease;left:var(--x-value,0);transform: translateX(-50%);background: rgba(255, 255, 255, 0.9);filter: drop-shadow(2.828px 2.828px 4px rgba(28, 27, 27, 0.05));border-radius: var(--wv-15)}
        <?php echo $skin_selector?> .depth-wrap-2:after{content:'';position: absolute;width: 0;top:calc(var(--wv-3) * -1);left:50%;transform: translateX(-50%);height: var(--wv-3);background: #96766D;z-index: 2;transition: .4s ease}
        <?php echo $skin_selector?> .depth-wrap-2.show:after{width: 60%}
        <?php echo $skin_selector?> .depth-wrap-2.show{   visibility: visible; opacity: 1;transition: opacity .3s ease}

        <?php echo $skin_selector?> .menu-background{transition: all .1s ease ;background:#fff;overflow-x: clip;position:absolute;top:100%;width: 100%;left:50%;transform: translateX(-50%);z-index: 100;;visibility: hidden;}
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

            var skin_right_end_x = $skin.position().left+$skin.outerWidth()


            $depth1_link.mouseenter(function() {
                clearTimeout(hovering);
                var $this = $(this);
                var menu_id = $this.data('menu-id');
                var $sub_content = $('[data-parent-menu-id="'+menu_id+'"]',$skin);

                if($on_depth1_link!=='' && $on_depth1_link!==$this){
                    $on_depth1_link.parent().removeClass('on');
                    $on_depth1_link = '';
                }
                $on_depth1_link = $this;
                $this.parent().addClass('on');

                if($sub_content.length === 0){
                    return false;
                }

                var depth1_left = $this.position().left;

                var parent_left = depth1_left+($this.outerWidth()/2);


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
                }, 400);
            });

        })
    </script>
</div>