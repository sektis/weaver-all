<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $current_store_wr_id;
if($only_main){
    $menus = wv_get_keys_by_nested_value($row['menu'],'1',true,'is_main');
}elseif($only_not_main){
    $menus = wv_get_keys_by_nested_value($row['menu'],'0',true,'is_main');
}else{
    $menus = $row['menu'];
}
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="ba">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{font-size: var(--wv-16);font-weight: 600}
        <?php echo $skin_selector?> .skin-line:last-of-type {display: none}



        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  w-full    " style=" ; background-color: #fff">

        <div class="  vstack menu-each-wrap" style="row-gap: var(--wv-20)">


                <?php foreach ($menus as $menu){
                    if(!$menu)continue;

                    ?>
                    <div  class="menu-each">
                        <div class="hstack align-items-start" style="padding: var(--wv-12) 0 var(--wv-20);gap:var(--wv-42)">
                            <div class="col">
                                <p class="fs-[16/22/-0.64/600/#0D171B]"><?php echo $menu['name']; ?></p>
                                <p class="fs-[12/17/-0.48/500/#97989C] mt-[2px]"><?php echo $menu['desc']?get_text($menu['desc']):'미입력'; ?></p>
                                <div class="fs-[16/22/-0.64/700/#0D171B] mt-[10px]">
                                    <?php foreach ($menu['prices'] as $price){ ?>
                                        <div class="hstack" style="gap:var(--wv-5)">
                                            <?php if($price['name']){ ?><span><?php echo $price['name']; ?></span><?php } ?>
                                            <span><?php echo number_format($price['price']); ?>원</span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="w-[90px] h-[90px] col-auto">
                                <?php if($menu['main_image']){ ?>
                                    <img src="<?php echo $menu['main_image']; ?>" alt="" class="wh-100 object-fit-cover" style="border-radius: var(--wv-4);overflow: hidden">
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="skin-line " style="height: var(--wv-2);background-color: #efefef"></div>
                <?php } ?>


        </div>


    </div>

    <script>
        $(document).ready(function() {
            var $skin = $("<?php echo $skin_selector?>");
        });
    </script>
</div>