<?php
$page_title = '계약 및 상품 설정';


?>

<div class="wv-vstack">


    <p class="fs-[16/22/-0.64/600/#0D171B] ff-Pretendard">설정 목록</p>

    <div class="content-inner-wrapper">
        <div class="hstack justify-content-between">
            <p class="fs-[18/25/-0.72/600/#0D171B] ff-Pretendard">계약 담당자 관리</p>
            <a href="#" class="top-menu-btn" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=wv_cont_manager&action=render_part&part=contractmanager&fields=manager_id_form'
               data-wv-ajax-options="end,backdrop" data-wv-ajax-class="w-[436px]"><i class="fa-solid fa-plus"></i> 추가하기</a>
        </div>
        <div style="background-color: #efefef;height: 1px" class="wv-mx-fit mt-[16px]"></div>
       <div class="pt-[16px]">
           <?php
           $result = wv()->store_manager->made('wv_cont_manager')->get_list();
           $list = $result['list'];

           ?>
           <table class="table table-borderless wv-table border-top border-bottom align-middle w-[500px]">
               <caption class="visually-hidden"><?php echo $board['bo_subject'] ?> 목록</caption>

               <tbody class="text-center">
               <?php
               for ($i = 0; $i < count($list); $i++) {
//                        dd($list);
                   ?>
                   <tr class="h-[40px]">
                       <td><?php echo $list[$i]['mb_id']; ?></td>
                       <td><?php echo $list[$i]['jm_mb_name']; ?></td>
                       <td><a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=wv_cont_manager&action=render_part&part=contractmanager&fields=manager_id_form&wr_id=<?php echo $list[$i]['wr_id']; ?>'
                              data-wv-ajax-options="end,backdrop" data-wv-ajax-class="w-[436px]">수정</a></td>
                   </tr>
               <?php } ?>
               </tbody>
           </table>
       </div>
    </div>
</div>


<script>
    $(document).ready(function (){

    })
</script>
