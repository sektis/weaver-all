# 📊 프로젝트 현재 상태

> 마지막 업데이트: 2025-10-01

---

## 📌 현재 진행중인 작업

### 작업명
- **작업 없음** (새 작업 시작 시 여기에 기록)

### 진행 상태
- 상태: 대기중
- 진행률: 0%
- 파일: -
- 다음 할 일: -

---

## 🔄 최근 작업 이력 (최신순)

### 2025-10-01

#### ✅ Store Manager 찜 기능 완성
- **get_simple_list() v4 메서드** 개발 완료
    - 파일: `plugins/store_manager/StoreManager.php`
    - 기능: 특정 조건의 데이터 존재 여부 확인 및 ID 반환
    - 반환: `array('wr_id' => int, '{part}_id' => int)` 또는 `null`

- **set() 메서드 개선**
    - 참조 전달을 통한 ID 자동 설정
    - 반환값에 자동으로 신규 생성된 ID 포함

- **update_render 액션** 구현
    - 파일: `plugins/store_manager/ajax.php`
    - 기능: 데이터 저장 후 바로 렌더링 결과 반환
    - 용도: 찜 버튼, 좋아요 등 즉시 피드백 필요한 UI

- **찜 기능 스킨** 완성
    - 파일: `plugins/store_manager/theme/basic/pc/favorite/view/status.php`
    - 목록 파트 신규 추가 패턴 확립 (키 없이 배열만 전달)
    - `replace_with` 옵션으로 자신만 교체

#### ✅ 프로젝트 문서 17개 작성 완료

**가이드 문서 (12개)**
- weaver_core_guide.md
- store_manager_guide.md ⭐ (업데이트: get_simple_list, update_render 추가)
- location_guide.md
- assets_guide.md
- page_guide.md
- layout_guide.md
- wv_css_guide.md
- widget_guide.md
- menu_guide.md
- gnu_skin_guide.md
- gnu_adm_guide.md
- ceo_guide.md

**관리 문서 (5개)**
- project_status.md
- coding_convention.md
- checklist.md
- faq.md
- todo.md

---

## 📝 현재 프로젝트 구조

### Store Manager 구성
```
wv()->store_manager->make('sub01_01')  // 매장 관리
  - parts: menu, biz, store, location, dayoffs, tempdayoffs, contract

wv()->store_manager->make('member')     // 회원 관리
  - parts: member

wv()->store_manager->make('invite')     // 초대 관리
  - parts: invite

wv()->store_manager->make('visit_cert') // 방문 인증
  - parts: visitcert

wv()->store_manager->make('contract_item') // 계약 상품
  - parts: contractitem

wv()->store_manager->make('store_category') // 매장 카테고리
  - parts: storecategory

wv()->store_manager->make('favorite_store') // 찜 관리 ⭐ NEW
  - parts: favorite
```

### 파트너 페이지
- `/admin` - 관리자 페이지 (gnu_adm)
- `/ceo` - 사장님 페이지 (ceo)

### 주 작업 방식
- **AJAX 비동기 처리**: `data-wv-ajax-url` + 클릭 이벤트
- **Store Manager 중심**: 모든 데이터 관리
- **파트 렌더링**: `render_part($field, $type)` 사용
- **update_render**: 저장 후 즉시 렌더링 패턴 ⭐ NEW

---

## ⚙️ 현재 설정 (setting.php)

```php
// 로드된 플러그인
wv()->load(array('wv_css', 'theme', 'adm_bbs', 'location', 'parsing'));
wv()->load(array('ceo', 'gnu_adm'));

// Assets
wv('assets')->add_library(array('weaver', 'weaver_ajax', 'weaver_bf_file', 'bootstrap', 'hc_sticky', 'font_awesome', 'swiper11', 'animate_css'));
wv('assets')->add_font(array('pretendard', 'roboto_mono', 'montserrat'));

// 테마 설정
wv('layout')->set_theme_dir('basic');
wv('page')->set_theme_dir('basic');
wv('menu')->set_theme_dir('basic');
wv('widget')->set_theme_dir('basic');
wv('gnu_skin')->set_theme_dir('basic');

// Store Manager 설정
wv()->store_manager->make('sub01_01', 'sub01_01', array('menu','biz','store','location','dayoffs','tempdayoffs','contract'))->prune_columns();
wv()->store_manager->make('member', 'member', array('member'))->prune_columns();
wv()->store_manager->make('invite', 'invite', array('invite'))->prune_columns();
wv()->store_manager->make('visit_cert', 'visit_cert', array('visitcert'))->prune_columns();
wv()->store_manager->make('contract_item', 'contract_item', array('contractitem'))->prune_columns();
wv()->store_manager->make('store_category', 'store_category', array('storecategory'))->prune_columns();
wv()->store_manager->make('favorite_store', 'favorite_store', array('favorite'))->prune_columns();  // ⭐ NEW
```

