<?php
/**
 * Location 플러그인 - Map 스킨 (범용 버전)
 * 파일: plugins/location/theme/basic/plugins/widget/skin/pc/location/map/skin.php
 *
 * 순수 카카오맵 + 클러스터링 기능
 * 범용적으로 사용 가능한 지도 위젯
 * 이벤트 송출: wv_location_map_changed, wv_location_map_marker_clicked
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 전달받은 옵션 처리
$map_options = isset($data) && is_array($data) ? $data : array();
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-location-map-skin position-relative h-100" style="width: 100%; position: relative;">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .kakao-map {width: 100%; height: 100%;}
        <?php echo $skin_selector?> .current-location-btn {position: absolute; bottom: 20px; right: 20px; z-index: 1000; width: 44px; height: 44px; background: rgba(255, 255, 255, 0.95); border: 1px solid #ddd; border-radius: var(--wv-6); cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); backdrop-filter: blur(4px); transition: all 0.2s;}
        <?php echo $skin_selector?> .current-location-btn:hover {background: #f8f9fa; transform: scale(1.05);}
        <?php echo $skin_selector?> .current-location-btn i {font-size: 18px; color: #007bff;}
        <?php echo $skin_selector?> .loading-overlay {position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 2000;}
        <?php echo $skin_selector?> .loading-spinner {width: 32px; height: 32px; border: 3px solid #f3f3f3; border-top: 3px solid #007bff; border-radius: 50%; animation: wv-map-spin 1s linear infinite;}
        @keyframes wv-map-spin {0% {transform: rotate(0deg);} 100% {transform: rotate(360deg);}}
        @media (max-width: 991.98px) {}
    </style>

    <!-- 로딩 오버레이 -->
    <div class="loading-overlay" id="loading-overlay-<?php echo $skin_id; ?>">
        <div class="loading-spinner"></div>
    </div>

    <!-- 내 위치 버튼 -->
    <button type="button" class="current-location-btn" id="btn-current-location-<?php echo $skin_id; ?>" title="현재 위치로 이동">
        <i class="fa-solid fa-location-crosshairs"></i>
    </button>

    <!-- 카카오맵 컨테이너 -->
    <div class="kakao-map"></div>

    <script>
        // 카카오맵 라이브러리 로드 확인
        function checkKakaoLibraries() {
            if (typeof kakao !== 'undefined' && kakao.maps && typeof kakao.maps.MarkerClusterer === 'function') {
                return true;
            } else {
                return false;
            }
        }

        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var initialLevel = <?php echo isset($map_options['initial_level']) ? intval($map_options['initial_level']) : 8; ?>;
            var minLevel = <?php echo isset($map_options['min_level']) ? intval($map_options['min_level']) : 1; ?>;
            var maxLevel = <?php echo isset($map_options['max_level']) ? intval($map_options['max_level']) : 14; ?>;
            var isClusterEnabled = <?php echo (isset($map_options['clustering']) ? $map_options['clustering'] : false) ? 'true' : 'false'; ?>;

            var map;
            var clusterer = null;
            var markers = [];
            var currentLocationMarker = null;

            // 이벤트 발송 함수
            function triggerMapChangedEvent() {
                var bounds = map.getBounds();
                var center = map.getCenter();
                var level = map.getLevel();
                console.log('map changed')
                $(document).trigger('wv_location_map_changed', {
                    bounds: bounds,
                    center: center,
                    level: level
                });
            }

            function initMap() {
                if (!checkKakaoLibraries()) {
                    console.error('카카오맵 라이브러리가 로드되지 않았습니다.');
                    return;
                }

                // 현재 위치 가져오기 및 지도 생성
                getCurrentLocation();
            }

            function getCurrentLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            var lat = position.coords.latitude;
                            var lng = position.coords.longitude;
                            createMap(lat, lng);
                        },
                        function() {
                            // 기본 위치 (서울)
                            createMap(37.5665, 126.9780);
                        }
                    );
                } else {
                    createMap(37.5665, 126.9780);
                }
            }

            function createMap(lat, lng) {
                var container = $skin.find('.kakao-map')[0];
                var options = {
                    center: new kakao.maps.LatLng(lat, lng),
                    level: initialLevel
                };

                map = new kakao.maps.Map(container, options);

                // 줌 레벨 제한
                map.setMinLevel(minLevel);
                map.setMaxLevel(maxLevel);

                // 클러스터러 초기화
                if (isClusterEnabled) {
                    clusterer = new kakao.maps.MarkerClusterer({
                        map: map,
                        averageCenter: true,
                        minLevel: 5,
                        disableClickZoom: false,
                        styles: [{
                            width: '30px', height: '30px',
                            background: 'rgba(51, 204, 255, .8)',
                            borderRadius: '15px',
                            color: '#fff',
                            textAlign: 'center',
                            fontWeight: 'bold',
                            lineHeight: '31px'
                        }]
                    });
                }

                // 현재 위치 마커 생성
                addCurrentLocationMarker(lat, lng);

                // 지도 이벤트 등록
                setupMapEvents();

                // 외부 통신 이벤트 등록
                setupExternalCommunication();

                // 로딩 오버레이 숨기기
                $skin.find('.loading-overlay').fadeOut();

                // 지도 초기화 완료 후 즉시 이벤트 발송
                setTimeout(function() {
                    triggerMapChangedEvent();
                }, 100);
            }

            function addCurrentLocationMarker(lat, lng) {
                var position = new kakao.maps.LatLng(lat, lng);

                currentLocationMarker = new kakao.maps.Marker({
                    position: position,
                    map: map,
                    image: new kakao.maps.MarkerImage(
                        'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iOCIgZmlsbD0iIzAwN2JmZiIgZmlsbC1vcGFjaXR5PSIwLjMiLz4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iNCIgZmlsbD0iIzAwN2JmZiIvPgo8L3N2Zz4K',
                        new kakao.maps.Size(24, 24),
                        { offset: new kakao.maps.Point(12, 12) }
                    ),
                    zIndex: 999
                });
            }

            function setupMapEvents() {
                var boundsChangeTimeout;

                // 지도 이동/줌 이벤트 (500ms 지연처리)
                kakao.maps.event.addListener(map, 'bounds_changed', function() {
                    clearTimeout(boundsChangeTimeout);
                    boundsChangeTimeout = setTimeout(function() {
                        triggerMapChangedEvent();
                    }, 500);
                });

                // 현재 위치 버튼 이벤트
                $skin.find('.current-location-btn').click(function() {
                    getCurrentLocation();
                    // 현재 위치 이동 후 즉시 이벤트 발송
                    setTimeout(function() {
                        triggerMapChangedEvent();
                    }, 100);
                });

            }

            function setupExternalCommunication() {
                // 외부에서 bounds 정보 요청시 응답
                $(document).on('wv_location_map_request_bounds', function(event, data) {

                        if (map) {
                            var bounds = map.getBounds();
                            var center = map.getCenter();
                            var level = map.getLevel();


                            $(document).trigger('wv_location_map_bounds_received', {
                                bounds: bounds,
                                center: center,
                                level: level
                            });
                        }

                });

                // 외부에서 특정 위치로 이동 요청시 처리
                $(document).on('wv_location_map_move_to', function(event, data) {

                    var movePosition = new kakao.maps.LatLng(data.lat, data.lng);
                    map.setCenter(movePosition);
                    if (data.level) {
                        map.setLevel(data.level);
                    }
                    // 외부 이동 후 즉시 이벤트 발송
                    setTimeout(function() {
                        triggerMapChangedEvent();
                    }, 100);

                });

                // 외부에서 마커 데이터 업데이트 이벤트 수신
                $(document).on('wv_location_map_markers_update', function(event, data) {

                        clearMarkers();
                        addMarkers(data.markers);

                });
            }

            function clearMarkers() {
                if (clusterer) {
                    clusterer.clear();
                }
                for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }
                markers = [];
            }

            function addMarkers(markersData) {
                for (var i = 0; i < markersData.length; i++) {
                    var item = markersData[i];
                    if (!item.lat || !item.lng) continue;

                    var position = new kakao.maps.LatLng(
                        parseFloat(item.lat),
                        parseFloat(item.lng)
                    );

                    var marker = new kakao.maps.Marker({
                        position: position,
                        title: item.title || item.name || '위치'
                    });

                    // 마커 클릭 이벤트
                    (function(markerItem) {
                        kakao.maps.event.addListener(marker, 'click', function() {
                            $(document).trigger('wv_location_map_marker_clicked', {
                                item: markerItem,
                                position: position
                            });
                        });
                    })(item);

                    markers.push(marker);
                }

                // 클러스터러에 마커 추가
                if (clusterer) {
                    clusterer.addMarkers(markers);
                } else {
                    for (var j = 0; j < markers.length; j++) {
                        markers[j].setMap(map);
                    }
                }
            }

            // 초기화 실행
            initMap();

        });
    </script>
</div>