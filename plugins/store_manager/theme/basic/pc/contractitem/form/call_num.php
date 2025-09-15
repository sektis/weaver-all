<div>
    <div class="form-floating position-relative" style="z-index: 10">
        <input type="text" name="<?php echo $field_name; ?>"   value="<?php echo $row['call_num'] ?>" id="<?php echo $field_name; ?>"   class="form-control wv-only-number"  minlength="1" placeholder="미입력시 문의하기 버튼 비활성화(숫자만 입력)">
        <label for="<?php echo $field_name; ?>" class="floatingInput">문의전화번호</label>
    </div>
</div>