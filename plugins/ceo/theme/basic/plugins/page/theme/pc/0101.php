 

<div class="hstack menu-tab-top" role="tablist">
    <a href="#" id="home-tab" class="" data-bs-toggle="tab" data-bs-target="#0101-basic">기본 정보</a>
    <a href="#" id="home-tab" class="" data-bs-toggle="tab" data-bs-target="#0101-biz">운영 정보</a>
    <a href="#" id="home-tab" class="active" data-bs-toggle="tab" data-bs-target="#0101-menu">메뉴 관리</a>
</div>

<div class="tab-content menu-tab-content " id="myTabContent">
    <div class="tab-pane fade  " id="0101-basic" >
        <div class="tab-pane-inner  ">
            <div>
                <?php echo $current_store->store->render_part('ceo/name','view');; ?>
            </div>
            <div>
                <?php echo $current_store->store->render_part('ceo/image','view');; ?>
            </div>
            <div>
                <?php echo $current_store->store->render_part('ceo/notice','view');; ?>
            </div>
            <div>
                <?php echo $current_store->location->render_part('ceo/address','view');; ?>
            </div>
            <div>
                <?php echo $current_store->store->render_part('ceo/tel','view');; ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade " id="0101-biz" >
        <div class="tab-pane-inner  ">
            <div>
                <?php echo $current_store->biz->render_part('ceo/open_time','view');; ?>
            </div>
            <div>
                <?php echo $current_store->biz->render_part('ceo/break_time','view');; ?>
            </div>
            <div>
                <?php echo $current_store->biz->render_part('ceo/holiday','view');; ?>
            </div>
            <div>
                <?php echo $current_store->biz->render_part('ceo/parking','view');; ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade show active" id="0101-menu" >
        <div class="tab-pane-inner  ">
            <div>
                <?php echo $current_store->menu->render_part('ceo/menu','view');; ?>
            </div>

        </div>
    </div>
</div>