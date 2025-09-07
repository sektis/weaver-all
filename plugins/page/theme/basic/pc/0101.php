<div class="wv-mx-fit" style="border-top: 1px solid #efefef">
    <?php
    /**
     * Page 플러그인 - Basic 테마 0101 스킨
     * 파일: plugins/page/theme/basic/pc/0101.php
     *
     * Store Manager + Location 플러그인 연동
     * 카카오맵 클러스터링을 통한 매장 위치 표시
     */
    if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

    // 1. 현재 위치 정보 가져오기 (location 플러그인)
    $current_location = wv()->location->get('current');
    $center_lat = 37.5665; // 서울 기본값
    $center_lng = 126.9780; // 서울 기본값

    if ($current_location && isset($current_location[0])) {
        $current = $current_location[0];
        if (isset($current['lat']) && isset($current['lng'])) {
            $center_lat = floatval($current['lat']);
            $center_lng = floatval($current['lng']);
        }
    }

    // 2. 매장 목록 가져오기 (store_manager 플러그인)
    $store_result = wv()->store_manager->made()->get_list();
    $store_list = $store_result['list'];

    // 3. 매장 데이터를 JavaScript용으로 변환
    $stores_for_map = array();
    foreach ($store_list as $store) {
        // location 파트에서 좌표 정보 추출
        $lat = isset($store['lat']) ? floatval($store['lat']) : null;
        $lng = isset($store['lng']) ? floatval($store['lng']) : null;

        // 좌표가 있는 매장만 지도에 표시
        if ($lat && $lng) {
            $store_info = array(
                'id' => $store['wr_id'],
                'lat' => $lat,
                'lng' => $lng,
                'name' => isset($store['wr_subject']) ? $store['wr_subject'] : '매장명',
                'address' => '',
                'region_1depth_name' => isset($store['region_1depth_name']) ? $store['region_1depth_name'] : '',
                'region_2depth_name' => isset($store['region_2depth_name']) ? $store['region_2depth_name'] : '',
                'region_3depth_name' => isset($store['region_3depth_name']) ? $store['region_3depth_name'] : '',
                'tel' => '',
                'category' => ''
            );

            // 주소 정보 조합
            if ($store_info['region_1depth_name'] && $store_info['region_3depth_name']) {
                $store_info['address'] = $store_info['region_1depth_name'] . ' ' .
                    ($store_info['region_2depth_name'] ? $store_info['region_2depth_name'] . ' ' : '') .
                    $store_info['region_3depth_name'];
            }

            // store 파트에서 추가 정보 추출
            if (isset($store['store']) && is_array($store['store'])) {
                foreach ($store['store'] as $store_detail) {
                    if (isset($store_detail['tel'])) $store_info['tel'] = $store_detail['tel'];
                    if (isset($store_detail['category'])) $store_info['category'] = $store_detail['category'];
                    break; // 첫 번째 매장 정보만 사용
                }
            }

            $stores_for_map[] = $store_info;
        }
    }

    // 4. AJAX 요청 처리 (맵 영역 변경시 매장 필터링)
    if (isset($_GET['ajax']) && $_GET['ajax'] === 'filter_stores') {
        $sw_lat = isset($_GET['sw_lat']) ? floatval($_GET['sw_lat']) : null;
        $sw_lng = isset($_GET['sw_lng']) ? floatval($_GET['sw_lng']) : null;
        $ne_lat = isset($_GET['ne_lat']) ? floatval($_GET['ne_lat']) : null;
        $ne_lng = isset($_GET['ne_lng']) ? floatval($_GET['ne_lng']) : null;

        if ($sw_lat && $sw_lng && $ne_lat && $ne_lng) {
            // WHERE 절을 통한 매장 필터링
            $where_location = array(
                'gte' => array('lat' => $sw_lat, 'lng' => $sw_lng),
                'lte' => array('lat' => $ne_lat, 'lng' => $ne_lng)
            );

            $filtered_result = wv()->store_manager->made()->get_list(array(
                'where_location' => $where_location
            ));

            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => true,
                'stores' => $filtered_result['list'],
                'total_count' => $filtered_result['total_count']
            ));
            exit;
        }
    }
    ?>

    <div class="store-map-container">
        <style>
            .store-map-container {
                height: 100vh;
                position: relative;
            }

            .map-wrapper {
                width: 100%;
                height: 100%;
                position: relative;
            }

            .kakao-map {
                width: 100%;
                height: 100%;
            }

            .map-controls {
                position: absolute;
                top: 20px;
                left: 20px;
                z-index: 1000;
                background: white;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                min-width: 250px;
            }

            .map-controls h3 {
                margin: 0 0 10px 0;
                font-size: 16px;
                font-weight: 600;
            }

            .store-counter {
                font-size: 14px;
                color: #666;
                margin-bottom: 10px;
            }

            .control-buttons {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .control-btn {
                padding: 6px 12px;
                border: 1px solid #ddd;
                background: white;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
                transition: all 0.2s;
            }

            .control-btn:hover {
                background: #f5f5f5;
            }

            .control-btn.active {
                background: #007bff;
                color: white;
                border-color: #007bff;
            }

            .store-info-window {
                padding: 10px;
                min-width: 200px;
            }

            .store-info-window h4 {
                margin: 0 0 8px 0;
                font-size: 14px;
                font-weight: 600;
            }

            .store-info-window p {
                margin: 4px 0;
                font-size: 12px;
                color: #666;
            }

            .loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255,255,255,0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2000;
                display: none;
            }

            .loading-spinner {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #007bff;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>

        <!-- 지도 컨트롤 패널 -->
        <div class="map-controls">
            <h3>매장 지도</h3>
            <div class="store-counter">
                전체 매장: <span id="total-stores"><?php echo count($stores_for_map); ?></span>개
                | 화면 내: <span id="visible-stores">-</span>개
            </div>
            <div class="control-buttons">
                <button type="button" class="control-btn" id="btn-current-location">내 위치</button>
                <button type="button" class="control-btn" id="btn-refresh-map">새로고침</button>
                <button type="button" class="control-btn" id="btn-cluster-toggle" data-cluster="on">클러스터링</button>
                <button type="button" class="control-btn" id="btn-fit-bounds">전체보기</button>
            </div>
        </div>

        <!-- 로딩 오버레이 -->
        <div class="loading-overlay" id="loading-overlay">
            <div class="loading-spinner"></div>
        </div>

        <!-- 카카오맵 -->
        <div class="map-wrapper">
            <div id="kakao-map" class="kakao-map"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // 전역 변수
            var map, clusterer, markers = [];
            var infoWindow = new kakao.maps.InfoWindow({zIndex:1});
            var isClusterEnabled = true;

            // 매장 데이터
            var allStores = <?php echo json_encode($stores_for_map); ?>;
            var currentStores = allStores.slice(); // 복사본

            console.log('로드된 매장 수:', allStores.length);

            /**
             * 카카오맵 초기화
             */
            function initKakaoMap() {
                if (!window.kakao || !window.kakao.maps) {
                    console.error('카카오맵 API가 로드되지 않았습니다.');
                    setTimeout(initKakaoMap, 100); // 재시도
                    return;
                }

                // 지도 생성
                var mapContainer = document.getElementById('kakao-map');
                var mapOptions = {
                    center: new kakao.maps.LatLng(<?php echo $center_lat; ?>, <?php echo $center_lng; ?>),
                    level: 8 // 확대 레벨 (1~14)
                };

                map = new kakao.maps.Map(mapContainer, mapOptions);

                // 클러스터러 생성 (마커 클러스터링)
                clusterer = new kakao.maps.MarkerClusterer({
                    map: map,
                    averageCenter: true,
                    minLevel: 6,
                    disableClickZoom: true,
                    styles: [{
                        width: '40px', height: '40px',
                        background: 'rgba(51, 204, 255, .8)',
                        borderRadius: '20px',
                        color: '#fff',
                        textAlign: 'center',
                        fontWeight: 'bold',
                        lineHeight: '40px'
                    }]
                });

                // 초기 매장 마커 생성
                createStoreMarkers(allStores);

                // 지도 이벤트 등록
                registerMapEvents();

                console.log('카카오맵 초기화 완료');
            }

            /**
             * 매장 마커 생성
             */
            function createStoreMarkers(stores) {
                clearMarkers();

                stores.forEach(function(store) {
                    if (!store.lat || !store.lng) return;

                    var markerPosition = new kakao.maps.LatLng(store.lat, store.lng);

                    var marker = new kakao.maps.Marker({
                        position: markerPosition,
                        title: store.name
                    });

                    // 마커 클릭 이벤트
                    kakao.maps.event.addListener(marker, 'click', function() {
                        showStoreInfoWindow(marker, store);
                    });

                    markers.push(marker);
                });

                // 클러스터러에 마커 추가
                if (isClusterEnabled) {
                    clusterer.addMarkers(markers);
                } else {
                    markers.forEach(function(marker) {
                        marker.setMap(map);
                    });
                }

                updateStoreCounter(stores.length);
                console.log('마커 생성 완료:', stores.length + '개');
            }

            /**
             * 매장 정보창 표시
             */
            function showStoreInfoWindow(marker, store) {
                var content = '<div class="store-info-window">' +
                    '<h4>' + store.name + '</h4>' +
                    '<p><i class="fa fa-map-marker"></i> ' + (store.address || '주소 정보 없음') + '</p>';

                if (store.tel) {
                    content += '<p><i class="fa fa-phone"></i> ' + store.tel + '</p>';
                }

                if (store.category) {
                    content += '<p><i class="fa fa-tag"></i> ' + store.category + '</p>';
                }

                content += '<p><a href="/store_detail.php?id=' + store.id + '" target="_blank">상세보기</a></p>';
                content += '</div>';

                infoWindow.setContent(content);
                infoWindow.open(map, marker);
            }

            /**
             * 기존 마커 제거
             */
            function clearMarkers() {
                clusterer.clear();
                markers.forEach(function(marker) {
                    marker.setMap(null);
                });
                markers = [];
                infoWindow.close();
            }

            /**
             * 지도 이벤트 등록
             */
            function registerMapEvents() {
                // 지도 영역 변경시 매장 필터링
                var filterTimeout;
                kakao.maps.event.addListener(map, 'bounds_changed', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() {
                        filterStoresByMapBounds();
                    }, 500); // 0.5초 딜레이
                });

                // 줌 레벨 변경시
                kakao.maps.event.addListener(map, 'zoom_changed', function() {
                    var level = map.getLevel();
                    console.log('줌 레벨:', level);

                    // 줌 레벨에 따른 클러스터러 조정
                    if (level <= 3) {
                        clusterer.setMinLevel(1);
                    } else if (level <= 6) {
                        clusterer.setMinLevel(4);
                    } else {
                        clusterer.setMinLevel(6);
                    }
                });
            }

            /**
             * 지도 화면 내 매장 필터링
             */
            function filterStoresByMapBounds() {
                var bounds = map.getBounds();
                var sw = bounds.getSouthWest(); // 남서쪽 좌표
                var ne = bounds.getNorthEast(); // 북동쪽 좌표

                // 화면 내 매장 필터링
                var visibleStores = allStores.filter(function(store) {
                    return store.lat >= sw.getLat() && store.lat <= ne.getLat() &&
                        store.lng >= sw.getLng() && store.lng <= ne.getLng();
                });

                updateStoreCounter(allStores.length, visibleStores.length);

                console.log('화면 내 매장:', visibleStores.length + '/' + allStores.length);
            }

            /**
             * 매장 카운터 업데이트
             */
            function updateStoreCounter(total, visible) {
                $('#total-stores').text(total);
                if (visible !== undefined) {
                    $('#visible-stores').text(visible);
                }
            }

            /**
             * 현재 위치로 이동
             */
            function moveToCurrentLocation() {
                if ('geolocation' in navigator) {
                    showLoading(true);
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        var moveLatLng = new kakao.maps.LatLng(lat, lng);

                        map.setCenter(moveLatLng);
                        map.setLevel(5);

                        showLoading(false);
                        console.log('현재 위치로 이동:', lat, lng);
                    }, function(error) {
                        showLoading(false);
                        alert('현재 위치를 가져올 수 없습니다.');
                        console.error('위치 오류:', error);
                    });
                } else {
                    alert('이 브라우저는 위치 서비스를 지원하지 않습니다.');
                }
            }

            /**
             * 전체 매장이 보이도록 지도 범위 조정
             */
            function fitMapToBounds() {
                if (allStores.length === 0) return;

                var bounds = new kakao.maps.LatLngBounds();
                allStores.forEach(function(store) {
                    bounds.extend(new kakao.maps.LatLng(store.lat, store.lng));
                });

                map.setBounds(bounds);
                console.log('전체 매장 범위로 지도 조정');
            }

            /**
             * 클러스터링 토글
             */
            function toggleClustering() {
                isClusterEnabled = !isClusterEnabled;
                var $btn = $('#btn-cluster-toggle');

                if (isClusterEnabled) {
                    $btn.text('클러스터링').addClass('active').attr('data-cluster', 'on');
                    createStoreMarkers(currentStores); // 클러스터링 적용
                } else {
                    $btn.text('개별표시').removeClass('active').attr('data-cluster', 'off');
                    createStoreMarkers(currentStores); // 개별 마커 표시
                }

                console.log('클러스터링:', isClusterEnabled ? '활성화' : '비활성화');
            }

            /**
             * 로딩 표시
             */
            function showLoading(show) {
                if (show) {
                    $('#loading-overlay').show();
                } else {
                    $('#loading-overlay').hide();
                }
            }

            /**
             * 버튼 이벤트 등록
             */
            $('#btn-current-location').on('click', moveToCurrentLocation);
            $('#btn-refresh-map').on('click', function() {
                createStoreMarkers(allStores);
            });
            $('#btn-cluster-toggle').on('click', toggleClustering);
            $('#btn-fit-bounds').on('click', fitMapToBounds);

            // 초기 버튼 상태 설정
            $('#btn-cluster-toggle').addClass('active');

            // 카카오맵 초기화 실행
            if (window.kakao && window.kakao.maps) {
                initKakaoMap();
            } else {
                // 카카오맵 API 로드 대기
                var checkKakao = setInterval(function() {
                    if (window.kakao && window.kakao.maps && window.kakao.maps.MarkerClusterer) {
                        clearInterval(checkKakao);
                        initKakaoMap();
                    }
                }, 100);

                // 10초 후에도 로드되지 않으면 오류 표시
                setTimeout(function() {
                    if (!window.kakao || !window.kakao.maps) {
                        clearInterval(checkKakao);
                        alert('카카오맵 API를 로드할 수 없습니다. 페이지를 새로고침해주세요.');
                    }
                }, 10000);
            }
        });
    </script>

    <!-- 카카오맵 API 및 클러스터러 로드 -->
    <script>
        // 카카오맵 클러스터러 API 추가 로드
        if (typeof kakao !== 'undefined' && kakao.maps && !kakao.maps.MarkerClusterer) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/kakao-maps-clusterer@1.0.9/lib/clusterer.min.js';
            script.async = true;
            document.head.appendChild(script);
        }
    </script>
</div>