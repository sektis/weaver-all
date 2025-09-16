# Weaver Location JavaScript 라이브러리

카카오맵 JavaScript SDK를 사용한 위치 서비스 라이브러리입니다.

## 📁 파일 구성

- **location.js** - 메인 라이브러리 (카카오맵 JavaScript SDK 전용)
- **location-examples.js** - 사용 예시 및 헬퍼 함수들
- **location-demo.html** - 완전한 테스트 데모 페이지
- ~~location-ajax.php~~ - ~~서버 프록시 (더이상 사용하지 않음)~~

## 🔧 설치 및 설정

### 1. HTML에 스크립트 추가

```html
<!-- jQuery (필수) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- 카카오맵 SDK (필수) -->
<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_API_KEY&libraries=services"></script>

<!-- Weaver Location 라이브러리 -->
<script src="location.js"></script>
<script src="location-examples.js"></script>
```

### 2. 초기화

```javascript
$(document).ready(function() {
    // 카카오 API 키는 이미 스크립트 태그에서 설정됨
    wv_location_init();
    
    // SDK 로드 확인
    if (wv_check_kakao()) {
        console.log('카카오맵 SDK 사용 준비 완료');
    } else {
        console.error('카카오맵 SDK 로드 실패');
    }
});
```

## 🚀 주요 기능

### 1. 키워드/주소 검색

```javascript
// 기본 검색
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

### 2. 좌표 ↔ 지역코드 변환

```javascript
// 좌표 → 지역코드
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

### 3. 좌표 ↔ 주소 변환

```javascript
// 좌표 → 주소
WeaverLocation.coords_to_address(35.0716, 129.0574, function(result) {
    if (result.list.length > 0) {
        var addr = result.list[0];
        console.log('지번주소:', addr.address_name);
        console.log('도로명주소:', addr.road_address_name);
    }
});

// 주소 → 좌표
WeaverLocation.address_to_coords('부산 영도구 동삼동', function(result) {
    if (result.list.length > 0) {
        var coord = result.list[0];
        console.log('위도:', coord.y);
        console.log('경도:', coord.x);
    }
});
```

### 4. 카테고리 검색

```javascript
// 전체 지역에서 카페 검색
WeaverLocation.category_search('CE7', function(result) {
    console.log('카페 목록:', result.list);
});

// 특정 위치 주변 음식점 검색
var lat = 35.0716;
var lng = 129.0574;
var radius = 1000; // 1km

WeaverLocation.category_search('FD6', lat, lng, radius, function(result) {
    console.log('주변 음식점:', result.list);
});

// 단축 함수
wv_category_search('CE7', function(result) {
    console.log('카페 검색 결과:', result);
});
```

## 📋 카테고리 코드

| 코드 | 카테고리 |
|------|----------|
| FD6  | 음식점   |
| CE7  | 카페     |
| AD5  | 숙박     |
| CT1  | 문화시설 |
| AT4  | 관광명소 |
| CS2  | 편의점   |
| PK6  | 주차장   |
| OL7  | 주유소   |
| SW8  | 지하철역 |
| BK9  | 은행     |

## 🎯 응답 데이터 구조

### 검색 결과
```javascript
{
    list: [
        {
            place_name: "장소명",
            address_name: "지번주소",
            road_address_name: "도로명주소",
            x: "경도",
            y: "위도",
            phone: "전화번호",
            category_name: "카테고리"
        }
    ],
    total_count: 10,
    is_end: true
}
```

### 지역코드 변환 결과
```javascript
{
    list: [
        {
            region_1depth_name: "시도",
            region_2depth_name: "시군구", 
            region_3depth_name: "읍면동",
            region_4depth_name: "리"
        }
    ]
}
```

## 🔍 현재 위치 기반 검색 예시

```javascript
function search_nearby_restaurants() {
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
                    });
                }
            });
        });
    }
}
```

## 🗺️ 카카오맵과 연동

```javascript
function display_on_map(places) {
    var mapContainer = document.getElementById('map');
    var center = new kakao.maps.LatLng(places[0].y, places[0].x);
    
    var map = new kakao.maps.Map(mapContainer, {
        center: center,
        level: 5
    });
    
    places.forEach(function(place) {
        var marker = new kakao.maps.Marker({
            position: new kakao.maps.LatLng(place.y, place.x)
        });
        marker.setMap(map);
    });
}
```

## ⚠️ 주의사항

1. **카카오맵 SDK 필수**: 반드시 카카오맵 JavaScript SDK를 먼저 로드해야 합니다.

2. **API 키 필요**: 카카오 개발자센터에서 JavaScript 키를 발급받아 사용하세요.

3. **도메인 등록**: 카카오 개발자센터에서 사용할 도메인을 등록해야 합니다.

4. **HTTPS 권장**: 위치 정보 사용 시 HTTPS 환경이 필요합니다.

## 🛠️ 에러 처리

```javascript
WeaverLocation.address_search('검색어', 10, 1, function(result) {
    if (result.error) {
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

## 📝 단축 함수 목록

- `wv_address_search()` - 주소/키워드 검색
- `wv_coords_to_region()` - 좌표 → 지역코드 변환
- `wv_coords_to_address()` - 좌표 → 주소 변환  
- `wv_address_to_coords()` - 주소 → 좌표 변환
- `wv_category_search()` - 카테고리 검색
- `wv_check_kakao()` - 카카오 SDK 로드 확인
- `wv_location_init()` - 라이브러리 초기화

## 🎮 데모 실행

`location-demo.html` 파일을 브라우저에서 열어 모든 기능을 테스트할 수 있습니다.

---

**변경사항**: 이제 서버 프록시 없이 순수하게 카카오맵 JavaScript SDK만 사용합니다!
