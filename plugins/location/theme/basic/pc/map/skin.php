<?php
/**
 * Location 플러그인 - Map 스킨
 * 파일: plugins/location/theme/basic/pc/map/skin.php
 *
 * 카카오맵 + 클러스터링 기능
 * 현재위치부터 시작, wv_location_map_changeed 이벤트 발송
 * category_icon 커스텀 마커 표시
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 전달받은 옵션 처리
$map_options = isset($data) && is_array($data) ? $data : array();
$height_selector = isset($map_options['height_wrapper']) ? $map_options['height_wrapper'] : '#content-wrapper';
$enable_clustering = isset($map_options['clustering']) ? $map_options['clustering'] : true;
$map_id = isset($map_options['map_id']) ? $map_options['map_id'] : 'location-map-' . uniqid();
$initial_level = isset($map_options['initial_level']) ? intval($map_options['initial_level']) : 8;
$min_level = isset($map_options['min_level']) ? intval($map_options['min_level']) : 1;
$max_level = isset($map_options['max_level']) ? intval($map_options['max_level']) : 14;
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

        /* 매장 정보 패널 스타일 */
        <?php echo $skin_selector?> .store-info-panel {
                                        transition: all 0.3s ease;
                                        border-radius: 8px 8px 0 0;
                                        box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
                                        display: none;
                                    }
        <?php echo $skin_selector?> .store-info-panel.active {
                                        display: block;
                                    }

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

    <!-- 카카오맵 API 및 클러스터링 라이브러리 로드 -->
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $config['cf_kakao_js_apikey']?>&libraries=clusterer,services"></script>

    <!-- 카카오맵 컨테이너 -->
    <div id="<?php echo $map_id; ?>" class="kakao-map"></div>

    <!-- 매장 정보 패널 -->
    <div id="store-info-panel-<?php echo $map_id; ?>" class="store-info-panel" style="position:absolute; left:50%;bottom:var(--wv-30);background:white;border-top:1px solid #ddd;padding:15px;z-index:9999;max-height:200px;overflow-y:auto;">
        <div class="store-info-content"></div>
        <button type="button" class="store-info-panel-close" style="position:absolute;top:10px;right:15px;border:none;background:none;font-size:18px;cursor:pointer;"  >×</button>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var mapId = '<?php echo $map_id; ?>';
            var heightSelector = '<?php echo $height_selector; ?>';
            var initialLevel = <?php echo $initial_level; ?>;
            var minLevel = <?php echo $min_level; ?>;
            var maxLevel = <?php echo $max_level; ?>;
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
                    if (typeof wv_get_current_location === 'function') {
                        wv_get_current_location(function(result) {
                            var lat = result && result.lat ? result.lat : 37.5665;
                            var lng = result && result.lng ? result.lng : 126.9780;
                            createMap(lat, lng, initialLevel);
                        });
                    } else {
                        createMap(37.5665, 126.9780, initialLevel);
                    }
                } catch (error) {
                    createMap(37.5665, 126.9780, initialLevel);
                }
            }

            /**
             * 지도 생성
             */
            function createMap(lat, lng, level) {
                level = level || initialLevel;

                try {
                    updateMapHeight();

                    var container = document.getElementById(mapId);
                    var options = {
                        center: new kakao.maps.LatLng(lat, lng),
                        level: level,
                        minLevel: minLevel,
                        maxLevel: maxLevel
                    };

                    map = new kakao.maps.Map(container, options);

                    // 클러스터링 설정
                    if (isClusterEnabled) {
                        if (typeof kakao.maps.MarkerClusterer !== 'undefined') {
                            clusterer = new kakao.maps.MarkerClusterer({
                                map: map,
                                averageCenter: true,
                                minLevel: Math.max(minLevel + 2, 6)
                            });
                        } else {
                            console.warn('MarkerClusterer가 로드되지 않았습니다.');
                            isClusterEnabled = false;
                        }
                    }

                    // 지도 이벤트 등록
                    var moveTimeout, zoomTimeout;

                    kakao.maps.event.addListener(map, 'center_changed', function() {
                        clearTimeout(moveTimeout);
                        moveTimeout = setTimeout(function() {
                            triggerMapEvent();
                        }, 300);
                    });

                    kakao.maps.event.addListener(map, 'zoom_changed', function() {
                        clearTimeout(zoomTimeout);
                        zoomTimeout = setTimeout(function() {
                            triggerMapEvent();
                        }, 300);
                    });

                    showLoading(false);
                    triggerMapEvent();

                } catch (error) {
                    showLoading(false);
                    alert('지도 로딩 중 오류가 발생했습니다: ' + error.message);
                }
            }

            /**
             * 현재 위치로 이동
             */
            function moveToCurrentLocationManual() {
                if (typeof wv_get_current_location === 'function') {
                    wv_get_current_location(function(result) {
                        if (result && result.lat && result.lng) {
                            var moveLatLng = new kakao.maps.LatLng(result.lat, result.lng);
                            map.setCenter(moveLatLng);
                            map.setLevel(Math.max(minLevel + 1, 5));
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
             * 매장 데이터 업데이트 이벤트 리스닝
             */
            $(document).on('wv_location_place_updated', function(event, data) {
                // console.log('매장 데이터 업데이트:', data);

                if (data.lists && Array.isArray(data.lists)) {
                    updateStoreMarkers(data.lists, data);
                }
            });

            /**
             * 커스텀 마커 이미지 생성
             */
            function createCustomMarkerImage(categoryIcon, isSelected) {
                var size = new kakao.maps.Size(36, 36);
                var option = { offset: new kakao.maps.Point(18, 36) };

                if (categoryIcon) {
                    // category_icon이 있으면 해당 이미지 사용
                    return new kakao.maps.MarkerImage(categoryIcon, size, option);
                } else {
                    // category_icon이 없으면 기본 마커 사용
                    var defaultIcon = isSelected ?
                        'data:image/svg+xml;base64,' + btoa(getDefaultSelectedMarkerSvg()) :
                        'data:image/svg+xml;base64,' + btoa(getDefaultMarkerSvg());
                    return new kakao.maps.MarkerImage(defaultIcon, size, option);
                }
            }

            /**
             * 기본 마커 SVG
             */
            function getDefaultMarkerSvg() {
                return '<svg width="36" height="36" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">' +
                    '<circle cx="18" cy="18" r="16" fill="#ff6b6b" stroke="#fff" stroke-width="2"/>' +
                    '<circle cx="18" cy="18" r="8" fill="#fff"/>' +
                    '</svg>';
            }

            /**
             * 선택된 마커 SVG
             */
            function getDefaultSelectedMarkerSvg() {
                return '<svg width="36" height="36" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">' +
                    '<circle cx="18" cy="18" r="16" fill="#007bff" stroke="#fff" stroke-width="2"/>' +
                    '<circle cx="18" cy="18" r="8" fill="#fff"/>' +
                    '</svg>';
            }

            /**
             * 매장 마커 업데이트
             */
            function updateStoreMarkers(lists, response) {
                if (!map) return;


                // 기존 마커 제거
                clearAllMarkers();

                lists.forEach(function(place) {

                    if (!place.location.lat || !place.location.lng) return;

                    var position = new kakao.maps.LatLng(place.location.lat, place.location.lng);

                    // 커스텀 마커 이미지 생성
                    var markerImage = createCustomMarkerImage(place.store.category_icon, false);

                    // 마커 생성
                    var marker = new kakao.maps.Marker({
                        position: position,
                        title: place.name,
                        image: markerImage
                    });

                    // 마커에 place 정보와 원본 이미지 저장
                    marker.storeData = place;
                    marker.originalImage = markerImage;
                    marker.selectedImage = createCustomMarkerImage(place.store.category_icon, true);

                    // 마커 클릭 이벤트
                    kakao.maps.event.addListener(marker, 'click', function() {
                        // 기존 선택 해제
                        clearSelectedMarkers();

                        // 현재 마커 선택 표시
                        marker.isSelected = true;
                        marker.setImage(marker.selectedImage);

                        // 매장 정보 패널 표시
                        showStoreInfoPanel(place, response);
                    });

                    // 클러스터링 적용 또는 개별 마커 표시
                    if (isClusterEnabled && clusterer) {
                        clusterer.addMarker(marker);
                    } else {
                        marker.setMap(map);
                    }

                    markers.push(marker);
                });

                // console.log('마커 업데이트 완료:', markers.length + '개');
            }

            /**
             * 선택된 마커 해제
             */
            function clearSelectedMarkers() {
                markers.forEach(function(marker) {
                    if (marker.isSelected) {
                        marker.isSelected = false;
                        marker.setImage(marker.originalImage);
                    }
                });
            }

            /**
             * 모든 마커 제거
             */
            function clearAllMarkers() {
                if (clusterer) {
                    clusterer.clear();
                }

                markers.forEach(function(marker) {
                    marker.setMap(null);
                });

                markers = [];
            }

            /**
             * 매장 정보 패널 표시
             */
            var $bot_info = $('.store-info-panel',$skin);
            function showStoreInfoPanel(place) {

                if (place.store.list_each) {

                    console.log($bot_info)
                    $(".store-info-content",$bot_info).html(place.store.list_each)
                    $bot_info.addClass('active');
                }
            }
            function closeStoreInfoPanel() {

                $bot_info.removeClass('active')
            }
            $(".store-info-panel-close",$skin).click(function () {
                closeStoreInfoPanel()
            })

            /**
             * 외부에서 호출 가능한 메서드들
             */
            window['wv_location_map_' + mapId] = {
                getMap: function() { return map; },
                getClusterer: function() { return clusterer; },
                clearMarkers: function() { clearAllMarkers(); },
                moveToLocation: function(lat, lng, level) {
                    if (map) {
                        map.setCenter(new kakao.maps.LatLng(lat, lng));
                        if (level) map.setLevel(level);
                    }
                },
                refresh: function() {
                    if (map) {
                        updateMapHeight();
                        map.relayout();
                    }
                },
                // 특정 매장으로 이동하는 메서드 추가
                moveToStore: function(storeId) {
                    var targetMarker = markers.find(function(marker) {
                        return marker.storeData && marker.storeData.wr_id == storeId;
                    });

                    if (targetMarker) {
                        map.setCenter(targetMarker.getPosition());
                        map.setLevel(Math.max(minLevel + 1, 5));

                        // 마커 선택 효과
                        clearSelectedMarkers();
                        targetMarker.isSelected = true;
                        targetMarker.setImage(targetMarker.selectedImage);

                        // 매장 정보 패널 표시
                        // showStoreInfoPanel 호출을 위해 response 데이터가 필요하므로
                        // 이 부분은 필요에 따라 구현
                    }
                }
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