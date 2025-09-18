<?php

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

                $id      = isset($v['id'])      ? (string)$v['id']      : '';
                $ord     = isset($v['ord'])     ? (int)$v['ord']        : $n;
                $name    = isset($v['name'])    ? (string)$v['name']    : '';
                $desc    = isset($v['desc'])    ? (string)$v['desc']    : '';
                $is_main = !empty($v['is_main']) ? 1 : 0;
                $prices  = (isset($v['prices']) && is_array($v['prices'])) ? $v['prices'] : array();
                if (!count($prices)) { $prices = array(array('id'=>'','ord'=>1,'name'=>'','price'=>'')); }
                $images  = (isset($v['images']) && is_array($v['images'])) ? $v['images'] : array();
                $demo_class = !$v ? 'wv-ps-demo' : '';
                $img_count = count(array_filter($images));

                ?>
                <div class="wv-ps-each <?php echo $demo_class; ?>">
                    <!-- 필수 hidden -->
                    <input type="hidden" name="menu[<?php echo $k; ?>][id]"  value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>">
                    <input type="hidden" name="menu[<?php echo $k; ?>][ord]" value="<?php echo (int)$ord; ?>">

                    <div class="vstack" style="row-gap: var(--wv-15)">

                        <div class="wv-ps-box">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <div class="grid-2">
                                        <div class="form-floating">
                                            <input
                                                    type="text"
                                                    class="form-control js-menu-name"
                                                    id="<?php echo $skin_id; ?>-name-<?php echo $k; ?>"
                                                    name="menu[<?php echo $k; ?>][name]"
                                                    maxlength="20"
                                                    placeholder="메뉴명"
                                                    value="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>"
                                                    pattern="[0-9A-Za-zㄱ-ㅎ가-힣\s]{1,20}"
                                                    inputmode="text">
                                            <label for="<?php echo $skin_id; ?>-name-<?php echo $k; ?>">메뉴명 (최대 20자, 특수문자 불가)</label>
                                        </div>

                                        <div class="form-check form-switch align-self-center"  >
                                            <input class="form-check-input" type="checkbox"
                                                   id="<?php echo $skin_id; ?>-main-<?php echo $k; ?>"
                                                   name="menu[<?php echo $k; ?>][is_main]"
                                                   value="1" <?php echo $is_main ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $skin_id; ?>-main-<?php echo $k; ?>">대표메뉴로 표시</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- 일반 행 삭제 버튼 -->

                            </div>
                        </div>

                        <!-- 설명 -->
                        <div class="wv-ps-box">
                            <div class="form-floating mb-1">
                            <textarea class="form-control js-desc"
                                      id="<?php echo $skin_id; ?>-desc-<?php echo $k; ?>"
                                      name="menu[<?php echo $k; ?>][desc]"
                                      placeholder="메뉴 부가설명"
                                      maxlength="100"
                                      style="height: 90px;"><?php echo htmlspecialchars($desc, ENT_QUOTES); ?></textarea>
                                <label for="<?php echo $skin_id; ?>-desc-<?php echo $k; ?>">메뉴 부가설명 (최대 100자)</label>
                            </div>
                            <div class="form-text subtle mb-2 text-end"><span class="js-desc-count">0</span>/100자</div>
                        </div>

                        <!-- 가격 옵션 -->
                        <div class="wv-ps-box">
                            <div class="wv-ps-subtitle">가격 옵션</div>
                            <div class="wv-ps-list prices">
                        <?php $pi = 1; foreach ($prices as $pk => $pv) {
                            $pid   = isset($pv['id'])    ? (string)$pv['id']    : '';
                            $pord  = isset($pv['ord'])   ? (int)$pv['ord']      : $pi;
                            $pname = isset($pv['name'])  ? (string)$pv['name']  : '';
                            $pval  = isset($pv['price']) ? (string)$pv['price'] : '';
                            $p_demo = ($pid==='') ? 'wv-ps-demo' : '';
                            ?>
                            <div class="wv-ps-each <?php echo $p_demo; ?>" style="padding:10px;">
                                <input type="hidden" name="menu[<?php echo $k; ?>][prices][<?php echo $pk; ?>][id]"  value="<?php echo htmlspecialchars($pid, ENT_QUOTES); ?>">
                                <input type="hidden" name="menu[<?php echo $k; ?>][prices][<?php echo $pk; ?>][ord]" value="<?php echo (int)$pord; ?>">

                                <div class="d-flex justify-content-between  ">
                                    <div class="grid-2 w-100">
                                        <div class="form-floating">
                                            <input type="text"
                                                   class="form-control"
                                                   id="<?php echo $skin_id; ?>-pname-<?php echo $k.'-'.$pk; ?>"
                                                   name="menu[<?php echo $k; ?>][prices][<?php echo $pk; ?>][name]"
                                                   maxlength="20"
                                                   placeholder="옵션명"
                                                   value="<?php echo htmlspecialchars($pname, ENT_QUOTES); ?>">
                                            <label for="<?php echo $skin_id; ?>-pname-<?php echo $k.'-'.$pk; ?>">옵션명 (예: 1~2인분)</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="text"
                                                   class="form-control js-int-only"
                                                   id="<?php echo $skin_id; ?>-pval-<?php echo $k.'-'.$pk; ?>"
                                                   name="menu[<?php echo $k; ?>][prices][<?php echo $pk; ?>][price]"
                                                   placeholder="가격"
                                                   inputmode="numeric" step="1" min="0"
                                                   value="<?php echo htmlspecialchars($pval, ENT_QUOTES); ?>">
                                            <label for="<?php echo $skin_id; ?>-pval-<?php echo $k.'-'.$pk; ?>">가격 (정수)</label>
                                        </div>
                                    </div>

                                    <div class="wv-ps-box ms-3">
                                        <label class="h-100">
                                            <input type="checkbox" class="d-none" name="menu[<?php echo $k; ?>][prices][<?php echo $pk; ?>][delete]" value="1">
                                            <span class=" ">삭제</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php $pi++; } ?>
                        <div class="wv-ps-new"><i class="fa-solid fa-plus me-2"></i> 가격 옵션 추가</div>
                    </div>
                        </div>

                        <div class="wv-ps-box">
                            <!-- 이미지 -->
                            <?php
                            $images  = (isset($v['images']) && is_array($v['images'])) ? $v['images'] : array();
                            // 이미지가 하나도 없으면 데모 행 1개 강제 생성 (multiple 업로드의 clone base 확보용)
                            if (!count(array_filter($images))) {
                                $images = array(array('id'=>'','path'=>''));
                            }
                            $img_count = count(array_filter($images));
                            ?>
                            <div class="wv-ps-subtitle mt-2">이미지</div>
                            <div class="hstack flex-wrap wv-ps-list wv-ps-list-image">
                        <div class="wv-ps-each  ">
                            <label   class="wh-100 cursor-pointer d-flex-center text-center position-relative">
                                <input type="file"
                                       name="menu[<?php echo $k; ?>][images][]"
                                       multiple accept="image/*"
                                       class="d-none"
                                       data-max-count="<?php echo $this->image_max_count; ?>">
                                <div class="vstack h-100 justify-content-center" style="row-gap:6px">
                                    <i class="fa-solid fa-plus fs-[30///700/] d-block"></i>
                                    <p class="m-0">
                                        <span class="fw-700 wv-ps-file-count"><?php echo count(array_filter($v['images'])); ?></span>
                                        /
                                        <span style="color:#97989c"><?php echo $this->image_max_count; ?></span>
                                    </p>
                                </div>
                            </label>
                            <div class=" subtle"></div>
                        </div>

                        <?php
                        foreach ($images as $ik => $iv) {
                            $iid  = isset($iv['id'])   ? (string)$iv['id']   : '';
                            $path = isset($iv['path']) ? (string)$iv['path'] : '';
                            $i_demo = ($iid==='') ? 'wv-ps-demo' : '';
                            ?>
                            <div class="wv-ps-each <?php echo $i_demo; ?>">

                                <img src="<?php echo htmlspecialchars($path, ENT_QUOTES); ?>" alt="" class="wh-100 object-fit-contain">

                                <input type="hidden" name="menu[<?php echo $k; ?>][images][<?php echo $ik; ?>][id]" value="<?php echo htmlspecialchars($iid, ENT_QUOTES); ?>">
                                <p class="position-absolute wv-ps-num"></p>
                                <label class="position-absolute wv-ps-delete-label">
                                    <input type="checkbox" name="menu[<?php echo $k; ?>][images][<?php echo $ik; ?>][delete]" value="1" class="d-none">
                                    <span></span>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                        </div>

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

            // 설명 100자 카운터
            $skin.find('.js-desc').each(function(){
                var $ta = $(this);
                var $cnt = $ta.closest('.form-floating').nextAll('.form-text').first().find('.js-desc-count');
                var upd = function(){ $cnt.text(($ta.val()||'').length); };
                $ta.on('input', upd); upd();
            });
            $skin.on('input','.js-desc',function(){

                var $ta = $(this);
                var $cnt = $ta.closest('.wv-ps-box').find('.js-desc-count');

                $cnt.text(($ta.val()||'').length)

            });

            // 메뉴명 특수문자 금지(한/영/숫/공백) & 20자
            var nameRe = /^[0-9A-Za-zㄱ-ㅎ가-힣\s]{0,20}$/;
            $skin.on('input', '.js-menu-name', function(){
                var v = $(this).val();
                var filtered = v.replace(/[^0-9A-Za-zㄱ-ㅎ가-힣\s]/g, '').slice(0,20);
                if (v !== filtered) $(this).val(filtered);
                this.setCustomValidity(nameRe.test($(this).val()) ? '' : '특수문자는 입력할 수 없습니다.');
            });

            // 가격 정수만
            $skin.on('input', '.js-int-only', function(){
                var v = $(this).val();
                var f = (v||'').replace(/[^\d]/g, '');
                if (v !== f) $(this).val(f);
            });




        });
    </script>
</div>
