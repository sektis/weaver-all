
<div class="border rounded-[4px] vstack" style="padding:0.775em 0.75em;row-gap: var(--wv-16)">
    <div class="hstack" style="gap:var(--wv-5)">
        <p class=" ">색상설정</p>
    </div>
    <div class="hstack">
        <div class="form-floating position-relative" style="z-index: 10">
            <input type="text" name="contractitem[color_type][text]" required value="<?php echo $row['color_type']['text'] ?>" id="contractitem[color_type][text]"   class="form-control  "  minlength="1" placeholder="글자색 (ex #ffffff)">
            <label for="contractitem[color_type][text]" class="floatingInput">글자색</label>
        </div>
        <div class="form-floating position-relative" style="z-index: 10">
            <input type="text" name="contractitem[color_type][bg]" required value="<?php echo $row['color_type']['bg'] ?>" id="contractitem[color_type][bg]"   class="form-control  "  minlength="1" placeholder="배경색 (ex #ffffff)">
            <label for="contractitem[color_type][bg]" class="floatingInput">배경색</label>
        </div>
    </div>
</div>