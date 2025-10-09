# 📦 Store Manager 플러그인 가이드

> Weaver 기반 매장 관리 시스템 완벽 가이드

---

## 📖 목차

1. [개요](#개요)
2. [기본 개념](#기본-개념)
3. [Store Manager 만들기](#store-manager-만들기)
4. [데이터 조회](#데이터-조회)
5. [데이터 저장](#데이터-저장)
6. [훅 시스템](#훅-시스템)
7. [파트 스킨 렌더링](#파트-스킨-렌더링)
8. [AJAX 처리](#ajax-처리)
9. [체크박스 처리](#체크박스-처리)
10. [실전 예제](#실전-예제)
11. [문제 해결](#문제-해결)

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

## 훅 시스템

### 1. 훅이란?

훅(Hook)은 데이터 저장 시 **각 컬럼의 값이 변경될 때** 자동으로 실행되는 콜백 함수입니다.

**훅을 사용하는 이유:**
- ✅ 특정 컬럼 값 변경 시 자동 처리
- ✅ 크로스 파트 업데이트 (`&$all_data` 사용)
- ✅ 삭제 전후 처리 (파일 삭제, 연관 데이터 정리 등)
- ✅ 유효성 검사 및 자동 계산

### 2. 훅 종류

| 훅 종류 | 실행 시점 | 사용 예시 |
|---------|----------|----------|
| **is_new** | 신규 데이터 저장 시 | 생성일시 자동 기록 |
| **is_edit** | 기존 데이터 수정 시 | 수정일시 자동 업데이트 |
| **is_delete** | 데이터 삭제 시 | 파일 삭제, 연관 데이터 정리 |
| **is_change** | 값이 변경될 때 (new/edit 모두) | 연관 데이터 업데이트 |
| **list_row_delete** | **목록 파트 행 삭제 시** | 목록 항목 삭제에만 동작 |

**⚠️ 중요: `list_row_delete` vs `is_delete`**

```php
// ❌ is_delete: 목록 파트에서는 행 삭제 시 실행 안 됨!
public function is_delete($col, &$current_value, $prev_value, &$all_data) {
    // 목록 파트에서 행 삭제 시 호출되지 않음
}

// ✅ list_row_delete: 목록 파트 행 삭제 시에만 실행
public function list_row_delete($col, &$current_value, $prev_value, &$all_data) {
    // 목록 파트의 행이 삭제될 때만 호출됨
    // 예: menu 파트의 특정 메뉴 항목 삭제 시
}
```

### 3. 훅 파라미터

```php
public function is_new($col, &$current_value, $prev_value, &$all_data, $node)
```

| 파라미터 | 타입 | 설명 |
|----------|------|------|
| `$col` | string | **경로 형식 컬럼명** (예: `store/name`, `menu/n/price`) |
| `&$current_value` | mixed | **참조**: 현재 값 (수정 가능) |
| `$prev_value` | mixed | 이전 값 (is_edit, is_change에서만 유의미) |
| `&$all_data` | array | **참조**: 전체 데이터 (크로스 파트 업데이트 가능) |
| `$node` | array | 데이터 경로 배열 (예: `array('menu', 0, 'price')`) |

### 3-1. $col 파라미터 형식 ⭐ NEW

**형식: `파트키/컬럼키/{배열이고 숫자키이면 n}/{배열키}`**

숫자 인덱스 배열은 모두 `n`으로 표시됩니다.

**예시:**

| $col 값 | 설명 | 실제 데이터 위치 |
|---------|------|------------------|
| `store/name` | 일반 파트의 단일 컬럼 | `$data['store']['name']` |
| `store/image/n` | image 배열의 각 항목 | `$data['store']['image'][0]`, `[1]`... |
| `store/image/n/path` | image 배열 내부의 path | `$data['store']['image'][0]['path']` |
| `menu/n` | 목록 파트의 각 행 | `$data['menu'][0]`, `[1]`... |
| `menu/n/price` | 목록 파트 행의 price | `$data['menu'][0]['price']` |
| `contract/memo/n` | memo 배열 필드 항목 | `$data['contract'][0]['memo'][0]` |

**실제 사용 예시:**

```php
// plugins/store_manager/parts/Contract.php
public function is_new($col, &$curr, $prev, &$data, $node) {
    // 목록 파트의 memo 배열 항목
    if ($col == 'contract/memo/n') {
        $curr['date'] = date('Y-m-d h:i:s');
    }
    
    // 목록 파트의 각 행
    if ($col == 'contract/n') {
        $curr['start'] = date('Y-m-d h:i:s');
    }
}

// plugins/store_manager/parts/Store.php
public function is_change($col, &$curr, $prev, &$data, $node) {
    // 일반 파트의 단일 컬럼
    if ($col == 'store/name') {
        // 매장명 변경 시 처리
    }
    
    // image 배열 필드
    if ($col == 'store/image/n') {
        // 각 이미지 항목 변경 시 처리
    }
}
```

**장점:**

1. ✅ **명확한 경로**: 어느 파트의 어느 필드인지 즉시 파악
2. ✅ **중복 방지**: 여러 파트에 같은 컬럼명이 있어도 구분 가능
3. ✅ **패턴 매칭**: 특정 깊이의 데이터만 처리 가능
4. ✅ **배열 구분**: `n`으로 배열 인덱스를 명확히 표시

### 4. Schema 클래스에 훅 구현

**기본 구조:**

```php
// plugins/store_manager/parts/Store.php
class Store extends StoreSchemaBase {
    protected $list_part = false;
    
    public function get_columns($bo_table = '') {
        return array(
            'name' => 'VARCHAR(255) NOT NULL',
            'avg_price' => 'INT DEFAULT 0'
        );
    }
    
    public function get_allowed_columns() {
        return array('name', 'avg_price');
    }
    
    // 훅 메서드 구현
    public function is_new($col, &$current_value, $prev_value, &$all_data) {
        if ($col === 'name') {
            // 새 매장 등록 시 처리
        }
    }
    
    public function is_change($col, &$current_value, $prev_value, &$all_data) {
        // 모든 변경에 반응
    }
}
```

### 5. 훅 실행 흐름

#### set() 메서드 실행 순서

StoreManager의 `set()` 메서드는 다음 순서로 실행됩니다:

```php
public function set($data = array()) {
    // 1. 트랜잭션 시작
    wv_execute_query_safe("START TRANSACTION", "transaction_start");
    
    // 2. before_set 훅 실행
    $this->execute_hook('before_set', $data);
    
    // 3. ✅ 파일 업로드 미리 병합
    $this->merge_file_uploads_to_data($data);
    
    // 4. wr_id 생성/업데이트
    $wr_id = $this->create_post_stub_and_get_wr_id($data);
    
    // 5. 각 파트 처리
    foreach ($data as $pkey => $part_data) {
        if ($is_list_part) {
            $this->process_list_part($pkey, $part_data, $schema, $wr_id, $data);
        } else {
            $this->process_normal_part($pkey, $part_data, $allowed, $prev_ext_row, $data);
        }
    }
    
    // 6. DB 저장
    // 7. after_set 훅 실행
    // 8. 커밋
}
```

#### 훅 실행 메커니즘

**핵심: `get_walk_function()` 메서드**

StoreManager는 `get_walk_function()`에서 반환하는 **`$walk_function` 클로저**를 통해 모든 훅을 처리합니다.

```php
// StoreManager.php
protected function get_walk_function($pkey, $is_list_part, &$current_data, &$all_data) {
    $walk_function = function (&$arr, $arr2, $node) use (...) {
        
        // === 1. 단일 값 처리 ===
        if (!is_array($arr)) {
            // change_pass_keys 체크 (ord, delete 등은 변경 감지 안 함)
            if (!in_array($parent_key, $this->change_pass_keys) 
                and $arr != $arr2 
                and !is_null($arr2)) {
                
                // ✅ 날짜 비교: 앞 10자리만 비교
                if (strlen($arr) == 10 
                    and strtotime($arr2) !== false 
                    and substr($arr, 0, 10) == substr($arr2, 0, 10)) {
                    return false;  // 날짜 부분이 같으면 변경 안 된 것으로 처리
                }
                
                $this->execute_hook('is_change', $all_data, $pkey, $curr_col, $arr, $arr2, $node);
            }
            return false;
        }
        
        // === 2. 배열 내부 순회 (재귀) ===
        $i = 0;
        foreach ($arr as $k => &$v) {
            // ✅ ord 자동 설정 (배열 요소가 2개 이상일 때만)
            if (is_numeric($k) 
                and !isset($v['delete']) 
                and array_filter($v)) {
                
                if (count($arr) > 1) {  // ← 2개 이상일 때만!
                    $v['ord'] = $i;
                }
                $i++;
            }
            
            if (!$is_delete) {
                wv_walk_by_ref_diff($v, $walk_function, isset($arr2[$k]) ? $arr2[$k] : null, array_merge($node, (array)$k));
            }
        }
        
        // === 3. 신규 생성 ===
        if ($is_new) {
            // 빈 배열 체크
            if (wv_empty_except_keys($arr, array('ord'))) {
                $combined = 'unset($current_data' . wv_array_to_text($node, "['", "']") . ');';
                @eval("$combined;");
                return false;
            }
            
            // id 생성
            if (($is_list_part and count($node) == 1) == false) {
                $arr['id'] = uniqid() . $parent_key;
            }
            
            $this->execute_hook('is_new', $all_data, $pkey, $curr_col, $arr, '', $node);
        } 
        // === 4. 삭제 ===
        else if ($is_delete) {
            $this->execute_hook('is_delete', $all_data, $pkey, $curr_col, $arr, '', $node);
            
            // 파일 삭제
            wv_walk_by_ref_diff($arr, function (&$arr, $arr2, $node) {
                if (wv_array_has_all_keys($this->file_meta_column, $arr2)) {
                    $this->delete_physical_paths_safely(array($arr2['path']));
                }
            }, $arr2, array());
            
            // 데이터 제거
            if (($is_list_part and count($node) == 1) == false) {
                $combined = 'unset($current_data' . wv_array_to_text($node, "['", "']") . ');';
                @eval("$combined;");
            }
            
            return false;
        } 
        // === 5. 수정 ===
        else {
            // 기존 데이터와 병합
            if (($int_key and is_array($arr2)) or $is_old_file) {
                $arr = array_merge($arr2, $arr);
            }
            
            // 변경 감지 (ord, id, delete 제외)
            $diff = wv_array_recursive_diff($arr, wv_shuffle_assoc($arr2), '', '', array('ord','id','delete'));
            
            if (count($diff)) {
                $this->execute_hook('is_change', $all_data, $pkey, $curr_col, $arr, $arr2, $node);
            }
        }
        
        return false;
    };
    
    return $walk_function;
}
```

**wv_walk_by_ref_diff()로 재귀 순회:**

```php
// process_list_part() 또는 process_normal_part()에서 호출
$walk_function = $this->get_walk_function($pkey, $is_list_part, $current_data, $all_data);
wv_walk_by_ref_diff($current_data, $walk_function, $prev_data, array());
```

**⚠️ 현재 디버깅 모드**

StoreManager.php의 훅 실행 코드는 현재 주석 처리되어 있고, 대신 디버그 출력만 합니다:

```php
// execute_hook() 메서드 내부
try {
    // ✅ 디버깅 출력
    echo "{$hook_name} : {$col} --- ".implode('/',$node)."<br>";
    
    // ❌ 실제 훅 실행은 주석 처리됨
    // $schema->{$hook_name}($col, $current_value, $prev_value, $all_data, $node);
}
```

**출력 예시:**
```
is_new : favorite/n --- favorite/n
is_change : store/name --- store/name
is_change : menu/n/price --- menu/n/price
```

**실제 사용 시:**
- 주석을 제거하고 `$schema->{$hook_name}(...)` 라인을 활성화
- `echo` 라인은 제거 또는 주석 처리

#### 훅 실행 시나리오

**시나리오 1: 일반 파트 단일 컬럼 변경**

```php
// POST 데이터
$_POST = array(
    'wr_id' => 123,
    'store' => array(
        'name' => '새 매장명'  // 기존: '우리매장'
    )
);

↓ StoreManager->set() 호출
↓ process_normal_part() 실행
↓ get_walk_function() 호출하여 $walk_function 생성
↓ wv_walk_by_ref_diff() 실행

// 훅 실행:
// $node = array('store', 'name')
// $curr_col = 'store/name'
is_change('store/name', '새 매장명', '우리매장', $all_data)
```

**시나리오 2: 배열 필드 (image 등)**

```php
// POST 데이터
$_POST = array(
    'wr_id' => 123,
    'store' => array(
        'image' => array(
            array('id' => 'abc123', 'name' => '이미지1.jpg'),  // 신규
            array('id' => 'def456', 'name' => '이미지2.jpg')   // 신규
        )
    )
);

↓ wv_walk_by_ref_diff() 재귀 순회

// 1단계: image 배열 전체에 대해
// $node = array('store', 'image')
// $curr_col = 'store/image'
is_new('store/image', [...전체 배열...], '', $all_data)

// 2단계: 각 이미지 항목에 대해 (재귀)
// $node = array('store', 'image', 0)
// $curr_col = 'store/image'
is_new('store/image', {0번 이미지}, '', $all_data)

// $node = array('store', 'image', 1)
is_new('store/image', {1번 이미지}, '', $all_data)
```

**시나리오 3: 목록 파트**

```php
// POST 데이터
$_POST = array(
    'wr_id' => 123,
    'menu' => array(
        array('id' => 1, 'price' => 9000),  // 수정 (기존: 8000)
        array('name' => '새메뉴', 'price' => 7000)  // 신규
    )
);

↓ process_list_part() 실행
↓ wv_walk_by_ref_diff() 재귀 순회

// 1번 행 (수정)
// $node = array('menu', 0)
// $curr_col = 'menu'
$diff = array('price' => 9000);  // ord, id, delete 제외
is_change('menu', {1번 행 전체}, {기존 1번 행}, $all_data)

// price 컬럼 (재귀)
// $node = array('menu', 0, 'price')
// $curr_col = 'menu/price'
is_change('menu/price', 9000, 8000, $all_data)

// 새 행 (신규)
// $node = array('menu', 1)
// $curr_col = 'menu'
is_new('menu', {새 행 전체}, '', $all_data)

// name 컬럼 (재귀)
// $node = array('menu', 1, 'name')
// $curr_col = 'menu/name'
is_new('menu/name', '새메뉴', '', $all_data)
```

**시나리오 4: 행 삭제**

```php
// POST 데이터
$_POST = array(
    'wr_id' => 123,
    'menu' => array(
        array('id' => 2, 'delete' => 1)  // 삭제
    )
);

↓ wv_walk_by_ref_diff() 실행

// $node = array('menu', 0)
// $curr_col = 'menu'
is_delete('menu', {2번 행 데이터}, '', $all_data)

// 파일이 있으면 자동 삭제 처리
// 데이터에서 제거: unset($current_data['menu'][0])
```

#### $node 파라미터

**$node는 현재 데이터의 경로를 나타내는 배열입니다:**

```php
// 예시 1: 일반 파트
$node = array('store', 'name')
// → $data['store']['name']

// 예시 2: 배열 필드
$node = array('store', 'image', 0, 'name')
// → $data['store']['image'][0]['name']

// 예시 3: 목록 파트
$node = array('menu', 1, 'price')
// → $data['menu'][1]['price']
```

**훅 메서드에서 $node 활용:**

```php
public function is_change($col, &$current_value, $prev_value, &$all_data, $node) {
    // $node를 통해 정확한 위치 파악 가능
    if ($node === array('menu', 'price')) {
        // 특정 위치의 price만 처리
    }
    
    // 또는 경로 문자열로 변환
    $path = implode('/', $node);  // 'menu/price'
    
    // 깊이 체크
    $depth = count($node);
    if ($depth === 2) {
        // 목록 파트의 최상위 레벨
    }
}
```

### 6. 크로스 파트 업데이트

**예시: 메뉴 가격 변경 시 매장의 평균가 자동 계산**

```php
// plugins/store_manager/parts/Menu.php
class Menu extends StoreSchemaBase {
    protected $list_part = true;
    
    public function is_change($col, &$current_value, $prev_value, &$all_data, $node) {
        // 목록 파트의 price 컬럼 변경 시
        if ($col === 'menu/n/price') {
            // 가격이 변경되면 평균가 재계산
            $this->recalculate_store_avg_price($all_data);
        }
    }
    
    public function list_row_delete($col, &$current_value, $prev_value, &$all_data) {
        // 메뉴 항목 삭제 시도 평균가 재계산
        $this->recalculate_store_avg_price($all_data);
    }
    
    private function recalculate_store_avg_price(&$all_data) {
        $total = 0;
        $count = 0;
        
        if (isset($all_data['menu']) && is_array($all_data['menu'])) {
            foreach ($all_data['menu'] as $menu_item) {
                if (isset($menu_item['price'])) {
                    $total += intval($menu_item['price']);
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            // 크로스 파트 업데이트!
            $all_data['store']['avg_price'] = intval($total / $count);
        }
    }
}
```

### 7. 훅 사용 예시

**예시 1: 생성일시 자동 기록**

```php
class Favorite extends StoreSchemaBase {
    public function is_new($col, &$current_value, $prev_value, &$all_data, $node) {
        // 목록 파트의 각 행 생성 시
        if ($col === 'favorite/n') {
            $current_value['created_at'] = G5_TIME_YMDHIS;
        }
    }
}
```

**예시 2: 파일 삭제**

```php
class Store extends StoreSchemaBase {
    public function is_delete($col, &$current_value, $prev_value, &$all_data, $node) {
        // image 배열의 각 항목 삭제 시
        if ($col === 'store/image/n') {
            if (isset($prev_value['path']) && file_exists($prev_value['path'])) {
                @unlink($prev_value['path']);
            }
        }
    }
}
```

**예시 3: 값 검증**

```php
class Store extends StoreSchemaBase {
    public function is_change($col, &$current_value, $prev_value, &$all_data, $node) {
        // 전화번호 컬럼 변경 시
        if ($col === 'store/tel') {
            // 전화번호 포맷 자동 변환
            $current_value = preg_replace('/[^0-9]/', '', $current_value);
        }
    }
}
```

### 8. 훅 주의사항

1. **참조 전달**: `&$current_value`, `&$all_data`는 참조이므로 직접 수정 가능
2. **목록 파트 삭제**: 반드시 `list_row_delete` 사용 (`is_delete` 아님!)
3. **컬럼 구분**: `$col` 파라미터로 어떤 컬럼인지 판별
4. **배열 필드**: 전체 배열 + 각 요소 모두 훅 실행됨
5. **성능**: 필요한 컬럼에만 조건문 사용 (if ($col === 'xxx'))

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

## 체크박스 처리

### 1. 문제점

HTML 폼에서 **체크박스는 OFF 상태일 때 데이터를 전송하지 않습니다.**

```html
<!-- 체크 ON → POST에 포함됨 -->
<input type="checkbox" name="store[is_open]" value="1" checked>

<!-- 체크 OFF → POST에 포함 안됨! ❌ -->
<input type="checkbox" name="store[is_open]" value="1">
```

**문제:**
- OFF 상태는 서버로 전송되지 않음
- 데이터베이스에 기존 값이 그대로 유지됨
- 사용자는 체크를 해제했지만 실제로는 변경 안 됨

### 2. 해결책: `$.fn.loaded()` 사용

Weaver는 페이지 로드 시 **모든 체크박스의 상태를 자동으로 hidden 필드로 추가**하는 기능을 제공합니다.

**자동 처리 메커니즘:**

```javascript
// Weaver 코어에 내장된 기능
$(function() {
    // data-wv-checkbox-group이 있는 요소 찾기
    $('[data-wv-checkbox-group]').each(function() {
        var $container = $(this);
        var groupName = $container.data('wv-checkbox-group');
        
        // 그룹 내 모든 체크박스 찾기
        $container.find('input[type="checkbox"]').each(function() {
            var $checkbox = $(this);
            var name = $checkbox.attr('name');
            
            // hidden 필드 추가 (OFF 상태 전송용)
            if (!$checkbox.is(':checked')) {
                $('<input type="hidden">')
                    .attr('name', name)
                    .val('0')
                    .insertBefore($checkbox);
            }
        });
    });
});
```

### 3. 사용 방법

**Step 1: 체크박스 그룹에 data 속성 추가**

```php
<!-- 체크박스들을 감싸는 컨테이너에 data-wv-checkbox-group 추가 -->
<div data-wv-checkbox-group="store">
    <label>
        <input type="checkbox" name="store[is_open]" value="1" 
               <?php echo $store->store->is_open ? 'checked' : ''; ?>>
        영업중
    </label>
    
    <label>
        <input type="checkbox" name="store[is_delivery]" value="1"
               <?php echo $store->store->is_delivery ? 'checked' : ''; ?>>
        배달가능
    </label>
    
    <label>
        <input type="checkbox" name="store[is_parking]" value="1"
               <?php echo $store->store->is_parking ? 'checked' : ''; ?>>
        주차가능
    </label>
</div>
```

**Step 2: $.fn.loaded() 호출**

```javascript
$(function() {
    // 페이지 로드 완료 후 체크박스 처리
    $(document).loaded();
});
```

### 4. 동작 원리

**페이지 로드 시:**

```html
<!-- 원본 HTML -->
<div data-wv-checkbox-group="store">
    <input type="checkbox" name="store[is_open]" value="1" checked>
    <input type="checkbox" name="store[is_delivery]" value="1">
    <input type="checkbox" name="store[is_parking]" value="1">
</div>

↓ $.fn.loaded() 실행 ↓

<!-- 처리 후 HTML -->
<div data-wv-checkbox-group="store">
    <!-- is_open: 체크됨 → hidden 추가 안 함 -->
    <input type="checkbox" name="store[is_open]" value="1" checked>
    
    <!-- is_delivery: 체크 안 됨 → hidden 추가 -->
    <input type="hidden" name="store[is_delivery]" value="0">
    <input type="checkbox" name="store[is_delivery]" value="1">
    
    <!-- is_parking: 체크 안 됨 → hidden 추가 -->
    <input type="hidden" name="store[is_parking]" value="0">
    <input type="checkbox" name="store[is_parking]" value="1">
</div>
```

**폼 전송 시:**

```php
// POST 데이터
$_POST = array(
    'store' => array(
        'is_open' => '1',      // 체크됨
        'is_delivery' => '0',  // hidden 필드로 전송
        'is_parking' => '0'    // hidden 필드로 전송
    )
);
```

### 5. 스킨에서 사용 예시

**Form 스킨 (plugins/store_manager/theme/basic/pc/store/form/options.php):**

```php
<?php
if (!defined('_GNUBOARD_')) exit;

$is_open = isset($row['store']['is_open']) ? $row['store']['is_open'] : 0;
$is_delivery = isset($row['store']['is_delivery']) ? $row['store']['is_delivery'] : 0;
$is_parking = isset($row['store']['is_parking']) ? $row['store']['is_parking'] : 0;
?>

<div id="<?php echo $skin_id; ?>" class="<?php echo $skin_class; ?>">
    <h4>매장 옵션</h4>
    
    <!-- 체크박스 그룹 -->
    <div data-wv-checkbox-group="store">
        <label class="option-item">
            <input type="checkbox" 
                   name="store[is_open]" 
                   value="1" 
                   <?php echo $is_open ? 'checked' : ''; ?>>
            영업중
        </label>
        
        <label class="option-item">
            <input type="checkbox" 
                   name="store[is_delivery]" 
                   value="1" 
                   <?php echo $is_delivery ? 'checked' : ''; ?>>
            배달 가능
        </label>
        
        <label class="option-item">
            <input type="checkbox" 
                   name="store[is_parking]" 
                   value="1" 
                   <?php echo $is_parking ? 'checked' : ''; ?>>
            주차 가능
        </label>
    </div>
</div>

<script>
$(function() {
    // 페이지 로드 완료 후 체크박스 처리
    $(document).loaded();
});
</script>
```

### 6. 목록 파트에서 사용

목록 파트(menu, contract 등)의 각 항목에도 동일하게 적용 가능합니다.

```php
<!-- 메뉴 목록 폼 -->
<div id="menu-list">
    <?php foreach($store->menu->list as $idx => $menu) { ?>
    <div class="menu-item" data-wv-checkbox-group="menu-<?php echo $idx; ?>">
        <input type="hidden" name="menu[<?php echo $idx; ?>][id]" 
               value="<?php echo $menu['id']; ?>">
        
        <input type="text" name="menu[<?php echo $idx; ?>][name]" 
               value="<?php echo $menu['name']; ?>">
        
        <label>
            <input type="checkbox" 
                   name="menu[<?php echo $idx; ?>][is_soldout]" 
                   value="1" 
                   <?php echo $menu['is_soldout'] ? 'checked' : ''; ?>>
            품절
        </label>
        
        <label>
            <input type="checkbox" 
                   name="menu[<?php echo $idx; ?>][is_popular]" 
                   value="1" 
                   <?php echo $menu['is_popular'] ? 'checked' : ''; ?>>
            인기메뉴
        </label>
    </div>
    <?php } ?>
</div>

<script>
$(function() {
    $(document).loaded();
});
</script>
```

### 7. AJAX 폼에서 사용

AJAX로 데이터를 전송할 때도 동일하게 작동합니다.

```javascript
$('#save-btn').on('click', function() {
    // 체크박스 상태 확인 (hidden 필드가 자동 추가되어 있음)
    var formData = $('#store-form').serializeObject();
    
    $.ajax({
        url: '<?php echo wv()->store_manager->plugin_url; ?>/ajax.php',
        method: 'POST',
        data: {
            action: 'update',
            made: 'sub01_01',
            ...formData
        },
        success: function(response) {
            alert('저장되었습니다.');
        }
    });
});
```

### 8. 주의사항

1. **data-wv-checkbox-group 필수**: 이 속성이 없으면 자동 처리 안 됨
2. **$.fn.loaded() 호출 필수**: 페이지 로드 후 반드시 호출해야 함
3. **동적 폼**: AJAX로 폼을 추가한 경우, 추가 후 다시 `$(document).loaded()` 호출
4. **value 속성**: 체크박스는 항상 `value="1"` 사용 권장
5. **이름 규칙**: name 속성은 파트 구조와 일치해야 함 (`part[column]` 형식)

### 9. 디버깅

**체크박스가 제대로 전송되는지 확인:**

```javascript
// 폼 전송 전 확인
$('#store-form').on('submit', function(e) {
    e.preventDefault();
    
    var formData = $(this).serializeArray();
    console.log('전송될 데이터:', formData);
    
    // 체크박스 필드 확인
    formData.forEach(function(item) {
        if (item.name.indexOf('is_') > -1) {
            console.log(item.name + ' = ' + item.value);
        }
    });
});
```

**예상 출력:**
```
전송될 데이터: [...]
store[is_open] = 1
store[is_delivery] = 0
store[is_parking] = 0
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