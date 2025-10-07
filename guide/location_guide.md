# Location 플러그인 개발 가이드

## 📌 플러그인 개요

Location 플러그인은 **카카오맵 API**를 활용한 위치 기반 서비스를 제공하는 플러그인입니다.

**주요 기능:**
- 주소/키워드 검색
- 좌표 ↔ 주소 변환
- 지역코드 조회 및 선택
- 카카오맵 렌더링
- 마커 표시 및 클러스터링

---

## 🗂️ 플러그인 구조

### 디렉토리 구조
```
plugins/location/
├── Location.php                (메인 클래스)
├── RegionApi.php              (지역 API)
├── plugin.php                 (플러그인 로더)
├── lib/
│   └── func.php              (유틸 함수)
├── js/
│   ├── weaver_location.js    (JS 라이브러리)
│   ├── location-examples.js  (사용 예시)
│   ├── location-demo.html    (데모 페이지)
│   └── README.md             (JS 문서)
└── theme/
    └── basic/
        └── plugins/
            └── widget/
                └── skin/
                    └── pc/
                        └── location/
                            ├── map/          (지도 위젯)
                            ├── address/      (주소 선택 위젯)
                            ├── region/       (지역 선택 위젯)
                            └── search_address/ (주소 검색 위젯)
```

---

## 🏗️ 핵심 클래스

### Location (메인 클래스)

```php
class Location extends Plugin {
    // Widget 렌더링
    public function render_widget($skin_type, $data = array())
    
    // API 엔드포인트
    public function ajax_url()
    
    // 주소 검색
    public function api_search($query)
    
    // 좌표 → 주소
    public function coord_to_address($lat, $lng)
}
```

### RegionApi (지역 API)

```php
class RegionApi {
    // Depth 1 지역 목록 (시도)
    protected function depth1($sort='name')
    
    // Depth 2 지역 목록 (시군구)
    protected function depth2($d1, $sort='name')
    
    // Depth 3 지역 목록 (읍면동)
    protected function depth3($d1, $d2, $sort='name')
    
    // 지역코드로 검색
    protected function code($cd)
    
    // 지역명 검색
    protected function search($q, $d1='', $d2='', $lim=50, $sort='name')
}
```

---

## 🎨 Widget 시스템

### Widget 렌더링

```php
// 기본 사용법
wv_widget('location/{widget_type}', $data);

// 또는
wv()->location->render_widget('{widget_type}', $data);
```

### 제공 Widget 목록

#### 1. **map** - 카카오맵 위젯

**파일**: `plugins/location/theme/basic/plugins/widget/skin/pc/location/map/skin.php`

**기능:**
- 카카오맵 렌더링
- 마커 클러스터링
- 현재 위치 버튼
- 마커 클릭 이벤트

**옵션:**
```php
$data = array(
    'map_id' => 'my-map',              // 맵 ID (선택)
    'initial_level' => 8,               // 초기 줌 레벨 (1-14)
    'min_level' => 1,                   // 최소 줌 레벨
    'max_level' => 14,                  // 최대 줌 레벨
    'markers' => array(                 // 마커 데이터
        array(
            'lat' => 37.5665,
            'lng' => 126.9780,
            'title' => '마커 제목',
            'wr_id' => 123,
            'category_icon' => 'icon_url.png',
            'category_icon_wrap' => 'wrapper_bg.png'
        )
    )
);

echo wv_widget('location/map', $data);
```

**발생 이벤트:**
- `wv_location_map_changed`: 지도 이동/줌 변경 시
- `wv_location_map_marker_clicked`: 마커 클릭 시

---

#### 2. **address** - 주소 선택 위젯

**파일**: `plugins/location/theme/basic/plugins/widget/skin/pc/location/address/skin.php`

**기능:**
- 주소 검색
- 카카오맵에서 위치 선택
- 마커 드래그로 위치 조정

**옵션:**
```php
$data = array(
    'lat' => 37.5665,                   // 초기 위도
    'lng' => 126.9780,                  // 초기 경도
    'address_name' => '서울시 중구',    // 초기 주소
    'use_search_address' => true        // 검색 기능 사용 여부
);

echo wv_widget('location/address', $data);
```

