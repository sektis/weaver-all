<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> .nav-pills .nav-link{background:#fff;color:#97989C; ;border:1px solid #efefef;height: var(--wv-40);display: flex;justify-content: center;align-items: center}
        <?php echo $skin_selector?> .nav-pills .nav-link.active{background:#000;color:#fff}
        <?php echo $skin_selector?> .time-row{display:flex;align-items:center;gap:8px;margin-bottom:12px;}
        <?php echo $skin_selector?> .time-label{min-width:50px;font-weight:600;color: #97989c;;font-size: var(--wv-14)}
        <?php echo $skin_selector?> .form-select{border:1px solid #ddd;padding:4px 8px;border-radius:4px;}
        <?php echo $skin_selector?> .tab-content{padding:20px 0;}
        <?php echo $skin_selector?> .time-section{background:#fff; ; :6px; ;}
        <?php echo $skin_selector?> .time-section h6{font-size: var(--wv-14);font-weight: 600}
        <?php echo $skin_selector?> .time-section .time-row{margin-top: var(--wv-6 )}
        <?php echo $skin_selector?> .day-item{padding: var(--wv-16) 0}
        <?php echo $skin_selector?> .day-item span{font-size: var(--wv-14);font-weight: 600}
        <?php echo $skin_selector?> .day-header{display:flex;align-items:center;gap:12px;margin-bottom:8px;}
        <?php echo $skin_selector?> .day-times{display:none;}
        <?php echo $skin_selector?> .day-times.show{display:block;margin-top: var(--wv-16)}
        <?php echo $skin_selector?> select{height: var(--wv-48);padding: var(--wv-13) var(--wv-12) !important;background-color: #f9f9f9;border: 0!important;font-size: var(--wv-16);font-weight: 500}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {
        <?php echo $skin_selector?> .time-row{flex-wrap:wrap;}
        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full  wv-ps-col"  >
        <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="made" value="member">
            <input type="hidden" name="made" value="member">
            <?php if ($is_list_item_mode) { ?>
                <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
            <?php } ?>
            <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
            <div class="hstack wv-ps-list flex-wrap" style="gap: var(--wv-8)">
                <?php foreach ($row[$column] as $k => $v) {?>
                    <div class="wv-flex-box wv-ps-each align-items-center" style="padding: var(--wv-4) var(--wv-6) var(--wv-4) var(--wv-10);background-color: #f9f9f9">
                        <!-- 필수 hidden -->
                        <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][id]"  value="<?php echo $v['id']; ?>">
                        <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][date]"  value="<?php echo $v['date']; ?>">
                        <p class="fs-[12/12/-0.48/600/#0D171B]"><?php echo $v['text']; ?></p>



                            <label class="lh-0  " style="cursor: pointer">
                                <input type="checkbox" class="d-none delete-each" name="<?php echo $field_name; ?>[<?php echo $k; ?>][delete]" value="1">
                                <img src="<?php echo $this->manager->plugin_url; ?>/img/button_close.png" class="w-[12px]" alt="">
                            </label>

                    </div>
                <?php  } ?>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function(){

            var $skin = $("<?php echo $skin_selector?>");

            $(".delete-each",$skin).click(function () {
                wv_ajax('<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php', {ajax:{reload:false}},wv_form_to_json_simple($("form",$skin)))
            })
        });
    </script>
</div>