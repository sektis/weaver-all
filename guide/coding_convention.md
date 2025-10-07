# 🎨 프로젝트 코딩 컨벤션

> Weaver 프로젝트 표준 코딩 규칙 및 패턴

---

## 📋 기본 규칙

### PHP 버전
- **PHP 5.6 기준**
- 타입힌트 사용 금지
- 최신 문법 사용 금지

### 코딩 스타일
```php
// 메서드명: snake_case
public function get_store_list() {}

// 클래스/메서드 선언: { 같은 줄
class Test {
    public function test_one() {
    }
}

// PHP 단축 문법 사용 금지
// ❌ <?php foreach($list as $item): ?>
// ✅ <?php foreach($list as $item) { ?>
```

### JavaScript
```javascript
// 함수명 및 변수명: snake_case
function get_store_data() {}
var store_list = [];
```

### CSS
```css
/* <style> 안의 속성은 한 줄로 */
.class { color: red; font-size: 14px; }

/* px 값은 var(--wv-{숫자})로 */
.box { padding: var(--wv-16); margin: var(--wv-20); }
```

---

## 🎯 자주 사용하는 변수명

### Store Manager 관련
```php
// Made 키
$made = 'sub01_01';      // 매장
$made = 'member';        // 회원
$made = 'invite';        // 초대

// Store 객체
$store = wv()->store_manager->made('sub01_01')->get($wr_id);
$list = wv()->store_manager->made('sub01_01')->get_list($opts);

// 결과
$result = array(
    'list' => array(),   // 목록
    'paging' => '',      // 페이징 HTML
    'total' => 0         // 전체 개수
);
```

### CEO/관리자 모드
```php
// CEO 모드 전역 변수
global $current_store;          // 현재 매장 객체
global $current_store_wr_id;    // 현재 매장 ID
global $current_member;         // 현재 회원 객체
global $current_member_wr_id;   // 현재 회원 ID

// 경로 식별
global $wv_dir_var;  // 'admin', 'ceo', null
```

### 공통 변수
```php
$wr_id           // 게시글/데이터 ID
$bo_table        // 게시판 테이블명
$page            // 페이지 번호
$member          // 로그인 회원 정보
$wv_page_id      // 페이지 ID
```

---

## 🔧 자주 사용하는 패턴

### 1. Store Manager 데이터 조회

#### 목록 조회
```php
$result = wv()->store_manager->made('sub01_01')->get_list(array(
    'page' => $page,
    'limit' => 20,
    'where' => "mb_id = '{$member['mb_id']}'",
    'orderby' => 'wr_id DESC'
));

$list = $result['list'];
$paging = $result['paging'];
$total = $result['total'];
```

#### 단건 조회
```php
$store = wv()->store_manager->made('sub01_01')->get($wr_id);

// 파트 데이터 접근
echo $store['store']['name'];        // 배열 방식
echo $store->store->name;            // 객체 방식 (추천)
```

#### 저장
```php
$data = array(
    'store' => array(
        'name' => '매장명',
        'tel' => '전화번호'
    ),
    'location' => array(
        'address' => '주소',
        'lat' => '위도',
        'lng' => '경도'
    )
);

$wr_id = wv()->store_manager->made('sub01_01')->save($wr_id, $data);
```

#### 삭제
```php
wv()->store_manager->made('sub01_01')->delete($wr_id);
```

### 2. AJAX 패턴

#### 수정 버튼 (Offcanvas)
```php
<a href="#" 
   data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
   data-wv-ajax-data='{"action":"form","made":"sub01_01","part":"store","field":"ceo/name","wr_id":"<?php echo $wr_id; ?>"}'
   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]">
    [수정]
</a>
```

#### 삭제 버튼
```php
<a href="#" 
   data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
   data-wv-ajax-data='{"action":"delete","made":"sub01_01","wr_id":"<?php echo $wr_id; ?>"}'
   class="wv-data-list-delete-btn">
    [삭제]
</a>
```

#### 위젯 로드
```php
<div data-wv-ajax-url="<?php echo wv()->store_manager->ajax_url(); ?>"
     data-wv-ajax-data='{"action":"widget","widget":"ceo/select_store"}'
     data-wv-ajax-option="offcanvas,bottom,backdrop-static">
    클릭하면 위젯 로드
</div>
```

#### AJAX 옵션
```
offcanvas,end          // 오른쪽에서 나오는 Offcanvas
offcanvas,bottom       // 아래에서 나오는 Offcanvas
modal                  // 모달
backdrop               // 배경 클릭 시 닫기
backdrop-static        // 배경 클릭 시 안 닫힌
class: w-[436px]       // 커스텀 클래스
```

### 3. 파트 렌더링

#### View 모드
```php
// 일반 파트
<?php echo $store->store->render_part('ceo/name', 'view'); ?>
<?php echo $store->location->render_part('ceo/address', 'view'); ?>

// 목록 파트
<?php echo $store->menu->render_part('ceo/menu', 'view'); ?>
```

#### Form 모드
```php
<?php echo $store->store->render_part('ceo/name', 'form'); ?>
<?php echo $store->location->render_part('ceo/address', 'form'); ?>
```

#### 변수 전달
```php
<?php echo $store->store->render_part('ceo/name', 'view', array(
    'custom_var' => 'value',
    'show_button' => true
)); ?>
```

### 4. 목록 페이지 템플릿

