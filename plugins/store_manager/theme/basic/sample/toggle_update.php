<?php
if (!defined('_GNUBOARD_')) exit;

global $member;

?>

<div id="<?php echo $skin_id; ?>" class="wv-part-skin position-relative h-100 d-flex-center flex-nowrap bg-white" <?php echo wv_display_reload_data($reload_ajax_data);; ?>>
    <style>
        <?php echo $skin_selector; ?> { display:inline-block; cursor:pointer; position: relative; z-index:99; }
        <?php echo $skin_selector; ?> [data-on-value] { transition: all 0.2s ease;cursor: pointer }
        <?php echo $skin_selector; ?>:hover [data-on-value] { transform: scale(1.1); }
    </style>

    <?php
    $ajax_data['action']='update_render';
    if ($row['id']) { // 삭제
        $ajax_data['favorite'][$row['id']]['delete']=1;
    }
    ?>

    <button
            type="button"
            class="btn btn-link p-0 border-0 favorite-toggle-btn"
            data-wv-ajax-url="<?php echo wv()->store_manager->plugin_url . '/ajax.php'; ?>"
            data-wv-ajax-data='<?php echo json_encode($ajax_data); ?>'
            data-wv-ajax-option='replace_with:<?php echo $skin_selector?>'
    >

        <label class=" "
               data-on-value='<img src="<?php echo wv()->store_manager->plugin_url; ?>/img/heart_on.png" class="w-[18px]">'
               data-off-value='<img src="<?php echo wv()->store_manager->plugin_url; ?>/img/heart_off.png" class="w-[18px]">'>
            <input class="d-none view-toggle" type="checkbox" value="1" <?php echo $row['id'] ? 'checked' : ''; ?>>
            <span class="hstack" style="gap:var(--wv-5)"></span>
        </label>
    </button>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector; ?>");
        });
    </script>
</div>