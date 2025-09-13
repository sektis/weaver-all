<?php
$page_title = '계약 및 상품 설정';


?>

<div class="wv-vstack">


    <p class="fs-[16/22/-0.64/600/#0D171B] ff-Pretendard">설정 목록</p>

    <div class="content-inner-wrapper">
        <div class="hstack justify-content-between">
            <p class="fs-[18/25/-0.72/600/#0D171B] ff-Pretendard">계약 담당자 관리</p>
            <a href="#" class="top-menu-btn" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=member&action=render_part&part=member&fields=manager_form'
               data-wv-ajax-options="end,backdrop" data-wv-ajax-class="w-[436px]"><i class="fa-solid fa-plus"></i> 추가하기</a>
        </div>
        <div style="background-color: #efefef;height: 1px" class="wv-mx-fit mt-[16px]"></div>
       <div class="pt-[16px]">
           <?php
           $result = wv()->store_manager->made('member')->get_list(array('where_member'=>array('is_manager'=>"=1")));
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
                       <td>담당자 <?php echo sprintf('%02d',$i+1); ?></td>
                       <td>

                               <div class="hstack border" style="gap:var(--wv-6);height: var(--wv-39);border-radius: var(--wv-4);padding: 0 var(--wv-16)">
                                   <p class="fs-[14/17/-0.56/500/#97989C]"><?php echo $list[$i]['jm_mb_name']; ?> (<?php echo $list[$i]['mb_id']; ?>)</p>
                                   <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=member&action=delete&wr_id=<?php echo $list[$i]['wr_id']; ?>'
                                      data-wv-ajax-type="none" data-wv-ajax-target="" class="fs-[14/22/-0.56/500/#FC5555] ms-auto">[삭제]</a>
                                   <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=member&action=render_part&part=member&fields=manager_form&wr_id=<?php echo $list[$i]['wr_id']; ?>'
                                      data-wv-ajax-options="end,backdrop" data-wv-ajax-class="w-[436px]" class="fs-[14/22/-0.56/500/#3F51B5]">[수정]</a>
                               </div>

                       </td>

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
