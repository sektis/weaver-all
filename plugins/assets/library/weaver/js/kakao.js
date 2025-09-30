/**
 * 카카오맵 앱 바로 실행하기
 * 위도, 경도, 주소값으로 카카오맵 앱 열기
 */

// ============================================================================
// 1. 카카오맵 앱 URL Scheme
// ============================================================================

/**
 * 카카오맵 앱으로 특정 위치 보기
 * @param {string} name - 장소명
 * @param {number} lat - 위도
 * @param {number} lng - 경도
 */
function open_kakaomap_app(name, lat, lng) {
    var app_url = 'kakaomap://look?p=' + lat + ',' + lng;
    window.location.href = app_url;
}

/**
 * 카카오맵 앱으로 장소 검색
 * @param {string} query - 검색어 (주소 또는 장소명)
 */
function open_kakaomap_search(query) {
    var app_url = 'kakaomap://search?q=' + encodeURIComponent(query);
    window.location.href = app_url;
}

/**
 * 카카오맵 앱으로 길찾기
 * @param {string} dest_name - 목적지명
 * @param {number} dest_lat - 목적지 위도
 * @param {number} dest_lng - 목적지 경도
 * @param {string} transport - 이동수단 (CAR, PUBLICTRANSIT, WALK)
 */
function open_kakaomap_route(dest_name, dest_lat, dest_lng, transport) {
    transport = transport || 'CAR';
    var app_url = 'kakaomap://route?ep=' + dest_lat + ',' + dest_lng + '&by=' + transport;
    window.location.href = app_url;
}

// ============================================================================
// 2. 카카오맵 웹 링크 (앱이 없을 때 대비)
// ============================================================================

/**
 * 카카오맵 웹으로 위치 보기
 */
function open_kakaomap_web(name, lat, lng) {
    var web_url = 'https://map.kakao.com/link/map/' + encodeURIComponent(name) + ',' + lat + ',' + lng;
    window.open(web_url, '_blank');
}

/**
 * 카카오맵 웹으로 길찾기
 */
function open_kakaomap_web_route(dest_name, dest_lat, dest_lng) {
    var web_url = 'https://map.kakao.com/link/to/' + encodeURIComponent(dest_name) + ',' + dest_lat + ',' + dest_lng;
    window.open(web_url, '_blank');
}

/**
 * 카카오맵 웹으로 검색
 */
function open_kakaomap_web_search(query) {
    var web_url = 'https://map.kakao.com/link/search/' + encodeURIComponent(query);
    window.open(web_url, '_blank');
}

// ============================================================================
// 3. 앱/웹 자동 전환 (앱 설치 여부에 따라)
// ============================================================================

/**
 * 카카오맵 앱 실행 (없으면 웹으로 fallback)
 */
function open_kakaomap_smart(name, lat, lng) {
    var app_url = 'kakaomap://look?p=' + lat + ',' + lng;
    var web_url = 'https://map.kakao.com/link/map/' + encodeURIComponent(name) + ',' + lat + ',' + lng;

    // 앱 실행 시도
    window.location.href = app_url;

    // 1.5초 후 앱이 실행되지 않았으면 웹으로 이동
    setTimeout(function() {
        if (!document.hidden) {
            window.location.href = web_url;
        }
    }, 1500);
}

/**
 * 더 안정적인 앱/웹 전환 (iframe 사용)
 */
function open_kakaomap_with_fallback(name, lat, lng) {
    var app_url = 'kakaomap://look?p=' + lat + ',' + lng;
    var web_url = 'https://map.kakao.com/link/map/' + encodeURIComponent(name) + ',' + lat + ',' + lng;

    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = app_url;
    document.body.appendChild(iframe);

    setTimeout(function() {
        document.body.removeChild(iframe);
        if (!document.hidden && !document.webkitHidden) {
            window.location.href = web_url;
        }
    }, 1500);
}

// ============================================================================
// 4. 카카오내비 앱 실행
// ============================================================================

/**
 * 카카오내비 앱으로 길찾기
 */
function open_kakao_navi(dest_name, dest_lat, dest_lng) {
    var navi_url = 'kakaonavi://navigate?name=' + encodeURIComponent(dest_name) +
        '&coord_type=wgs84' +
        '&x=' + dest_lng +
        '&y=' + dest_lat;

    window.location.href = navi_url;

    // 앱이 없으면 카카오맵으로 fallback
    setTimeout(function() {
        if (!document.hidden) {
            open_kakaomap_web_route(dest_name, dest_lat, dest_lng);
        }
    }, 1500);
}

// ============================================================================
// 5. weaver 프로젝트용 함수
// ============================================================================

/**
 * weaver용 카카오맵 열기 함수
 */
