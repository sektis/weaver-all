# 🧩 Widget 플러그인 가이드

> **Weaver 프로젝트의 재사용 가능한 UI 컴포넌트 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [Widget 시스템 구조](#widget-시스템-구조)
3. [기본 사용법](#기본-사용법)
4. [위젯 스킨 제작](#위젯-스킨-제작)
5. [주입 위젯 스킨](#주입-위젯-스킨)
6. [실전 패턴](#실전-패턴)
7. [고급 활용](#고급-활용)
8. [문제 해결](#문제-해결)

---

## 📌 개요

**Widget 플러그인**은 Weaver 프로젝트에서 재사용 가능한 UI 컴포넌트를 쉽게 만들고 사용할 수 있게 해주는 시스템입니다.

### 핵심 특징

✅ **재사용성**: 한 번 만든 위젯을 여러 곳에서 재사용  
✅ **스킨 시스템**: 테마/디바이스별 스킨 분리  
✅ **데이터 전달**: 위젯에 동적 데이터 전달 가능  
✅ **주입 시스템**: 다른 플러그인에서 위젯 스킨 주입 가능  
✅ **간편한 호출**: `wv_widget()` 함수로 어디서나 호출

### Widget vs 일반 스킨

| 구분 | Widget | 일반 스킨 |
|------|--------|-----------|
| **목적** | 재사용 가능한 컴포넌트 | 특정 페이지 전용 |
| **호출** | `wv_widget()` | `make_skin()` |
| **위치** | `theme/pc/{widget_name}/` | `theme/pc/{page_name}/` |
| **주입** | 다른 플러그인에서 주입 가능 | 주입 불가 |

---

## 🏗️ Widget 시스템 구조

### 디렉토리 구조

```
plugins/widget/
├── Widget.php              # 메인 클래스
├── widget.lib.php          # wv_widget() 함수
├── plugin.php              # 플러그인 로더
└── theme/
    └── basic/              # 기본 테마
        ├── pc/
        │   ├── common/
        │   │   └── fixed_quick/
        │   │       └── skin.php
        │   └── header/
        │       └── skin.php
        └── mobile/
            └── ... (동일 구조)
```

### Widget 클래스 구조

```php
namespace weaver;

class Widget extends Makeable {
    
    public function __construct(){
        // 생성자
    }

    public function init_once(){
        // 플러그인당 1회 초기화
    }

    public function display_widget($skin, $data=''){
        // 위젯 렌더링
        return $this->make_skin($skin, $data);
    }
}
```

### wv_widget() 함수

```php
/**
 * 위젯 호출 함수
 * @param string $skin 스킨 경로 (예: 'common/fixed_quick')
 * @param mixed $data 전달할 데이터
 * @param string $make_name Makeable ID (선택)
 * @return string 렌더링된 HTML
 */
function wv_widget($skin, $data='', $make_name=''){
    return wv('widget')->make($make_name)->display_widget($skin, $data);
}
```

---

## 🚀 기본 사용법

### 1. 위젯 호출하기

```php
<!-- 기본 호출 -->
<?php echo wv_widget('common/fixed_quick'); ?>

<!-- 데이터 전달 -->
<?php echo wv_widget('header', array(
    'title' => '메인 페이지',
    'show_search' => true
)); ?>

<!-- Makeable 인스턴스 사용 -->
<?php echo wv_widget('user/profile', $user_data, 'user_01'); ?>
```

### 2. 위젯 스킨 파일 예시

**파일**: `plugins/widget/theme/basic/pc/common/fixed_quick/skin.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget">
    <style>
        <?php echo $skin_selector?> {position: fixed; right: 20px; bottom: 20px; z-index: 1000;}
    </style>

    <div class="quick-menu">
        <button class="quick-btn">TOP</button>
        <button class="quick-btn">문의</button>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            
            $skin.find('.quick-btn').click(function(){
                // 버튼 클릭 처리
            });
        });
    </script>
</div>
```

### 3. 스킨 내부에서 사용 가능한 변수

```php
<?php
// 자동 생성되는 변수들
$skin_id         // 고유 ID (예: 'wv-skin-123')
$skin_selector   // CSS 선택자 (예: '#wv-skin-123')
$skin_class      // CSS 클래스명
$data            // 전달받은 데이터
$wv_skin_path    // 스킨 파일 경로
$wv_skin_url     // 스킨 URL

// 전역 변수
$g5              // 그누보드5 전역 변수
$member          // 로그인 회원 정보
$is_member       // 로그인 여부
$config          // 사이트 설정
?>
```

---

## 🎨 위젯 스킨 제작

### 스킨 제작 기본 템플릿

```php
<?php
/**
 * 위젯명: {위젯 설명}
 * 파일: plugins/widget/theme/basic/pc/{widget_path}/skin.php
 */
if (!defined('_GNUBOARD_')) exit;

// 전달받은 데이터 처리
$title = isset($data['title']) ? $data['title'] : '기본 제목';
$items = isset($data['items']) ? $data['items'] : array();
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-widget-{widget-name}">
    <style>
        <?php echo $skin_selector?> {/* 스타일 */}
        
        @media (min-width: 992px) {
            /* PC 전용 스타일 */
        }

        @media (max-width: 991.98px) {
            /* Mobile 전용 스타일 */
        }
    </style>

    <div class="widget-content">
        <h3><?php echo $title; ?></h3>
        
        <?php if(!empty($items)): ?>
            <ul>
                <?php foreach($items as $item): ?>
                    <li><?php echo $item['name']; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>항목이 없습니다.</p>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            
            // 위젯 초기화 로직
            console.log('Widget initialized:', '<?php echo $skin_id; ?>');
        });
    </script>
</div>
```

### 스킨 파일 네이밍 규칙

```
plugins/widget/theme/basic/
├── pc/
│   ├── {category}/           # 카테고리 폴더 (선택)
│   │   └── {widget_name}/    # 위젯명 폴더
│   │       └── skin.php      # 필수: skin.php
│   └── {widget_name}/        # 또는 바로 위젯명
│       └── skin.php
```

**호출 방식:**
- 카테고리 있음: `wv_widget('category/widget_name')`
- 카테고리 없음: `wv_widget('widget_name')`

### 반응형 처리

```php
<style>
    /* 공통 스타일 */
    <?php echo $skin_selector?> {width: 100%; padding: var(--wv-16);}

    /* PC 전용 (992px 이상) */
    @media (min-width: 992px) {
        <?php echo $skin_selector?> {max-width: var(--wv-1200);}
    }

    /* Mobile 전용 (991.98px 이하) */
    @media (max-width: 991.98px) {
        <?php echo $skin_selector?> {padding: var(--wv-12);}
    }
</style>
```

---

## 🔗 주입 위젯 스킨

### 주입 위젯이란?

다른 플러그인에서 widget 플러그인의 스킨을 확장하거나 재정의하는 시스템입니다.

### 주입 스킨 위치

```
plugins/{other_plugin}/theme/basic/plugins/widget/skin/pc/{widget_path}/
```

**예시:**
```
plugins/store_manager/theme/basic/plugins/widget/skin/
├── pc/
│   └── location/
│       ├── map/
│       │   └── skin1.php      # location 플러그인 map 위젯 재정의
│       └── address/
│           └── skin.php       # location 플러그인 address 위젯 재정의
```

### 주입 스킨 호출

```php
<!-- location 플러그인의 map 위젯 -->
<?php echo wv_widget('location/map'); ?>
<!-- → store_manager가 주입한 스킨을 우선 사용 -->

<!-- 특정 스킨 지정 -->
<?php echo wv_widget('location/map', array(
    'skin_variant' => 'skin1'  // skin1.php 사용
)); ?>
```

### 주입 스킨 제작 예시

**파일**: `plugins/store_manager/theme/basic/plugins/widget/skin/pc/location/map/skin1.php`

```php
<?php
/**
 * Store Manager의 Location Map 위젯 커스텀 스킨
 * location 플러그인의 map 위젯을 재정의
 */
if (!defined('_GNUBOARD_')) exit;

// Store Manager 전용 기능 추가
$show_store_info = isset($data['show_store_info']) ? $data['show_store_info'] : true;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> store-location-map">
    <style>
        <?php echo $skin_selector?> {/* 커스텀 스타일 */}
    </style>

    <!-- 카카오맵 -->
    <div class="kakao-map"></div>

    <?php if($show_store_info): ?>
        <!-- Store Manager 전용: 매장 정보 패널 -->
        <div class="store-info-panel">
            <h4>매장 정보</h4>
            <!-- ... -->
        </div>
    <?php endif; ?>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            
            // Store Manager 전용 기능
            $(document).on('wv_location_map_marker_clicked', function(e, marker_data){
                // 마커 클릭 시 매장 정보 패널 업데이트
                console.log('Store marker clicked:', marker_data);
            });
        });
    </script>
</div>
```

---

## 💡 실전 패턴

### 1. 조건부 위젯 렌더링

```php
<?php
// 로그인한 경우에만 표시
if($is_member){
    echo wv_widget('user/my_menu', array(
        'member' => $member
    ));
}

// 특정 권한이 있는 경우
if($member['mb_level'] >= 10){
    echo wv_widget('admin/quick_menu');
}

// 페이지별 다른 위젯
$page_id = $_GET['wv_page_id'];
if($page_id == 'main'){
    echo wv_widget('main/banner');
} else {
    echo wv_widget('sub/banner');
}
?>
```

### 2. 위젯 체이닝

```php
<!-- 여러 위젯 연속 호출 -->
<div class="widget-container">
    <?php echo wv_widget('common/header'); ?>
    <?php echo wv_widget('common/nav'); ?>
    
    <main>
        <?php echo wv_widget('content/body', $page_data); ?>
    </main>
    
    <?php echo wv_widget('common/footer'); ?>
</div>
```

### 3. 동적 데이터 전달

```php
<?php
// DB에서 데이터 조회
$store_list = wv()->store_manager->made('sub01_01')->get_list(array(
    'where_location' => array('lat' => "<>''"),
    'rows' => 10
));

// 위젯에 전달
echo wv_widget('store/list', array(
    'stores' => $store_list['list'],
    'total' => $store_list['total_count'],
    'show_map' => true
));
?>
```

### 4. AJAX 위젯 업데이트

```php
<!-- 초기 렌더링 -->
<div id="widget-container">
    <?php echo wv_widget('news/list', array('page' => 1)); ?>
</div>

<script>
// AJAX로 위젯 업데이트
function loadMoreNews(page){
    $.ajax({
        url: '<?php echo wv()->widget->ajax_url; ?>',
        data: {
            action: 'render_widget',
            skin: 'news/list',
            data: {page: page}
        },
        success: function(response){
            $('#widget-container').html(response.content);
        }
    });
}
</script>
```

### 5. 이벤트 기반 위젯

```php
<!-- 위젯 A: 이벤트 발생 -->
<script>
$(document).trigger('store_selected', {
    wr_id: 123,
    name: '맛있는 한식집'
});
</script>

<!-- 위젯 B: 이벤트 수신 -->
<script>
$(document).on('store_selected', function(e, store_data){
    console.log('Store selected:', store_data);
    // 위젯 B 업데이트
});
</script>
```

---

## 🔧 고급 활용

### 1. 위젯 캐싱

```php
<?php
/**
 * 위젯 결과 캐싱 (성능 최적화)
 */
function wv_widget_cached($skin, $data='', $cache_time=3600){
    $cache_key = 'widget_' . md5($skin . serialize($data));
    
    // 캐시 확인
    $cached = get_transient($cache_key);
    if($cached !== false){
        return $cached;
    }
    
    // 위젯 렌더링
    $output = wv_widget($skin, $data);
    
    // 캐시 저장
    set_transient($cache_key, $output, $cache_time);
    
    return $output;
}

// 사용
echo wv_widget_cached('heavy/widget', $data, 1800); // 30분 캐싱
?>
```

### 2. 위젯 Lazy Loading

```php
<!-- Lazy Load 위젯 -->
<div class="widget-lazy" 
     data-widget="store/list" 
     data-widget-data='{"rows":20}'>
    <div class="loading">로딩 중...</div>
</div>

<script>
$(document).ready(function(){
    $('.widget-lazy').each(function(){
        var $container = $(this);
        var widget = $container.data('widget');
        var widget_data = $container.data('widget-data');
        
        // Intersection Observer로 뷰포트 진입 시 로드
        var observer = new IntersectionObserver(function(entries){
            if(entries[0].isIntersecting){
                $.ajax({
                    url: '<?php echo wv()->widget->ajax_url; ?>',
                    data: {
                        action: 'render_widget',
                        skin: widget,
                        data: widget_data
                    },
                    success: function(response){
                        $container.html(response.content);
                    }
                });
                observer.unobserve($container[0]);
            }
        });
        
        observer.observe($container[0]);
    });
});
</script>
```

### 3. 위젯 테마 동적 변경

```php
<?php
// 사용자 선택에 따라 다른 테마의 위젯 사용
$user_theme = $member['mb_theme'] ?: 'basic';

// 특정 테마로 위젯 렌더링
wv()->widget->set_theme_dir($user_theme);
echo wv_widget('user/dashboard', $user_data);

// 원래 테마로 복원
wv()->widget->set_theme_dir('basic');
?>
```

### 4. 위젯 A/B 테스트

```php
<?php
// A/B 테스트를 위한 랜덤 위젯 선택
function wv_widget_ab_test($skin_a, $skin_b, $data='', $ratio=50){
    $variant = (rand(1, 100) <= $ratio) ? $skin_a : $skin_b;
    
    // 로깅 (GA4 등)
    echo "<script>gtag('event', 'widget_variant', {variant: '{$variant}'});</script>";
    
    return wv_widget($variant, $data);
}

// 사용
echo wv_widget_ab_test(
    'banner/style_a',   // 버전 A
    'banner/style_b',   // 버전 B
    $banner_data,
    60                  // A를 60% 확률로 표시
);
?>
```

---

## 🎯 실전 예시

### 예시 1: Location Map 위젯 (store_manager에서 주입)

**파일**: `plugins/store_manager/theme/basic/plugins/widget/skin/pc/location/map/skin1.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

$map_options = isset($data) && is_array($data) ? $data : array();
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> store-map-widget">
    <style>
        <?php echo $skin_selector?> .kakao-map {width: 100%; height: 100%;}
        <?php echo $skin_selector?> .store-info-panel {position: absolute; top: 20px; left: 20px; background: white; padding: var(--wv-16); border-radius: var(--wv-8); box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 100;}
    </style>

    <!-- 카카오맵 -->
    <div class="kakao-map"></div>

    <!-- 매장 정보 패널 -->
    <div class="store-info-panel" style="display: none;">
        <h4 class="store-name"></h4>
        <p class="store-address"></p>
        <button class="btn-detail">상세보기</button>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            var map, markers = [];

            // 카카오맵 초기화
            function initMap(){
                var container = $skin.find('.kakao-map')[0];
                var options = {
                    center: new kakao.maps.LatLng(37.5665, 126.9780),
                    level: 8
                };
                map = new kakao.maps.Map(container, options);
            }

            // 매장 마커 추가
            function addStoreMarkers(stores){
                stores.forEach(function(store){
                    var marker = new kakao.maps.Marker({
                        position: new kakao.maps.LatLng(
                            store.location.lat,
                            store.location.lng
                        ),
                        map: map
                    });

                    // 마커 클릭 이벤트
                    kakao.maps.event.addListener(marker, 'click', function(){
                        showStoreInfo(store);
                    });

                    markers.push(marker);
                });
            }

            // 매장 정보 패널 표시
            function showStoreInfo(store){
                var $panel = $skin.find('.store-info-panel');
                $panel.find('.store-name').text(store.store.name);
                $panel.find('.store-address').text(store.location.address_name);
                $panel.show();

                // 상세보기 버튼
                $panel.find('.btn-detail').off('click').on('click', function(){
                    location.href = '/store/' + store.wr_id;
                });
            }

            // 지도 변경 이벤트 수신
            $(document).on('wv_location_map_changed', function(e, bounds){
                // 현재 보이는 영역의 매장 로드
                loadStoresInBounds(bounds);
            });

            // 초기화
            initMap();
        });
    </script>
</div>
```

### 예시 2: Fixed Quick Menu 위젯

**파일**: `plugins/widget/theme/basic/pc/common/fixed_quick/skin.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget" 
     style="position: fixed; height: 100%; pointer-events: none; max-width: inherit; width: inherit; z-index: 1000; top: 0">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?>:has(.modal.show) {position: static!important;}

        @media (min-width: 992px) {
            <?php echo $skin_selector?> .left-wrap {right: unset; left: 0;}
            <?php echo $skin_selector?> .right-wrap {left: unset; right: 0;}
        }

        @media (max-width: 991.98px) {
            <?php echo $skin_selector?> .left-wrap {right: unset; left: 0;}
            <?php echo $skin_selector?> .right-wrap {left: unset; right: 0;}
        }
    </style>

    <?php include_once $wv_skin_path.'/left.php'; ?>
    <?php include_once $wv_skin_path.'/right.php'; ?>
    <?php include_once $wv_skin_path.'/bottom.php'; ?>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            
            // 모달이 열릴 때 fixed 해제
            $(document).on('show.bs.modal', function(){
                $skin.css('position', 'static');
            });

            // 모달이 닫힐 때 fixed 복원
            $(document).on('hidden.bs.modal', function(){
                if(!$('.modal.show').length){
                    $skin.css('position', 'fixed');
                }
            });
        });
    </script>
</div>
```

---

## 🔍 문제 해결

### 위젯이 렌더링되지 않을 때

```php
// 1. 스킨 파일 경로 확인
$skin_path = wv()->widget->get_theme_path() . '/common/header/skin.php';
if(!file_exists($skin_path)){
    echo '스킨 파일이 없습니다: ' . $skin_path;
}

// 2. 테마 디렉토리 확인
echo wv()->widget->plugin_theme_path;
// → plugins/widget/theme/basic/pc 또는 mobile

// 3. 스킨 호출 경로 확인
echo wv_widget('common/header');  // ✅ 정확
echo wv_widget('/common/header'); // ❌ 슬래시 불필요
echo wv_widget('header');         // ❌ 경로 누락
```

### 데이터가 전달되지 않을 때

```php
<!-- 위젯 호출 -->
<?php 
$widget_data = array(
    'title' => '제목',
    'items' => $items
);
echo wv_widget('my/widget', $widget_data);
?>

<!-- 스킨 파일 내부 -->
<?php
// $data 변수로 접근
var_dump($data);
// → array('title' => '제목', 'items' => [...])

// 안전한 접근
$title = isset($data['title']) ? $data['title'] : '기본값';
?>
```

### 스타일이 적용되지 않을 때

```php
<!-- skin.php -->
<style>
    /* ❌ 잘못된 방법 */
    .my-widget {color: red;}

    /* ✅ 올바른 방법 */
    <?php echo $skin_selector?> .my-widget {color: red;}
</style>

<!-- 이유: $skin_selector는 고유 ID로 스코핑 -->
```

### 주입 스킨이 작동하지 않을 때

```php
// 1. 주입 스킨 경로 확인
// plugins/{plugin}/theme/basic/plugins/widget/skin/pc/{widget_path}/

// 2. widget 플러그인이 해당 플러그인의 주입을 인식하는지 확인
$injections = wv()->widget->injection_plugins;
print_r($injections);

// 3. 테마 디렉토리가 올바른지 확인
// theme/basic (O) theme/basic/pc (X)
```

---

## 📚 참고사항

### Widget vs Plugin 스킨

| 항목 | Widget 스킨 | Plugin 스킨 |
|------|-------------|-------------|
| 위치 | `plugins/widget/theme/` | `plugins/{plugin}/theme/` |
| 호출 | `wv_widget()` | `$plugin->make_skin()` |
| 주입 | 가능 | 불가 |
| 용도 | 재사용 컴포넌트 | 플러그인 전용 UI |

### 네이밍 컨벤션

```php
// 위젯명: 카테고리/기능명 형식 추천
wv_widget('user/profile')      // ✅ 명확
wv_widget('common/header')     // ✅ 명확
wv_widget('widget1')           // ❌ 모호함

// 데이터 키: snake_case 사용
array(
    'store_name' => '매장명',   // ✅
    'storeName' => '매장명',    // ❌ (camelCase 지양)
)
```

### 성능 최적화 팁

```php
// 1. 불필요한 위젯 호출 최소화
<?php if($show_banner): ?>
    <?php echo wv_widget('banner'); ?>
<?php endif; ?>

// 2. 무거운 위젯은 캐싱 사용
<?php echo wv_widget_cached('heavy/widget', $data, 3600); ?>

// 3. Lazy Loading 활용
<div class="widget-lazy" data-widget="store/list"></div>
```

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-10-01  
**작성자**: Claude AI