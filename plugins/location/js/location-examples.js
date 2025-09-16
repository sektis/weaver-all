/**
 * WeaverLocation JavaScript 사용 예시
 */

// 1. 초기화
$(document).ready(function() {
    // 카카오 API 키 설정 (필요시)
    if (typeof wv_kakao_js_apikey !== 'undefined') {
        wv_location_init(wv_kakao_js_apikey);
    }
    
    // 카카오 SDK 로드 확인
    if (!wv_check_kakao()) {
        console.error('카카오맵 SDK가 로드되지 않았습니다.');
    }
});

/**
 * 사용 예시 1: 주소 검색
 */
function example_address_search() {
    // 기본 키워드 검색
    WeaverLocation.address_search('영도구 동삼동', 10, 1, function(result) {
        if (result.error) {
            console.error('검색 오류:', result.error);
            return;
        }
        
        console.log('검색 결과:', result.list);
        console.log('총 개수:', result.total_count);
        console.log('마지막 페이지:', result.is_end);
        
        // 결과를 화면에 표시
        display_search_results(result.list);
    });
}

/**
 * 사용 예시 2: 좌표를 지역코드로 변환
 */
function example_coords_to_region() {
    var lat = 35.0716363470706;  // 위도
    var lng = 129.057370812646;  // 경도
    
    WeaverLocation.coords_to_region(lat, lng, function(result) {
        if (result.error) {
            console.error('변환 오류:', result.error);
            return;
        }
        
        console.log('지역 정보:', result.list);
        
        if (result.list.length > 0) {
            var region = result.list[0];
            console.log('시도:', region.region_1depth_name);
            console.log('시군구:', region.region_2depth_name);
            console.log('읍면동:', region.region_3depth_name);
        }
    });
}

/**
 * 사용 예시 3: 좌표를 주소로 변환
 */
function example_coords_to_address() {
    var lat = 35.0716363470706;  // 위도
    var lng = 129.057370812646;  // 경도
    
    WeaverLocation.coords_to_address(lat, lng, function(result) {
        if (result.error) {
            console.error('주소 변환 오류:', result.error);
            return;
        }
        
        console.log('주소 정보:', result.list);
        
        if (result.list.length > 0) {
            var address = result.list[0];
            console.log('주소:', address.address_name);
            console.log('도로명주소:', address.road_address_name);
        }
    });
}

/**
 * 사용 예시 4: 주소를 좌표로 변환
 */
function example_address_to_coords() {
    var address = '부산 영도구 동삼동';
    
    WeaverLocation.address_to_coords(address, function(result) {
        if (result.error) {
            console.error('좌표 변환 오류:', result.error);
            return;
        }
        
        console.log('좌표 정보:', result.list);
        
        if (result.list.length > 0) {
            var coord = result.list[0];
            console.log('위도:', coord.y);
            console.log('경도:', coord.x);
            console.log('주소:', coord.address_name);
        }
    });
}

/**
 * 사용 예시 5: 카테고리로 장소 검색
 */
function example_category_search() {
    // 전체 지역에서 카페 검색
    WeaverLocation.category_search('CE7', function(result) {
        if (result.error) {
            console.error('카테고리 검색 오류:', result.error);
            return;
        }
        
        console.log('카페 검색 결과:', result.list);
        display_search_results(result.list);
    });
    
    // 특정 위치 주변 음식점 검색
    var lat = 35.0716;
    var lng = 129.0574;
    var radius = 1000; // 1km
    
    WeaverLocation.category_search('FD6', lat, lng, radius, function(result) {
        if (result.error) {
            console.error('주변 음식점 검색 오류:', result.error);
            return;
        }
        
        console.log('주변 음식점:', result.list);
        display_nearby_restaurants(result.list);
    });
}

/**
 * 사용 예시 6: 현재 위치 기반 검색
 */
function example_current_location_search() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            
            // 1. 현재 위치의 지역 정보 가져오기
            WeaverLocation.coords_to_region(lat, lng, function(region_result) {
                if (region_result.error) {
                    console.error('지역 정보 오류:', region_result.error);
                    return;
                }
                
                if (region_result.list.length > 0) {
                    var region = region_result.list[0];
                    var search_keyword = region.region_2depth_name + ' 맛집';
                    
                    // 2. 해당 지역의 맛집 검색
                    WeaverLocation.address_search(search_keyword, 10, 1, function(search_result) {
                        if (search_result.error) {
                            console.error('맛집 검색 오류:', search_result.error);
                            return;
                        }
                        
                        console.log('현재 위치 주변 맛집:', search_result.list);
                        display_nearby_restaurants(search_result.list);
                    });
                }
            });
        }, function(error) {
            console.error('위치 정보 오류:', error);
        });
    }
}

/**
 * 사용 예시 7: 검색 결과를 지도에 표시
 */
function example_search_and_display_map() {
    WeaverLocation.address_search('부산 해운대 카페', 15, 1, function(result) {
        if (result.error) {
            console.error('검색 오류:', result.error);
            return;
        }
        
        // 카카오맵에 마커 표시
        if (typeof kakao !== 'undefined' && kakao.maps) {
            display_places_on_map(result.list);
        }
        
        // 리스트로 표시
        display_places_list(result.list);
    });
}

/**
 * 검색 결과를 화면에 표시하는 함수
 */
function display_search_results(places) {
    var html = '<div class="search-results">';
    html += '<h3>검색 결과 (' + places.length + '개)</h3>';
    html += '<ul>';
    
    places.forEach(function(place) {
        html += '<li>';
        html += '<strong>' + (place.place_name || place.address_name) + '</strong><br>';
        html += '주소: ' + place.address_name + '<br>';
        if (place.road_address_name) {
            html += '도로명: ' + place.road_address_name + '<br>';
        }
        html += '좌표: ' + place.y + ', ' + place.x;
        html += '</li>';
    });
    
    html += '</ul>';
    html += '</div>';
    
    $('#search-results').html(html);
}

