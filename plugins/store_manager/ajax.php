<?php
include_once '_common.php';

if($action == 'get_stores_by_bounds'){

    // 입력값 검증
    if (!$sw_lat || !$sw_lng || !$ne_lat || !$ne_lng) {
        alert('잘못된 좌표 정보입니다.');
    }

    if ($sw_lat >= $ne_lat || $sw_lng >= $ne_lng) {
        alert('좌표 범위가 올바르지 않습니다.');
    }

    // Store Manager에서 매장 목록 조회
    $manager = wv()->store_manager->made();

    // WHERE 조건 구성 (확장테이블 s의 물리키 사용)
    $where_s = array(
        "location_lat BETWEEN {$sw_lat} AND {$ne_lat}",
        "location_lng BETWEEN {$sw_lng} AND {$ne_lng}"
    );

    // get_list 옵션
    $options = array(
        'where_s' => $where_s,
        'order_by' => 'w.wr_datetime DESC',
        'rows' => 1000  // 최대 1000개까지
    );

    $result = $manager->get_list($options);

    // 응답 데이터 정리
    $stores = array();
    if (isset($result['list']) && is_array($result['list'])) {
        foreach ($result['list'] as $item) {
            // location 파트에서 좌표 정보 추출 (일반 파트 - Array 접근)
            $lat = null;
            $lng = null;

            if (isset($item['location']) && is_array($item['location'])) {
                $lat = floatval($item['location']['lat']);
                $lng = floatval($item['location']['lng']);
            }

            // 좌표가 없는 매장은 제외
            if (!$lat || !$lng) continue;

            // 범위 재검증 (혹시나 하는 이중체크)
            if ($lat < $sw_lat || $lat > $ne_lat || $lng < $sw_lng || $lng > $ne_lng) {
                continue;
            }

            $stores[] = array(
                'wr_id' => intval($item['wr_id']),
                'name' => isset($item['store']['name']) ? $item['store']['name'] : $item['wr_subject'],
                'lat' => $lat,
                'lng' => $lng,
                'category' => isset($item['store']['category']) ? $item['store']['category'] : '',
                'category_text' => isset($item['store']['category_text']) ? $item['store']['category_text'] : '',
                'category_icon' => isset($item['store']['category_icon']) ? $item['store']['category_icon'] : '',
                'main_image' => isset($item['store']['main_image']) ? $item['store']['main_image'] : '',
                'tel' => isset($item['store']['tel']) ? $item['store']['tel'] : '',
                'address' => isset($item['location']['address_name']) ? $item['location']['address_name'] : '',
                'notice' => isset($item['store']['notice']) ? $item['store']['notice'] : '',
                'store_info' => '<div>dasdasda</div>',
            );
        }
    }

    // 성공 응답
    wv_json_exit(array(
        'result' => true,
        'content' => array(
            'count' => count($stores),
            'stores' => $stores,
            'bounds' => array(
                'sw_lat' => $sw_lat,
                'sw_lng' => $sw_lng,
                'ne_lat' => $ne_lat,
                'ne_lng' => $ne_lng
            ),
            'category_icon_wrap'=>wv()->store_manager->made()->plugin_url.'/img/category_list/small/category_icon_wrap.png',
            'category_icon_wrap_on'=>wv()->store_manager->made()->plugin_url.'/img/category_list/small/category_icon_wrap_on.png',

            'timestamp' => time()
        )
    ));
}

// 잘못된 action
alert('잘못된 요청입니다.');