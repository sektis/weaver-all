<?php
$page_title = '계약 및 상품 설정';


?>

<div class="wv-vstack">


    <p class="fs-[16/22/-0.64/600/#0D171B] ff-Pretendard">설정 목록</p>

    <div class="content-inner-wrapper">
        <div class="hstack justify-content-between">
            <p class="fs-[18/25/-0.72/600/#0D171B] ff-Pretendard">계약 담당자 관리</p>
            <a href="#" class="top-menu-btn" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=member&action=render_part_form&part=member&fields=manager_form'
               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"   ><i class="fa-solid fa-plus"></i> 추가하기</a>
        </div>
        <div style="background-color: #efefef;height: 1px" class="wv-mx-fit mt-[16px]"></div>
       <div class="pt-[16px]">
           <?php
           $result = wv()->store_manager->made('member')->get_list(array('where_member'=>array('is_manager'=>"=1"),  'order_by'   => 'w.wr_id asc',));
           $list = $result['list'];

           ?>
           <table class="table table-borderless wv-table align-middle w-[500px]">
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
                                   <p class="fs-[14/17/-0.56/500/#97989C]"><?php echo $list[$i]['mb_mb_name']; ?> (<?php echo $list[$i]['mb_id']; ?>)</p>
                                   <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=member&action=delete&wr_id=<?php echo $list[$i]['wr_id']; ?>'
                                      class="wv-data-list-delete-btn ms-auto">[삭제]</a>
                                   <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                                      data-wv-ajax-data='{ "action":"form","made":"member","part":"member","field":"admin/manager_form","wr_id":"<?php echo $list[$i]['wr_id']; ?>"}'
                                      data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"   class="wv-data-list-edit-btn">[수정]</a>
                               </div>

                       </td>

                   </tr>
               <?php } ?>

               </tbody>
           </table>
           <?php if($i==0){?>
               <div><p>등록 된 데이터가 없습니다.</p></div>
           <?php }; ?>
       </div>
    </div>

    <div class="content-inner-wrapper">
        <div class="hstack justify-content-between">
            <p class="fs-[18/25/-0.72/600/#0D171B] ff-Pretendard">계약 상품 관리</p>
            <a href="#" class="top-menu-btn"
               data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
               data-wv-ajax-data='{"action":"form","made":"contract_item","part":"contractitem","field":"admin/item_form","wr_id":""}'
               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]" ><i class="fa-solid fa-plus"></i> 추가하기</a>
        </div>
        <div style="background-color: #efefef;height: 1px" class="wv-mx-fit mt-[16px]"></div>
        <div class="pt-[16px]">
            <?php
            $result = wv()->store_manager->made('contract_item')->get_list(array( 'order_by'   => 'w.wr_id asc',));
            $list = $result['list'];

            ?>
            <table class="table table-borderless wv-table align-middle w-[500px]">
                <caption class="visually-hidden"><?php echo $board['bo_subject'] ?> 목록</caption>

                <tbody class="text-center">
                <?php
                for ($i = 0; $i < count($list); $i++) {
//                        dd($list);
                    ?>
                    <tr class="h-[40px]">
                        <td>상품 <?php echo sprintf('%02d',$i+1); ?></td>
                        <td>

                            <div class="hstack border" style="gap:var(--wv-6);height: var(--wv-39);border-radius: var(--wv-4);padding: 0 var(--wv-16)">
                                <p class="fs-[14/17/-0.56/500/#97989C]"><?php echo $list[$i]['contractitem']['name']; ?></p>
                                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=contract_item&action=delete&wr_id=<?php echo $list[$i]['wr_id']; ?>'
                                   class="wv-data-list-delete-btn ms-auto">[삭제]</a>
                                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                                   data-wv-ajax-data='{"action":"form","made":"contract_item","part":"contractitem","field":"admin/item_form","wr_id":"<?php echo $list[$i]['wr_id']; ?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"  class="wv-data-list-edit-btn">[수정]</a>
                            </div>

                        </td>

                    </tr>
                <?php } ?>

                </tbody>
            </table>
            <?php if($i==0){?>
                <div><p>등록 된 데이터가 없습니다.</p></div>
            <?php }; ?>
        </div>
    </div>

    <div class="content-inner-wrapper">
        <div class="hstack justify-content-between">
            <p class="fs-[18/25/-0.72/600/#0D171B] ff-Pretendard">업종 카테고리</p>
            <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
               data-wv-ajax-data='{"action":"form","made":"store_category","part":"storecategory","field":"admin/item_form","wr_id":""}'
               data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"

               class="top-menu-btn" ><i class="fa-solid fa-plus"></i> 추가하기</a>
        </div>
        <div style="background-color: #efefef;height: 1px" class="wv-mx-fit mt-[16px]"></div>
        <div class="pt-[16px]">
            <?php
            $result = wv()->store_manager->made('store_category')->get_list(array(  'order_by'   => 'w.wr_id asc',));
            $list = $result['list'];

            ?>
            <table class="table table-borderless wv-table align-middle w-[500px]">
                <caption class="visually-hidden"><?php echo $board['bo_subject'] ?> 목록</caption>

                <tbody class="text-center">
                <?php
                for ($i = 0; $i < count($list); $i++) {
//                        dd($list);
                    ?>
                    <tr class="h-[40px]">
                        <td>카테고리 <?php echo sprintf('%02d',$i+1); ?></td>
                        <td>

                            <div class="hstack border" style="gap:var(--wv-6);height: var(--wv-39);border-radius: var(--wv-4);padding: 0 var(--wv-16)">
                                <?php if($list[$i]['storecategory']['icon']['path']){ ?>
                                    <div class="w-[30px]"><img src="<?php echo $list[$i]['storecategory']['icon_main']['path']; ?>" alt="" class="wh-100 object-fit-cover"></div>
                                <?php } ?>
                                <p class="fs-[14/17/-0.56/500/#97989C]"><?php echo $list[$i]['storecategory']['name']; ?></p>

                                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=store_category&action=delete&wr_id=<?php echo $list[$i]['wr_id']; ?>'
                                   class="wv-data-list-delete-btn ms-auto">[삭제]</a>
                                <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url?>/ajax.php'
                                   data-wv-ajax-data='{"action":"form","made":"store_category","part":"storecategory","field":"admin/item_form","wr_id":"<?php echo $list[$i]['wr_id']; ?>"}'
                                   data-wv-ajax-option="offcanvas,end,backdrop,class: w-[436px]"  class="wv-data-list-edit-btn">[수정]</a>
                            </div>

                        </td>

                    </tr>
                <?php } ?>

                </tbody>
            </table>
            <?php if($i==0){?>
                <div><p>등록 된 데이터가 없습니다.</p></div>
            <?php }; ?>
        </div>
    </div>
</div>


<script>
    $(document).ready(function (){

    })
</script>
