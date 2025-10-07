# 📦 Store Manager 플러그인 가이드

> Weaver 기반 매장 관리 시스템 완벽 가이드

---

## 📖 목차

1. [개요](#개요)
2. [기본 개념](#기본-개념)
3. [Store Manager 만들기](#store-manager-만들기)
4. [데이터 조회](#데이터-조회)
5. [데이터 저장](#데이터-저장)
6. [파트 스킨 렌더링](#파트-스킨-렌더링)
7. [AJAX 처리](#ajax-처리)
8. [실전 예제](#실전-예제)
9. [문제 해결](#문제-해결)

---

## 개요

Store Manager는 **Makeable 패턴**을 사용하여 매장 데이터를 관리하는 플러그인입니다.

### 핵심 특징

- ✅ **파트 기반 구조**: 매장 데이터를 기능별로 분리 (store, location, menu, contract 등)
- ✅ **일반 파트 / 목록 파트**: 1:1 관계와 1:N 관계 모두 지원
- ✅ **자동 테이블 생성**: 파트 스키마만 작성하면 테이블 자동 생성
- ✅ **스킨 시스템**: 파트별로 view/form 스킨 작성 가능
- ✅ **AJAX 통합**: 비동기 저장/수정/삭제 지원
- ✅ **update_render**: 저장 후 즉시 렌더링 패턴 지원 ⭐ NEW

---

## 기본 개념

### 1. Store Manager 구조

```
Store Manager (made)
├── Write Row (g5_write_xxx)     // 그누보드 게시글
├── Ext Row (wv_store_xxx)        // 확장 테이블 (일반 파트)
└── Parts                         // 파트별 데이터
    ├── store (일반 파트)         // wv_store_xxx_store
    ├── location (일반 파트)      // wv_store_xxx_location
    ├── menu (목록 파트)          // wv_store_xxx_menu
    └── contract (목록 파트)      // wv_store_xxx_contract
```

### 2. 일반 파트 vs 목록 파트

| 구분 | 일반 파트 | 목록 파트 |
|------|-----------|-----------|
| **관계** | 1:1 | 1:N |
| **테이블** | 확장 테이블 (wv_store_xxx) | 별도 테이블 (wv_store_xxx_{part}) |
| **예시** | store, location, biz | menu, contract, dayoffs |
| **스키마** | `list_part = false` | `list_part = true` |

### 3. 파트 스키마

```php
// plugins/store_manager/parts/Store.php
class Store extends StoreSchemaBase {
    protected $list_part = false;  // 일반 파트
    
    public function get_columns($bo_table = ''){
        return array(
            'name' => 'VARCHAR(255) NOT NULL',
            'tel' => 'VARCHAR(20) DEFAULT NULL'
        );
    }
    
    public function get_allowed_columns(){
        return array('name', 'tel');
    }
}
```

---

## Store Manager 만들기

### 1. setting.php에서 생성

```php
// plugins/setting.php

// 매장 관리 (sub01_01)
wv()->store_manager->make(
    'sub01_01',                    // made ID
    'sub01_01',                    // 게시판 테이블명
    array('menu','biz','store','location','dayoffs','tempdayoffs','contract')  // 파트 목록
)->prune_columns();

// 회원 관리
wv()->store_manager->make('member', 'member', array('member'))->prune_columns();

// 찜 관리 ⭐ NEW
wv()->store_manager->make('favorite_store', 'favorite_store', array('favorite'))->prune_columns();
```

### 2. 사용하기

```php
// Made 객체 가져오기
$manager = wv()->store_manager->made('sub01_01');

// 데이터 조회
$store = $manager->get(123);

// 목록 조회
$result = $manager->get_list(array('page' => 1));
```

---

## 데이터 조회

### 1. 단건 조회: get()

```php
$store = wv()->store_manager->made('sub01_01')->get(123);

// 일반 파트 접근
echo $store->store->name;        // 매장명
echo $store->location->lat;      // 위도
echo $store->location->lng;      // 경도

// 목록 파트 접근
foreach($store->menu->list as $menu){
    echo $menu['name'];
    echo $menu['price'];
}
```

**⚠️ 중요: get(0)의 안전성**

```php
// wr_id = 0으로 호출해도 안전하게 동작
$empty_store = wv()->store_manager->made('sub01_01')->get(0);

// 빈 Store 객체 반환 (에러 없음)
// - write_row = array()
// - ext_row = array()
// - 모든 파트 프록시 정상 생성
// - 스킨 렌더링 가능

// 실전 활용 예시: 찜 기능에서 데이터 없을 때
$favorite_wr_id = 0;  // 찜 안 한 상태
echo $favorite_manager->get($favorite_wr_id)->favorite->render_part('status', 'view', array(...));
// → 정상 렌더링 (새로 추가 폼 표시)
```

### 2. 목록 조회: get_list()

```php
$result = wv()->store_manager->made('sub01_01')->get_list(array(
    'page' => 1,
    'rows' => 20,
    'where' => "wr_is_comment = 0",
    'where_store' => array(
        'category' => array('=', '한식')
    ),
    'where_location' => array(
        'region_1depth_name' => array('=', '서울특별시')
    ),
    'order_by' => 'wr_id DESC',
    'with_list_part' => 'menu,contract'  // 목록 파트 포함
));

// 결과 활용
foreach($result['list'] as $store){
    echo $store->store->name;
    
    // with_list_part로 요청한 목록 파트 데이터
    foreach($store->menu->list as $menu){
        echo $menu['name'];
    }
}

// 페이징
echo $result['paging'];
```

**get_list() 반환값 구조:**

```php
array(
    'list' => array(),           // Store 객체 배열
    'total_count' => 0,          // 전체 개수 ⚠️ (주의: 'total' 아님!)
    'total_page' => 0,           // 전체 페이지 수
    'page' => 1,                 // 현재 페이지
    'page_rows' => 20,           // 페이지당 행수
    'from_record' => 0,          // 시작 레코드 번호
    'write_table' => '',         // 게시판 테이블명
    'base_table' => '',          // 확장 테이블명
    'sql' => '',                 // 실행된 SQL
    'sql_count' => '',           // 카운트 SQL
    'paging' => ''               // 페이징 HTML
)
```

### 3. 간단한 조회: get_simple_list() ⭐ NEW

**용도**: 특정 조건의 데이터가 존재하는지 확인하고 ID만 반환

```php
public function get_simple_list($conditions = array(), $just_one = true)
```

**특징:**
- ✅ `$need_object` 인수 제거 (항상 id 포함한 객체 반환)
- ✅ `$mb_id` 선택 사항 (conditions 배열 안에 포함)
- ✅ `where` 조건 추가 가능
- ✅ 일반 파트 논리키→물리키 자동 변환
- ✅ 최소한의 데이터만 조회 (성능 최적화)

**사용 예시:**

```php
// 예시 1: 찜 기능 (mb_id 포함)
$result = $favorite_manager->get_simple_list(array(
    'mb_id' => $member['mb_id'],
    'favorite' => array('store_wr_id' => $row['wr_id'])
));

if ($result) {
    echo $result['wr_id'];        // 게시글 ID
    echo $result['favorite_id'];  // 찜 항목 ID
}
// 결과: array('wr_id' => 10, 'favorite_id' => 5) 또는 null

// 예시 2: invite 매니저 (mb_id 없음)
$result = $invite_manager->get_simple_list(array(
    'invite' => array('member_wr_id' => 123)
));

// 예시 3: 추가 WHERE 조건
$result = $manager->get_simple_list(array(
    'mb_id' => $member['mb_id'],
    'where' => "w.wr_subject LIKE '%검색어%'",
    'favorite' => array('store_wr_id' => 321)
));

// 예시 4: 여러 개 조회 (just_one = false)
$results = $manager->get_simple_list(array(
    'mb_id' => $member['mb_id']
), false);
// 반환: array(array('wr_id' => 1, 'favorite_id' => 1), array(...))
```

**생성되는 쿼리:**

```sql
SELECT w.wr_id, favorite.id AS favorite_id
FROM g5_write_favorite_store w
LEFT JOIN wv_store_favorite_store_favorite favorite ON favorite.wr_id = w.wr_id
WHERE w.wr_is_comment = 0
AND w.mb_id = 'admin'
AND favorite.store_wr_id = 14871
ORDER BY w.wr_id DESC
LIMIT 1
```

**파라미터:**

| 파라미터 | 타입 | 기본값 | 설명 |
|----------|------|--------|------|
| `$conditions` | array | array() | 조회 조건 |
| `$just_one` | bool | true | true: 1개만, false: 전체 |

**conditions 구조:**

```php
array(
    'mb_id' => 'user_id',                        // 선택 사항
    'where' => 'w.wr_subject LIKE "%검색%"',     // 추가 WHERE
    '{part}' => array(                           // 파트별 조건
        '{column}' => '{value}'                  // 자동으로 = 비교
    )
)
```

---

## 데이터 저장

### 1. 단건 저장/수정: set()

```php
$data = wv()->store_manager->made('sub01_01')->set(array(
    'wr_id' => 123,               // 수정 시 필수, 신규 시 생략
    'wr_subject' => '매장명',     // Write Row
    'store' => array(             // 일반 파트
        'name' => '우리매장',
        'tel' => '02-1234-5678'
    ),
    'location' => array(           // 일반 파트
        'lat' => 37.5665,
        'lng' => 126.9780
    ),
    'menu' => array(                // 목록 파트
        1 => array(                 // 수정 (id 필수)
            'id' => 1,
            'name' => '김치찌개',
            'price' => 8000
        ),
        2 => array(                 // 수정
            'id' => 2,
            'delete' => 1           // 삭제
        ),
        array(                      // 신규 (키 없음! ⚠️)
            'name' => '된장찌개',
            'price' => 7000
        )
    )
));
```

**⭐ set() 메서드 개선 (참조 전달)**

```php
// StoreManager.php의 set() 메서드는 참조 전달로 ID를 자동 설정합니다
foreach ($data as &$row) {  // ← 참조 전달!
    // INSERT 후
    $row['id'] = sql_insert_id();  // 새로 생성된 ID 자동 설정
}

// 반환된 $data에 이미 ID가 포함되어 있음
return $data;
```

**반환 구조:**

```php
array(
    'wr_id' => 153,
    'favorite' => array(
        array(
            'id' => 5,              // ← 자동으로 포함됨!
            'mb_id' => 'admin',
            'store_wr_id' => 6198,
            'created_at' => '2025-10-01 13:07:46'
        )
    )
)
```

**중요 규칙:**

1. **일반 파트**: 논리 컬럼명으로 전달하면 자동으로 물리 컬럼명으로 변환
2. **목록 파트 수정**: 반드시 `id` 키 포함
3. **목록 파트 신규**: 키 없이 배열만 전달 (`array(array(...))`) ⚠️
4. **목록 파트 삭제**: `'delete' => 1` 추가
5. **참조 전달**: 신규 생성된 ID가 자동으로 반환 데이터에 포함됨

### 2. POST 데이터 직접 저장

```php
// POST 데이터가 다음과 같을 때:
// $_POST = array(
//     'wr_id' => 123,
//     'store' => array('name' => '매장명'),
//     'menu' => array(array('name' => '메뉴명'))
// )

$data = wv()->store_manager->made('sub01_01')->set($_POST);
```

---

## 파트 스킨 렌더링

### 1. 기본 렌더링

```php
// view 스킨
echo $store->store->render_part('name', 'view');

// form 스킨
echo $store->store->render_part('name', 'form');

// 변수 전달
echo $store->menu->render_part('status', 'view', array(
    'show_price' => true,
    'editable' => false
));
```

### 2. 스킨 파일 위치

```
plugins/store_manager/theme/basic/pc/
├── store/
│   ├── view/
│   │   ├── name.php
│   │   └── tel.php
│   └── form/
│       ├── name.php
│       └── tel.php
└── menu/
    ├── view/
    │   ├── list.php
    │   └── status.php
    └── form/
        └── item.php
```

### 3. 스킨 내부 변수

**모든 스킨에서 사용 가능:**
- `$skin_id`: 고유 ID
- `$skin_class`: CSS 클래스
- `$skin_selector`: jQuery 셀렉터
- `$row`: Store 객체 데이터
- `$vars`: render_part()로 전달한 변수

**목록 파트 스킨 추가 변수:**
- `$this`: PartProxy 객체
- `$this->part_key`: 파트 키 ('menu', 'favorite' 등)
- `$this->manager`: StoreManager 객체
- `$this->manager->get_make_id()`: made ID

---

## AJAX 처리

### 1. AJAX 액션 종류

**update** (일반 저장/수정)
```javascript
$.ajax({
    url: '<?php echo wv()->store_manager->plugin_url; ?>/ajax.php',
    data: {
        action: 'update',
        made: 'sub01_01',
        wr_id: 123,
        store: { name: '매장명' }
    }
});
```

**update_render** (저장 후 즉시 렌더링) ⭐ NEW
```javascript
$.ajax({
    url: '<?php echo wv()->store_manager->plugin_url; ?>/ajax.php',
    data: {
        action: 'update_render',
        made: 'favorite_store',
        part: 'favorite',      // ← 파트 키
        wr_id: 123,
        favorite: { ... }
    },
    success: function(html) {
        // 렌더링된 HTML이 바로 반환됨
        $('#target').replaceWith(html);
    }
});
```

**update_render 동작 원리:**

```php
// ajax.php
if($action == 'update_render'){
    // 1. update 실행 → $data 반환 (ID 포함)
    $data = wv()->store_manager->made($made)->set($_POST);
    $wr_id = $data['wr_id'];
    
    // 2. 첫 번째 요소 가져오기
    $vars = reset($data[$part]);
    
    // 3. {part}_id 생성
    $vars["{$part}_id"] = $vars['id'];
    unset($vars['id']);
    
    // 4. 렌더링 (field는 자동으로 'status' 사용)
    echo wv()->store_manager->made($made)
        ->get($wr_id)
        ->{$part}
        ->render_part('status', 'view', $vars);
    exit;
}
```

**특징:**
- ✅ 저장 후 바로 렌더링 결과 반환
- ✅ 찜 버튼, 좋아요 등 즉시 피드백 필요한 UI에 최적
- ✅ `part` 파라미터만 전달하면 자동으로 처리
- ✅ 반환된 $data에서 자동으로 vars 추출
- ✅ `{part}_id` 자동 생성

**form** (폼 렌더링)
```javascript
$.ajax({
    url: '<?php echo wv()->store_manager->plugin_url; ?>/ajax.php',
    data: {
        action: 'form',
        made: 'sub01_01',
        part: 'store',
        field: 'name',
        wr_id: 123
    }
});
```

### 2. data-wv-ajax-url 사용

```html
<!-- 기본 사용 -->
<button data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
        data-wv-ajax-data='{"action":"update","made":"sub01_01","wr_id":123}'>
    저장
</button>

<!-- update_render 사용 -->
<button data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
        data-wv-ajax-data='{"action":"update_render","part":"favorite","made":"favorite_store"}'
        data-wv-ajax-option='replace_with:#favorite-btn-<?php echo $row["wr_id"]; ?>'>
    찜하기
</button>
```

---

## 실전 예제

### 1. 찜 기능 구현 (완전판) ⭐

#### Step 1: 파트 스키마 작성

```php
// plugins/store_manager/parts/Favorite.php
class Favorite extends StoreSchemaBase {
    protected $list_part = true;  // 목록 파트
    
    public function get_columns($bo_table = ''){
        return array(
            'mb_id' => 'VARCHAR(255) NOT NULL',
            'store_wr_id' => 'INT NOT NULL',
            'created_at' => 'DATETIME DEFAULT NULL'
        );
    }
    
    public function get_indexes($bo_table = ''){
        return array(
            'idx_mb_store' => 'UNIQUE (mb_id, store_wr_id)'
        );
    }
    
    public function get_allowed_columns(){
        return array('mb_id', 'store_wr_id', 'created_at');
    }
}
```

#### Step 2: setting.php 등록

```php
wv()->store_manager->make('favorite_store', 'favorite_store', array('favorite'))->prune_columns();
```

#### Step 3: 매장 목록에서 찜 상태 조회

```php
// plugins/store_manager/theme/basic/pc/store/view/list_each.php

$favorite_manager = wv()->store_manager->made('favorite_store');

$favorite_wr_id = 0;
$favorite_id = 0;

if ($member['mb_id']) {
    // get_simple_list() 사용
    $result = $favorite_manager->get_simple_list(array(
        'mb_id' => $member['mb_id'],
        'favorite' => array('store_wr_id' => $row['wr_id'])
    ));

    if ($result) {
        $favorite_wr_id = $result['wr_id'];
        $favorite_id = $result['favorite_id'];
    }
}

// 찜하기 버튼 렌더링 (get(0) 사용으로 안전)
echo $favorite_manager->get($favorite_wr_id)->favorite->render_part('status', 'view', array(
    'favorite_id' => $favorite_id,
    'store_wr_id' => $row['wr_id']
));
```

#### Step 4: 찜 버튼 스킨 (status.php)

```php
// plugins/store_manager/theme/basic/pc/favorite/view/status.php
<?php
if (!defined('_GNUBOARD_')) exit;

global $member;

$favorite_id = isset($vars['favorite_id']) ? (int)$vars['favorite_id'] : 0;
$store_wr_id = isset($vars['store_wr_id']) ? (int)$vars['store_wr_id'] : 0;
$is_favorited = $favorite_id > 0;
$mb_id = isset($member['mb_id']) ? $member['mb_id'] : '';

// AJAX 데이터 구성
if ($is_favorited) {
    // 찜 취소
    $ajax_data = array(
        'action' => 'update_render',
        'part' => $this->part_key,  // ← 이것만!
        'made' => $this->manager->get_make_id(),
        'wr_id' => $row['wr_id'],
        'favorite' => array(
            $favorite_id => array(
                'id' => $favorite_id,
                'delete' => 1
            )
        )
    );
} else {
    // 찜 추가
    $ajax_data = array(
        'action' => 'update_render',
        'part' => $this->part_key,  // ← 이것만!
        'made' => $this->manager->get_make_id(),
        'wr_id' => '',  // 빈값 필수!
        'favorite' => array(
            array(  // ← 키 없이 배열만!
                'mb_id' => $mb_id,
                'store_wr_id' => $store_wr_id,
                'created_at' => G5_TIME_YMDHIS
            )
        )
    );
}

$ajax_data_json = htmlspecialchars(json_encode($ajax_data), ENT_QUOTES, 'UTF-8');
?>
<div id="<?php echo $skin_id; ?>" class="<?php echo $skin_class; ?>">
    <button type="button" 
            class="btn btn-favorite <?php echo $is_favorited ? 'active' : ''; ?>"
            data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
            data-wv-ajax-data='<?php echo $ajax_data_json; ?>'
            data-wv-ajax-option='replace_with:<?php echo $skin_selector; ?>'>
        <i class="fa <?php echo $is_favorited ? 'fa-heart' : 'fa-heart-o'; ?>"></i>
        <?php echo $is_favorited ? '찜 취소' : '찜하기'; ?>
    </button>
</div>
```

**핵심 포인트:**
1. ✅ `part` 파라미터만 전달 (update_render가 자동 처리)
2. ✅ 찜 추가 시: `wr_id` 빈값, 키 없이 배열만
3. ✅ 찜 취소 시: `wr_id` 포함, `id`와 `delete: 1`
4. ✅ `replace_with`로 자신만 교체
5. ✅ `get(0)` 사용으로 안전한 렌더링

### 2. 매장 검색 페이지

```php
// 검색 조건
$search = array(
    'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
    'rows' => 20,
    'where' => "wr_is_comment = 0",
    'order_by' => 'wr_id DESC'
);

// 매장명 검색
if(isset($_GET['keyword']) && $_GET['keyword']){
    $search['where_store'] = array(
        'name' => array('LIKE', '%' . $_GET['keyword'] . '%')
    );
}

// 지역 필터
if(isset($_GET['region']) && $_GET['region']){
    $search['where_location'] = array(
        'region_1depth_name' => array('=', $_GET['region'])
    );
}

// 메뉴 포함 조회
$search['with_list_part'] = 'menu';

// 조회
$result = wv()->store_manager->made('sub01_01')->get_list($search);

// 목록 출력
foreach($result['list'] as $store){
    echo $store->store->render_part('list_each', 'view');
}

// 페이징
echo $result['paging'];
```

### 3. 매장 상세 페이지 (AJAX 저장)

```php
// 페이지
$wr_id = isset($_GET['wr_id']) ? (int)$_GET['wr_id'] : 0;
$store = wv()->store_manager->made('sub01_01')->get($wr_id);
?>

<form id="store-form">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="made" value="sub01_01">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id; ?>">
    
    <?php echo $store->store->render_part('name', 'form'); ?>
    <?php echo $store->store->render_part('tel', 'form'); ?>
    <?php echo $store->location->render_part('address', 'form'); ?>
    
    <button type="button" id="save-btn"
            data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
            data-wv-ajax-form="#store-form">
        저장
    </button>
</form>

<script>
$(document).on('ajaxComplete', function(e, xhr, settings){
    if(settings.url.indexOf('/store_manager/ajax.php') > -1){
        alert('저장되었습니다.');
        location.reload();
    }
});
</script>
```

---

## 문제 해결

### 1. 파트 스킨이 안 나올 때
- 파일 경로 확인
- 파트 바인딩 여부 확인
- 논리/물리 컬럼 매핑 확인

### 2. 데이터가 저장 안 될 때
- `get_allowed_columns()` 확인
- 데이터 구조 확인 (`array('part' => array('column' => 'value'))`)
- 훅 에러 확인

### 3. 목록 파트 데이터가 안 나올 때
- `list_part = true` 설정 확인
- `with_list_part` 옵션 사용 확인
- 테이블 생성 확인

### 4. 목록 파트 신규 추가 시 저장 안 될 때 ⭐
```php
// ❌ 틀림: 음수 키 사용 금지
'favorite' => array(
    -1 => array(...)  // set()에서 패싱됨!
)

// ✅ 올바름: 키 없이 배열만 전달
'favorite' => array(
    array(...)  // 정상 동작
)
```

### 5. update_render가 작동하지 않을 때
- `part` 파라미터 전달 확인
- `wr_id` 빈값 전달 확인 (신규 생성 시)
- ajax.php에 update_render 액션 구현 확인

### 6. 캐시 문제
- 수동으로 캐시 클리어: `$manager->clear_cache($wr_id)`
- 개발 중에는 캐시 비활성화 고려

---

## 💡 팁

1. **get(0)은 안전**: wr_id가 0이어도 빈 Store 객체를 반환하므로 안전하게 사용 가능
2. **total_count 사용**: get_list() 결과의 개수는 `$result['total']`이 아닌 `$result['total_count']`
3. **목록 파트 ID**: 항목 수정 시 반드시 `id` 키 포함, 새 항목은 키 없이 배열만 전달
4. **캐시 활용**: 반복 조회 시 자동으로 캐시 활용되므로 성능 걱정 없음
5. **where_{part} 조건**: 파트별 조건은 자동으로 적절한 쿼리(JOIN/EXISTS)로 변환됨
6. **update_render**: 저장 후 바로 렌더링 필요할 때 사용 (찜, 좋아요 등)
7. **get_simple_list()**: 존재 여부만 확인할 때 가장 효율적
8. **참조 전달**: set() 메서드는 신규 생성된 ID를 자동으로 반환 데이터에 포함

---

## 📚 참고 문서

- [Weaver Core Guide](weaver_core_guide.md)
- [Location Plugin Guide](location_guide.md)
- [CEO Plugin Guide](ceo_guide.md)
- [GNU Admin Plugin Guide](gnu_adm_guide.md)
- [Coding Convention](coding_convention.md)
- [FAQ](faq.md)