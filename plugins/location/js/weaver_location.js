/**
 * Weaver Location JavaScript Library
 * 카카오맵 JavaScript SDK를 사용한 위치 서비스
 */

window.WeaverLocation = (function() {

    var config = {
        kakao_js_apikey: 'f1d7df6827c9cbc8a39c19ac0baac410'
    };

    /**
     * 카카오맵 JavaScript SDK 사용
     * @param {string} api - API 타입
     * @param {object} query_array - 쿼리 파라미터
     * @param {function} callback - 콜백 함수
     */
    function kakao_api_sdk(api, query_array, callback) {
        if (typeof kakao === 'undefined' || !kakao.maps) {
            console.error('Kakao Maps SDK not loaded');
            if (callback) callback({error: 'Kakao SDK not available'});
            return;
        }

        switch(api) {
            case 'keyword':
                keyword_search(query_array, callback);
                break;

            case 'coord2regioncode':
                coord_to_region_code(query_array, callback);
                break;

            case 'coord2address':
                coord_to_address(query_array, callback);
                break;

            case 'address':
                address_to_coord(query_array, callback);
                break;

            default:
                console.error('Unsupported API type:', api);
                if (callback) callback({error: 'Unsupported API type'});
        }
    }

    /**
     * 키워드 검색 (Places 서비스)
     */
    function keyword_search(query_array, callback) {
        var places = new kakao.maps.services.Places();

        var options = {
            page: query_array.page || 1,
            size: query_array.size || 10
        };

        // 카테고리 검색인 경우
        if (query_array.category_group_code) {
            places.categorySearch(query_array.category_group_code, function(result, status, pagination) {
                var response = format_places_response(result, status, pagination);
                if (callback) callback(response);
            }, options);
        } else {
            // 키워드 검색
            places.keywordSearch(query_array.query, function(result, status, pagination) {
                var response = format_places_response(result, status, pagination);
                if (callback) callback(response);
            }, options);
        }
    }

    /**
     * 좌표를 지역코드로 변환 (Geocoder 서비스)
     */
    function coord_to_region_code(query_array, callback) {
        var geocoder = new kakao.maps.services.Geocoder();

        geocoder.coord2RegionCode(query_array.x, query_array.y, function(result, status) {
            var response = format_geocoder_response(result, status, 'region');
            if (callback) callback(response);
        });
    }

    /**
     * 좌표를 주소로 변환 (Geocoder 서비스)
     */
    function coord_to_address(query_array, callback) {
        var geocoder = new kakao.maps.services.Geocoder();

        geocoder.coord2Address(query_array.x, query_array.y, function(result, status) {
            var response = format_geocoder_response(result, status, 'address');
            if (callback) callback(response);
        });
    }

    /**
     * 주소를 좌표로 변환 (Geocoder 서비스)
     */
    function address_to_coord(query_array, callback) {
        var geocoder = new kakao.maps.services.Geocoder();

        geocoder.addressSearch(query_array.query, function(result, status) {
            var response = format_geocoder_response(result, status, 'coord');
            if (callback) callback(response);
        });
    }

    /**
     * Places 서비스 응답 포맷팅
     */
    function format_places_response(result, status, pagination) {
        var response = {};

        if (status === kakao.maps.services.Status.OK) {
            // 결과 데이터 포맷팅 (PHP와 동일한 구조로)
            response.list = result.map(function(place) {
                return {
                    address_name: place.address_name || '',
                    road_address_name: place.road_address_name || '',
                    x: place.x || '',
                    y: place.y || '',
                    // place_name: place.place_name || '',
                    // category_name: place.category_name || '',
                    // category_group_name: place.category_group_name || '',
                    // phone: place.phone || '',
                    // place_url: place.place_url || '',
                    // distance: place.distance || '',
                    // id: place.id || ''
                };
            });

            response.total_count = pagination ? pagination.totalCount : result.length;
            response.is_end = pagination ? !pagination.hasNextPage : true;
        } else {
            response.error = 'Kakao Places API Error: ' + status;
        }

        return response;
    }

    /**
     * Geocoder 서비스 응답 포맷팅
     */
    function format_geocoder_response(result, status, type) {
        var response = {};

        if (status === kakao.maps.services.Status.OK) {
            if (type === 'region') {
                // 좌표 → 지역코드 변환 결과
                response.list = result.map(function(item) {
                    return {
                        region_1depth_name: item.region_1depth_name || '',
                        region_2depth_name: item.region_2depth_name || '',
                        region_3depth_name: item.region_3depth_name || '',
                        region_4depth_name: item.region_4depth_name || '',
                        // code: item.code || '',
                        // region_type: item.region_type || ''
                    };
                });
            } else if (type === 'address') {
                // 좌표 → 주소 변환 결과
                response.list = result.map(function(item) {
                    var addr = item.address || {};
                    var road_addr = item.road_address || {};

                    return {
                        address_name: addr.address_name || '',
                        region_1depth_name: addr.region_1depth_name || '',
                        region_2depth_name: addr.region_2depth_name || '',
                        region_3depth_name: addr.region_3depth_name || '',
                        mountain_yn: addr.mountain_yn || '',
                        main_address_no: addr.main_address_no || '',
                        sub_address_no: addr.sub_address_no || '',
                        road_address_name: road_addr.address_name || '',
                        road_name: road_addr.road_name || '',
                        building_name: road_addr.building_name || ''
                    };
                });
            } else if (type === 'coord') {
                // 주소 → 좌표 변환 결과
                response.list = result.map(function(item) {
                    return {
                        address_name: item.address_name || '',
                        road_address_name: item.road_address ? item.road_address.address_name : '',
                        x: item.x || '',
                        y: item.y || '',
                        address_type: item.address_type || ''
                    };
                });
            }

            response.total_count = result.length;
            response.is_end = true;
        } else {
            response.error = 'Kakao Geocoder API Error: ' + status;
        }

        return response;
    }

    /**
     * 주소/키워드 검색
     * @param {string} address - 검색할 주소 또는 키워드
     * @param {number} size - 결과 개수 (기본값: 10)
     * @param {number} page - 페이지 번호 (기본값: 1)
     * @param {function} callback - 콜백 함수
     */
    function address_search(address, size, page, callback) {
        size = size || 10;
        page = page || 1;

        var query_array = {
            'query': address,
            'size': size,
            'page': page
        };

        kakao_api_sdk('keyword', query_array, callback);
    }

    /**
     * 좌표를 지역코드로 변환
     * @param {number} lat - 위도
     * @param {number} lng - 경도
     * @param {function} callback - 콜백 함수
     */
    function coords_to_region(lat, lng, callback) {
        var query_array = {
            'x': lng,
            'y': lat
        };

        kakao_api_sdk('coord2regioncode', query_array, callback);
    }

    /**
     * 좌표를 주소로 변환
     * @param {number} lat - 위도
     * @param {number} lng - 경도
     * @param {function} callback - 콜백 함수
     */
    function coords_to_address(lat, lng, callback) {
        var query_array = {
            'x': lng,
            'y': lat
        };

        kakao_api_sdk('coord2address', query_array, callback);
    }

    /**
     * 주소를 좌표로 변환
     * @param {string} address - 주소
     * @param {function} callback - 콜백 함수
     */
    function address_to_coords(address, callback) {
        var query_array = {
            'query': address
        };

        kakao_api_sdk('address', query_array, callback);
    }

    /**
     * 카테고리로 장소 검색
     * @param {string} category_code - 카테고리 그룹 코드 (예: FD6-음식점, CE7-카페)
     * @param {number} lat - 중심 위도 (선택)
     * @param {number} lng - 중심 경도 (선택)
     * @param {number} radius - 반경(미터, 선택)
     * @param {function} callback - 콜백 함수
     */
    function category_search(category_code, lat, lng, radius, callback) {
        // 파라미터 조정 (callback이 다른 위치에 올 수 있음)
        if (typeof lat === 'function') {
            callback = lat;
            lat = null;
            lng = null;
            radius = null;
        } else if (typeof lng === 'function') {
            callback = lng;
            lng = null;
            radius = null;
        } else if (typeof radius === 'function') {
            callback = radius;
            radius = null;
        }

        var query_array = {
            'category_group_code': category_code
        };

        // 위치 기반 검색인 경우
        if (lat && lng) {
            query_array.x = lng;
            query_array.y = lat;
            if (radius) {
                query_array.radius = radius;
            }
        }

        kakao_api_sdk('keyword', query_array, callback);
    }

    /**
     * 설정 업데이트
     * @param {object} new_config - 새로운 설정
     */
    function set_config(new_config) {
        config = Object.assign(config, new_config);
    }

    /**
     * 현재 설정 반환
     */
    function get_config() {
        return config;
    }

    /**
     * 카카오 SDK 로드 상태 확인
     */
    function is_kakao_loaded() {
        return typeof kakao !== 'undefined' && kakao.maps && kakao.maps.services;
    }

    // Public API
    return {
        // 메인 API 함수들
        address_search: address_search,
        coords_to_region: coords_to_region,
        coords_to_address: coords_to_address,
        address_to_coords: address_to_coords,
        category_search: category_search,

        // 내부 함수들 (필요시 직접 접근)
        kakao_api_sdk: kakao_api_sdk,

        // 유틸리티 함수들
        set_config: set_config,
        get_config: get_config,
        is_kakao_loaded: is_kakao_loaded
    };

})();

