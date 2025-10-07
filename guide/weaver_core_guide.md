# Weaver 코어 개발 가이드

## 📌 프로젝트 개요

Weaver는 그누보드5를 기반으로 한 **플러그인 기반 웹 프레임워크**입니다.

**핵심 목표:**
- 그누보드5 코어 코드를 최대한 건드리지 않고 기능 확장
- 플러그인 내에서 모든 것을 작동할 수 있게 구성
- 모듈화된 플러그인 생태계 구축

---

## 🎯 프로젝트 기본 규칙

### 코딩 스타일

#### PHP 규칙
- **PHP 버전**: 5.6 기준 (타입힌트, 최신 문법 사용 금지)
- **단축 문법 금지**: loop문에서 PHP 단축 문법 사용하지 않기
- **메서드명**: `snake_case`
- **클래스/메서드 선언**: `{` 같은 줄에 위치

```php
class Test{
    public function test_one(){
        // 코드
    }
}
```

#### JavaScript 규칙
- **함수명 및 변수명**: 스네이크 언더바 (`something_else()`, `var one_two;`)

```javascript
function test_function(){
    var my_variable = 10;
}
```

#### CSS/스타일 규칙
- `<style></style>` 안의 속성들은 **한 줄로 작성**
- `{숫자}px` 값은 `var(--wv-{숫자})`로 표시 (자체 반응형 코드)

```css
.my-class {font-size: var(--wv-14); margin-top: var(--wv-10);}
```

### 개발 원칙
- **코드 수정 시 기존 변수명 임의 수정 금지** (필요시 반드시 알림)
- **필요한 부분만 간결하게 수정**
- **중복 코드 붙이지 않기**
- **복잡한 헬퍼 메서드 추가보다 간단한 수정 우선**
- **요청하는 코드의 변수나 함수 역추적해서 기능과 의미 파악**

---

## 🗂️ 디렉토리 구조

```
/plugin/weaver/
├── class/                          (코어 클래스)
│   ├── Weaver.php                 (메인 코어)
│   ├── Plugin.php                 (플러그인 베이스)
│   ├── Makeable.php               (Make 패턴)
│   ├── Info.php                   (환경 정보)
│   ├── Configs.php                (설정 관리)
│   ├── Error.php                  (에러 처리)
│   └── PluginProps.php            (플러그인 속성)
├── lib/                            (라이브러리)
│   ├── common.lib.php             (공통 함수)
│   └── func/                      (유틸 함수)
├── plugins/                        (플러그인들)
│   ├── store_manager/
│   ├── location/
│   ├── page/
│   ├── layout/
│   └── ...
├── theme/                          (테마)
└── manifest.json                   (매니페스트)
```

---

## 🏗️ 핵심 아키텍처

### 클래스 관계도

```
Weaver (코어)
    ↓
Plugin (플러그인 베이스)
    ↓
Makeable (Make 패턴)
    ↓
{PluginName} (개별 플러그인)
```

---

## 🔧 핵심 클래스

### 1. Weaver (코어 클래스)

**파일**: `/plugin/weaver/class/Weaver.php`

```php
namespace weaver;

class Weaver {
    private static $core;
    private static $info;
    private static $error;
    protected static $plugins;
    protected static $plugins_props;
    
    public static function getInstance()
    public function load($plugin)
    public function __get($name)
}
```

**역할:**
- 싱글톤 패턴으로 코어 인스턴스 관리
- 플러그인 로딩 및 관리
- 전역 설정 및 정보 관리

**주요 메서드:**

#### `getInstance()` - 싱글톤 인스턴스 반환
```php
$weaver = Weaver::getInstance();
```

#### `load($plugin)` - 플러그인 로드
```php
// 단일 로드
wv()->load('location');

// 다중 로드
wv()->load(array('location', 'page', 'layout'));
```

#### `__get($name)` - 매직 접근자
```php
// 플러그인 접근
$location = wv()->location;

// 시스템 정보 접근
$info = wv()->info;
$configs = wv()->configs;
$error = wv()->error;
```

---

### 2. Plugin (플러그인 베이스)

**파일**: `/plugin/weaver/class/Plugin.php`

