<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$store = wv()->store_manager->made('sub01_01')->get($wr_id);

//dd($store->contract->cont_pdt_type_text);
//$sss = $store->get_ext_row();
//dd(wv_base64_decode_unserialize($sss['store_image']));
?>
<style>
    .info-wrap{
        display: flex;
        padding: var(--wv-53);
        border-bottom: 2px solid #efefef;
        gap: var(--wv-138);
        align-items: start;
    }
    .info-wrap:last-child{
        border-bottom: 0;
    }
    .info-wrap:first-child{
        border-top: 2px solid #efefef;
    }
    .info-title{
        width: var(--wv-65);
        height: var(--wv-39);
        border:1px solid #efefef;
        border-radius: var(--wv-4);
        font-size: var(--wv-14);
        color:#0D171B;
        font-weight: 600;
        display: flex;
        justify-content: center;
        align-items: center;
       
    }
    .info-right{
        display: flex;
        flex-direction: column;
        align-self: stretch;
    }
    .info-right > *{
        width: auto;
    }
</style>
<div class="wv-vstack">
    <div class="hstack justify-content-between">
        <p class="fw-600">(매장) <?php echo $wr_id?'수정':'등록'; ?> 등록</p>
    </div>

    <div class="content-inner-wrapper">

        <form name="wv-data-form" id="wv-data-form"  method="post" enctype="multipart/form-data" class="" action="<?php echo wv_page_url('0201_u'); ?>">
            <input type="hidden" name="page" value="<?php echo $page ?>">

            <?php echo $store->basic->render_all('form');; ?>
            <div class="wv-vstack">


                 <?php echo $store->contract->render_all('form');; ?>


                <div class="mx-fit">
                    <div class="info-wrap ">
                        <div class="info-title">기본정보</div>
                        <div class="wv-vstack" style="--wv-vstack-row-gap: var(--wv-30)">
                            <?php echo $store->store->render_part(array('name','image','category'),'form');; ?>
                            <?php echo $store->location->render_part('address','form');; ?>
                            <?php echo $store->store->render_part(array('tel','notice'),'form');; ?>
                        </div>
                    </div>
                    <div class="info-wrap ">
                        <div class="info-title">영업정보</div>
                        <div class="wv-vstack" style="--wv-vstack-row-gap: var(--wv-8)">
                            <?php echo $store->biz->render_all('form'); ?>
                        </div>
                    </div>
                    <div class="info-wrap ">
                        <div class="info-title">메뉴정보</div>
                        <div class="wv-vstack" style="--wv-vstack-row-gap: var(--wv-8)">
                            <?php echo $store->menu->render_all('form');;; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-top-menu"  >
                <a href="<?php echo wv_page_url('0201'); ?>" class="top-menu-btn bg-[#BBBFCF]">취소</a>
                <button class="top-menu-btn bg-[#FC5555]" id="wv-data-form-submit"><?php echo $wr_id?'수정':'등록'; ?></button>
            </div>
        </form>
    </div>
</div>



<script>
    $(document).ready(function (){

    })
</script>
