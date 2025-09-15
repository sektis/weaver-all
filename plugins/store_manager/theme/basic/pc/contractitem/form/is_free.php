<div class="form-check form-switch <?php echo $row['is_free']?'':'disabled'; ?>" style="gap:var(--wv-6)" data-on-value="프리플랜" data-off-value="구독플랜">
    <label class="form-check-label" for="<?php echo $field_name; ?>">

    </label>
    <input class="form-check-input"
           type="checkbox"
           role="switch"
           name="<?php echo $field_name; ?>"
           value="1"
        <?php echo $row['is_free']?'checked':''; ?>
           id="<?php echo $field_name; ?>">

</div>


