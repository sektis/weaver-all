<?php
$this->make_array($row['tel_add']);

?>
<div class="wv-ps-col">
    <div class="wv-ps-list  vstack" style="row-gap: var(--wv-10)">
        <?php foreach ($row['tel_add'] as $k => $v) {


            ?>
            <div class="wv-ps-each w-full  ">
                <!-- 필수 hidden -->
                <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][id]"  value="<?php echo $v['id']; ?>">
                <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $k; ?>][date]"  value="<?php echo $v['date']; ?>">
                <div class="d-flex justify-content-between  ">
                    <?php if($v['id']){ ?>


                        <p class="fs-[14/17/-0.56/600/#0D171B] col"><span class="pe-2">·</span> <?php echo $v['text']; ?></p>

                    <?php }else{ ?>


                        <input
                                type="text"
                                class="form-control col"
                                id="<?php echo $field_name; ?>[<?php echo $k; ?>][text]"
                                name="<?php echo $field_name; ?>[<?php echo $k; ?>][text]"
                                maxlength="20"
                                placeholder="추가 전화번호를 입력하세요."
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
            <p class="fs-[14/17/-0.56/500/#0D171B]">전화번호 추가</p>
        </a>
    </div>
</div>