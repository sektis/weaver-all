<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $member;
$get_list_option = array(
    'where'=>"w.mb_id='{$member['mb_id']}'",

    'where_location' =>    array(
        'and'=>array(
            array('lat'=>"<>''"),
            array('lng'=>"<>''"),
        )
    ),
    'page'=>$page,
    'rows'=>20,
);
$result = wv()->store_manager->made('sub01_01')->get_list($get_list_option);

$rows = $result['list'];

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .select-store:not(.active) img{filter: invert(87%) sepia(1%) saturate(48%) hue-rotate(314deg) brightness(94%) contrast(95%);}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="background:#fff;border-radius: var(--wv-4) var(--wv-4) 0 0;;;">

        <div  class="vstack  " style="height: 90dvh; ">


            <div class="col-auto" >
                <div class=" hstack justify-content-between align-items-start" style="padding: var(--wv-16) var(--wv-17)">
                    <div>
                        <p class="fs-[16//-0.64/700/#0D171B]">관리 중인 매장</p>
                        <p class="fs-[12/17/-0.48/500/#97989C] mt-[4px]">관리가 필요한 매장을 선택해주세요</p>
                    </div>
                    <a href="" data-bs-dismiss="offcanvas"><img src="<?php echo $wv_skin_url; ?>/close.png" class="w-[28px]" alt=""></a>
                </div>
            </div>



            <div class="col mt-[12px] overflow-hidden container"  >
                <div class="vstack h-100" style="overflow-y: auto;row-gap: var(--wv-10)">
                <?php if(count($rows) > 0){ ?>
                    <?php for($i=0; $i<count($rows); $i++){  ;?>
                            <div class="inline-flex px-[16px] py-[12px] items-center gap-[64px] rounded-[10px] justify-content-between cursor-pointer select-store bg-[#f9f9f9]" data-wr-id="<?php echo $rows[$i]['wr_id']; ?>">
                                <div class="">
                                    <p class="fs-[11/15/-0.44/500/#97989C]"><?php echo $rows[$i]['store']['category_text']; ?></p>
                                    <p class="fs-[16/22/-0.64/600/#0D171B] mt-[4px]"><?php echo $rows[$i]['store']['name']; ?></p>
                                </div>
                                <div  >
                                    <img src="<?php echo $wv_skin_url; ?>/check.png" alt="" class="w-[24px]">
                                </div>
                            </div>

                    <?php } ?>
                <?php }else{ ?>
                    <div class="text-center py-5">자료가 없습니다.</div>
                <?php } ?>
                </div>
            </div>






        </div>
    </div>

    <script>
        $(function () {
            var $skin = $("<?php echo $skin_selector?>");
            $skin.on('click', '.select-store', function (e) {


                var $this = $(this);
               var wr_id =$this.data('wr-id');
               if(!wr_id){
                   alert('wr_id 오류');
               }
                $.ajax({
                    url: '<?php echo wv()->store_manager->ajax_url() ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'set_current_store',
                        wr_id: wr_id,
                    }
                })
                    .done(function (res) { alert('저장완료');location.reload() })
                    .fail(function () { alert('통신 오류가 발생했습니다.'); })

            });

         });
    </script>






</div>