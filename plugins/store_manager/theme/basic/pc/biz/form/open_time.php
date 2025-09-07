<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$this->make_array($row['open_time']);
$open_time = $row['open_time'];
$days = array('mon'=>'월','tue'=>'화','wed'=>'수','thu'=>'목','fri'=>'금','sat'=>'토','sun'=>'일');

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> position-relative d-flex-center flex-nowrap" style="">
    <style>
        <?php echo $skin_selector?> .nav-pills .nav-link{background:#fff;color:#97989C;margin-right:8px;border:1px solid #efefef}
        <?php echo $skin_selector?> .nav-pills .nav-link.active{background:#000;color:#fff}
        <?php echo $skin_selector?> .time-row{display:flex;align-items:center;gap:8px;margin-bottom:12px;}
        <?php echo $skin_selector?> .time-label{min-width:50px;font-weight:500;}
        <?php echo $skin_selector?> .form-select{border:1px solid #ddd;padding:4px 8px;border-radius:4px;}
        <?php echo $skin_selector?> .tab-content{padding:20px 0;}
        <?php echo $skin_selector?> .time-section{background:#f8f9fa;padding:16px;border-radius:6px;margin-bottom:16px;}
        <?php echo $skin_selector?> .day-item{border:1px solid #e9ecef;padding:12px;border-radius:6px;margin-bottom:12px;}
        <?php echo $skin_selector?> .day-header{display:flex;align-items:center;gap:12px;margin-bottom:8px;}
        <?php echo $skin_selector?> .day-times{display:none;}
        <?php echo $skin_selector?> .day-times.show{display:block;}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {
        <?php echo $skin_selector?> .time-row{flex-wrap:wrap;}
        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full wv-vstack" style="row-gap: var(--wv-10)">
        <p class="wv-ps-subtitle">영업시간</p>
        <div class="w-[500px]">

            <ul class="nav nav-pills mb-3 text-center">
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
                            $ds = isset($open_time['daily']['start']) ? $open_time['daily']['start'] : array();
                            echo $this->wv_biz_time_select($field_name.'[daily][start][period]', $ds, 'period');
                            echo $this->wv_biz_time_select($field_name.'[daily][start][hour]', $ds, 'hour');
                            echo '<span>:</span>';
                            echo $this->wv_biz_time_select($field_name.'[daily][start][minute]', $ds, 'minute');
                            ?>
                        </div>
                        <div class="time-row">
                            <span class="time-label">종료:</span>
                            <?php
                            $de = isset($open_time['daily']['end']) ? $open_time['daily']['end'] : array();
                            echo $this->wv_biz_time_select($field_name.'[daily][end][period]', $de, 'period');
                            echo $this->wv_biz_time_select($field_name.'[daily][end][hour]', $de, 'hour');
                            echo '<span>:</span>';
                            echo $this->wv_biz_time_select($field_name.'[daily][end][minute]', $de, 'minute');
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
                            $ws = isset($open_time['weekday']['start']) ? $open_time['weekday']['start'] : array();
                            echo $this->wv_biz_time_select($field_name.'[weekday][start][period]', $ws, 'period');
                            echo $this->wv_biz_time_select($field_name.'[weekday][start][hour]', $ws, 'hour');
                            echo '<span>:</span>';
                            echo $this->wv_biz_time_select($field_name.'[weekday][start][minute]', $ws, 'minute');
                            ?>
                        </div>
                        <div class="time-row">
                            <span class="time-label">종료:</span>
                            <?php
                            $we = isset($open_time['weekday']['end']) ? $open_time['weekday']['end'] : array();
                            echo $this->wv_biz_time_select($field_name.'[weekday][end][period]', $we, 'period');
                            echo $this->wv_biz_time_select($field_name.'[weekday][end][hour]', $we, 'hour');
                            echo '<span>:</span>';
                            echo $this->wv_biz_time_select($field_name.'[weekday][end][minute]', $we, 'minute');
                            ?>
                        </div>
                    </div>
                    <div class="time-section">
                        <h6>주말</h6>
                        <div class="time-row">
                            <span class="time-label">시작:</span>
                            <?php
                            $wes = isset($open_time['weekend']['start']) ? $open_time['weekend']['start'] : array();
                            echo $this->wv_biz_time_select($field_name.'[weekend][start][period]', $wes, 'period');
                            echo $this->wv_biz_time_select($field_name.'[weekend][start][hour]', $wes, 'hour');
                            echo '<span>:</span>';
                            echo $this->wv_biz_time_select($field_name.'[weekend][start][minute]', $wes, 'minute');
                            ?>
                        </div>
                        <div class="time-row">
                            <span class="time-label">종료:</span>
                            <?php
                            $wee = isset($open_time['weekend']['end']) ? $open_time['weekend']['end'] : array();
                            echo $this->wv_biz_time_select($field_name.'[weekend][end][period]', $wee, 'period');
                            echo $this->wv_biz_time_select($field_name.'[weekend][end][hour]', $wee, 'hour');
                            echo '<span>:</span>';
                            echo $this->wv_biz_time_select($field_name.'[weekend][end][minute]', $wee, 'minute');
                            ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="days">
                    <?php foreach($days as $k=>$v):
                        $d = isset($open_time[$k]) ? $open_time[$k] : array();
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
                                    echo $this->wv_biz_time_select($field_name.'['.$k.'][start][period]', $ds, 'period');
                                    echo $this->wv_biz_time_select($field_name.'['.$k.'][start][hour]', $ds, 'hour');
                                    echo '<span>:</span>';
                                    echo $this->wv_biz_time_select($field_name.'['.$k.'][start][minute]', $ds, 'minute');
                                    ?>
                                </div>
                                <div class="time-row">
                                    <span class="time-label">종료:</span>
                                    <?php
                                    $de = isset($d['end']) ? $d['end'] : array();
                                    echo $this->wv_biz_time_select($field_name.'['.$k.'][end][period]', $de, 'period');
                                    echo $this->wv_biz_time_select($field_name.'['.$k.'][end][hour]', $de, 'hour');
                                    echo '<span>:</span>';
                                    echo $this->wv_biz_time_select($field_name.'['.$k.'][end][minute]', $de, 'minute');
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