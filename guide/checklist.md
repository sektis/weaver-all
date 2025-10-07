# ✅ 작업 체크리스트

> 새 기능 추가 및 수정 시 확인사항

---

## 📋 새 페이지 만들 때

### CEO/관리자 주입 페이지
- [ ] 페이지 파일 생성 (예: 0201.php)
    - CEO: `plugins/ceo/theme/basic/plugins/page/theme/pc/0201.php`
    - 관리자: `plugins/gnu_adm/theme/basic/plugins/page/theme/pc/0201.php`
- [ ] 페이지 ID 메뉴에 추가 (GnuAdm.php 또는 Ceo.php)
- [ ] 권한 체크 추가 (필요시)
- [ ] 현재 매장 전역 변수 사용 확인 (CEO 모드)
    - `$current_store`
    - `$current_store_wr_id`
- [ ] 모바일 페이지 생성 (필요시)
- [ ] 테스트
    - [ ] PC 접근 확인
    - [ ] 모바일 접근 확인
    - [ ] 메뉴 활성화 확인

### 일반 페이지
- [ ] 페이지 파일 생성
    - `plugins/page/theme/basic/pc/0101.php`
- [ ] 페이지 라우팅 확인
- [ ] Layout 설정 확인
- [ ] 테스트

---

## 📋 새 파트 스킨 만들 때

### View 스킨
- [ ] 파일 생성
    - `plugins/store_manager/theme/basic/pc/{part}/view/{column}.php`
- [ ] 기본 구조 작성
  ```php
  <?php if (!defined('_GNUBOARD_')) exit; ?>
  <div id="<?php echo $skin_id; ?>" class="<?php echo $skin_class; ?>">
      <style><?php echo $skin_selector; ?> {}</style>
      <!-- 내용 -->
  </div>
  ```
- [ ] 데이터 출력 확인
    - `$row['part']['column']`
- [ ] 스타일 추가
- [ ] 모바일 대응 확인

### Form 스킨
- [ ] 파일 생성
    - `plugins/store_manager/theme/basic/pc/{part}/form/{column}.php`
- [ ] 폼 구조 작성
  ```php
  <form method="post" action="...">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="made" value="sub01_01">
      <input type="hidden" name="wr_id" value="<?php echo $row['wr_id']; ?>">
      <!-- 입력 필드 -->
  </form>
  ```
- [ ] 기본값 설정
    - `value="<?php echo $row['part']['column']; ?>"`
- [ ] 유효성 검사 추가
- [ ] AJAX 처리 확인
- [ ] 저장 테스트

### 목록 파트 스킨
- [ ] 목록 렌더링 확인
    - `$row['part']` (배열)
- [ ] 항목 추가 버튼
- [ ] 항목 수정 버튼
- [ ] 항목 삭제 버튼
- [ ] 순서 변경 기능 (필요시)

---

## 📋 AJAX 기능 추가 시

### ajax.php 수정
- [ ] case 추가
  ```php
  case 'action_name':
      // 처리 로직
      break;
  ```
- [ ] 권한 체크
- [ ] 데이터 유효성 검사
- [ ] 에러 처리
- [ ] 응답 형식 확인
    - JSON: `echo json_encode(array('success' => true));`
    - HTML: `echo $html;`

### 프론트엔드
- [ ] data-wv-ajax-url 속성 확인
  ```php
  data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url; ?>/ajax.php"
  ```
- [ ] data-wv-ajax-data 속성 확인
  ```php
  data-wv-ajax-data='{"action":"form","made":"sub01_01","wr_id":"<?php echo $wr_id; ?>"}'
  ```
- [ ] data-wv-ajax-option 설정
    - offcanvas / modal
    - backdrop / backdrop-static
    - 커스텀 클래스
- [ ] 응답 처리 확인
- [ ] 에러 처리 확인

### 테스트
- [ ] 정상 동작 확인
- [ ] 에러 케이스 확인
- [ ] 권한 없을 때 확인
- [ ] 네트워크 에러 시 확인

---

## 📋 Store Manager 설정 시

### Made 생성
- [ ] setting.php에 추가
  ```php
  wv()->store_manager->make('made_key', 'bo_table', array('parts'))->prune_columns();
  ```
- [ ] 파트 스키마 작성
    - `plugins/store_manager/parts/{Part}.php`
- [ ] 컬럼 정의
    - `get_columns()`
    - `get_indexes()`
    - `get_allowed_columns()`
- [ ] 테이블 생성 확인
    - `wv_store_{bo_table}`
    - `wv_store_{bo_table}_{list_part}` (목록 파트)
