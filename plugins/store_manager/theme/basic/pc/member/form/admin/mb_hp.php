<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin"  style="">
    <style>
        <?php echo $skin_selector; ?> {}
    </style>

    <div class="form-floating position-relative mb-3">
        <input type="text" class="form-control   wv-only-number" id="<?php echo $field_name; ?>" required name="<?php echo $field_name; ?>" value="<?php echo $row[$column]; ?>" placeholder="연락처." >
        <label for="<?php echo $field_name; ?>" class="floatingInput">연락처 </label>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");

        });
    </script>
</div>