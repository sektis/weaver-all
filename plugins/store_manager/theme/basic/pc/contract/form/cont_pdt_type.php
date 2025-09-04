<div>
    <div class="form-floating">
        <select name="contract[cont_pdt_type]" id="contract[cont_pdt_type]" class="form-select required" required>
            <option value="">선택하세요.</option>
            <?php foreach ($this->cont_pdt_type_text as $key=>$val){
                echo option_selected($key,$row['cont_pdt_type'],$val);
            } ?>
        </select>
        <label for="mb_id" class="floatingSelect">계약 상품 유형 선택</label>
    </div>
</div>