```php
namespace weaver;

class Plugin extends Weaver{
    public static function getInstance()
    public function plugin_init($plugin_name='')
    public function set_theme_dir($theme_dir='basic', $make_skin_once=false)
    public function make_skin($skin, $data='')
    protected function theme_injection()
    protected function skin_injection()
}
```

**역할:**
- 모든 플러그인의 베이스 클래스
- 플러그인 초기화
- 테마/스킨 시스템 제공
- 주입(Injection) 시스템

**주요 메서드:**

#### `getInstance()` - 플러그인 싱글톤 인스턴스
```php
class MyPlugin extends Plugin {
    public static function getInstance() {
        // 자동으로 처리됨
    }
}
MyPlugin::getInstance();
```

#### `plugin_init()` - 플러그인 초기화
```php
public function plugin_init($plugin_name='') {
    // 자동 호출됨
    // plugin_path, plugin_url, plugin_theme_path 등 설정
}
```

**자동 설정되는 속성:**
```php
$this->plugin_name        // 플러그인명
$this->plugin_path        // 플러그인 경로
$this->plugin_url         // 플러그인 URL
$this->plugin_theme_dir   // 현재 테마 디렉토리
$this->plugin_theme_path  // 테마 전체 경로
$this->plugin_theme_url   // 테마 URL
$this->ajax_url           // AJAX URL
```

#### `make_skin()` - 스킨 렌더링
```php
// 기본 사용
echo $this->make_skin('skin_name', array('data' => 'value'));

// 자동 경로
// plugins/{plugin_name}/theme/{theme_dir}/{device}/skin_name/skin.php
```

---

### 3. Makeable (Make 패턴)

**파일**: `/plugin/weaver/class/Makeable.php`

```php
namespace weaver;

class Makeable extends Plugin{
    protected $execute_once = false;
    protected $make_id;
    
    public function make($id='')
    public function made($id='')
    public function made_all()
}
```

**역할:**
- 동일 플러그인의 다중 인스턴스 관리
- ID 기반 인스턴스 생성 및 조회
- 인스턴스 캐싱

**주요 메서드:**

#### `make($id, ...)` - 인스턴스 생성
```php
// Store Manager 예시
wv()->store_manager->make('sub01_01', 'sub01_01', array('menu', 'store', 'location'));

// Widget 예시
wv()->widget->make('map_widget', array('option' => 'value'));
```

**특징:**
- 첫 번째 인자: `$id` (인스턴스 식별자)
- 나머지 인자: 플러그인 `__construct()`로 전달
- 동일 ID로 중복 생성 시 에러

#### `made($id)` - 인스턴스 조회
```php
// 생성된 인스턴스 조회
$manager = wv()->store_manager->made('sub01_01');

// ID 생략 시 기본값 1
$widget = wv()->widget->made(); // made(1)과 동일
```

#### `made_all()` - 모든 인스턴스 조회
```php
$all_instances = wv()->store_manager->made_all();
// array('sub01_01' => object, 'member' => object, ...)
```

#### `init_once()` - 1회 초기화 훅
```php
class MyPlugin extends Makeable {
    public function init_once(){
        // 플러그인당 1회만 실행
        add_javascript('...');
        add_stylesheet('...');
    }
}
```

---

## 🎨 테마/스킨 시스템

### 테마 구조

```
plugins/{plugin_name}/theme/
├── basic/                    (기본 테마)
│   ├── pc/                  (PC 버전)
│   │   └── {skin_name}/
│   │       └── skin.php
│   └── mobile/              (모바일 버전)
│       └── {skin_name}/
│           └── skin.php
└── custom/                   (커스텀 테마)
    └── ...
```

### 테마 설정

```php
// 테마 디렉토리 변경
wv()->layout->set_theme_dir('custom');

// 1회성 테마 변경 (다음 make_skin에만 적용)
wv()->layout->set_theme_dir('special', true);
```

### 스킨 렌더링

```php
// 기본 사용
echo wv()->layout->make_skin('header');

// 데이터 전달
echo wv()->layout->make_skin('header', array(
    'title' => '페이지 제목',
    'user' => $user_data
));

// 스킨 파일 경로 자동 결정:
// plugins/layout/theme/basic/pc/header/skin.php
```

### 스킨 파일 내 사용 가능 변수

