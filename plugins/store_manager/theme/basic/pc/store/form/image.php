<?php
$this->make_array($row[$column]);
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"    >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .wv-ps-each{width: var(--wv-160);max-width:calc(50% - var(--wv-5));height: var(--wv-104);background-color: #f9f9f9;border-radius: var(--wv-4);overflow: hidden;border:1px solid #dcdcdc}
        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style> 
    
    <div class="position-relative col col-lg-auto w-full md:w-full " style="">

        <div class="wv-ps-col">
            <div class="wv-ps-list wv-ps-list-image" style="">
                <div class="wv-ps-each ">

                    <label   class="wh-100 cursor-pointer d-flex-center text-center position-relative ">
                        <input type="file" name="<?php echo $field_name; ?>[]"  multiple    accept="image/*" class="d-none"   data-max-count="<?php echo $this->image_max_count; ?>">
                        <div class="vstack h-100 justify-content-center" style="row-gap:var(--wv-6)">
                            <i class="fa-solid fa-plus fs-[30///700/] d-block"></i>
                            <p ><span class="fw-700 wv-ps-file-count"></span> / <span style="color:#97989c"><?php echo $this->image_max_count; ?></span></p>
    <!--                        <div class="wv-ps-multiple-list  position-absolute inset-0 d-flex-center " style=""  ></div>-->
                        </div>
                    </label>
                </div>
                <?php $i=1; foreach ($row['image']  as $k => $v){   ?>
                    <div class="wv-ps-each  " >
                        <img src="<?php echo $v['path'] ?>"  alt="" class="wh-100 object-fit-contain">
                        <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][id]"      value="<?php echo $v['id']; ?>"    >
                        <p class="position-absolute wv-ps-num"> </p>
                        <label class="position-absolute wv-ps-delete-label" style="">
                            <input type="checkbox" name="<?php echo $field_name; ?>[<?php echo $k; ?>][delete]" value="1" class="d-none"  >
                            <span></span>
                        </label>
                    </div>
                    <?php $i++;} ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function (){
            var $skin = $("<?php echo $skin_selector?>");
        })
    </script>
</div>