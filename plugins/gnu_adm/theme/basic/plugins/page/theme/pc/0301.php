<?php
if (!defined('_GNUBOARD_')) exit;

$page_title = '친구초대 관리';
$sfl_options = array(
    'm.mb_id'   => '아이디',
    'm.mb_name' => '이름',
    'm.mb_nick' => '닉네임',
);
$sfl_whitelist = array_keys($sfl_options);

// invite 테이블
$invite_table = wv()->store_manager->made('invite')->get_write_table_name();
$member_table = $g5['member_table'];

// 검색 조건
$where_sql = "1=1";
if($stx !== '') {
    $stx_esc = sql_escape_string($stx);
    if ($sfl && in_array($sfl, $sfl_whitelist)) {
        $where_sql .= " AND {$sfl} LIKE '%{$stx_esc}%'";
    }
}

// 페이징
$rows_per_page = 20;
$from_record = ($page - 1) * $rows_per_page;

// 초대자별 집계 및 목록 조회
$list_sql = "
    SELECT 
        i.mb_id,
        m.mb_name,
        m.mb_nick,
        MAX(i.invite_code) as invite_code,
        COUNT(*) as invite_count,
        SUM(CASE WHEN i.invite_member_wr_id > 0 THEN 1 ELSE 0 END) as valid_count
    FROM {$invite_table} i
    LEFT JOIN {$member_table} m ON i.mb_id = m.mb_id
    WHERE {$where_sql}
    GROUP BY i.mb_id
    ORDER BY invite_count DESC, valid_count DESC
    LIMIT {$from_record}, {$rows_per_page}
";
$list_result = sql_query($list_sql);
$list = array();
while($row = sql_fetch_array($list_result)) {
    $list[] = $row;
}

// 전체 개수
$total_sql = "
    SELECT COUNT(DISTINCT i.mb_id) as cnt
    FROM {$invite_table} i
    LEFT JOIN {$member_table} m ON i.mb_id = m.mb_id
    WHERE {$where_sql}
";
$total_row = sql_fetch($total_sql);
$total_count = $total_row['cnt'];

// 페이징
$total_page = ceil($total_count / $rows_per_page);

// 전체 통계
$stats = sql_fetch("
    SELECT 
        COUNT(*) as total_invites,
        COUNT(DISTINCT mb_id) as total_users,
        SUM(CASE WHEN invite_member_wr_id > 0 THEN 1 ELSE 0 END) as total_valid
    FROM {$invite_table}
");
?>

<div class="wv-vstack">
    <div class="page-top-menu">
        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
           data-wv-ajax-data='{"action":"view","made":"invite","part":"invite","field":"admin/stats","type":"view"}'
           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[600px]" class="top-menu-btn">
            <i class="fa-solid fa-chart-simple"></i> 통계 보기
        </a>
    </div>

    <div class="hstack justify-content-between">
        <div></div>
        <form method="post" action="<?php echo wv_page_url($wv_page_id); ?>" class="hstack" style="gap: var(--wv-8);">
            <div class="form-floating" style="min-width: 180px;">
                <select name="sfl" id="sfl" required class="form-select" aria-label="검색필드 선택">
                    <option value=""<?php echo $sfl === '' ? ' selected' : ''; ?>>검색필드 선택</option>
                    <?php
                    foreach($sfl_options as $key => $label) {
                        $sel = ($key === $sfl) ? ' selected' : '';
                        echo '<option value="' . $key . '"' . $sel . '>' . get_text($label) . '</option>';
                    }
                    ?>
                </select>
                <label for="sfl" class="floatingSelect">검색필드</label>
            </div>

            <div class="form-floating position-relative" style="z-index: 10; min-width: 220px;">
                <input type="text" name="stx" id="stx" value="<?php echo get_text($stx); ?>" class="form-control wv-input-text-reset" placeholder="검색어를 입력하세요">
                <label for="stx" class="floatingInput">검색어</label>
            </div>

            <button type="submit" class="btn border h-100 btn-dark">검색</button>
        </form>
    </div>

    <div class="fw-600 hstack fs-[14//-0.56/600/#0D171B]" style="gap: var(--wv-10);">
        <p>총 회원 수 (<?php echo number_format($stats['total_users']); ?>)</p>
        <p>총 초대 수 (<?php echo number_format($stats['total_invites']); ?>)</p>
        <p>유효 가입 수 (<?php echo number_format($stats['total_valid']); ?>)</p>
    </div>

    <div class="content-inner-wrapper">
        <form name="wv-data-form" id="wv-data-form" method="post">
            <div class="table-responsive">
                <table class="table align-middle wv-data-list">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center" style="width: 80px;">순위</th>
                        <th scope="col">이름(닉네임)</th>
                        <th scope="col" style="width: 120px;">초대코드</th>
                        <th scope="col" class="text-center" style="width: 100px;">초대한 수</th>
                        <th scope="col" class="text-center" style="width: 120px;">유효 가입 수</th>
                        <th scope="col" class="text-end" style="width: 140px;">적립금 합계</th>
                        <th scope="col" class="text-center" style="width: 140px;">관리</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(count($list) > 0) { ?>
                        <?php
                        $rank = $from_record + 1;
                        foreach($list as $row) {
                            $mb_id = $row['mb_id'];
                            $mb_name = $row['mb_name'] ?? '';
                            $mb_nick = $row['mb_nick'] ?? '';
                            $invite_code = $row['invite_code'] ?? '-';
                            $invite_count = (int)$row['invite_count'];
                            $valid_count = (int)$row['valid_count'];
                            ?>
                            <tr>
                                <td class="text-center fw-bold"><?php echo number_format($rank); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo get_text($mb_name); ?></div>
                                    <div class="fs-[12/16/-0.48/400/#888888]"><?php echo get_text($mb_nick); ?></div>
                                    <div class="fs-[11/14/-0.44/400/#AAAAAA]"><?php echo $mb_id; ?></div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded"><?php echo $invite_code; ?></code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?php echo number_format($invite_count); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?php echo number_format($valid_count); ?></span>
                                </td>
                                <td class="text-end ff-Roboto-mono fw-bold">
                                    0원
                                </td>
                                <td>
                                    <div class="hstack justify-content-center gap-[6px]">
                                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                           data-wv-ajax-data='{"action":"view","made":"invite","part":"invite","field":"admin/detail","mb_id":"<?php echo $mb_id; ?>"}'
                                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[500px]" class="wv-data-list-edit-btn">[상세]</a>
                                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->plugin_url ?>/ajax.php'
                                           data-wv-ajax-data='{"action":"view","made":"invite","part":"invite","field":"admin/invited_list","mb_id":"<?php echo $mb_id; ?>"}'
                                           data-wv-ajax-option="offcanvas,end,backdrop,class: w-[600px]" class="wv-data-list-view-btn">[초대목록]</a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $rank++;
                        } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7"><div class="text-center py-5">자료가 없습니다.</div></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="bo-list-paging-wrap d-flex-center mt-[50px]">
                <?php echo $paging; ?>
            </div>
        </form>
    </div>
</div>

<script>
    $(function(){
        // 추가 기능
    });
</script>