/**
 * 배열에서 특정 키들만 추출 (JavaScript 버전)
 * PHP의 wv_extract_keys() 함수와 동일한 기능
 */
function wv_extract_keys(array, keys) {
    return array.map(function(item) {
        var result = {};
        keys.forEach(function(key) {
            result[key] = item[key] || '';
        });
        return result;
    });
}

/**
 * 사용 예시 및 단축 함수들
 */

// 주소 검색 단축 함수
function wv_address_search(address, callback, size, page) {
    WeaverLocation.address_search(address, size, page, callback);
}

// 좌표→지역 변환 단축 함수  
function wv_coords_to_region(lat, lng, callback) {
    WeaverLocation.coords_to_region(lat, lng, callback);
}
function wv_address_result_to_region_merge(address_result, callback) {
    WeaverLocation.coords_to_region(address_result['y'], address_result['x'], function (response) {
        var result = Object.assign({}, response.list[0], address_result)
        callback(result)
    });
}

// 좌표→주소 변환 단축 함수
function wv_coords_to_address(lat, lng, callback) {
    WeaverLocation.coords_to_address(lat, lng, callback);
}

// 주소→좌표 변환 단축 함수
function wv_address_to_coords(address, callback) {
    WeaverLocation.address_to_coords(address, callback);
}

