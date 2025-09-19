<?php if(wv('menu')->made()->getActiveMenuId()){ ?>
<div class="container">
    <div class="row justify-content-between">
        <div class="col-lg-auto">
            <?php echo wv('menu')->made()->displayNavigation('common/navigation')?>
        </div>
        <div class="col-lg-auto">
            <?php echo wv('menu')->made()->displayBreadcrumb('common/breadcrumb')?>
        </div>
    </div>
    <div class="row mt-[30px]" style="--bs-gutter-x: var(--wv-30)">
        <div class="col-auto view-pc">
            <?php echo wv('menu')->made()->displaySubMenu('common/left')?>
        </div>
        <div class="col text-truncate">
            <!--><!-->
        </div>
    </div>
</div>
<?php }else {?>
    <div class="container col ">
        <!--><!-->
    </div>
<?php }?>
