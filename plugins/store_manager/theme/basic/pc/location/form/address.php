<?php
/**
 * Store Manager - Location 파트 Address Form 스킨
 * 파일: plugins/store_manager/theme/basic/pc/location/form/address.php
 *
 * Location 플러그인의 address 스킨을 포함하고
 * 이벤트를 받아서 location 파트의 모든 hidden input에 데이터 설정
 *
 * 주의: address 컬럼은 빈 DDL이므로 실제로는 다른 컬럼들만 처리
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 기존 값 가져오기 (location 파트의 모든 필드)
$location_data = array();
if (isset($row['location']) && is_array($row['location'])) {
    $location_data = $row['location'];
}

// 개별 필드 값 직접 추출 (store_manager 클래스의 물리키→논리키 변환 활용)
$lat = isset($row['lat']) ? $row['lat'] : '';
$lng = isset($row['lng']) ? $row['lng'] : '';
$region_1depth_name = isset($row['region_1depth_name']) ? $row['region_1depth_name'] : '';
$region_2depth_name = isset($row['region_2depth_name']) ? $row['region_2depth_name'] : '';
$region_3depth_name = isset($row['region_3depth_name']) ? $row['region_3depth_name'] : '';
$address_name = isset($row['address_name']) ? $row['address_name'] : '';

// Location 플러그인 address 스킨에 전달할 초기 데이터
$address_skin_data = array(
    'lat' => $lat,
    'lng' => $lng,
    'address_name' => $address_name
);

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> store-manager-location-address-form position-relative" style="">
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .form-label {
            font-size: var(--wv-14);
            font-weight: 600;
            color: #0D171B;
            margin-bottom: var(--wv-8);
        }

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full">
        <div class=" ">
            <div class="wv-vstack" style="row-gap: var(--wv-10)">
                <p class="wv-ps-subtitle">매장 주소</p>
                <div class="hstack  " style="gap:var(--wv-10)">
                    <div class="form-floating position-relative" style="z-index: 10">
                        <input type="text" name="location[address_name]"   id="location[address_name]" required readonly class="form-control   location-address-name " maxlength="10" minlength="10" placeholder="지도에서 검색하거나 핀을 움직이세요."
                               value="<?php echo htmlspecialchars($row['address_name']); ?>">
                        <label for="contract[biz_num]" class="floatingInput">기본주소</label>
                    </div>
                    <div class="form-floating position-relative" style="z-index: 10">
                        <input type="text" name="location[road_address_name]"   id="location[road_address_name]"   readonly class="form-control   location-road-address-name " maxlength="10" minlength="10" placeholder="지도에서 검색하거나 핀을 움직이세요."
                               value="<?php echo htmlspecialchars($row['road_address_name']); ?>">
                        <label for="contract[biz_num]" class="floatingInput">도로명주소</label>
                    </div>
                    <div class="form-floating position-relative col" style="z-index: 10">
                        <input type="text" name="location[detail_address_name]"   id="location[detail_address_name]"   class="form-control     "  placeholder="지도에서 검색하거나 핀을 움직이세요."
                                value="<?php echo htmlspecialchars($row['detail_address_name']); ?>">
                        <label for="contract[biz_num]" class="floatingInput">상세주소</label>
                    </div>
                </div>

                <div class="">
                <?php
                // Location 플러그인의 address 스킨 렌더링
//                if (wv_plugin_exists('location')) {
//                    echo wv('location')->make_skin('address', $address_skin_data);
//                } else {
//                    echo '<div class="alert alert-warning">Location 플러그인이 활성화되지 않았습니다.</div>';
//                }

                echo wv('location')->display('address', $address_skin_data);
                ?>
                </div>
            </div>

            <!-- Hidden Fields - location 파트의 모든 필드 -->
            <input type="hidden" name="location[lat]" class="location-lat" value="<?php echo htmlspecialchars($lat); ?>">
            <input type="hidden" name="location[lng]" class="location-lng" value="<?php echo htmlspecialchars($lng); ?>">
            <input type="hidden" name="location[region_1depth_name]" class="location-region-1depth-name" value="<?php echo htmlspecialchars($region_1depth_name); ?>">
            <input type="hidden" name="location[region_2depth_name]" class="location-region-2depth-name" value="<?php echo htmlspecialchars($region_2depth_name); ?>">
            <input type="hidden" name="location[region_3depth_name]" class="location-region-3depth-name" value="<?php echo htmlspecialchars($region_3depth_name); ?>">

        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");

            // Location 플러그인에서 발생하는 주소 변경 이벤트 리스닝
            function handleAddressChanged(event, data) {
                // 이벤트 데이터가 없으면 CustomEvent detail에서 가져오기
                if (!data && event.originalEvent && event.originalEvent.detail) {
                    data = event.originalEvent.detail;
                }

                if (!data) {
                    console.warn('주소 데이터가 없습니다.');
                    return;
                }

                console.log('Store Manager에서 주소 변경 이벤트 수신:', data);

                // Hidden input 필드들 업데이트
                if (data.y !== undefined) {
                    $skin.find('.location-lat').val(data.lng);
                }
                if (data.x !== undefined) {
                    $skin.find('.location-lng').val(data.lat);
                }
                if (data.region_1depth_name !== undefined) {
                    $skin.find('.location-region-1depth-name').val(data.region_1depth_name);
                }
                if (data.region_2depth_name !== undefined) {
                    $skin.find('.location-region-2depth-name').val(data.region_2depth_name);
                }
                if (data.region_3depth_name !== undefined) {
                    $skin.find('.location-region-3depth-name').val(data.region_3depth_name);
                }
                if (data.address_name !== undefined) {
                    $skin.find('.location-address-name').val(data.address_name);
                }
                if (data.road_address_name !== undefined) {
                    $skin.find('.location-road-address-name').val(data.road_address_name);
                }

                // 선택적: 커스텀 처리가 필요하면 여기에 추가
                // 예: 주소 변경 후 추가 검증, UI 업데이트 등
            }

            // jQuery 이벤트 리스너 등록
            $(document).on('wv_location_address_changed', handleAddressChanged);



            // 폼 제출 전 검증
            var $form = $skin.closest('form');
            if ($form.length) {
                $form.on('submit', function(e) {
                    var lat = $skin.find('.location-lat').val();
                    var lng = $skin.find('.location-lng').val();

                    if (!lat || !lng) {
                        alert('매장 위치를 선택해주세요.');
                        e.preventDefault();
                        return false;
                    }

                    // 추가 검증 로직
                    var region1 = $skin.find('.location-region-1depth-name').val();
                    var region3 = $skin.find('.location-region-3depth-name').val();

                    if (!region1 || !region3) {
                        alert('올바른 주소를 선택해주세요.');
                        e.preventDefault();
                        return false;
                    }
                });
            }


        });
    </script>
</div>