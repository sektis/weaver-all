<?php
$page_title='타이틀';
$sfl_options = array(
    'mb_id'    => '아이디',
    'mb_name'  => '대표자이름',
);
?>

<div class="wv-vstack">

    <div class="page-top-menu">
        <a href="" class="top-menu-btn"><i class="fa-solid fa-plus"></i> 신규등록</a>
    </div>

    <div class="hstack justify-content-between">
        <div class="fw-600 hstack fs-[14//-0.56/600/#0D171B]" style="gap:var(--wv-10)">
            <p>등록된 매장 수(0명)</p>
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

        <form name="wv-data-form" id="wv-data-form"  method="post" class=" ">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <div class="wv-data-list mx-fit mt-[16px]">
                <table class="table wv-table border-top border-bottom align-middle">
                    <caption class="visually-hidden"><?php echo $board['bo_subject'] ?> 목록</caption>
                    <thead>
                    <tr class="text-center">
                        <th scope="col" class="all_chk" style="width: var(--wv-62)">
                            <div class="form-check justify-content-center">
                                <label for="chkall" class="sound_only">현재 페이지 데이터 전체선택</label>
                                <input class="form-check-input" type="checkbox" value="1" id="chkall"  >
                            </div>
                        </th>
                        <th scope="col" >번호</th>
                        <th scope="col" style="width: 60%">제목</th>
                        <th scope="col">글쓴이</th>

                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php
                    for ($i = 0; $i < count($list); $i++) {
                        ?>
                        <tr>
                            <td>
                                <div class="form-check justify-content-center">
                                    <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
                                    <input class="form-check-input" name="chk[]" type="checkbox" id="chk_<?php echo $i ?>" value="<?php echo $i ?>">
                                    <label class="form-check-label visually-hidden" for="chk_<?php echo $i ?>">현재 페이지 자료 전체선택</label>
                                </div>
                            </td>
                            <td></td>
                            <td class="text-start"  >
                                <div class="hstack align-items-center bo_tit" style="gap:.5em">

                                </div>
                            </td>
                            <td></td>
                            <td></td>
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