- [ ] 데이터 조회 테스트
    - `get($wr_id)`
    - `get_list($opts)`

### 파트 추가
- [ ] 파트 스키마 클래스 작성
- [ ] 일반 파트 / 목록 파트 선택
    - `protected $list_part = false;` (일반)
    - `protected $list_part = true;` (목록)
- [ ] 컬럼 추가
- [ ] 인덱스 추가 (필요시)
- [ ] 스킨 제작
- [ ] 테스트

---

## 📋 위젯 만들 때

### 파일 생성
- [ ] 스킨 파일 생성
    - `plugins/widget/theme/basic/pc/{widget_name}/skin.php`
- [ ] 기본 구조 작성
  ```php
  <?php if (!defined('_GNUBOARD_')) exit; ?>
  <div id="<?php echo $skin_id; ?>" class="wv-skin-widget">
      <style><?php echo $skin_selector; ?> {}</style>
      <!-- 위젯 내용 -->
  </div>
  ```
- [ ] 변수 전달 확인
    - 위젯 호출 시: `wv_widget('name', array('var' => 'value'))`
    - 스킨에서: `<?php echo $var; ?>`

### 위젯 호출
- [ ] 페이지/레이아웃에서 호출
  ```php
  <?php echo wv_widget('widget_name'); ?>
  ```
- [ ] 변수 전달 확인
- [ ] 스타일 확인
- [ ] 모바일 대응 확인

---

## 📋 메뉴 추가 시

### 메뉴 데이터 작성
- [ ] 메뉴 배열 작성
  ```php
  $menu = array(
      array(
          'name' => '메뉴명',
          'url' => '/?wv_page_id=0101',
          'icon' => WV_URL . '/img/icon.png',
          'sub' => array(/* 서브메뉴 */)
      )
  );
  ```
- [ ] 메뉴 생성
  ```php
  wv('menu')->make('menu_key')->setMenu($menu, true);
  ```
- [ ] 메뉴 출력
  ```php
  <?php echo wv('menu')->made('menu_key')->displayMenu('menu_skin'); ?>
  ```

### 메뉴 스킨
- [ ] 메뉴 스킨 선택
    - `left_collapse` (왼쪽 접이식)
    - `fixed_bottom` (하단 고정)
    - `dropdown` (드롭다운)
- [ ] 메뉴 활성화 확인
- [ ] 서브메뉴 동작 확인
- [ ] 모바일 대응 확인

---

## 📋 Layout 수정 시

### Head/Tail 수정
- [ ] 파일 경로 확인
    - 일반: `plugins/layout/theme/basic/pc/head.php`
    - CEO: `plugins/ceo/theme/basic/plugins/layout/theme/pc/head.php`
    - 관리자: `plugins/gnu_adm/theme/basic/plugins/layout/theme/pc/head.php`
- [ ] 수정 내용 반영
- [ ] CSS 추가 (layout.css)
- [ ] JS 추가 (layout.js)
- [ ] 모든 페이지에서 테스트

### Common 수정
- [ ] common.php 수정
- [ ] 컨텐츠 영역 확인
- [ ] 사이드바 확인 (있을 경우)
- [ ] 반응형 확인

---

## 📋 배포 전 체크리스트

### 코드 품질
- [ ] 불필요한 주석 제거
- [ ] console.log 제거
- [ ] 에러 처리 확인
- [ ] 보안 이슈 확인
    - SQL Injection
    - XSS
    - CSRF

### 문서 업데이트
- [ ] project_status.md 업데이트
- [ ] todo.md 업데이트
- [ ] 변경 사항 기록

### 테스트
- [ ] PC 브라우저 테스트
- [ ] 모바일 브라우저 테스트
- [ ] 권한별 테스트
- [ ] 에러 케이스 테스트

### 성능
- [ ] 쿼리 최적화 확인
- [ ] 이미지 최적화 확인
- [ ] 불필요한 리소스 제거

---

## 🚨 긴급 수정 시

### 버그 발견
- [ ] 에러 메시지 확인
- [ ] 로그 확인
- [ ] 재현 방법 파악
- [ ] 원인 분석
- [ ] 수정
- [ ] 테스트
- [ ] 문서 기록

### 긴급 배포
- [ ] 수정 내용 확인
- [ ] 최소 범위로 수정
- [ ] 테스트
- [ ] 백업 확인
- [ ] 배포
- [ ] 모니터링

---

## 📚 참고

- project_status.md - 현재 작업 상태
- coding_convention.md - 코딩 규칙
- faq.md - 자주 묻는 질문
- store_manager_guide.md - Store Manager 가이드