<?php
$this->make_array($row['images']);
?>
<div id="<?php echo $skin_id; ?>" class="container">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .wv-ps-each{ height: var(--wv-104);background-color: #f9f9f9;border-radius: var(--wv-4);overflow: hidden;border:1px solid #dcdcdc}
    </style>
    <div class="wv-ps-col">
        <div class="wv-ps-list row row-cols-2 wv-ps-list-image" style="">
            <div class="wv-ps-each col ">
                <label   class="wh-100 cursor-pointer d-flex-center text-center position-relative">
                    <input type="file" name="<?php echo $field_name; ?>[]" multiple accept="image/*" class="d-none" data-max-count="<?php echo $this->image_max_count; ?>">
                    <div class="vstack h-100 justify-content-center" style="row-gap:6px">
                        <i class="fa-solid fa-plus fs-[30///700/] d-block"></i>
                        <p class="m-0">
                            <span class="fw-700 wv-ps-file-count"><?php echo count(array_filter($v['images'])); ?></span>
                            /
                            <span style="color:#97989c"><?php echo $this->image_max_count; ?></span>
                        </p>
                    </div>
                </label>
            </div>
            <?php
            foreach ($row['images'] as $k => $v) {
                $demo_class = !$v ? 'wv-ps-demo' : '';
                ?>
                <div class="wv-ps-each col  ">

                    <img src="<?php echo htmlspecialchars($v['path'], ENT_QUOTES); ?>" alt="" class="wh-100 object-fit-contain">

                    <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][id]" value="<?php echo $v['id']; ?>">
                    <p class="position-absolute wv-ps-num"></p>
                    <label class="position-absolute wv-ps-delete-label">
                        <input type="checkbox" name="<?php echo $field_name; ?>[<?php echo $k; ?>][delete]" value="1" class="d-none">
                        <span></span>
                    </label>
                </div>
            <?php } ?>


        </div>
    </div>
    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");





        });
    </script>
</div>