<div id="<?php echo $skin_id; ?>">
    <div>
        <input type="text"class="form-control h-[48px] bg-[#f9f9f9] border-0  js-menu-name" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" value="<?php echo $row['name']; ?>" placeholder="ex) 대패삼겹살 (300g), 돼지김치찌개" >
        <label for="<?php echo $field_name; ?>" class="visually-hidden">메뉴명 </label>
    </div>
    <div class="form-text subtle mb-2 text-end"><span class="js-menu-name-count">0</span>/20자</div>

<script>
    $(document).ready(function(){
        var $skin = $("<?php echo $skin_selector?>");



        // 메뉴명 특수문자 금지(한/영/숫/공백) & 20자

        $skin.on('input', '.js-menu-name', function(){
            var v = $(this).val();
            // SQL 인젝션 위험 문자와 !, @, # 제거
            var dangerousChars = /[!@#'"`;=<>|&%()[\]{}\\\/\*\-]+/g;
            var filtered = v.replace(dangerousChars, '').slice(0,20);
            if (v !== filtered) $(this).val(filtered);
            this.setCustomValidity(dangerousChars.test($(this).val()) ? '!, @, #과 SQL 관련 특수문자는 사용할 수 없습니다.' : '');
        });

        // 설명 100자 카운터
        $skin.find('.js-menu-name').each(function(){
            var $ta = $(this);
            var $cnt =$skin.find('.js-menu-name-count');
            var upd = function(){ $cnt.text(($ta.val()||'').length); };
            $ta.on('input', upd); upd();
        });
        $skin.on('input','.js-menu-name-count',function(){

            var $ta = $(this);
            var $cnt = $skin.find('.js-menu-name-count');

            $cnt.text(($ta.val()||'').length)

        });


    });
</script>
</div>