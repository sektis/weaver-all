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
    <div class="loading-overlay">
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

            var map, clusterer;
            var markers = [];
            var selectedMarkerId = null;

            // 카테고리 아이콘 래핑 이미지 경로 (전역 변수)
            var markerIconWrap = '';
            var markerIconWrapOn = '';

            // 이벤트 발송 함수
            function triggerMapChangedEvent() {
                var bounds = map.getBounds();
                var center = map.getCenter();
                var level = map.getLevel();
                console.log('map changed')
                // 기존 선택 해제
                clearSelectedMarkers();
                $skin.trigger('wv_location_map_changed', {
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

            function showLoading(show) {
                var $loading = $('.loading-overlay',$skin);
                if (show) {
                    $loading.show();
                } else {
                    $loading.hide();
                }
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
                // if (isClusterEnabled) {
                //     clusterer = new kakao.maps.MarkerClusterer({
                //         map: map,
                //         averageCenter: true,
                //         minLevel: 5,
                //         disableClickZoom: false,
                //         styles: [{
                //             width: '30px', height: '30px',
                //             background: 'rgba(51, 204, 255, .8)',
                //             borderRadius: '15px',
                //             color: '#fff',
                //             textAlign: 'center',
                //             fontWeight: 'bold',
                //             lineHeight: '31px'
                //         }]
                //     });
                // }
                // 클러스터러 초기화 (안전한 방식)
                initClusterer();

                // 현재 위치 마커 생성
                addCurrentLocationMarker(lat, lng);

                // 지도 이벤트 등록
                setupMapEvents();

                // 외부 통신 이벤트 등록
                setupExternalCommunication();

                // 로딩 오버레이 숨기기
                showLoading(false);
                // 지도 초기화 완료 후 즉시 이벤트 발송
                setTimeout(function() {
                    triggerMapChangedEvent();
                }, 100);
            }

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
                $skin.on('wv_location_map_request_bounds', function(event, data) {
                    if (map) {
                        var bounds = map.getBounds();
                        var center = map.getCenter();
                        var level = map.getLevel();

                        $skin.trigger('wv_location_map_bounds_received', {
                            bounds: bounds,
                            center: center,
                            level: level
                        });
                    }
                });

                // 외부에서 특정 위치로 이동 요청시 처리
                $skin.on('wv_location_map_move_to', function(event, data) {
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



                // 범용 위치 데이터 업데이트 이벤트 수신 (가공된 데이터)
                $skin.on('wv_location_map_updated', function(event, data) {
                    if (data.marker_wrap) {
                        markerIconWrap = data.marker_wrap;
                    }
                    if (data.marker_wrap_on) {
                        markerIconWrapOn = data.marker_wrap_on;
                    }


                    clearSelectedMarkers()

                    if (data.lists && Array.isArray(data.lists)) {
                        updateMarkers(data.lists, data);
                    }
                });
            }


            function updateMarkers(lists, response) {
                if (!map) return;

                // 새로운 매장 목록에서 id 추출
                var newPlaceIds = lists.map(function(place) {
                    return place.id;
                });

                // 기존 마커들에서 id 추출
                var existingPlaceIds = markers.map(function(marker) {
                    return marker.placeData ? marker.placeData.id : null;
                }).filter(function(id) { return id !== null; });

                // 1. 제거할 마커들 (기존에 있지만 새 목록에 없는 것들)
                var markersToRemove = markers.filter(function(marker) {
                    return marker.placeData && newPlaceIds.indexOf(marker.placeData.id) === -1;
                });

                // 2. 추가할 매장들 (새 목록에 있지만 기존에 없는 것들)
                var placesToAdd = lists.filter(function(place) {
                    return existingPlaceIds.indexOf(place.id) === -1;
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
                var totalNewMarkers = placesToAdd.length;

                if (totalNewMarkers === 0) {
                    return; // 추가할 마커가 없으면 종료
                }

                placesToAdd.forEach(function(place, index) {
                    if (!place.lat || !place.lng) {
                        processedMarkers++;
                        return;
                    }

                    var position = new kakao.maps.LatLng(place.lat, place.lng);
                    var categoryIcon = place.marker.image ? place.marker.image : null;

                    // 커스텀 마커 이미지 생성 (비동기)
                    createCustomMarkerImage(categoryIcon, false, function(markerImage) {
                        // 마커 생성
                        var marker = new kakao.maps.Marker({
                            position: position,
                            title: place.name,
                            image: markerImage
                        });

                        // 마커에 place 정보와 이미지 정보 저장
                        marker.placeData = place;
                        marker.categoryIcon = categoryIcon;
                        marker.markerId = 'marker_' + place.id;

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
             * 이미지 합성 함수 - 배경 위에 카테고리 아이콘을 올려서 새로운 마커 이미지 생성
             */
            function createCompositeMarkerImage(backgroundImageUrl, categoryIconUrl, isSelected, callback) {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                var loadedImages = 0;
                var totalImages = 2;

                // 캔버스 크기 설정 (마커 크기 + shadow 공간)
                canvas.width = 44;  // shadow 공간 확보
                canvas.height = 44; // shadow 공간 확보

                var backgroundImg = new Image();
                var categoryImg = new Image();

                function onImageLoad() {
                    loadedImages++;
                    if (loadedImages === totalImages) {
                        // 모든 이미지가 로드되면 합성
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        // Shadow 설정
                        ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
                        ctx.shadowBlur = 4;
                        ctx.shadowOffsetX = 0;
                        ctx.shadowOffsetY = 2;

                        // 배경 이미지 그리기 (중앙에 36x36 크기로)
                        var markerX = (canvas.width - 36) / 2;
                        var markerY = (canvas.height - 36) / 2;
                        ctx.drawImage(backgroundImg, markerX, markerY, 36, 36);

                        // Shadow 제거 (카테고리 아이콘에는 shadow 안주기)
                        ctx.shadowColor = 'transparent';

                        // 카테고리 아이콘 그리기 (중앙 위치, 조금 작게)
                        var iconSize = 20;
                        var iconX = (canvas.width - iconSize) / 2;
                        var iconY = (canvas.height - iconSize) / 2;
                        ctx.drawImage(categoryImg, iconX, iconY, iconSize, iconSize);

                        // Canvas를 데이터 URL로 변환
                        var dataUrl = canvas.toDataURL('image/png');

                        // 마커 이미지 생성 (offset도 조정)
                        var size = new kakao.maps.Size(44, 44);
                        var option = { offset: new kakao.maps.Point(22, 44) };
                        var markerImage = new kakao.maps.MarkerImage(dataUrl, size, option);

                        callback(markerImage);
                    }
                }

                backgroundImg.onload = onImageLoad;
                categoryImg.onload = onImageLoad;

                backgroundImg.onerror = function() {
                    callback(createDefaultMarkerImage(isSelected));
                };

                categoryImg.onerror = function() {
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
                if (categoryIcon && markerIconWrap && markerIconWrapOn) {
                    var backgroundImageUrl = isSelected ? markerIconWrapOn : markerIconWrap;
                    createCompositeMarkerImage(backgroundImageUrl, categoryIcon, isSelected, callback);
                } else {
                    // 기본 마커 사용
                    callback(createDefaultMarkerImage(isSelected));
                }
            }

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
                $skin.trigger('wv_location_map_marker_clicked', {place});

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

            // 초기화 실행
            initMap();

        });
    </script>
</div>