**발생 이벤트:**
```javascript
// wv_location_address_changed 이벤트 데이터
{
    lat: 37.5665,
    lng: 126.9780,
    address_name: '서울특별시 중구 을지로',
    road_address_name: '서울특별시 중구 을지로 100',
    region_1depth_name: '서울특별시',
    region_2depth_name: '중구',
    region_3depth_name: '을지로동'
}
```

---

#### 3. **region/depth** - 지역 선택 위젯

**파일**: `plugins/location/theme/basic/plugins/widget/skin/pc/location/region/depth/skin.php`

**기능:**
- 시도/시군구/읍면동 3단계 선택
- 다중 선택 지원
- 선택 제한 (max_count)

**옵션:**
```php
$data = array(
    'multiple' => true,                 // 다중 선택 허용
    'max_count' => 3,                   // 최대 선택 개수
    'selected' => array(                // 초기 선택값
        array(
            'region_1depth_name' => '서울특별시',
            'region_2depth_name' => '강남구',
            'region_3depth_name' => '역삼동'
        )
    )
);

echo wv_widget('location/region/depth', $data);
```

---

#### 4. **search_address** - 주소 검색 위젯

**파일**: `plugins/location/theme/basic/plugins/widget/skin/pc/location/search_address/skin.php`

**기능:**
- 키워드/주소 검색
- 검색 결과 목록 표시
- 자동완성 지원

**사용:**
```php
echo wv_widget('location/search_address', array());
```

---

## 🌐 JavaScript API

### 라이브러리 로드

```html
<!-- jQuery (필수) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- 카카오맵 SDK (필수) -->
<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_API_KEY&libraries=services"></script>

<!-- Weaver Location 라이브러리 -->
<script src="/plugin/weaver/plugins/location/js/weaver_location.js"></script>
```

### 초기화

```javascript
$(document).ready(function() {
    wv_location_init();
    
    // SDK 로드 확인
    if (wv_check_kakao()) {
        console.log('카카오맵 SDK 사용 준비 완료');
    }
});
```

### 주요 API 메서드

#### 1. 주소/키워드 검색

```javascript
WeaverLocation.address_search(keyword, size, page, callback);

// 예시
WeaverLocation.address_search('영도구 맛집', 10, 1, function(result) {
    if (result.error) {
        console.error('오류:', result.error);
        return;
    }
    
    console.log('검색 결과:', result.list);
    console.log('총 개수:', result.total_count);
});

// 단축 함수
wv_address_search('부산 카페', function(result) {
    console.log(result);
});
```

#### 2. 좌표 → 지역코드 변환

```javascript
WeaverLocation.coords_to_region(lat, lng, callback);

// 예시
WeaverLocation.coords_to_region(35.0716, 129.0574, function(result) {
    if (result.list.length > 0) {
        var region = result.list[0];
        console.log('시도:', region.region_1depth_name);
        console.log('시군구:', region.region_2depth_name);
        console.log('읍면동:', region.region_3depth_name);
    }
});

// 단축 함수
wv_coords_to_region(35.0716, 129.0574, function(result) {
    console.log('지역 정보:', result);
});
```

#### 3. 좌표 ↔ 주소 변환

```javascript
// 좌표 → 주소
WeaverLocation.coords_to_address(lat, lng, callback);

WeaverLocation.coords_to_address(35.0716, 129.0574, function(result) {
    if (result.list.length > 0) {
        var addr = result.list[0];
        console.log('지번주소:', addr.address_name);
        console.log('도로명주소:', addr.road_address_name);
    }
});

// 주소 → 좌표
WeaverLocation.address_to_coords(address, callback);

WeaverLocation.address_to_coords('부산 영도구 동삼동', function(result) {
    if (result.list.length > 0) {
        var coord = result.list[0];
        console.log('위도:', coord.y);
        console.log('경도:', coord.x);
    }
});

// 단축 함수
wv_coords_to_address(35.0716, 129.0574, function(result) {
    console.log('주소:', result);
});

wv_address_to_coords('부산 영도구', function(result) {
    console.log('좌표:', result);
});
```