```php
<?php
// 목록 조회
$result = wv()->store_manager->made('sub01_01')->get_list(array(
    'page' => $page,
    'limit' => 20
));
$list = $result['list'];
?>

<form name="flist" method="post">
    <table class="table">
        <thead>
            <tr>
                <th>번호</th>
                <th>매장명</th>
                <th>주소</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($list) { ?>
                <?php foreach ($list as $i => $row) { ?>
                    <tr>
                        <td><?php echo $row['num']; ?></td>
                        <td><?php echo $row['store']['name']; ?></td>
                        <td><?php echo $row['location']['address']; ?></td>
                        <td>
                            <a href="#" 
                               data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
                               data-wv-ajax-data='{"action":"form","made":"sub01_01","wr_id":"<?php echo $row['wr_id']; ?>"}'
                               data-wv-ajax-option="offcanvas,end,backdrop">
                                [수정]
                            </a>
                            <a href="#" 
                               data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
                               data-wv-ajax-data='{"action":"delete","made":"sub01_01","wr_id":"<?php echo $row['wr_id']; ?>"}'
                               class="wv-data-list-delete-btn">
                                [삭제]
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4">자료가 없습니다.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <!-- 페이징 -->
    <div class="pagination">
        <?php echo $result['paging']; ?>
    </div>
</form>
```

### 5. 위젯 호출

```php
// 기본 호출
<?php echo wv_widget('common/header'); ?>

// 변수 전달
<?php echo wv_widget('ceo/store_card', array(
    'wr_id' => $wr_id,
    'show_edit' => true
)); ?>
```

### 6. 메뉴 생성

```php
$menu_array = array(
    array(
        'name' => '홈',
        'url' => '/',
        'icon' => WV_URL . '/img/home.png'
    ),
    array(
        'name' => '매장관리',
        'url' => '/?wv_page_id=0101',
        'icon' => WV_URL . '/img/store.png',
        'sub' => array(
            array('name' => '매장 목록', 'url' => '/?wv_page_id=0101'),
            array('name' => '매장 추가', 'url' => '/?wv_page_id=0102')
        )
    )
);

wv('menu')->make('main_menu')->setMenu($menu_array, true);
```

---

## 📁 파일 경로 규칙

### 일반 페이지
```
plugins/page/theme/basic/pc/0101.php
plugins/page/theme/basic/mobile/0101.php
```

### CEO 주입 페이지
```
plugins/ceo/theme/basic/plugins/page/theme/pc/0101.php
plugins/ceo/theme/basic/plugins/page/theme/mobile/0101.php
```

### 관리자 주입 페이지
```
plugins/gnu_adm/theme/basic/plugins/page/theme/pc/0101.php
plugins/gnu_adm/theme/basic/plugins/page/theme/mobile/0101.php
```

### 파트 스킨
```
plugins/store_manager/theme/basic/pc/{part}/view/{column}.php
plugins/store_manager/theme/basic/pc/{part}/form/{column}.php
```

### 위젯
```
plugins/widget/theme/basic/pc/{widget_name}/skin.php
plugins/widget/theme/basic/mobile/{widget_name}/skin.php
```

### Layout
```
plugins/layout/theme/basic/pc/head.php
plugins/layout/theme/basic/pc/tail.php
plugins/layout/theme/basic/pc/common.php
```

---

## 🎨 스킨 작성 패턴

### 파트 스킨 (view)
```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<div id="<?php echo $skin_id; ?>" class="<?php echo $skin_class; ?>">
    <style>
        <?php echo $skin_selector; ?> { /* 스타일 */ }
    </style>
    
    <div class="content">
        <?php echo $row['store']['name']; ?>
    </div>
    
    <script>
    $(document).ready(function() {
        var $skin = $("<?php echo $skin_selector; ?>");
    });
    </script>
</div>
```

### 파트 스킨 (form)
```php
<?php
if (!defined('_GNUBOARD_')) exit;
?>
<form method="post" action="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="made" value="sub01_01">
    <input type="hidden" name="wr_id" value="<?php echo $row['wr_id']; ?>">
    
    <div class="form-group">
        <label>매장명</label>
        <input type="text" name="store[name]" 
               value="<?php echo $row['store']['name']; ?>" 
               class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary">저장</button>
</form>
```

---

## 🚫 금지 사항

### 코드 수정 시
- ❌ 기존 변수명 임의 수정 금지
- ❌ 중복 코드 붙이지 않기
- ❌ 복잡한 헬퍼 메서드 추가하지 않기

### 파일 작성 시
- ❌ 임의로 파일 경로 변경 금지
- ❌ 네이밍 규칙 무시 금지
- ❌ 주석 없는 복잡한 코드 금지

---

## ✅ 권장 사항

### 코드 작성
- ✅ 필요한 부분만 간결하게 수정
- ✅ 주석으로 의도 명확히 표시
- ✅ 함수/변수 역추적해서 기능 파악
- ✅ 최대한 간단한 수정 방법 찾기

### 협업
- ✅ 작업 시작 전 project_status.md 확인
- ✅ 작업 완료 후 문서 업데이트
- ✅ 변경 사항은 반드시 기록

---

## 📚 참고

- store_manager_guide.md - Store Manager 상세 가이드
- project_status.md - 현재 프로젝트 상태
- checklist.md - 작업 체크리스트
- faq.md - 자주 묻는 질문