// 카테고리 검색 단축 함수
function wv_category_search(category_code, callback, lat, lng, radius) {
    WeaverLocation.category_search(category_code, lat, lng, radius, callback);
}

// 카카오 SDK 로드 확인 함수
function wv_check_kakao() {
    return WeaverLocation.is_kakao_loaded();
}

// 설정 초기화 함수 (카카오 API 키 설정)
function wv_location_init(api_key) {
    if (api_key) {
        WeaverLocation.set_config({
            kakao_js_apikey: api_key
        });
    }

    // 카카오 SDK 로드 확인
    if (!WeaverLocation.is_kakao_loaded()) {
        console.warn('카카오맵 SDK가 로드되지 않았습니다. 스크립트 태그를 확인해주세요.');
        console.log('필요한 스크립트: <script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_API_KEY&libraries=services"></script>');
    }
}
function wv_trans_sido(text) {
    // 긴 공식명 → 2글자 약칭
    const map = {
        '서울특별시': '서울',
        '부산광역시': '부산',
        '대구광역시': '대구',
        '인천광역시': '인천',
        '광주광역시': '광주',
        '대전광역시': '대전',
        '울산광역시': '울산',
        '세종특별자치시': '세종',

        '경기도': '경기',
        '강원특별자치도': '강원',
        '강원도': '강원',
        '충청북도': '충북',
        '충청남도': '충남',
        '전라북도': '전북',
        '전라남도': '전남',
        '경상북도': '경북',
        '경상남도': '경남',
        '제주특별자치도': '제주',
        '제주도': '제주',

        // 약식 표기 대응
        '전북특별자치도': '전북',
        '서울시': '서울', '부산시': '부산', '대구시': '대구', '인천시': '인천',
        '광주시': '광주', '대전시': '대전', '울산시': '울산', '세종시': '세종'
    };

    // 긴 키부터 정렬
    const keys = Object.keys(map).sort((a, b) => b.length - a.length);

    // 정규식 패턴 생성
    const pattern = new RegExp(keys.map(k => k.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|'), 'gu');

    // 치환
    return text.replace(pattern, match => map[match] || match);
}
function wv_get_current_location(callback) {
    if (!navigator.geolocation) {
        alert("이 브라우저에서는 위치 정보를 지원하지 않습니다.");
        return;
    }


    navigator.geolocation.getCurrentPosition(
        function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            if(lat && lng){

                wv_coords_to_region(lat,lng,function (result) {
                    callback(Object.assign({'lat': lat, 'lng': lng}, result.list[0]));
                })
            }
        },
        function (error) {

        },
        {
            enableHighAccuracy: false,
            timeout: 10000,
            maximumAge: 0
        }
    );
}