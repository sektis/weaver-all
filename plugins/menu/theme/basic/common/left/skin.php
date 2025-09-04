<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$active_title = '';
foreach ($data['sub'] as $key=>$val){
    if($val['active']==1){
        $active_title = $val['name'];
        break;
    }
}
?>
<div id="<?php echo $skin_id?>" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .depth-text-1.active:after{content: '';width: 100%;height: 1px;position: absolute;left:50%;transform: translateX(-50%);top:100%;background-color: #000}
        <?php echo $skin_selector?> .depth-arrow{opacity: 0}
        <?php echo $skin_selector?> .depth-text-1.active + .depth-arrow{opacity: 1}
    </style>

 
        <div class="vstack" style="row-gap: var(--wv-20)">
            <?php
            $i=0;
            foreach ($data['sub'] as $menu) {
            ?>
            <a class="d-flex-between" style="gap:1em"   href="<?php echo $menu['url']?>" target="<?php echo $menu['target']?>"  >
                <span class="position-relative depth-text-1 <?php echo( $menu['active'] or  $menu['expand'])?'active':''?>"><?php echo $menu['name']?></span>
                <span class="depth-arrow">></span>
            </a>
            <?php $i++;}?>
        </div>
  

</div>





