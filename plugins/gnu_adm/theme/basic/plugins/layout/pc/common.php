<div class="d-flex col">
    <div class="col-auto" style="padding-top: var(--wv-18);width: var(--wv-200);">
        <?php echo wv('menu')->made('left_menu')->displayMenu('left_collapse'); ?>
    </div>
    <div class="col bg-white" style="min-height: 100%">

        <div class="container py-[12px]" style="border-bottom: 2px solid #efefef;min-height: var(--wv-54)">
            <div class="hstack justify-content-between">
                <p class="page-title fs-[18//-0.72/600/#0D171B]"><?php echo $page_title?$page_title:wv('menu')->made('left_menu')->getMenu(wv('menu')->made('left_menu')->getActiveMenuId())['name']; ?></p>
            </div>

        </div>

        <div class="container py-[26px]" >
            <!--><!-->
        </div>
    </div>
</div>