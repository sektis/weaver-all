<?php
/**
 * Location 플러그인 - Address 스킨
 * 파일: plugins/location/theme/basic/pc/address/skin.php
 *
 * 주소 검색 + 카카오맵 + 위치 선택 기능
 * 주소 변경시 전역 이벤트 'wv_location_address_changed' 발생
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 초기 데이터 (옵션)
$initial_lat = isset($data['lat']) ? $data['lat'] : '';
$initial_lng = isset($data['lng']) ? $data['lng'] : '';
$initial_address = isset($data['address_name']) ? $data['address_name'] : '';
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-location-address-skin position-relative" style="">
    <style>
        <?php echo $skin_selector?> {
            border: 1px solid #EFEFEF;
            border-radius: var(--wv-8);
            background: #fff;
            overflow: hidden;
        }

        <?php echo $skin_selector?> .address-search-section {
            padding: var(--wv-16);
            border-bottom: 1px solid #EFEFEF;
        }

        <?php echo $skin_selector?> .address-search-input {
                                        height: var(--wv-43);
                                        border: 1px solid #EFEFEF;
                                        border-radius: var(--wv-4);
                                        background: #F9F9F9;
                                        padding: 0 var(--wv-12);
                                        font-size: var(--wv-14);
                                        outline: none;
                                        letter-spacing: calc(var(--wv-0_56) * -1);
                                    }

        <?php echo $skin_selector?> .address-search-input::placeholder {
                                        color: #CFCFCF;
                                        font-size: var(--wv-14);
                                        font-weight: 500;
                                    }

        <?php echo $skin_selector?> .address-search-input:focus {
                                        border-color: #0D6EFD;
                                        background: #fff;
                                    }

        <?php echo $skin_selector?> .search-btn {
                                        position: absolute;
                                        top: 50%;
                                        right: var(--wv-12);
                                        transform: translateY(-50%);
                                        background: none;
                                        border: none;
                                        padding: 0;
                                        cursor: pointer;
                                    }

        <?php echo $skin_selector?> .map-section {
                                        height: 300px;
                                        position: relative;
                                    }

        <?php echo $skin_selector?> .current-location-btn {
                                        position: absolute;
                                        top: var(--wv-12);
                                        right: var(--wv-12);
                                        z-index: 10;
                                        background: #fff;
                                        border: 1px solid #EFEFEF;
                                        border-radius: var(--wv-4);
                                        padding: var(--wv-8) var(--wv-12);
                                        font-size: var(--wv-12);
                                        cursor: pointer;
                                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                        display: flex;
                                        align-items: center;
                                        gap: var(--wv-4);
                                    }

        <?php echo $skin_selector?> .current-location-btn:hover {
                                        background: #F9F9F9;
                                    }

        <?php echo $skin_selector?> .address-display {
                                        padding: var(--wv-12) var(--wv-16);
                                        background: #F9F9F9;
                                        border-top: 1px solid #EFEFEF;
                                        font-size: var(--wv-14);
                                        color: #0D171B;
                                        min-height: var(--wv-40);
                                        display: flex;
                                        align-items: center;
                                    }

        <?php echo $skin_selector?> .address-display.empty {
                                        color: #97989C;
                                    }

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {
        <?php echo $skin_selector?> .map-section {
            height: 250px;
        }
        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full">
        <div class="">
            <!-- 주소 검색 섹션 -->
            <div class="address-search-section">
                <div class="position-relative">
                    <input type="text"
                           class="form-control address-search-input w-100"
                           placeholder="주소를 검색하세요 (예: 서울 강남구, 강남구 논현동)"
                           autocomplete="off">
                    <button type="button" class="search-btn">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="#97989C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.9999 19L14.6499 14.65" stroke="#97989C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 카카오맵 섹션 -->
            <div class="map-section">
                <div class="kakao-map w-100 h-100"></div>
                <button type="button" class="current-location-btn">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <circle cx="6" cy="6" r="2" fill="#97989C"/>
                        <path d="M6 1v2M6 9v2M1 6h2M9 6h2" stroke="#97989C" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    현 위치로 설정
                </button>
            </div>

            <!-- 선택된 주소 표시 -->
            <div class="address-display empty">
                주소를 검색하거나 지도에서 위치를 선택해주세요.
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var map, marker, geocoder;
            var currentData = {
                lat: <?php echo $initial_lat ? $initial_lat : 'null'; ?>,
                lng: <?php echo $initial_lng ? $initial_lng : 'null'; ?>,
                region_1depth_name: '',
                region_2depth_name: '',
                region_3depth_name: '',
                address_name: '<?php echo htmlspecialchars($initial_address); ?>',
                full_address: ''
            };

            // 전역 이벤트 발생 함수
            function triggerAddressChangedEvent(data) {
                // jQuery 이벤트
                $(document).trigger('wv_location_address_changed', [data]);

                // 네이티브 이벤트
                var event = new CustomEvent('wv_location_address_changed', {
                    detail: data
                });
                document.dispatchEvent(event);

                console.log('Location address changed:', data);
            }

            // location 플러그인의 주소 검색 API 활용
            function searchAddress(query) {
                if (!query.trim()) return;

                $.ajax({
                    url: '<?php echo wv()->location->ajax_url(); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        wv_location_action: 'api',
                        a: 'search',
                        q: query.trim()
                    }
                }).done(function(res) {
                    handleSearchResults(res);
                }).fail(function() {
                    console.warn('주소 검색 실패');
                });
            }

            function handleSearchResults(res) {
                var results = [];
                if (Array.isArray(res)) {
                    results = res;
                } else if (res.items && Array.isArray(res.items)) {
                    results = res.items;
                } else if (res.data && Array.isArray(res.data)) {
                    results = res.data;
                }

                if (results.length > 0) {
                    var first = results[0];
                    var region1 = first.depth1 || first.region_1depth_name || first.d1 || '';
                    var region2 = first.depth2 || first.region_2depth_name || first.d2 || '';
                    var region3 = first.depth3 || first.region_3depth_name || first.name || first.d3 || '';

                    if (region1 && region3) {
                        moveMapToAddress(region1, region2, region3);
                    }
                }
            }

            function moveMapToAddress(region1, region2, region3) {
                if (!geocoder) return;

                var fullAddress = region1 + ' ' + (region2 ? region2 + ' ' : '') + region3;

                geocoder.addressSearch(fullAddress, function(result, status) {
                    if (status === kakao.maps.services.Status.OK) {
                        var coords = new kakao.maps.LatLng(result[0].y, result[0].x);

                        map.setCenter(coords);
                        marker.setPosition(coords);

                        updateLocationData(result[0].y, result[0].x, region1, region2, region3, fullAddress);
                    }
                });
            }

            function updateLocationData(lat, lng, region1, region2, region3, fullAddress) {
                currentData = {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng),
                    region_1depth_name: region1 || '',
                    region_2depth_name: region2 || '',
                    region_3depth_name: region3 || '',
                    address_name: fullAddress || '',
                    full_address: fullAddress || ''
                };

                // 주소 표시 업데이트
                var $display = $skin.find('.address-display');
                var displayText = (region1 || '') +
                    (region2 ? ' ' + region2 : '') +
                    (region3 ? ' ' + region3 : '');

                if (displayText.trim()) {
                    $display.removeClass('empty').text(displayText.trim());
                } else {
                    $display.addClass('empty').text('주소를 검색하거나 지도에서 위치를 선택해주세요.');
                }

                // 전역 이벤트 발생
                triggerAddressChangedEvent(currentData);
            }

            function getCurrentLocation() {
                if (typeof wv_get_current_location === 'function') {
                    wv_get_current_location(function(result) {
                        if (result && result.lat && result.lng) {
                            var coords = new kakao.maps.LatLng(result.lat, result.lng);
                            map.setCenter(coords);
                            marker.setPosition(coords);

                            var addr = result.address || {};
                            updateLocationData(
                                result.lat,
                                result.lng,
                                addr.region_1depth_name || '',
                                addr.region_2depth_name || '',
                                addr.region_3depth_name || '',
                                addr.address_name || ''
                            );
                        }
                    });
                } else {
                    // 기본 geolocation 사용
                    if ('geolocation' in navigator) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var lat = position.coords.latitude;
                            var lng = position.coords.longitude;

                            var coords = new kakao.maps.LatLng(lat, lng);
                            map.setCenter(coords);
                            marker.setPosition(coords);

                            // 좌표를 주소로 변환
                            geocoder.coord2RegionCode(lng, lat, function(result, status) {
                                if (status === kakao.maps.services.Status.OK) {
                                    for (var i = 0; i < result.length; i++) {
                                        if (result[i].region_type === 'H') {
                                            updateLocationData(
                                                lat, lng,
                                                result[i].region_1depth_name,
                                                result[i].region_2depth_name,
                                                result[i].region_3depth_name,
                                                result[i].address_name || ''
                                            );
                                            break;
                                        }
                                    }
                                }
                            });
                        }, function() {
                            alert('현재 위치를 가져올 수 없습니다.');
                        });
                    }
                }
            }

            function initMap() {
                if (typeof kakao === 'undefined' || !kakao.maps) {
                    console.error('카카오맵 API가 로드되지 않았습니다.');
                    return;
                }

                var container = $skin.find('.kakao-map')[0];
                var options = {
                    center: new kakao.maps.LatLng(currentData.lat || 37.5665, currentData.lng || 126.9780),
                    level: 3
                };

                map = new kakao.maps.Map(container, options);
                geocoder = new kakao.maps.services.Geocoder();

                // 마커 생성
                marker = new kakao.maps.Marker({
                    position: map.getCenter()
                });
                marker.setMap(map);

                // 지도 클릭 이벤트
                kakao.maps.event.addListener(map, 'click', function(mouseEvent) {
                    var latlng = mouseEvent.latLng;

                    marker.setPosition(latlng);

                    // 클릭한 위치의 주소 정보 가져오기
                    geocoder.coord2RegionCode(latlng.getLng(), latlng.getLat(), function(result, status) {
                        if (status === kakao.maps.services.Status.OK) {
                            for (var i = 0; i < result.length; i++) {
                                if (result[i].region_type === 'H') {
                                    updateLocationData(
                                        latlng.getLat(),
                                        latlng.getLng(),
                                        result[i].region_1depth_name,
                                        result[i].region_2depth_name,
                                        result[i].region_3depth_name,
                                        result[i].address_name || ''
                                    );
                                    break;
                                }
                            }
                        }
                    });
                });

                // 초기 위치가 있으면 설정
                if (currentData.lat && currentData.lng) {
                    var coords = new kakao.maps.LatLng(currentData.lat, currentData.lng);
                    map.setCenter(coords);
                    marker.setPosition(coords);

                    if (currentData.address_name) {
                        $skin.find('.address-display').removeClass('empty').text(currentData.address_name);
                    }
                }
            }

            // 이벤트 바인딩
            function bindEvents() {
                // 주소 검색
                $skin.on('keypress', '.address-search-input', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        searchAddress($(this).val());
                    }
                });

                $skin.on('click', '.search-btn', function() {
                    var query = $skin.find('.address-search-input').val();
                    searchAddress(query);
                });

                // 현재 위치 버튼
                $skin.on('click', '.current-location-btn', function() {
                    getCurrentLocation();
                });
            }

            // 카카오맵이 로드될 때까지 대기
            function checkKakaoMap() {
                if (typeof kakao !== 'undefined' && kakao.maps && kakao.maps.services) {
                    initMap();
                    bindEvents();
                } else {
                    setTimeout(checkKakaoMap, 100);
                }
            }
            checkKakaoMap();

            // 외부에서 데이터 설정할 수 있는 메서드 노출
            window['wv_location_address_' + '<?php echo $skin_id; ?>'] = {
                setData: function(data) {
                    if (data.lat && data.lng && map) {
                        var coords = new kakao.maps.LatLng(data.lat, data.lng);
                        map.setCenter(coords);
                        marker.setPosition(coords);
                        updateLocationData(data.lat, data.lng, data.region_1depth_name, data.region_2depth_name, data.region_3depth_name, data.address_name);
                    }
                },
                getData: function() {
                    return currentData;
                }
            };
        });
    </script>
</div>