<?php
$cont =  $row['contract'][$contract_key];
?>
<div>
    <div class="hstack" style="gap:var(--wv-6)">
        <p><?php echo $cont['status_html'] ?></p>
        <p class="fs-[14/22/-0.56/500/] wv-flex-box border" style="height:var(--wv-31);padding:0 var(--wv-10);color:#09F;border-radius:var(--wv-4);"><?php echo $cont['end']?"D-".wv_get_days_since($cont['end']):''; ?></p>

    </div>
    <div class="mt-[18px]">
        <?php
        $post_data=array(
            'wr_id'=>$row['wr_id']
        );
        $post_data['contract'][$contract_key]['status']=$this->status_change_value_array[$cont['status']];
        $post_data['contract'][$contract_key]['id']=$cont['id'];
        ?>
        <a class="fs-[14/17/-0.56/600/] hstack justify-content-center w-full cursor-pointer gap-[6px]"
              style="height:var(--wv-40);padding:0 var(--wv-10);color:#fff;border-radius:var(--wv-4);<?php echo $this->status_change_style_array[$cont['status']]?>"
           href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=sub01_01&action=update&part=contract&fields=cont_form' data-wv-ajax-option="target:<?php echo $target; ?>"
        data-wv-ajax-data='<?php echo json_encode($post_data); ?>'>

            <?php echo  $this->status_change_icon_array[$cont['status']]; ?>
            <span><?php echo $this->status_change_text_array[$cont['status']]; ?></span>

        </a>
    </div>
</div>