#### 4. 카테고리 검색

```javascript
WeaverLocation.category_search(category_code, callback);

// 카테고리 코드
// CE7: 카페
// FD6: 음식점
// MT1: 대형마트
// CS2: 편의점
// PS3: 어린이집, 유치원
// SC4: 학교
// AC5: 학원
// PK6: 주차장
// OL7: 주유소, 충전소
// SW8: 지하철역
// BK9: 은행
// CT1: 문화시설
// AG2: 중개업소
// PO3: 공공기관
// AT4: 관광명소
// AD5: 숙박
// HP8: 병원
// PM9: 약국

// 예시 - 카페 검색
WeaverLocation.category_search('CE7', function(result) {
    console.log('주변 카페:', result.list);
});

// 단축 함수
wv_category_search('FD6', function(result) {
    console.log('주변 음식점:', result);
});
```

---

## 🔗 PHP API

### Widget 렌더링

```php
// Location 플러그인 인스턴스
$location = wv()->location;

// Widget 렌더링
echo $location->render_widget('map', array(
    'initial_level' => 5,
    'markers' => $marker_data
));
```

### 주소 검색

```php
$result = wv()->location->api_search('영도구 맛집');

// 결과
array(
    'ok' => true,
    'list' => array(
        array(
            'address_name' => '부산 영도구 동삼동',
            'road_address_name' => '부산 영도구 동삼로 123',
            'lat' => '35.0716',
            'lng' => '129.0574',
            'place_name' => '맛있는집'
        )
    ),
    'total_count' => 10
);
```

### 좌표 → 주소 변환

```php
$result = wv()->location->coord_to_address(35.0716, 129.0574);

// 결과
array(
    'ok' => true,
    'list' => array(
        array(
            'address_name' => '부산 영도구 동삼동',
            'road_address_name' => '부산 영도구 동삼로 123',
            'region_1depth_name' => '부산광역시',
            'region_2depth_name' => '영도구',
            'region_3depth_name' => '동삼동'
        )
    )
);
```

---

## 🎯 실전 활용 예시

### 1. Store Manager와 연동

**주소 입력 폼:**
```php
// plugins/store_manager/theme/basic/pc/location/form/address.php

// Location 플러그인 address 위젯 사용
$address_skin_data = array(
    'lat' => $row['lat'],
    'lng' => $row['lng'],
    'address_name' => $row['address_name'],
    'use_search_address' => true
);

echo wv_widget('location/address', $address_skin_data);

// Hidden inputs for form submission
?>
<input type="hidden" name="location[lat]" class="location-lat">
<input type="hidden" name="location[lng]" class="location-lng">
<input type="hidden" name="location[address_name]" class="location-address-name">
<input type="hidden" name="location[region_1depth_name]" class="location-region-1depth-name">
<input type="hidden" name="location[region_2depth_name]" class="location-region-2depth-name">
<input type="hidden" name="location[region_3depth_name]" class="location-region-3depth-name">

<script>
$(document).ready(function(){
    // 주소 변경 이벤트 리스닝
    $(document).on('wv_location_address_changed', function(event, data) {
        $('.location-lat').val(data.lat);
        $('.location-lng').val(data.lng);
        $('.location-address-name').val(data.address_name);
        $('.location-region-1depth-name').val(data.region_1depth_name);
        $('.location-region-2depth-name').val(data.region_2depth_name);
        $('.location-region-3depth-name').val(data.region_3depth_name);
    });
});
</script>
```

---

### 2. 매장 목록 지도 표시

```php
// 매장 목록 조회
$stores = wv()->store_manager->made('sub01_01')->get_list(array(
    'where' => "wr_is_comment = 0",
    'limit' => 100
));

// 마커 데이터 생성
$markers = array();
foreach($stores['list'] as $store){
    if($store->location->lat && $store->location->lng){
        $markers[] = array(
            'lat' => $store->location->lat,
            'lng' => $store->location->lng,
            'title' => $store->store->name,
            'wr_id' => $store->wr_id,
            'category_icon' => $store->store->category_icon,
            'category_icon_wrap' => $store->store->category_icon_wrap
        );
    }
}

// 지도 렌더링
echo wv_widget('location/map', array(
    'markers' => $markers,
    'initial_level' => 8
));
```

