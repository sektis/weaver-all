<?php
/**
 * Location 플러그인 - Map 스킨
 * 파일: plugins/location/theme/basic/pc/map/skin.php
 *
 * 카카오맵 + 클러스터링 기능
 * 현재위치부터 시작, wv_location_map_changeed 이벤트 발송
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 전달받은 옵션 처리
$map_options = isset($data) && is_array($data) ? $data : array();
$height_selector = isset($map_options['height_wrapper']) ? $map_options['height_wrapper'] : '#content-wrapper';
$enable_clustering = isset($map_options['clustering']) ? $map_options['clustering'] : true;
$map_id = isset($map_options['map_id']) ? $map_options['map_id'] : 'location-map-' . uniqid();
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-location-map-skin position-relative" style="<?php echo isset($data['margin_top'])?"margin-top:{$data['margin_top']};":""; ?>">
    <style>
        <?php echo $skin_selector?> { width: 100%; position: relative; border-radius: var(--wv-8); overflow: hidden; border: 1px solid #EFEFEF; }
        <?php echo $skin_selector?> .kakao-map { width: 100%; height: 100%; }
        <?php echo $skin_selector?> .current-location-btn { position: absolute; bottom: 20px; right: 20px; z-index: 1000; width: 44px; height: 44px; background: rgba(255, 255, 255, 0.95); border: 1px solid #ddd; border-radius: var(--wv-6); cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); backdrop-filter: blur(4px); transition: all 0.2s; }
        <?php echo $skin_selector?> .current-location-btn:hover { background: #f8f9fa; transform: scale(1.05); }
        <?php echo $skin_selector?> .current-location-btn i { font-size: 18px; color: #007bff; }
        <?php echo $skin_selector?> .loading-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 2000; }
        <?php echo $skin_selector?> .loading-spinner { width: 32px; height: 32px; border: 3px solid #f3f3f3; border-top: 3px solid #007bff; border-radius: 50%; animation: wv-map-spin 1s linear infinite; }
        @keyframes wv-map-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @media (max-width: 991.98px) {
        <?php echo $skin_selector?> .current-location-btn { bottom: 15px; right: 15px; width: 40px; height: 40px; }
        <?php echo $skin_selector?> .current-location-btn i { font-size: 16px; }
        }
    </style>

    <!-- 로딩 오버레이 -->
    <div class="loading-overlay" id="loading-overlay-<?php echo $map_id; ?>">
        <div class="loading-spinner"></div>
    </div>

    <!-- 내 위치 버튼 -->
    <button type="button" class="current-location-btn" id="btn-current-location-<?php echo $map_id; ?>" title="현재 위치로 이동">
        <i class="fa-solid fa-location-crosshairs"></i>
    </button>

    <!-- 카카오맵 컨테이너 -->
    <div id="<?php echo $map_id; ?>" class="kakao-map"></div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var mapId = '<?php echo $map_id; ?>';
            var heightSelector = '<?php echo $height_selector; ?>';
            var map, clusterer;
            var isClusterEnabled = <?php echo $enable_clustering ? 'true' : 'false'; ?>;
            var markers = [];

            /**
             * 높이 업데이트 함수
             */
            function updateMapHeight() {
                var $target = $(heightSelector);
                if ($target.length > 0) {
                    var targetHeight = $target.height();
                    if (targetHeight > 0) {
                        $skin.height(targetHeight);
                        if (map) {
                            map.relayout();
                        }
                    }
                }
            }

            /**
             * 지도 변경 이벤트 발생 (통합)
             */
            function triggerMapEvent() {
                if (!map) return;

                var bounds = map.getBounds();

                var eventData = {
                    mapId: mapId,
                    timestamp: new Date().getTime(),
                    bounds: {
                        sw_lat: bounds.getSouthWest().getLat(),
                        sw_lng: bounds.getSouthWest().getLng(),
                        ne_lat: bounds.getNorthEast().getLat(),
                        ne_lng: bounds.getNorthEast().getLng()
                    }
                };

                // jQuery 이벤트
                $(document).trigger('wv_location_map_changeed', [eventData]);
            }

            /**
             * 카카오맵 초기화
             */
            function initKakaoMap() {
                if (!window.kakao || !window.kakao.maps) {
                    setTimeout(initKakaoMap, 100);
                    return;
                }

                showLoading(true);

                try {
                    // 현재 위치 가져오기
                    if (typeof wv_get_current_location === 'function') {
                        wv_get_current_location(function(result) {
                            var lat = result && result.lat ? result.lat : 37.5665;
                            var lng = result && result.lng ? result.lng : 126.9780;
                            createMap(lat, lng);
                        });
                    } else {
                        // location 플러그인 함수가 없으면 기본 위치 사용
                        createMap(37.5665, 126.9780);
                    }
                } catch (error) {
                    createMap(37.5665, 126.9780);
                }
            }

            /**
             * 지도 생성
             */
            function createMap(lat, lng) {
                try {
                    updateMapHeight();

                    var container = document.getElementById(mapId);
                    var options = {
                        center: new kakao.maps.LatLng(lat, lng),
                        level: 8
                    };

                    map = new kakao.maps.Map(container, options);

                    // 클러스터링 설정
                    if (isClusterEnabled && typeof kakao.maps.MarkerClusterer !== 'undefined') {
                        clusterer = new kakao.maps.MarkerClusterer({
                            map: map,
                            averageCenter: true,
                            minLevel: 6
                        });
                    }

                    // 지도 이벤트 등록
                    var moveTimeout, zoomTimeout;

                    // 지도 이동 이벤트 (디바운싱)
                    kakao.maps.event.addListener(map, 'center_changed', function() {
                        clearTimeout(moveTimeout);
                        moveTimeout = setTimeout(function() {
                            triggerMapEvent();
                        }, 300);
                    });

                    // 줌 변경 이벤트 (디바운싱)
                    kakao.maps.event.addListener(map, 'zoom_changed', function() {
                        clearTimeout(zoomTimeout);
                        zoomTimeout = setTimeout(function() {
                            triggerMapEvent();
                        }, 300);
                    });

                    showLoading(false);

                    // 지도 로딩 완료 후 초기 이벤트 발생
                    triggerMapEvent();

                } catch (error) {
                    showLoading(false);
                    alert('지도 로딩 중 오류가 발생했습니다: ' + error.message);
                }
            }

            /**
             * 현재 위치로 이동 (수동)
             */
            function moveToCurrentLocationManual() {
                if (typeof wv_get_current_location === 'function') {
                    wv_get_current_location(function(result) {
                        if (result && result.lat && result.lng) {
                            var moveLatLng = new kakao.maps.LatLng(result.lat, result.lng);
                            map.setCenter(moveLatLng);
                            map.setLevel(5);
                            triggerMapEvent();
                        } else {
                            alert('현재 위치를 가져올 수 없습니다.');
                        }
                    });
                } else {
                    alert('위치 서비스를 사용할 수 없습니다.');
                }
            }

            /**
             * 로딩 표시
             */
            function showLoading(show) {
                var $loading = $('#loading-overlay-' + mapId);
                if (show) {
                    $loading.show();
                } else {
                    $loading.hide();
                }
            }

            /**
             * 외부에서 호출 가능한 메서드들
             */
            window['wv_location_map_' + mapId] = {
                getMap: function() { return map; },
                getClusterer: function() { return clusterer; },
                addMarkers: function(markerArray) {
                    markers = markers.concat(markerArray);
                    if (isClusterEnabled && clusterer) {
                        clusterer.addMarkers(markerArray);
                    } else {
                        markerArray.forEach(function(marker) {
                            marker.setMap(map);
                        });
                    }
                },
                clearMarkers: function() {
                    if (clusterer) clusterer.clear();
                    markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    markers = [];
                },
                moveToLocation: function(lat, lng, level) {
                    if (map) {
                        map.setCenter(new kakao.maps.LatLng(lat, lng));
                        if (level) map.setLevel(level);
                    }
                },
                getBounds: function() {
                    if (!map) return null;
                    var bounds = map.getBounds();
                    return {
                        sw_lat: bounds.getSouthWest().getLat(),
                        sw_lng: bounds.getSouthWest().getLng(),
                        ne_lat: bounds.getNorthEast().getLat(),
                        ne_lng: bounds.getNorthEast().getLng()
                    };
                },
                refresh: function() {
                    if (map) {
                        updateMapHeight();
                        map.relayout();
                    }
                },
                updateHeight: updateMapHeight
            };

            // 현재 위치 버튼 이벤트
            $('#btn-current-location-' + mapId).on('click', moveToCurrentLocationManual);

            // 윈도우 리사이즈 이벤트
            $(window).on('resize', function() {
                updateMapHeight();
            });

            // 카카오맵 초기화 실행
            if (window.kakao && window.kakao.maps) {
                initKakaoMap();
            } else {
                // 카카오맵 API 로드 대기
                var checkKakao = setInterval(function() {
                    if (window.kakao && window.kakao.maps) {
                        clearInterval(checkKakao);
                        initKakaoMap();
                    }
                }, 100);
            }
        });
    </script>
</div>