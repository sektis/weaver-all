<?php
$result = wv()->store_manager->made('member')->get_list(array( 'where_member'=>array('is_manager'=>'=1'), 'order_by'   => 'w.wr_id asc',));


; ?>
<div>
    <div class="form-floating">
        <select name=<?php echo $field_name; ?> id="<?php echo $field_name; ?>" class="form-select required" required>
            <option value="">선택하세요.</option>
            <?php foreach ($result['list'] as $k=>$v){
                echo option_selected($v['wr_id'],$row[$column],$v['mb_mb_name']);
            } ?>
        </select>
        <label for="<?php echo $field_name; ?>" class="floatingSelect">계약담당자 선택</label>
    </div>
</div>