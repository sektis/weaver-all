<div>
    <div class="form-floating">

        <select name="store[category]" id="store[category]" class="form-select  "  >
            <option value="">선택하세요.</option>
            <?php foreach ($this->category_arr as $key=>$val){
                echo option_selected($key,$row['category'],$val);
            } ?>
        </select>
        <label for="store[category]" class="floatingSelect">매장 카테고리</label>
    </div>
</div>

<?php
$result = wv()->store_manager->made('store_category')->get_list(array( 'where_storecategory'=>array('use'=>'=1'), 'order_by'   => 'w.wr_id asc',));
$list = $result['list'];

?>
<div>
    <div class="form-floating">
        <select name="store[category_wr_id]" id="contract[0][category_wr_id]" class="form-select required" required>
            <option value="">선택하세요.</option>
            <?php foreach ($list as $k=>$v){
                echo option_selected($v['wr_id'],'',$v['storecategory']['name']);
            } ?>
        </select>
        <label for="store[category_wr_id]" class="floatingSelect">카테고리 선택</label>
    </div>
</div>