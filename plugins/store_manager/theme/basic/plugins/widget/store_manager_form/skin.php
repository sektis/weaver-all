<?php
include_once '_common.php';
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .wv-offcanvas-header{padding: var(--wv-27) 0 var(--wv-7);font-size: var(--wv-18);font-weight: 700;}
        <?php echo $skin_selector?> .wv-offcanvas-body{padding: var(--wv-30) 0}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="flogin" action='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.ph' method="post" class="h-100" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">

                <?php echo $data['render_content']; ?>


            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");

        })
    </script>
</div>