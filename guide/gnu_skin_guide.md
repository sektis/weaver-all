# 🎨 Gnu Skin 플러그인 가이드

> **그누보드5 기본 스킨 시스템을 Weaver에서 사용하기**

---

## 📋 목차

1. [개요](#개요)
2. [Gnu Skin 시스템 구조](#gnu-skin-시스템-구조)
3. [기본 사용법](#기본-사용법)
4. [스킨 종류별 가이드](#스킨-종류별-가이드)
5. [스킨 파일 제작](#스킨-파일-제작)
6. [Symlink 시스템](#symlink-시스템)
7. [실전 예시](#실전-예시)
8. [문제 해결](#문제-해결)

---

## 📌 개요

**Gnu Skin 플러그인**은 그누보드5의 기본 스킨 시스템(게시판, 최신글, 회원, 검색, FAQ, 쇼핑몰 등)을 Weaver 플러그인 내에서 사용할 수 있게 해주는 브릿지 플러그인입니다.

### 핵심 특징

✅ **그누보드5 호환**: 기존 그누보드5 스킨 파일명 그대로 사용  
✅ **Weaver 통합**: Weaver 테마 시스템 내에서 관리  
✅ **Symlink 연결**: 자동으로 그누보드5 스킨 경로와 연결  
✅ **다양한 스킨**: 게시판, 회원, 검색, FAQ, 쇼핑몰 등 모든 스킨 지원  
✅ **PC/Mobile 분리**: 디바이스별 스킨 관리

### Gnu Skin vs Widget

| 구분 | Gnu Skin | Widget |
|------|----------|--------|
| **목적** | 그누보드5 스킨 재현 | 재사용 컴포넌트 |
| **파일명** | `list.skin.php` 등 | `skin.php` |
| **호출** | 그누보드5 자동 호출 | `wv_widget()` |
| **위치** | `theme/weaver/pc/{스킨종류}/` | `theme/basic/pc/{위젯명}/` |

---

## 🏗️ Gnu Skin 시스템 구조

### 디렉토리 구조

```
plugins/gnu_skin/
├── GnuSkin.php                    # 메인 클래스
├── gnu_skin.lib.php               # 헬퍼 함수들
└── theme/
    └── weaver/                    # Weaver 테마
        ├── pc/                    # PC 스킨
        │   ├── board/             # 게시판 스킨
        │   │   ├── basic/         # 기본 게시판
        │   │   ├── gallery/       # 갤러리 게시판
        │   │   ├── basic_depth/   # 댓글형 게시판
        │   │   └── ...
        │   ├── latest/            # 최신글 스킨
        │   │   └── gallery/
        │   ├── member/            # 회원 스킨
        │   ├── search/            # 검색 스킨
        │   ├── faq/               # FAQ 스킨
        │   ├── new/               # 새글 스킨
        │   └── shop/              # 쇼핑몰 스킨
        └── mobile/                # 모바일 스킨
            └── (위와 동일 구조)
```

### GnuSkin 클래스 주요 메서드

```php
class GnuSkin extends Plugin {
    
    // 1. 스킨 사용 설정
    public function set_use_skin($dir, $file='', $skin='basic', $device='pc')
    
    // 2. 스킨 경로 확인
    public function skin_check($dir, $file, $skin)
    
    // 3. 스킨 경로 가져오기
    public function get_skin_path($device='pc', $skin_gubun='', $skin_dir='', $file_name='')
    
    // 4. Symlink 추가
    public function add_symlink($org_path, $skin_gubun, $skin_dir, $device='pc', $file_name='')
    
    // 5. Social 스킨 사용
    public function use_social_skin()
}
```

---

## 🚀 기본 사용법

### 1. 그누보드5 설정에서 Weaver 스킨 선택

**방법 1: 관리자 페이지에서 설정**

```
[관리자] → [환경설정] → [기본환경설정]
→ 회원스킨: weaver/basic 선택
→ 새글스킨: weaver/basic 선택
→ 검색스킨: weaver/basic 선택
→ FAQ스킨: weaver/basic 선택
→ 쇼핑몰스킨: weaver/basic 선택
```

**방법 2: DB에서 직접 설정**

```sql
-- PC 스킨
UPDATE g5_config SET cf_member_skin = 'weaver/basic';
UPDATE g5_config SET cf_new_skin = 'weaver/basic';
UPDATE g5_config SET cf_search_skin = 'weaver/basic';
UPDATE g5_config SET cf_faq_skin = 'weaver/basic';
UPDATE g5_config SET de_shop_skin = 'weaver/basic';

-- 모바일 스킨
UPDATE g5_config SET cf_mobile_member_skin = 'weaver/basic';
UPDATE g5_config SET cf_mobile_new_skin = 'weaver/basic';
UPDATE g5_config SET cf_mobile_search_skin = 'weaver/basic';
UPDATE g5_config SET cf_mobile_faq_skin = 'weaver/basic';
UPDATE g5_config SET de_mobile_shop_skin = 'weaver/basic';
```

### 2. 게시판 스킨 설정

**방법 1: 게시판 관리에서 설정**

```
[관리자] → [게시판관리] → [게시판 추가/수정]
→ PC 스킨: weaver/basic 선택
→ 모바일 스킨: weaver/basic 선택
```

**방법 2: DB에서 설정**

```sql
-- 특정 게시판의 스킨 변경
UPDATE g5_board SET bo_skin = 'weaver/basic' WHERE bo_table = 'notice';
UPDATE g5_board SET bo_mobile_skin = 'weaver/basic' WHERE bo_table = 'notice';

-- 갤러리 스킨 적용
UPDATE g5_board SET bo_skin = 'weaver/gallery' WHERE bo_table = 'gallery';
```

### 3. 코드에서 스킨 설정

```php
// 특정 스킨만 Weaver 스킨 사용하도록 설정
wv('gnu_skin')->set_use_skin('member', '', 'basic', 'pc');
wv('gnu_skin')->set_use_skin('search', '', 'basic', 'pc');
wv('gnu_skin')->set_use_skin('faq', '', 'basic', 'pc');

// 게시판 특정 파일만 Weaver 스킨 사용
wv('gnu_skin')->set_use_skin('board', 'list', 'basic', 'pc');
wv('gnu_skin')->set_use_skin('board', 'view', 'basic', 'pc');

// 모바일 스킨 설정
wv('gnu_skin')->set_use_skin('member', '', 'basic', 'mobile');
```

### 4. 스킨 경로 직접 가져오기

```php
// 특정 스킨 파일 경로 가져오기
$login_file = wv('gnu_skin')->get_skin_path(
    G5_IS_MOBILE ? 'mobile' : 'pc',  // 디바이스
    'member',                         // 스킨 종류
    'basic',                          // 스킨명
    '/login.skin.php'                 // 파일명
);

// 스킨 파일 직접 include
include_once $login_file;

// 게시판 스킨 경로
$board_skin_path = wv('gnu_skin')->get_skin_path('pc', 'board', 'gallery', '/list.skin.php');

// 최신글 스킨 경로
$latest_skin_path = wv('gnu_skin')->get_skin_path('pc', 'latest', 'gallery', '/latest.skin.php');
```

---

## 📚 스킨 종류별 가이드

### 1. 게시판 스킨 (Board)

#### 파일 구조

```
theme/weaver/pc/board/{스킨명}/
├── list.skin.php              # 목록 페이지
├── view.skin.php              # 보기 페이지
├── write.skin.php             # 쓰기 페이지
├── write_update.head.skin.php # 쓰기 전처리
├── write_update.tail.skin.php # 쓰기 후처리
├── view_comment.skin.php      # 댓글
├── delete.skin.php            # 삭제 확인
├── password.skin.php          # 비밀번호 확인
└── style.css                  # 스타일시트
```

#### 기본 스킨 종류

- **basic**: 기본 게시판 (일반 목록형)
- **gallery**: 갤러리 게시판 (이미지 썸네일)
- **basic_depth**: 댓글형 게시판 (트리 구조)

#### 사용 예시

```php
// 게시판 생성 시 스킨 설정
$sql = "INSERT INTO g5_board SET
    bo_table = 'notice',
    bo_subject = '공지사항',
    bo_skin = 'weaver/basic',         // PC 스킨
    bo_mobile_skin = 'weaver/basic'   // 모바일 스킨
";

// 갤러리 게시판
$sql = "INSERT INTO g5_board SET
    bo_table = 'gallery',
    bo_subject = '갤러리',
    bo_skin = 'weaver/gallery',
    bo_mobile_skin = 'weaver/gallery',
    bo_gallery_cols = 4,              // 갤러리 컬럼 수
    bo_gallery_width = 297,           // 썸네일 너비
    bo_gallery_height = 212           // 썸네일 높이
";
```

### 2. 최신글 스킨 (Latest)

#### 파일 구조

```
theme/weaver/pc/latest/{스킨명}/
├── latest.skin.php            # 최신글 출력
└── style.css                  # 스타일시트
```

#### 사용 예시

```php
// 최신글 출력
echo latest('weaver/gallery', 'notice', 5, 23);

// 커스텀 변수 전달
echo latest('weaver/gallery', 'gallery', 8, 0, 0, 1, 'custom_class');
```

#### 스킨 파일 내부 구조

```php
// latest.skin.php
<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// 리스트 후킹 (필수)
$list = run_replace('wv_hook_board_list', $list, $board);

// 스킨 ID/Selector 사용 가능
?>
<div id="<?php echo $skin_id?>" class="wv-skin-latest">
    <style>
        <?php echo $skin_selector?> {}
    </style>
    
    <!-- 최신글 목록 출력 -->
    <?php for ($i=0; $i<count($list); $i++) { 
        $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);
        ?>
        <div class="latest-item">
            <a href="<?php echo $list[$i]['href']; ?>">
                <?php echo $list[$i]['subject']; ?>
            </a>
        </div>
    <?php } ?>
</div>
```

### 3. 회원 스킨 (Member)

#### 파일 구조

```
theme/weaver/pc/member/basic/
├── login.skin.php             # 로그인
├── register.skin.php          # 회원가입
├── register_form.skin.php     # 회원가입 폼
├── member_confirm.skin.php    # 비밀번호 확인
├── password_lost.skin.php     # 비밀번호 찾기
├── profile.skin.php           # 정보수정
└── style.css
```

#### 설정 방법

```php
// 환경설정에서
cf_member_skin = 'weaver/basic'
cf_mobile_member_skin = 'weaver/basic'

// 코드에서
wv('gnu_skin')->set_use_skin('member', '', 'basic', 'pc');
```

### 4. 검색 스킨 (Search)

#### 파일 구조

```
theme/weaver/pc/search/basic/
├── search.skin.php            # 검색 결과
└── style.css
```

### 5. FAQ 스킈 (Faq)

#### 파일 구조

```
theme/weaver/pc/faq/basic/
├── list.skin.php              # FAQ 목록
├── view.skin.php              # FAQ 상세
└── style.css
```

### 6. 새글 스킨 (New)

#### 파일 구조

```
theme/weaver/pc/new/basic/
├── new.skin.php               # 새글 목록
└── style.css
```

### 7. 쇼핑몰 스킨 (Shop)

#### 파일 구조

```
theme/weaver/pc/shop/basic/
├── (쇼핑몰 관련 다양한 스킨 파일들)
└── style.css
```

---

## 🎨 스킨 파일 제작

### 게시판 스킨 제작 예시

#### 1. list.skin.php (목록 페이지)

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// 필수: 리스트 후킹
$list = run_replace('wv_hook_board_list', $list, $board);

// CSS 추가
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- 게시판 목록 -->
<div id="bo_list">
    
    <!-- 카테고리 -->
    <?php if ($is_category) { ?>
        <div class="mb-3">
            <?php echo wv_make_menu_display($bo_table, 'common/scroll', explode('|', $board['bo_category_list']), 'sca'); ?>
        </div>
    <?php } ?>
    
    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        
        <!-- 페이지 정보 -->
        <div class="board-info">
            <span>Total <?php echo number_format($total_count) ?>건</span>
            <span><?php echo $page ?> 페이지</span>
        </div>
        
        <!-- 목록 -->
        <table class="table">
            <thead>
                <tr>
                    <?php if ($is_checkbox) { ?>
                        <th><input type="checkbox" id="chkall"></th>
                    <?php } ?>
                    <th>번호</th>
                    <th>제목</th>
                    <th>글쓴이</th>
                    <th>날짜</th>
                    <th>조회</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<count($list); $i++) { 
                    $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);
                    ?>
                    <tr>
                        <?php if ($is_checkbox) { ?>
                            <td>
                                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>">
                            </td>
                        <?php } ?>
                        <td><?php echo $list[$i]['num'] ?></td>
                        <td>
                            <a href="<?php echo $list[$i]['href'] ?>">
                                <?php echo $list[$i]['subject'] ?>
                                <?php if ($list[$i]['comment_cnt']) { ?>
                                    <span class="comment-cnt">[<?php echo $list[$i]['comment_cnt'] ?>]</span>
                                <?php } ?>
                            </a>
                        </td>
                        <td><?php echo $list[$i]['name'] ?></td>
                        <td><?php echo $list[$i]['datetime2'] ?></td>
                        <td><?php echo $list[$i]['wr_hit'] ?></td>
                    </tr>
                <?php } ?>
                
                <?php if (count($list) == 0) { ?>
                    <tr>
                        <td colspan="<?php echo $colspan ?>" class="text-center">
                            게시물이 없습니다.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <!-- 페이징 -->
        <?php echo $write_pages ?>
        
        <!-- 버튼 -->
        <div class="board-buttons">
            <?php if ($write_href) { ?>
                <a href="<?php echo $write_href ?>" class="btn btn-primary">글쓰기</a>
            <?php } ?>
        </div>
    </form>
</div>

<script>
// 전체 선택
document.getElementById('chkall').onclick = function() {
    var checkboxes = document.getElementsByName('chk_wr_id[]');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = this.checked;
    }
};
</script>
```

#### 2. view.skin.php (보기 페이지)

```php
<?php
if (!defined("_GNUBOARD_")) exit;

// 필수: 뷰 후킹
$view = run_replace('wv_hook_board_view', $view, $board);

// CSS 추가
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- 게시글 보기 -->
<article id="bo_v">
    <header>
        <h2><?php echo $view['subject'] ?></h2>
        <div class="view-info">
            <span class="author"><?php echo $view['name'] ?></span>
            <span class="date"><?php echo $view['datetime'] ?></span>
            <span class="hit">조회 <?php echo $view['wr_hit'] ?></span>
        </div>
    </header>
    
    <!-- 본문 -->
    <section class="view-content">
        <?php echo get_view_thumbnail($view['content']); ?>
    </section>
    
    <!-- 첨부파일 -->
    <?php if ($view['file']['count']) { ?>
        <div class="view-files">
            <h3>첨부파일</h3>
            <?php for ($i=0; $i<$view['file']['count']; $i++) { ?>
                <a href="<?php echo $view['file'][$i]['href'] ?>" download>
                    <?php echo $view['file'][$i]['source'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                </a>
            <?php } ?>
        </div>
    <?php } ?>
    
    <!-- 버튼 -->
    <div class="view-buttons">
        <a href="<?php echo $list_href ?>" class="btn">목록</a>
        <?php if ($update_href) { ?>
            <a href="<?php echo $update_href ?>" class="btn">수정</a>
        <?php } ?>
        <?php if ($delete_href) { ?>
            <a href="<?php echo $delete_href ?>" class="btn">삭제</a>
        <?php } ?>
        <?php if ($copy_href) { ?>
            <a href="<?php echo $copy_href ?>" class="btn">복사</a>
        <?php } ?>
        <?php if ($reply_href) { ?>
            <a href="<?php echo $reply_href ?>" class="btn">답변</a>
        <?php } ?>
        <?php if ($write_href) { ?>
            <a href="<?php echo $write_href ?>" class="btn">글쓰기</a>
        <?php } ?>
    </div>
</article>

<!-- 댓글 -->
<?php include_once(G5_BBS_PATH.'/view_comment.php'); ?>
```

#### 3. write.skin.php (쓰기 페이지)

```php
<?php
if (!defined('_GNUBOARD_')) exit;

// CSS 추가
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- 게시글 쓰기 -->
<form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    
    <div class="write-form">
        <!-- 제목 -->
        <div class="form-group">
            <label for="wr_subject">제목</label>
            <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="form-control" maxlength="255">
        </div>
        
        <!-- 내용 -->
        <div class="form-group">
            <label for="wr_content">내용</label>
            <?php echo $editor_html; // 에디터 HTML ?>
        </div>
        
        <!-- 링크 -->
        <div class="form-group">
            <label for="wr_link1">링크 1</label>
            <input type="text" name="wr_link1" value="<?php echo $write['wr_link1'] ?>" id="wr_link1" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="wr_link2">링크 2</label>
            <input type="text" name="wr_link2" value="<?php echo $write['wr_link2'] ?>" id="wr_link2" class="form-control">
        </div>
        
        <!-- 파일첨부 -->
        <div class="form-group">
            <label>파일첨부</label>
            <?php for ($i=0; $i<$board['bo_upload_count']; $i++) { ?>
                <input type="file" name="bf_file[]" class="form-control">
                <?php if ($w == 'u' && $file[$i]['file']) { ?>
                    <input type="checkbox" name="bf_file_del[<?php echo $i ?>]" value="1" id="bf_file_del<?php echo $i ?>">
                    <label for="bf_file_del<?php echo $i ?>">
                        <?php echo $file[$i]['source'].'('.$file[$i]['size'].')'; ?> 파일 삭제
                    </label>
                <?php } ?>
            <?php } ?>
        </div>
        
        <!-- 버튼 -->
        <div class="write-buttons">
            <button type="submit" class="btn btn-primary">작성완료</button>
            <button type="button" onclick="history.back();" class="btn">취소</button>
        </div>
    </div>
</form>

<script>
// 폼 유효성 검사
document.getElementById('fwrite').onsubmit = function() {
    if (!document.getElementById('wr_subject').value) {
        alert('제목을 입력하세요.');
        document.getElementById('wr_subject').focus();
        return false;
    }
    
    var content = document.getElementById('wr_content').value;
    if (!content || content == '<p><br></p>') {
        alert('내용을 입력하세요.');
        return false;
    }
    
    return true;
};
</script>
```

#### 4. write_update.tail.skin.php (쓰기 후처리)

```php
<?php
if (!defined("_GNUBOARD_")) exit;

// 댓글이 아닐 때만 처리
if (!$wr_comment) {
    
    // 글 작성 후 특정 작업 수행
    // 예: 포인트 추가, 알림 발송 등
    
    // 예시: 첫 게시글 작성 시 보너스 포인트
    if ($w == '' && !$is_admin) {
        $mb = sql_fetch("SELECT count(*) as cnt FROM {$write_table} WHERE mb_id = '{$member['mb_id']}'");
        if ($mb['cnt'] == 1) {
            // 첫 게시글 보너스 포인트
            insert_point($member['mb_id'], 1000, '첫 게시글 작성 보너스', $bo_table, $wr_id, '첫게시글');
        }
    }
    
    // 예시: 관리자에게 알림
    if ($board['bo_use_email'] && $w == '') {
        $admin_email = 'admin@example.com';
        $subject = '[새글알림] ' . $wr_subject;
        $content = "{$member['mb_name']}님이 {$board['bo_subject']} 게시판에 새 글을 작성했습니다.\n\n{$wr_content}";
        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $admin_email, $subject, $content, 1);
    }
}
?>
```

### 갤러리 게시판 스킨 제작

#### list.skin.php (갤러리 목록)

```php
<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$list = run_replace('wv_hook_board_list', $list, $board);

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 갤러리 설정
$cols_width = number_format(100/$board['bo_gallery_cols'], 3);
$board_img_ratio = sprintf('%0.2f', $board['bo_gallery_height']/$board['bo_gallery_width']);
$board_img_ratio_mobile = sprintf('%0.2f', $board['bo_mobile_gallery_height']/$board['bo_mobile_gallery_width']);
?>

<style>
    .col-item .ratio { --bs-aspect-ratio: <?php echo $board_img_ratio_mobile*100; ?>% }
    @media (min-width: 992px) {
        .col-item { width: <?php echo $cols_width ?>% }
        .col-item .ratio { --bs-aspect-ratio: <?php echo $board_img_ratio*100; ?>% }
    }
</style>

<div id="bo_gall">
    
    <!-- 카테고리 -->
    <?php if ($is_category) { ?>
        <div class="mb-3">
            <?php echo wv_make_menu_display($bo_table, 'common/scroll', explode('|', $board['bo_category_list']), 'sca'); ?>
        </div>
    <?php } ?>
    
    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        
        <!-- 갤러리 목록 -->
        <div class="row" style="--bs-gutter-x: 3em; --bs-gutter-y: 3em">
            <?php for ($i=0; $i<count($list); $i++) { 
                $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);
                
                // 썸네일 이미지
                $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);
                
                if ($thumb['src']) {
                    $img_src = $thumb['src'];
                } else {
                    // YouTube 썸네일 체크
                    $yt_thumb = wv_get_youtube_thumb($list[$i]['wr_link1']);
                    if ($yt_thumb) {
                        $img_src = $yt_thumb;
                    } else {
                        $img_src = G5_IMG_URL . '/no_img.png';
                    }
                }
                
                $img_src = run_replace('thumb_image_tag', $img_src, $thumb);
                ?>
                
                <div class="col-item col-6 col-lg-auto">
                    <?php if ($is_checkbox) { ?>
                        <div class="form-check mb-2">
                            <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="form-check-input">
                            <label class="form-check-label" for="chk_wr_id_<?php echo $i ?>">선택</label>
                        </div>
                    <?php } ?>
                    
                    <!-- 이미지 -->
                    <div class="gallery-item">
                        <a href="<?php echo $list[$i]['href'] ?>">
                            <div class="ratio" style="background-image: url('<?php echo $img_src ?>'); background-size: cover; background-position: center;"></div>
                        </a>
                        
                        <!-- 제목 -->
                        <div class="item-info">
                            <a href="<?php echo $list[$i]['href'] ?>" class="item-title">
                                <?php echo $list[$i]['subject'] ?>
                                <?php if ($list[$i]['comment_cnt']) { ?>
                                    <span class="comment-cnt">[<?php echo $list[$i]['comment_cnt'] ?>]</span>
                                <?php } ?>
                            </a>
                            
                            <div class="item-meta">
                                <span><?php echo $list[$i]['name'] ?></span>
                                <span><?php echo $list[$i]['datetime2'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            
            <?php if (count($list) == 0) { ?>
                <div class="col-12 text-center py-5">
                    게시물이 없습니다.
                </div>
            <?php } ?>
        </div>
        
        <!-- 페이징 -->
        <div class="pagination mt-4">
            <?php echo $write_pages ?>
        </div>
        
        <!-- 버튼 -->
        <div class="board-buttons">
            <?php if ($is_checkbox) { ?>
                <button type="button" onclick="document.pressed='선택삭제'; form_submit(this.form);">선택삭제</button>
            <?php } ?>
            <?php if ($write_href) { ?>
                <a href="<?php echo $write_href ?>" class="btn btn-primary">글쓰기</a>
            <?php } ?>
        </div>
    </form>
</div>
```

---

## 🔗 Symlink 시스템

Gnu Skin은 Symlink를 사용하여 Weaver 테마 내의 스킨을 그누보드5 기본 스킨 경로로 연결합니다.

### Symlink 생성 방법

```php
// 기본 사용법
wv('gnu_skin')->add_symlink(
    $org_path,      // 원본 스킨 경로
    $skin_gubun,    // 스킨 종류 (board, member 등)
    $skin_dir,      // 스킨 디렉토리명
    $device,        // 디바이스 (pc/mobile)
    $file_name      // 파일명 (선택사항)
);

// 예시: 게시판 스킨 연결
wv('gnu_skin')->add_symlink(
    G5_PLUGIN_PATH . '/my_plugin/skin/board/custom',  // 원본 경로
    'board',                                           // 게시판 스킨
    'custom',                                          // 스킨명
    'pc'                                               // PC용
);

// 예시: 소셜 로그인 스킨 연결
wv('gnu_skin')->use_social_skin();
```

### Symlink가 생성되는 위치

```
plugins/gnu_skin/theme/weaver/pc/{스킨종류}/{스킨명}/
→ Symlink → 원본 스킨 경로
```

---

## 💡 실전 예시

### 예시 1: 커스텀 게시판 스킨 만들기

**1단계: 스킨 디렉토리 생성**

```
plugins/gnu_skin/theme/weaver/pc/board/my_custom/
├── list.skin.php
├── view.skin.php
├── write.skin.php
└── style.css
```

**2단계: list.skin.php 작성**

```php
<?php
if (!defined('_GNUBOARD_')) exit;
$list = run_replace('wv_hook_board_list', $list, $board);
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<div id="bo_list" class="my-custom-board">
    <!-- 커스텀 디자인 구현 -->
    <div class="custom-header">
        <h2><?php echo $board['bo_subject'] ?></h2>
    </div>
    
    <div class="custom-list">
        <?php for ($i=0; $i<count($list); $i++) { 
            $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);
            ?>
            <div class="custom-item">
                <a href="<?php echo $list[$i]['href'] ?>">
                    <?php echo $list[$i]['subject'] ?>
                </a>
                <span><?php echo $list[$i]['name'] ?></span>
                <span><?php echo $list[$i]['datetime2'] ?></span>
            </div>
        <?php } ?>
    </div>
</div>
```

**3단계: 게시판에 적용**

```sql
UPDATE g5_board SET bo_skin = 'weaver/my_custom' WHERE bo_table = 'notice';
```

### 예시 2: 최신글 위젯 스킨 제작

**1단계: 스킨 생성**

```
plugins/gnu_skin/theme/weaver/pc/latest/card_style/
├── latest.skin.php
└── style.css
```

**2단계: latest.skin.php 작성**

```php
<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$list = run_replace('wv_hook_board_list', $list, $board);
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
?>

<div id="<?php echo $skin_id ?>" class="latest-card-style">
    <style>
        <?php echo $skin_selector ?> {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        <?php echo $skin_selector ?> .card-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        <?php echo $skin_selector ?> .card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
    
    <?php for ($i=0; $i<count($list); $i++) { 
        $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);
        
        $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], 250, 150, false, true);
        $img = $thumb['src'] ?: G5_IMG_URL . '/no_img.png';
        ?>
        
        <div class="card-item">
            <a href="<?php echo $list[$i]['href'] ?>">
                <img src="<?php echo $img ?>" alt="<?php echo $list[$i]['subject'] ?>">
                <div class="card-body">
                    <h3><?php echo $list[$i]['subject'] ?></h3>
                    <p><?php echo $list[$i]['wr_content'] ?></p>
                    <span><?php echo $list[$i]['datetime2'] ?></span>
                </div>
            </a>
        </div>
    <?php } ?>
</div>
```

**3단계: 사용**

```php
// 레이아웃이나 페이지에서
echo latest('weaver/card_style', 'notice', 6, 23);
```

### 예시 3: 다른 플러그인에서 Symlink로 스킨 추가

**1단계: 다른 플러그인에서 스킨 추가**

```php
// plugins/my_plugin/MyPlugin.php
class MyPlugin extends Plugin {
    
    protected function __construct() {
        // 게시판 스킨 추가
        wv('gnu_skin')->add_symlink(
            $this->plugin_path . '/skin/board/my_board',
            'board',
            'my_board',
            'pc'
        );
        
        // 최신글 스킨 추가
        wv('gnu_skin')->add_symlink(
            $this->plugin_path . '/skin/latest/my_latest',
            'latest',
            'my_latest',
            'pc'
        );
    }
}
```

**2단계: 스킨 파일 생성**

```
plugins/my_plugin/skin/
├── board/
│   └── my_board/
│       ├── list.skin.php
│       └── view.skin.php
└── latest/
    └── my_latest/
        └── latest.skin.php
```

**3단계: 자동 연결 확인**

```
plugins/gnu_skin/theme/weaver/pc/board/my_board/ (Symlink)
→ plugins/my_plugin/skin/board/my_board/

plugins/gnu_skin/theme/weaver/pc/latest/my_latest/ (Symlink)
→ plugins/my_plugin/skin/latest/my_latest/
```

---

## ❓ 문제 해결

### 1. 스킨이 적용되지 않을 때

**원인**: 스킨 경로 설정 오류

**해결**:

```php
// 1. 스킨 경로 확인
$skin_path = wv('gnu_skin')->get_skin_path('pc', 'board', 'basic', '/list.skin.php');
echo $skin_path;  // 경로가 올바른지 확인

// 2. 파일 존재 확인
if (!file_exists($skin_path)) {
    echo '스킨 파일이 없습니다: ' . $skin_path;
}

// 3. 설정 확인
var_dump(wv('gnu_skin')->gnu_skin_use_array);
var_dump(wv('gnu_skin')->gnu_skin_dir_array);
```

### 2. Symlink 생성 실패

**원인**: 권한 문제 또는 이미 존재하는 파일

**해결**:

```bash
# 1. 권한 확인
ls -la plugins/gnu_skin/theme/weaver/pc/

# 2. 기존 Symlink 삭제
rm plugins/gnu_skin/theme/weaver/pc/board/custom

# 3. 다시 생성
```

```php
// 코드에서 재생성
wv('gnu_skin')->add_symlink($org_path, 'board', 'custom', 'pc');
```

### 3. 스킨 변수가 없다고 나올 때

**원인**: 스킨 파일에서 필수 변수 누락

**해결**:

```php
// 스킨 파일 상단에 추가
<?php
if (!defined('_GNUBOARD_')) exit;

// 게시판 스킨의 경우
$list = run_replace('wv_hook_board_list', $list, $board);

// 뷰 스킨의 경우
$view = run_replace('wv_hook_board_view', $view, $board);

// 최신글 스킨의 경우
$list = run_replace('wv_hook_board_list', $list, $board);
?>
```

### 4. CSS가 적용되지 않을 때

**원인**: CSS 파일 경로 오류

**해결**:

```php
// 올바른 CSS 추가 방법
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 또는
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);

// 또는
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
```

### 5. 모바일 스킨이 적용되지 않을 때

**원인**: 모바일 스킨 설정 누락

**해결**:

```php
// 1. 모바일 스킨 설정
wv('gnu_skin')->set_use_skin('member', '', 'basic', 'mobile');
wv('gnu_skin')->set_use_skin('board', 'list', 'basic', 'mobile');

// 2. DB 확인
UPDATE g5_config SET cf_mobile_member_skin = 'weaver/basic';
UPDATE g5_board SET bo_mobile_skin = 'weaver/basic' WHERE bo_table = 'notice';

// 3. 모바일 스킨 파일 생성
// plugins/gnu_skin/theme/weaver/mobile/board/basic/list.skin.php
```

---

## 📚 참고: 사용 가능한 변수들

### 게시판 list.skin.php

```php
// 게시판 정보
$board          // 게시판 설정 배열
$bo_table       // 게시판 테이블명
$board_skin_url // 스킨 URL

// 목록 정보
$list           // 게시글 목록 배열
$total_count    // 전체 게시글 수
$page           // 현재 페이지
$total_page     // 전체 페이지 수
$write_pages    // 페이징 HTML

// 권한 및 옵션
$is_checkbox    // 체크박스 사용 여부
$is_admin       // 관리자 여부
$is_category    // 카테고리 사용 여부
$is_good        // 추천 사용 여부
$is_nogood      // 비추천 사용 여부

// 검색
$sfl            // 검색 필드
$stx            // 검색어
$sca            // 카테고리
$sst            // 정렬 필드
$sod            // 정렬 방향

// URL
$write_href     // 글쓰기 URL
$list_href      // 목록 URL

// 각 $list[$i] 항목
$list[$i]['num']          // 번호
$list[$i]['subject']      // 제목
$list[$i]['comment_cnt']  // 댓글 수
$list[$i]['name']         // 작성자
$list[$i]['datetime']     // 작성일시
$list[$i]['datetime2']    // 작성일시 (짧은 형식)
$list[$i]['wr_hit']       // 조회수
$list[$i]['href']         // 글 보기 URL
$list[$i]['wr_id']        // 글 ID
```

### 게시판 view.skin.php

```php
// 게시판 정보
$board          // 게시판 설정
$bo_table       // 게시판 테이블명

// 글 정보
$view           // 글 정보 배열
$wr_id          // 글 ID

// 글 내용
$view['subject']     // 제목
$view['content']     // 내용
$view['name']        // 작성자
$view['datetime']    // 작성일시
$view['wr_hit']      // 조회수
$view['wr_good']     // 추천 수
$view['wr_nogood']   // 비추천 수

// 파일
$view['file']        // 첨부파일 배열
$view['file']['count']                 // 파일 개수
$view['file'][$i]['source']            // 원본 파일명
$view['file'][$i]['file']              // 저장된 파일명
$view['file'][$i]['size']              // 파일 크기
$view['file'][$i]['href']              // 다운로드 URL

// URL
$list_href      // 목록 URL
$write_href     // 글쓰기 URL
$update_href    // 수정 URL
$delete_href    // 삭제 URL
$copy_href      // 복사 URL
$reply_href     // 답변 URL
$prev_href      // 이전글 URL
$next_href      // 다음글 URL
```

### 최신글 latest.skin.php

```php
// 최신글 정보
$list           // 최신글 목록 배열
$board          // 게시판 정보
$bo_table       // 게시판 테이블명

// 스킨 정보
$skin_id        // 스킨 ID
$skin_selector  // CSS 선택자
$latest_skin_url // 스킨 URL

// 각 $list[$i] 항목
$list[$i]['subject']      // 제목
$list[$i]['comment_cnt']  // 댓글 수
$list[$i]['name']         // 작성자
$list[$i]['datetime']     // 작성일시
$list[$i]['datetime2']    // 작성일시 (짧은 형식)
$list[$i]['href']         // 글 보기 URL
$list[$i]['wr_id']        // 글 ID
$list[$i]['wr_content']   // 내용
```

---

## 🎯 핵심 정리

### 1. **Gnu Skin의 역할**
- 그누보드5 기본 스킨 시스템을 Weaver에서 사용
- 게시판, 회원, 검색, FAQ 등 모든 스킨 지원
- Symlink로 자동 연결

### 2. **스킨 파일 규칙**
- 그누보드5와 동일한 파일명 사용 (`list.skin.php` 등)
- 필수 후킹: `run_replace('wv_hook_board_list', $list, $board)`
- 스킨 경로: `theme/weaver/pc/{스킨종류}/{스킨명}/`

### 3. **스킨 적용 방법**
```php
// 환경설정에서: weaver/basic
// 코드에서: wv('gnu_skin')->set_use_skin()
// DB에서: UPDATE g5_config SET cf_member_skin = 'weaver/basic'
```

### 4. **다른 플러그인에서 스킨 추가**
```php
wv('gnu_skin')->add_symlink($org_path, $skin_gubun, $skin_dir, $device);
```

### 5. **스킨 제작 핵심**
- 그누보드5 기본 변수 사용 (`$list`, `$view`, `$board` 등)
- `add_stylesheet()`로 CSS 추가
- `run_replace()`로 후킹 처리

---

## 📚 다음 단계

1. **실제 스킨 커스터마이징 시작**
2. **다양한 게시판 스킨 제작**
3. **최신글 위젯 스킨 만들기**
4. **회원 관련 스킨 커스터마이징**

---

**완성! 🎉**

이제 Gnu Skin 플러그인을 완벽히 이해하고 활용할 수 있습니다. 그누보드5의 강력한 스킨 시스템을 Weaver에서 자유롭게 사용하세요!