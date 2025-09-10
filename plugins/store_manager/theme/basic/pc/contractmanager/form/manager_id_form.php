<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
dd($this->render_all('manager_id','form'));
?>
<div class="vstack h-100 ">
    <div class="wv-offcanvas-header col-auto">
        <div class="row align-items-center">
            <div class="col"></div>
            <div class="col-auto"><p>계약 담당자 관리</p></div>

            <div class="col text-end">
                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    </div>

    <div class="wv-offcanvas-body col vstack">
        <p class="fs-[24/32/-0.96/600/#0D171B]">
            친구 추천 코드가 <br>
            있나요?
        </p>
        <div class="mt-[40px] "  >

            <div class="border-bottom">
                <label for="mb_recommend_code" class="fs-[12//-0.48/600/#0D171B]">친구 추천 코드<strong class="sound_only"> 필수</strong></label>
                <input type="text" name="mb_recommend_code" id="mb_recommend_code"   class="form-control fs-[14/20/-0.56/600/#0d171b]  border-0 px-0 py-[6px]    mt-[6px]  " style="padding: var(--wv-17) var(--wv-16)"  maxLength="20" placeholder="친구 추천 코드 6자리를 입력해주세요" autocomplete="new-password">
            </div>

        </div>


    </div>

    <div class="mt-auto mb-[50px]">

        <button type="submit" class="w-full btn hstack justify-content-center" style="gap:var(--wv-6)">
            <p class="fs-[14/20/-0.56/500/#97989C]">추천 코드가 없어요</p>
            <p class="fs-[14/20/-0.56/500/#0D171B]">건너뛰기</p>
        </button>
        <button type="submit" class="w-full py-[14px] fs-[14//-0.56/700/#FFF] submit-btn transition hover:bg-[#0d171b] mt-[22px]" style="border:0;background-color: #cfcfcf;border-radius: var(--wv-4)">확인</button>
    </div>
</div>