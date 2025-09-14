<div class="form-floating position-relative mb-3">
    <input type="password" name="mb_password" id="mb_password"
           class="form-control wv-password-toggle"
        <?php echo $row['mb_id'] ? '' : 'required'; ?>
           placeholder="비밀번호" autocomplete="new-password">
    <label for="mb_password" class="floatingInput"><?php echo $row['mb_id'] ? '비밀번호(변경 시 입력)' : '비밀번호'; ?></label>
</div>

