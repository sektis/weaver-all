<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin"  style="">
    <style>
        <?php echo $skin_selector; ?> {}
        <?php echo $skin_selector; ?> .input-wrap{position: relative}
        <?php echo $skin_selector; ?> .input-wrap:before{content:' ';position:absolute;width: 100%;height: 2px;background-color: #efefef;bottom:0;left:0;}
        <?php echo $skin_selector; ?> .input-wrap:has(.active):before{background-color: #0d171b}
    </style>

    <div class="input-wrap">
        <input type="text" class="form-control py-[6px] fs-[16/22/-0.64/600/#CFCFCF] wv-only-number  wv-input-text-reset border-0   py-[6px]" style="border:0;outline: none"    id="<?php echo $field_name; ?>" required name="<?php echo $field_name; ?>" value="<?php echo $row[$column]; ?>" placeholder="계좌번호" >
        <label for="<?php echo $field_name; ?>" class="visually-hidden">메뉴명 </label>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
            $("input",$skin).on("input", function() {
                $(this).toggleClass("active", $(this).val().trim() !== "");
            });
        });
    </script>
</div>