
<div class="border rounded-[4px] vstack" style="padding:0.775em 0.75em;row-gap: var(--wv-16)">
    <div class="hstack" style="gap:var(--wv-5)">
        <p class=" ">인트로설정</p>
        <p class="fs-[11/15/-0.44/500/#97989C]">(인트로사용 : 서비스 최초등록 시 아래설정 적용)</p>
    </div>

    <div class="form-check form-switch <?php echo $row['use_intro']?'':'disabled'; ?>" style="gap:var(--wv-6)" data-on-value="인트로사용" data-off-value="인트로미사용">
        <label class="form-check-label w-[84px]" for="contractitem[use_intro]">

        </label>
        <input class="form-check-input" onchange="if($(this).is(':checked')){$('.intro-setting-wrap').show()}else{$('.intro-setting-wrap').hide()}"
               type="checkbox"
               role="switch"
               name="contractitem[use_intro]"
               value="1"
            <?php echo $row['use_intro']?'checked':''; ?>
               id="contractitem[use_intro]">

    </div>

    <div class="intro-setting-wrap vstack" style="row-gap:var(--wv-10);<?php echo $row['use_intro']?'':'display:none' ; ?>">
        <div class="form-floating position-relative" style="z-index: 10">
            <textarea name="contractitem[intro][desc]" id="contractitem[intro][desc]"class="form-control  " placeholder="첫 등록 시 버튼 위쪽에 표시"><?php echo $row['intro']['desc'] ?></textarea>
            <label for="contractitem[intro][desc]" class="floatingInput">첫등록 설명</label>
        </div>
        <div class="form-floating position-relative" style="z-index: 10">
            <input type="text" name="contractitem[intro][button_label]"   value="<?php echo $row['intro']['button_label'] ?>" id="contractitem[intro][button_label]"   class="form-control  "  minlength="1" placeholder="첫 등록 시 버튼 내 상단에 표시">
            <label for="contractitem[intro][button_label]" class="floatingInput">버튼상단 라벨</label>
        </div>
        <div class="form-floating position-relative" style="z-index: 10">
            <textarea name="contractitem[intro][text1]" id="contractitem[intro][text1]"class="form-control  " placeholder="인트로화면 제목"><?php echo $row['intro']['text1'] ?></textarea>
            <label for="contractitem[intro][text1]" class="floatingInput">인트로화면 제목</label>
        </div>
        <div class="form-floating position-relative" style="z-index: 10">
            <textarea name="contractitem[intro][text2]" id="contractitem[intro][text2]"class="form-control  " placeholder="인트로화면 부제목"><?php echo $row['intro']['text2'] ?></textarea>
            <label for="contractitem[intro][text2]" class="floatingInput">인트로화면 부제목</label>
        </div>


        <div class="border rounded-[4px]" style="padding:0.775em 0.75em">
            <p class=" ">인트로화면 이미지</p>
            <div class="wv-ps-file w-[200px] mx-auto ratio ratio-16x9 mt-[8.5px]" >
                <div>
                    <input type="hidden" name="contractitem[intro][image][id]"      value="<?php echo $row['intro']['image']['id']; ?>"    >
                    <label class="wh-100 cursor-pointer d-flex-center text-center position-relative ">
                        <input type="file" name="contractitem[intro][image]" accept="image/*" class="d-none" >
                        <?php if($row['intro']['image']['path']){ ?>
                            <img src="<?php echo $row['intro']['image']['path'] ?>"  alt="" class="wh-100 object-fit-contain">
                        <?php } ?>
                        <label class="position-absolute wv-ps-file-delete" style="">
                            <input type="checkbox" name="contractitem[intro][image][delete]" value="1" class="d-none"  >
                            <span></span>
                        </label>
                        <div class="absolute inset-0 vstack h-100 justify-content-center wv-ps-file-new <?php echo $row['intro']['image']['path']?'d-none':''; ?>" style="row-gap:var(--wv-6)">
                            <i class="fa-solid fa-plus fs-[30///700/] d-block"></i>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="border rounded-[4px] " style="padding:0.775em 0.75em">
            <p class=" ">인트로화면 특징</p>


            <?php   $this->make_array($row['intro']['point']); ?>
            <div class="wv-ps-col mt-[8.5px]">
                <div class="wv-ps-list  vstack" style="row-gap: var(--wv-10)">

                <?php
                foreach ($row['intro']['point'] as $k => $v) {

                    $demo_class = !$v ? 'wv-ps-demo' : '';

                    ?>
                    <div class="wv-ps-each w-full <?php echo $demo_class; ?>">
                        <!-- 필수 hidden -->
                        <input type="hidden" name="contractitem[intro][point][<?php echo $k; ?>][id]"  value="<?php echo $v['id']; ?>">
                        <div class="d-flex justify-content-between  ">
                        <div class="form-floating col">
                            <input
                                    type="text"
                                    class="form-control js-menu-name"
                                    id="contractitem[intro][point][<?php echo $k; ?>][text]"
                                    name="contractitem[intro][point][<?php echo $k; ?>][text]"
                                    maxlength="20"
                                    placeholder="특징을 입력하세요."
                                    value="<?php echo $v['text']; ?>">
                            <label for="contractitem[intro][point][<?php echo $k; ?>][text]">특징</label>
                        </div>

                        <div class=" wv-ps-box ms-3  ">
                            <label class="h-100 ">
                                <input type="checkbox" class="d-none" name="contractitem[intro][point][<?php echo $k; ?>][delete]" value="1">
                                <span class="btn w-100 h-100 border btn-danger d-flex-center">삭제</span>
                            </label>
                        </div>
                        </div>
                    </div>
                    <?php  } ?>

                    <div class="wv-ps-new col-auto btn btn-dark"><i class="fa-solid fa-plus me-2"></i> 특징 추가</div>
            </div>
            </div>



        </div>
    </div>

     
</div>
