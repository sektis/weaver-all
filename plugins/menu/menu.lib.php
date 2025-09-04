<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!function_exists('wv_depth_menu')){
    function wv_depth_menu($callback, $curr_id, $data, $max_depth='99', $depth=1,$parent_menu_id=''){

        if($depth==1){
            $curr_id.='-top';
        }
        if($depth>$max_depth){
            return '';
        }

        ob_start();
        ?>

        <div id="<?php echo $curr_id; ?>" class="depth-wrap-<?php echo $depth?>  " data-parent-menu-id="<?php echo $parent_menu_id; ?>" data-depth="<?php echo $depth; ?>" style="--wv-menu-depth:<?php echo $depth; ?>">
            <ul class="nav depth-ul-<?php echo $depth?>  " >
                <?php
                foreach ($data as $i=>$menu) {
                    $child_id = "{$curr_id}-{$i}";
                    ?>
                    <li class="depth-li-<?php echo $depth?>" >

                        <a  class="depth-link-<?php echo $depth?> d-flex-center <?php echo ($menu['expand'] or $menu['active'])?'active':''?>" href="<?php echo $menu['url']?>"  target="<?php echo $menu['target']?>" data-menu-id="<?php echo $menu['id']?>" data-depth="<?php echo $depth; ?>">
                            <span><?php echo $menu['name']?></span>
                            <span class="wv-hover-box"><?php echo $menu['name']?></span>
                        </a>

                        <?php if($menu['sub']){
                            echo wv_depth_menu($callback, $child_id,$menu['sub'], $max_depth,$depth+1,$menu['id']);
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
if(!function_exists('wv_swiper_menu')){
    function wv_swiper_menu($callback, $curr_id, $data, $max_depth='99', $depth=1,$parent_menu_id=''){

        if($depth==1){
            $curr_id.='-top';
        }
        if($depth>$max_depth){
            return '';
        }

        ob_start();
        ?>

        <div id="<?php echo $curr_id; ?>" class="swiper depth-wrap-<?php echo $depth?>" data-parent-menu-id="<?php echo $parent_menu_id; ?>" data-depth="<?php echo $depth; ?>" style="--wv-menu-depth:<?php echo $depth; ?>">
             <div class="swiper-wrapper depth-ul-<?php echo $depth?>">

                <?php
                foreach ($data as $i=>$menu) {
                    $child_id = "{$curr_id}-{$i}";
                    ?>
                    <div class="swiper-slide depth-li-<?php echo $depth?>" >

                        <a  class="depth-link-<?php echo $depth?> d-flex-center   <?php echo ($menu['expand'] or $menu['active'])?'active':''?>" href="<?php echo $menu['url']?>"  target="<?php echo $menu['target']?>" data-menu-id="<?php echo $menu['id']?>" data-depth="<?php echo $depth; ?>">
                            <span><?php echo $menu['name']?></span>
                            <span class="wv-hover-box"><?php echo $menu['name']?></span>
                        </a>

                        <?php if($menu['sub']){
                            echo wv_swiper_menu($callback, $child_id,$menu['sub'], $max_depth,$depth+1,$menu['id']);
                        }?>

                    </div>
                <?php }?>

             </div>
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
if(!function_exists('wv_swiper_wrap_loop')){
    function wv_swiper_wrap_loop($depth_array,$index=0){

        if($index==0){
            $depth_array = array_unique($depth_array);
            sort($depth_array);
        }
        ob_start();

        ?>
            <div class="depth-swiper-<?php echo $depth_array[$index]; ?>">
                <div class="swiper sub-swiper-<?php echo $depth_array[$index]; ?>" >
                    <div class="swiper-wrapper">

                    </div>
                </div>
                <?php
                if(isset($depth_array[$index+1])){
                    echo wv_swiper_wrap_loop($depth_array,$index+1);
                }
                ?>
            </div>
        <?php


        return ob_get_clean();
    }
}
if(!function_exists('wv_collapse_menu')){
    function wv_collapse_menu($callback, $curr_id, $data,$parent=array(), $max_depth='99', $depth=1,$parent_menu_id=''){

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

                        <div class="position-relative">
                            <a  class="depth-link-<?php echo $depth?> d-flex-start h-100 <?php echo ($menu['expand'])?'expand':''?> <?php echo ($menu['active'])?'active':''?>" href="<?php echo $menu['url']?>"  target="<?php echo $menu['target']?>" data-menu-id="<?php echo $menu['id']?>" data-depth="<?php echo $depth; ?>">
                                <span><?php echo $menu['name']?></span>
                                <span class="wv-hover-box"><?php echo $menu['name']?></span>

                            </a>
                            <?php if($menu['sub']){?>
                                <div data-bs-target="<?php echo "#{$child_id}"; ?>" data-bs-toggle="collapse" class="collapse-wrap cursor-pointer  " aria-expanded="<?php echo ($menu['expand'] or $menu['active'])?'true':'false'?>" >
                                    <div class="collapse-icon d-flex-center"><i class="fa-solid"></i></div>
                                </div>
                            <?php } ?>
                        </div>


                        <?php if($menu['sub']){
                            echo wv_collapse_menu($callback, $child_id,$menu['sub'],$menu, $max_depth,$depth+1,$menu['id']);
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
if(!function_exists('wv_make_menu_display')){
    function wv_make_menu_display($make_name,$skin,$arr,$var_name,$all_name='전체'){
        global $config;
        $menu = wv('menu')->make($make_name);
        $parse = wv_get_current_url($config['cf_bbs_rewrite']!==0,'',true);
        unset($parse['query'][$var_name]);
        $menu->append(array('name' => $all_name, 'url' => wv_build_url($parse)));

        foreach ($arr as $key=>$val){
            $parse['query'][$var_name] = $val;
            $menu->append(array('name' => $val, 'url' => wv_build_url($parse)));
        }

        return $menu->displayMenu($skin);
    }
}