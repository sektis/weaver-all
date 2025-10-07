# 📦 Assets 플러그인 가이드

> **Weaver 프로젝트의 자산(CSS/JS/Font) 관리 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [Assets 플러그인 구조](#assets-플러그인-구조)
3. [기본 사용법](#기본-사용법)
4. [주요 라이브러리](#주요-라이브러리)
5. [실전 패턴](#실전-패턴)
6. [문제 해결](#문제-해결)

---

## 📌 개요

**Assets 플러그인**은 Weaver 프로젝트에서 CSS, JavaScript, 폰트 등의 자산을 체계적으로 관리하는 플러그인입니다.

### 핵심 특징

- ✅ **자동 버전 관리**: CSS/JS 파일에 자동으로 버전 파라미터 추가
- ✅ **vendor 기반 관리**: 라이브러리별로 독립적인 폴더 구조
- ✅ **config.php 지원**: 각 라이브러리별 초기화 코드 실행
- ✅ **min 파일 우선**: .min.css/.min.js 파일이 있으면 자동 우선 로드
- ✅ **중복 방지**: 동일 라이브러리 중복 로드 방지

---

## 🏗️ Assets 플러그인 구조

### 디렉토리 구조

```
plugins/assets/
├── Assets.php              # 메인 클래스
├── plugin.php             # 플러그인 진입점
├── library/               # JavaScript/CSS 라이브러리
│   ├── bootstrap/
│   │   ├── config.php     # 초기화 코드
│   │   ├── bootstrap.min.css
│   │   └── bootstrap.min.js
│   ├── weaver/           # Weaver 공통 JS
│   │   ├── js/
│   │   │   └── common.js
│   │   └── css/
│   ├── weaver_ajax/      # AJAX 처리
│   │   ├── config.php
│   │   └── js/
│   ├── weaver_bf_file/   # 파일 업로드 미리보기
│   │   ├── config.php
│   │   ├── js/preview.js
│   │   └── css/bf_file.css
│   └── weaver_spam/      # 스팸 방지
│       ├── config.php
│       └── spam_check.php
└── font/                  # 웹폰트
    ├── pretendard/
    ├── roboto_mono/
    └── montserrat/
```

---

## 🎯 기본 사용법

### Assets 플러그인 클래스

```php
<?php
namespace weaver;

class Assets extends Plugin {
    public $js = array();      // JS 파일 배열
    public $css = array();     // CSS 파일 배열
    public $link = array();    // Link 태그 배열
    public $script = array();  // Script 태그 배열
}
```

### 라이브러리 추가

#### 1. 개별 라이브러리 추가

```php
// 단일 라이브러리
wv('assets')->add_library('bootstrap');

// 복수 라이브러리
wv('assets')->add_library(array('bootstrap', 'weaver', 'weaver_ajax'));
```

#### 2. 폰트 추가

```php
// 단일 폰트
wv('assets')->add_font('pretendard');

// 복수 폰트
wv('assets')->add_font(array('pretendard', 'roboto_mono', 'montserrat'));
```

#### 3. 전체 라이브러리/폰트 추가 (특정 제외)

```php
// 모든 라이브러리 추가 (jquery 제외)
wv('assets')->add_all_library('jquery');

// 모든 라이브러리 추가 (여러 개 제외)
wv('assets')->add_all_library(array('jquery', 'old_library'));

// 모든 폰트 추가
wv('assets')->add_all_font();
```

### setting.php 사용 예시

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// Assets 플러그인 로드
wv()->load('assets');

// 라이브러리 추가
wv('assets')->add_library(array(
    'weaver',
    'weaver_ajax',
    'weaver_bf_file',
    'bootstrap',
    'hc_sticky',
    'font_awesome',
    'swiper11',
    'animate_css'
));

// 폰트 추가
wv('assets')->add_font(array(
    'pretendard',
    'roboto_mono',
    'montserrat'
));
```

---

## 📚 주요 라이브러리

### 1. Bootstrap

**위치**: `plugins/assets/library/bootstrap/`

**기능**:
- Bootstrap 5 CSS/JS 프레임워크
- 자동으로 viewport 메타 태그 추가 (PC만)

**config.php 동작**:

```php
// viewport 메타 태그 자동 추가 (모바일 제외)
add_event('common_header','wv_assets_plugin_bootstrap_add_meta',0);

function wv_assets_plugin_bootstrap_add_meta() {
    if(G5_IS_MOBILE) return;
    wv_add_config_meta('<meta name="viewport" content="width=device-width, initial-scale=1">');
}

// Bootstrap CSS 중복 제거 (Weaver CSS 사용 시)
add_event('wv_hook_assets_before_add_assets','wv_assets_plugin_bootstrap');

function wv_assets_plugin_bootstrap() {
    unset(wv('assets')->css['bootstrap_bootstrap']);
}
```

**특징**:
- 자동으로 반응형 viewport 설정
- Weaver CSS와 충돌 방지

---

#### Bootstrap z-config.js - 핵심 JavaScript 함수

**위치**: `plugins/assets/library/bootstrap/js/z-config.js`

이 파일은 Bootstrap과 Weaver를 연결하는 핵심 JavaScript 함수들을 제공합니다.

##### 주요 기능 목록

1. **AJAX 시스템**
    - `parseWvAjaxOptions()` - 옵션 파싱
    - `parseWvAjaxData()` - 데이터 파싱
    - `wv_ajax()` - 일반 AJAX
    - `wv_ajax_modal()` - 모달 AJAX
    - `wv_ajax_offcanvas()` - 오프캔버스 AJAX

2. **리로드 시스템**
    - `wv_reload_modal()` - 모달 리로드
    - `wv_reload_offcanvas()` - 오프캔버스 리로드
    - `wv_reload_current_modal()` - 현재 모달 리로드
    - `wv_handle_parent_reload()` - 부모 리로드

3. **Bootstrap 컴포넌트**
    - 탭 (URL Hash 지원)
    - Popover / Tooltip 자동 초기화
    - Modal Portal (z-index 문제 해결)
    - Dropdown Select (커스텀)

4. **유틸리티**
    - `data-wv-ajax-url` 자동 처리
    - `data-on-value` / `data-off-value` 체크박스 텍스트

---

##### 1. data-wv-ajax-url - 선언적 AJAX

가장 자주 사용하는 기능으로, HTML 속성만으로 AJAX를 처리합니다.

**HTML 속성**:
- `data-wv-ajax-url` - AJAX URL (필수)
- `data-wv-ajax-data` - 전송 데이터 (JSON)
- `data-wv-ajax-data-add` - 추가 데이터 (기존 데이터와 병합)
- `data-wv-ajax-option` - 옵션 (문자열)

**옵션 종류**:

| 옵션 | 설명 | 예시 |
|------|------|------|
| `modal` | 모달로 열기 | `data-wv-ajax-option="modal"` |
| `offcanvas` | 오프캔버스로 열기 | `data-wv-ajax-option="offcanvas"` |
| `centered` | 모달 중앙 정렬 | `data-wv-ajax-option="modal,centered"` |
| `scrollable` | 모달 스크롤 | `data-wv-ajax-option="modal,scrollable"` |
| `lg/xl/sm` | 모달 크기 | `data-wv-ajax-option="modal,lg"` |
| `end/top/bottom` | 오프캔버스 위치 | `data-wv-ajax-option="offcanvas,end"` |
| `backdrop` | 백드롭 사용 | `data-wv-ajax-option="offcanvas,backdrop"` |
| `backdrop-static` | 백드롭 클릭 무시 | `data-wv-ajax-option="modal,backdrop-static"` |
| `class:클래스명` | 커스텀 클래스 | `data-wv-ajax-option="offcanvas,class:w-[360px]"` |
| `id:아이디` | 커스텀 ID | `data-wv-ajax-option="modal,id:my-modal"` |
| `replace_in:#선택자` | 내부 HTML 교체 | `data-wv-ajax-option="replace_in:#content"` |
| `replace_with:#선택자` | 전체 요소 교체 | `data-wv-ajax-option="replace_with:#item"` |
| `append:#선택자` | 끝에 추가 | `data-wv-ajax-option="append:#list"` |
| `reload_ajax:true` | 완료 후 리로드 | `data-wv-ajax-option="reload_ajax:true"` |
| `reload_ajax:parent` | 부모 리로드 | `data-wv-ajax-option="reload_ajax:parent"` |
| `reload_ajax:on_close` | 닫힐 때 리로드 | `data-wv-ajax-option="modal,reload_ajax:on_close"` |

**사용 예시**:

```html
<!-- 1. 기본 AJAX (DOM 교체 없음) -->
<a href="#" 
   data-wv-ajax-url="/api/update" 
   data-wv-ajax-data='{"id":123}'>
    업데이트
</a>

<!-- 2. 모달로 열기 -->
<a href="#" 
   data-wv-ajax-url="/modal/content" 
   data-wv-ajax-data='{"id":123}'
   data-wv-ajax-option="modal,centered,lg">
    상세보기
</a>

<!-- 3. 오프캔버스로 열기 (오른쪽, 360px 너비) -->
<a href="#" 
   data-wv-ajax-url="/form/edit" 
   data-wv-ajax-data='{"id":456}'
   data-wv-ajax-option="offcanvas,end,backdrop,class:w-[360px]">
    수정
</a>

<!-- 4. DOM 교체 -->
<a href="#" 
   data-wv-ajax-url="/get/list" 
   data-wv-ajax-option="replace_in:#product-list">
    목록 새로고침
</a>

<!-- 5. 업데이트 후 리로드 -->
<a href="#" 
   data-wv-ajax-url="/submit/data" 
   data-wv-ajax-data='{"value":"test"}'
   data-wv-ajax-option="reload_ajax:true">
    저장 후 리로드
</a>

<!-- 6. 모달 닫힐 때 부모 리로드 -->
<a href="#" 
   data-wv-ajax-url="/modal/form" 
   data-wv-ajax-option="modal,reload_ajax:on_close">
    수정 (닫힐 때 리로드)
</a>

<!-- 7. 데이터 병합 (data + data-add) -->
<a href="#" 
   data-wv-ajax-url="/submit" 
   data-wv-ajax-data='{"base":"value"}'
   data-wv-ajax-data-add='{"extra":"data"}'>
    추가 데이터 포함
</a>
```

---

##### 2. JavaScript 함수 직접 호출

**wv_ajax()** - 일반 AJAX:
```javascript
// 기본 사용
wv_ajax('/api/data', {}, {id: 123});

// DOM 교체
wv_ajax('/get/content', 'replace_in:#target', {});

// 부모 리로드
wv_ajax('/update', 'reload_ajax:parent', {data: 'value'});
```

**wv_ajax_modal()** - 모달 AJAX:
```javascript
// 기본 모달
wv_ajax_modal('/modal/content', 'modal', {id: 123});

// 중앙 정렬 + 큰 모달
wv_ajax_modal('/modal/detail', 'modal,centered,lg', {});

// 닫힐 때 리로드
wv_ajax_modal('/modal/edit', 'modal,reload_ajax:on_close', {id: 456});

// 옵션 객체로 전달
wv_ajax_modal('/modal/custom', {
    type: 'modal',
    class: 'my-modal',
    id: 'custom-modal',
    other: ['centered', 'scrollable']
}, {data: 'value'});
```

**wv_ajax_offcanvas()** - 오프캔버스 AJAX:
```javascript
// 기본 (왼쪽)
wv_ajax_offcanvas('/offcanvas/menu', 'offcanvas', {});

// 오른쪽 + 백드롭 + 커스텀 너비
wv_ajax_offcanvas('/offcanvas/form', 'offcanvas,end,backdrop,class:w-[360px]', {});

// 닫힐 때 리로드
wv_ajax_offcanvas('/offcanvas/edit', 'offcanvas,end,reload_ajax:on_close', {id: 789});
```

---

##### 3. 리로드 함수들

**wv_reload_modal()** - 특정 모달 리로드:
```javascript
// ID로 모달 리로드
wv_reload_modal('wv-modal-abc123');

// 리로드 카운트 확인
var $modal = $('#wv-modal-abc123');
console.log($modal.attr('data-wv-reload-count'));
```

**wv_reload_offcanvas()** - 특정 오프캔버스 리로드:
```javascript
// ID로 오프캔버스 리로드
wv_reload_offcanvas('wv-offcanvas-xyz456');
```

**wv_reload_current_modal()** - 현재 활성 모달 리로드:
```javascript
// 현재 열려있는 모달 리로드
wv_reload_current_modal();

// 폼 제출 후 리로드
$('form').ajaxForm({
    success: function() {
        wv_reload_current_modal();
    }
});
```

**wv_handle_parent_reload()** - 부모 리로드 (자동 감지):
```javascript
// 즉시 부모 리로드
wv_handle_parent_reload($clickElement, false);

// 닫힐 때 부모 리로드 (이벤트에서 호출)
wv_handle_parent_reload($modal, true);
```

---

##### 4. Bootstrap 컴포넌트 자동 초기화

**탭 - URL Hash 지원**:
```html
<!-- URL: /page#tab-2 로 접속하면 자동으로 해당 탭 활성화 -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-1">탭 1</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-2">탭 2</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane" id="tab-1">내용 1</div>
    <div class="tab-pane" id="tab-2">내용 2</div>
</div>

<script>
// 탭 변경 시 URL Hash 자동 업데이트
// /page → /page#tab-2
</script>
```

**Popover / Tooltip 자동 초기화**:
```html
<!-- 초기 로드 시 자동 초기화 -->
<button data-bs-toggle="popover" data-bs-content="팝오버 내용">팝오버</button>
<button data-bs-toggle="tooltip" title="툴팁 내용">툴팁</button>

<!-- AJAX로 추가된 요소도 자동 초기화 -->
<script>
$('#container').html('<button data-bs-toggle="tooltip" title="동적 툴팁">버튼</button>');
// $(document).loaded()가 자동으로 초기화
</script>
```

---

##### 5. Modal Portal 기능

Modal Portal은 z-index 문제를 해결하기 위해 모달을 `body`로 이동시키는 기능입니다.

**동작 방식**:
```html
<!-- wv-modal-portal 클래스 추가 -->
<div id="my-modal" class="modal wv-modal-portal">
    <div class="modal-dialog">
        <div class="modal-content">...</div>
    </div>
</div>

<script>
// 1. 모달 열기 시:
//    - 원래 위치 저장 (WeakMap)
//    - body로 이동 (z-index 문제 해결)

// 2. 모달 닫기 시:
//    - body에서 제거
//    - 원래 위치로 복원

// 3. 중복 이동 방지:
//    - WeakMap으로 상태 관리
</script>
```

**자동 적용**:
- `wv_ajax_modal()`로 생성된 모달은 자동으로 `wv-modal-portal` 클래스 적용
- 수동 생성 시에도 클래스만 추가하면 자동 동작

---

##### 6. wv-dropdown-select - 커스텀 드롭다운

Bootstrap 드롭다운을 Select처럼 사용할 수 있게 해주는 기능입니다.

**HTML**:
```html
<div class="wv-dropdown-select dropdown">
    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
        <span class="dropdown-label">선택하세요</span>
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">옵션 1</a></li>
        <li><a class="dropdown-item selected" href="#">옵션 2</a></li>
        <li><a class="dropdown-item" href="#">옵션 3</a></li>
    </ul>
</div>
```

**JavaScript**:
```javascript
// 선택 변경 이벤트 감지
$('.wv-dropdown-select').on('wv.dropdown.change', function() {
    var selected = $(this).find('.dropdown-label').text();
    console.log('선택됨:', selected);
});

// 선택 값 가져오기
var currentSelection = $('.wv-dropdown-select .dropdown-label').text();
```

**동작**:
1. `.selected` 클래스가 있는 항목을 초기 선택값으로 표시
2. 항목 클릭 시 라벨 텍스트 업데이트
3. `wv.dropdown.change` 이벤트 발생

---

##### 7. data-on-value / data-off-value - 체크박스 텍스트

체크박스 상태에 따라 텍스트를 자동으로 변경하는 기능입니다.

**기본 사용**:
```html
<div class="form-check form-switch" data-on-value="켜짐" data-off-value="꺼짐">
    <input class="form-check-input" type="checkbox" id="switch1">
    <label class="form-check-label" for="switch1">꺼짐</label>
</div>
```

**span 사용**:
```html
<label class="form-check form-switch" data-on-value="활성" data-off-value="비활성">
    <input class="form-check-input" type="checkbox">
    <span>비활성</span>
</label>
```

**동작**:
- 체크 → `data-on-value` 텍스트 표시
- 언체크 → `data-off-value` 텍스트 표시
- 초기값 자동 설정

---

##### 8. 전체 워크플로우

```
사용자 클릭
    ↓
[data-wv-ajax-url] 감지
    ↓
parseWvAjaxOptions() - 옵션 파싱
    ↓
parseWvAjaxData() - 데이터 파싱
    ↓
타입 판별:
    - modal → wv_ajax_modal()
    - offcanvas → wv_ajax_offcanvas()
    - 없음 → wv_ajax()
    ↓
AJAX 요청 실행
    ↓
응답 처리:
    - replace_in → $(selector).html()
    - append → $(selector).append()
    - modal/offcanvas → .modal-content 삽입
    ↓
리로드 처리:
    - reload_ajax:parent → wv_handle_parent_reload()
    - reload_ajax:on_close → 닫힐 때 리로드 예약
    ↓
리로드 실행:
    - 부모 modal/offcanvas 찾기
    - wv_reload_modal() 또는 wv_reload_offcanvas()
    - 없으면 location.reload()
```

---

##### 9. 고급 활용 예시

**중첩 모달/오프캔버스**:
```html
<!-- 모달 1 -->
<div id="modal-1" class="modal">
    <div class="modal-content">
        <!-- 모달 2 열기 버튼 -->
        <a href="#" 
           data-wv-ajax-url="/modal2" 
           data-wv-ajax-option="modal,reload_ajax:on_close">
            모달 2 열기
        </a>
    </div>
</div>

<!-- 모달 2가 닫힐 때 모달 1 자동 리로드 -->
```

**폼 제출 + 리로드**:
```html
<form id="my-form">
    <input name="data" value="test">
    <button type="submit">저장</button>
</form>

<script>
$('#my-form').ajaxForm({
    url: '/submit',
    reload_ajax: true, // 제출 후 부모 리로드
    success: function() {
        console.log('저장 완료!');
    }
});
</script>
```

**조건부 리로드**:
```html
<a href="#" 
   data-wv-ajax-url="/check" 
   data-wv-ajax-option="modal,reload_ajax:on_close">
    확인
</a>

<script>
// 모달 내부에서 조건부 리로드
$('.save-btn').click(function() {
    if (isDataChanged) {
        // 리로드 카운트 증가 (닫힐 때 리로드됨)
        var $modal = $(this).closest('.modal');
        var count = parseInt($modal.attr('data-wv-reload-count') || '0');
        $modal.attr('data-wv-reload-count', count + 1);
    }
});
</script>
```

---

### 2. Weaver (공통 JS)

**위치**: `plugins/assets/library/weaver/`

**주요 파일**:
- `js/common.js`: Weaver 공통 JavaScript 함수
- `css/common.css`: Weaver 공통 스타일

**common.js 주요 기능**:

#### $(document).loaded() - DOM 로드 체크

```javascript
// 특정 셀렉터가 DOM에 존재하면 콜백 실행
$(document).loaded('.my-element', function() {
    var $this = $(this); // 해당 요소
    console.log('요소가 로드됨:', $this);
});

// 복수 요소도 각각 실행
$(document).loaded('.item', function() {
    console.log('각 아이템마다 실행');
});
```

#### wv-scroll-more - 스크롤 끝 더보기 버튼

```javascript
// 스크롤 끝에 도달하면 자동으로 더보기 버튼 생성
$(document).loaded('.wv-scroll-more', function() {
    var $container = $(this);
    
    // 더보기 버튼 자동 생성
    // wv-scroll-more-hidden 클래스가 있으면 비활성화
});
```

**HTML 사용 예시**:

```html
<!-- 기본 사용 -->
<div class="wv-scroll-more" style="height: 300px; overflow-y: auto;">
    <div class="item">항목 1</div>
    <div class="item">항목 2</div>
    <!-- 스크롤 끝에 도달하면 더보기 버튼 자동 생성 -->
</div>

<!-- 더보기 비활성화 -->
<div class="wv-scroll-more wv-scroll-more-hidden">
    <!-- 더보기 버튼 생성 안 됨 -->
</div>

<!-- AJAX 연동 -->
<div class="wv-scroll-more" 
     data-wv-ajax-url="/api/load-more" 
     data-wv-ajax-data='{"page":2}'>
    <!-- AJAX로 더보기 처리 -->
</div>
```

---

### 3. Weaver AJAX

**위치**: `plugins/assets/library/weaver_ajax/`

**기능**:
- AJAX 요청 시 자동 동작
- JSON/일반 응답 자동 구분
- JS/CSS 자동 수집 및 출력
- `no_layout` 시 head/tail 자동 제거

**config.php 동작**:

```php
// AJAX 요청 시에만 동작
if(wv_is_ajax()) {
    // 스킨 ID/선택자 생성
    global $skin_id, $skin_selector;
    $skin_id = wv_make_skin_id();
    $skin_selector = wv_make_skin_selector($skin_id);
    
    // alert 이벤트 처리 (JSON/일반 응답 구분)
    add_event('alert', 'wv_assets_plugin_weaver_ajax', 0, 4);
    
    // no_layout 시 head/tail 제거
    global $no_layout;
    if($no_layout) {
        add_replace('wv_hook_act_code_layout_replace', 
                    'wv_assets_plugin_weaver_ajax_no_layout', -1, 3);
    }
    
    // HTML Process 확장 (JS/CSS 자동 수집)
    if(class_exists('html_process')) {
        // ... JS/CSS 수집 및 출력 로직
    }
}
```

**alert 함수 처리**:

```php
function wv_assets_plugin_weaver_ajax($msg, $url, $error, $post) {
    $is_json_request = false;
    
    // JSON 요청 감지
    if(strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        $is_json_request = true;
    }
    
    if($error) {
        // 에러 처리 (400)
        global $wv_ajax_add_resource;
        $wv_ajax_add_resource = false;
        $msg = str_replace('\\n', "\n", $msg);
        wv_abort(400, $msg, $url);
    }
    
    if($is_json_request) {
        // JSON 응답
        wv_json_exit(array('result' => true, 'content' => $msg));
    } else {
        // 일반 응답 (200)
        wv_abort(200, $msg, $url);
    }
}
```

**특징**:
- AJAX 요청 시 자동으로 활성화
- JS/CSS 자동 수집하여 응답에 포함
- `no_layout` 옵션 시 레이아웃 자동 제거
- JSON/일반 응답 자동 구분

**AJAX 요청 예시**:

```javascript
// JSON 요청
$.ajax({
    url: '/some/ajax/url',
    headers: {
        'Accept': 'application/json'
    },
    success: function(response) {
        // response.result = true
        // response.content = 내용
    }
});

// 일반 요청 (HTML)
$.ajax({
    url: '/some/ajax/url',
    success: function(html) {
        // HTML + JS/CSS 태그 포함
    }
});
```

---

### 4. Weaver BF File (파일 업로드 미리보기)

**위치**: `plugins/assets/library/weaver_bf_file/`

**주요 파일**:
- `config.php`: 파일 업로드 전처리
- `js/preview.js`: 미리보기 JavaScript
- `css/bf_file.css`: 미리보기 스타일
- `inc_write.php`: 수정 시 기존 파일 표시

**기능**:
- 파일 업로드 미리보기 (이미지/비디오)
- 다중 파일 순서 관리
- 파일 삭제 처리
- 수정 시 기존 파일 표시

**HTML 구조**:

```html
<!-- 단일 파일 업로드 -->
<div class="wv-preview-wrap">
    <input type="file" id="bf_file_0" name="bf_file[0]" class="wv-file-preview">
    <label for="bf_file_0" class="ratio ratio-1x1">
        <!-- 미리보기가 여기에 표시됨 -->
    </label>
</div>

<!-- 다중 파일 업로드 -->
<div class="wv-multiple">
    <div class="wv-preview-wrap">
        <input type="file" id="bf_file_0" name="bf_file[0]" class="wv-file-preview">
        <input type="hidden" name="wv_multiple_order[0]" value="0">
    </div>
    <div class="wv-preview-wrap">
        <input type="file" id="bf_file_1" name="bf_file[1]" class="wv-file-preview">
        <input type="hidden" name="wv_multiple_order[1]" value="1">
    </div>
</div>
```

**JavaScript 사용**:

```javascript
// 자동 초기화
$(document).ready(function() {
    $("form[name='fwrite']").loaded(".wv-file-preview", function() {
        $(this).wv_preview_file();
    });
});

// 수동 초기화
$('.my-file-input').wv_preview_file();
```

**CSS 변수**:

```css
:root {
    --wv-prewview-wrap-width: 20;        /* 미리보기 박스 너비 (%) */
    --wv-prewview-wrap-gap: 1em;         /* 박스 간 간격 */
    --wv-prewview-wrap-row-gap: 1em;     /* 줄 간격 */
}

@media (max-width: 991.98px) {
    :root {
        --wv-prewview-wrap-width: 33.333 !important; /* 모바일: 3열 */
    }
}
```

**config.php 주요 기능**:

#### 1. 파일 업로드 전처리

```php
add_event('write_update_before', 'wv_assets_plugin_write_update_before', 1, 4);

function wv_assets_plugin_write_update_before($board, $wr_id, $w, $qstr) {
    // 1. AJAX 삭제 시 더미 파일명 추가
    if(wv_is_ajax() and is_array($_POST['bf_file_del'])) {
        foreach ($_POST['bf_file_del'] as $key => $val) {
            if(!$_FILES['bf_file']['name'][$key]) {
                $_FILES['bf_file']['name'][$key] = '.';
            }
        }
    }
    
    // 2. 파일 배열 정렬 (krsort)
    // 3. 빈 인덱스 더미 추가
    // 4. 다중 파일 순서 변경 처리 (wv_multiple_order)
}
```

#### 2. 빈 파일 레코드 삭제

```php
add_event('write_update_file_insert', 'wv_assets_plugin_write_update_file_insert', 1, 3);

function wv_assets_plugin_write_update_file_insert($bo_table, $wr_id, $upload) {
    if($upload['file']) return;
    
    // 파일명이 빈 레코드 삭제
    sql_query("DELETE FROM {$g5['board_file_table']} 
               WHERE bo_table = '{$bo_table}' 
               AND wr_id = '{$wr_id}' 
               AND bf_file = ''");
}
```

#### 3. 수정 시 기존 파일 표시

```php
add_event('tail_sub', 'wv_assets_plugin_file_preview_tail_sub', 1, 0);

function wv_assets_plugin_file_preview_tail_sub() {
    global $wr_id, $w, $board;
    
    if($wr_id and $w == 'u') {
        include_once dirname(__FILE__).'/inc_write.php';
    }
}
```

**inc_write.php**:

```php
<?php
// 기존 파일 정보 조회
$files = get_file($board['bo_table'], $wr_id);
?>
<script>
$(document).ready(function() {
    var files = <?php echo count($files) ? json_encode($files) : json_encode(array()); ?>;
    var attr_name = 'data-bf-file';
    
    $("form[name='fwrite']").loaded("[name^='bf_file[']", function() {
        var $this = $(this);
        var match = $this.attr('name').match(/.*\[(.*)\]$/);
        
        if(!match[1]) return false;
        var i = parseInt(match[1]);
        var f = files[i];
        
        // 기존 파일 경로를 data 속성에 저장
        if(f && f.file) {
            $this[0].setAttribute(attr_name, f.path + '/' + f.file);
        }
    });
});
</script>
```

**preview.js 주요 기능**:

#### $.fn.wv_preview_file()

```javascript
$.fn.wv_preview_file = function() {
    wv_preview_file_do($(this));
};

function wv_preview_file_do($this) {
    var input = $this[0];
    var $form = $this.closest('form');
    var $preview_area = $("[for=" + $this.attr('id') + "]", $form);
    
    // multiple 속성이 있으면 처리 안 함
    if($this.is("[multiple]")) {
        return false;
    }
    
    // input 숨기기
    $this.addClass('d-none');
    
    // wv-preview-wrap으로 감싸기
    if(window.getComputedStyle($this.parent()[0]).position !== 'relative') {
        $this.wrap('<div class="wv-preview-wrap"></div>');
    }
    
    var $preview_wrap = $this.closest('.wv-preview-wrap');
    
    // wv-multiple 내부인 경우 순서 관리 input 추가
    if($this.closest('.wv-multiple').length) {
        var attr_name = $this.attr('name');
        var match = attr_name.match(/.*\[(.*)\]$/);
        $preview_wrap.append('<input type="hidden" name="wv_multiple_order[' + match[1] + ']" value="' + match[1] + '" >');
    }
    
    // 미리보기 영역 생성
    if($preview_area.length == 0) {
        $preview_area = $('<label class="ratio ratio-1x1 w-100 overflow-hidden" for="' + $this.attr('id') + '"></label>')
            .insertAfter($this);
    }
    
    // 기존 파일 체크 (data-bf-file 속성)
    var bf_file_check_time = 0;
    var bf_file_check_tik = 50;
    var bf_file_check = setInterval(function() {
        var data_bf_file = $this.data('bf-file');
        if(bf_file_check_time > 1000) {
            clearInterval(bf_file_check);
        }
        if(data_bf_file) {
            wv_insert_preview($this, $preview_area, wv_get_file_type(data_bf_file), data_bf_file);
            clearInterval(bf_file_check);
        }
        bf_file_check_time += bf_file_check_tik;
    }, bf_file_check_tik);
    
    // 파일 선택 시 미리보기
    $this.on('change', function() {
        if (input.files && input.files[0]) {
            var file_type = input.files[0].type.split("/")[0];
            var preview_url = URL.createObjectURL(input.files[0]);
            
            $preview_area.addClass('position-relative');
            if($preview_area.css('background-image')) {
                $preview_area[0].setAttribute('data-background-image', 
                    $preview_area.css('background-image'));
            }
            
            wv_insert_preview($this, $preview_area, file_type, preview_url, 
                input.files[0].name);
        }
    });
}
```

#### wv_insert_preview() - 미리보기 삽입

```javascript
function wv_insert_preview($this, $preview_area, file_type, preview_url, file_name) {
    // 기존 미리보기 제거
    $preview_area.find('img, video, .file-reset').remove();
    
    // 이미지 미리보기
    if(file_type == 'image') {
        var $img = $('<img class="w-100 h-100" style="object-fit: cover;">');
        $img.attr('src', preview_url);
        $preview_area.append($img);
    }
    // 비디오 미리보기
    else if(file_type == 'video') {
        var $video = $('<video class="w-100 h-100" style="object-fit: cover;">');
        $video.attr('src', preview_url);
        $preview_area.append($video);
    }
    
    // 삭제 버튼 추가
    var $reset_btn = $('<button type="button" class="btn btn-sm btn-danger file-reset">삭제</button>');
    $preview_area.append($reset_btn);
    
    // 삭제 버튼 클릭 이벤트
    $reset_btn.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // 미리보기 제거
        $preview_area.find('img, video, .file-reset').remove();
        
        // 배경 이미지 복원
        if($preview_area[0].hasAttribute('data-background-image')) {
            $preview_area.css('background-image', 
                $preview_area[0].getAttribute('data-background-image'));
        }
        
        // input 초기화
        $this.val('');
        $this.trigger('change');
        
        // bf_file_del 체크박스 설정
        var match = $this.attr('name').match(/.*\[(.*)\]$/);
        var $del_input = $("[name='bf_file_del[" + match[1] + "]']");
        
        if($del_input.length) {
            $del_input.attr('checked', true);
        } else {
            $('<input type="hidden" name="bf_file_del[' + match[1] + ']" value="1">')
                .insertAfter($preview_area);
        }
        
        // wv-multiple 내부인 경우 순서 재정렬
        var $multiple = $preview_area.closest('.wv-multiple');
        if($multiple.length) {
            var order = $("[name^='wv_multiple_order[']", $multiple).first().val();
            $multiple.append($preview_area.closest('.wv-preview-wrap').detach());
            
            $("[name^='wv_multiple_order[']", $multiple).each(function(i, e) {
                $(e).val(order);
                order++;
            });
        }
    });
}
```

#### wv_get_file_type() - 파일 타입 판별

```javascript
function wv_get_file_type(fileName) {
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    const videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
    
    // 파일 확장자 추출
    const extension = fileName.split('.').pop().toLowerCase();
    
    if(imageExtensions.includes(extension)) {
        return 'image';
    } else if(videoExtensions.includes(extension)) {
        return 'video';
    } else {
        return 'file';
    }
}
```

**특징**:
- 단일 파일: 미리보기 + 삭제 버튼
- 다중 파일: 순서 관리 + 드래그 이동
- 수정 시: 기존 파일 자동 표시
- 이미지/비디오: 실시간 미리보기
- 반응형: PC/모바일 자동 대응

---

### 5. Weaver Spam (스팸 방지)

**위치**: `plugins/assets/library/weaver_spam/`

**주요 파일**:
- `config.php`: 이벤트 훅 등록
- `spam_check.php`: Honey Spot HTML 생성

**기능**:
- Honey Spot 방식 스팸 방지
- 회원가입/게시글 작성 시 자동 검증
- 봇 접근 차단

**config.php**:

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// tail_sub 이벤트에 Honey Spot 추가
add_event('tail_sub', 'wv_spam_honey_spot_add');

// write_update_before 이벤트에 Honey Spot 검증
add_event('write_update_before', 'wv_spam_honey_spot_check');

// register_form_update_before 이벤트에 Honey Spot 검증
add_event('register_form_update_before', 'wv_spam_honey_spot_check');

function wv_spam_honey_spot_add() {
    global $bo_table;
    $form_id = '';
    
    // 게시글 작성 폼
    if((wv_info('dir') == 'bbs' and wv_info('file') == 'write' and $bo_table)) {
        $form_id = 'fwrite';
    }
    
    // 회원가입 폼
    if((wv_info('dir') == 'bbs' and (wv_info('file') == 'register' or wv_info('file') == 'register_form'))) {
        $form_id = 'fregisterform';
    }
    
    if(!$form_id) {
        return;
    }
    
    include_once dirname(__FILE__).'/spam_check.php';
}

function wv_spam_honey_spot_check() {
    // wv_h_spot 필드 검증
    if(!isset($_POST['wv_h_spot']) or $_POST['wv_h_spot'] != 'wv') {
        alert('스팸으로 의심되어 등록이 불가합니다.');
    }
}
```

**spam_check.php**:

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// Honey Spot HTML 생성
?>
<script>
$(document).ready(function() {
    var $form = $('#<?php echo $form_id; ?>');
    
    // 화면에 보이지 않는 히든 필드 추가
    var $honey_spot = $('<input type="text" name="wv_h_spot" value="" style="position:absolute;left:-9999px;width:1px;height:1px;">');
    $form.append($honey_spot);
    
    // 정상 사용자: 값 자동 설정
    setTimeout(function() {
        $honey_spot.val('wv');
    }, 100);
});
</script>
```

**동작 원리**:

1. **Honey Spot 삽입**:
    - 화면 밖에 숨겨진 input 필드 생성
    - 봇은 이 필드를 찾아서 채움

2. **정상 사용자 구분**:
    - JavaScript로 자동으로 `wv` 값 설정
    - 봇은 JavaScript 실행 안 함

3. **제출 시 검증**:
    - `wv_h_spot` 필드 값이 `wv`가 아니면 차단
    - alert로 스팸 경고 표시

**특징**:
- JavaScript 필수 (봇 차단)
- 화면에 보이지 않음
- 사용자 경험 방해 없음
- 간단하지만 효과적

---

## 🎨 실전 패턴

### 1. 새 라이브러리 추가하기

```
1. plugins/assets/library/my_library/ 폴더 생성
2. my_library.css, my_library.js 파일 추가
3. (선택) config.php 생성 - 초기화 코드 작성
4. setting.php에서 add_library('my_library') 호출
```

**config.php 예시**:

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 이벤트 훅 등록
add_event('tail_sub', 'my_library_init');

function my_library_init() {
    // 초기화 코드
    echo '<script>console.log("My Library Loaded");</script>';
}
```

### 2. 조건부 라이브러리 로드

```php
<?php
// 특정 페이지에서만 로드
if(wv_info('file') == 'write') {
    wv('assets')->add_library('weaver_bf_file');
}

// 관리자만 로드
if($is_admin) {
    wv('assets')->add_library('admin_tools');
}

// 모바일만 로드
if(G5_IS_MOBILE) {
    wv('assets')->add_library('mobile_menu');
}
```

### 3. 라이브러리 제거

```php
<?php
// 특정 라이브러리 제거
add_event('wv_hook_assets_before_add_assets', function() {
    unset(wv('assets')->css['bootstrap_bootstrap']);
    unset(wv('assets')->js['jquery_jquery']);
});
```

### 4. vendor_load() 동작 이해

```php
private function vendor_load($vendor, $dir) {
    $asset_include_path = dirname(__FILE__).'/'.$dir.'/'.$vendor;
    $asset_config_path = $asset_include_path.'/config.php';
    
    // 1. 폴더 존재 체크
    if(!is_dir($asset_include_path)) {
        $this->error("{$vendor} {$dir} not found", 2);
    }
    
    // 2. config.php 실행
    if(file_exists($asset_config_path)) {
        include_once $asset_config_path;
    }
    
    // 3. CSS/JS 파일 자동 검색
    $find_files = wv_glob($asset_include_path, '*.{css,js}');
    
    if(!count($find_files)) return;
    
    foreach ($find_files as $file) {
        $file_info = pathinfo($file);
        $file_name = $file_info['filename'];
        $file_name_remove_min = rtrim($file_name, '.min');
        $file_ext = $file_info['extension'];
        $key_name = $vendor.'_'.$file_name_remove_min;
        
        // 4. 중복 체크 (.min 파일 우선)
        if(@isset($this->$file_ext[$key_name]) and $file_name == $file_name_remove_min) {
            continue;
        }
        
        // 5. 배열에 추가
        if($file_ext == 'css') {
            $this->css[$key_name] = $file;
        } else if($file_ext == 'js') {
            $this->js[$key_name] = $file;
        }
    }
}
```

**동작 순서**:

1. ✅ 폴더 존재 확인
2. ✅ `config.php` 실행 (있으면)
3. ✅ `*.css`, `*.js` 파일 자동 검색
4. ✅ `.min.css`, `.min.js` 우선 로드
5. ✅ `$this->css[]`, `$this->js[]` 배열에 추가

**키 네이밍**:

- `{vendor}_{filename_without_min}`
- 예: `bootstrap_bootstrap`, `weaver_common`

### 5. add_event_tail_sub() - 자동 출력

```php
public function add_event_tail_sub() {
    // 훅 실행
    run_event('wv_hook_assets_before_add_assets');
    
    // JS 파일 출력
    foreach ($this->js as $js) {
        add_javascript('<script src="'.wv_path_replace_url($js).'?ver='.G5_JS_VER.'"></script>', 10);
    }
    
    // CSS 파일 출력
    foreach ($this->css as $css) {
        add_stylesheet('<link rel="stylesheet" href="'.wv_path_replace_url($css).'?ver='.G5_CSS_VER.'">', 10);
    }
}
```

**특징**:
- `tail_sub` 이벤트에서 자동 실행
- 그누보드5의 `add_javascript()`, `add_stylesheet()` 사용
- 버전 파라미터 자동 추가 (`?ver=...`)

---

## 🔍 문제 해결

### 라이브러리가 로드되지 않을 때

```php
// 1. 라이브러리 존재 확인
$library_path = WV_PLUGINS_PATH.'/assets/library/my_library';
if(!is_dir($library_path)) {
    echo '라이브러리 폴더가 없습니다.';
}

// 2. CSS/JS 파일 확인
$files = wv_glob($library_path, '*.{css,js}');
if(!count($files)) {
    echo 'CSS/JS 파일이 없습니다.';
}

// 3. 로드 확인
wv('assets')->add_library('my_library');
print_r(wv('assets')->css);
print_r(wv('assets')->js);
```

### 라이브러리 순서 변경

```php
// CSS/JS 배열 직접 조작
add_event('wv_hook_assets_before_add_assets', function() {
    $assets = wv('assets');
    
    // 순서 변경
    $jquery = $assets->js['jquery_jquery'];
    unset($assets->js['jquery_jquery']);
    
    // 맨 앞에 추가
    $assets->js = array_merge(
        array('jquery_jquery' => $jquery),
        $assets->js
    );
});
```

### 특정 파일만 로드

```php
// config.php에서 특정 파일 제외
add_event('wv_hook_assets_before_add_assets', function() {
    // Bootstrap JS는 제외하고 CSS만 로드
    unset(wv('assets')->js['bootstrap_bootstrap']);
});
```

### AJAX 응답에 CSS/JS 포함 안 될 때

```php
// weaver_ajax 라이브러리 추가 확인
wv('assets')->add_library('weaver_ajax');

// AJAX 요청 시 Accept 헤더 확인
$.ajax({
    url: '/some/url',
    headers: {
        'Accept': 'application/json' // 또는 'text/html'
    }
});
```

### 파일 업로드 미리보기 안 될 때

```php
// 1. weaver_bf_file 라이브러리 추가 확인
wv('assets')->add_library('weaver_bf_file');

// 2. HTML 구조 확인
<div class="wv-preview-wrap">
    <input type="file" id="bf_file_0" name="bf_file[0]" class="wv-file-preview">
</div>

// 3. JavaScript 초기화 확인
$(document).ready(function() {
    $('.wv-file-preview').wv_preview_file();
});
```

### Honey Spot 스팸 검증 실패

```php
// 1. weaver_spam 라이브러리 추가 확인
wv('assets')->add_library('weaver_spam');

// 2. form ID 확인
// 게시글: #fwrite
// 회원가입: #fregisterform

// 3. JavaScript 활성화 확인
// Honey Spot은 JavaScript 필수

// 4. 수동으로 필드 추가 (테스트용)
<input type="hidden" name="wv_h_spot" value="wv">
```

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-01-02  
**작성자**: Claude AI