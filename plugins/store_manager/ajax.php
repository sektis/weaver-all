<?php
/**
 * Store Manager 플러그인 - Ajax 엔드포인트
 * 파일: plugins/store_manager/ajax.php
 *
 * 지도 영역 기반 매장 조회 API
 */
include_once '_common.php';
// POST/GET 데이터 받기
$action = wv_get_param('action', '');
$sw_lat = wv_get_param('sw_lat', 0);
$sw_lng = wv_get_param('sw_lng', 0);
$ne_lat = wv_get_param('ne_lat', 0);
$ne_lng = wv_get_param('ne_lng', 0);

// 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

try {
    switch ($action) {
        case 'get_stores_by_bounds':
            $result = get_stores_by_bounds($sw_lat, $sw_lng, $ne_lat, $ne_lng);
            echo json_encode($result);
            break;

        case 'get_store_detail':
            $store_id = wv_get_param('store_id', 0);
            $result = get_store_detail($store_id);
            echo json_encode($result);
            break;

        default:
            throw new Exception('Invalid action: ' . $action);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage()
    ));
}

/**
 * 지도 영역 내 매장 조회
 * @param float $sw_lat 남서쪽 위도
 * @param float $sw_lng 남서쪽 경도
 * @param float $ne_lat 북동쪽 위도
 * @param float $ne_lng 북동쪽 경도
 * @return array
 */
function get_stores_by_bounds($sw_lat, $sw_lng, $ne_lat, $ne_lng) {
    // 입력값 검증
    if (!$sw_lat || !$sw_lng || !$ne_lat || !$ne_lng) {
        throw new Exception('잘못된 좌표 정보입니다.');
    }

    if ($sw_lat >= $ne_lat || $sw_lng >= $ne_lng) {
        throw new Exception('좌표 범위가 올바르지 않습니다.');
    }

    // Store Manager에서 매장 목록 조회
    $manager = wv()->store_manager->made();

    // WHERE 조건 구성 (location 파트의 lat, lng 기준)
    $where_location = array(
        'lat' => array(
            'gte' => $sw_lat,
            'lte' => $ne_lat
        ),
        'lng' => array(
            'gte' => $sw_lng,
            'lte' => $ne_lng
        )
    );

    // get_list 옵션
    $options = array(
        'where_location' => $where_location,
        'order' => 'wr_datetime desc',
        'limit' => 1000  // 최대 1000개까지
    );

    $result = $manager->get_list($options);

    // 응답 데이터 정리
    $stores = array();
    if (isset($result['list']) && is_array($result['list'])) {
        foreach ($result['list'] as $item) {
            // location 파트에서 좌표 정보 추출
            $lat = null;
            $lng = null;

            if (isset($item->location)) {
                $lat = floatval($item->location->lat);
                $lng = floatval($item->location->lng);
            }

            // 좌표가 없는 매장은 제외
            if (!$lat || !$lng) continue;

            // 범위 재검증 (혹시나 하는 이중체크)
            if ($lat < $sw_lat || $lat > $ne_lat || $lng < $sw_lng || $lng > $ne_lng) {
                continue;
            }

            $stores[] = array(
                'wr_id' => intval($item->wr_id),
                'name' => $item->store->name ?? $item->wr_subject,
                'lat' => $lat,
                'lng' => $lng,
                'category' => $item->store->category ?? '',
                'tel' => $item->store->tel ?? '',
                'address' => $item->location->address_name ?? '',
                // 추가 필요한 필드들...
            );
        }
    }

    return array(
        'success' => true,
        'count' => count($stores),
        'stores' => $stores,
        'bounds' => array(
            'sw_lat' => $sw_lat,
            'sw_lng' => $sw_lng,
            'ne_lat' => $ne_lat,
            'ne_lng' => $ne_lng
        ),
        'timestamp' => time()
    );
}

/**
 * 특정 매장 상세 정보 조회
 * @param int $store_id
 * @return array
 */
function get_store_detail($store_id) {
    if (!$store_id) {
        throw new Exception('매장 ID가 필요합니다.');
    }

    $manager = wv()->store_manager->made();
    $store = $manager->get($store_id);

    if (!$store) {
        throw new Exception('매장을 찾을 수 없습니다.');
    }

    return array(
        'success' => true,
        'store' => array(
            'wr_id' => intval($store->wr_id),
            'name' => $store->store->name ?? $store->wr_subject,
            'lat' => floatval($store->location->lat ?? 0),
            'lng' => floatval($store->location->lng ?? 0),
            'category' => $store->store->category ?? '',
            'tel' => $store->store->tel ?? '',
            'address' => $store->location->address_name ?? '',
            'notice' => $store->store->notice ?? '',
            'business_hours' => $store->store->business_hours ?? '',
            // 메뉴, 휴무일 등 추가 정보도 필요시 포함
        )
    );
}