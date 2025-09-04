<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<nav id="<?php echo $skin_id?>" style="--bs-breadcrumb-divider: '>';--wv-py:10;" class="h-100 d-flex align-items-center" >
    <style>
        #<?php echo $skin_id?> {}
    </style>
    <ol class="breadcrumb mb-0 justify-content-center">
        <li class="breadcrumb-item"><a href="<?php echo G5_URL?>" style="--wv-p:10"> <i class="fa-solid fa-house"></i></a></li>
        <?php $i=0;
        foreach ($data as $menu) { ?>
            <li class="breadcrumb-item"><a style="--wv-px:10" href="<?php echo $menu['url']?>" target="<?php echo $menu['target']?>"><?php echo $menu['name']?></a></li>
        <?php $i++;}?>
    </ol>
</nav>





