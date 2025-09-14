<select name="member[active]" id="member[active]" class="form-select required" required>
    <option value="">선택하세요.</option>
    <?php foreach ($this->active_arr as $key=>$val){
        echo option_selected($key,$row['active'],$val);
    } ?>
</select>
<label for="member[active]" class="  visually-hidden">계정상태</label>

