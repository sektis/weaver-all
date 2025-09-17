<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .modal{--bs-modal-width:100%;--bs-modal-padding:0}
        <?php echo $skin_selector?> .modal-content{--bs-modal-bg:transparent;--bs-modal-border-width:0}
        <?php echo $skin_selector?> .content-wrap{border-radius: var(--wv-20);background-color: #fff;padding: var(--wv-49) var(--wv-51); }
        <?php echo $skin_selector?> .login-from-wrap{min-height: revert!important; }

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {
        <?php echo $skin_selector?> .content-wrap{width: 90% }
        }
    </style>
    <a href="<?php echo G5_BBS_URL ?>/login.php" class="hstack  modal-login-btn" style="gap:var(--wv-9_35)">
        <span>Log in</span>
        <svg class="w-[16px]" viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <rect width="16" height="22" fill="url(#pattern0_1_600)"/>
            <defs>
                <pattern id="pattern0_1_600" patternContentUnits="objectBoundingBox" width="1" height="1">
                    <use xlink:href="#image0_1_600" transform="scale(0.0625 0.0454545)"/>
                </pattern>
                <image id="image0_1_600" width="16" height="22" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAWCAMAAAD+dOxOAAAAq1BMVEUAAAAjHyAkJCQjHyAgICAjHiAiICAjHx8jHiAjHyAjHyAjHyAjHyAiHyAjICAbGxsjHyAcHBwkHyAjICAiHiAjHyAnHBwjHyAjHiAjHx8kHiEjICEjHyAkICAjICAgICAkJCQkHiEnJyciHSIzMzMkHx8mGiYjHx8jHx8gICAjIyMiHiAjHyAjHyAlHh4qKiojHyEjHCMlICAjHyAiIiIjICAjHyAjHiEkHyCZF/m4AAAAOXRSTlMA/w7AEKBwi7D93sbZ9VIK8hLPrH+YGvjJm1aH+4FgKAddDTUFahRBgggWd+C3IgaMJDflD5DMbZ7wx8iXAAAAoklEQVR4nGWO5xaCMAxGEwGl7D1kuDfu+f5PJqUWrN4f+Zp7TtIApRcYefxYzJumZlSRfZAlqC9Zf9OuF5qWKTFRkBl7THDYpObzWUOldYNPLlKDVhlLLu5OXaIUPfWDlhdH0E1daklICWTHByh+Btj/FsqAi8gVhUXMsyBOyK5sxcGpbHGHp/wsfbHoxN+3nYhDV27Z6msIUWAFYPc6pmN4A14pCb671pI1AAAAAElFTkSuQmCC"/>
            </defs>
        </svg>

    </a>
    <div class="modal fade" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">


                <div class="modal-body"  >
                    <div class="d-flex-center">
                        <div class="content-wrap d-flex-center position-relative">
                            <div>
                                <div class="text-end">
                                    <a href="" class=" text-white " data-bs-dismiss="modal"><img src="<?php echo $wv_skin_url?>/icon_close.png" class="w-[20px]"  ></a>
                                </div>
                                <div class="content-inner px-[99px] md:px-[0px] md:mt-[48px]">

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            var $modal = $('.modal',$skin);
            var $btn = $('.modal-login-btn',$skin);

            $(document).on('click',"<?php echo $skin_selector?> .modal-login-btn",function (e) {
                e.preventDefault();
                var $this = $(this);

                $.post($this.attr('href'),{ no_layout:true},function (data){

                    $(".content-inner",$modal).html(data)
                    $modal.modal('show')

                },'html')
            })
            // $("form[name='flogin']",$skin).ajaxForm();
            $($skin).on('submit',"form[name='flogin']",function (e) {
                e.preventDefault();
                $(this).ajaxSubmit({
                    success:function () {
                        location.reload()
                    }
                });
                return false;
            })


        })

    </script>
</div>




