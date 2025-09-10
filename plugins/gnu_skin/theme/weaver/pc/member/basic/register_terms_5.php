<?php
include_once '_common.php';
global $g5;


?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="flogin" action="<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/register_step2.php" method="post" class="h-100">
                <div class="vstack h-100 ">
                    <div class="wv-offcanvas-header col-auto">
                        <div class="row align-items-center">
                            <div class="col">
                                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-arrow-left-long"></i></button>
                            </div>
                            <div class="col-auto"><p class="wv-offcanvas-title">이벤트 참여 및 혜택 제공</p></div>
                            <div class="col"></div>
                        </div>
                    </div>

                    <div class="wv-offcanvas-body col vstack">

                        dsadsadsa
                    </div>


                </div>


            </form>
        </div>
    </div>

    <script>

        $(document).ready(function () {

            var $skin = $("<?php echo $skin_selector?>");

        })

    </script>
</div>