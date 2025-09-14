
<div class="form-floating position-relative mb-3">
    <input type="text" name="mb_id" id="mb_id"
           value="<?php echo get_text($row['mb_id']); ?>"
           class="form-control"
        <?php echo $row['mb_id'] ? 'readonly' : 'required'; ?>
           maxlength="20" placeholder="아이디" autocomplete="new-password">
    <label for="mb_id" class="floatingInput">아이디</label>
</div>
