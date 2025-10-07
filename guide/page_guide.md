# 📄 Page 플러그인 가이드

> **Weaver 프로젝트의 커스텀 페이지 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [핵심 개념](#핵심-개념)
3. [페이지 파일 구조](#페이지-파일-구조)
4. [페이지 ID 시스템](#페이지-id-시스템)
5. [페이지 생성 방법](#페이지-생성-방법)
6. [페이지 접근 방식](#페이지-접근-방식)
7. [주요 메서드](#주요-메서드)
8. [특수 페이지](#특수-페이지)
9. [마이페이지 시스템](#마이페이지-시스템)
10. [실전 활용 예시](#실전-활용-예시)
11. [문제 해결](#문제-해결)

---

## 📌 개요

**Page 플러그인**은 그누보드5의 표준 페이지 구조를 확장하여, 테마 기반의 커스텀 페이지를 쉽게 만들 수 있게 해주는 시스템입니다.

### 핵심 특징

✅ **테마 기반**: 테마 디렉토리에서 페이지 파일 관리  
✅ **Layout 통합**: head/tail 자동 포함  
✅ **URL Rewrite**: `/page/{page_id}/` 형태의 깔끔한 URL  
✅ **멀티디바이스**: PC/Mobile 스킨 자동 분기  
✅ **마이페이지 시스템**: 회원 전용 페이지 자동 감지  
✅ **하이픈 폴더 지원**: `page_id='main-sub'` → `/theme/pc/main/sub.php`

---

## 🧩 핵심 개념

### 1. Page ID

페이지를 식별하는 고유한 문자열입니다.

```php
// URL
/?wv_page_id=about
/page/about/

// 페이지 파일
theme/basic/pc/about.php
theme/basic/mobile/about.php
```

### 2. 하이픈 → 폴더 변환

Page ID에 하이픈(`-`)이 있으면 자동으로 슬래시(`/`)로 변환되어 폴더 구조를 지원합니다.

```php
// Page ID: main-sub-detail
/?wv_page_id=main-sub-detail

// 실제 파일 경로
theme/basic/pc/main/sub/detail.php
theme/basic/mobile/main/sub/detail.php
```

### 3. 디바이스별 자동 분기

모바일에서 접근하면 `mobile/` 디렉토리를 먼저 찾고, 없으면 `pc/` 디렉토리의 파일을 사용합니다.

```
theme/basic/
├── pc/
│   ├── about.php        ← PC에서 사용
│   └── contact.php      ← PC/Mobile 모두 사용 (mobile 없을 때)
└── mobile/
    └── about.php        ← Mobile에서만 사용
```

---

## 🗂️ 페이지 파일 구조

### 기본 구조

```
plugins/page/
├── Page.php              # 메인 클래스
├── page_index.php        # 페이지 렌더링 파일
├── plugin.php            # 플러그인 로더
└── theme/
    └── basic/            # 테마 디렉토리
        ├── config.php    # 테마 설정 (선택)
        ├── pc/
        │   ├── main.php           # 메인 페이지
        │   ├── about.php          # 회사소개
        │   ├── mypage.php         # 마이페이지
        │   └── member/
        │       ├── login.php      # 로그인 페이지 오버라이드
        │       └── register.php   # 회원가입 페이지 오버라이드
        └── mobile/
            ├── main.php
            └── about.php
```

### 파일 네이밍 규칙

| Page ID | 파일 경로 |
|---------|-----------|
| `main` | `theme/basic/pc/main.php` |
| `about` | `theme/basic/pc/about.php` |
| `main-sub` | `theme/basic/pc/main/sub.php` |
| `board-notice` | `theme/basic/pc/board/notice.php` |
| `member-login` | `theme/basic/pc/member/login.php` |

---

## 🆔 페이지 ID 시스템

### 1. 기본 페이지 ID

- **메인 페이지**: `main` (기본값)
- **쇼핑몰 메인**: `main_shop`

### 2. Page ID 설정 방법

#### A. URL 쿼리스트링

```
/?wv_page_id=about
```

#### B. URL Rewrite

```
/page/about/
```

#### C. PHP 코드에서 설정

```php
// 플러그인 초기화 시
wv('page')->set_page_index_id('custom_main');
```

### 3. Page ID 우선순위

1. **Rewrite URL**: `/page/{page_id}/`
2. **쿼리스트링**: `wv_page_id` 파라미터
3. **기본값**: `main` (index.php) 또는 `main_shop` (shop/index.php)

---

## 📝 페이지 생성 방법

### 1. 간단한 페이지

**파일**: `plugins/page/theme/basic/pc/about.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<div class="container py-5">
    <h1>회사소개</h1>
    <p>우리 회사는...</p>
</div>
```

**접근**:
- `/?wv_page_id=about`
- `/page/about/`

### 2. 스타일/스크립트 포함

**파일**: `plugins/page/theme/basic/pc/services.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 페이지 전용 CSS/JS 추가
add_stylesheet('<link rel="stylesheet" href="'.$wv_page_skin_url.'/page.css">', 100);
add_javascript('<script src="'.$wv_page_skin_url.'/page.js"></script>', 100);
?>

<div class="services-page">
    <h1>서비스 안내</h1>
    <!-- 내용 -->
</div>
```

### 3. 하위 폴더 페이지

**파일**: `plugins/page/theme/basic/pc/company/history.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<div class="container">
    <h1>회사 연혁</h1>
    <!-- 내용 -->
</div>
```

**접근**:
- `/?wv_page_id=company-history`
- `/page/company-history/`

### 4. 데이터 연동 페이지

**파일**: `plugins/page/theme/basic/pc/store-list.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// Store Manager 플러그인 사용
$result = wv()->store_manager->made('sub01_01')->get_list(array(
    'where' => "wr_is_comment = 0",
    'order_by' => 'wr_datetime DESC',
    'rows' => 20,
    'page' => (int)$_GET['page'] ?: 1
));
?>

<div class="container py-5">
    <h1>매장 목록</h1>
    
    <?php foreach($result['list'] as $store): ?>
    <div class="store-item mb-4">
        <h3><?php echo $store->store->name; ?></h3>
        <p><?php echo $store->location->address_name; ?></p>
        <p><?php echo $store->store->tel; ?></p>
    </div>
    <?php endforeach; ?>
    
    <!-- 페이징 -->
    <?php echo $result['paging']; ?>
</div>
```

### 5. Widget 통합 페이지

**파일**: `plugins/page/theme/basic/pc/map.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<div class="container-fluid p-0" style="height: 100vh;">
    <h1 class="visually-hidden">매장 지도</h1>
    
    <?php
    // Location 플러그인 Map 위젯
    echo wv_widget('location/map', array(
        'map_id' => 'main_map',
        'height' => '100vh',
        'center_lat' => 37.5665,
        'center_lng' => 126.9780,
        'zoom' => 12
    ));
    ?>
</div>
```

---

## 🌐 페이지 접근 방식

### 1. 링크 생성

#### A. `wv_page_url()` 함수

```php
// 기본 사용
<a href="<?php echo wv_page_url('about'); ?>">회사소개</a>

// 쿼리스트링 추가
<a href="<?php echo wv_page_url('services', 'category=web'); ?>">웹 서비스</a>

// 배열로 쿼리 전달
<a href="<?php echo wv_page_url('search', array('q' => '검색어', 'page' => 1)); ?>">검색</a>

// 디바이스 강제 지정
<a href="<?php echo wv_page_url('mobile_page', '', 'mobile'); ?>">모바일 페이지</a>
```

#### B. 직접 URL 작성

```php
// 쿼리스트링 방식
<a href="/?wv_page_id=about">회사소개</a>

// Rewrite 방식 (short_url_clean 함수 필요)
<a href="/page/about/">회사소개</a>
```

### 2. 현재 페이지 ID 확인

```php
// Page 플러그인에서
$current_page_id = wv('page')->page_id;

// 전역 변수
global $wv_page_id;
echo $wv_page_id;

// body 속성에서 (JavaScript)
var pageId = document.body.getAttribute('wv-page-id');
```

### 3. 페이지 타입 확인

```php
// 일반 페이지
if(wv_info('type') == 'page') {
    // 페이지 전용 로직
}

// 마이페이지
if(wv_info('type') == 'mypage') {
    // 마이페이지 전용 로직
}

// 메인 페이지 (index.php)
if(wv_info('type') == 'index') {
    // 메인 페이지 로직
}
```

---

## 🛠️ 주요 메서드

### Page 클래스 메서드

```php
// Page 인스턴스 가져오기
$page = wv('page');
```

#### 1. `set_page_index_id($page_id)`

메인 페이지(index.php)의 기본 Page ID를 설정합니다.

```php
// plugin.php에서
wv('page')->set_page_index_id('home');

// 이제 index.php 접근 시 theme/basic/pc/home.php 사용
```

#### 2. `get_page_path()`

현재 페이지 파일의 전체 경로를 반환합니다.

```php
$page_path = wv('page')->get_page_path();
// 예: /home/user/public_html/plugin/weaver/plugins/page/theme/basic/pc/about.php
```

#### 3. `page_url_make($url)`

일반 URL을 Page 플러그인의 Rewrite URL로 변환합니다.

```php
$url = '/?wv_page_id=about&category=web';
$clean_url = wv('page')->page_url_make($url);
// 결과: /page/about/?category=web
```

---

## 🌟 특수 페이지

### 1. 메인 페이지 (Index)

**설정**:

```php
// config.php 또는 plugin.php
wv('page')->set_page_index_id('main');
```

**파일**: `theme/basic/pc/main.php`

**특징**:
- `index.php` 접근 시 자동 로드
- `_INDEX_` 상수 자동 정의

### 2. 쇼핑몰 메인

**설정**: 자동 (`main_shop` 고정)

**파일**: `theme/basic/pc/main_shop.php`

**특징**:
- `shop/index.php` 접근 시 자동 로드
- 쇼핑몰 전용 메인 페이지

### 3. URL Rewrite 페이지

**설정**: `page_rewrite_dir = 'page'` (기본값)

**URL 구조**:
```
/page/{page_id}/
```

**특징**:
- 깔끔한 URL 구조
- SEO 친화적
- `short_url_clean()` 함수와 연동

---

## 👤 마이페이지 시스템

### 마이페이지 자동 감지

Page 플러그인은 다음 조건에서 자동으로 **마이페이지 모드**로 전환됩니다:

1. **기본 회원 관련 파일**:
    - `login.php`
    - `register_form.php`
    - `register.php`

2. **Menu 플러그인의 'member' 메뉴**:
    - Menu 플러그인에서 `made('member')` 메뉴의 모든 페이지

3. **커스텀 마이페이지 설정**:
```php
// config.php
wv('page')->page_my_page_id_array = array('mypage', 'profile', 'settings');
wv('page')->page_my_page_file_array = array('member_confirm', 'password_lost');
wv('page')->page_my_page_bo_table_array = array('member_notice');
```

### 마이페이지 확인

```php
if(wv_info('type') == 'mypage') {
    // 마이페이지 전용 레이아웃/로직
}
```

### 회원 메뉴 자동 생성

```php
// Menu 플러그인과 연동 시 자동으로 회원 메뉴 생성
$member_menu = wv('page')->get_member_menu_array('마이페이지', 'mypage');

/*
array(
    'name' => '마이페이지',
    'url' => '/?wv_page_id=mypage',
    'sub' => array(
        array('name' => '정보변경', 'url' => '...', 'icon' => '...'),
        array('name' => '로그아웃', 'url' => '...', 'icon' => '...')
    )
)
*/
```

---

## 🎯 실전 활용 예시

### 1. 회사소개 페이지

**파일**: `theme/basic/pc/company.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<section class="hero bg-primary text-white py-5">
    <div class="container">
        <h1>회사소개</h1>
        <p class="lead">우리는 최고의 서비스를 제공합니다</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2>회사 비전</h2>
                <p>...</p>
            </div>
            <div class="col-lg-6">
                <h2>핵심 가치</h2>
                <p>...</p>
            </div>
        </div>
    </div>
</section>
```

### 2. 동적 콘텐츠 페이지

**파일**: `theme/basic/pc/blog.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 게시판 목록 조회
$bo_table = 'blog';
$page = (int)($_GET['page'] ?: 1);

include_once(G5_LIB_PATH.'/latest.lib.php');

$list = latest($bo_table, 10, 150, $page);
?>

<div class="container py-5">
    <h1>블로그</h1>
    
    <div class="row">
        <?php foreach($list as $row): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <?php if($row['thumb']['src']): ?>
                <img src="<?php echo $row['thumb']['src']; ?>" class="card-img-top" alt="">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['wr_subject']; ?></h5>
                    <p class="card-text"><?php echo $row['wr_content']; ?></p>
                    <a href="<?php echo $row['href']; ?>" class="btn btn-primary">자세히 보기</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
```

### 3. Widget 통합 대시보드

**파일**: `theme/basic/pc/dashboard.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 로그인 체크
if(!$member['mb_id']) {
    alert_close('로그인이 필요합니다.');
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- 프로필 위젯 -->
        <div class="col-lg-3">
            <?php echo wv_widget('member/profile'); ?>
        </div>
        
        <!-- 통계 위젯 -->
        <div class="col-lg-9">
            <div class="row mb-4">
                <div class="col-md-4">
                    <?php echo wv_widget('stats/orders'); ?>
                </div>
                <div class="col-md-4">
                    <?php echo wv_widget('stats/reviews'); ?>
                </div>
                <div class="col-md-4">
                    <?php echo wv_widget('stats/points'); ?>
                </div>
            </div>
            
            <!-- 최근 활동 -->
            <div class="card">
                <div class="card-header">
                    <h5>최근 활동</h5>
                </div>
                <div class="card-body">
                    <?php echo wv_widget('member/recent_activity'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 4. 모바일 전용 페이지

**파일**: `theme/basic/mobile/m-order.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 모바일에서만 접근 가능
if(!G5_IS_MOBILE) {
    alert_close('모바일에서만 접근 가능합니다.');
}
?>

<div class="mobile-order-page">
    <header class="mobile-header">
        <h1>주문하기</h1>
        <button type="button" class="btn-back">뒤로</button>
    </header>
    
    <div class="order-form">
        <!-- 주문 폼 -->
    </div>
</div>
```

### 5. 조건부 레이아웃 페이지

**파일**: `theme/basic/pc/event.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 이벤트 기간 체크
$event_start = '2025-01-01';
$event_end = '2025-12-31';
$is_event_period = (G5_TIME_YMDHIS >= $event_start && G5_TIME_YMDHIS <= $event_end);
?>

<?php if($is_event_period): ?>
    <!-- 이벤트 진행 중 -->
    <div class="event-active">
        <h1>🎉 특별 이벤트 진행 중!</h1>
        <!-- 이벤트 내용 -->
    </div>
<?php else: ?>
    <!-- 이벤트 종료 -->
    <div class="event-closed">
        <h1>이벤트가 종료되었습니다</h1>
        <p>다음 이벤트를 기대해주세요!</p>
    </div>
<?php endif; ?>
```

---

## 🔗 다른 플러그인과 통합

### 1. Menu 플러그인

**메뉴에 페이지 추가**:

```php
// Menu 플러그인 메뉴 배열
array(
    array(
        'name' => '회사소개',
        'url' => '/?wv_page_id=company',  // Page ID 사용
        'sub' => array(
            array('name' => '인사말', 'url' => '/?wv_page_id=company-greeting'),
            array('name' => '연혁', 'url' => '/?wv_page_id=company-history'),
        )
    )
)
```

### 2. Layout 플러그인

**페이지별 레이아웃 분기**:

**파일**: `plugins/layout/theme/basic/layout_head.php`

```php
<?php
$page_id = wv('page')->page_id;

// 페이지별 다른 헤더
if($page_id == 'landing') {
    // 랜딩 페이지 헤더 (메뉴 없음)
    ?>
    <header class="landing-header">
        <img src="<?php echo G5_THEME_URL; ?>/img/logo.png" alt="Logo">
    </header>
    <?php
} else {
    // 일반 페이지 헤더
    ?>
    <header class="site-header">
        <?php echo wv_widget('menu/main'); ?>
    </header>
    <?php
}
?>
```

### 3. Store Manager 플러그인

**매장 상세 페이지**:

**파일**: `theme/basic/pc/store-detail.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

$wr_id = (int)$_GET['wr_id'];
if(!$wr_id) {
    alert_close('잘못된 접근입니다.');
}

// 매장 정보 조회
$store = wv()->store_manager->made('sub01_01')->get($wr_id);
if(!$store) {
    alert_close('존재하지 않는 매장입니다.');
}
?>

<div class="container py-5">
    <!-- 매장 기본 정보 -->
    <section class="store-info mb-5">
        <h1><?php echo $store->store->name; ?></h1>
        <p class="text-muted"><?php echo $store->store->category; ?></p>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>연락처</h4>
                <p><?php echo $store->store->tel; ?></p>
                
                <h4 class="mt-3">주소</h4>
                <p><?php echo $store->location->address_name; ?></p>
            </div>
            <div class="col-md-6">
                <!-- 지도 위젯 -->
                <?php
                echo wv_widget('location/map', array(
                    'center_lat' => $store->location->lat,
                    'center_lng' => $store->location->lng,
                    'height' => '400px'
                ));
                ?>
            </div>
        </div>
    </section>
    
    <!-- 메뉴 목록 -->
    <section class="store-menu">
        <h2>메뉴</h2>
        <div class="list-group">
            <?php foreach($store->menu->list as $menu): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <h5><?php echo $menu['name']; ?></h5>
                    <strong><?php echo number_format($menu['price']); ?>원</strong>
                </div>
                <?php if($menu['description']): ?>
                <p class="mb-0 text-muted"><?php echo $menu['description']; ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
```

---

## 🐛 문제 해결

### 페이지가 로드되지 않을 때

#### 1. 파일 경로 확인

```php
// 디버깅 코드 추가
var_dump(wv('page')->get_page_path());

// 예상 경로:
// /home/user/public_html/plugin/weaver/plugins/page/theme/basic/pc/about.php
```

#### 2. 파일 존재 여부 확인

```bash
# 서버에서 확인
ls -la plugins/page/theme/basic/pc/

# 파일 권한 확인
chmod 644 plugins/page/theme/basic/pc/*.php
```

#### 3. Page ID 확인

```php
// 현재 Page ID 출력
global $wv_page_id;
var_dump($wv_page_id);

// 또는
var_dump(wv('page')->page_id);
```

### URL Rewrite가 작동하지 않을 때

#### 1. short_url_clean 함수 확인

```php
// 함수 존재 여부 확인
if(function_exists('short_url_clean')) {
    echo "✅ short_url_clean 함수 존재";
} else {
    echo "❌ short_url_clean 함수 없음";
}
```

#### 2. .htaccess 확인

```apache
# 그누보드5 루트의 .htaccess
RewriteEngine On
RewriteBase /

# Page Rewrite
RewriteRule ^page/([^/]+)/?$ /bbs/board.php?bo_table=page&wr_seo_title=$1 [L,QSA]
```

### 마이페이지가 감지되지 않을 때

#### 1. 페이지 타입 확인

```php
// 현재 타입 출력
var_dump(wv_info('type'));

// 기대값: 'mypage'
```

#### 2. 강제 마이페이지 설정

```php
// config.php
wv('page')->page_my_page_id_array = array('mypage', 'profile');
```

### Layout이 적용되지 않을 때

#### 1. page_index.php 확인

```php
// plugins/page/page_index.php

// head.php / tail.php 포함 여부 확인
include_once(G5_PATH.'/head.php');  // ← 있어야 함
echo $page_content;
include_once(G5_PATH.'/tail.php');  // ← 있어야 함
```

#### 2. Layout 플러그인 활성화 확인

```php
if(wv_plugin_exists('layout')) {
    echo "✅ Layout 플러그인 활성화됨";
} else {
    echo "❌ Layout 플러그인 없음";
}
```

---

## 💡 고급 활용 팁

### 1. 동적 페이지 제목 설정

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 페이지 제목 설정
add_replace('seo_title', function($title) {
    return '커스텀 페이지 제목 - ' . $title;
});
?>
```

### 2. 페이지별 Body 클래스 추가

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// Body 클래스 추가
add_replace('body_class', function($classes) {
    $classes[] = 'page-about';
    return $classes;
});
?>
```

### 3. 조건부 CSS/JS 로딩

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 특정 페이지에만 스크립트 로드
if(wv('page')->page_id == 'map') {
    add_javascript('<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_KEY"></script>', 0);
}
?>
```

### 4. 페이지 캐싱

```php
<?php
if (!defined('_GNUBOARD_')) exit;

$cache_key = 'page_' . wv('page')->page_id;
$cache_time = 3600; // 1시간

// 캐시에서 가져오기
$content = wv_cache_get($cache_key);

if(!$content) {
    // 무거운 작업 수행
    ob_start();
    ?>
    <div class="heavy-content">
        <!-- 복잡한 콘텐츠 -->
    </div>
    <?php
    $content = ob_get_clean();
    
    // 캐시에 저장
    wv_cache_set($cache_key, $content, $cache_time);
}

echo $content;
?>
```

### 5. AJAX 페이지 로딩

**HTML**:
```html
<button data-wv-ajax-url="/?wv_page_id=product-detail&wr_id=123&no_layout=1"
        data-wv-ajax-target="#product-modal .modal-body">
    제품 상세보기
</button>

<div class="modal" id="product-modal">
    <div class="modal-body"></div>
</div>
```

**페이지 파일**:
```php
<?php
if (!defined('_GNUBOARD_')) exit;

// no_layout 파라미터로 head/tail 제외
if($_GET['no_layout']) {
    // Layout 없이 콘텐츠만 출력
    ?>
    <div class="product-detail">
        <h3>제품 상세</h3>
        <!-- 내용 -->
    </div>
    <?php
    exit; // head/tail 건너뛰기
}
?>
```

---

## 📊 페이지 라이프사이클

```
1. 요청 시작
   ↓
2. Page ID 결정
   - URL rewrite 체크
   - wv_page_id 파라미터 체크
   - 기본값 (main/main_shop)
   ↓
3. 페이지 파일 검색
   - mobile 디렉토리 (모바일인 경우)
   - pc 디렉토리 (fallback)
   ↓
4. 마이페이지 감지
   - 파일명 체크
   - Menu 플러그인 체크
   - 커스텀 설정 체크
   ↓
5. 페이지 타입 설정
   - wv_info('type') 업데이트
   ↓
6. 페이지 렌더링
   - config.php 로드 (선택)
   - head.php 포함
   - 페이지 콘텐츠 출력
   - tail.php 포함
   ↓
7. 응답 전송
```

---

## 📋 체크리스트

### 새 페이지 만들 때

- [ ] Page ID 결정 (`about`, `company-history` 등)
- [ ] 파일 생성 (`theme/basic/pc/{page_id}.php`)
- [ ] 그누보드 체크 (`if (!defined('_GNUBOARD_')) exit;`)
- [ ] 콘텐츠 작성
- [ ] CSS/JS 필요 시 추가 (`add_stylesheet`, `add_javascript`)
- [ ] 모바일 버전 필요 시 `mobile/` 디렉토리에도 생성
- [ ] 메뉴에 링크 추가 (Menu 플러그인 사용 시)
- [ ] 접근 권한 체크 (회원 전용 등)
- [ ] SEO 메타 태그 설정 (필요 시)
- [ ] 테스트 (PC/Mobile 모두)

### 문제 발생 시 체크

- [ ] 파일 경로가 올바른가?
- [ ] 파일 권한이 올바른가? (644)
- [ ] Page ID가 URL과 일치하는가?
- [ ] 하이픈이 폴더 구조와 맞는가?
- [ ] Layout 플러그인이 활성화되어 있는가?
- [ ] head.php / tail.php가 포함되어 있는가?
- [ ] PHP 오류가 발생하지 않는가?

---

## 🎓 핵심 개념 요약

1. **테마 기반 구조**: 모든 페이지는 `theme/{theme_dir}/{device}/` 디렉토리에 위치
2. **Page ID = 파일명**: Page ID가 파일 경로를 결정 (하이픈 → 폴더)
3. **자동 Layout 통합**: head/tail 자동 포함
4. **멀티디바이스**: mobile → pc fallback
5. **마이페이지 자동 감지**: 특정 조건에서 자동으로 마이페이지 모드 전환
6. **URL Rewrite 지원**: `/page/{page_id}/` 형태의 깔끔한 URL
7. **플러그인 통합**: Menu, Layout, Widget 등과 자연스럽게 연동

---

## 📚 관련 문서

- [Weaver 코어 가이드](weaver_core_guide.md)
- [Layout 플러그인 가이드](#)
- [Menu 플러그인 가이드](#)
- [Widget 플러그인 가이드](#)

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-10-01  
**작성자**: Claude AI