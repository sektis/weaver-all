<?php
$result = wv()->store_manager->made('contract_item')->get_list(array( 'where_contractitem'=>array('is_free'=>'!=1'), 'order_by'   => 'w.wr_id asc',));

$key_map = array_column($list,'contractitem_wr_id','id');

?>
<div id="<?php echo $skin_id; ?>">
    <select  id="contract-item-change" class="form-select  " name="<?php echo $field_name; ?>" required >

        <?php $i=0; foreach (array_filter($result['list']) as $k=>$v){
            if(!$contract_id and in_array($v['wr_id'],$key_map))continue;
            echo option_selected($v['wr_id'],$row['contractitem_wr_id'],$v['contractitem']['name']);
            $i++;} ?>
        <?php if($i==0){ ?>
            <option value="">추가할수있는 계약이 없습니다.</option>
        <?php } ?>
    </select>
    <label for="contract-item-change" class="visually-hidden">계약 상품 유형 선택</label>

</div>

<script>
    $(document).ready(function () {

        var $skin = $("<?php echo $skin_selector?>");


        var ajax_data = <?php echo json_encode($ajax_data)?>;

        var key_map = <?php echo json_encode(array_flip($key_map))?>;

        $("#contract-item-change", $skin).change(function () {
            ajax_data['contract_id']=key_map[$(this).val()];


            wv_ajax('<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?action=render_part',
                'replace_with:<?php echo $form_selector?>',ajax_data)
        })
    })
</script>