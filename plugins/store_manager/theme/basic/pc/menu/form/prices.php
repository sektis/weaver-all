<?php
$this->make_array($row['prices']);

?>
<div id="<?php echo $skin_id; ?>">
    <style>
        <?php echo $skin_selector?> {}
        }
    </style>
    <div class="wv-ps-col">
        <div class="wv-ps-list  vstack" style="row-gap: var(--wv-20)">
            <?php foreach ($row['prices'] as $k => $v) {

                $demo_class = !$v ? 'wv-ps-demo' : '';

                ?>
                <div class="wv-ps-each w-full <?php echo $demo_class; ?>">
                    <!-- 필수 hidden -->
                    <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][id]"  value="<?php echo $v['id']; ?>">
                    <div class="hstack justify-content-between" style="gap:var(--wv-8)">

                            <div class="col">
                                <input type="text" class="form-control h-[48px] bg-[#f9f9f9] border-0" id="<?php echo $field_name; ?>[<?php echo $k; ?>][name]" name="<?php echo $field_name; ?>[<?php echo $k; ?>][name]" maxlength="20" placeholder="ex) 1~2인분" value="<?php echo htmlspecialchars($v['name'], ENT_QUOTES); ?>">
                                <label for="<?php echo $field_name; ?>[<?php echo $k; ?>][name]" class="visually-hidden">옵션명 (예: 1~2인분)</label>
                            </div>
                            <div class="col ">
                                <div class="position-relative">
                                <input type="text" class="form-control h-[48px] bg-[#f9f9f9] border-0 wv-only-number" id="<?php echo $field_name; ?>[<?php echo $k; ?>][name]" name="<?php echo $field_name; ?>[<?php echo $k; ?>][name]" maxlength="20" placeholder="가격" value="<?php echo htmlspecialchars($v['price'], ENT_QUOTES); ?>">
                                <label for="<?php echo $field_name; ?>[<?php echo $k; ?>][name]" class="visually-hidden">가격</label>
                                <span class="pe-none position-absolute top-50 translate-middle-y fs-[12/17/-0.48/500/#CFCFCF]" style="right:var(--wv-8)">원</span>
                                </div>
                            </div>


                        <div class="wv-ps-box ms-3 col-auto">
                            <label class="h-100">
                                <input type="checkbox" class="d-none" name="menu[<?php echo $k; ?>][prices][<?php echo $pk; ?>][delete]" value="1">
                                <span class=" ">삭제</span>
                            </label>
                        </div>
                    </div>
                </div>
            <?php  } ?>


            <a href="#"  class="wv-ps-new wv-flex-box border h-[40px]">
                <i class="fa-solid fa-plus"></i>
                <p class="fs-[14/17/-0.56/500/#0D171B]">가격 추가</p>
            </a>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");





        });
    </script>
</div>