```php
// skin.php 내부
<?php
$skin_id       // 고유 ID (예: skin-abc123)
$skin_selector // CSS 선택자 (예: #skin-abc123)
$skin_class    // CSS 클래스 (예: wv-layout-skin-abc123)
$data          // 전달받은 데이터
$this          // 플러그인 인스턴스
?>

<div id="<?php echo $skin_id?>" class="<?php echo $skin_class?>">
    <style>
        <?php echo $skin_selector?> {font-size: var(--wv-14);}
    </style>
    
    <h1><?php echo $data['title']; ?></h1>
</div>
```

---

## 🔌 주입(Injection) 시스템

### 개념

특정 플러그인이 다른 플러그인에 **테마나 스킨을 주입**할 수 있는 시스템입니다.

### 주입 타입

#### 1. 테마 주입 (Theme Injection)
- **주입 위치**: `plugins/{A}/theme/basic/plugins/{B}/theme/`
- **의미**: A 플러그인이 B 플러그인에 전용 테마 제공

#### 2. 스킨 주입 (Skin Injection)
- **주입 위치**: `plugins/{A}/theme/basic/plugins/{B}/skin/`
- **의미**: A 플러그인이 B 플러그인에 커스텀 스킨 제공

### 실제 예시

#### Store Manager → Widget 스킨 주입

```
plugins/store_manager/theme/basic/plugins/widget/skin/pc/location/
├── map/
│   └── skin1.php
└── address/
    └── skin.php
```

**의미**: Store Manager가 Widget 플러그인의 location 위젯에 커스텀 스킨 제공

**사용:**
```php
// Widget이 자동으로 주입된 스킨 사용
echo wv_widget('location/map', $data);
```

#### Gnu Admin → Page 테마 주입

```
plugins/gnu_adm/theme/basic/plugins/page/theme/pc/
├── 0101.php
├── 0201.php
└── ...
```

**의미**: Gnu Admin이 Page 플러그인에 관리자 전용 페이지 테마 제공

### 자동 처리

주입 시스템은 **자동으로 처리**됩니다:
1. `plugin_init()` 시 `theme_injection()`, `skin_injection()` 자동 호출
2. 심볼릭 링크 생성으로 파일 시스템 연결
3. 플러그인은 자동으로 주입된 리소스 우선 사용

---

## 📚 핵심 함수

### 코어 함수

#### `wv($plugin_name='')` - Weaver 인스턴스 접근

```php
// 코어 인스턴스
$weaver = wv();

// 플러그인 로드 및 접근
$location = wv('location');
$page = wv('page');

// 체인 방식
wv()->location->render_widget('map', $data);
```

#### `wv_info($info)` - 환경 정보 조회

```php
wv_info('path');        // 현재 경로 (예: 'bbs', 'plugin')
wv_info('is_mobile');   // 모바일 여부
wv_info('device');      // 'pc' 또는 'mobile'
wv_info('is_admin');    // 관리자 여부
```

#### `wv_load($plugin)` - 플러그인 로드

```php
// 단일 로드
wv_load('location');

// 다중 로드
wv_load(array('location', 'page', 'widget'));
```

#### `wv_error($msg, $level=0)` - 에러 처리

```php
wv_error('에러 메시지', 0);  // 경고
wv_error('에러 메시지', 1);  // 일반 에러
wv_error('에러 메시지', 2);  // 치명적 에러 (종료)
```

---

### 플러그인 관련 함수

#### `wv_plugin_exists($plugin_name)` - 플러그인 존재 확인

```php
if (wv_plugin_exists('location')) {
    wv_load('location');
}
```

#### `wv_widget($skin, $data)` - Widget 렌더링

```php
// Widget 플러그인 래퍼
echo wv_widget('location/map', array(
    'markers' => $markers,
    'initial_level' => 8
));

// 내부적으로 다음과 동일:
// wv('widget')->make('widget')->display_widget('location/map', $data)
```

---

### 유틸리티 함수

#### `wv_make_skin_id()` - 고유 스킨 ID 생성

```php
$skin_id = wv_make_skin_id();
// 'skin-abc123def456'
```

#### `wv_make_skin_selector($skin_id)` - CSS 선택자 생성

```php
$skin_id = wv_make_skin_id();
$skin_selector = wv_make_skin_selector($skin_id);
// '#skin-abc123def456'
```

