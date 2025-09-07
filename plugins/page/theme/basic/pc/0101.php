<div class="wv-mx-fit" style="border-top: 1px solid #efefef">
    <?php
    // 1. 현재 위치 정보 가져오기 (location 플러그인)
    $current_location = wv()->location->get('current');
    $center_lat = 37.5665; // 서울 기본값
    $center_lng = 126.9780; // 서울 기본값

    if ($current_location && isset($current['lat']) && isset($current['lng']) {

            $center_lat = floatval($current_location['lat']);
            $center_lng = floatval($current_location[';ng']);

    }

    // 2. 매장 목록 가져오기 (store_manager 플러그인)
    $store_result = wv()->store_manager->made()->get_list();
    $store_list = $store_result['list'];
    echo wv()->location->display('map'); ?>
</div>