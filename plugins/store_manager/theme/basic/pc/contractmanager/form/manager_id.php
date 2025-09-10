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