---

## 🎯 주요 전역 변수

### CEO 모드
```php
$current_store          // 현재 매장 객체
$current_store_wr_id    // 현재 매장 ID
$current_member         // 현재 회원 객체
$current_member_wr_id   // 현재 회원 ID
```

### 공통
```php
$wv_dir_var            // 경로 식별자 ('admin', 'ceo', null)
$wv_page_id            // 페이지 ID
$member                // 로그인 회원 정보
```

---

## ⚠️ 주의사항

### 개발 규칙
- ✅ Store Manager 중심으로 모든 데이터 관리
- ✅ 비동기 AJAX 방식 우선 사용
- ✅ 파트 스킨 경로: `theme/basic/pc/{part}/{type}/{column}.php`
- ✅ PHP 5.6 기준 (타입힌트, 최신 문법 X)
- ✅ 메서드명: snake_case
- ✅ JavaScript: snake_case
- ✅ CSS px값: `var(--wv-{숫자})`

### 목록 파트 신규 추가 규칙 ⭐ NEW
```php
// ❌ 틀림: 음수 키는 set()에서 패싱됨!
'favorite' => array(-1 => array(...))

// ✅ 올바름: 키 없이 배열만!
'favorite' => array(array(...))
```

### 파일 경로
- 일반 페이지: `plugins/page/theme/basic/pc/0101.php`
- CEO 주입 페이지: `plugins/ceo/theme/basic/plugins/page/theme/pc/0101.php`
- 관리자 주입 페이지: `plugins/gnu_adm/theme/basic/plugins/page/theme/pc/0101.php`
- 파트 스킨: `plugins/store_manager/theme/basic/pc/{part}/{type}/{column}.php`

---

## 🔧 핵심 메서드

### StoreManager 주요 메서드

**get()**
```php
$store = wv()->store_manager->made('sub01_01')->get($wr_id);
// get(0)도 안전 - 빈 Store 객체 반환
```

**get_list()**
```php
$result = $manager->get_list(array(
    'page' => 1,
    'rows' => 20,
    'with_list_part' => 'menu,contract'
));
// 반환: array('list', 'total_count', 'paging', ...)
```

**get_simple_list()** ⭐ NEW
```php
$result = $manager->get_simple_list(array(
    'mb_id' => $member['mb_id'],
    'favorite' => array('store_wr_id' => 123)
));
// 반환: array('wr_id' => int, '{part}_id' => int) 또는 null
```

**set()**
```php
$data = $manager->set(array(
    'wr_id' => $wr_id,
    'store' => array('name' => '매장명'),
    'favorite' => array(array('mb_id' => 'admin'))  // ← 키 없음!
));
// 반환: array('wr_id' => int, 'favorite' => array(array('id' => int, ...)))
```

### AJAX 액션

**update**
```php
// 일반 저장/수정
action=update&made=sub01_01&wr_id=123&store[name]=매장명
```

**update_render** ⭐ NEW
```php
// 저장 후 바로 렌더링
action=update_render&made=favorite_store&part=favorite&...
// 반환: 렌더링된 HTML
```

**form**
```php
// 폼 렌더링
action=form&made=sub01_01&part=store&field=name&wr_id=123
```

---

## 📚 참고 문서

- weaver_core_guide.md - Weaver 코어 시스템
- store_manager_guide.md - Store Manager 플러그인 ⭐ (업데이트됨)
- coding_convention.md - 코딩 규칙/패턴
- checklist.md - 작업 체크리스트
- faq.md - 자주 묻는 질문
- todo.md - TODO 리스트

---

## 🔄 업데이트 방법

새 작업 시작 시:
```
"project_status.md 업데이트:
- 현재 작업: [작업명]
- 파일: [경로]
- 상태: 진행중"
```

작업 완료 시:
```
"project_status.md 업데이트:
- [날짜] [작업명] 완료
- 파일: [목록]"
```