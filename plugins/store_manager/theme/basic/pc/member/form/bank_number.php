<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin"  style="">
    <style>
        <?php echo $skin_selector; ?> {}
        <?php echo $skin_selector; ?> .input-wrap{position: relative;padding: var(--wv-6) 0}
        <?php echo $skin_selector; ?> .input-wrap:before{content:' ';position:absolute;width: 100%;height: 2px;background-color: #efefef;bottom:0;left:0;}
        <?php echo $skin_selector; ?> .input-wrap.active:before{background-color: #0d171b}
        <?php echo $skin_selector; ?> .input-wrap.active .no-select{display: none}
        <?php echo $skin_selector; ?> .input-wrap .yes-select{display: none}
        <?php echo $skin_selector; ?> .input-wrap.active .yes-select{display: block}
        <?php echo $skin_selector; ?> .bank-each{cursor: pointer}
    </style>

    <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $row[$column]; ?>" required>
    <div class="input-wrap <?php echo $row['bank_number']?'active':''; ?>">
        <div class="no-select">
            <div class="cursor-pointer  select-bank">
                <p class="fs-[16/22/-0.64/600/#CFCFCF]" style="padding: .375rem .75rem">은행</p>
            </div>
        </div>
        <div class="yes-select">
            <div class="cursor-pointer  select-bank hstack justify-content-between">
                <p class="fs-[16/22/-0.64/600/#0D171B] bank-name" style="padding: .375rem .75rem"><?php echo $this->store->member->bank_name; ?></p>
                <img src="<?php echo wv_store_manager_img_url(); ?>/chevron-under.png" class="w-[16px]" alt="">
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-bottom bg-white" tabindex="-1" id="part-skin-offcanvas"   style="height: auto;max-height: 50dvh;border-top-left-radius: var(--wv-4);border-top-right-radius: var(--wv-4);overflow: hidden">
        <div class="offcanvas-header justify-content-end">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body pb-[40px]">

            <div class="container">
                <p class="fs-[20/28/-0.8/600/#0D171B]">은행을 선택해주세요</p>
                <div class="row mt-[20px]">
                    <?php foreach ($this->bank_array as $key=>$val){ ?>
                        <div class="col-4 col-lg-auto">
                            <div class="w-md-full position-relative w-[103px] h-md-auto h-[100px] bank-each text-center pt-[5px]" style="border-radius: var(--wv-4);background-color: #f9f9f9" data-bank-key="<?php echo $key; ?>" data-bank-name="<?php echo $val; ?>">
                                <img src="<?php echo wv_store_manager_img_url(); ?>/bank/<?php echo $key; ?>.png" class="w-[70px] h-[70px] object-fit-cover" alt="">
                                <p class="fs-[14/20/-0.56/500/#0D171B] text-nowrap position-absolute start-50 translate-middle-x" style="bottom:var(--wv-12)"><?php echo $val; ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");



            var offcanvasElement = document.getElementById('part-skin-offcanvas');
            var offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
            $(".select-bank",$skin).click(function () {
                offcanvasInstance.show();
            })
            $(".bank-each",$skin).click(function () {
                var $this = $(this);
                var bank_key=$this.data('bank-key')
                var bank_name=$this.data('bank-name')
                $('input',$skin).val(bank_key).trigger('input');
                $(".input-wrap",$skin).addClass('active')
                $(".bank-name",$skin).html(bank_name)
                offcanvasInstance.hide();
            })
        });
    </script>
</div>