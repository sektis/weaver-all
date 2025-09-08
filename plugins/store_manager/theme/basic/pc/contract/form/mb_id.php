<div>
    <div class="form-floating">
        <select name="mb_id" id="mb_id" class="form-select required" required>
            <option value="">선택하세요.</option>
            <?php echo $this->get_member_options('mb_level>=3',$row['mb_id']); ?>
        </select>
        <label for="mb_id" class="floatingSelect">계약담당자</label>
    </div>
</div>