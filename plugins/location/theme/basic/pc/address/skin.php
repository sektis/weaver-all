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
                    <?php echo wv_widget('search_address',array())?>
                </div>
            </div>

            <!-- 카카오맵 섹션 -->
            <div class="map-section">
                <div class="kakao-map w-100 h-100"></div>
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
            var  event_sending = false;

            // 전역 이벤트 발생 함수
            function triggerAddressChangedEvent(data) {

                // jQuery 이벤트
                if(event_sending){
                    event_sending=false;
                    return true;
                }
                $(document).trigger('wv_location_address_changed', [data]);
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
                    wv_coords_to_address(latlng.getLat(),latlng.getLng(),function (result) {

                        if(isset(result.list) && result.list.length){
                            var assign_result = Object.assign(  result.list[0], {lat:latlng.getLat(),lng:latlng.getLng()})
                            triggerAddressChangedEvent( assign_result);
                        }

                    })

                    // 클릭한 위치의 주소 정보 가져오기
 ;
                });

                // 초기 위치가 있으면 설정
                if (currentData.lat && currentData.lng) {
                    var coords = new kakao.maps.LatLng(currentData.lat, currentData.lng);
                    map.setCenter(coords);
                    marker.setPosition(coords);
                }
            }

            // 이벤트 바인딩
            function bindEvents() {


                $(document).on("wv_location_search_address_select",function(event, data) {

                    var coords = new kakao.maps.LatLng(data.lat, data.lng);

                    // ✅ 지도 센터와 마커 즉시 이동


                    wv_address_result_to_region_merge(data,function (res) {

                        triggerAddressChangedEvent(res);

                        map.setCenter(coords);
                        marker.setPosition(coords);
                        map.setLevel(3);
                    })


                })

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


        });
    </script>
</div>