<?php
$skin_data = array(

    'open_trigger'=>$skin_selector,
    'ev_trigger_name'=>"member-select-{$skin_id}"
);
echo wv_widget('member_search', $skin_data);
?>
<div id="<?php echo $skin_id; ?>">
    <div class="form-floating">
        <input type="hidden" name="mb_id" class="selected-mb-id" value="">
        <input type="text" name="" value=""    class="form-control selected-mb-name "  minlength="1" placeholder="클릭하세요.">
        <label for="mb_id" class="floatingSelect">대표관리자선택</label>
    </div>
    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            $(document).on('<?php echo $skin_data['ev_trigger_name']?>', function(event, data) {


                $(".selected-mb-id",$skin).val(data.mb_id)
                $(".selected-mb-name",$skin).val(data.mb_name)
            });
        })

    </script>
</div>