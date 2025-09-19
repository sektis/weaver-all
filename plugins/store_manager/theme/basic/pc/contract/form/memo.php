<?php
$this->make_array($row['memo']);

?>
<div class="wv-ps-col">
    <div class="wv-ps-list  vstack" style="row-gap: var(--wv-10)">
        <?php foreach ($row['memo'] as $k => $v) {

            $demo_class = !$v ? 'wv-ps-demo' : '';

            ?>
            <div class="wv-ps-each w-full <?php echo $demo_class; ?>">
                <!-- 필수 hidden -->
                <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][id]"  value="<?php echo $v['id']; ?>">
                <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][date]"  value="<?php echo $v['date']; ?>">
                <div class="d-flex justify-content-between  ">
                <?php if($v['id']){ ?>


                        <p class="fs-[14/17/-0.56/600/#0D171B] col"><span class="pe-2">·</span> <?php echo $v['text']; ?></p>
                        <p class="fs-[11/17/-0.44/500/#97989C] col-auto"><?php echo date('Y.m.d',strtotime($v['date'])); ?></p>

                <?php }else{ ?>


                        <input
                                type="text"
                                class="form-control col"
                                id="<?php echo $field_name; ?>[<?php echo $k; ?>][text]"
                                name="<?php echo $field_name; ?>[<?php echo $k; ?>][text]"
                                maxlength="20"
                                placeholder="메모를 입력하세요."
                                value="<?php echo $v['text']; ?>">
                        <label for="<?php echo $field_name; ?>[<?php echo $k; ?>][text]" class="visually-hidden">메모</label>





                <?php } ?>
                    <div class=" wv-ps-box ms-3  col-auto ">
                        <label class="h-100 ">
                            <input type="checkbox" class="d-none" name="<?php echo $field_name; ?>[<?php echo $k; ?>][delete]" value="1">
                            <span>  </span>
                        </label>
                    </div>
                </div>
            </div>
        <?php  } ?>


        <a href="#"  class="wv-ps-new wv-flex-box border h-[39px]">
            <i class="fa-solid fa-plus"></i>
            <p class="fs-[14/17/-0.56/500/#0D171B]">새로운 메모</p>
        </a>
    </div>
</div>