<div id="<?php echo $skin_id; ?>">
    <div  >
        <textarea class="form-control h-[48px] bg-[#f9f9f9] border-0 js-desc" 
                  id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" placeholder="ex) 메뉴 구성, 주요 재료 및 원산지, 맛의 특징 등" maxlength="100" style="min-height: var(--wv-140);"><?php echo htmlspecialchars($row['desc'], ENT_QUOTES); ?></textarea>
        <label for="<?php echo $field_name; ?>" class="visually-hidden">메뉴 부가설명 (최대 100자)</label>
    </div>
    <div class="form-text subtle mb-2 text-end"><span class="js-desc-count">0</span>/100자</div>

<script>
    $(document).ready(function(){
        var $skin = $("<?php echo $skin_selector?>");

        // 설명 100자 카운터
        $skin.find('.js-desc').each(function(){ 
            var $ta = $(this);
            var $cnt = $skin.find('.js-desc-count');
            var upd = function(){ $cnt.text(($ta.val()||'').length); };
            $ta.on('input', upd); upd();
        });
        $skin.on('input','.js-desc',function(){

            var $ta = $(this);
            var $cnt = $skin.find('.js-desc-count');

            $cnt.text(($ta.val()||'').length)

        });




    });
</script>
</div>