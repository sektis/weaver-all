<div class="form-check form-switch align-self-center"  data-on-value="대표메뉴"  data-off-value="대표메뉴">
    <input class="form-check-input" type="checkbox" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" value="1" <?php echo $row['is_main'] ? 'checked' : ''; ?>>
    <label class="form-check-label" for="<?php echo $field_name; ?>"></label>
</div>