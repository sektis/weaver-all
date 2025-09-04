<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$list = run_replace('wv_hook_board_list', $list, $board);

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 99;
// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
$board_img_ratio = ( sprintf('%0.2f', $board['bo_gallery_height']/$board['bo_gallery_width']));
$board_img_ratio_mobile = ( sprintf('%0.2f', $board['bo_mobile_gallery_height']/$board['bo_mobile_gallery_width']));
?>
<!-- 게시판 목록 시작 { -->
<div id="bo_list">

    <!-- 게시판 카테고리 시작 { -->
    <?php if ($is_category) { ?>
        <div class="mb-3">
            <?php echo  wv_make_menu_display($bo_table,'common/scroll',explode('|', $board['bo_category_list']),'sca'); ?>
        </div>
    <?php }?>


    <!-- } 게시판 카테고리 끝 -->

    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">

    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <!-- 게시판 페이지 정보 및 버튼 시작 { -->
    <div class="row align-items-center justify-content-between">
        <div class="col-auto ">
            <div class="hstack text-[#4e546f] bo-list-top-left" style="gap:.5em;font-size: .92em">
                <span>Total <?php echo number_format($total_count) ?>건</span>
                <span><?php echo $page ?> 페이지</span>
            </div>
        </div>
        <div class="col-auto ">
            <div class="hstack outline-none bo-list-top-right">
                <?php if ($admin_href) { ?><a href="<?php echo $admin_href ?>" class="btn text-danger" title="관리자"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only">관리자</span></a><?php } ?>
                <?php if ($rss_href) { ?><a href="<?php echo $rss_href ?>" class="btn"><i class="fas fa-rss" aria-hidden="true"></i></a><?php } ?>
                <a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#list-search-modal"><i class="fa-solid fa-magnifying-glass"></i></a>
                <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="btn"><i class="fas fa-pen" aria-hidden="true"></i></a><?php } ?>
                <?php if ($is_checkbox) { ?>
                    <div class="dropdown">
                        <a href="#" class="btn" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul class="vstack">
                                <button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" class="btn outline-none d-flex-between">선택삭제 <i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                                <button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" class="btn outline-none d-flex-between">선택복사 <i class="fas fa-copy" aria-hidden="true"></i></button>
                                <button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" class="btn outline-none d-flex-between">선택이동 <i class="fas fa-arrows-alt" aria-hidden="true"></i></button>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- } 게시판 페이지 정보 및 버튼 끝 -->


    <div class="board-list-wrap md:mx-fit mt-[10px]">
        <table class="table border-top border-bottom align-middle">
            <caption class="visually-hidden"><?php echo $board['bo_subject'] ?> 목록</caption>
            <thead class="view-pc">
            <tr class="text-center">
                <?php if ($is_checkbox) { ?>
                    <th scope="col" class="all_chk">
                        <div class="form-check justify-content-center">
                            <input class="form-check-input" type="checkbox" value="" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);"  >
                            <label class="form-check-label visually-hidden" for="chkall">
                                <b class="sound_only">현재 페이지 게시물 전체선택</b>
                            </label>
                        </div>
                    </th>
                <?php } ?>
                <th scope="col">번호</th>
                <th scope="col" style="width: 60%">제목</th>
                <th scope="col">글쓴이</th>
                <th scope="col"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>조회</a></th>
                <?php if ($is_good) { ?><th scope="col"><?php echo subject_sort_link('wr_good', $qstr2, 1) ?>추천</a></th><?php } ?>
                <?php if ($is_nogood) { ?><th scope="col"><?php echo subject_sort_link('wr_nogood', $qstr2, 1) ?>비추천</a></th><?php } ?>
                <th scope="col"><?php echo subject_sort_link('wr_datetime', $qstr2, 1) ?>날짜</a></th>
            </tr>
            </thead>
            <tbody class="text-center">
            <?php
            for ($i = 0; $i < count($list); $i++) {
                $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);
                ?>
                <tr>
                    <?php if ($is_checkbox) { ?>
                        <td>
                            <div class="form-check justify-content-center">
                                <input class="form-check-input" name="chk_wr_id[]" type="checkbox" id="chk_wr_id_<?php echo $i ?>" value="<?php echo $list[$i]['wr_id'] ?>">
                                <label class="form-check-label visually-hidden" for="chk_wr_id_<?php echo $i ?>">현재 페이지 게시물 전체선택</label>
                            </div>
                        </td>
                    <?php } ?>

                    <td class="view-pc" data-label="번호:">
                        <?php
                        if ($list[$i]['is_notice']) // 공지사항
                            echo '<strong class="notice_icon">공지</strong>';
                        else if ($wr_id == $list[$i]['wr_id'])
                            echo "<span class=\"bo_current\">열람중</span>";
                        else
                            echo $list[$i]['num'];
                        ?>
                    </td>

                    <td class="text-start td-subject" style="<?php echo $list[$i]['reply'] ? 'padding-left:1em' : ''; ?>;max-width: 1px">
                        <div class="hstack align-items-center bo_tit" style="gap:.5em">
                            <?php if ($is_category && $list[$i]['ca_name']) { ?>
                                    <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="wv-flex-box text-white rounded-3 fs-08em"  style="background-color: #818181"><?php echo $list[$i]['ca_name'] ?></a>

                            <?php } ?>
                            <div class="hstack text-truncate" style="gap:1em" >
                                <a href="<?php echo $list[$i]['href'] ?>" class="text-truncate <?php if ($list[$i]['is_notice']) echo "fw-900"; ?>">
                                    <?php echo $list[$i]['icon_reply'] ?>
                                    <?php
                                    if (isset($list[$i]['icon_secret'])) echo rtrim($list[$i]['icon_secret']);
                                    ?>
                                    <?php echo $list[$i]['subject'] ?>
                                </a>
                                <div class="flex-shrink-1" >
                                    <div class="hstack" style="gap:.3em">
                                        <?php
                                        if ($list[$i]['icon_new']) echo "<span class=\"new_icon\">N<span class=\"sound_only\">새글</span></span>";
                                        if (isset($list[$i]['icon_hot'])) echo rtrim($list[$i]['icon_hot']);
                                        if (isset($list[$i]['icon_file'])) echo rtrim($list[$i]['icon_file']);
                                        if (isset($list[$i]['icon_link'])) echo rtrim($list[$i]['icon_link']);
                                        if ($list[$i]['comment_cnt']) echo '<div style="color:#ccc"><i class="fa-solid fa-comment-dots"></i> <span class=" ">'.$list[$i]['wr_comment'].'</span></div>';
                                        ?>

                                    </div>
                                </div>
                            </div>

                        </div>

                    </td>
                    <td class=""><?php echo $list[$i]['name'] ?></td>
                    <td class=""><div class="td-data-label"><i class="fa-regular fa-eye"></i></div><?php echo $list[$i]['wr_hit'] ?></td>
                    <?php if ($is_good) { ?><td class=""><div class="td-data-label"><i class="fa-regular fa-thumbs-up"></i></div><?php echo $list[$i]['wr_good'] ?></td><?php } ?>
                    <?php if ($is_nogood) { ?><td class=""><div class="td-data-label"><i class="fa-regular fa-thumbs-down"></i></div><?php echo $list[$i]['wr_nogood'] ?></td><?php } ?>
                    <td class=""><div class="td-data-label"><i class="fa-regular fa-clock-o"></i></div><?php echo $list[$i]['datetime2'] ?></td>

                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php if (count($list) == 0) { echo '<div><p class="text-center  py-5">게시물이 없습니다.</p></div>'; } ?>
    </div>
	<!-- 페이지 -->
    <div class="bo-list-paging-wrap d-flex-center mt-[50px]">
        <?php echo wv_get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page='));  ?>
    </div>
	<!-- 페이지 -->


    </form>
</div>

<div class="modal fade" id="list-search-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">검색</h1>
                <button type="button" class="btn-close outline-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form name="fsearch" method="get">
                    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
                    <input type="hidden" name="sca" value="<?php echo $sca ?>">
                    <input type="hidden" name="sop" value="and">
                    <select name="sfl" id="sfl" class="form-select">
                        <option value="wr_subject"<?php echo get_selected($sfl, 'wr_subject', true); ?>>제목</option>
                        <option value="wr_content"<?php echo get_selected($sfl, 'wr_content'); ?>>내용</option>
                        <option value="wr_subject||wr_content"<?php echo get_selected($sfl, 'wr_subject||wr_content'); ?>>제목+내용</option>
                        <option value="mb_id,1"<?php echo get_selected($sfl, 'mb_id,1'); ?>>회원아이디</option>
                        <option value="mb_id,0"<?php echo get_selected($sfl, 'mb_id,0'); ?>>회원아이디(코)</option>
                        <option value="wr_name,1"<?php echo get_selected($sfl, 'wr_name,1'); ?>>글쓴이</option>
                        <option value="wr_name,0"<?php echo get_selected($sfl, 'wr_name,0'); ?>>글쓴이(코)</option>
                    </select>
                    <div class="input-group mt-3">
                        <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="form-control" placeholder="검색어입력">
                        <button type="submit" value="검색" class="btn border" style="border-top-left-radius: 0 !important;border-bottom-left-radius: 0 !important;"><i class="fas fa-search" aria-hidden="true"></i><span
                                    class="sound_only">검색</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>


<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        select_copy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = g5_bbs_url+"/board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == "copy")
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = g5_bbs_url+"/move.php";
    f.submit();
}

// 게시판 리스트 관리자 옵션
jQuery(function($){
    $(".btn_more_opt.is_list_btn").on("click", function(e) {
        e.stopPropagation();
        $(".more_opt.is_list_btn").toggle();
    });
    $(document).on("click", function (e) {
        if(!$(e.target).closest('.is_list_btn').length) {
            $(".more_opt.is_list_btn").hide();
        }
    });
});
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