**마커 클릭 이벤트 처리:**
```javascript
$(document).on('wv_location_map_marker_clicked', function(event, markerData) {
    var wr_id = markerData.wr_id;
    
    // 매장 상세 정보 표시
    $.ajax({
        url: '/plugin/weaver/plugins/store_manager/ajax.php',
        data: {
            action: 'get_store_info',
            wr_id: wr_id
        },
        success: function(response) {
            // 정보창 표시
            showStoreInfo(response);
        }
    });
});
```

---

### 3. 지역 선택 필터

```php
// 지역 선택 위젯
echo wv_widget('location/region/depth', array(
    'multiple' => true,
    'max_count' => 3
));
```

**선택 이벤트 처리:**
```javascript
$(document).on('wv_location_region_changed', function(event, regions) {
    console.log('선택된 지역:', regions);
    
    // 매장 목록 필터링
    var region_filter = regions.map(function(r) {
        return r.region_1depth_name + ' ' + r.region_2depth_name;
    }).join(',');
    
    // 목록 새로고침
    location.href = '?region=' + encodeURIComponent(region_filter);
});
```

---

### 4. 현재 위치 기반 검색

```javascript
// 현재 위치 가져오기
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        
        // 1. 현재 위치의 지역 정보 가져오기
        WeaverLocation.coords_to_region(lat, lng, function(region_result) {
            if (region_result.list.length > 0) {
                var region = region_result.list[0];
                var keyword = region.region_2depth_name + ' 맛집';
                
                // 2. 해당 지역 맛집 검색
                WeaverLocation.address_search(keyword, 10, 1, function(result) {
                    console.log('주변 맛집:', result.list);
                    displayStores(result.list);
                });
            }
        });
    });
}
```

---

## 📡 이벤트 시스템

### 발생 이벤트

| 이벤트명 | 발생 시점 | 데이터 |
|---------|----------|--------|
| `wv_location_address_changed` | 주소 선택 시 | lat, lng, address_name, region_*_name |
| `wv_location_map_changed` | 지도 이동/줌 변경 | center: {lat, lng}, bounds, level |
| `wv_location_map_marker_clicked` | 마커 클릭 시 | lat, lng, title, wr_id, 기타 마커 데이터 |
| `wv_location_region_changed` | 지역 선택 시 | 선택된 지역 배열 |

### 이벤트 리스닝

```javascript
// jQuery 이벤트 리스닝
$(document).on('wv_location_address_changed', function(event, data) {
    console.log('주소 변경:', data);
});

// 또는 CustomEvent 리스닝
document.addEventListener('wv_location_address_changed', function(event) {
    console.log('주소 변경:', event.detail);
});
```

### 이벤트 발생

```javascript
// jQuery 방식
$(document).trigger('wv_location_address_changed', [data]);

// CustomEvent 방식
var event = new CustomEvent('wv_location_address_changed', {
    detail: data,
    bubbles: true
});
document.dispatchEvent(event);
```

---

## 🛠️ RegionApi 사용

### AJAX 엔드포인트

**URL**: `/plugin/weaver/plugins/location/region_api.php`

### API 액션

#### 1. depth1 - 시도 목록

```javascript
$.get('/plugin/weaver/plugins/location/region_api.php', {
    a: 'depth1',
    sort: 'code'  // 'code' 또는 'name'
}, function(result) {
    console.log('시도 목록:', result.depth1);
});
```

**응답:**
```json
{
    "ok": true,
    "depth1": ["서울특별시", "부산광역시", "대구광역시", ...],
    "sorted_by": "code"
}
```

#### 2. depth2 - 시군구 목록

