<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';

if(!function_exists('wv_collapse_menu_icon')){
    function wv_collapse_menu_icon($callback, $curr_id, $data,$parent=array(), $max_depth='99', $depth=1,$parent_menu_id=''){

        if($depth==1){
            $curr_id.='-top';
        }
        if($depth>$max_depth){
            return '';
        }

        ob_start();
        ?>

        <div id="<?php echo $curr_id; ?>" class="depth-wrap-<?php echo $depth?> <?php echo $depth>1?'collapse'.(($parent['expand'] or $parent['active'])?' show':''):''; ?>" data-parent-menu-id="<?php echo $parent_menu_id; ?>" data-depth="<?php echo $depth; ?>" style="--wv-menu-depth:<?php echo $depth; ?>">
            <ul class="nav depth-ul-<?php echo $depth?>  " >
                <?php
                foreach ($data as $i=>$menu) {
                    $child_id = "{$curr_id}-{$i}";
                    ?>
                    <li class="depth-li-<?php echo $depth?>" >

                        <div class="position-relative  "  >

                            <a  class="depth-link-<?php echo $depth?> d-flex-start h-100 <?php echo ($menu['expand'])?'expand':''?> <?php echo ($menu['active'])?'active':''?>" href="<?php echo $menu['url']?>"  target="<?php echo $menu['target']?>" data-menu-id="<?php echo $menu['id']?>" data-depth="<?php echo $depth; ?>">
                                <?php if($menu['icon']){ ?>
                                    <img src="<?php echo $menu['icon']; ?>" class="w-[16px]" alt="">
                                <?php } ?>

                                <span><?php echo $menu['name']?></span>

                            </a>
                            <?php if($menu['sub']){?>
                                <div data-bs-target="<?php echo "#{$child_id}"; ?>" data-bs-toggle="collapse" class="collapse-wrap cursor-pointer  " aria-expanded="<?php echo ($menu['expand'] or $menu['active'])?'true':'false'?>" >
                                    <div class="collapse-icon d-flex-center"><i class="fa-solid"></i></div>
                                </div>
                            <?php } ?>
                        </div>


                        <?php if($menu['sub']){
                            echo wv_collapse_menu_icon($callback, $child_id,$menu['sub'],$menu, $max_depth,$depth+1,$menu['id']);
                        }?>

                    </li>
                <?php }?>
            </ul>
        </div>
        <?php
        $ob_content = ob_get_clean();
        if(is_callable($callback)){
            $result = $callback($depth,$ob_content,$curr_id,$data);
            if($result===false){
                $ob_content = '';
            }elseif($result and $result!==true){
                $ob_content = $result;
            }
        }
        return $ob_content;
    }
}

?>
<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .collapse-wrap {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
        }
        <?php echo $skin_selector?> .collapse-icon i{transition:transform 0.5s  cubic-bezier(0.77,0.2,0.05,1.0);transform-origin: center;}
        <?php echo $skin_selector?> .collapse-icon i:before{content: "\2b"}
        <?php echo $skin_selector?> [aria-expanded="true"]>.collapse-icon i{transform: rotate(360deg);}
        <?php echo $skin_selector?> [aria-expanded="true"]>.collapse-icon i:before{content: "\f068"}
        <?php echo $skin_selector?> [class*=depth-ul-]{flex-direction: column;; ;}

        <?php echo $skin_selector?> .depth-wrap-1{height: 100%}
        <?php echo $skin_selector?> .depth-ul-1{}
        <?php echo $skin_selector?> .depth-li-1{ ;margin-top: var(--wv-30)}
        <?php echo $skin_selector?> .depth-li-1:first-child{margin-top: 0}
        <?php echo $skin_selector?> .depth-link-1{color: #fff;font-size: var(--wv-12);font-weight: 600;line-height: var(--wv-17);letter-spacing: calc(var(--wv-0_48) * -1);opacity: 0.5;padding: var(--wv-4) var(--wv-24);pointer-events: none;user-select: none;}
        <?php echo $skin_selector?> .depth-link-1 ~ *{pointer-events: none;user-select: none}

        <?php echo $skin_selector?> .depth-li-1.on .depth-link-1{}

        <?php echo $skin_selector?> .depth-wrap-2  {display: block!important; }
        <?php echo $skin_selector?> .depth-ul-2  {margin-top: var(--wv-6);  }
        <?php echo $skin_selector?> .depth-li-2  {;}
        <?php echo $skin_selector?> .depth-li-2:has(.depth-link-2.expand)  { background-color: #2f409f;padding-bottom: var(--wv-6)}
        <?php echo $skin_selector?> .depth-link-2{padding: var(--wv-12) var(--wv-26);font-size: var(--wv-14);line-height: var(--wv-20);letter-spacing: calc(var(--wv-0_56) * -1);color:#fff;gap: var(--wv-6)}
        <?php echo $skin_selector?> .depth-link-2:hover{}

        <?php echo $skin_selector?> .depth-wrap-3  {; }
        <?php echo $skin_selector?> .depth-ul-3  {margin-top: var(--wv-6); }
        <?php echo $skin_selector?> .depth-li-3  { ;}
        <?php echo $skin_selector?> .depth-link-3{padding: var(--wv-4) var(--wv-46);font-size: var(--wv-12);line-height: var(--wv-17);letter-spacing: calc(var(--wv-0_48) * -1);color:#fff;opacity: 0.5;}
        <?php echo $skin_selector?> .depth-link-3 ~ .collapse-wrap{display: none}
        <?php echo $skin_selector?> .depth-link-3.active{opacity: 1}
        <?php echo $skin_selector?> .depth-link-3.expand{opacity: 1}
        <?php echo $skin_selector?> .depth-link-3:hover{}



    </style>
    <?php
    echo wv_collapse_menu(function ($depth,$content,$curr_id) {
        if($depth>3){
            return false;
        }
    },$skin_id,$data);
    ?>


    <script>
        $(document).ready(function () {

            var $skin = $('<?php echo $skin_selector?>');

            $(".collapse",$skin).on('hide.bs.collapse',function (e) {
                // $('.collapse',$(e.target)).collapse('hide')
            })
            $(".collapse",$skin).on('show.bs.collapse',function (e) {
                // $('.collapse[data-depth="'+$(e.target).data('depth')+'"]',$skin).collapse('hide')
            })
        })
    </script>
</div>