#### `wv_class_to_plugin_name($class)` - 클래스명 → 플러그인명

```php
$plugin_name = wv_class_to_plugin_name('weaver\\StoreManager');
// 'store_manager'
```

#### `wv_plugin_name_to_class($plugin_name)` - 플러그인명 → 클래스명

```php
$class = wv_plugin_name_to_class('store_manager');
// 'StoreManager'
```

---

## 🔨 플러그인 개발 가이드

### 기본 플러그인 생성

#### 1. 디렉토리 구조 생성

```
plugins/my_plugin/
├── MyPlugin.php                (메인 클래스)
├── plugin.php                  (로더)
└── theme/
    └── basic/
        ├── pc/
        └── mobile/
```

#### 2. 플러그인 클래스 작성

**파일**: `plugins/my_plugin/MyPlugin.php`

```php
<?php
namespace weaver;

class MyPlugin extends Plugin {
    
    public function __construct(){
        // 생성자 (선택)
    }
    
    public function init_once(){
        // 1회 초기화 (선택)
        add_javascript('...');
        add_stylesheet('...');
    }
    
    public function my_method(){
        // 메서드
        return 'Hello Weaver!';
    }
}

MyPlugin::getInstance();
```

#### 3. 로더 파일 작성

**파일**: `plugins/my_plugin/plugin.php`

```php
<?php
if (!defined('_GNUBOARD_')) exit;

include_once dirname(__FILE__).'/MyPlugin.php';
```

#### 4. 사용

```php
// 로드
wv_load('my_plugin');

// 사용
echo wv()->my_plugin->my_method();
// 'Hello Weaver!'
```

---

### Makeable 플러그인 생성

#### 1. 클래스 작성

**파일**: `plugins/my_makeable/MyMakeable.php`

```php
<?php
namespace weaver;

class MyMakeable extends Makeable {
    
    protected $name;
    protected $config;
    
    public function __construct($id, $name, $config = array()){
        $this->name = $name;
        $this->config = $config;
    }
    
    public function init_once(){
        // 플러그인당 1회 실행
        add_javascript('...');
    }
    
    public function get_name(){
        return $this->name;
    }
}

MyMakeable::getInstance();
```

#### 2. 사용

```php
// 인스턴스 생성
wv()->my_makeable->make('first', 'First Instance', array('key' => 'value'));
wv()->my_makeable->make('second', 'Second Instance');

// 인스턴스 사용
$first = wv()->my_makeable->made('first');
echo $first->get_name(); // 'First Instance'

$second = wv()->my_makeable->made('second');
echo $second->get_name(); // 'Second Instance'

// 전체 조회
$all = wv()->my_makeable->made_all();
// array('first' => object, 'second' => object)
```

---

## 🎯 실전 패턴

### 1. 플러그인 간 통신

```php
// Location 플러그인 사용
if (wv_plugin_exists('location')) {
    $result = wv()->location->api_search('영도구 맛집');
    // 결과 사용
}
```

### 2. 조건부 기능 활성화

```php
// setting.php에서
if (wv_plugin_exists('location')) {
    wv_load('location');
    // Location 기능 활성화
}

if (wv_plugin_exists('store_manager')) {
    wv()->store_manager->make('sub01_01', 'sub01_01', array('store', 'location'));
}
```

### 3. 플러그인 설정

```php
// 플러그인에서
$this->config = wv()->configs->get('my_plugin');

// 또는 자동 설정됨
// $this->config에 자동으로 할당
```

### 4. 에러 처리

```php
class MyPlugin extends Plugin {
    
    public function my_method($param){
        if (!$param) {
            $this->error('파라미터가 필요합니다.', 1);
        }
        
        // 처리
    }
}
```

### 5. 테마 분기

```php
public function render(){
    $device = wv_info('device');
    
    if ($device === 'mobile') {
        return $this->make_skin('mobile_template');
    } else {
        return $this->make_skin('pc_template');
    }
}
```

---

## 🚀 고급 기능

### 1. 동적 스킨 로딩

```php
public function render_dynamic($type){
    $skin_name = 'dynamic_' . $type;
    
    return $this->make_skin($skin_name, array(
        'type' => $type,
        'data' => $this->get_data($type)
    ));
}
```

### 2. 주입 스킨 확인

