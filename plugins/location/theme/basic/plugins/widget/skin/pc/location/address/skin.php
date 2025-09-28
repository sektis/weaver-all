<?php
/**
 * Location 플러그인 - Address 스킨
 * 파일: plugins/location/theme/basic/pc/address/skin.php
 *
 * 주소 검색 + 카카오맵 + 위치 선택 기능
 * 주소 변경시 전역 이벤트 'wv_location_address_changed' 발생
 * 커스텀 마커 아이콘 지원 ($data['icon'], $data['icon_wrap'])
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 초기 데이터 (옵션)
$initial_lat = isset($data['lat']) ? $data['lat'] : '';
$initial_lng = isset($data['lng']) ? $data['lng'] : '';
$initial_address = isset($data['address_name']) ? $data['address_name'] : '';
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-location-address-skin position-relative h-100" style="">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .address-search-section {padding: var(--wv-16);border-bottom: 1px solid #EFEFEF;}
        <?php echo $skin_selector?> .kakao-map {min-height: var(--wv-100)}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100">

        <!-- 주소 검색 섹션 -->
        <?php if($data['use_search_address']){ ?>
            <div class="address-search-section">
                <div class="position-relative">
                    <?php echo wv_widget('location/search_address',array())?>
                </div>
            </div>
        <?php } ?>

        <div class="kakao-map w-100 h-100"></div>
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

            /**
             * 커스텀 아이콘으로 마커 생성
             */
            function createMarkerWithIcon(position) {
                var markerOptions = {
                    position: position
                };

                // 커스텀 아이콘 처리
                <?php if(isset($data['icon']) && $data['icon']): ?>
                <?php if(isset($data['icon_wrap']) && $data['icon_wrap']): ?>
                // 아이콘과 배경이 모두 있는 경우 - 합성 이미지 생성
                createCompositeMarkerImage('<?php echo $data['icon_wrap']; ?>', '<?php echo $data['icon']; ?>', function(compositeImage) {
                    markerOptions.image = compositeImage;
                    if (marker) marker.setMap(null);
                    marker = new kakao.maps.Marker(markerOptions);
                    marker.setMap(map);
                });
                return;
                <?php else: ?>
                // 아이콘만 있는 경우
                var iconSize = new kakao.maps.Size(32, 32);
                var iconOption = { offset: new kakao.maps.Point(16, 32) };
                markerOptions.image = new kakao.maps.MarkerImage('<?php echo $data['icon']; ?>', iconSize, iconOption);
                <?php endif; ?>
                <?php endif; ?>

                // 마커 생성 및 설정
                if (marker) marker.setMap(null);
                marker = new kakao.maps.Marker(markerOptions);
                marker.setMap(map);
            }

            /**
             * 합성 마커 이미지 생성 (아이콘 + 배경 + 그림자)
             */
            function createCompositeMarkerImage(backgroundUrl, iconUrl, callback) {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                canvas.width = 48;  // 그림자 공간 확보
                canvas.height = 48;

                var backgroundImg = new Image();
                var iconImg = new Image();
                var loadedCount = 0;

                function checkComplete() {
                    loadedCount++;
                    if (loadedCount === 2) {
                        // 그림자 효과 설정
                        ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
                        ctx.shadowBlur = 6;
                        ctx.shadowOffsetX = 2;
                        ctx.shadowOffsetY = 2;

                        // 배경 이미지 그리기 (그림자 포함)
                        ctx.drawImage(backgroundImg, 4, 4, 40, 40);

                        // 그림자 효과 제거 (아이콘에는 적용하지 않음)
                        ctx.shadowColor = 'transparent';
                        ctx.shadowBlur = 0;
                        ctx.shadowOffsetX = 0;
                        ctx.shadowOffsetY = 0;

                        // 아이콘 이미지 그리기 (중앙에 배치)
                        var iconSize = 24;
                        var iconX = (48 - iconSize) / 2;
                        var iconY = (48 - iconSize) / 2;
                        ctx.drawImage(iconImg, iconX, iconY, iconSize, iconSize);

                        // 마커 이미지 생성
                        var imageSize = new kakao.maps.Size(48, 48);
                        var imageOption = { offset: new kakao.maps.Point(24, 44) };
                        var markerImage = new kakao.maps.MarkerImage(canvas.toDataURL(), imageSize, imageOption);

                        callback(markerImage);
                    }
                }

                backgroundImg.onload = checkComplete;
                iconImg.onload = checkComplete;

                backgroundImg.src = backgroundUrl;
                iconImg.src = iconUrl;
            }

            /**
             * 마커 위치 업데이트
             */
            function updateMarkerPosition(position) {
                createMarkerWithIcon(position);
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

                // 마커 생성 (커스텀 아이콘 적용)
                createMarkerWithIcon(map.getCenter());

                // 지도 클릭 이벤트
                kakao.maps.event.addListener(map, 'click', function(mouseEvent) {
                    var latlng = mouseEvent.latLng;

                    updateMarkerPosition(latlng);
                    wv_coords_to_address(latlng.getLat(),latlng.getLng(),function (result) {

                        if(isset(result.list) && result.list.length){
                            var assign_result = Object.assign(  result.list[0], {lat:latlng.getLat(),lng:latlng.getLng()})
                            triggerAddressChangedEvent( assign_result);
                        }

                    })

                    // 클릭한 위치의 주소 정보 가져오기
                });

                // 초기 위치가 있으면 설정
                if (currentData.lat && currentData.lng) {
                    var coords = new kakao.maps.LatLng(currentData.lat, currentData.lng);
                    map.setCenter(coords);
                    updateMarkerPosition(coords);
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
                        updateMarkerPosition(coords);
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