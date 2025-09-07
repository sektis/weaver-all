<?php
/**
 * Page 플러그인 - Basic 테마 0101 스킨
 * 파일: plugins/page/theme/basic/pc/0101.php
 *
 * Location 플러그인 Map 스킨의 이벤트를 리스닝하여
 * Store Manager 매장 데이터와 연동
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// Store Manager 매장 목록 가져오기
$store_result = wv()->store_manager->made()->get_list();
$store_list = $store_result['list'];

// Map 옵션 설정
$map_options = array(
    'height' => '#content-wrapper',
    'clustering' => true,
    'map_id' => 'store-map-main'
);
?>

<div class="wv-mx-fit" style="border-top: 1px solid #efefef">

    <!-- Location 플러그인 Map 스킨 호출 -->
    <div class="map-container">
        <?php echo wv()->location->display('map', $map_options); ?>
    </div>

    <!-- 매장 정보 패널 (향후 확장용) -->
    <div id="store-info-panel" style="display: none; padding: 20px; background: #f8f9fa; border-top: 1px solid #dee2e6;">
        <h4>매장 정보</h4>
        <div id="store-list-container">
            <!-- 매장 목록이 여기에 표시됩니다 -->
        </div>
    </div>

    <script>
        $(document).ready(function() {


            // Store 데이터 (PHP에서 전달)
            var storeData = <?php echo json_encode($store_list); ?>;
            $(document).on('wv_location_map_changeed', function(event, data) {

                var bounds = data.bounds;
                // bounds.sw_lat, bounds.sw_lng, bounds.ne_lat, bounds.ne_lng

                // TODO: Store Manager 매장 조회
                console.log('지동change', bounds);
            });



            /**
             * TODO: 매장 관련 헬퍼 함수들 (향후 구현)
             */

            // 매장 마커 생성
            function createStoreMarkers(map, stores) {
                console.log('TODO: 매장 마커 생성', stores.length + '개');

                // stores.forEach(function(store) {
                //     if (store.lat && store.lng) {
                //         var marker = new kakao.maps.Marker({
                //             position: new kakao.maps.LatLng(store.lat, store.lng),
                //             title: store.name
                //         });
                //
                //         // 마커 클릭 이벤트
                //         kakao.maps.event.addListener(marker, 'click', function() {
                //             showStoreDetail(store);
                //         });
                //     }
                // });
            }

            // 지도 영역 내 매장 필터링
            function filterStoresByBounds(stores, bounds) {
                console.log('TODO: 매장 필터링', bounds);

                // return stores.filter(function(store) {
                //     return store.lat >= bounds.sw.lat && store.lat <= bounds.ne.lat &&
                //            store.lng >= bounds.sw.lng && store.lng <= bounds.ne.lng;
                // });

                return stores; // 임시로 전체 반환
            }

            // 매장 목록 표시
            function displayStoreList(stores) {
                console.log('TODO: 매장 목록 표시', stores.length + '개');

                // var html = '';
                // stores.forEach(function(store) {
                //     html += '<div class="store-item">';
                //     html += '<h5>' + store.name + '</h5>';
                //     html += '<p>' + store.address + '</p>';
                //     html += '</div>';
                // });
                // $('#store-list-container').html(html);
                // $('#store-info-panel').show();
            }

            // 매장 상세 정보 표시
            function showStoreDetail(store) {
                console.log('TODO: 매장 상세 정보 표시', store);

                // 모달이나 사이드 패널에 매장 상세 정보 표시
                // $('#store-detail-modal').modal('show');
                // populateStoreDetail(store);
            }

            console.log('✅ Page 0101 이벤트 리스너 등록 완료');
        });
    </script>
</div>