
<?php
$result = wv()->store_manager->made('store_category')->get_list(array( 'where_storecategory'=>array('use'=>'=1'), 'order_by'   => 'w.wr_id asc',));
$list = $result['list'];

?>
<div>
    <div class="form-floating">
        <select name="store[category_wr_id]" id="contract[0][category_wr_id]" class="form-select required" required>
            <option value="">선택하세요.</option>
            <?php foreach ($list as $k=>$v){
                echo option_selected($v['wr_id'],$row['category_wr_id'],$v['storecategory']['name']);
            } ?>
        </select>
        <label for="store[category_wr_id]" class="floatingSelect">카테고리 선택</label>
    </div>
</div>