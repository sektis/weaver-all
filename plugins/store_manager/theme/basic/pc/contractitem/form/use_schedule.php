<div class="form-check form-switch <?php echo $row[$column]?'':'disabled'; ?>" style="gap:var(--wv-6)" data-on-value="이용가능시간 설정함" data-off-value="이용가능시간 설정안함">
    <label class="form-check-label w-[142px]" for="<?php echo $field_name; ?>">

    </label>
    <input class="form-check-input"
           type="checkbox"
           role="switch"
           name="<?php echo $field_name; ?>"
           value="1"
        <?php echo $row[$column]?'checked':''; ?>
           id="<?php echo $field_name; ?>">

</div>


