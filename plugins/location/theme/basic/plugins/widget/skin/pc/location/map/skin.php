<?php
/**
 * Location 플러그인 - Map 스킨
 * 파일: plugins/location/theme/basic/pc/map/skin.php
 *
 * 카카오맵 + 클러스터링 기능
 * 현재위치부터 시작, wv_location_map_changeed 이벤트 발송
 * category_icon 커스텀 마커 표시 + category_icon_wrap 배경 적용
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
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-location-map-skin position-relative h-100" style="<?php echo isset($data['margin_top'])?"margin-top:{$data['margin_top']};":""; ?>">
    <style>
        <?php echo $skin_selector?> { width: 100%; position: relative;    }
        <?php echo $skin_selector?> .kakao-map { width: 100%; height: 100%; }
        <?php echo $skin_selector?> .current-location-btn { position: absolute; bottom: 20px; right: 20px; z-index: 1000; width: 44px; height: 44px; background: rgba(255, 255, 255, 0.95); border: 1px solid #ddd; border-radius: var(--wv-6); cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); backdrop-filter: blur(4px); transition: all 0.2s; }
        <?php echo $skin_selector?> .current-location-btn:hover { background: #f8f9fa; transform: scale(1.05); }
        <?php echo $skin_selector?> .current-location-btn i { font-size: 18px; color: #007bff; }
        <?php echo $skin_selector?> .loading-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 2000; }
        <?php echo $skin_selector?> .loading-spinner { width: 32px; height: 32px; border: 3px solid #f3f3f3; border-top: 3px solid #007bff; border-radius: 50%; animation: wv-map-spin 1s linear infinite; }

        /* 매장 정보 패널 스타일 */
        <?php echo $skin_selector?> .store-info-panel {display: none;position:absolute; left:50%;bottom:var(--wv-17);z-index:1001;transform: translateX(-50%);}

        <?php echo $skin_selector?> .store-info-panel.active {display: block;}
        <?php echo $skin_selector?> .store-list-btn {transition: all 0.3s ease;width: var(--wv-119);height: var(--wv-33);border-radius: var(--wv-43);gap: var(--wv-4);display: inline-flex;
                                        padding: var(--wv-8) var(--wv-18);
                                        justify-content: center;
                                        align-items: center;
                                        font-size: var(--wv-12);font-weight: 500;background-color: #0d171b;color:#fff;
                                        position:absolute; left:50%;bottom:var(--wv-17); ;border-top:1px solid #ddd; ;z-index:1000;transform: translateX(-50%);
                                    }

        @keyframes wv-map-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        /* 매장 목록 패널 스타일 */
        <?php echo $skin_selector?> .store-list {
                                        position: absolute;
                                        top: 0;
                                        left: 0;
                                        width: 100%;
                                        height: 100%;
                                        background-color: rgba(255, 255, 255, 0.95);
                                        z-index: 2001;
                                        overflow-y: auto;
                                        backdrop-filter: blur(4px);
                                    }

        <?php echo $skin_selector?> .store-list-header {
                                        position: sticky;
                                        top: 0;
                                        background-color: white;
                                        padding: var(--wv-20);
                                        border-bottom: 1px solid #ddd;
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    }

        <?php echo $skin_selector?> .store-list-header h3 {
                                        margin: 0;
                                        font-size: var(--wv-18);
                                        font-weight: 600;
                                        color: #333;
                                    }

        <?php echo $skin_selector?> .store-list-close {
                                        background: none;
                                        border: none;
                                        font-size: var(--wv-24);
                                        cursor: pointer;
                                        color: #666;
                                        width: var(--wv-40);
                                        height: var(--wv-40);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        border-radius: 50%;
                                        transition: all 0.2s;
                                    }

        <?php echo $skin_selector?> .store-list-close:hover {
                                        background-color: #f0f0f0;
                                        color: #333;
                                    }

        <?php echo $skin_selector?> .store-list-content {
                                        padding: var(--wv-20);
                                    }
         <?php echo $skin_selector?> .store-list {
             position: absolute;
             top: 0;
             left: 0;
             width: 100%;
             height: 100%;
             background-color: rgba(255, 255, 255, 0.95);
             z-index: 2001;
             overflow-y: auto;
             backdrop-filter: blur(4px);
         }

        <?php echo $skin_selector?> .store-list-header {
                                        position: sticky;
                                        top: 0;
                                        background-color: white;
                                        padding: var(--wv-20);
                                        border-bottom: 1px solid #ddd;
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    }

        <?php echo $skin_selector?> .store-list-header h3 {
                                        margin: 0;
                                        font-size: var(--wv-18);
                                        font-weight: 600;
                                        color: #333;
                                    }

        <?php echo $skin_selector?> .store-list-close {
                                        background: none;
                                        border: none;
                                        font-size: var(--wv-24);
                                        cursor: pointer;
                                        color: #666;
                                        width: var(--wv-40);
                                        height: var(--wv-40);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        border-radius: 50%;
                                        transition: all 0.2s;
                                    }

        <?php echo $skin_selector?> .store-list-close:hover {
                                        background-color: #f0f0f0;
                                        color: #333;
                                    }

        <?php echo $skin_selector?> .store-list-content {
                                        padding: var(--wv-20);
                                    }
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
    <div class="store-info-panel">
        <div class="store-info-content"></div>
        <button type="button" class="store-info-panel-close" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>

    <div class="store-list" style="display: none;">
        <div class="store-list-header">
            <h3>매장 목록</h3>
            <button type="button" class="store-list-close">&times;</button>
        </div>
        <div class="store-list-content">
            <!-- AJAX로 로드될 내용 -->
        </div>
    </div>

    <a href="" class="store-list-btn"><i class="fa-solid fa-bars"></i><span>목록으로 보기</span></a>

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
            var mapId = '<?php echo $map_id; ?>';
            var heightSelector = '<?php echo $height_selector; ?>';
            var initialLevel = <?php echo $initial_level; ?>;
            var minLevel = <?php echo $min_level; ?>;
            var maxLevel = <?php echo $max_level; ?>;
            var map, clusterer;
            var isClusterEnabled = <?php echo $enable_clustering ? 'true' : 'false'; ?>;
            var markers = [];
            var selectedMarkerId = null;

            // 카테고리 아이콘 래핑 이미지 경로 (전역 변수)
            var categoryIconWrap = '';
            var categoryIconWrapOn = '';

            var bounds = new kakao.maps.LatLngBounds();

            // 카카오맵 라이브러리 상태 확인
            var clustererAvailable = checkKakaoLibraries();
            if (!clustererAvailable && isClusterEnabled) {
                isClusterEnabled = false;
            }

            // 지도 높이 설정
            function setMapHeight() {
                var $heightWrapper = $(heightSelector);
                if ($heightWrapper.length) {
                    var wrapperHeight = $heightWrapper.outerHeight();
                    var currentHeight = wrapperHeight > 0 ? wrapperHeight : 400;
                    // $skin.css('height', currentHeight + 'px');
                }
            }

            // 지도 초기화
            function initMap() {
                setMapHeight();

                var mapContainer = $skin.find('.kakao-map')[0];
                var mapOption = {
                    center: new kakao.maps.LatLng(37.5665, 126.9780), // 서울 중심점
                    level: initialLevel,
                    minLevel: minLevel,
                    maxLevel: maxLevel
                };

                map = new kakao.maps.Map(mapContainer, mapOption);

                // 클러스터러 초기화 (안전한 방식)
                initClusterer();

                // 지도 이벤트 리스너 등록
                setupMapEvents();

                // 현재 위치 버튼 이벤트
                setupCurrentLocationButton();

                // 매장 목록 버튼 이벤트 설정 (새로 추가)
                setupStoreListButton();

                // 초기 위치 설정
                getCurrentLocationAndSetMap();
            }

            // 클러스터러 안전 초기화
            function initClusterer() {
                if (!isClusterEnabled) {
                    return;
                }

                try {
                    // 카카오맵 클러스터러 로드 확인
                    if (typeof kakao !== 'undefined' &&
                        kakao.maps &&
                        typeof kakao.maps.MarkerClusterer === 'function') {

                        clusterer = new kakao.maps.MarkerClusterer({
                            map: map,
                            averageCenter: true,
                            minLevel: Math.max(minLevel + 2, 6),
                            disableClickZoom: true,  // 기본 클릭 줌 비활성화
                            calculator: [10, 30, 50],
                        });
                        kakao.maps.event.addListener(clusterer, 'clusterclick', function(cluster) {
                            var clusterMarkers = cluster.getMarkers();
                            if (clusterMarkers.length > 0) {
                                var bounds = new kakao.maps.LatLngBounds();

                                // 클러스터 내 모든 마커의 위치를 포함하는 영역 계산
                                for (var i = 0; i < clusterMarkers.length; i++) {
                                    bounds.extend(clusterMarkers[i].getPosition());
                                }

                                // 영역 중심점 계산
                                var centerLat = (bounds.getNorthEast().getLat() + bounds.getSouthWest().getLat()) / 2;
                                var centerLng = (bounds.getNorthEast().getLng() + bounds.getSouthWest().getLng()) / 2;
                                var clusterCenter = new kakao.maps.LatLng(centerLat, centerLng);

                                // 현재 줌 레벨보다 1-2 단계 확대
                                var currentLevel = map.getLevel();
                                var newLevel = Math.max(currentLevel - 2, minLevel + 1);

                                // 부드러운 이동과 줌 조정
                                map.panTo(clusterCenter);
                                setTimeout(function() {
                                    map.setLevel(newLevel);
                                }, 300);  // 이동 후 줌 조정
                            }
                        });
                    } else {
                        isClusterEnabled = false;
                        clusterer = null;
                    }
                } catch (error) {
                    isClusterEnabled = false;
                    clusterer = null;
                }
            }

            // 지도 이벤트 설정
            function setupMapEvents() {
                var mapChangeTimeout;

                // 지도 변경 이벤트를 지연 처리하는 함수
                function scheduleMapEvent() {
                    clearTimeout(mapChangeTimeout);
                    closeStoreInfoPanel();
                    clearSelectedMarkers();  // 마커 선택 해제 추가
                    mapChangeTimeout = setTimeout(function() {
                        triggerMapEvent();
                    }, 300);
                }

                // 지도 중심 변경과 줌 변경 이벤트 모두 동일한 함수 사용
                kakao.maps.event.addListener(map, 'center_changed', scheduleMapEvent);
                kakao.maps.event.addListener(map, 'zoom_changed', scheduleMapEvent);

                // 기존 idle 이벤트는 제거 (중복 방지)
                // kakao.maps.event.addListener(map, 'idle', function() {
                //     triggerMapChangeEvent();
                // });
            }

            // 지도 변경 이벤트 발생
            function triggerMapEvent() {
                var bounds = map.getBounds();
                var center = map.getCenter();
                var level = map.getLevel();

                var eventData = {
                    bounds: {
                        sw_lat: bounds.getSouthWest().getLat(),
                        sw_lng: bounds.getSouthWest().getLng(),
                        ne_lat: bounds.getNorthEast().getLat(),
                        ne_lng: bounds.getNorthEast().getLng()
                    },
                    center: {
                        lat: center.getLat(),
                        lng: center.getLng()
                    },
                    level: level
                };

                $(document).trigger('wv_location_map_changeed', [eventData]);
            }

            // 현재 위치 버튼 설정
            function setupCurrentLocationButton() {
                $('#btn-current-location-' + mapId).on('click', function() {
                    getCurrentLocationAndSetMap();
                });
            }

            // 현재 위치 가져오기 및 지도 설정
            function getCurrentLocationAndSetMap() {
                showLoading(true);

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        var moveLatLng = new kakao.maps.LatLng(lat, lng);

                        map.panTo(moveLatLng);
                        showLoading(false);
                    }, function(error) {
                        showLoading(false);
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                console.log("사용자가 위치 정보 제공을 거부했습니다.");
                                break;
                            case error.POSITION_UNAVAILABLE:
                                console.log("위치 정보를 사용할 수 없습니다.");
                                break;
                            case error.TIMEOUT:
                                console.log("위치 정보 요청 시간이 초과되었습니다.");
                                break;
                        }
                    });
                } else {
                    alert('위치 서비스를 사용할 수 없습니다.');
                }
            }

            // 로딩 표시
            function showLoading(show) {
                var $loading = $('#loading-overlay-' + mapId);
                if (show) {
                    $loading.show();
                } else {
                    $loading.hide();
                }
            }

            // 매장 데이터 업데이트 이벤트 리스닝
            $(document).on('wv_location_place_updated', function(event, data) {
                console.log('매장 데이터 업데이트:', data);

                // 카테고리 아이콘 래핑 이미지 경로 저장
                if (data.category_icon_wrap) {
                    categoryIconWrap = data.category_icon_wrap;
                }
                if (data.category_icon_wrap_on) {
                    categoryIconWrapOn = data.category_icon_wrap_on;
                }

                if (data.lists && Array.isArray(data.lists)) {
                    updateStoreMarkers(data.lists, data);
                }
            });

            /**
             * 이미지 합성 함수 - 배경 위에 카테고리 아이콘을 올려서 새로운 마커 이미지 생성
             */
            function createCompositeMarkerImage(backgroundImageUrl, categoryIconUrl, isSelected, callback) {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                var loadedImages = 0;
                var totalImages = 2;

                // 캔버스 크기 설정 (마커 크기)
                canvas.width = 36;
                canvas.height = 36;

                var backgroundImg = new Image();
                var categoryImg = new Image();

                function onImageLoad() {
                    loadedImages++;
                    if (loadedImages === totalImages) {
                        // 모든 이미지가 로드되면 합성
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        // 배경 이미지 그리기 (전체 크기)
                        ctx.drawImage(backgroundImg, 0, 0, canvas.width, canvas.height);

                        // 카테고리 아이콘 그리기 (중앙 위치, 조금 작게)
                        var iconSize = 20; // 카테고리 아이콘 크기
                        var iconX = (canvas.width - iconSize) / 2;
                        var iconY = (canvas.height - iconSize) / 2;
                        ctx.drawImage(categoryImg, iconX, iconY, iconSize, iconSize);

                        // Canvas를 데이터 URL로 변환
                        var dataUrl = canvas.toDataURL('image/png');

                        // 마커 이미지 생성
                        var size = new kakao.maps.Size(36, 36);
                        var option = { offset: new kakao.maps.Point(18, 36) };
                        var markerImage = new kakao.maps.MarkerImage(dataUrl, size, option);

                        callback(markerImage);
                    }
                }

                backgroundImg.onload = onImageLoad;
                categoryImg.onload = onImageLoad;

                backgroundImg.onerror = function() {
                    // 배경 이미지 로드 실패시 기본 마커 사용
                    callback(createDefaultMarkerImage(isSelected));
                };

                categoryImg.onerror = function() {
                    // 카테고리 이미지 로드 실패시 기본 마커 사용
                    callback(createDefaultMarkerImage(isSelected));
                };

                backgroundImg.src = backgroundImageUrl;
                categoryImg.src = categoryIconUrl;
            }

            /**
             * 커스텀 마커 이미지 생성 (수정된 버전)
             */
            function createCustomMarkerImage(categoryIcon, isSelected, callback) {
                // 카테고리 아이콘과 래핑 이미지가 모두 있는 경우 합성 마커 생성
                if (categoryIcon && categoryIconWrap && categoryIconWrapOn) {
                    var backgroundImageUrl = isSelected ? categoryIconWrapOn : categoryIconWrap;
                    createCompositeMarkerImage(backgroundImageUrl, categoryIcon, isSelected, callback);
                } else {
                    // 기본 마커 사용
                    callback(createDefaultMarkerImage(isSelected));
                }
            }

            /**
             * 기본 마커 이미지 생성
             */
            function createDefaultMarkerImage(isSelected) {
                var size = new kakao.maps.Size(36, 36);
                var option = { offset: new kakao.maps.Point(18, 36) };
                var defaultIcon = isSelected ?
                    'data:image/svg+xml;base64,' + btoa(getDefaultSelectedMarkerSvg()) :
                    'data:image/svg+xml;base64,' + btoa(getDefaultMarkerSvg());
                return new kakao.maps.MarkerImage(defaultIcon, size, option);
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
             * 매장 마커 업데이트 (수정된 버전)
             */
            function updateStoreMarkers(lists, response) {
                if (!map) return;

                // 새로운 매장 목록에서 wr_id 추출
                var newStoreIds = lists.map(function(place) {
                    return place.wr_id;
                });

                // 기존 마커들에서 wr_id 추출
                var existingStoreIds = markers.map(function(marker) {
                    return marker.storeData ? marker.storeData.wr_id : null;
                }).filter(function(id) { return id !== null; });

                // 1. 제거할 마커들 (기존에 있지만 새 목록에 없는 것들)
                var markersToRemove = markers.filter(function(marker) {
                    return marker.storeData && newStoreIds.indexOf(marker.storeData.wr_id) === -1;
                });

                // 2. 추가할 매장들 (새 목록에 있지만 기존에 없는 것들)
                var storesToAdd = lists.filter(function(place) {
                    return existingStoreIds.indexOf(place.wr_id) === -1;
                });

                // 3. 제거할 마커들 처리
                markersToRemove.forEach(function(marker) {
                    if (isClusterEnabled && clusterer) {
                        try {
                            clusterer.removeMarker(marker);
                        } catch (error) {
                            marker.setMap(null);
                        }
                    } else {
                        marker.setMap(null);
                    }

                    // markers 배열에서 제거
                    var index = markers.indexOf(marker);
                    if (index > -1) {
                        markers.splice(index, 1);
                    }
                });

                // 4. 새로운 마커들 추가
                var processedMarkers = 0;
                var totalNewMarkers = storesToAdd.length;

                if (totalNewMarkers === 0) {
                    return; // 추가할 마커가 없으면 종료
                }

                storesToAdd.forEach(function(place, index) {
                    if (!place.location.lat || !place.location.lng) {
                        processedMarkers++;
                        return;
                    }

                    var position = new kakao.maps.LatLng(place.location.lat, place.location.lng);
                    var categoryIcon = place.store.category_item && place.store.category_item.icon ? place.store.category_item.icon.path : null;

                    // 커스텀 마커 이미지 생성 (비동기)
                    createCustomMarkerImage(categoryIcon, false, function(markerImage) {
                        // 마커 생성
                        var marker = new kakao.maps.Marker({
                            position: position,
                            title: place.name || place.store.name,
                            image: markerImage
                        });

                        // 마커에 place 정보와 이미지 정보 저장
                        marker.storeData = place;
                        marker.categoryIcon = categoryIcon;
                        marker.markerId = 'marker_' + place.wr_id;

                        // 선택된 마커 이미지 미리 생성
                        createCustomMarkerImage(categoryIcon, true, function(selectedMarkerImage) {
                            marker.selectedImage = selectedMarkerImage;
                        });

                        // 마커 클릭 이벤트
                        kakao.maps.event.addListener(marker, 'click', function() {
                            handleMarkerClick(marker, place, response);
                        });

                        // 클러스터링 적용 또는 개별 마커 표시
                        if (isClusterEnabled && clusterer) {
                            try {
                                clusterer.addMarker(marker);
                            } catch (error) {
                                marker.setMap(map);
                            }
                        } else {
                            marker.setMap(map);
                        }

                        markers.push(marker);
                        processedMarkers++;

                        // 모든 새 마커 처리 완료 시
                        if (processedMarkers === totalNewMarkers) {
                            // 처리 완료
                        }
                    });
                });
            }
            /**
             * 마커 클릭 처리 함수
             */
            function handleMarkerClick(clickedMarker, place, response) {

                // 기존 선택 해제
                clearSelectedMarkers();

                // 현재 마커 선택 표시
                selectedMarkerId = clickedMarker.markerId;
                clickedMarker.isSelected = true;

                // 선택된 마커 이미지로 변경
                if (clickedMarker.selectedImage) {
                    clickedMarker.setImage(clickedMarker.selectedImage);
                } else {
                    // 선택된 이미지가 아직 준비되지 않은 경우 새로 생성
                    createCustomMarkerImage(clickedMarker.categoryIcon, true, function(selectedMarkerImage) {
                        clickedMarker.setImage(selectedMarkerImage);
                    });
                }

                // 매장 정보 패널 표시
                showStoreInfoPanel(place);
            }

            /**
             * 선택된 마커 해제
             */
            function clearSelectedMarkers() {
                markers.forEach(function(marker) {
                    if (marker.isSelected) {  // 조건 수정: 모든 선택된 마커 해제
                        marker.isSelected = false;
                        // 원래 이미지로 복원 (비선택 상태)
                        createCustomMarkerImage(marker.categoryIcon, false, function(originalImage) {
                            marker.setImage(originalImage);
                        });
                    }
                });
                selectedMarkerId = null;
            }

            /**
             * 모든 마커 제거
             */
            function clearAllMarkers() {
                if (isClusterEnabled && clusterer) {
                    try {
                        clusterer.clear();
                    } catch (error) {
                        console.warn('클러스터러 제거 중 오류:', error);
                        // 클러스터러 오류 시 개별 마커 제거로 대체
                        markers.forEach(function(marker) {
                            marker.setMap(null);
                        });
                    }
                } else {
                    markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                }
                markers = [];
                selectedMarkerId = null;
            }

            /**
             * 매장 정보 패널 표시
             */
            var $bot_info = $('.store-info-panel', $skin);
            function showStoreInfoPanel(place) {
                if (place.store && place.store.list_each) {
                    console.log($bot_info);
                    $(".store-info-content", $bot_info).html(place.store.list_each);
                    $bot_info.addClass('active');
                } else {
                    // list_each가 없으면 기본 정보로 표시
                    var storeName = place.name || (place.store && place.store.name) || '매장명 없음';
                    var storeAddress = (place.location && place.location.address_name) || '주소 정보 없음';
                    var storeCategory = (place.store && place.store.category_text) || '';

                    var basicHtml = '<div class="store-info-card">' +
                        '<h4>' + storeName + '</h4>' +
                        (storeCategory ? '<p class="category">' + storeCategory + '</p>' : '') +
                        '<p class="address">' + storeAddress + '</p>' +
                        '</div>';

                    $(".store-info-content", $bot_info).html(basicHtml);
                    $bot_info.addClass('active');
                }
            }

            function closeStoreInfoPanel() {
                $bot_info.removeClass('active');
            }

            $(".store-info-panel-close", $skin).click(function() {
                closeStoreInfoPanel();
            });

            // 매장 목록 버튼 이벤트 설정
            function setupStoreListButton() {
                // 기존 이벤트 제거 후 재바인딩
                $('.store-list-btn', $skin).off('click').on('click', function(e) {
                    e.preventDefault();
                    console.log('목록 버튼 클릭됨'); // 디버그용
                    showStoreList();
                });

                $('.store-list-close', $skin).off('click').on('click', function() {
                    hideStoreList();
                });
            }

// 매장 목록 표시
            function showStoreList() {
                var currentBounds = map.getBounds();
                var currentCenter = map.getCenter();
                var currentLevel = map.getLevel();

                var requestData = {
                    action: 'widget',
                    bounds: {
                        sw_lat: currentBounds.getSouthWest().getLat(),
                        sw_lng: currentBounds.getSouthWest().getLng(),
                        ne_lat: currentBounds.getNorthEast().getLat(),
                        ne_lng: currentBounds.getNorthEast().getLng()
                    },
                    center: {
                        lat: currentCenter.getLat(),
                        lng: currentCenter.getLng()
                    },
                    widget:'content/map_list',
                    level: currentLevel
                };

                // AJAX로 매장 목록 로드 (올바른 옵션명 사용)
                wv_ajax('<?php echo wv()->store_manager->ajax_url(); ?>', {
                    replace: '.store-list-content'  // ✅ replace_width → replace로 수정
                }, requestData);

                // 매장 목록 패널 표시
                $('.store-list', $skin).fadeIn(300);
            }
// 매장 목록 숨기기
            function hideStoreList() {
                $('.store-list', $skin).fadeOut(300);
            }

            // 초기화 실행
            initMap();

            // 윈도우 리사이즈 시 지도 높이 재조정
            $(window).on('resize', function() {
                setMapHeight();
                if (map) {
                    map.relayout();
                }
            });
        });
    </script>
</div>