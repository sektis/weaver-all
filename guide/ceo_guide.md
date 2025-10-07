# 👨‍💼 CEO 플러그인 가이드

> **사장님 전용 파트너 페이지 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [시스템 구조](#시스템-구조)
3. [기본 개념](#기본-개념)
4. [설정 방법](#설정-방법)
5. [주입 테마 시스템](#주입-테마-시스템)
6. [매장 관리 기능](#매장-관리-기능)
7. [실전 예시](#실전-예시)
8. [향후 개선 (Makeable)](#향후-개선-makeable)

---

## 📌 개요

**CEO 플러그인**은 `/ceo` 경로로 접근했을 때 일반 사이트와 완전히 분리된 사장님 전용 환경을 제공하는 파트너 페이지 시스템입니다.

### 핵심 특징

✅ **경로 분리**: `/ceo`로 접근 시 별도 환경  
✅ **전용 레이아웃**: 사장님 전용 layout 사용  
✅ **전용 페이지 테마**: 사장님 전용 page 테마 사용  
✅ **주입 테마**: 다른 플러그인(layout, page)에 테마 주입  
✅ **접근 제어**: 로그인 필수, 회원가입 불가  
✅ **매장 관리**: 현재 매장 자동 설정 및 관리  
✅ **메뉴 시스템**: 사장님 전용 메뉴 구성

### 기본 흐름

```
1. 사용자가 /ceo 접근
2. wv_dir_var = 'ceo' 설정
3. 로그인 체크 (wv_must_login)
4. 회원가입 차단 (wv_never_register)
5. 현재 매장 확인 (get_current_store)
6. 매장 없으면 매장 선택 유도
7. 사장님 전용 layout/page 테마 로드
8. 사장님 메뉴 구성
```

---

## 🏗️ 시스템 구조

### 디렉토리 구조

```
plugins/ceo/
├── Ceo.php                        # 메인 클래스
├── plugin.php                     # 플러그인 초기화
├── init_current.php               # 현재 매장 초기화
└── theme/
    └── basic/                     # 기본 테마
        └── plugins/               # 주입 테마
            ├── layout/            # Layout 플러그인 주입
            │   └── theme/
            │       ├── pc/
            │       │   ├── head.php
            │       │   ├── tail.php
            │       │   └── common.php
            │       └── mobile/
            └── page/              # Page 플러그인 주입
                └── theme/
                    ├── pc/
                    │   ├── main.php
                    │   ├── 0101.php (매장관리)
                    │   ├── 0201.php (서비스관리)
                    │   ├── 0301.php (MY 계정)
                    │   └── ...
                    └── mobile/
```

### Ceo 클래스 구조

```php
class Ceo extends Plugin {
    
    protected $dir_var = 'ceo';  // 경로 식별자
    
    public function __construct() {
        global $wv_dir_var;
        
        // 1. dir_var 사전 체크
        wv_dir_var_pre_check('ceo');
        
        // 2. ceo 경로일 때만 실행
        if ($wv_dir_var == $this->dir_var) {
            // 로그인 필수
            wv_must_login();
            
            // 회원가입 차단
            wv_never_register();
            
            // 훅 등록
            add_event('wv_hook_eval_action_before',
                array($this, 'wv_hook_eval_action_before'), -1);
        }
    }
    
    public function wv_hook_before_header_wrapper() {
        // 현재 매장 초기화
        include dirname(__FILE__) . '/init_current.php';
    }
    
    public function wv_hook_eval_action_before() {
        // 현재 매장 확인
        $curr_store = wv()->store_manager->made('sub01_01')->get_current_store();
        
        // 매장 없으면 헤더 전 훅 등록
        if (!$curr_store['wr_id']) {
            add_event('wv_hook_before_header_wrapper',
                array($this, 'wv_hook_before_header_wrapper'));
        }
        
        // 메뉴 구성
        $wv_main_menu_array = array(/* 메뉴 데이터 */);
        wv('menu')->made('fixed_bottom')->setMenu($wv_main_menu_array, true);
        
        // 주입 테마 사용
        $this->injection_theme_use();
    }
}
```

---

## 💡 기본 개념

### 1. dir_var 시스템

**dir_var**는 URL 경로를 식별하는 변수입니다.

```php
// URL: https://example.com/ceo
// → $wv_dir_var = 'ceo'

// URL: https://example.com/admin
// → $wv_dir_var = 'admin'

// URL: https://example.com/ (일반)
// → $wv_dir_var = null
```

**dir_var 확인:**

```php
global $wv_dir_var;

if ($wv_dir_var == 'ceo') {
    // 사장님 모드
}
```

### 2. 현재 매장 (Current Store)

CEO 모드에서는 사장님이 관리하는 **현재 매장**을 자동으로 설정합니다.

```php
// 현재 매장 가져오기
$curr_store = wv()->store_manager->made('sub01_01')->get_current_store();

if ($curr_store['wr_id']) {
    // 매장 있음
    $store_name = $curr_store['store']['name'];
    $store_address = $curr_store['location']['address'];
} else {
    // 매장 없음 → 매장 선택 유도
}
```

**현재 매장 설정:**

```php
// 쿠키에 매장 ID 저장
wv()->store_manager->made('sub01_01')->set_current_store($wr_id);

// 다음부터 get_current_store()로 자동 조회 가능
```

### 3. 주입 테마 (Injection Theme)

다른 플러그인(layout, page 등)에 테마를 "주입"하는 시스템입니다.

```
일반 모드: 
plugins/layout/theme/basic/pc/head.php

사장님 모드:
plugins/ceo/theme/basic/plugins/layout/theme/pc/head.php
```

**동작 원리:**

```php
// $this->injection_theme_use() 실행 시

// Layout 플러그인이 테마를 찾을 때
// 1. plugins/ceo/theme/basic/plugins/layout/theme/pc/head.php (주입 테마)
// 2. plugins/layout/theme/basic/pc/head.php (기본 테마)
// 순서로 찾고, 주입 테마가 있으면 우선 사용
```

### 4. 파트너 페이지 개념

**파트너 페이지**는 동일한 사이트 내에서 완전히 분리된 별도의 환경을 제공합니다.

| 구분 | 일반 사이트 | 사장님 페이지 (/ceo) |
|------|-------------|----------------------|
| **URL** | / | /ceo |
| **Layout** | 기본 layout | 사장님 layout |
| **Page** | 기본 page | 사장님 page |
| **메뉴** | 일반 메뉴 | 사장님 메뉴 |
| **접근** | 누구나 | 로그인 필수 |
| **회원가입** | 가능 | 불가 |
| **현재 매장** | 없음 | 자동 설정 |

---

## ⚙️ 설정 방법

### 1. 기본 설정 (setting.php)

```php
// setting.php

// ceo 플러그인 로드
wv()->load(array('ceo'));

// 테마 설정
wv('layout')->set_theme_dir('basic');
wv('page')->set_theme_dir('basic');

// Store Manager 설정
wv()->store_manager->make('sub01_01', 'sub01_01', array(
    'menu', 'biz', 'store', 'location', 'dayoffs', 'tempdayoffs', 'contract'
))->prune_columns();
```

### 2. Ceo.php 설정

```php
class Ceo extends Plugin {
    
    protected $dir_var = 'ceo';  // 경로 식별자
    
    public function __construct() {
        global $wv_dir_var;
        
        // dir_var 사전 체크
        wv_dir_var_pre_check('ceo');
        
        // ceo 경로일 때만 실행
        if ($wv_dir_var == $this->dir_var) {
            
            // 로그인 필수
            wv_must_login();
            
            // 회원가입 차단
            wv_never_register();
            
            // 액션 전 훅 등록
            add_event('wv_hook_eval_action_before',
                array($this, 'wv_hook_eval_action_before'), -1);
        }
    }
    
    public function wv_hook_before_header_wrapper() {
        // 현재 매장 초기화
        include dirname(__FILE__) . '/init_current.php';
    }
    
    public function wv_hook_eval_action_before() {
        
        // 1. 현재 매장 확인
        $curr_store = wv()->store_manager->made('sub01_01')->get_current_store();
        
        // 2. 매장 없으면 매장 선택 유도
        if (!$curr_store['wr_id']) {
            add_event('wv_hook_before_header_wrapper',
                array($this, 'wv_hook_before_header_wrapper'));
        }
        
        // 3. 사장님 메뉴 구성
        $wv_main_menu_array = array(
            array(
                'name' => '홈',
                'url' => '/ceo',
                'icon' => WV_URL . '/img/foot_1.png'
            ),
            array(
                'name' => '매장관리',
                'url' => '/?wv_page_id=0101',
                'icon' => WV_URL . '/img/foot_6.png'
            ),
            array(
                'name' => '서비스관리',
                'url' => '/?wv_page_id=0201',
                'icon' => WV_URL . '/img/foot_7.png'
            ),
            array(
                'name' => 'MY 계정',
                'url' => '/?wv_page_id=0301',
                'icon' => WV_URL . '/img/foot_8.png'
            ),
        );
        
        // 4. 메뉴 생성
        wv('menu')->made('fixed_bottom')->setMenu($wv_main_menu_array, true);
        
        // 5. 주입 테마 사용
        $this->injection_theme_use();
    }
}
```

### 3. 접근 URL

```
https://example.com/ceo
→ 사장님 메인 페이지

https://example.com/ceo?wv_page_id=0101
→ 매장 관리 페이지

https://example.com/ceo?wv_page_id=0201
→ 서비스 관리 페이지

https://example.com/ceo?wv_page_id=0301
→ MY 계정 페이지
```

---

## 🎨 주입 테마 시스템

### Layout 주입 테마

#### 1. head.php (헤더)

```php
// plugins/ceo/theme/basic/plugins/layout/theme/pc/head.php

<?php echo wv_widget('common/fixed_quick'); ?>

<div id="header-wrapper">
    <div id="header-menu">
        <div class="container">
            <div class="hstack justify-content-between">
                
                <!-- 왼쪽: 매장 선택 / 페이지 타이틀 -->
                <div class="col-auto">
                    <?php if ($wv_page_id == 'main') { ?>
                        
                        <!-- 메인 페이지: 매장 선택 -->
                        <div data-wv-ajax-url="<?php echo wv()->store_manager->ajax_url(); ?>" 
                             class="cursor-pointer"
                             data-wv-ajax-data='{"action":"widget","widget":"ceo/select_store"}'
                             data-wv-ajax-option="offcanvas,bottom,backdrop-static">
                            <?php echo wv_widget('ceo/stores_display'); ?>
                        </div>
                        
                    <?php } else { ?>
                        
                        <!-- 서브 페이지: 페이지 타이틀 -->
                        <p class="fs-[22/130%/-0.88/700/#0D171B]">
                            <?php 
                            echo str_replace('DUM ', '', strip_tags(
                                wv('menu')->made('fixed_bottom')->getMenu(
                                    wv('menu')->made('fixed_bottom')->getActiveMenuId()
                                )['name']
                            )); 
                            ?>
                        </p>
                        
                    <?php } ?>
                </div>
                
                <!-- 오른쪽: 알림 -->
                <div class="col-auto ms-auto">
                    <div class="hstack" style="gap: var(--wv-12)">
                        <a href="">
                            <img src="<?php echo WV_URL . '/img/icon_alarm.png'; ?>" 
                                 class="w-[28px]" alt="알림">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

#### 2. common.php (공통 레이아웃)

```php
// plugins/ceo/theme/basic/plugins/layout/theme/pc/common.php

<div class="ceo-page-wrapper">
    
    <!-- 페이지 컨텐츠 -->
    <div class="container py-[20px]">
        <!--><!-->
    </div>
    
    <!-- 하단 고정 메뉴 -->
    <div class="fixed-bottom-menu">
        <?php echo wv('menu')->made('fixed_bottom')->displayMenu('fixed_bottom'); ?>
    </div>
</div>
```

#### 3. tail.php (푸터)

```php
// plugins/ceo/theme/basic/plugins/layout/theme/pc/tail.php

</div><!-- #site-wrapper -->

<script>
// 사장님 페이지 전용 스크립트
$(document).ready(function() {
    // CEO 기능 초기화
});
</script>
```

### Page 주입 테마

#### 1. main.php (메인 페이지)

```php
// plugins/ceo/theme/basic/plugins/page/theme/pc/main.php

<?php
global $current_store;
?>

<div class="wv-vstack" style="row-gap: var(--wv-12)">
    <?php
    // 현재 매장 정보 표시
    echo $current_store->store->render_part('ceo/main', 'view');
    ?>
</div>
```

#### 2. 0101.php (매장 관리 페이지)

```php
// plugins/ceo/theme/basic/plugins/page/theme/pc/0101.php

<?php
global $current_store;
?>

<!-- 탭 메뉴 -->
<div class="hstack menu-tab-top" role="tablist">
    <a href="#" id="home-tab" class="active" 
       data-bs-toggle="tab" data-bs-target="#0101-basic">기본 정보</a>
    <a href="#" id="home-tab" class="" 
       data-bs-toggle="tab" data-bs-target="#0101-biz">운영 정보</a>
    <a href="#" id="home-tab" class="" 
       data-bs-toggle="tab" data-bs-target="#0101-menu">메뉴 관리</a>
</div>

<!-- 탭 컨텐츠 -->
<div class="tab-content menu-tab-content" id="myTabContent">
    
    <!-- 기본 정보 탭 -->
    <div class="tab-pane fade show active" id="0101-basic">
        <div class="tab-pane-inner">
            <div>
                <?php echo $current_store->store->render_part('ceo/name', 'view'); ?>
            </div>
            <div>
                <?php echo $current_store->store->render_part('ceo/image', 'view'); ?>
            </div>
            <div>
                <?php echo $current_store->store->render_part('ceo/notice', 'view'); ?>
            </div>
            <div>
                <?php echo $current_store->location->render_part('ceo/address', 'view'); ?>
            </div>
            <div>
                <?php echo $current_store->store->render_part('ceo/tel', 'view'); ?>
            </div>
        </div>
    </div>
    
    <!-- 운영 정보 탭 -->
    <div class="tab-pane fade" id="0101-biz">
        <div class="tab-pane-inner">
            <div>
                <?php echo $current_store->biz->render_part('ceo/open_time', 'view'); ?>
            </div>
            <div>
                <?php echo $current_store->biz->render_part('ceo/break_time', 'view'); ?>
            </div>
            <div>
                <?php echo $current_store->biz->render_part('ceo/holiday', 'view'); ?>
            </div>
            <div>
                <?php echo $current_store->biz->render_part('ceo/parking', 'view'); ?>
            </div>
        </div>
    </div>
    
    <!-- 메뉴 관리 탭 -->
    <div class="tab-pane fade" id="0101-menu">
        <div class="tab-pane-inner">
            <div>
                <?php echo $current_store->menu->render_part('ceo/menu', 'view'); ?>
            </div>
        </div>
    </div>
</div>
```

#### 3. 0301.php (MY 계정 페이지)

```php
// plugins/ceo/theme/basic/plugins/page/theme/pc/0301.php

<?php
global $current_member;
?>

<div class="my-account-page">
    <?php echo $current_member->member->render_part('ceo/my_page', 'view'); ?>
</div>
```

---

## 🏪 매장 관리 기능

### 1. 현재 매장 설정

```php
// 사장님이 매장 선택 시
$wr_id = 123;  // 선택한 매장 ID

// 현재 매장으로 설정 (쿠키에 저장)
wv()->store_manager->made('sub01_01')->set_current_store($wr_id);

// 이후 어디서나 현재 매장 조회 가능
$curr_store = wv()->store_manager->made('sub01_01')->get_current_store();
```

### 2. 현재 매장 조회

```php
// 현재 매장 가져오기
$curr_store = wv()->store_manager->made('sub01_01')->get_current_store();

if ($curr_store['wr_id']) {
    // 매장 정보 사용
    echo $curr_store['store']['name'];        // 매장명
    echo $curr_store['location']['address'];  // 주소
    echo $curr_store['store']['tel'];         // 전화번호
} else {
    // 매장 없음
    echo '매장을 선택해주세요.';
}
```

### 3. 매장 선택 위젯

```php
// plugins/ceo/init_current.php

<?php
global $current_store, $current_store_wr_id;

$curr_store = wv()->store_manager->made('sub01_01')->get_current_store();

if (!$curr_store['wr_id']) {
    // 매장 없으면 선택 모달 표시
    ?>
    <div class="position-fixed top-0 start-0 w-100 h-100 bg-white z-3">
        <div class="container d-flex align-items-center justify-content-center" 
             style="min-height: 100vh;">
            <div>
                <h2>매장을 선택해주세요</h2>
                <?php echo wv_widget('ceo/select_store'); ?>
            </div>
        </div>
    </div>
    <?php
    exit;
}

// 현재 매장 전역 변수 설정
$current_store = wv()->store_manager->made('sub01_01')->get($curr_store['wr_id']);
$current_store_wr_id = $curr_store['wr_id'];
?>
```

### 4. 매장 정보 렌더링

```php
// Store 파트 스킨 사용
<?php echo $current_store->store->render_part('ceo/name', 'view'); ?>
<?php echo $current_store->store->render_part('ceo/tel', 'view'); ?>

// Location 파트 스킨 사용
<?php echo $current_store->location->render_part('ceo/address', 'view'); ?>

// Menu 파트 스킨 사용 (목록 파트)
<?php echo $current_store->menu->render_part('ceo/menu', 'view'); ?>

// Biz 파트 스킨 사용
<?php echo $current_store->biz->render_part('ceo/open_time', 'view'); ?>
```

---

## 💼 실전 예시

### 예시 1: 기본 사장님 페이지 구성

#### Ceo.php

```php
<?php
namespace weaver;

class Ceo extends Plugin {
    
    protected $dir_var = 'ceo';
    
    public function __construct() {
        global $wv_dir_var;
        
        wv_dir_var_pre_check('ceo');
        
        if ($wv_dir_var == $this->dir_var) {
            wv_must_login();
            wv_never_register();
            
            add_event('wv_hook_eval_action_before',
                array($this, 'wv_hook_eval_action_before'), -1);
        }
    }
    
    public function wv_hook_before_header_wrapper() {
        include dirname(__FILE__) . '/init_current.php';
    }
    
    public function wv_hook_eval_action_before() {
        
        // 현재 매장 확인
        $curr_store = wv()->store_manager->made('sub01_01')->get_current_store();
        
        if (!$curr_store['wr_id']) {
            add_event('wv_hook_before_header_wrapper',
                array($this, 'wv_hook_before_header_wrapper'));
        }
        
        // 메뉴 구성
        $menu = array(
            array(
                'name' => '홈',
                'url' => '/ceo',
                'icon' => WV_URL . '/img/home.png'
            ),
            array(
                'name' => '매장관리',
                'url' => '/?wv_page_id=store',
                'icon' => WV_URL . '/img/store.png'
            ),
            array(
                'name' => '서비스관리',
                'url' => '/?wv_page_id=service',
                'icon' => WV_URL . '/img/service.png'
            ),
            array(
                'name' => 'MY 계정',
                'url' => '/?wv_page_id=mypage',
                'icon' => WV_URL . '/img/user.png'
            )
        );
        
        wv('menu')->made('fixed_bottom')->setMenu($menu, true);
        
        $this->injection_theme_use();
    }
}

Ceo::getInstance();
```

#### setting.php

```php
// 사장님 페이지 설정
wv()->load(array('ceo'));

// Store Manager 설정
wv()->store_manager->make('sub01_01', 'sub01_01', array(
    'menu', 'biz', 'store', 'location', 'dayoffs', 'contract'
))->prune_columns();
```

### 예시 2: 매장 선택 위젯

```php
// plugins/ceo/theme/basic/plugins/widget/skin/pc/select_store/skin.php

<?php
global $member;

// 사장님이 관리하는 매장 목록 조회
$result = wv()->store_manager->made('sub01_01')->get_list(array(
    'where' => "mb_id = '{$member['mb_id']}'",
    'limit' => 100
));

$stores = $result['list'];
?>

<div class="select-store-widget">
    <h3>매장을 선택하세요</h3>
    
    <div class="store-list">
        <?php foreach ($stores as $store) { ?>
            <div class="store-item">
                <a href="#" onclick="selectStore(<?php echo $store['wr_id']; ?>); return false;">
                    <img src="<?php echo $store['image']; ?>" alt="<?php echo $store['store']['name']; ?>">
                    <p><?php echo $store['store']['name']; ?></p>
                    <p><?php echo $store['location']['address']; ?></p>
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function selectStore(wr_id) {
    // AJAX로 현재 매장 설정
    $.ajax({
        url: '<?php echo wv()->store_manager->ajax_url(); ?>',
        type: 'POST',
        data: {
            action: 'set_current_store',
            wr_id: wr_id
        },
        success: function(response) {
            // 페이지 새로고침
            location.reload();
        }
    });
}
</script>
```

### 예시 3: 현재 매장 표시 위젯

```php
// plugins/ceo/theme/basic/plugins/widget/skin/pc/stores_display/skin.php

<?php
global $current_store;

if ($current_store && $current_store['wr_id']) {
    ?>
    <div class="current-store-display">
        <div class="hstack" style="gap: var(--wv-8)">
            <img src="<?php echo $current_store['store']['image']; ?>" 
                 class="store-icon" alt="">
            <div>
                <p class="store-name"><?php echo $current_store['store']['name']; ?></p>
                <p class="store-address"><?php echo $current_store['location']['address']; ?></p>
            </div>
            <i class="fa fa-chevron-down"></i>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="no-store-message">
        <p>매장을 선택해주세요</p>
    </div>
    <?php
}
?>
```

### 예시 4: 매장 정보 수정

```php
// plugins/ceo/theme/basic/plugins/page/theme/pc/store_edit.php

<?php
global $current_store, $current_store_wr_id;

if ($_POST['act'] == 'update') {
    // 매장 정보 업데이트
    $data = array(
        'store' => array(
            'name' => $_POST['store']['name'],
            'tel' => $_POST['store']['tel'],
            'notice' => $_POST['store']['notice']
        ),
        'location' => array(
            'address' => $_POST['location']['address'],
            'lat' => $_POST['location']['lat'],
            'lng' => $_POST['location']['lng']
        )
    );
    
    wv()->store_manager->made('sub01_01')->save($current_store_wr_id, $data);
    
    alert('저장되었습니다.');
    goto_url('/ceo?wv_page_id=store');
}
?>

<form method="post">
    <input type="hidden" name="act" value="update">
    
    <!-- 매장명 -->
    <div class="form-group">
        <label>매장명</label>
        <input type="text" name="store[name]" 
               value="<?php echo $current_store['store']['name']; ?>" 
               class="form-control">
    </div>
    
    <!-- 전화번호 -->
    <div class="form-group">
        <label>전화번호</label>
        <input type="text" name="store[tel]" 
               value="<?php echo $current_store['store']['tel']; ?>" 
               class="form-control">
    </div>
    
    <!-- 공지사항 -->
    <div class="form-group">
        <label>공지사항</label>
        <textarea name="store[notice]" class="form-control"><?php echo $current_store['store']['notice']; ?></textarea>
    </div>
    
    <!-- 주소 -->
    <div class="form-group">
        <label>주소</label>
        <input type="text" name="location[address]" 
               value="<?php echo $current_store['location']['address']; ?>" 
               class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary">저장</button>
</form>
```

---

## 🔮 향후 개선 (Makeable)

현재는 단일 인스턴스 플러그인이지만, 향후 **Makeable** 패턴으로 개선하여 여러 파트너 페이지를 동적으로 생성할 수 있습니다.

### 현재 구조

```php
class Ceo extends Plugin {
    protected $dir_var = 'ceo';  // 고정
}
```

### 개선 후 (Makeable)

```php
class PartnerPage extends Makeable {
    
    public function make($dir_var, $config = array()) {
        // 동적으로 파트너 페이지 생성
        $instance = new self();
        $instance->dir_var = $dir_var;
        $instance->config = $config;
        
        return $instance;
    }
}

// 사용 예시
wv()->partner_page->make('ceo', array(
    'theme' => 'ceo_theme',
    'menu' => $ceo_menu,
    'store_table' => 'sub01_01',
    'require_store' => true,  // 매장 필수
    'auth_level' => 5
));

wv()->partner_page->make('partner', array(
    'theme' => 'partner_theme',
    'menu' => $partner_menu,
    'require_store' => false,
    'auth_level' => 3
));
```

### 개선 사항

1. **동적 생성**: 여러 파트너 페이지를 코드로 생성
2. **매장 관리**: 매장 연동 여부 선택 가능
3. **설정 분리**: 각 파트너 페이지마다 독립적인 설정
4. **재사용성**: 동일한 구조를 여러 용도로 활용

---

## 🎯 핵심 정리

### 1. **CEO 플러그인의 역할**
- `/ceo` 경로로 분리된 사장님 환경 제공
- 전용 layout, page 테마 사용
- 현재 매장 자동 관리
- 주입 테마 시스템으로 기존 플러그인 확장

### 2. **dir_var 시스템**
```php
global $wv_dir_var;

if ($wv_dir_var == 'ceo') {
    // 사장님 모드
}
```

### 3. **현재 매장 관리**
```php
// 매장 설정
wv()->store_manager->made('sub01_01')->set_current_store($wr_id);

// 매장 조회
$curr_store = wv()->store_manager->made('sub01_01')->get_current_store();
```

### 4. **접근 제어**
```php
wv_must_login();      // 로그인 필수
wv_never_register();  // 회원가입 차단
```

### 5. **주입 테마 경로**
```
plugins/ceo/theme/basic/plugins/
├── layout/theme/pc/
└── page/theme/pc/
```

### 6. **Store Manager 연동**
```php
// 현재 매장 정보 렌더링
<?php echo $current_store->store->render_part('ceo/name', 'view'); ?>
<?php echo $current_store->location->render_part('ceo/address', 'view'); ?>
<?php echo $current_store->menu->render_part('ceo/menu', 'view'); ?>
```

---

**완성! 🎉**

CEO 플러그인으로 사장님 전용 환경을 구축하고, 매장 관리 기능을 통합할 수 있습니다!