```php
// 다른 플러그인이 주입한 스킨 체크
$injection_plugins = $this->get_injection_plugins();

foreach ($injection_plugins as $plugin_path) {
    $plugin_name = basename($plugin_path);
    echo "주입 플러그인: {$plugin_name}\n";
}
```

### 3. 테마 변경 이벤트

```php
// Page 플러그인 예시
public function set_theme($theme_name){
    $this->set_theme_dir($theme_name);
    
    // 테마 변경 이벤트 발생
    do_action('page_theme_changed', $theme_name);
}
```

---

## 📝 개발 시 주의사항

### 1. 네임스페이스

- 모든 플러그인 클래스: `namespace weaver;`
- 하위 네임스페이스 사용 가능: `namespace weaver\store_manager;`

### 2. 싱글톤 패턴

```php
// 항상 getInstance() 호출
MyPlugin::getInstance();

// new 사용 금지 (Makeable 제외)
// new MyPlugin(); // ❌
```

### 3. 파일 포함

```php
// 그누보드 체크 불필요 (플러그인 클래스)
// if (!defined('_GNUBOARD_')) exit; // ❌

// 스킨 파일에서는 필수
if (!defined('_GNUBOARD_')) exit; // ✅
```

### 4. 변수 네이밍

```php
// PHP: snake_case
public function get_user_data(){}

// JavaScript: snake_case
function get_user_data(){}
var user_name = '';
```

### 5. CSS 반응형 변수

```php
// HTML/PHP
<div style="margin-top: var(--wv-10); padding: var(--wv-16);">

// CSS
.my-class {
    font-size: var(--wv-14);
    margin-bottom: var(--wv-20);
}
```

---

## 🎓 핵심 개념 요약

1. **플러그인 기반**: 모든 기능은 플러그인으로 구현
2. **Make 패턴**: Makeable을 통한 다중 인스턴스 관리
3. **테마/스킨 시스템**: 유연한 UI 커스터마이징
4. **주입 시스템**: 플러그인 간 리소스 공유
5. **그누보드 비침습적**: 코어 수정 없이 확장
6. **PHP 5.6 호환**: 구형 환경 지원

---

## 📚 추가 리소스

### 플러그인 템플릿

#### 일반 플러그인
```php
<?php
namespace weaver;

class TemplatPlugin extends Plugin {
    
    public function __construct(){
        // 초기화
    }
    
    public function init_once(){
        // 1회 실행 (JS/CSS 로드)
    }
    
    public function my_function(){
        // 기능 구현
    }
}

TemplatPlugin::getInstance();
```

#### Makeable 플러그인
```php
<?php
namespace weaver;

class TemplateMakeable extends Makeable {
    
    protected $id;
    protected $options;
    
    public function __construct($id, $options = array()){
        $this->id = $id;
        $this->options = $options;
    }
    
    public function init_once(){
        // 플러그인당 1회
    }
    
    public function process(){
        // 인스턴스별 처리
    }
}

TemplateMakeable::getInstance();
```

---

## 🔍 문제 해결

### 플러그인이 로드되지 않을 때

```php
// 1. 플러그인 존재 확인
if (!wv_plugin_exists('my_plugin')) {
    echo '플러그인이 없습니다.';
}

// 2. plugin.php 확인
// plugins/my_plugin/plugin.php 존재하는지 확인

// 3. 네임스페이스 확인
// namespace weaver; 선언했는지 확인
```

### 스킨이 렌더링 안 될 때

```php
// 1. 경로 확인
// plugins/{plugin}/theme/{theme_dir}/{device}/{skin_name}/skin.php

// 2. 테마 디렉토리 확인
echo $this->plugin_theme_path;

// 3. 스킨 파일 존재 확인
$skin_path = $this->plugin_theme_path . '/pc/my_skin/skin.php';
if (!file_exists($skin_path)) {
    echo '스킨 파일이 없습니다: ' . $skin_path;
}
```

### Make 인스턴스가 안 될 때

```php
// 1. Makeable 상속 확인
class MyPlugin extends Makeable { } // ✅

// 2. getInstance() 호출 확인
MyPlugin::getInstance(); // 필수

// 3. make() 전에 로드 확인
wv_load('my_plugin');
wv()->my_plugin->make('id');
```

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-01-02  
**작성자**: Claude AI