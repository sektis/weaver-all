<?php
$result = wv()->store_manager->made('contract_item')->get_list(array( 'where_contractitem'=>array('is_free'=>'!=1'), 'order_by'   => 'w.wr_id asc',));
$rows = $result['list'];

?>
<input type="hidden" name="contract[<?php echo $contract_id; ?>][contractitem_wr_id]" value="<?php echo $list[$contract_id]['contractitem_wr_id']; ?>">
<div id="<?php echo $skin_id; ?>">
    <select  id="contract-item-change" class="form-select  "  >

        <?php foreach (array_filter($result['list']) as $k=>$v){
            echo option_selected($v['wr_id'],$row['contractitem_wr_id'],$v['contractitem']['name']);
        } ?>
    </select>
    <label for="contract-item-change" class="visually-hidden">계약 상품 유형 선택</label>

</div>

<script>
    $(document).ready(function () {

        var $skin = $("<?php echo $skin_selector?>");


        var ajax_data = <?php echo json_encode($ajax_data)?>;

        var key_map = <?php echo json_encode(array_flip(array_column($list,'contractitem_wr_id','id')))?>;

        $("#contract-item-change", $skin).change(function () {
            ajax_data['contract_id']=key_map[$(this).val()];


            wv_ajax('<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?action=update',
                'replace_with:<?php echo $form_selector?>',ajax_data)
        })
    })
</script>