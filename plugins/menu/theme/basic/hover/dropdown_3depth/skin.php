<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';
?>
<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .depth-wrap-1{height: 100%}
        <?php echo $skin_selector?> .depth-ul-1{height: 100%;gap: var(--wv-40); }
        <?php echo $skin_selector?> .depth-li-1 {position: relative}


        <?php echo $skin_selector?> .depth-wrap-2 {position: absolute;visibility: hidden;opacity: 0;border: 0;width: auto;min-width: 100%;background: rgba(255,255,255,1);
                                        transition: all 0.15s ease;overflow: hidden;top: 100%;z-index: 100;padding: var(--wv-22) var(--wv-30);box-shadow: 3.5px 8.3px 13px 0 rgba(0, 0, 0, 0.29);border-radius: 0 0 var(--wv-5) var(--wv-5);
                                        left:50%;transform: translateX(-50%);display: flex;gap: var(--wv-40)}
        <?php echo $skin_selector?> .depth-ul-2{flex-direction: column;align-items: start}

        <?php echo $skin_selector?> .depth-wrap-2.show {visibility: visible;opacity: 1;transition: all .4s ease-in-out;}
        <?php echo $skin_selector?> .depth-link-2 {font-size: var(--wv-18);letter-spacing: -0.05em;padding: var(--wv-15) 0;font-weight: 300;transition: none;position: relative;display: block;color:#9c9c9c;}
        <?php echo $skin_selector?> .depth-link-2.on,<?php echo $skin_selector?> .depth2-link:hover {color:#C98D8D ;}
        <?php echo $skin_selector?> .depth-link-2.on:after {content: '>';font-size: var(--wv-18);letter-spacing: -0.05em;color:#000;position: absolute;top:50%;left:calc(100% + .5em);transform: translateY(-50%);}

        <?php echo $skin_selector?> .depth-wrap-3{position: absolute;opacity: 0;transition: all .3s linear;overflow: hidden;pointer-events: none;}
        <?php echo $skin_selector?> .depth-wrap-3.on{transition: all .5s linear;opacity: 1;pointer-events: auto;}
        <?php echo $skin_selector?> .depth-ul-3{flex-direction: column;align-items: start}

        <?php echo $skin_selector?> .depth-link-3{font-size: var(--wv-18);letter-spacing: -0.05em;font-weight: 300;color:#9c9c9c;padding: var(--wv-15) 0;}
        <?php echo $skin_selector?> .depth-link-3:hover{color:#C98D8D ;}
        <?php echo $skin_selector?> .depth-inner-3{padding: 0 var(--wv-31) 0 var(--wv-62);}


        <?php echo $skin_selector?> .depth-sub-wrap{width:0;left:0;top:0;position: relative;transition: all .4s ease;overflow: hidden;}
        <?php echo $skin_selector?> .depth-divider{width:1px;background-color: #9c9c9c;opacity: 0}
        <?php echo $skin_selector?> .depth2-wrap:has(.depth-wrap-3.on) .depth-divider{opacity: 1}

    </style>

    <?php
    $sub_contents=array();
    echo wv_depth_menu(function ($depth,$content,$curr_id) use(&$sub_contents){
        if($depth==3){
            $sub_contents[wv_remove_last_part($curr_id)].=$content;
            return false;
        }
        if($depth==2){
            $content = (rtrim(trim($content),'</div>'));
            $content.='<div class="depth-divider"></div>';
            $content.='<div class="depth-sub-wrap">'.$sub_contents[$curr_id].'</div>';
            $content.='</div>';
            return $content;
        }


    },$skin_id,$data);

    ?>

    <script>
        $(document).ready(function () {
            var hovering = [];
            var $skin = $("<?php echo $skin_selector?>");
            var $header_menu = $skin.closest('#header-menu');
            var $visible_sub_content = '';
            var $depth1_link = $('.depth-link-1',$skin);
            var $depth2_link = $('.depth-link-2',$skin);
            var $on_depth1_link = '';
            var depth3_view_time = 0;
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

                menu_hide();
                if($sub_content.length === 0){
                    return false;
                }

                $visible_sub_content = $sub_content;

                $sub_content.addClass('show')
                // $header_menu.addClass('hover');
            });

            $depth2_link.mouseenter(function() {

                var $this = $(this);
                var menu_id = $this.data('menu-id');
                var $sub_content = $('[data-parent-menu-id="'+menu_id+'"]',$skin);
                var $depth2_wrap = $this.closest('.depth-wrap-2')

                $(".depth-link-2",$depth2_wrap).removeClass('on');
                if($sub_content.length){
                    $this.addClass('on')
                }

                $(".depth-wrap-3",$depth2_wrap).removeClass('on')
                $sub_content.addClass('on')
                var cont_w = $sub_content.outerWidth();
                var cont_h = $sub_content.outerHeight();

                if(!$sub_content.length){
                    depth3_view_time = 1000;
                }else{
                    depth3_view_time =0;
                }
                $("> .depth-sub-wrap",$depth2_wrap).css('width',(cont_w?cont_w:0)+'px')
                $("> .depth-sub-wrap",$depth2_wrap).css('height',(cont_h?cont_h:0)+'px')



            });

            $skin.mouseenter(function() {
                clearTimeout(hovering);
            });

            $skin.mouseleave(function() {

                hovering = setTimeout(function () {


                    menu_hide()
                    $on_depth1_link.parent().removeClass('on');
                    $on_depth1_link = '';

                }, depth3_view_time?depth3_view_time:300)

            });

            function menu_hide() {

                if($visible_sub_content=='')return true;
                $visible_sub_content.removeClass('show');
                $visible_sub_content = '';
                $(".depth-sub-wrap",$skin).css('width','0');
                $(".depth-wrap-3",$skin).removeClass('on')
                $(".depth-link-2",$skin).removeClass('on');

            }


        })
    </script>
</div>


