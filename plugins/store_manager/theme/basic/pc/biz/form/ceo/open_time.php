<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5;
$vars['form_selector']=$skin_selector;

?>
<style>
    <?php echo $skin_selector?> {}
</style>
<div class="vstack h-100 " id="<?php echo $skin_id; ?>">
    <div class="wv-offcanvas-header col-auto">
        <div class=" ">
            <div class="row align-items-center g-0"  >


                <div class="col">
                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/vec3.png" class="w-[28px]" alt=""></div>

                </div>

                <div class="col-auto text-center">
                    <p class="fs-[14/20/-0.56/600/#0D171B]">영업시간</p>
                </div>
                <div class="col"></div>
            </div>
        </div>

    </div>
    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>
    <div class="wv-offcanvas-body col"   >


        <div class=" h-100 vstack">
            <?php echo $this->store->biz->render_part('open_time','form');  ?>

        </div>

    </div>

    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
        <?php if($row['id']) {?>
            <?php
            $post_data=array(
                'wr_id'=>$row['wr_id'],
            );
            $post_data['contract'][$contract_id]['delete']=1;
            $post_data['contract'][$contract_id]['id']=$row['id'];
            ?>
            <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php'
               class="w-[125px] h-[46px] px-[19px] py-[10px] flex justify-center items-center gap-[4px] rounded-[4px] border-[1px] border-solid border-[#FC5555] fs-[14/20/-0.56/600/#FC5555] wv-data-list-delete-btn  "
               data-wv-ajax-data='{
                                               "made":"sub01_01",
                                               "part":"contract",
                                               "action":"update"
                                               }'
               data-wv-ajax-option='ajax_option:{"reload":"true"}'
               data-wv-ajax-data-add='<?php echo json_encode($post_data); ?>'>계약 해지</a>
        <?php } ?>
        <button type="submit" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">완료</button>
    </div>
</div>