/**
 * 주변 맛집을 화면에 표시하는 함수
 */
function display_nearby_restaurants(restaurants) {
    var html = '<div class="nearby-restaurants">';
    html += '<h3>주변 맛집</h3>';
    html += '<div class="restaurant-grid">';
    
    restaurants.forEach(function(restaurant) {
        html += '<div class="restaurant-card">';
        html += '<h4>' + restaurant.place_name + '</h4>';
        html += '<p>' + restaurant.address_name + '</p>';
        if (restaurant.phone) {
            html += '<p>전화: ' + restaurant.phone + '</p>';
        }
        html += '<button onclick="show_on_map(' + restaurant.y + ',' + restaurant.x + ')">지도에서 보기</button>';
        html += '</div>';
    });
    
    html += '</div>';
    html += '</div>';
    
    $('#nearby-restaurants').html(html);
}

/**
 * 카카오맵에 장소들을 마커로 표시
 */
function display_places_on_map(places) {
    if (typeof kakao === 'undefined' || !kakao.maps) {
        console.error('카카오맵 SDK가 로드되지 않았습니다.');
        return;
    }
    
    var mapContainer = document.getElementById('map');
    if (!mapContainer) {
        console.error('지도 컨테이너를 찾을 수 없습니다.');
        return;
    }
    
    // 첫 번째 장소를 중심으로 지도 생성
    var center = new kakao.maps.LatLng(places[0].y, places[0].x);
    var mapOption = {
        center: center,
        level: 5
    };
    
    var map = new kakao.maps.Map(mapContainer, mapOption);
    
    // 각 장소에 마커 표시
    places.forEach(function(place) {
        var markerPosition = new kakao.maps.LatLng(place.y, place.x);
        var marker = new kakao.maps.Marker({
            position: markerPosition
        });
        
        marker.setMap(map);
        
        // 마커 클릭 시 정보창 표시
        var infowindow = new kakao.maps.InfoWindow({
            content: '<div style="padding:5px;">' + place.place_name + '</div>'
        });
        
        kakao.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });
    });
}

/**
 * 장소 리스트 표시
 */
function display_places_list(places) {
    var html = '<div class="places-list">';
    html += '<h3>장소 목록</h3>';
    
    places.forEach(function(place, index) {
        html += '<div class="place-item" data-index="' + index + '">';
        html += '<h4>' + place.place_name + '</h4>';
        html += '<p class="address">' + place.address_name + '</p>';
        if (place.road_address_name) {
            html += '<p class="road-address">' + place.road_address_name + '</p>';
        }
        if (place.phone) {
            html += '<p class="phone">' + place.phone + '</p>';
        }
        html += '<div class="coords">위도: ' + place.y + ', 경도: ' + place.x + '</div>';
        html += '</div>';
    });
    
    html += '</div>';
    
    $('#places-list').html(html);
}

/**
 * 특정 좌표를 지도에서 보기
 */
function show_on_map(lat, lng) {
    if (typeof kakao === 'undefined' || !kakao.maps) {
        alert('카카오맵을 사용할 수 없습니다.');
        return;
    }
    
    var mapContainer = document.getElementById('map');
    if (!mapContainer) {
        alert('지도 컨테이너를 찾을 수 없습니다.');
        return;
    }
    
    var center = new kakao.maps.LatLng(lat, lng);
    var mapOption = {
        center: center,
        level: 3
    };
    
    var map = new kakao.maps.Map(mapContainer, mapOption);
    
    var marker = new kakao.maps.Marker({
        position: center
    });
    
    marker.setMap(map);
}

/**
 * 단축 함수들 사용 예시
 */
function example_shortcut_functions() {
    // 간단한 주소 검색
    wv_address_search('영도구 맛집', function(result) {
        console.log('단축함수 검색 결과:', result);
    });
    
    // 간단한 좌표 변환
    wv_coords_to_region(35.0716, 129.0574, function(result) {
        console.log('단축함수 지역 변환 결과:', result);
    });
    
    // 간단한 주소 변환
    wv_coords_to_address(35.0716, 129.0574, function(result) {
        console.log('단축함수 주소 변환 결과:', result);
    });
    
    // 간단한 좌표 변환
    wv_address_to_coords('부산 영도구', function(result) {
        console.log('단축함수 좌표 변환 결과:', result);
    });
    
    // 간단한 카테고리 검색
    wv_category_search('CE7', function(result) {
        console.log('단축함수 카페 검색 결과:', result);
    });
}

/**
 * 에러 처리 예시
 */
function example_error_handling() {
    // 카카오 SDK 로드 확인
    if (!wv_check_kakao()) {
        alert('카카오맵 SDK가 로드되지 않았습니다. 스크립트 태그를 확인해주세요.');
        return;
    }
    
    WeaverLocation.address_search('', 10, 1, function(result) {
        if (result.error) {
            switch(true) {
                case result.error.includes('Kakao SDK not available'):
                    alert('카카오맵 SDK를 사용할 수 없습니다.');
                    break;
                case result.error.includes('Unsupported API type'):
                    alert('지원하지 않는 API 타입입니다.');
                    break;
                case result.error.includes('Kakao Places API Error'):
                    alert('카카오 Places API 오류가 발생했습니다.');
                    break;
                case result.error.includes('Kakao Geocoder API Error'):
                    alert('카카오 Geocoder API 오류가 발생했습니다.');
                    break;
                default:
                    alert('오류가 발생했습니다: ' + result.error);
            }
            return;
        }
        
        // 정상 처리
        console.log('검색 성공:', result);
    });
}
