<div class="wv-mx-fit h-100">
    <?php
    $data = array(
        'view_type'=>$view_type,
        'q'=>$q,
        'category_wr_id'=>$category_wr_id,
        'contractitem_wr_id'=>3,
        'limit_km'=>50
    );
    echo wv_widget('store_list',$data);
    ?>

</div>