```javascript
$.get('/plugin/weaver/plugins/location/region_api.php', {
    a: 'depth2',
    d1: '서울특별시',
    sort: 'code'
}, function(result) {
    console.log('시군구 목록:', result.depth2);
});
```

**응답:**
```json
{
    "ok": true,
    "depth1": "서울특별시",
    "depth2": [
        {"code": "1111000000", "name": "전체"},
        {"code": "1111010100", "name": "종로구"},
        {"code": "1111010200", "name": "중구"},
        ...
    ],
    "sorted_by": "code"
}
```

#### 3. depth3 - 읍면동 목록

```javascript
$.get('/plugin/weaver/plugins/location/region_api.php', {
    a: 'depth3',
    d1: '서울특별시',
    d2: '강남구',
    sort: 'code'
}, function(result) {
    console.log('읍면동 목록:', result.depth3);
});
```

#### 4. code - 지역코드로 조회

```javascript
$.get('/plugin/weaver/plugins/location/region_api.php', {
    a: 'code',
    code: '1168010100'
}, function(result) {
    console.log('지역 정보:', result.region);
});
```

#### 5. search - 지역명 검색

```javascript
$.get('/plugin/weaver/plugins/location/region_api.php', {
    a: 'search',
    q: '개포',
    d1: '서울특별시',
    d2: '강남구',
    limit: 50,
    sort: 'code'
}, function(result) {
    console.log('검색 결과:', result.list);
});
```

---

## ⚙️ 설정 및 초기화

### 카카오맵 API 키 설정

```javascript
// config 객체에서 설정
var config = {
    kakao_js_apikey: 'YOUR_API_KEY'
};

// 또는 초기화 시 설정
wv_location_init('YOUR_API_KEY');
```

### SDK 로드 확인

```javascript
if (wv_check_kakao()) {
    console.log('카카오맵 SDK 로드 완료');
} else {
    console.error('카카오맵 SDK 로드 실패');
}
```

---

## 📝 개발 시 주의사항

### 1. 카카오맵 SDK 필수

```html
<!-- 반드시 카카오맵 SDK를 먼저 로드 -->
<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_KEY&libraries=services"></script>
```

### 2. HTTPS 환경 권장

위치 정보 사용 시 HTTPS 환경이 필요합니다.

### 3. 도메인 등록

카카오 개발자센터에서 사용할 도메인을 등록해야 합니다.

### 4. 에러 처리

```javascript
WeaverLocation.address_search('검색어', 10, 1, function(result) {
    if (result.error) {
        // 에러 처리
        switch(true) {
            case result.error.includes('Kakao SDK not available'):
                alert('카카오맵 SDK를 사용할 수 없습니다.');
                break;
            case result.error.includes('Kakao Places API Error'):
                alert('카카오 Places API 오류가 발생했습니다.');
                break;
            default:
                alert('오류: ' + result.error);
        }
        return;
    }
    
    // 정상 처리
    console.log('검색 성공:', result);
});
```

---

## 🎓 핵심 개념 요약

1. **카카오맵 기반**: 순수 카카오맵 JavaScript SDK 사용
2. **Widget 시스템**: 재사용 가능한 위젯 컴포넌트
3. **이벤트 드리븐**: 전역 이벤트를 통한 플러그인 간 통신
4. **다중 플러그인 연동**: Store Manager 등과 자연스러운 연동
5. **유연한 API**: JavaScript와 PHP 양쪽에서 사용 가능

---

## 📚 추가 리소스

### JavaScript 데모

`plugins/location/js/location-demo.html` 파일을 브라우저에서 열어 모든 기능을 테스트할 수 있습니다.

### 단축 함수 목록

- `wv_address_search()` - 주소/키워드 검색
- `wv_coords_to_region()` - 좌표 → 지역코드 변환
- `wv_coords_to_address()` - 좌표 → 주소 변환
- `wv_address_to_coords()` - 주소 → 좌표 변환
- `wv_category_search()` - 카테고리 검색
- `wv_check_kakao()` - 카카오 SDK 로드 확인
- `wv_location_init()` - 라이브러리 초기화

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-01-02  
**작성자**: Claude AI