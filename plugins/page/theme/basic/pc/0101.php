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
    'height_wrapper' => '#content-wrapper',
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

            /**
             * 🗺️ 지도 변경 이벤트 (통합)
             * 지도 이동, 줌 변경시 모두 이 이벤트로 처리
             */
            $(document).on('wv_location_map_changeed', function(event, data) {

                var bounds = data.bounds;
                console.log('지도 변경됨:', bounds);

                // Ajax로 매장 데이터 조회
                fetchStoresByBounds(bounds);
            });

            /**
             * 📡 Ajax로 지도 영역 내 매장 조회
             */
            function fetchStoresByBounds(bounds) {
                var ajaxUrl = '<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php';

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'get_stores_by_bounds',
                        sw_lat: bounds.sw_lat,
                        sw_lng: bounds.sw_lng,
                        ne_lat: bounds.ne_lat,
                        ne_lng: bounds.ne_lng
                    },
                    success: function(response) {
                        console.log('매장 조회 성공:', response);

                        if (response.success && response.stores) {
                            // 새로운 이벤트 발생: Map 스킨에서 마커 처리
                            triggerStoreUpdateEvent(response.stores, bounds);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('매장 조회 실패:', error);
                        console.error('응답:', xhr.responseText);
                    }
                });
            }

            /**
             * 🚀 매장 데이터 업데이트 이벤트 발생
             */
            function triggerStoreUpdateEvent(stores, bounds) {
                var eventData = {
                    stores: stores,
                    bounds: bounds,
                    count: stores.length,
                    timestamp: new Date().getTime()
                };

                // Map 스킨으로 이벤트 전송
                $(document).trigger('wv_location_place_updated', [eventData]);

                console.log('매장 업데이트 이벤트 발생:', eventData.count + '개');
            }

            /**
             * TODO: 지도 영역 기준 매장 필터링 함수
             */
            function filterStoresByBounds(bounds) {
                // var filteredStores = storeData.filter(function(store) {
                //     return store.lat >= bounds.sw_lat &&
                //            store.lat <= bounds.ne_lat &&
                //            store.lng >= bounds.sw_lng &&
                //            store.lng <= bounds.ne_lng;
                // });
                // return filteredStores;
            }

            /**
             * TODO: 매장 마커 생성/업데이트 함수
             */
            function updateStoreMarkers(stores) {
                // var mapInstance = window['wv_location_map_store-map-main'];
                // if (!mapInstance) return;

                // mapInstance.clearMarkers();

                // var markers = stores.map(function(store) {
                //     var marker = new kakao.maps.Marker({
                //         position: new kakao.maps.LatLng(store.lat, store.lng),
                //         title: store.name
                //     });
                //     return marker;
                // });

                // mapInstance.addMarkers(markers);
            }

            /**
             * TODO: 매장 목록 UI 업데이트 함수
             */
            function updateStoreList(stores) {
                // var $container = $('#store-list-container');
                // var html = stores.map(function(store) {
                //     return '<div class="store-item">' +
                //            '<h5>' + store.name + '</h5>' +
                //            '<p>' + store.address + '</p>' +
                //            '</div>';
                // }).join('');
                // $container.html(html);

                // $('#store-info-panel').toggle(stores.length > 0);
            }

            /**
             * TODO: 초기 매장 데이터 로드
             */
            function initializeStores() {
                // updateStoreMarkers(storeData);
                // updateStoreList(storeData);
            }

            // 초기화
            // initializeStores();
        });
    </script>

</div>