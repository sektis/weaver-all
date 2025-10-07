<?php
uasort($row['menu'], function($a, $b) {
    return  $b['is_main'] -$a['is_main'];
});

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .grid-2{ display:grid; grid-template-columns: 1fr 260px; gap: var(--wv-12); }
        <?php echo $skin_selector?> .grid-1{ display:grid; grid-template-columns: 1fr; gap: var(--wv-10); }
        <?php echo $skin_selector?> .subtle{ color:#64748b; font-size: var(--wv-12); }


        /* 이미지 리스트 전용 */
        <?php echo $skin_selector?> .wv-ps-list.images{ display:flex; flex-wrap:wrap; gap: var(--wv-10); }


        /* 일반 행(메뉴/가격) 삭제 버튼 모양 - 이미지용 클래스 사용 안 함 */
        <?php echo $skin_selector?> .wv-ps-actions .btn-del{border:1px solid #ef4444; color:#ef4444; background:#fff;padding: var(--wv-4) var(--wv-8); border-radius: var(--wv-6); font-size: var(--wv-12); cursor:pointer;}
        <?php echo $skin_selector?> .wv-ps-actions .btn-del.active{ background:#ef4444; color:#fff; }

        @media (max-width: 640px){
        <?php echo $skin_selector?> .grid-2{ grid-template-columns: 1fr; }
        }
    </style>

    <div class="position-relative col w-full wv-ps-col">
        <!-- 최상위: 메뉴 리스트 -->
        <div class="wv-ps-list  vstack" style="row-gap: var(--wv-50)">

            <?php $n = 1; foreach ($row['menu'] as $k => $v) {

                ?>
                <div class="wv-ps-each  ">
                    <!-- 필수 hidden -->
                    <?php echo $this->store->menu->render_part('id','form',array('menu_id'=>$v['id'])); ?>

                    <div class="vstack border p-[16px]" style="row-gap: var(--wv-15)">



                            <?php echo $this->store->menu->render_part('is_main','form',array('menu_id'=>$v['id'])); ?>
                            <?php echo $this->store->menu->render_part('name','form',array('menu_id'=>$v['id'])); ?>
                            <?php echo $this->store->menu->render_part('desc','form',array('menu_id'=>$v['id'])); ?>
                            <?php echo $this->store->menu->render_part('prices','form',array('menu_id'=>$v['id'])); ?>
                            <?php echo $this->store->menu->render_part('images','form',array('menu_id'=>$v['id'])); ?>




                        <!-- 가격 옵션 -->




                        <div class=" wv-ps-box ">
                            <label class=" ">
                                <input type="checkbox" class="d-none" name="menu[<?php echo $k; ?>][delete]" value="1">
                                <span class=" ">삭제</span>
                            </label>
                        </div>
                    </div>
                </div>
                <?php $n++; } ?>

            <div class="wv-ps-new col-auto btn btn-dark"><i class="fa-solid fa-plus me-2"></i> 메뉴 추가</div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");




        });
    </script>
</div>
