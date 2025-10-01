<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 d-flex-center flex-nowrap bg-white" data-wv-max-length="20" style="">
    <div>
        <input type="text" class="form-control h-[48px] bg-[#f9f9f9] border-0  js-menu-name" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" value="<?php echo $row['name']; ?>" placeholder="ex) 대패삼겹살 (300g), 돼지김치찌개" >
        <label for="<?php echo $field_name; ?>" class="visually-hidden">메뉴명 </label>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");

        });
    </script>
</div>