<div id="<?php echo $skin_id; ?>">
    <div  >
        <textarea class="form-control h-[48px] bg-[#f9f9f9] border-0 js-desc" data-wv-max-length="50"
                  id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" placeholder="ex) 3인 이상 방문 시 '1인분 추가 제공'  "   style="min-height: var(--wv-140);"><?php echo get_text($row['desc']); ?></textarea>
        <label for="<?php echo $field_name; ?>" class="visually-hidden">메뉴 부가설명 (최대 50자)</label>

    </div>


<script>
    $(document).ready(function(){
        var $skin = $("<?php echo $skin_selector?>");

        // 설명 100자 카운터





    });
</script>
</div>