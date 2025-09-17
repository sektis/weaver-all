<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}


        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div id="wv-modal-<?php echo $skin_id; ?>" class="modal wv-modal wv-modal-portal fade">
        <div class="modal-dialog  modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content  ">
                <div class="w-[500px] bg-white modal-content-ajax h-100 p-[15px]" style="border-radius: var(--wv-4)">

                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            var $modal = $("#wv-modal-<?php echo $skin_id?>");
            var $trigger = $("<?php echo $data['open_trigger']?>")
            $trigger.click(function () {
                $modal.modal('show');
                $.post('<?php echo wv_path_replace_url(dirname(__FILE__))?>/member_list.php',function (data) {
                    $(".modal-content-ajax",$modal).html(data)
                },'html')
            })

            $modal.on('click','.select-member',function () {
                var $this = $(this);
                var eventData = {
                    mb_id: $this.data('mb-id'),
                    mb_name: $this.data('mb-name'),
                };

                $(document).trigger('<?php echo $data['ev_trigger_name']; ?>', [eventData]);
                $modal.modal('hide')
            })

            $modal.on('click','.bo-list-paging-wrap a',function (e) {
                e.preventDefault()
                var $this = $(this);
                var href = $this.attr('href');
                if(!href)return false;
                $.post(href,function (data) {
                    $(".modal-content-ajax",$modal).html(data)
                },'html')
            })
            $modal.on('submit','form',function (e) {
                e.preventDefault()
                var $this = $(this);
                $this.ajaxSubmit({
                    success: function (data) {

                        $(".modal-content-ajax",$modal).html(data)
                    }
                })
                return false;
            })
        })

    </script>
</div>