function wv_open_kakaomap(options) {
    var defaults = {
        name: '목적지',
        lat: 0,
        lng: 0,
        address: '',
        mode: 'view',        // 'view', 'route', 'search', 'navi'
        transport: 'CAR',    // 'CAR', 'PUBLICTRANSIT', 'WALK'
        app_first: true,     // 앱 우선 실행 여부
        fallback_web: true   // 웹 fallback 여부
    };

    var opts = $.extend({}, defaults, options);

    // 검색 모드
    if (opts.mode === 'search') {
        var query = opts.address || opts.name;
        if (opts.app_first) {
            open_kakaomap_search(query);
        } else {
            open_kakaomap_web_search(query);
        }
        return;
    }

    // 내비 모드
    if (opts.mode === 'navi') {
        open_kakao_navi(opts.name, opts.lat, opts.lng);
        return;
    }

    // 길찾기 모드
    if (opts.mode === 'route') {
        if (opts.app_first) {
            open_kakaomap_route(opts.name, opts.lat, opts.lng, opts.transport);
        } else {
            open_kakaomap_web_route(opts.name, opts.lat, opts.lng);
        }
        return;
    }

    // 보기 모드 (기본)
    if (opts.app_first && opts.fallback_web) {
        open_kakaomap_with_fallback(opts.name, opts.lat, opts.lng);
    } else if (opts.app_first) {
        open_kakaomap_app(opts.name, opts.lat, opts.lng);
    } else {
        open_kakaomap_web(opts.name, opts.lat, opts.lng);
    }
}

/**
 * jQuery 확장 함수
 */
$.fn.wv_kakaomap = function(options) {
    this.click(function(e) {
        e.preventDefault();

        var $this = $(this);
        var opts = $.extend({}, options, {
            name: $this.data('name') || options.name,
            lat: $this.data('lat') || options.lat,
            lng: $this.data('lng') || options.lng,
            address: $this.data('address') || options.address,
            mode: $this.data('mode') || options.mode
        });

        wv_open_kakaomap(opts);
    });

    return this;
};

// ============================================================================
// 6. 다양한 지도 앱 선택 팝업
// ============================================================================

/**
 * 카카오맵/구글맵/네이버지도 선택 팝업
 */
function show_map_options(name, lat, lng, address) {
    var modal = `
        <div class="map-options-modal" style="position:fixed;bottom:0;left:0;right:0;background:white;box-shadow:0 -2px 10px rgba(0,0,0,0.2);z-index:9999;">
            <div class="map-option" data-app="kakaomap" style="padding:15px;text-align:center;border-bottom:1px solid #eee;">
                🗺️ 카카오맵으로 보기
            </div>
            <div class="map-option" data-app="kakaonavi" style="padding:15px;text-align:center;border-bottom:1px solid #eee;">
                🚗 카카오내비로 길찾기
            </div>
            <div class="map-option" data-app="navermap" style="padding:15px;text-align:center;border-bottom:1px solid #eee;">
                📍 네이버지도로 보기
            </div>
            <div class="map-option" data-app="googlemap" style="padding:15px;text-align:center;border-bottom:1px solid #eee;">
                🌏 구글맵으로 보기
            </div>
            <div class="map-option cancel" style="padding:15px;text-align:center;color:#dc3545;font-weight:bold;">
                취소
            </div>
        </div>
    `;

    $('body').append(modal);

    $('.map-option[data-app]').click(function() {
        var app = $(this).data('app');

        switch(app) {
            case 'kakaomap':
                open_kakaomap_with_fallback(name, lat, lng);
                break;
            case 'kakaonavi':
                open_kakao_navi(name, lat, lng);
                break;
            case 'navermap':
                open_naver_map(name, lat, lng);
                break;
            case 'googlemap':
                open_google_map(lat, lng);
                break;
        }

        $('.map-options-modal').remove();
    });

    $('.map-option.cancel').click(function() {
        $('.map-options-modal').remove();
    });
}

// 네이버지도 열기
function open_naver_map(name, lat, lng) {
    var naver_url = 'nmap://place?lat=' + lat + '&lng=' + lng + '&name=' + encodeURIComponent(name);
    var naver_web = 'https://map.naver.com/v5/search/' + encodeURIComponent(name);

    window.location.href = naver_url;

    setTimeout(function() {
        if (!document.hidden) {
            window.location.href = naver_web;
        }
    }, 1500);
}

// 구글맵 열기
function open_google_map(lat, lng) {
    var google_url = 'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng;
    window.open(google_url, '_blank');
}

// ============================================================================
// 사용 예시
// ============================================================================

$(document).ready(function() {

    // 예시 1: 간단한 카카오맵 열기
    $('#open-map-1').click(function(e) {
        e.preventDefault();
        open_kakaomap_app('스타벅스 강남점', 37.497942, 127.027621);
    });

    // 예시 2: 앱/웹 자동 전환
    $('#open-map-2').click(function(e) {
        e.preventDefault();
        open_kakaomap_with_fallback('스타벅스 강남점', 37.497942, 127.027621);
    });

    // 예시 3: 길찾기
    $('#open-route').click(function(e) {
        e.preventDefault();
        open_kakaomap_route('스타벅스 강남점', 37.497942, 127.027621, 'CAR');
    });

    // 예시 4: 카카오내비
    $('#open-navi').click(function(e) {
        e.preventDefault();
        open_kakao_navi('스타벅스 강남점', 37.497942, 127.027621);
    });

    // 예시 5: weaver 스타일
    $('#open-map-3').click(function(e) {
        e.preventDefault();
        wv_open_kakaomap({
            name: '스타벅스 강남점',
            lat: 37.497942,
            lng: 127.027621,
            mode: 'view',
            app_first: true,
            fallback_web: true
        });
    });

    // 예시 6: jQuery 확장 함수
    $('.kakaomap-link').wv_kakaomap({
        mode: 'view',
        app_first: true,
        fallback_web: true
    });

    // 예시 7: 지도 앱 선택 팝업
    $('#show-map-options').click(function(e) {
        e.preventDefault();
        show_map_options('스타벅스 강남점', 37.497942, 127.027621, '서울 강남구 테헤란로 123');
    });

});