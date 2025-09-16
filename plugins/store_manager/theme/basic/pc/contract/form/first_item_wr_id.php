<?php
$result = wv()->store_manager->made('contract_item')->get_list(array( 'where_contractitem'=>array('is_free'=>'!=1'), 'order_by'   => 'w.wr_id asc',));
$list = $result['list'];

?>
<div>
    <div class="form-floating">
        <select name="contract[0][contractitem_wr_id]" id="contract[0][contractitem_wr_id]" class="form-select required" required>
            <option value="">선택하세요.</option>
            <?php foreach ($list as $k=>$v){
                echo option_selected($v['wr_id'],'',$v['contractitem']['name']);
            } ?>
        </select>
        <label for="contract[0][contractitem_wr_id]" class="floatingSelect">계약 상품 유형 선택</label>
    </div>
</div>