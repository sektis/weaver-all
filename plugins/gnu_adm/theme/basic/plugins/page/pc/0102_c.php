<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$page_title='일반 사용자 관리';



$row = array(
    'mb_id'    => '',
    'mb_name'  => '',
    'mb_email' => '',
    'mb_level' => '2'
);

if($w=='u'){
    $q_mb_id = sql_escape_string($_GET['mb_id']);
    $row_db = sql_fetch(" SELECT * FROM {$g5['member_table']} WHERE mb_id = '{$q_mb_id}' LIMIT 1 ");
    if($row_db){
        $row = array_merge($row, $row_db);
        if(!$row['mb_level']) $row['mb_level'] = '3';
    }
}

$action_url = wv_page_url('0102_u');
$back_url   = wv_page_url('0102');
?>

<div class="wv-vstack">
    <div class="hstack justify-content-between">
        <p class="fw-600">(사장님) <?php echo $w=='u' ? '수정' : '등록'; ?></p>
    </div>

    <div class="content-inner-wrapper">
        <form name="fmember" id="fmember" method="post" action="<?php echo $action_url; ?>" autocomplete="off" class="wv-form demo-form">
            <input type="hidden" name="w" value="<?php echo $w; ?>">
            <input type="hidden" name="mb_level" value="3"><?php /* 레벨은 hidden만 사용 */ ?>
            <?php if($w=='u'){ ?>
                <input type="hidden" name="pk_mb_id" value="<?php echo get_text($row['mb_id']); ?>">
            <?php } ?>

            <!-- 아이디 -->
            <div class="form-floating position-relative mb-3">
                <input type="text" name="mb_id" id="mb_id"
                       value="<?php echo get_text($row['mb_id']); ?>"
                       class="form-control"
                    <?php echo $w=='u' ? 'readonly' : 'required'; ?>
                       maxlength="20" placeholder="아이디" autocomplete="new-password">
                <label for="mb_id" class="floatingInput">아이디</label>
            </div>

            <!-- 비밀번호 / 확인 (생성시 필수, 수정시 변경 시에만 입력) -->
            <div class="form-floating position-relative mb-3">
                <input type="password" name="mb_password" id="mb_password"
                       class="form-control"
                    <?php echo $w=='u' ? '' : 'required'; ?>
                       placeholder="비밀번호" autocomplete="new-password">
                <label for="mb_password" class="floatingInput"><?php echo $is_update ? '비밀번호(변경 시 입력)' : '비밀번호'; ?></label>
            </div>

            <div class="form-floating position-relative mb-3">
                <input type="password" name="mb_password_re" id="mb_password_re"
                       class="form-control"
                    <?php echo $w=='u' ? '' : 'required'; ?>
                       placeholder="비밀번호 확인" autocomplete="new-password">
                <label for="mb_password_re" class="floatingInput"><?php echo $is_update ? '비밀번호 확인(변경 시 입력)' : '비밀번호 확인'; ?></label>
            </div>

            <!-- 이름 -->
            <div class="form-floating position-relative mb-3">
                <input type="text" name="mb_name" id="mb_name"
                       value="<?php echo get_text($row['mb_name']); ?>"
                       class="form-control" required placeholder="대표자이름">
                <label for="mb_name" class="floatingInput">대표자이름</label>
            </div>

            <!-- 이메일 -->
            <div class="form-floating position-relative mb-3">
                <input type="email" name="mb_email" id="mb_email"
                       value="<?php echo get_text($row['mb_email']); ?>"
                       class="form-control" required placeholder="이메일">
                <label for="mb_email" class="floatingInput">이메일</label>
            </div>

            <!-- 버튼 -->
            <div class="page-top-menu">
                <a href="<?php echo $back_url; ?>" class="top-menu-btn bg-[#BBBFCF]">목록</a>
                <button type="submit" class="top-menu-btn bg-[#FC5555]"><?php echo $w=='u' ? '수정 저장' : '등록'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    (function(){
        var f = document.getElementById('fmember');
        if(!f) return;
        f.addEventListener('submit', function(e){
            var pw = document.getElementById('mb_password').value;
            var pr = document.getElementById('mb_password_re').value;
            if(pw.length || pr.length){
                if(pw !== pr){
                    alert('비밀번호와 비밀번호 확인이 일치하지 않습니다.');
                    e.preventDefault();
                    return false;
                }
            }
        }, false);
    })();
</script>
