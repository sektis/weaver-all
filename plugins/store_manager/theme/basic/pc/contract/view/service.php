<?php 
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap  " style=" ">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .col-box{padding: var(--wv-30) 0}

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full h-100 " style="">
        <div class="hstack " style="color:<?php echo $row['item']['color_type']['text']; ?>;gap:var(--wv-6)">
           <p class="fs-[10/11/-0.5/700/]" style="padding: var(--wv-4_5) var(--wv-4);background-color: <?php echo $row['item']['color_type']['bg']; ?>"><?php echo $row['item_name']; ?></p>
           <p class="fs-[12/17/-0.48/500/] "  ><?php echo $row['service_content']; ?></p>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");

            $("form", $skin).ajaxForm({

            })
        })
    </script>
</div>