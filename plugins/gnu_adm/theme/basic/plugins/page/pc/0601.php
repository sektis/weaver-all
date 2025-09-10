<?php
$page_title = '계약 및 상품 설정';


?>

<div class="wv-vstack">


    <p class="fs-[16/22/-0.64/600/#0D171B] ff-Pretendard">설정 목록</p>

    <div class="content-inner-wrapper">
        <div class="hstack justify-content-between">
            <p class="fs-[18/25/-0.72/600/#0D171B] ff-Pretendard">계약 담당자 관리</p>
            <a href="#" class="top-menu-btn" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=wv_cont_manager&action=render_part&part=contractmanager&fields=manager_id_form'
               data-wv-ajax-options="end,backdrop-static" data-wv-ajax-class="w-[436px]"><i class="fa-solid fa-plus"></i> 추가하기</a>
        </div>
        <div style="background-color: #efefef;height: 1px" class="wv-mx-fit mt-[16px]"></div>
        <?php  
        $result = wv()->store_manager->made('wv_cont_manager')->get_list();
        $list = $result['list'];
        ?>
    </div>
</div>


<script>
    $(document).ready(function (){

    })
</script>
