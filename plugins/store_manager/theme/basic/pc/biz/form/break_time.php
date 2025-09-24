<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$this->make_array($row['break_time']);
$break_time = $row['break_time'];
$days = array('mon'=>'월','tue'=>'화','wed'=>'수','thu'=>'목','fri'=>'금','sat'=>'토','sun'=>'일');
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
        <p class="wv-ps-subtitle">브레이크타임</p>
        <div class="w-[500px] mw-100">

            <ul class="nav nav-pills mb-3 text-center mb-[16px]" style="gap:var(--wv-6)">
                <li class="nav-item col"><a class="nav-link active" href="#daily">매일</a></li>
                <li class="nav-item col"><a class="nav-link" href="#week">평일/주말</a></li>
                <li class="nav-item col"><a class="nav-link" href="#days">요일별</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane show active" id="daily">
                    <div class="time-section">
                        <div class="time-row">
                            <span class="time-label">시작:</span>
                            <?php
                            $ds = isset($break_time['daily']['start']) ? $break_time['daily']['start'] : array();
                            echo wv_store_manager_time_select($field_name.'[daily][start][period]', $ds, 'period');
                            echo wv_store_manager_time_select($field_name.'[daily][start][hour]', $ds, 'hour');
                            echo '<span>:</span>';
                            echo wv_store_manager_time_select($field_name.'[daily][start][minute]', $ds, 'minute');
                            ?>
                        </div>
                        <div class="time-row">
                            <span class="time-label">종료:</span>
                            <?php
                            $de = isset($break_time['daily']['end']) ? $break_time['daily']['end'] : array();
                            echo wv_store_manager_time_select($field_name.'[daily][end][period]', $de, 'period');
                            echo wv_store_manager_time_select($field_name.'[daily][end][hour]', $de, 'hour');
                            echo '<span>:</span>';
                            echo wv_store_manager_time_select($field_name.'[daily][end][minute]', $de, 'minute');
                            ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="week">
                    <div class="time-section">
                        <h6>평일</h6>
                        <div class="time-row">
                            <span class="time-label">시작:</span>
                            <?php
                            $ws = isset($break_time['weekday']['start']) ? $break_time['weekday']['start'] : array();
                            echo wv_store_manager_time_select($field_name.'[weekday][start][period]', $ws, 'period');
                            echo wv_store_manager_time_select($field_name.'[weekday][start][hour]', $ws, 'hour');
                            echo '<span>:</span>';
                            echo wv_store_manager_time_select($field_name.'[weekday][start][minute]', $ws, 'minute');
                            ?>
                        </div>
                        <div class="time-row">
                            <span class="time-label">종료:</span>
                            <?php
                            $we = isset($break_time['weekday']['end']) ? $break_time['weekday']['end'] : array();
                            echo wv_store_manager_time_select($field_name.'[weekday][end][period]', $we, 'period');
                            echo wv_store_manager_time_select($field_name.'[weekday][end][hour]', $we, 'hour');
                            echo '<span>:</span>';
                            echo wv_store_manager_time_select($field_name.'[weekday][end][minute]', $we, 'minute');
                            ?>
                        </div>
                    </div>
                    <div class="time-section">
                        <h6>주말</h6>
                        <div class="time-row">
                            <span class="time-label">시작:</span>
                            <?php
                            $wes = isset($break_time['weekend']['start']) ? $break_time['weekend']['start'] : array();
                            echo wv_store_manager_time_select($field_name.'[weekend][start][period]', $wes, 'period');
                            echo wv_store_manager_time_select($field_name.'[weekend][start][hour]', $wes, 'hour');
                            echo '<span>:</span>';
                            echo wv_store_manager_time_select($field_name.'[weekend][start][minute]', $wes, 'minute');
                            ?>
                        </div>
                        <div class="time-row">
                            <span class="time-label">종료:</span>
                            <?php
                            $wee = isset($break_time['weekend']['end']) ? $break_time['weekend']['end'] : array();
                            echo wv_store_manager_time_select($field_name.'[weekend][end][period]', $wee, 'period');
                            echo wv_store_manager_time_select($field_name.'[weekend][end][hour]', $wee, 'hour');
                            echo '<span>:</span>';
                            echo wv_store_manager_time_select($field_name.'[weekend][end][minute]', $wee, 'minute');
                            ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="days">
                    <?php foreach($days as $k=>$v):
                        $d = isset($break_time[$k]) ? $break_time[$k] : array();
                        $en = isset($d['enabled']) ? (int)$d['enabled'] : 0;
                        ?>
                        <div class="day-item">
                            <div class="day-header hstack justify-content-between">
                                <span><?php echo $v; ?>요일</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="<?php echo $field_name; ?>[<?php echo $k; ?>][enabled]" id="<?php echo $field_name; ?>[<?php echo $k; ?>][enabled]" value="1" <?php echo $en?'checked':''; ?> data-day="<?php echo $k; ?>">
                                    <label class="form-check-label" for="<?php echo $field_name; ?>[<?php echo $k; ?>][enabled]"><?php echo $en?'설정 함':'설정 안함'; ?></label>
                                </div>
                            </div>
                            <div class="day-times <?php echo $en?'show':''; ?>" data-times="<?php echo $k; ?>">
                                <div class="time-row">
                                    <span class="time-label">시작:</span>
                                    <?php
                                    $ds = isset($d['start']) ? $d['start'] : array();
                                    echo wv_store_manager_time_select($field_name.'['.$k.'][start][period]', $ds, 'period');
                                    echo wv_store_manager_time_select($field_name.'['.$k.'][start][hour]', $ds, 'hour');
                                    echo '<span>:</span>';
                                    echo wv_store_manager_time_select($field_name.'['.$k.'][start][minute]', $ds, 'minute');
                                    ?>
                                </div>
                                <div class="time-row">
                                    <span class="time-label">종료:</span>
                                    <?php
                                    $de = isset($d['end']) ? $d['end'] : array();
                                    echo wv_store_manager_time_select($field_name.'['.$k.'][end][period]', $de, 'period');
                                    echo wv_store_manager_time_select($field_name.'['.$k.'][end][hour]', $de, 'hour');
                                    echo '<span>:</span>';
                                    echo wv_store_manager_time_select($field_name.'['.$k.'][end][minute]', $de, 'minute');
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");

            $skin.on('click', '.nav-link', function(e){
                e.preventDefault();
                var target = $(this).attr('href');
                $skin.find('.nav-link').removeClass('active');
                $(this).addClass('active');
                $skin.find('.tab-pane').removeClass('show active');
                $skin.find(target).addClass('show active');
            });

            $skin.on('change', 'input[data-day]', function(){

                var day = $(this).data('day');
                var checked = $(this).is(':checked');
                var $switch = $(this).closest('.form-switch');
                var $label = $switch.find('label');
                $switch.toggleClass('disabled', !checked);
                $label.text(checked ? '설정 함' : '설정 안함');
                $skin.find('[data-times="'+day+'"]').toggleClass('show', checked);
            });
        });
    </script>
</div>