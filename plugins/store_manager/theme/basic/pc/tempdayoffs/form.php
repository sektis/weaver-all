<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// StorePartProxy에서 자동 처리되므로 바로 사용
$tempdayoffs_list = $row['tempdayoffs'];
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}
       

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-ps-col" style="">
        <div class="dayoffs-container">
            <p class="wv-ps-subtitle">임시휴무</p>

            <!-- 정기휴무 목록 -->
            <div class="wv-ps-list wv-vstack mt-[20px]" style="row-gap:var(--wv-20)">
                <?php
                foreach($tempdayoffs_list as $k => $dayoff) {
                    $id = isset($dayoff['id']) ? $dayoff['id'] : '';
                    $cycle = isset($dayoff['cycle']) ? $dayoff['cycle'] : '';
                    $target = isset($dayoff['target']) ? $dayoff['target'] : '';
                    $demo_class = ($id === '') ? 'wv-ps-demo' : '';
                    ?>
                    <div class="wv-ps-each <?php echo $demo_class; ?>" data-index="<?php echo $k; ?>">
                        <div class="temp-dayoff-item">


                         
                            <!-- 삭제 체크박스 (weaver 플러그인 규칙) -->
                            <label class="  wv-ps-delete-label-list " style="">
                                <input type="checkbox" class="d-none" name="dayoffs[<?php echo $k; ?>][delete]" value="1">
                                <span class="       " style=""><i class="fa-solid fa-circle-minus"></i> 삭제</span>
                            </label>
                        </div>
                    </div>
                    <?php
                } ?>

                <button type="button" class="btn border w-100 wv-ps-new fs-[14////] fw-600 h-[40px]">
                    + 임시휴무일 추가
                </button>
            </div>
        </div>

        <!-- Bootstrap5 Offcanvas -->
        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="dayoff-offcanvas" aria-labelledby="dayoff-offcanvas-label">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="dayoff-offcanvas-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
          
        });
    </script>
</div>