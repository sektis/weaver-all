<?php
include_once '_common.php';
global $g5;

?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .wv-offcanvas-header{padding: var(--wv-27) 0 var(--wv-7);font-size: var(--wv-18);font-weight: 700;}
        <?php echo $skin_selector?> .wv-offcanvas-body{padding: var(--wv-12) 0}
        <?php echo $skin_selector?> .wv-submit-btn:not(.active){pointer-events: none;user-select: none}
        <?php echo $skin_selector?> .wv-submit-btn.active{  background-color: #0d171b !important;
                                        color: #fff !important;}
        <?php echo $skin_selector?> .wv-submit-btn:hover{  background-color: #0d171b !important;
                                        color: #fff !important;}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">

                <div class="h-100" style="padding-top:var(--wv-10)">
                    <?php echo $data['render_content']; ?>
                </div>



        </div>
    </div>

    <script>
        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");



            // $("form",$skin).submit(function (e) {
            //     e.preventDefault()
            //
            //     console.log($("form",$skin).serializeArray())
            // })
            var replace_with = '<?php echo $data['replace_with']?>';
            var replace_field = '<?php echo $data['replace_field']?>';


            $("form", $skin).ajaxForm({
                dataType:'json',
                success: function (data) {

                    if(replace_with){
                        console.log(data)
                    }else{
                        if(data.result){
                            alert('완료');
                        }
                        // location.reload()
                    }



                }

            })
        })
    </script>
</div>