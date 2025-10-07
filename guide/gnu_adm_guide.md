# 👨‍💼 Gnu Adm 플러그인 가이드

> **관리자 전용 파트너 페이지 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [시스템 구조](#시스템-구조)
3. [기본 개념](#기본-개념)
4. [설정 방법](#설정-방법)
5. [주입 테마 시스템](#주입-테마-시스템)
6. [실전 예시](#실전-예시)
7. [향후 개선 (Makeable)](#향후-개선-makeable)

---

## 📌 개요

**Gnu Adm 플러그인**은 `/admin` 경로로 접근했을 때 일반 사이트와 완전히 분리된 관리자 전용 환경을 제공하는 파트너 페이지 시스템입니다.

### 핵심 특징

✅ **경로 분리**: `/admin`으로 접근 시 별도 환경  
✅ **전용 레이아웃**: 관리자 전용 layout 사용  
✅ **전용 페이지 테마**: 관리자 전용 page 테마 사용  
✅ **주입 테마**: 다른 플러그인(layout, page)에 테마 주입  
✅ **접근 제어**: 로그인 필수, 회원가입 불가  
✅ **메뉴 시스템**: 관리자 전용 메뉴 구성

### 기본 흐름

```
1. 사용자가 /admin 접근
2. wv_dir_var = 'admin' 설정
3. 로그인 체크 (wv_must_login)
4. 회원가입 차단 (wv_never_register)
5. 관리자 전용 layout/page 테마 로드
6. 관리자 메뉴 구성
```

---

## 🏗️ 시스템 구조

### 디렉토리 구조

```
plugins/gnu_adm/
├── GnuAdm.php                     # 메인 클래스
├── plugin.php                     # 플러그인 초기화
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
            ├── page/              # Page 플러그인 주입
            │   └── theme/
            │       ├── pc/
            │       │   ├── main.php
            │       │   ├── 0101.php
            │       │   ├── 0102.php
            │       │   └── ...
            │       └── mobile/
            └── gnu_skin/          # Gnu_skin 플러그인 주입
                └── theme/
                    └── pc/
                        └── member/
                            └── basic/
                                └── login.skin.php
```

### GnuAdm 클래스 구조

```php
class GnuAdm extends Plugin {
    
    protected $dir_var = 'admin';  // 경로 식별자
    
    public function __construct() {
        // 1. dir_var 사전 체크
        wv_dir_var_pre_check($this->dir_var);
        
        // 2. admin 경로일 때만 실행
        if($wv_dir_var == $this->dir_var) {
            // 로그인 필수
            wv_must_login();
            
            // 회원가입 차단
            wv_never_register();
            
            // 훅 등록
            add_event('wv_hook_eval_action_before', 
                array($this, 'wv_hook_eval_action_before'), -1);
        }
    }
    
    public function wv_hook_eval_action_before() {
        // 메뉴 구성
        $wv_main_menu_array = array(/* 메뉴 데이터 */);
        wv('menu')->make('left_menu')->setMenu($wv_main_menu_array, true);
        
        // 기본 페이지 설정
        wv()->page->set_page_index_id('0101');
        
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
// URL: https://example.com/admin
// → $wv_dir_var = 'admin'

// URL: https://example.com/ceo
// → $wv_dir_var = 'ceo'

// URL: https://example.com/ (일반)
// → $wv_dir_var = null
```

**dir_var 확인:**

```php
global $wv_dir_var;

if ($wv_dir_var == 'admin') {
    // 관리자 모드
}
```

### 2. 주입 테마 (Injection Theme)

다른 플러그인(layout, page 등)에 테마를 "주입"하는 시스템입니다.

```
일반 모드: 
plugins/layout/theme/basic/pc/head.php

관리자 모드:
plugins/gnu_adm/theme/basic/plugins/layout/theme/pc/head.php
```

**동작 원리:**

```php
// $this->injection_theme_use() 실행 시

// Layout 플러그인이 테마를 찾을 때
// 1. plugins/gnu_adm/theme/basic/plugins/layout/theme/pc/head.php (주입 테마)
// 2. plugins/layout/theme/basic/pc/head.php (기본 테마)
// 순서로 찾고, 주입 테마가 있으면 우선 사용
```

### 3. 파트너 페이지 개념

**파트너 페이지**는 동일한 사이트 내에서 완전히 분리된 별도의 환경을 제공합니다.

| 구분 | 일반 사이트 | 관리자 페이지 (/admin) |
|------|-------------|------------------------|
| **URL** | / | /admin |
| **Layout** | 기본 layout | 관리자 layout |
| **Page** | 기본 page | 관리자 page |
| **메뉴** | 일반 메뉴 | 관리자 메뉴 |
| **접근** | 누구나 | 로그인 필수 |
| **회원가입** | 가능 | 불가 |

---

## ⚙️ 설정 방법

### 1. 기본 설정 (setting.php)

```php
// setting.php

// gnu_adm 플러그인 로드
wv()->load(array('gnu_adm'));

// 테마 설정
wv('layout')->set_theme_dir('basic');
wv('page')->set_theme_dir('basic');
```

### 2. GnuAdm.php 설정

```php
class GnuAdm extends Plugin {
    
    protected $dir_var = 'admin';  // 경로 식별자
    
    public function __construct() {
        global $wv_dir_var;
        
        // dir_var 사전 체크
        wv_dir_var_pre_check($this->dir_var);
        
        // admin 경로일 때만 실행
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
    
    public function wv_hook_eval_action_before() {
        
        // 1. 관리자 메뉴 구성
        $wv_main_menu_array = array(
            array(
                'name' => '고객관리',
                'url' => '/',
                'sub' => array(
                    array(
                        'name' => '회원 관리',
                        'url' => '/?wv_page_id=0101',
                        'icon' => wv()->gnu_adm->plugin_url . '/img/menu1.png',
                        'sub' => array(
                            array('name' => '사장님 관리', 'url' => '/?wv_page_id=0101'),
                            array('name' => '일반 사용자 관리', 'url' => '/?wv_page_id=0102'),
                        )
                    ),
                    array(
                        'name' => '매장관리',
                        'url' => '/?wv_page_id=0201',
                        'icon' => wv()->gnu_adm->plugin_url . '/img/menu2.png'
                    ),
                )
            ),
            array(
                'name' => '사이트 설정',
                'url' => '/?wv_page_id=0601',
                'sub' => array(
                    array('name' => '계약 및 상품 설정', 'url' => '/?wv_page_id=0601'),
                    array('name' => '이미지/콘텐츠 관리', 'url' => '/?wv_page_id=0701'),
                )
            )
        );
        
        // 2. 메뉴 생성
        wv('menu')->make('left_menu')->setMenu($wv_main_menu_array, true);
        
        // 3. 기본 페이지 ID 설정
        wv()->page->set_page_index_id('0101');
        
        // 4. 주입 테마 사용
        $this->injection_theme_use();
    }
}
```

### 3. 접근 URL

```
https://example.com/admin
→ 관리자 로그인 페이지

https://example.com/admin?wv_page_id=0101
→ 회원 관리 페이지

https://example.com/admin?wv_page_id=0201
→ 매장 관리 페이지
```

---

## 🎨 주입 테마 시스템

### Layout 주입 테마

#### 1. head.php (헤더)

```php
// plugins/gnu_adm/theme/basic/plugins/layout/theme/pc/head.php

<div id="header-wrapper">
    <div id="header-menu">
        <div class="container">
            <div class="hstack justify-content-between">
                <div class="col-auto">
                    <a href="<?php echo G5_URL; ?>">ADMINISTRATOR</a>
                </div>
                
                <div class="col-auto ms-auto">
                    <div class="hstack" style="gap: var(--wv-12)">
                        <a href="<?php echo G5_BBS_URL; ?>/logout.php">LOGOUT</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

#### 2. common.php (공통 레이아웃)

```php
// plugins/gnu_adm/theme/basic/plugins/layout/theme/pc/common.php

<div class="d-flex col">
    <!-- 왼쪽 사이드바 메뉴 -->
    <div class="col-auto" style="padding-top: var(--wv-18); width: var(--wv-200);">
        <?php echo wv('menu')->made('left_menu')->displayMenu('left_collapse'); ?>
    </div>
    
    <!-- 메인 컨텐츠 영역 -->
    <div class="col bg-white" style="min-height: 100%">
        
        <!-- 페이지 타이틀 -->
        <div class="container py-[12px]" style="border-bottom: 2px solid #efefef;">
            <div class="hstack justify-content-between">
                <p class="page-title">
                    <?php 
                    echo $page_title ? $page_title : 
                         wv('menu')->made('left_menu')->getMenu(
                             wv('menu')->made('left_menu')->getActiveMenuId()
                         )['name']; 
                    ?>
                </p>
            </div>
        </div>
        
        <!-- 페이지 컨텐츠 -->
        <div class="container py-[26px]">
            <!--><!-->
        </div>
    </div>
</div>
```

#### 3. tail.php (푸터)

```php
// plugins/gnu_adm/theme/basic/plugins/layout/theme/pc/tail.php

</div><!-- #site-wrapper -->

<script>
// 관리자 페이지 전용 스크립트
$(document).ready(function() {
    // 관리자 기능 초기화
});
</script>
```

### Page 주입 테마

#### 1. main.php (메인 페이지)

```php
// plugins/gnu_adm/theme/basic/plugins/page/theme/pc/main.php

<div class="admin-dashboard">
    <h2>관리자 대시보드</h2>
    
    <div class="row">
        <div class="col-md-3">
            <div class="stat-box">
                <h3>총 회원 수</h3>
                <p class="stat-number">1,234</p>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-box">
                <h3>총 매장 수</h3>
                <p class="stat-number">567</p>
            </div>
        </div>
    </div>
</div>
```

#### 2. 0101.php (회원 관리 페이지)

```php
// plugins/gnu_adm/theme/basic/plugins/page/theme/pc/0101.php

<?php
// 회원 목록 조회
$result = wv()->store_manager->made('member')->get_list(array(
    'page' => $page,
    'limit' => 20
));

$list = $result['list'];
$paging = $result['paging'];
?>

<div class="admin-member-list">
    <form name="flist" method="post">
        
        <!-- 검색 폼 -->
        <div class="search-box">
            <input type="text" name="stx" placeholder="검색어 입력">
            <button type="submit">검색</button>
        </div>
        
        <!-- 회원 목록 테이블 -->
        <table class="table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>아이디</th>
                    <th>이름</th>
                    <th>이메일</th>
                    <th>가입일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($list) { ?>
                    <?php foreach ($list as $i => $row) { ?>
                        <tr>
                            <td><?php echo $row['num']; ?></td>
                            <td><?php echo $row['mb_id']; ?></td>
                            <td><?php echo $row['mb_name']; ?></td>
                            <td><?php echo $row['mb_email']; ?></td>
                            <td><?php echo $row['mb_datetime']; ?></td>
                            <td>
                                <a href="#" 
                                   data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
                                   data-wv-ajax-data='{"action":"form","made":"member","wr_id":"<?php echo $row['wr_id']; ?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop">
                                    [수정]
                                </a>
                                <a href="#" 
                                   data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
                                   data-wv-ajax-data='{"action":"delete","made":"member","wr_id":"<?php echo $row['wr_id']; ?>"}'>
                                    [삭제]
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6">자료가 없습니다.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <!-- 페이징 -->
        <div class="pagination">
            <?php echo $paging; ?>
        </div>
    </form>
</div>
```

### Gnu_skin 주입 테마

#### login.skin.php (관리자 로그인)

```php
// plugins/gnu_adm/theme/basic/plugins/gnu_skin/theme/pc/member/basic/login.skin.php

<?php
if (!defined('_GNUBOARD_')) exit;
$skin_id = wv_make_skin_id();
$skin_selector = wv_make_skin_selector($skin_id);
?>

<div id="<?php echo $skin_id; ?>" class="admin-login">
    <style>
        <?php echo $skin_selector; ?> {
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        <?php echo $skin_selector; ?> .login-btn.active {
            background-color: #000 !important;
        }
    </style>
    
    <div class="login-box">
        <div class="container">
            <form name="flogin" action="<?php echo $login_action_url; ?>" 
                  onsubmit="return flogin_submit(this);" method="post">
                
                <input type="hidden" name="url" value="<?php echo $login_url; ?>">
                
                <div style="padding: 0 var(--wv-8)">
                    <p class="fs-[30///700/] text-center" style="color: #000">
                        관리자모드
                    </p>
                    
                    <div class="mt-[40px]">
                        <!-- 아이디 -->
                        <div>
                            <label for="login_id">아이디</label>
                            <input type="text" name="mb_id" id="login_id" 
                                   required class="form-control" 
                                   placeholder="아이디 입력" 
                                   autocomplete="new-password">
                        </div>
                        
                        <!-- 비밀번호 -->
                        <div class="mt-[24px]">
                            <label for="login_pw">비밀번호</label>
                            <input type="password" name="mb_password" id="login_pw" 
                                   required class="form-control" 
                                   placeholder="비밀번호 입력" 
                                   autocomplete="new-password">
                        </div>
                    </div>
                </div>
                
                <!-- 로그인 버튼 -->
                <div class="mt-[40px]">
                    <button type="submit" class="w-full py-[14px] login-btn">
                        로그인
                    </button>
                </div>
                
                <!-- 안내 -->
                <div class="text-center mt-[16px]">
                    <p>아이디 / 비밀번호를 잃어버리신 경우, 고객센터로 문의바랍니다.</p>
                </div>
                
                <!-- 로고 -->
                <div class="text-center mt-[128px]">
                    <img src="<?php echo WV_URL; ?>/img/logo1.png" 
                         class="w-[54.3px]" alt="">
                </div>
            </form>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        // 입력 필드 활성화 시 버튼 활성화
        $('#login_id, #login_pw').on('input', function() {
            if ($('#login_id').val() && $('#login_pw').val()) {
                $('.login-btn').addClass('active');
            } else {
                $('.login-btn').removeClass('active');
            }
        });
    });
    
    function flogin_submit(f) {
        if ($(document.body).triggerHandler('login_sumit', [f, 'flogin']) !== false) {
            return true;
        }
        return false;
    }
    </script>
</div>
```

---

## 💼 실전 예시

### 예시 1: 기본 관리자 페이지 구성

#### GnuAdm.php

```php
<?php
namespace weaver;

class GnuAdm extends Plugin {
    
    protected $dir_var = 'admin';
    
    public function __construct() {
        global $wv_dir_var;
        
        wv_dir_var_pre_check($this->dir_var);
        
        if ($wv_dir_var == $this->dir_var) {
            wv_must_login();
            wv_never_register();
            
            add_event('wv_hook_eval_action_before',
                array($this, 'wv_hook_eval_action_before'), -1);
        }
    }
    
    public function wv_hook_eval_action_before() {
        
        // 메뉴 구성
        $menu = array(
            array(
                'name' => '대시보드',
                'url' => '/admin',
                'icon' => $this->plugin_url . '/img/dashboard.png'
            ),
            array(
                'name' => '회원관리',
                'url' => '/?wv_page_id=member',
                'icon' => $this->plugin_url . '/img/user.png',
                'sub' => array(
                    array('name' => '회원목록', 'url' => '/?wv_page_id=member_list'),
                    array('name' => '회원등급', 'url' => '/?wv_page_id=member_level'),
                )
            ),
            array(
                'name' => '게시판관리',
                'url' => '/?wv_page_id=board',
                'icon' => $this->plugin_url . '/img/board.png'
            ),
            array(
                'name' => '설정',
                'url' => '/?wv_page_id=config',
                'icon' => $this->plugin_url . '/img/config.png'
            )
        );
        
        wv('menu')->make('admin_menu')->setMenu($menu, true);
        wv()->page->set_page_index_id('main');
        
        $this->injection_theme_use();
    }
}

GnuAdm::getInstance();
```

#### setting.php

```php
// 관리자 페이지 설정
wv()->load(array('gnu_adm'));
```

### 예시 2: 관리자 권한 체크

```php
// GnuAdm.php

public function wv_hook_eval_action_before() {
    global $member;
    
    // 관리자 권한 체크
    if ($member['mb_level'] < 10) {
        alert('관리자만 접근 가능합니다.');
        goto_url('/');
        exit;
    }
    
    // 메뉴 및 테마 설정
    // ...
}
```

### 예시 3: 관리자 전용 스타일

```php
// plugins/gnu_adm/theme/basic/plugins/layout/theme/pc/layout.css

/* 관리자 페이지 전용 스타일 */
#header-wrapper {
    background-color: #2c3e50;
    color: #fff;
}

#header-menu a {
    color: #fff;
}

.admin-sidebar {
    background-color: #34495e;
    min-height: 100vh;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}
```

---

## 🔮 향후 개선 (Makeable)

현재는 단일 인스턴스 플러그인이지만, 향후 **Makeable** 패턴으로 개선하여 여러 파트너 페이지를 동적으로 생성할 수 있습니다.

### 현재 구조

```php
class GnuAdm extends Plugin {
    protected $dir_var = 'admin';  // 고정
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
wv()->partner_page->make('admin', array(
    'theme' => 'admin_theme',
    'menu' => $admin_menu,
    'auth_level' => 10
));

wv()->partner_page->make('ceo', array(
    'theme' => 'ceo_theme',
    'menu' => $ceo_menu,
    'auth_level' => 5
));

wv()->partner_page->make('partner', array(
    'theme' => 'partner_theme',
    'menu' => $partner_menu,
    'auth_level' => 3
));
```

### 개선 사항

1. **동적 생성**: 여러 파트너 페이지를 코드로 생성
2. **설정 분리**: 각 파트너 페이지마다 독립적인 설정
3. **재사용성**: 동일한 구조를 여러 용도로 활용
4. **유지보수**: 중앙 집중식 관리

---

## 🎯 핵심 정리

### 1. **Gnu Adm의 역할**
- `/admin` 경로로 분리된 관리자 환경 제공
- 전용 layout, page 테마 사용
- 주입 테마 시스템으로 기존 플러그인 확장

### 2. **dir_var 시스템**
```php
global $wv_dir_var;

if ($wv_dir_var == 'admin') {
    // 관리자 모드
}
```

### 3. **접근 제어**
```php
wv_must_login();      // 로그인 필수
wv_never_register();  // 회원가입 차단
```

### 4. **주입 테마 경로**
```
plugins/gnu_adm/theme/basic/plugins/
├── layout/theme/pc/
├── page/theme/pc/
└── gnu_skin/theme/pc/
```

### 5. **향후 개선**
- Makeable 패턴으로 전환
- 동적 파트너 페이지 생성
- 설정 기반 관리

---

**완성! 🎉**

Gnu Adm 플러그인으로 관리자 전용 환경을 구축하고, 주입 테마 시스템으로 기존 플러그인을 확장할 수 있습니다!