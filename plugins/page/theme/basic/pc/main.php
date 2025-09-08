<div class="wv-vstack">
    <div>
        <img src="<?php echo $wv_page_skin_url; ?>/img/main/1.png" class="w-full" alt="">
    </div>


    <?php
    $skin_data = array(
        'text1' => '먹고 싶은 메뉴, 가게 검색',
    );
    echo wv_widget('content/main_search', $skin_data)
    ?>
    <?php
    $skin_data = array(
        'text1' => '먹고 싶은 메뉴, 가게 검색',
    );
    echo wv()->store_manager->made()->get()->store->render_part('category_list', 'view');
    ?>
    <div class="vstack" style="background-color: #efefef;padding:var(--wv-5) 0;row-gap: var(--wv-1)">
    <?php
    echo wv_widget('content/popular_store')
    ?>
    </div>
</div>