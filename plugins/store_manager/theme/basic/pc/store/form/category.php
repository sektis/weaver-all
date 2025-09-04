<div>
    <div class="form-floating">
        <select name="store[category]" id="store[category]" class="form-select  "  >
            <option value="">선택하세요.</option>
            <?php foreach ($this->category_text as $key=>$val){
                echo option_selected($key,$row['category'],$val);
            } ?>
        </select>
        <label for="store[category]" class="floatingSelect">매장 카테고리</label>
    </div>
</div>