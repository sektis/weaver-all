<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// StorePartProxy에서 자동 처리되므로 바로 사용
$dayoffs_list = $row['dayoffs'];
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .dayoff-item{display:flex;gap:var(--wv-8);height: var(--wv-48)}
        <?php echo $skin_selector?> .wv-ps-demo .dayoff-item{border-color:#f0f0f0;background:#fafafa;}
        <?php echo $skin_selector?> .dayoff-select{flex:1;font-size: var(--wv-16);background:#f9f9f9;padding:var(--wv-13) var(--wv-12);border-radius:var(--wv-4);cursor:pointer;user-select:none;position:relative;display:flex;align-items:center;justify-content:space-between;min-height:40px;}
        <?php echo $skin_selector?> .dayoff-select.empty{color:#999;background:#f8f9fa;}
        <?php echo $skin_selector?> .dayoff-select:hover{border-color:#007bff;}
        <?php echo $skin_selector?> .dayoff-select::after{content:'\f107';font-family:'Font Awesome 5 Free';font-weight:900;color:#666;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .delete-btn{background:#dc3545;color:white;border:none;padding:var(--wv-4) var(--wv-8);border-radius:var(--wv-4);cursor:pointer;font-size:var(--wv-12);}
        <?php echo $skin_selector?> .delete-btn:hover{background:#c82333;}

        /* 날짜 룰렛 스타일 */
        <?php echo $skin_selector?> .date-roulette{position:relative;height: var(--wv-180); ;border:1px solid #ddd;border-radius:var(--wv-8);background:white;}
        <?php echo $skin_selector?> .date-roulette-container {
                                        position: relative;
                                        height: 100%;
                                        overflow-y: auto;
                                        scroll-behavior: smooth;
                                        touch-action: pan-y;
                                    }
        <?php echo $skin_selector?> .date-roulette-list {
                                        position: relative;
                                    }
        <?php echo $skin_selector?> .date-roulette-item{height:40px;display:flex;align-items:center;justify-content:center;font-size:var(--wv-16);color:#666;transition:all 0.3s ease;cursor:pointer;}
        <?php echo $skin_selector?> .date-roulette-item.empty{color:transparent;cursor:default;pointer-events:none;}
        <?php echo $skin_selector?> .date-roulette-item.active{background:#efefef;color:#fff;font-weight:600;font-size:var(--wv-18);border-radius:var(--wv-4);}
        <?php echo $skin_selector?> .date-roulette::before, <?php echo $skin_selector?> .date-roulette::after{content:'';position:absolute;left:0;right:0;height:80px;pointer-events:none;z-index:10;}
        <?php echo $skin_selector?> .date-roulette::before{top:0;background:linear-gradient(to bottom, rgba(255,255,255,0.9), rgba(255,255,255,0));}
        <?php echo $skin_selector?> .date-roulette::after{bottom:0;background:linear-gradient(to top, rgba(255,255,255,0.9), rgba(255,255,255,0));}

        /* 날짜 선택 버튼 */
        <?php echo $skin_selector?> .date-confirm-btn{background:#0d171b;color:white;border:none;padding:var(--wv-12) var(--wv-20);border-radius:var(--wv-4);width:100%;cursor:pointer;margin-top:var(--wv-12);}
        <?php echo $skin_selector?> .date-confirm-btn:hover{background:#0d171b;}
        <?php echo $skin_selector?> .date-confirm-btn:disabled{background:#ccc;cursor:not-allowed;}

        /* Offcanvas 내부 스타일 */
        <?php echo $skin_selector?> .option-list{display:flex;flex-direction:column;gap:var(--wv-8);}
        <?php echo $skin_selector?> .option-item{padding:var(--wv-12) var(--wv-16);border:1px solid #ddd;border-radius:var(--wv-4);cursor:pointer;display:flex;justify-content:space-between;align-items:center;background:white;}
        <?php echo $skin_selector?> .option-item:hover{background:#f8f9fa;border-color:#0d171b;}
        <?php echo $skin_selector?> .option-item.selected{background:#e3f2fd;border-color:#0d171b;}
        <?php echo $skin_selector?> .option-item i{color:#28a745;opacity:0.5;transition:opacity 0.2s;}
        <?php echo $skin_selector?> .option-item.selected i{opacity:1;}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-ps-col" style="">

        <div class="container">
            <!-- 정기휴무 목록 -->
            <div class="row row-cols-5" style="--bs-gutter-x: var(--wv-14);--bs-gutter-y: var(--wv-12)">
                <div class="col">
                    <div class="text-center vstack justify-content-center" style="row-gap: var(--wv-2)">
                        <div class="w-[54px] h-[54px]">
                            <img src="<?php echo $this->manager->plugin_url; ?>/img/category_list/0.png" class="wh-100 object-fit-contain" alt="">
                        </div>
                        <p class="fs-[11/140%//500/] text-nowrap">전체</p>
                    </div>
                </div>
                <?php
                for($i=1;$i<=11;$i++) {

                    ?>
                    <div class="col">
                        <div class="text-center vstack justify-content-center " style="row-gap: var(--wv-2)">
                            <div class="w-[54px] h-[54px]">
                                <img src="<?php echo $this->manager->plugin_url; ?>/img/category_list/<?php echo $i; ?>.png" class="wh-100 object-fit-contain" alt="">
                            </div>
                            <p class="fs-[11/140%//500/] text-nowrap"><?php echo $this->category_arr[$i]; ?></p>
                        </div>
                    </div>
                    <?php
                } ?>
                <div class="col">
                    <div class="text-center vstack justify-content-center" style="row-gap: var(--wv-2)">
                        <div class="w-[54px] h-[54px]">
                            <img src="<?php echo $this->manager->plugin_url; ?>/img/category_list/99.png" class="wh-100 object-fit-contain" alt="">
                        </div>
                        <p class="fs-[11/140%//500/] text-nowrap">기타</p>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");
    </script>
</div>