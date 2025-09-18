<?php
//$ajax_data['contract_id'] 
?>
<input type="hidden" name="contract[<?php echo $contract_id; ?>][contractitem_wr_id]" value="<?php echo $list[$contract_id]['contractitem_wr_id']; ?>">
<div id="<?php echo $skin_id; ?>">
    <select  id="contract-item-change" class="form-select  "  >

        <?php foreach (array_filter($list) as $k=>$v){
            echo option_selected($v['id'],$contract_id['contractitem_wr_id'],$v['item_name']);
        } ?>
    </select>
    <label for="contract-item-change" class="visually-hidden">계약 상품 유형 선택</label>

</div>

<script>
    $(document).ready(function () {

        var $skin = $("<?php echo $skin_selector?>");


        var ajax_data = <?php echo json_encode($ajax_data)?>;



        $("#contract-item-change", $skin).change(function () {
            ajax_data['contract_id']=$(this).val();

            wv_ajax('<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?action=update',
                'replace_with:<?php echo $form_selector?>',ajax_data)
        })
    })
</script>