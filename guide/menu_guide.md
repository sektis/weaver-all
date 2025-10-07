# 🧭 Menu 플러그인 가이드

> **Weaver 프로젝트의 계층형 메뉴 관리 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [Menu 시스템 구조](#menu-시스템-구조)
3. [메뉴 생성 및 관리](#메뉴-생성-및-관리)
4. [메뉴 스킨 제작](#메뉴-스킨-제작)
5. [헬퍼 함수](#헬퍼-함수)
6. [실전 패턴](#실전-패턴)
7. [고급 활용](#고급-활용)
8. [문제 해결](#문제-해결)

---

## 📌 개요

**Menu 플러그인**은 Weaver 프로젝트에서 계층형 메뉴 구조를 쉽게 만들고 관리할 수 있게 해주는 시스템입니다.

### 핵심 특징

✅ **다중 Depth 지원**: 무제한 깊이의 계층 메뉴  
✅ **활성 메뉴 자동 감지**: 현재 페이지에 맞는 메뉴 자동 활성화  
✅ **유연한 메뉴 구조**: append, prepend, insertBefore, insertAfter  
✅ **다양한 스킨**: 드롭다운, 메가메뉴, 스와이퍼, Collapse 등  
✅ **Breadcrumb/Navigation**: 빵 부스러기, 네비게이션 자동 생성  
✅ **URL 중복 처리**: wvd 파라미터로 동일 URL 메뉴 구분

### 주요 용도

- **헤더 메뉴**: GNB (Global Navigation Bar)
- **사이드바 메뉴**: LNB (Local Navigation Bar)
- **Breadcrumb**: 현재 위치 표시
- **Sitemap**: 전체 메뉴 구조 표시
- **Admin 메뉴**: 관리자 메뉴

---

## 🏗️ Menu 시스템 구조

### 디렉토리 구조

```
plugins/menu/
├── Menu.php                # 메인 클래스
├── menu.lib.php            # 헬퍼 함수
├── plugin.php              # 플러그인 로더
└── theme/
    └── basic/              # 기본 테마
        ├── hover/          # 호버 메뉴
        │   └── mega/
        │       └── skin.php
        ├── depth/          # Depth 메뉴
        │   └── skin.php
        └── swiper/         # Swiper 메뉴
            └── skin.php
```

### Menu 클래스 구조

```php
namespace weaver;

class Menu extends Makeable {
    
    protected $menu = array();              // 메뉴 배열
    protected $arr_ref = array();           // 메뉴 참조 배열
    protected $arr_parent_ref = array();    // 부모 참조 배열
    protected $arr_id = array();            // ID 목록
    protected $arr_url = array();           // URL 목록
    protected $active_id = '';              // 활성 메뉴 ID
    protected $expand_ids = array();        // 확장된 메뉴 ID들
    
    // 메뉴 추가 메서드
    public function append($menu_array, $parent_id='')
    public function prepend($menu_array, $parent_id='')
    public function insertAfter($menu_array, $sibling_id)
    public function insertBefore($menu_array, $sibling_id)
    
    // 메뉴 조회/표시
    public function getMenu($id='')
    public function displayMenu($skin, $menu_id='', $re_order=true, $option=array())
    public function displaySubMenu($skin, $re_order=true)
    public function displayBreadcrumb($skin, $re_order=true)
    public function displayNavigation($skin, $re_order=true)
    
    // 활성 메뉴
    public function getActiveMenuId()
    public function getParentId($current_id='', $top_parent=false)
}
```

### 메뉴 배열 구조

```php
array(
    'id' => 'menu1',              // 필수: 고유 ID
    'name' => '메뉴명',            // 필수: 표시 이름
    'url' => '/page/main/',       // 필수: 연결 URL
    'target' => '_self',          // 선택: 링크 타겟
    'order' => 0,                 // 선택: 정렬 순서
    'icon' => '/img/icon.png',    // 선택: 아이콘
    'active' => false,            // 자동: 활성 여부
    'expand' => false,            // 자동: 확장 여부
    'sub' => array(               // 선택: 하위 메뉴
        array(
            'id' => 'menu1-1',
            'name' => '서브메뉴',
            'url' => '/page/sub/',
            // ...
        )
    )
)
```

---

## 🔧 메뉴 생성 및 관리

### 1. 기본 메뉴 생성

```php
<?php
// 메뉴 인스턴스 생성 (Makeable)
$menu = wv()->menu->make('main_menu');

// 메뉴 추가
$menu->append(array(
    'id' => 'home',
    'name' => '홈',
    'url' => '/'
));

$menu->append(array(
    'id' => 'about',
    'name' => '회사소개',
    'url' => '/page/about/'
));

$menu->append(array(
    'id' => 'service',
    'name' => '서비스',
    'url' => '/page/service/'
));

// 메뉴 표시
echo $menu->displayMenu('depth');
?>
```

### 2. 계층형 메뉴 생성

#### 방법 1: sub 배열 사용

```php
$menu->append(array(
    'id' => 'company',
    'name' => '회사소개',
    'url' => '/page/company/',
    'sub' => array(
        array(
            'id' => 'company-intro',
            'name' => '회사개요',
            'url' => '/page/company/intro/'
        ),
        array(
            'id' => 'company-history',
            'name' => '연혁',
            'url' => '/page/company/history/'
        ),
        array(
            'id' => 'company-vision',
            'name' => '비전',
            'url' => '/page/company/vision/'
        )
    )
));
```

#### 방법 2: parent_id 사용

```php
// 부모 메뉴 추가
$menu->append(array(
    'id' => 'company',
    'name' => '회사소개',
    'url' => '/page/company/'
));

// 자식 메뉴 추가
$menu->append(array(
    'id' => 'company-intro',
    'name' => '회사개요',
    'url' => '/page/company/intro/'
), 'company');  // parent_id

$menu->append(array(
    'id' => 'company-history',
    'name' => '연혁',
    'url' => '/page/company/history/'
), 'company');
```

### 3. 메뉴 위치 제어

```php
// append: 마지막에 추가 (기본)
$menu->append($menu_array, $parent_id);

// prepend: 맨 앞에 추가
$menu->prepend($menu_array, $parent_id);

// insertAfter: 특정 메뉴 뒤에 추가
$menu->insertAfter($menu_array, $sibling_id);

// insertBefore: 특정 메뉴 앞에 추가
$menu->insertBefore($menu_array, $sibling_id);
```

**예시:**

```php
// 기존 메뉴
$menu->append(array('id' => 'menu1', 'name' => '메뉴1', 'url' => '/1/'));
$menu->append(array('id' => 'menu3', 'name' => '메뉴3', 'url' => '/3/'));

// menu1과 menu3 사이에 menu2 추가
$menu->insertAfter(array(
    'id' => 'menu2',
    'name' => '메뉴2',
    'url' => '/2/'
), 'menu1');

// 결과: menu1 → menu2 → menu3
```

### 4. 메뉴 정렬

```php
// order 값으로 자동 정렬
$menu->append(array(
    'id' => 'menu1',
    'name' => '메뉴1',
    'url' => '/1/',
    'order' => 2
));

$menu->append(array(
    'id' => 'menu2',
    'name' => '메뉴2',
    'url' => '/2/',
    'order' => 1
));

$menu->append(array(
    'id' => 'menu3',
    'name' => '메뉴3',
    'url' => '/3/',
    'order' => 3
));

// displayMenu 시 자동 정렬: menu2 → menu1 → menu3
```

---

## 🎨 메뉴 스킨 제작

### 스킨 기본 구조

**파일**: `plugins/menu/theme/basic/depth/skin.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-menu-skin">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .depth-wrap-1 {}
        <?php echo $skin_selector?> .depth-ul-1 {}
        <?php echo $skin_selector?> .depth-li-1 {}
        <?php echo $skin_selector?> .depth-link-1 {}
        
        <?php echo $skin_selector?> .depth-wrap-2 {}
        <?php echo $skin_selector?> .depth-ul-2 {}
        <?php echo $skin_selector?> .depth-li-2 {}
        <?php echo $skin_selector?> .depth-link-2 {}
        
        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <?php echo wv_depth_menu(null, $skin_id, $data, 99); ?>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            
            // 메뉴 초기화 로직
        });
    </script>
</div>
```

### wv_depth_menu() 헬퍼 함수

```php
/**
 * Depth 메뉴 렌더링
 * @param callable $callback 커스텀 처리 콜백
 * @param string $curr_id 현재 ID
 * @param array $data 메뉴 데이터
 * @param int $max_depth 최대 깊이
 * @param int $depth 현재 깊이
 * @param string $parent_menu_id 부모 메뉴 ID
 * @return string
 */
function wv_depth_menu($callback, $curr_id, $data, $max_depth='99', $depth=1, $parent_menu_id='')
```

**출력 HTML 구조:**

```html
<div class="depth-wrap-1" data-depth="1">
    <ul class="depth-ul-1">
        <li class="depth-li-1">
            <a class="depth-link-1 active" 
               href="/page/main/" 
               data-menu-id="menu1" 
               data-depth="1">
                <span>메뉴명</span>
                <span class="wv-hover-box">메뉴명</span>
            </a>
            
            <!-- 하위 메뉴 (depth-2) -->
            <div class="depth-wrap-2" data-depth="2">
                <ul class="depth-ul-2">
                    <li class="depth-li-2">
                        <a class="depth-link-2" 
                           href="/page/sub/" 
                           data-menu-id="menu1-1" 
                           data-depth="2">
                            <span>서브메뉴</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>
```

### CSS 클래스 패턴

| 클래스명 | 설명 |
|----------|------|
| `.depth-wrap-{N}` | Depth별 래퍼 |
| `.depth-ul-{N}` | Depth별 리스트 |
| `.depth-li-{N}` | Depth별 아이템 |
| `.depth-link-{N}` | Depth별 링크 |
| `.active` | 활성 메뉴 |
| `.expand` | 확장된 메뉴 (자식이 활성일 때) |

### 스킨 예시: 드롭다운 메뉴

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-dropdown-menu">
    <style>
        <?php echo $skin_selector?> .depth-wrap-1 {display: flex; gap: var(--wv-20);}
        <?php echo $skin_selector?> .depth-ul-1 {display: flex; list-style: none; padding: 0; margin: 0;}
        <?php echo $skin_selector?> .depth-li-1 {position: relative;}
        <?php echo $skin_selector?> .depth-link-1 {padding: var(--wv-12) var(--wv-16); display: block; text-decoration: none; color: #333;}
        <?php echo $skin_selector?> .depth-link-1:hover, <?php echo $skin_selector?> .depth-link-1.active {background: #007bff; color: white;}
        
        /* 서브메뉴 */
        <?php echo $skin_selector?> .depth-wrap-2 {position: absolute; top: 100%; left: 0; display: none; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); min-width: var(--wv-200); z-index: 1000;}
        <?php echo $skin_selector?> .depth-li-1:hover .depth-wrap-2 {display: block;}
        <?php echo $skin_selector?> .depth-ul-2 {list-style: none; padding: 0; margin: 0;}
        <?php echo $skin_selector?> .depth-link-2 {padding: var(--wv-10) var(--wv-16); display: block; color: #333; text-decoration: none;}
        <?php echo $skin_selector?> .depth-link-2:hover {background: #f8f9fa;}
    </style>

    <?php echo wv_depth_menu(null, $skin_id, $data, 2); ?>
</div>
```

### 스킨 예시: 메가메뉴

```php
<?php
if (!defined('_GNUBOARD_')) exit;
$sub_contents = '';
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-mega-menu">
    <style>
        <?php echo $skin_selector?> .depth-wrap-1 {height: 100%;}
        <?php echo $skin_selector?> .depth-ul-1 {display: flex; gap: var(--wv-20); height: 100%;}
        <?php echo $skin_selector?> .depth-li-1 {position: relative;}
        <?php echo $skin_selector?> .depth-link-1 {display: flex; align-items: center; height: 100%; padding: 0 var(--wv-16); font-size: var(--wv-18); font-weight: 500;}
        
        /* 메가메뉴 배경 */
        <?php echo $skin_selector?> .menu-background {position: absolute; top: 100%; left: 0; width: 100%; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.1); opacity: 0; visibility: hidden; transition: all 0.3s;}
        <?php echo $skin_selector?> .menu-background.show {opacity: 1; visibility: visible;}
        
        /* 서브메뉴 */
        <?php echo $skin_selector?> .depth-wrap-2 {position: absolute; opacity: 0; visibility: hidden; transition: opacity 0.3s;}
        <?php echo $skin_selector?> .depth-wrap-2.show {opacity: 1; visibility: visible;}
        <?php echo $skin_selector?> .depth-ul-2 {display: flex; gap: var(--wv-40); padding: var(--wv-30);}
        <?php echo $skin_selector?> .depth-link-2 {font-size: var(--wv-16); padding: var(--wv-8) 0;}
    </style>

    <?php
    // 1depth만 표시하고, 2depth는 따로 수집
    echo wv_depth_menu(function ($depth, $content, $curr_id) use(&$sub_contents){
        if($depth == 2){
            $sub_contents .= $content;
            return false;  // 2depth는 원래 위치에 표시 안 함
        }
    }, $skin_id, $data, 2);
    ?>

    <!-- 메가메뉴 배경 (모든 2depth 포함) -->
    <div class="menu-background">
        <div class="container">
            <?php echo $sub_contents; ?>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $('<?php echo $skin_selector?>');
            var $depth1_links = $('.depth-link-1', $skin);
            var $menu_background = $('.menu-background', $skin);
            var hovering = null;

            $depth1_links.mouseenter(function(){
                clearTimeout(hovering);
                var menu_id = $(this).data('menu-id');
                var $sub_content = $('[data-parent-menu-id="'+menu_id+'"]', $skin);
                
                // 모든 서브메뉴 숨기기
                $('.depth-wrap-2', $skin).removeClass('show');
                
                // 해당 서브메뉴만 표시
                if($sub_content.length){
                    $menu_background.addClass('show');
                    $sub_content.addClass('show');
                }
            });

            $skin.mouseleave(function(){
                hovering = setTimeout(function(){
                    $menu_background.removeClass('show');
                    $('.depth-wrap-2', $skin).removeClass('show');
                }, 300);
            });
        });
    </script>
</div>
```

---

## 🛠️ 헬퍼 함수

### wv_depth_menu()

일반적인 Depth 메뉴를 렌더링합니다.

```php
wv_depth_menu($callback, $curr_id, $data, $max_depth='99', $depth=1, $parent_menu_id='')
```

**콜백 활용:**

```php
echo wv_depth_menu(function($depth, $content, $curr_id, $data){
    if($depth == 1){
        // 1depth 커스터마이징
        return '<div class="custom-wrap">' . $content . '</div>';
    }
    
    if($depth == 3){
        // 3depth 이상은 표시 안 함
        return false;
    }
    
    // 그 외는 원본 사용
    return true;
}, $skin_id, $data, 5);
```

### wv_swiper_menu()

Swiper 슬라이더 형태의 메뉴를 렌더링합니다.

```php
wv_swiper_menu($callback, $curr_id, $data, $max_depth='99', $depth=1, $parent_menu_id='')
```

**출력:**

```html
<div class="swiper depth-wrap-1">
    <div class="swiper-wrapper depth-ul-1">
        <div class="swiper-slide depth-li-1">
            <a class="depth-link-1" href="...">메뉴명</a>
        </div>
    </div>
</div>
```

---

## 💡 실전 패턴

### 1. 활성 메뉴 감지 및 스타일링

```php
<?php
// 메뉴 표시
echo $menu->displayMenu('depth');
?>

<style>
/* active 클래스가 자동으로 추가됨 */
.depth-link-1.active {
    background: #007bff;
    color: white;
}

/* expand 클래스 (하위에 active가 있을 때) */
.depth-link-1.expand {
    font-weight: bold;
}
</style>
```

### 2. Breadcrumb (빵 부스러기)

```php
<?php
$menu = wv()->menu->make('main_menu');

// 메뉴 구성
$menu->append(array('id' => 'home', 'name' => '홈', 'url' => '/'));
$menu->append(array(
    'id' => 'company',
    'name' => '회사소개',
    'url' => '/page/company/',
    'sub' => array(
        array('id' => 'intro', 'name' => '회사개요', 'url' => '/page/company/intro/')
    )
));

// Breadcrumb 표시
echo $menu->displayBreadcrumb('breadcrumb');
?>
```

**Breadcrumb 스킨**: `plugins/menu/theme/basic/breadcrumb/skin.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> breadcrumb-menu">
    <style>
        <?php echo $skin_selector?> {display: flex; align-items: center; gap: var(--wv-8);}
        <?php echo $skin_selector?> a {color: #666; text-decoration: none;}
        <?php echo $skin_selector?> a:hover {color: #007bff;}
        <?php echo $skin_selector?> .separator {color: #ccc;}
        <?php echo $skin_selector?> .current {color: #007bff; font-weight: 500;}
    </style>

    <?php foreach($data as $i => $menu): ?>
        <?php if($i > 0): ?>
            <span class="separator">›</span>
        <?php endif; ?>
        
        <?php if($menu['active']): ?>
            <span class="current"><?php echo $menu['name']; ?></span>
        <?php else: ?>
            <a href="<?php echo $menu['url']; ?>"><?php echo $menu['name']; ?></a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
```

### 3. 서브메뉴만 표시

```php
<?php
// 현재 활성 메뉴의 서브메뉴만 표시
echo $menu->displaySubMenu('depth');

// 또는 특정 메뉴의 서브메뉴 표시
echo $menu->displayMenu('depth', 'company');  // company 메뉴의 하위만
?>
```

### 4. 모바일 메뉴 (Offcanvas)

```php
<!-- 모바일 메뉴 버튼 -->
<button class="btn d-lg-none" 
        data-bs-toggle="offcanvas" 
        data-bs-target="#mobile-menu">
    <i class="fa fa-bars"></i>
</button>

<!-- Offcanvas 메뉴 -->
<div class="offcanvas offcanvas-start" id="mobile-menu">
    <div class="offcanvas-header">
        <h5>메뉴</h5>
        <button type="button" 
                class="btn-close" 
                data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <?php echo $menu->displayMenu('depth'); ?>
    </div>
</div>

<style>
/* 모바일 메뉴 스타일 */
#mobile-menu .depth-ul-1 {
    display: flex;
    flex-direction: column;
}

#mobile-menu .depth-link-1 {
    padding: var(--wv-12);
    border-bottom: 1px solid #eee;
}

#mobile-menu .depth-wrap-2 {
    padding-left: var(--wv-20);
}
</style>
```

### 5. 조건부 메뉴 표시

```php
<?php
$menu = wv()->menu->make('main_menu');

// 기본 메뉴
$menu->append(array('id' => 'home', 'name' => '홈', 'url' => '/'));
$menu->append(array('id' => 'about', 'name' => '소개', 'url' => '/page/about/'));

// 로그인한 경우에만 추가
if($is_member){
    $menu->append(array(
        'id' => 'mypage',
        'name' => '마이페이지',
        'url' => '/page/mypage/'
    ));
}

// 관리자인 경우에만 추가
if($member['mb_level'] >= 10){
    $menu->append(array(
        'id' => 'admin',
        'name' => '관리자',
        'url' => '/admin/'
    ));
}

echo $menu->displayMenu('depth');
?>
```

---

## 🔧 고급 활용

### 1. 동일 URL 메뉴 구분 (wvd 파라미터)

동일한 URL에 대해 여러 메뉴 항목이 필요할 때:

```php
$menu->append(array(
    'id' => 'store-all',
    'name' => '전체 매장',
    'url' => '/page/store-list/'
));

$menu->append(array(
    'id' => 'store-seoul',
    'name' => '서울 매장',
    'url' => '/page/store-list/'  // 동일 URL
));

// 시스템이 자동으로 wvd 파라미터 추가:
// /page/store-list/?wvd=0
// /page/store-list/?wvd=1
```

**특정 메뉴 활성화:**

```html
<!-- wvt 파라미터로 타게팅 -->
<a href="/page/store-list/?wvt=store-seoul">서울 매장</a>
```

### 2. 메뉴 아이콘 추가

```php
$menu->append(array(
    'id' => 'settings',
    'name' => '설정',
    'url' => '/page/settings/',
    'icon' => '/img/icon-settings.png'
));

// 스킨에서 아이콘 표시
?>
<a class="depth-link-1" href="<?php echo $menu['url']; ?>">
    <?php if($menu['icon']): ?>
        <img src="<?php echo $menu['icon']; ?>" alt="" class="menu-icon">
    <?php endif; ?>
    <span><?php echo $menu['name']; ?></span>
</a>
```

### 3. 부모 메뉴 ID 추적

```php
<?php
// 현재 활성 메뉴 ID
$active_id = $menu->getActiveMenuId();

// 직계 부모 ID
$parent_id = $menu->getParentId($active_id);

// 최상위 부모 ID
$top_parent_id = $menu->getParentId($active_id, true);

// N단계 위 부모 ID
$second_parent_id = $menu->getParentId($active_id, 2);
?>
```

### 4. 메뉴 데이터 가져오기

```php
// 전체 메뉴
$all_menus = $menu->getMenu();

// 특정 메뉴와 하위
$company_menu = $menu->getMenu('company');

// JSON으로 변환
$menu_json = json_encode($all_menus);
```

### 5. Collapse 메뉴 (Accordion)

**스킨**: `plugins/menu/theme/basic/collapse/skin.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> collapse-menu">
    <style>
        <?php echo $skin_selector?> .depth-wrap-2 {padding-left: var(--wv-20);}
        <?php echo $skin_selector?> .collapse-toggle {cursor: pointer; user-select: none;}
        <?php echo $skin_selector?> .collapse-toggle i {transition: transform 0.3s;}
        <?php echo $skin_selector?> .collapse-toggle.expanded i {transform: rotate(90deg);}
    </style>

    <?php
    echo wv_depth_menu(function($depth, $content, $curr_id, $data) use($skin_id){
        if($depth == 1){
            return $content;
        }
        
        // 2depth 이상은 Collapse로
        $parent_menu = $data[0] ?? array();
        $show = ($parent_menu['expand'] || $parent_menu['active']) ? ' show' : '';
        
        return '<div class="collapse' . $show . '" id="collapse-' . $curr_id . '">' . 
               $content . 
               '</div>';
    }, $skin_id, $data, 99);
    ?>

    <script>
        $(document).ready(function(){
            var $skin = $('<?php echo $skin_selector?>');
            
            // Collapse 토글 버튼 추가
            $skin.find('.depth-li-1').each(function(){
                var $li = $(this);
                var $subMenu = $li.find('.depth-wrap-2');
                
                if($subMenu.length){
                    var $link = $li.children('.depth-link-1');
                    var collapseId = $subMenu.find('.collapse').attr('id');
                    
                    $link.after('<span class="collapse-toggle" data-bs-toggle="collapse" data-bs-target="#' + collapseId + '"><i class="fa fa-chevron-right"></i></span>');
                }
            });
        });
    </script>
</div>
```

---

## 🎯 실전 예시

### 예시 1: 헤더 메뉴 (GNB)

**파일**: `plugins/layout/theme/basic/pc/head.php`

```php
<?php
// 메뉴 설정
$gnb = wv()->menu->make('gnb');

$gnb->append(array('id' => 'home', 'name' => '홈', 'url' => '/'));

$gnb->append(array(
    'id' => 'company',
    'name' => '회사소개',
    'url' => '/page/company/',
    'sub' => array(
        array('id' => 'intro', 'name' => '회사개요', 'url' => '/page/company/intro/'),
        array('id' => 'history', 'name' => '연혁', 'url' => '/page/company/history/'),
        array('id' => 'vision', 'name' => '비전', 'url' => '/page/company/vision/')
    )
));

$gnb->append(array(
    'id' => 'service',
    'name' => '서비스',
    'url' => '/page/service/',
    'sub' => array(
        array('id' => 'service1', 'name' => '서비스1', 'url' => '/page/service/1/'),
        array('id' => 'service2', 'name' => '서비스2', 'url' => '/page/service/2/')
    )
));

$gnb->append(array('id' => 'contact', 'name' => '문의', 'url' => '/page/contact/'));
?>

<header class="header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div class="logo">
                <a href="/"><img src="/img/logo.png" alt="로고"></a>
            </div>
            
            <nav class="gnb">
                <?php echo $gnb->displayMenu('hover/mega'); ?>
            </nav>
            
            <div class="header-utils">
                <?php if($is_member): ?>
                    <a href="/page/mypage/">마이페이지</a>
                    <a href="/bbs/logout.php">로그아웃</a>
                <?php else: ?>
                    <a href="/bbs/login.php">로그인</a>
                    <a href="/bbs/register.php">회원가입</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
```

### 예시 2: 관리자 사이드바 메뉴

**파일**: `plugins/gnu_adm/theme/basic/plugins/menu/theme/pc/left_collapse/skin.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 관리자 메뉴 설정
$admin_menu = wv()->menu->make('admin_menu');

$admin_menu->append(array(
    'id' => 'dashboard',
    'name' => '대시보드',
    'url' => '/admin/',
    'icon' => '/img/admin/icon-dashboard.png'
));

$admin_menu->append(array(
    'id' => 'member',
    'name' => '회원관리',
    'url' => '/admin/member/',
    'icon' => '/img/admin/icon-member.png',
    'sub' => array(
        array('id' => 'member-list', 'name' => '회원목록', 'url' => '/admin/member/list/'),
        array('id' => 'member-level', 'name' => '등급관리', 'url' => '/admin/member/level/')
    )
));

$admin_menu->append(array(
    'id' => 'board',
    'name' => '게시판관리',
    'url' => '/admin/board/',
    'icon' => '/img/admin/icon-board.png',
    'sub' => array(
        array('id' => 'board-list', 'name' => '게시판목록', 'url' => '/admin/board/list/'),
        array('id' => 'board-group', 'name' => '게시판그룹', 'url' => '/admin/board/group/')
    )
));

$admin_menu->append(array(
    'id' => 'config',
    'name' => '환경설정',
    'url' => '/admin/config/',
    'icon' => '/img/admin/icon-config.png'
));
?>

<div class="admin-sidebar">
    <div class="sidebar-header">
        <h3>관리자</h3>
    </div>
    
    <nav class="sidebar-menu">
        <?php echo $admin_menu->displayMenu('left_collapse'); ?>
    </nav>
</div>

<style>
.admin-sidebar {
    width: var(--wv-250);
    height: 100vh;
    background: #2c3e50;
    color: white;
    position: fixed;
    left: 0;
    top: 0;
}

.sidebar-menu .depth-link-1 {
    display: flex;
    align-items: center;
    padding: var(--wv-12) var(--wv-20);
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s;
}

.sidebar-menu .depth-link-1:hover,
.sidebar-menu .depth-link-1.active {
    background: rgba(255,255,255,0.1);
    color: white;
}

.sidebar-menu .menu-icon {
    width: var(--wv-20);
    margin-right: var(--wv-12);
}

.sidebar-menu .depth-wrap-2 {
    background: rgba(0,0,0,0.2);
}

.sidebar-menu .depth-link-2 {
    padding: var(--wv-10) var(--wv-20) var(--wv-10) var(--wv-52);
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    display: block;
}

.sidebar-menu .depth-link-2:hover {
    background: rgba(255,255,255,0.05);
    color: white;
}
</style>
```

### 예시 3: Swiper 탭 메뉴

```php
<?php
$tab_menu = wv()->menu->make('tab_menu');

$tab_menu->append(array('id' => 'tab1', 'name' => '전체', 'url' => '/page/list/?tab=all'));
$tab_menu->append(array('id' => 'tab2', 'name' => '인기', 'url' => '/page/list/?tab=popular'));
$tab_menu->append(array('id' => 'tab3', 'name' => '최신', 'url' => '/page/list/?tab=latest'));
$tab_menu->append(array('id' => 'tab4', 'name' => '추천', 'url' => '/page/list/?tab=recommend'));
?>

<div class="tab-menu-container">
    <?php echo $tab_menu->displayMenu('swiper'); ?>
</div>

<style>
.tab-menu-container .swiper {
    width: 100%;
    padding: var(--wv-10) 0;
}

.tab-menu-container .depth-link-1 {
    padding: var(--wv-10) var(--wv-20);
    border-radius: var(--wv-20);
    background: #f8f9fa;
    text-decoration: none;
    color: #666;
    white-space: nowrap;
}

.tab-menu-container .depth-link-1.active {
    background: #007bff;
    color: white;
}
</style>

<script>
$(document).ready(function(){
    new Swiper('.tab-menu-container .swiper', {
        slidesPerView: 'auto',
        spaceBetween: 8,
        freeMode: true
    });
});
</script>
```

---

## 🔍 문제 해결

### 메뉴가 활성화되지 않을 때

```php
// 1. URL 확인
echo "현재 URL: " . $_SERVER['REQUEST_URI'];

// 2. 메뉴 URL과 정확히 일치하는지 확인
$menu->append(array(
    'id' => 'page1',
    'name' => '페이지1',
    'url' => '/page/page1/'  // 끝에 슬래시 주의
));

// 3. wvt 파라미터로 강제 활성화
<a href="/page/page1/?wvt=page1">페이지1</a>
```

### 서브메뉴가 표시되지 않을 때

```php
// 1. 스킨에서 max_depth 확인
echo wv_depth_menu(null, $skin_id, $data, 2);  // 2depth까지만
//                                          ↑ 이 값을 늘리기

// 2. CSS에서 display: none 확인
<style>
.depth-wrap-2 {
    /* display: none; ← 이 부분 제거 */
}
</style>

// 3. 콜백에서 false 반환하지 않는지 확인
echo wv_depth_menu(function($depth, $content){
    if($depth == 2){
        return false;  // ← 2depth가 표시 안 됨
    }
}, $skin_id, $data);
```

### 메뉴 순서가 맞지 않을 때

```php
// order 값으로 정렬
$menu->append(array(
    'id' => 'menu1',
    'name' => '메뉴1',
    'url' => '/1/',
    'order' => 10
));

$menu->append(array(
    'id' => 'menu2',
    'name' => '메뉴2',
    'url' => '/2/',
    'order' => 5  // 더 낮은 값이 먼저
));

// displayMenu 시 자동 정렬됨
echo $menu->displayMenu('depth');
```

### 동일 URL 메뉴가 모두 활성화되는 문제

```php
// wvt 파라미터로 명시적 타게팅
<a href="/page/list/?wvt=menu-seoul">서울</a>
<a href="/page/list/?wvt=menu-busan">부산</a>

// 또는 URL을 다르게
$menu->append(array('id' => 'seoul', 'name' => '서울', 'url' => '/page/list/?region=seoul'));
$menu->append(array('id' => 'busan', 'name' => '부산', 'url' => '/page/list/?region=busan'));
```

---

## 📚 참고사항

### Menu vs Widget

| 항목 | Menu | Widget |
|------|------|--------|
| 용도 | 네비게이션 | 재사용 컴포넌트 |
| 활성화 | 자동 URL 감지 | 수동 처리 |
| 계층 구조 | 다중 Depth | 단일 레벨 |
| Makeable | O | O |

### 네이밍 컨벤션

```php
// 메뉴 ID: kebab-case 추천
'id' => 'company-intro'      // ✅
'id' => 'company_intro'      // ✅
'id' => 'companyIntro'       // ❌ (camelCase 지양)

// 스킨 경로: 슬래시 구분
$menu->displayMenu('hover/mega');  // ✅
```

### 성능 최적화

```php
// 1. 메뉴 재정렬 캐싱
echo $menu->displayMenu('depth', '', false);  // re_order=false

// 2. 메뉴 인스턴스 재사용
$gnb = wv()->menu->make('gnb');
// ... 메뉴 설정 ...
// 여러 곳에서 같은 인스턴스 사용
echo $gnb->displayMenu('depth');
```

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-10-01  
**작성자**: Claude AI