<div class="wv-vstack">
    <a href="/boot.html">test</a>
    <div>
        <img src="<?php echo $wv_page_skin_url; ?>/img/main/1.png" class="w-full" alt="">
    </div>
    <?php
    $data = array(
    );
    echo wv_widget('main/map',$data);
    ?>

    <?php
    $skin_data = array(
        'text1' => '먹고 싶은 메뉴, 가게 검색',
    );
    echo wv_widget('main/search', $skin_data)
    ?>
    <?php
    echo wv_widget('main/category');
    ?>
    <div class="vstack" style="background-color: #efefef;padding:var(--wv-5) 0;row-gap: var(--wv-1)">
    <?php
    $data = array(
            'text1'=>'우리동네 인기 가게'
    );
    echo wv_widget('main/our_town_store',$data);
    ?>
    <?php
    $data = array(
        'text1'=>'우리동네 신규 가게'
    );
    echo wv_widget('main/our_town_store',$data);
    ?>
    </div>
</div>