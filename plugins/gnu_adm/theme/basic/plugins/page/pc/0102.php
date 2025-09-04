<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$page_title = '일반 사용자 관리';
$sfl_options = array(
    'mb_id'    => '아이디',
    'mb_name'  => '대표자이름',
);
$sfl_whitelist = array_keys($sfl_options);

$sfl = isset($_GET['sfl']) && in_array($_GET['sfl'], $sfl_whitelist) ? $_GET['sfl'] : 'mb_id';
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';

$sst_whitelist = array('mb_datetime','mb_today_login','mb_id','mb_nick','mb_name');
$sst = isset($_GET['sst']) && in_array($_GET['sst'], $sst_whitelist) ? $_GET['sst'] : 'mb_datetime';
$sod = (isset($_GET['sod']) && strtolower($_GET['sod']) === 'asc') ? 'asc' : 'desc';

$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$rows = isset($config['cf_page_rows']) && (int)$config['cf_page_rows'] > 0 ? (int)$config['cf_page_rows'] : 20;

$sql_common = " FROM {$g5['member_table']} AS m ";
$sql_where  = " WHERE m.mb_level = '2' ";

if($stx !== ''){
    $stx_esc = sql_escape_string($stx);
    if($sfl && in_array($sfl, $sfl_whitelist)){
        $sql_where .= " AND m.{$sfl} LIKE '%{$stx_esc}%' ";
    }else{
        $sql_where .= " AND ( m.mb_id   LIKE '%{$stx_esc}%'
                          OR   m.mb_name LIKE '%{$stx_esc}%'
                          OR   m.mb_nick LIKE '%{$stx_esc}%'
                          OR   m.mb_email LIKE '%{$stx_esc}%'
                          OR   m.mb_hp    LIKE '%{$stx_esc}%' ) ";
    }
}

/* 총 건수 (현재 검색/필터 반영) */
$row = sql_fetch(" SELECT COUNT(*) AS cnt {$sql_common} {$sql_where} ");
$total_count = (int)$row['cnt'];

/* 레벨3 + 탈퇴 회원 수 (현재 검색/필터 반영) */
$left_where = $sql_where." AND m.mb_leave_date <> '' ";
$left_row = sql_fetch(" SELECT COUNT(*) AS cnt {$sql_common} {$left_where} ");
$left_count = (int)$left_row['cnt'];

$total_page  = $total_count ? ceil($total_count / $rows) : 1;
$from_record = ($page - 1) * $rows;

$orderby = " ORDER BY m.{$sst} {$sod} ";

$sql = " SELECT m.* {$sql_common} {$sql_where} {$orderby} LIMIT {$from_record}, {$rows} ";
$result = sql_query($sql);

$list = array();
for($i=0; $row = sql_fetch_array($result); $i++){
    $status = '정상';
    if($row['mb_leave_date']) $status = '탈퇴';
    else if($row['mb_intercept_date']) $status = '차단';

    $list[$i] = array(
        'mb_id'          => $row['mb_id'],
        'mb_name'        => $row['mb_name'],
        'mb_nick'        => $row['mb_nick'],
        'mb_email'       => $row['mb_email'],
        'mb_hp'          => $row['mb_hp'],
        'mb_datetime'    => substr($row['mb_datetime'], 0, 10),
        'mb_today_login' => substr($row['mb_today_login'], 0, 16),
        'mb_point'       => (int)$row['mb_point'],
        'status'         => $status,
        'profile_url'    => G5_BBS_URL.'/profile.php?mb_id='.urlencode($row['mb_id']),
    );
}

$qarr = array(
    'sfl' => $sfl,
    'stx' => $stx,
    'sst' => $sst,
    'sod' => $sod
);
$qstr_for_paging = http_build_query(array_filter($qarr));
$list_url = wv_page_url('0102', $qstr_for_paging);
$write_pages = isset($config['cf_write_pages']) && (int)$config['cf_write_pages'] > 0 ? (int)$config['cf_write_pages'] : 5;
$paging = wv_get_paging($write_pages, $page, $total_page, $list_url);

function sort_link($field, $cur_field, $cur_order){
    $next = ($cur_field === $field && strtolower($cur_order) === 'desc') ? 'asc' : 'desc';
    $q = $_GET;
    $q['sst'] = $field;
    $q['sod'] = $next;
    return wv_page_url('0102', http_build_query($q));
}
?>
<div class="wv-vstack">



    <div class="hstack justify-content-between">
        <div class="fw-600 hstack fs-[14//-0.56/600/#0D171B]" style="gap:var(--wv-10)">
            <p>등록된 회원 수(<?php echo number_format($total_count); ?>명)</p>
            <p>탈퇴 회원 수(<?php echo number_format($left_count); ?>명)</p>
        </div>

        <form method="get" action="<?php echo wv_page_url('0102'); ?>" class="hstack" style="gap: var(--wv-8);">
            <input type="hidden" name="wv_page_id" value="0102">

            <div class="form-floating" style="min-width:180px;">
                <select name="sfl" id="sfl" class="form-select" aria-label="검색필드 선택">
                    <option value=""<?php echo $sfl===''?' selected':''; ?>>검색필드 선택</option>
                    <?php
                    foreach($sfl_options as $key => $label){
                        $sel = ($key === $sfl) ? ' selected' : '';
                        echo '<option value="'.$key.'"'.$sel.'>'.get_text($label).'</option>';
                    }
                    ?>
                </select>
                <label for="sfl" class="floatingSelect">검색필드</label>
            </div>

            <div class="form-floating position-relative" style="z-index:10; min-width:220px;">
                <input type="text" name="stx" id="stx" value="<?php echo get_text($stx); ?>" class="form-control" placeholder="검색어를 입력하세요">
                <label for="stx" class="floatingInput">검색어</label>
            </div>

            <button type="submit" class="btn border h-100 btn-dark">검색</button>
        </form>
    </div>

    <div class="content-inner-wrapper">
        <form name="wv-data-form" id="wv-data-form" method="post">
            <div class="table-responsive">
                <table class="table table-hover align-middle wv-data-list">
                    <thead>
                    <tr>
                        <th>이름</th>
                        <th>아이디</th>
                        <th>전화번호</th>
                        <th>가입일자</th>
                        <th>누적 적립금</th>
                        <th>잔여 적립금</th>
                        <th>계정 상태</th>
                        <th>관리</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(count($list) > 0){ ?>
                        <?php for($i=0; $i<count($list); $i++){ ?>
                            <tr>
                                <td><?php echo get_text($list[$i]['mb_name']); ?></td>
                                <td><?php echo get_text($list[$i]['mb_id']); ?></td>
                                <td><?php echo $list[$i]['mb_hp']; ?></td>
                                <td><?php echo get_text($list[$i]['mb_datetime']); ?></td>
                                <td>0</td>
                                <td>0</td>
                                <td></td>
                                <td><a href="<?php echo wv_page_url('0102_c','w=u&mb_id='.$list[$i]['mb_id']); ?>">수정</a> </td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <tr>
                            <td colspan="9"><div class="text-center py-5">자료가 없습니다.</div></td>
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
        // 필요 시 스크립트 추가
    });
</script>
