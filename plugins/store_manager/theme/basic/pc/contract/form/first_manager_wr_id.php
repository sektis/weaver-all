<?php
$result = wv()->store_manager->made('member')->get_list(array( 'where_member'=>array('is_manager'=>'=1'), 'order_by'   => 'w.wr_id asc',));
$list = $result['list'];

; ?>
<div>
    <div class="form-floating">
        <select name="contract[0][contractmanager_wr_id]" id="contract[0][contractmanager_wr_id]" class="form-select required" required>
            <option value="">선택하세요.</option>
            <?php foreach ($list as $k=>$v){
                echo option_selected($v['wr_id'],'',$v['jm_mb_name']);
            } ?>
        </select>
        <label for="contract[0][contractmanager_wr_id]" class="floatingSelect">계약담당자 선택</label>
    </div>
</div>