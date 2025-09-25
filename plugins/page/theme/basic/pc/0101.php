<div class="wv-mx-fit h-100">
<?php
$data = array(
    'view_list'=>$view_list,
    'q'=>$q,
    'category_wr_id'=>$category_wr_id,
);
echo wv_widget('store_list',$data);
?>

</div>