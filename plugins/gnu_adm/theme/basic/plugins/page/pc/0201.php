<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$page_title = '매장 관리';
$sfl_options = array(
    'jm_mb_id'    => '아이디',
    'jm_mb_name'  => '대표자이름',
);
$sfl_whitelist = array_keys($sfl_options);

$sfl = isset($_GET['sfl']) && in_array($_GET['sfl'], $sfl_whitelist) ? $_GET['sfl'] : 'mb_id';
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';

$get_list_where = array();
if($stx !== ''){
    $stx_esc = sql_escape_string($stx);
    if($sfl && in_array($sfl, $sfl_whitelist)){
        $get_list_where[] = "   {$sfl} LIKE '%{$stx_esc}%' ";
    }
}


$get_list_option = array(
    'where'=>$get_list_where,

    'where_location' =>    array(
        'and'=>array(
                array('lat'=>"<>''"),
                array('lng'=>"<>''"),
        )
    ),

    'page'=>$page,
    'rows'=>20,
    'list_url'=>wv_page_url($wv_page_id, $qstr)
);
$result = wv()->store_manager->made('sub01_01')->get_list($get_list_option);
$list = $result['list'];

?>
<div class="wv-vstack">

    <div class="page-top-menu">
        <a href="<?php echo wv_page_url('0201_c'); ?>" class="top-menu-btn"><i class="fa-solid fa-plus"></i> 신규등록</a>
    </div>

    <div class="hstack justify-content-between">
        <div class="fw-600 hstack fs-[14//-0.56/600/#0D171B]" style="gap:var(--wv-10)">
            <p>등록된 매장 수(<?php echo number_format($result['total_count']); ?>명)</p>
        </div>

        <form method="get" action="<?php echo wv_page_url($wv_page_id); ?>" class="hstack" style="gap: var(--wv-8);">
            <input type="hidden" name="wv_page_id" value="0102">

            <div class="form-floating" style="min-width:180px;">
                <select name="sfl" id="sfl" required class="form-select" aria-label="검색필드 선택">
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
                        <th scope="col" >아이디</th>
                        <th scope="col" >대표자 이름</th>
                        <th scope="col">매장명</th>
                        <th scope="col">업종</th>
                        <th scope="col">지역</th>
                        <th scope="col">상품</th>
                        <th scope="col">등록일</th>
                        <th scope="col">서비스 등록 여부</th>
                        <th class="text-center w-[140px]">관리</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(count($list) > 0){ ?>
                        <?php for($i=0; $i<count($list); $i++){ ?>
                            <tr>
                                <td><?php echo $list[$i]['mb_id']; ?></td>
                                <td><?php echo $list[$i]['jm_mb_name']; ?></td>
                                <td class="text-start" >
                                    <div class="hstack align-items-center bo_tit" style="gap:.5em">
                                        <div class="hstack text-truncate" style="gap:1em" >
                                            <p class="text-truncate">
                                                <?php echo $list[$i]['store']['name'] ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $list[$i]['store']['category_text'] ?></td>
                                <td><?php echo $list[$i]['location']['region_name_full'] ?></td>
                                <td><?php echo $list[$i]['contract']['cont_pdt_type_text'] ?></td>
                                <td><?php echo $list[$i]['wr_datetime']; ?></td>
                                <td></td>
                                <td>
                                    <div class="hstack justify-content-center gap-[6px]">
                                        <a href="#" data-wv-ajax-url='<?php echo wv()->store_manager->made()->plugin_url?>/ajax.php?made=sub01_01&action=delete&wr_id=<?php echo $list[$i]['wr_id']; ?>'
                                           class="wv-data-list-delete-btn  ">[삭제]</a>
                                        <a href="<?php echo wv_page_url('0201_c','wr_id='.$list[$i]['wr_id']); ?>" class="wv-data-list-edit-btn">[수정]</a>
                                    </div>
                                </td>
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
                <?php echo $result['paging']; ?>
            </div>
        </form>
    </div>
</div>

<script>

    $(function(){
        // 필요 시 스크립트 추가
    });
</script>
