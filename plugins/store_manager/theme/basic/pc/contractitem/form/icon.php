
<div class="border rounded-[4px]" style="padding:0.775em 0.75em">
    <p class=" ">상품 아이콘</p>
    <div class="wv-ps-file w-[200px] ratio ratio-16x9 mt-[8.5px]" >
        <div>
            <input type="hidden" name="contractitem[icon][id]"      value="<?php echo $row['icon']['id']; ?>"    >
            <label class="wh-100 cursor-pointer d-flex-center text-center position-relative ">
                <input type="file" name="contractitem[icon]" accept="image/*" class="d-none" >
                <?php if($row['icon']['path']){ ?>
                    <img src="<?php echo $row['icon']['path'] ?>"  alt="" class="wh-100 object-fit-contain">
                <?php } ?>
                    <label class="position-absolute wv-ps-file-delete" style="">
                        <input type="checkbox" name="contractitem[icon][delete]" value="1" class="d-none"  >
                        <span></span>
                    </label>
                    <div class="absolute inset-0 vstack h-100 justify-content-center wv-ps-file-new <?php echo $row['icon']['path']?'d-none':''; ?>" style="row-gap:var(--wv-6)">
                        <i class="fa-solid fa-plus fs-[30///700/] d-block"></i>
                    </div>
            </label>
        </div>
    </div>
</div>
