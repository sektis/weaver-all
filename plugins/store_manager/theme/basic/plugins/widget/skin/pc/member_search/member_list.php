<?php
include_once '_common.php';
if(!wv_is_ajax()){
    die();
}

$page_title = '사장님 관리';
$sfl_options = array(
    'jm.mb_id'    => '아이디',
    'jm.mb_name'  => '대표자이름',
);
$sfl_whitelist = array_keys($sfl_options);

$sfl = isset($_REQUEST['sfl']) && in_array($_REQUEST['sfl'], $sfl_whitelist) ? $_REQUEST['sfl'] : 'mb_id';
$stx = isset($_REQUEST['stx']) ? trim($_REQUEST['stx']) : '';

$get_list_where = array();
if($stx !== ''){
    $stx_esc = sql_escape_string($stx);
    if($sfl && in_array($sfl, $sfl_whitelist)){
        $get_list_where[] = "   {$sfl} LIKE '%{$stx_esc}%' ";
    }
}


$get_list_option = array(
    'where'=>$get_list_where,
    'where_member' => array(
        array('is_ceo' => "=1")
    ),
    'page'=>$page,
    'rows'=>10,
    'list_url'=>wv_path_replace_url(__FILE__)."?"
);
$result = wv()->store_manager->made('member')->get_list($get_list_option);
$list = $result['list'];

?>
<div class="wv-vstack h-100">


    <div class="col-auto w-full">


        <form method="post" action="<?php echo wv_path_replace_url(__FILE__); ?>" class=" " style="gap: var(--wv-8);">
            <input type="hidden" name="wv_page_id" value="0101">

            <div class="hstack w-full justify-content-between " style="gap:var(--wv-10)">
                <div class="form-floating" style="min-width:180px;">
                    <select name="sfl" id="sfl" class="form-select" required aria-label="검색필드 선택">
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

                <button type="submit" class="btn border h-100 btn-dark col">검색</button>
            </div>
        </form>
    </div>

    <div class="  col" style="overflow-y: auto">

            <div class="table-responsive">
                <table class="table table-hover align-middle wv-data-list">
                    <thead>
                    <tr>
                        <th>아이디</th>
                        <th>대표자 이름</th>
                        <th class="text-center w-[140px]">관리</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(count($list) > 0){ ?>
                        <?php for($i=0; $i<count($list); $i++){ ?>
                            <tr>
                                <td><?php echo get_text($list[$i]['mb_id']); ?></td>
                                <td><?php echo get_text($list[$i]['jm_mb_name']); ?></td>
                                <td>
                                    <div class="hstack justify-content-center gap-[6px]">
                                        <a href="#" data-mb-id="<?php echo $list[$i]['mb_id']; ?>" data-mb-name="<?php echo $list[$i]['jm_mb_name']; ?>"
                                           class="select-member  ">선택</a>
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

    </div>
</div>

<script>

    $(function(){
        // 필요 시 스크립트 추가
    });
</script>
