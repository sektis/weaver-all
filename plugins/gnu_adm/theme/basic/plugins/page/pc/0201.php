<?php
$page_title = '매장 관리';
$sfl_options = array(
    'mb_id'    => '아이디',
    'mb_name'  => '대표자이름',
);

$store_result = wv()->store_manager->made()->get_list();

$list = $store_result['list'];
?>

<div class="wv-vstack">
    <div class="page-top-menu">
        <a href="<?php echo wv_page_url('0201_c'); ?>" class="top-menu-btn"><i class="fa-solid fa-plus"></i> 신규등록</a>
    </div>

    <div class="hstack justify-content-between">
        <div class="fw-600 hstack fs-[14//-0.56/600/#0D171B]" style="gap:var(--wv-10)">
            <p>등록된 매장 수(<?php echo number_format($store_result['total_count']); ?>)</p>
        </div>

        <form method="get" action="<?php echo wv_page_url('0101'); ?>" class="hstack" style="gap: var(--wv-8);">
            <input type="hidden" name="wv_page_id" value="0101">

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

        <form name="wv-data-form" id="wv-data-form"  method="post" class=""  >
            <input type="hidden" name="page" value="<?php echo $page ?>">

            <div class="wv-data-list mx-fit mt-[16px]">
                <table class="table wv-table border-top border-bottom align-middle">
                    <caption class="visually-hidden"><?php echo $board['bo_subject'] ?> 목록</caption>
                    <thead class="view-pc">
                    <tr class="text-center">
                        <th scope="col" >아이디</th>
                        <th scope="col" >대표자 이름</th>
                        <th scope="col">매장명</th>
                        <th scope="col">업종</th>
                        <th scope="col">지역</th>
                        <th scope="col">상품</th>
                        <th scope="col">등록일</th>
                        <th scope="col">서비스 등록 여부</th>
                        <th scope="col">관리</th>

                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php
                    for ($i = 0; $i < count($list); $i++) {
                        ?>
                        <tr>
                            <td><?php echo $list[$i]['mb_id']; ?></td>
                            <td><?php echo $list[$i]['mb_name']; ?></td>
                            <td class="text-start" >
                                <div class="hstack align-items-center bo_tit" style="gap:.5em">
                                    <div class="hstack text-truncate" style="gap:1em" >
                                        <p class="text-truncate">
                                            <?php echo $list[$i]['subject'] ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?php echo $list[$i]['wr_datetime']; ?></td>
                            <td></td>
                            <td><a href="<?php echo wv_page_url('0201_c','wr_id='.$list[$i]['wr_id']); ?>">수정</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php if (count($list) == 0) { echo '<div><p class="text-center  py-5">자료가 없습니다.</p></div>'; } ?>
            </div>
            <!-- 페이지 -->
            <div class="bo-list-paging-wrap d-flex-center mt-[50px]">
                <?php echo wv_get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page='));  ?>
            </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function (){

    })
</script>
