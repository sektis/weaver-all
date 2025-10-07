# 🎨 Layout 플러그인 가이드

> **Weaver 프로젝트의 통합 레이아웃 관리 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [핵심 개념](#핵심-개념)
3. [레이아웃 파일 구조](#레이아웃-파일-구조)
4. [레이아웃 선택 시스템](#레이아웃-선택-시스템)
5. [레이아웃 생성 방법](#레이아웃-생성-방법)
6. [Site Wrapper 시스템](#site-wrapper-시스템)
7. [주요 메서드](#주요-메서드)
8. [실전 활용 예시](#실전-활용-예시)
9. [고급 기능](#고급-기능)
10. [문제 해결](#문제-해결)

---

## 📌 개요

**Layout 플러그인**은 그누보드5의 표준 head/tail 시스템을 가로채서, 페이지별로 다른 레이아웃을 적용할 수 있는 강력한 시스템입니다.

### 핵심 특징

✅ **자동 head/tail 교체**: 그누보드 코어 수정 없이 레이아웃 변경  
✅ **페이지별 레이아웃**: 게시판, 콘텐츠, 페이지 등 타입별 다른 레이아웃  
✅ **레이아웃 분할**: `<!-->`로 header/content/footer 영역 분리  
✅ **Site Wrapper**: 전체 콘텐츠를 감싸는 래퍼 자동 추가  
✅ **멀티디바이스**: PC/Mobile 레이아웃 자동 분기  
✅ **하이픈 폴더 지원**: `layout_id='board-notice'` → `/theme/pc/board/notice.php`  
✅ **CSS/JS 자동 로드**: 레이아웃별 스타일/스크립트 자동 포함

---

## 🧩 핵심 개념

### 1. Layout ID

레이아웃을 식별하는 고유한 문자열입니다.

```php
// 기본값: 'common' (공통 레이아웃)
$layout_id = 'common';

// 게시판별 레이아웃
$layout_id = 'board-notice';    // 공지사항 게시판 전용

// 콘텐츠별 레이아웃
$layout_id = 'content-company';  // 회사소개 콘텐츠 전용

// 페이지별 레이아웃
$layout_id = 'page-landing';     // 랜딩 페이지 전용
```

### 2. Head / Tail 분리

그누보드의 `head.php`와 `tail.php`를 커스텀 레이아웃으로 교체합니다.

```
원본: include G5_PATH.'/head.php'
↓
교체: 
  ├─ head.sub.php (그누보드 기본)
  ├─ layout/head.php (커스텀 헤더)
  ├─ [페이지 콘텐츠]
  ├─ layout/tail.php (커스텀 푸터)
  └─ tail.sub.php (그누보드 기본)
```

### 3. <!--> 구분자

레이아웃 파일 내에서 header/content/footer 영역을 구분합니다.

```php
<!-- theme/basic/pc/common.php -->
<header>헤더 영역</header>
<!--> // 첫 번째 구분자: 여기까지 head 영역

[페이지 콘텐츠가 여기에 삽입됨]

<!--> // 두 번째 구분자: 여기서부터 tail 영역
<footer>푸터 영역</footer>
```

### 4. Site Wrapper

전체 콘텐츠를 감싸는 `<div id="site-wrapper">` 요소입니다.

```html
<body>
  <div id="site-wrapper">
    <!-- 헤더 -->
    <div id="content-wrapper">
      <!-- 페이지 콘텐츠 -->
    </div>
    <!-- 푸터 -->
  </div>
</body>
```

**용도**:
- 전체 레이아웃의 최상위 컨테이너
- CSS 스타일링의 기준점
- 반응형 레이아웃의 시작점

---

## 🗂️ 레이아웃 파일 구조

### 기본 구조

```
plugins/layout/
├── Layout.php              # 메인 클래스
├── layout_head.php         # Head 래퍼
├── layout_tail.php         # Tail 래퍼
├── layout_extend.php       # 공통 라이브러리
├── plugin.php              # 플러그인 로더
└── theme/
    └── basic/              # 테마 디렉토리
        ├── config.php      # 테마 설정 (선택)
        ├── pc/
        │   ├── head.php    # 공통 헤더
        │   ├── tail.php    # 공통 푸터
        │   ├── common.php  # 기본 레이아웃
        │   ├── board/      # 게시판 레이아웃들
        │   │   ├── notice.php
        │   │   └── free.php
        │   ├── content/    # 콘텐츠 레이아웃들
        │   │   └── company.php
        │   └── page/       # 페이지 레이아웃들
        │       └── landing.php
        └── mobile/
            ├── head.php
            ├── tail.php
            └── common.php
```

### 파일 네이밍 규칙

| Layout ID | 파일 경로 |
|-----------|-----------|
| `common` | `theme/basic/pc/common.php` |
| `board` | `theme/basic/pc/board.php` |
| `board-notice` | `theme/basic/pc/board/notice.php` |
| `content-company` | `theme/basic/pc/content/company.php` |
| `page-landing` | `theme/basic/pc/page/landing.php` |

### 필수 파일

1. **head.php**: 공통 헤더 (메뉴, 로고 등)
2. **tail.php**: 공통 푸터 (저작권, 링크 등)
3. **{layout_id}.php**: 레이아웃 파일 (선택적)

---

## 🎯 레이아웃 선택 시스템

### 선택 우선순위

Layout 플러그인은 다음 순서로 레이아웃을 자동 선택합니다:

```
1. type + 관련 변수 조합
   ↓
2. type (페이지 타입)
   ↓
3. dir (디렉토리명)
   ↓
4. 기본 레이아웃 (common)
```

### 타입별 관련 변수

| 타입 | 관련 변수 | 레이아웃 ID 예시 |
|------|-----------|------------------|
| `board` | `$bo_table` | `board-notice`, `board-free` |
| `content` | `$co_id` | `content-company`, `content-privacy` |
| `page` | `$wv_page_id` | `page-main`, `page-about` |

### 선택 예시

#### 게시판 페이지

```php
// URL: /bbs/board.php?bo_table=notice

// 레이아웃 ID 후보:
// 1. board-notice  (type + bo_table)
// 2. board         (type)
// 3. bbs           (dir)
// 4. common        (기본값)

// 실제 선택: theme/basic/pc/board/notice.php (있으면)
//           theme/basic/pc/board.php (없으면)
//           theme/basic/pc/common.php (최종)
```

#### 콘텐츠 페이지

```php
// URL: /bbs/content.php?co_id=company

// 레이아웃 ID 후보:
// 1. content-company
// 2. content
// 3. bbs
// 4. common

// 실제 선택: theme/basic/pc/content/company.php
```

#### 커스텀 페이지

```php
// URL: /?wv_page_id=landing

// 레이아웃 ID 후보:
// 1. page-landing
// 2. page
// 3. (dir 없음)
// 4. common

// 실제 선택: theme/basic/pc/page/landing.php
```

---

## 📝 레이아웃 생성 방법

### 1. 기본 레이아웃 (3파일 구조)

#### A. head.php (공통 헤더)

**파일**: `theme/basic/pc/head.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

$wv_layout_skin_url = wv_path_replace_url(dirname(__FILE__));
add_stylesheet('<link rel="stylesheet" href="'.$wv_layout_skin_url.'/layout.css?ver='.G5_CSS_VER.'">', 11);
add_javascript('<script src="'.$wv_layout_skin_url.'/layout.js?ver='.G5_JS_VER.'"></script>', 11);
?>

<header id="site-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="<?php echo G5_URL; ?>">
                    <img src="<?php echo G5_THEME_URL; ?>/img/logo.png" alt="Logo">
                </a>
            </div>
            <div class="col">
                <!-- 메인 메뉴 -->
                <?php if(wv_plugin_exists('menu')): ?>
                    <?php echo wv_widget('menu/main'); ?>
                <?php endif; ?>
            </div>
            <div class="col-auto">
                <!-- 사용자 메뉴 -->
                <?php if($member['mb_id']): ?>
                    <a href="<?php echo wv_page_url('mypage'); ?>">마이페이지</a>
                    <a href="<?php echo G5_BBS_URL; ?>/logout.php">로그아웃</a>
                <?php else: ?>
                    <a href="<?php echo G5_BBS_URL; ?>/login.php">로그인</a>
                    <a href="<?php echo G5_BBS_URL; ?>/register.php">회원가입</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
```

#### B. tail.php (공통 푸터)

**파일**: `theme/basic/pc/tail.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<footer id="site-footer" class="py-5 bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>회사 정보</h5>
                <p>
                    상호: <?php echo $config['cf_title']; ?><br>
                    주소: <?php echo $config['cf_addr']; ?><br>
                    전화: <?php echo $config['cf_phone']; ?>
                </p>
            </div>
            <div class="col-md-6">
                <h5>고객 지원</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo wv_page_url('faq'); ?>" class="text-white">자주 묻는 질문</a></li>
                    <li><a href="<?php echo wv_page_url('contact'); ?>" class="text-white">문의하기</a></li>
                    <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=privacy" class="text-white">개인정보처리방침</a></li>
                </ul>
            </div>
        </div>
        <hr class="border-light">
        <p class="text-center mb-0">
            &copy; <?php echo date('Y'); ?> <?php echo $config['cf_title']; ?>. All rights reserved.
        </p>
    </div>
</footer>
```

#### C. common.php (기본 레이아웃)

**파일**: `theme/basic/pc/common.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<!-- 헤더 영역 (head.php 내용) -->
<header>
    <p>공통 레이아웃 헤더</p>
</header>

<!--> <!-- 첫 번째 구분자: 여기까지 head -->

<!-- 페이지 콘텐츠가 이 위치에 삽입됩니다 -->

<!--> <!-- 두 번째 구분자: 여기부터 tail -->

<!-- 푸터 영역 (tail.php 내용) -->
<footer>
    <p>공통 레이아웃 푸터</p>
</footer>
```

**결과**:
```html
<body>
  <div id="site-wrapper">
    <header>공통 레이아웃 헤더</header>
    <div id="content-wrapper">
      [페이지 콘텐츠]
    </div>
    <footer>공통 레이아웃 푸터</footer>
  </div>
</body>
```

### 2. 게시판 전용 레이아웃

**파일**: `theme/basic/pc/board/notice.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<!-- 공지사항 게시판 전용 헤더 -->
<div class="notice-header bg-primary text-white py-3">
    <div class="container">
        <h1><i class="fa fa-bullhorn"></i> 공지사항</h1>
        <p>중요한 소식을 확인하세요</p>
    </div>
</div>

<!-->

<!-->

<!-- 공지사항 게시판 전용 푸터 -->
<div class="notice-footer bg-light py-3">
    <div class="container">
        <p class="mb-0">더 많은 정보는 고객센터를 이용해주세요</p>
    </div>
</div>
```

### 3. 랜딩 페이지 레이아웃 (헤더/푸터 없음)

**파일**: `theme/basic/pc/page/landing.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 헤더/푸터 사용 안 함
wv('layout')->set_use_header_footer(false);
?>

<!-- 랜딩 페이지는 헤더/푸터 없이 전체 화면 -->
<!-- <!--> 구분자가 없으면 레이아웃 전체가 콘텐츠 영역 -->

<div class="landing-page min-vh-100 d-flex align-items-center bg-gradient">
    <div class="container text-center text-white">
        <h1 class="display-1 fw-bold mb-4">환영합니다</h1>
        <p class="lead mb-5">최고의 서비스를 경험하세요</p>
        <a href="<?php echo G5_BBS_URL; ?>/register.php" class="btn btn-lg btn-light">
            지금 시작하기
        </a>
    </div>
</div>
```

**설정**:
```php
// set_use_header_footer(false) 호출 시:
// - head.php 사용 안 함
// - tail.php 사용 안 함
// - <!--> 구분자 무시
// - 레이아웃 파일 전체가 그대로 출력
```

### 4. 조건부 레이아웃

**파일**: `theme/basic/pc/board/free.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 회원 전용 게시판
if(!$member['mb_id']) {
    // 비회원은 다른 레이아웃 사용
    include dirname(__FILE__).'/free_guest.php';
    return;
}
?>

<!-- 회원 전용 헤더 -->
<div class="member-board-header">
    <h1>자유게시판 (회원 전용)</h1>
    <p>안녕하세요, <?php echo $member['mb_nick']; ?>님</p>
</div>

<!-->

<!-->

<div class="member-board-footer">
    <p>회원만 작성할 수 있습니다</p>
</div>
```

### 5. 중첩 레이아웃

**파일**: `theme/basic/pc/content/company.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<div class="company-layout">
    <!-- 회사소개 전용 서브 메뉴 -->
    <aside class="company-sidebar">
        <nav>
            <ul>
                <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=company">회사소개</a></li>
                <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=history">연혁</a></li>
                <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=location">오시는 길</a></li>
            </ul>
        </nav>
    </aside>
    
    <main class="company-content">
        <!--> <!-- 콘텐츠 삽입 -->
        <!-->
    </main>
</div>
```

---

## 🏗️ Site Wrapper 시스템

### Site Wrapper란?

전체 페이지를 감싸는 최상위 컨테이너입니다.

```html
<body>
  <div id="site-wrapper">
    <!-- 모든 콘텐츠 -->
  </div>
</body>
```

### 자동 추가 조건

Layout 플러그인은 다음 조건에서 자동으로 `site-wrapper`를 추가합니다:

1. **head.php가 없는 경우**
2. **관리자 페이지가 아닌 경우**
3. **`must_add_site_wrapper`가 true인 경우**

### 수동 제어

```php
// Site Wrapper 추가 안 함
wv('layout')->set_must_add_site_wrapper(false);

// Site Wrapper 추가
wv('layout')->set_must_add_site_wrapper(true);
```

### Site Wrapper 모드

**A모드**: body의 첫 요소가 `#site-wrapper`가 아니면 전체 랩핑

**B모드** (기본): body 내에 `#site-wrapper`가 하나라도 있으면 랩핑 안 함

```php
// Layout.php의 body_add_class() 메서드에서 설정
$MODE = 'B'; // 'A' 또는 'B'
```

---

## 🛠️ 주요 메서드

### Layout 클래스 메서드

```php
// Layout 인스턴스 가져오기
$layout = wv('layout');
```

#### 1. `set_layout_id($layout_id)`

레이아웃 ID를 강제로 설정합니다.

```php
// 특정 레이아웃 사용
wv('layout')->set_layout_id('custom');

// theme/basic/pc/custom.php 사용
```

#### 2. `set_use_header_footer($bool)`

head/tail 파일 사용 여부를 설정합니다.

```php
// 헤더/푸터 사용 안 함 (랜딩 페이지 등)
wv('layout')->set_use_header_footer(false);

// 헤더/푸터 사용 (기본값)
wv('layout')->set_use_header_footer(true);
```

#### 3. `set_must_add_site_wrapper($bool)`

Site Wrapper 자동 추가 여부를 설정합니다.

```php
// Site Wrapper 추가 안 함
wv('layout')->set_must_add_site_wrapper(false);

// Site Wrapper 추가 (기본값)
wv('layout')->set_must_add_site_wrapper(true);
```

#### 4. `get_layout_path()`

현재 선택된 레이아웃 파일의 전체 경로를 반환합니다.

```php
$path = wv('layout')->get_layout_path();
// 예: /home/user/public_html/plugin/weaver/plugins/layout/theme/basic/pc/common.php
```

#### 5. `get_layout_head_path()`

현재 head 파일의 전체 경로를 반환합니다.

```php
$head_path = wv('layout')->get_layout_head_path();
```

#### 6. `get_layout_tail_path()`

현재 tail 파일의 전체 경로를 반환합니다.

```php
$tail_path = wv('layout')->get_layout_tail_path();
```

#### 7. `image($file)`

레이아웃 플러그인의 이미지 URL을 반환합니다.

```php
// /plugin/weaver/plugins/layout/img/logo.png
$logo_url = wv('layout')->image('logo.png');
```

---

## 🎯 실전 활용 예시

### 1. 게시판별 다른 레이아웃

**시나리오**: 공지사항은 강조 헤더, 자유게시판은 일반 헤더

#### A. 공지사항 레이아웃

**파일**: `theme/basic/pc/board/notice.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<div class="notice-banner bg-danger text-white py-4">
    <div class="container">
        <h1><i class="fa fa-exclamation-circle"></i> 중요 공지사항</h1>
        <p>반드시 확인해주세요</p>
    </div>
</div>

<!-->

<!-->

<div class="notice-footer alert alert-warning">
    <i class="fa fa-info-circle"></i> 공지사항을 놓치지 마세요!
</div>
```

#### B. 자유게시판 레이아웃

**파일**: `theme/basic/pc/board/free.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<div class="board-header bg-light py-3">
    <div class="container">
        <h1>자유게시판</h1>
        <p>자유롭게 의견을 나누세요</p>
    </div>
</div>

<!-->

<!-->
```

### 2. 관리자/사용자 레이아웃 분기

**파일**: `theme/basic/pc/common.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 관리자 여부 확인
$is_admin = ($member['mb_id'] && $member['mb_level'] >= 10);
?>

<?php if($is_admin): ?>
    <!-- 관리자용 헤더 -->
    <header class="admin-header bg-dark text-white">
        <div class="container">
            <span class="badge bg-danger">관리자 모드</span>
            <a href="<?php echo G5_ADMIN_URL; ?>" class="text-white">관리자 페이지</a>
        </div>
    </header>
<?php else: ?>
    <!-- 일반 사용자 헤더 -->
    <header class="user-header">
        <div class="container">
            <!-- 일반 메뉴 -->
        </div>
    </header>
<?php endif; ?>

<!-->

<!-->

<footer class="site-footer">
    <!-- 공통 푸터 -->
</footer>
```

### 3. 반응형 레이아웃

**파일**: `theme/basic/pc/common.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

$wv_layout_skin_url = wv_path_replace_url(dirname(__FILE__));
?>

<style>
/* PC 레이아웃 */
@media (min-width: 992px) {
    #site-header .container {
        display: flex;
        justify-content: space-between;
    }
    
    #site-header nav {
        display: block !important;
    }
    
    #mobile-menu-button {
        display: none;
    }
}

/* 모바일 레이아웃 */
@media (max-width: 991.98px) {
    #site-header nav {
        display: none;
        position: fixed;
        top: 60px;
        left: 0;
        right: 0;
        background: white;
        z-index: 1000;
    }
    
    #site-header nav.active {
        display: block;
    }
    
    #mobile-menu-button {
        display: block;
    }
}
</style>

<header id="site-header">
    <div class="container">
        <a href="<?php echo G5_URL; ?>">로고</a>
        
        <button id="mobile-menu-button" class="btn btn-link">
            <i class="fa fa-bars"></i>
        </button>
        
        <nav>
            <?php echo wv_widget('menu/main'); ?>
        </nav>
    </div>
</header>

<!-->

<!-->

<footer id="site-footer">
    <div class="container">
        <p>&copy; 2025 Company</p>
    </div>
</footer>

<script>
$(document).ready(function(){
    $('#mobile-menu-button').click(function(){
        $('nav').toggleClass('active');
    });
});
</script>
```

### 4. 페이지별 다른 네비게이션

**파일**: `theme/basic/pc/common.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 현재 페이지 타입 확인
$page_type = wv_info('type');
$layout_id = wv('layout')->layout_id;
?>

<header id="site-header">
    <div class="container">
        <?php if($page_type == 'index'): ?>
            <!-- 메인 페이지: 간단한 메뉴 -->
            <nav>
                <a href="<?php echo wv_page_url('about'); ?>">소개</a>
                <a href="<?php echo wv_page_url('services'); ?>">서비스</a>
                <a href="<?php echo wv_page_url('contact'); ?>">문의</a>
            </nav>
        <?php elseif($page_type == 'board'): ?>
            <!-- 게시판: 게시판 메뉴 -->
            <nav>
                <a href="/bbs/board.php?bo_table=notice">공지사항</a>
                <a href="/bbs/board.php?bo_table=free">자유게시판</a>
                <a href="/bbs/board.php?bo_table=qna">Q&A</a>
            </nav>
        <?php else: ?>
            <!-- 기타: 전체 메뉴 -->
            <?php echo wv_widget('menu/main'); ?>
        <?php endif; ?>
    </div>
</header>

<!-->

<!-->
```

### 5. 쇼핑몰/일반 사이트 레이아웃 분기

**파일**: `theme/basic/pc/common.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 쇼핑몰 페이지인지 확인
$is_shop = (strpos($_SERVER['REQUEST_URI'], '/shop/') !== false);
?>

<?php if($is_shop): ?>
    <!-- 쇼핑몰 레이아웃 -->
    <header class="shop-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-3">
                    <a href="<?php echo G5_SHOP_URL; ?>">쇼핑몰 로고</a>
                </div>
                <div class="col-6">
                    <!-- 상품 검색 -->
                    <form action="<?php echo G5_SHOP_URL; ?>/search.php">
                        <input type="text" name="q" placeholder="상품 검색">
                    </form>
                </div>
                <div class="col-3 text-end">
                    <a href="<?php echo G5_SHOP_URL; ?>/cart.php">
                        <i class="fa fa-shopping-cart"></i> 장바구니
                    </a>
                </div>
            </div>
        </div>
    </header>
<?php else: ?>
    <!-- 일반 사이트 레이아웃 -->
    <header class="site-header">
        <div class="container">
            <a href="<?php echo G5_URL; ?>">사이트 로고</a>
            <?php echo wv_widget('menu/main'); ?>
        </div>
    </header>
<?php endif; ?>

<!-->

<!-->
```

---

## 🔗 다른 플러그인과 통합

### 1. Menu 플러그인

**레이아웃에서 메뉴 위젯 사용**:

```php
<!-- theme/basic/pc/head.php -->
<header>
    <div class="container">
        <a href="<?php echo G5_URL; ?>">로고</a>
        
        <!-- 메인 메뉴 -->
        <?php if(wv_plugin_exists('menu')): ?>
            <?php echo wv_widget('menu/main'); ?>
        <?php endif; ?>
    </div>
</header>
```

### 2. Page 플러그인

**페이지별 레이아웃 자동 선택**:

```php
// Page ID: landing
// Layout ID: page-landing
// 파일: theme/basic/pc/page/landing.php

// 자동으로 랜딩 페이지 전용 레이아웃 사용
```

### 3. Widget 플러그인

**레이아웃에 위젯 삽입**:

```php
<!-- theme/basic/pc/tail.php -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <!-- 최근 게시물 위젯 -->
                <?php echo wv_widget('board/latest', array('bo_table' => 'notice')); ?>
            </div>
            <div class="col-md-4">
                <!-- 인기 게시물 위젯 -->
                <?php echo wv_widget('board/popular'); ?>
            </div>
            <div class="col-md-4">
                <!-- 배너 위젯 -->
                <?php echo wv_widget('banner/sidebar'); ?>
            </div>
        </div>
    </div>
</footer>
```

---

## 🎨 고급 기능

### 1. 동적 레이아웃 전환

```php
// config.php 또는 특정 파일에서
if(date('H') >= 22 || date('H') < 6) {
    // 밤 10시 ~ 새벽 6시: 다크 모드 레이아웃
    wv('layout')->set_layout_id('dark');
} else {
    // 주간: 일반 레이아웃
    wv('layout')->set_layout_id('common');
}
```

### 2. A/B 테스트 레이아웃

```php
// 사용자 ID 기반 A/B 테스트
if($member['mb_id']) {
    $user_id_last_digit = substr($member['mb_no'], -1);
    
    if($user_id_last_digit % 2 == 0) {
        // A안 레이아웃
        wv('layout')->set_layout_id('variant-a');
    } else {
        // B안 레이아웃
        wv('layout')->set_layout_id('variant-b');
    }
}
```

### 3. 이벤트 기반 레이아웃

```php
// config.php
$event_start = '2025-12-01';
$event_end = '2025-12-31';
$is_christmas_event = (G5_TIME_YMDHIS >= $event_start && G5_TIME_YMDHIS <= $event_end);

if($is_christmas_event) {
    wv('layout')->set_layout_id('christmas');
}
```

### 4. 레이아웃 상속

**베이스 레이아웃**: `theme/basic/pc/base.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>

<header class="site-header">
    <?php if(isset($custom_header)): ?>
        <?php echo $custom_header; ?>
    <?php else: ?>
        <!-- 기본 헤더 -->
        <p>기본 헤더</p>
    <?php endif; ?>
</header>

<!-->

<!-->

<footer class="site-footer">
    <p>기본 푸터</p>
</footer>
```

**확장 레이아웃**: `theme/basic/pc/board/notice.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 베이스 레이아웃에 전달할 변수
$custom_header = '<div class="notice-custom-header">공지사항 전용 헤더</div>';

// 베이스 레이아웃 포함
include dirname(__DIR__).'/base.php';
?>
```

### 5. 조건부 CSS/JS 로딩

```php
<!-- theme/basic/pc/head.php -->
<?php
$wv_layout_skin_url = wv_path_replace_url(dirname(__FILE__));

// 기본 CSS/JS
add_stylesheet('<link rel="stylesheet" href="'.$wv_layout_skin_url.'/layout.css?ver='.G5_CSS_VER.'">', 11);
add_javascript('<script src="'.$wv_layout_skin_url.'/layout.js?ver='.G5_JS_VER.'"></script>', 11);

// 페이지 타입별 추가 CSS/JS
if(wv_info('type') == 'board') {
    add_stylesheet('<link rel="stylesheet" href="'.$wv_layout_skin_url.'/board.css?ver='.G5_CSS_VER.'">', 12);
}

if(wv_info('type') == 'shop_index' || strpos($_SERVER['REQUEST_URI'], '/shop/') !== false) {
    add_stylesheet('<link rel="stylesheet" href="'.$wv_layout_skin_url.'/shop.css?ver='.G5_CSS_VER.'">', 12);
}
?>
```

---

## 🐛 문제 해결

### 레이아웃이 적용되지 않을 때

#### 1. Layout 플러그인 활성화 확인

```php
if(wv_plugin_exists('layout')) {
    echo "✅ Layout 플러그인 활성화됨";
} else {
    echo "❌ Layout 플러그인 없음";
}
```

#### 2. 테마 디렉토리 확인

```php
// 현재 테마 경로 확인
var_dump(wv('layout')->plugin_theme_path);

// 예상 경로:
// /home/user/public_html/plugin/weaver/plugins/layout/theme/basic/pc
```

#### 3. 레이아웃 파일 존재 확인

```bash
# 서버에서 확인
ls -la plugins/layout/theme/basic/pc/

# 필수 파일 확인
- head.php
- tail.php
- common.php (또는 다른 레이아웃 파일)
```

#### 4. 레이아웃 ID 확인

```php
// 현재 Layout ID 확인
global $g5;
$layout_id = wv('layout')->layout_id;
var_dump($layout_id);

// body 속성에서도 확인 가능
// <body wv-layout-id="common">
```

### <!--> 구분자가 작동하지 않을 때

#### 1. 구분자 위치 확인

```php
// 올바른 사용
<header>헤더</header>
<!--> // 첫 번째 구분자
[콘텐츠]
<!--> // 두 번째 구분자
<footer>푸터</footer>

// 잘못된 사용
<header>헤더</header>
<!--> <!--> // 연속된 구분자 (X)
```

#### 2. 공백 확인

```php
// <!--> 앞뒤로 공백 있어도 OK
<?php echo $something; ?>
<!--> 

<!-- 이렇게도 OK -->
    <!-->
```

### Site Wrapper가 중복될 때

#### 1. 수동 제어

```php
// 레이아웃 파일 상단에서
wv('layout')->set_must_add_site_wrapper(false);
```

#### 2. 모드 변경

```php
// Layout.php의 body_add_class() 메서드에서
$MODE = 'B'; // B모드로 변경 (기본값)

// B모드: body 내에 이미 #site-wrapper가 있으면 추가 안 함
```

### 모바일 레이아웃이 적용되지 않을 때

#### 1. Mobile 디렉토리 확인

```bash
# mobile 디렉토리 존재 확인
ls -la plugins/layout/theme/basic/mobile/

# 없으면 자동으로 PC 레이아웃 사용
```

#### 2. 강제 디바이스 지정

```php
// PC 레이아웃 강제 사용
define('G5_SET_DEVICE', 'pc');

// Mobile 레이아웃 강제 사용
define('G5_SET_DEVICE', 'mobile');
```

### CSS/JS가 로드되지 않을 때

#### 1. 경로 확인

```php
// head.php에서
$wv_layout_skin_url = wv_path_replace_url(dirname(__FILE__));
var_dump($wv_layout_skin_url);

// 예상: /plugin/weaver/plugins/layout/theme/basic/pc
```

#### 2. 파일 존재 확인

```bash
# CSS/JS 파일 확인
ls -la plugins/layout/theme/basic/pc/layout.css
ls -la plugins/layout/theme/basic/pc/layout.js
```

#### 3. 관리자 페이지 제외

```php
// layout_head.php에서
if(wv_info('type')!='admin' and !wv_is_ajax()){
    add_stylesheet('...', 99);
    add_javascript('...', 99);
}
```

---

## 💡 베스트 프랙티스

### 1. 레이아웃 파일 구조화

```
theme/basic/pc/
├── head.php           # 공통 헤더
├── tail.php           # 공통 푸터
├── common.php         # 기본 레이아웃
├── base.php           # 베이스 레이아웃 (상속용)
├── board/             # 게시판 레이아웃들
│   ├── _common.php    # 게시판 공통
│   ├── notice.php
│   └── free.php
├── content/           # 콘텐츠 레이아웃들
│   └── company.php
└── page/              # 페이지 레이아웃들
    ├── landing.php
    └── mypage.php
```

### 2. 레이아웃 네이밍

- **기본**: `common.php`
- **타입별**: `board.php`, `content.php`, `page.php`
- **타입+ID**: `board-notice.php`, `page-landing.php`
- **공통 베이스**: `base.php`, `_common.php`

### 3. CSS 변수 활용

```css
/* theme/basic/pc/layout.css */
:root {
    --layout-header-height: 80px;
    --layout-footer-height: 200px;
    --layout-sidebar-width: 250px;
    --layout-max-width: 1200px;
}

#site-header {
    height: var(--layout-header-height);
}

#content-wrapper {
    min-height: calc(100vh - var(--layout-header-height) - var(--layout-footer-height));
    max-width: var(--layout-max-width);
}
```

### 4. 반응형 유틸리티

```php
<!-- head.php -->
<script>
// 반응형 브레이크포인트 감지
window.wv_breakpoint = function() {
    var width = window.innerWidth;
    if(width < 576) return 'xs';
    if(width < 768) return 'sm';
    if(width < 992) return 'md';
    if(width < 1200) return 'lg';
    return 'xl';
};

// Body에 브레이크포인트 클래스 추가
$(window).on('resize load', function(){
    $('body').attr('data-breakpoint', wv_breakpoint());
});
</script>
```

### 5. 레이아웃 디버깅

```php
// config.php에서
if($member['mb_id'] == 'admin') {
    // 관리자에게만 레이아웃 정보 표시
    add_event('wv_hook_before_header_wrapper', function(){
        echo '<div style="background:yellow; padding:10px; position:fixed; top:0; right:0; z-index:9999;">';
        echo 'Layout ID: ' . wv('layout')->layout_id . '<br>';
        echo 'Type: ' . wv_info('type') . '<br>';
        echo 'Device: ' . (G5_IS_MOBILE ? 'Mobile' : 'PC');
        echo '</div>';
    });
}
```

---

## 📊 레이아웃 라이프사이클

```
1. 페이지 요청
   ↓
2. Layout 플러그인 초기화
   ↓
3. Layout ID 결정
   - type + 관련 변수
   - type
   - dir
   - common (기본값)
   ↓
4. 레이아웃 파일 검색
   - mobile 디렉토리 (모바일인 경우)
   - pc 디렉토리 (fallback)
   ↓
5. head.php / tail.php 검색
   ↓
6. 원본 코드 교체
   - include head.php → layout_head.php
   - include tail.php → layout_tail.php
   ↓
7. 레이아웃 파일 파싱
   - <!--> 구분자로 head/content/tail 분리
   ↓
8. 렌더링
   - head.sub.php
   - head.php (레이아웃 헤더)
   - [페이지 콘텐츠]
   - tail.php (레이아웃 푸터)
   - tail.sub.php
   ↓
9. Site Wrapper 추가 (필요시)
   ↓
10. 응답 전송
```

---

## 📋 체크리스트

### 새 레이아웃 만들 때

- [ ] Layout ID 결정 (`common`, `board-notice` 등)
- [ ] 파일 생성 (`theme/basic/pc/{layout_id}.php`)
- [ ] 그누보드 체크 (`if (!defined('_GNUBOARD_')) exit;`)
- [ ] <!--> 구분자 사용 (head/content/tail 분리)
- [ ] CSS/JS 추가 (`add_stylesheet`, `add_javascript`)
- [ ] 모바일 버전 필요 시 `mobile/` 디렉토리에도 생성
- [ ] head.php / tail.php 수정 (공통 헤더/푸터)
- [ ] 디바이스별 테스트 (PC/Mobile)
- [ ] 다른 페이지 타입에서도 테스트

### 문제 발생 시 체크

- [ ] Layout 플러그인이 활성화되어 있는가?
- [ ] 레이아웃 파일 경로가 올바른가?
- [ ] 파일 권한이 올바른가? (644)
- [ ] <!--> 구분자가 올바르게 사용되었는가?
- [ ] head.php / tail.php가 존재하는가?
- [ ] CSS/JS 경로가 올바른가?
- [ ] Site Wrapper 중복은 없는가?
- [ ] PHP 오류가 발생하지 않는가?

---

## 🎓 핵심 개념 요약

1. **자동 교체 시스템**: 그누보드 head/tail을 가로채서 커스텀 레이아웃으로 교체
2. **Layout ID = 파일명**: Layout ID가 파일 경로를 결정 (하이픈 → 폴더)
3. **<!--> 구분자**: 레이아웃 파일을 head/content/tail로 분할
4. **자동 선택**: type + 관련 변수 조합으로 적절한 레이아웃 자동 선택
5. **Site Wrapper**: 전체 콘텐츠를 감싸는 최상위 컨테이너
6. **멀티디바이스**: mobile → pc fallback 지원
7. **플러그인 통합**: Menu, Page, Widget 등과 자연스럽게 연동
8. **유연성**: 조건부 레이아웃, 동적 전환, 상속 등 고급 기능 지원

---

## 📚 관련 문서

- [Weaver 코어 가이드](weaver_core_guide.md)
- [Page 플러그인 가이드](page_guide.md)
- [Menu 플러그인 가이드](#)
- [Widget 플러그인 가이드](#)

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-10-01  
**작성자**: Claude AI