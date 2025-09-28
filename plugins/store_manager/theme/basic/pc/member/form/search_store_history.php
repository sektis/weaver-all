<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$this->make_array($row[$column]);

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> .nav-pills .nav-link{background:#fff;color:#97989C; ;border:1px solid #efefef;height: var(--wv-40);display: flex;justify-content: center;align-items: center}
        <?php echo $skin_selector?> .nav-pills .nav-link.active{background:#000;color:#fff}
        <?php echo $skin_selector?> .time-row{display:flex;align-items:center;gap:8px;margin-bottom:12px;}
        <?php echo $skin_selector?> .time-label{min-width:50px;font-weight:600;color: #97989c;;font-size: var(--wv-14)}
        <?php echo $skin_selector?> .form-select{border:1px solid #ddd;padding:4px 8px;border-radius:4px;}
        <?php echo $skin_selector?> .tab-content{padding:20px 0;}
        <?php echo $skin_selector?> .time-section{background:#fff; ; :6px; ;}
        <?php echo $skin_selector?> .time-section h6{font-size: var(--wv-14);font-weight: 600}
        <?php echo $skin_selector?> .time-section .time-row{margin-top: var(--wv-6 )}
        <?php echo $skin_selector?> .day-item{padding: var(--wv-16) 0}
        <?php echo $skin_selector?> .day-item span{font-size: var(--wv-14);font-weight: 600}
        <?php echo $skin_selector?> .day-header{display:flex;align-items:center;gap:12px;margin-bottom:8px;}
        <?php echo $skin_selector?> .day-times{display:none;}
        <?php echo $skin_selector?> .day-times.show{display:block;margin-top: var(--wv-16)}
        <?php echo $skin_selector?> select{height: var(--wv-48);padding: var(--wv-13) var(--wv-12) !important;background-color: #f9f9f9;border: 0!important;font-size: var(--wv-16);font-weight: 500}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {
        <?php echo $skin_selector?> .time-row{flex-wrap:wrap;}
        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-vstack" style="row-gap: var(--wv-10)">
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
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");


        });
    </script>
</div>