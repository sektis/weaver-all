<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$depth=1;
?>

<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .depth-ul-1{align-items: start;gap: var(--wv-16_5)}
        <?php echo $skin_selector?> .depth-li-1{transition: all .1s }
        <?php echo $skin_selector?> .depth-link-1{text-align: center;font-weight: 600;color:#0D171B}
        <?php echo $skin_selector?> .depth-link-1.active{font-weight: 700}
        <?php echo $skin_selector?> .depth-link-1:not(.active) *{filter: invert(97%) sepia(0%) saturate(54%) hue-rotate(134deg) brightness(87%) contrast(89%);}




    </style>

    <div class="position-relative">
        <div   class="depth-wrap-<?php echo $depth?>  " data-parent-menu-id="<?php echo $parent_menu_id; ?>" data-depth="<?php echo $depth; ?>" style="--wv-menu-depth:<?php echo $depth; ?>">
            <ul class="nav depth-ul-<?php echo $depth?>  " >
                <?php
                foreach ($data as $i=>$menu) {
                    $child_id = "{$curr_id}-{$i}";
                    ?>
                    <li class="depth-li-<?php echo $depth?> col" >

                        <a  class="depth-link-<?php echo $depth?> d-flex-center fs-[10/11/-0.5/] <?php echo ($menu['expand'] or $menu['active'])?'active':''?>" href="<?php echo $menu['url']?>"  target="<?php echo $menu['target']?>" data-menu-id="<?php echo $menu['id']?>" data-depth="<?php echo $depth; ?>">
                          <span class="d-block">
                              <span class="d-block"><img src="<?php echo $menu['icon']?>" alt="" class="w-[30px]"></span>
                              <span class="d-block"><?php echo $menu['name']?></span>
                          </span>
                        </a>



                    </li>
                <?php }?>
            </ul>
        </div>    </div>

    <script>
        $(document).ready(function () {

            var $skin = $('<?php echo $skin_selector?>');


        })
    </script>
</div>


