<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$list = run_replace('wv_hook_board_list', $list, $board);

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 99;

if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;
$cols_width = number_format(100/$board['bo_gallery_cols'],3);

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
$board_img_ratio = ( sprintf('%0.2f', $board['bo_gallery_height']/$board['bo_gallery_width']));
$board_img_ratio_mobile = ( sprintf('%0.2f', $board['bo_mobile_gallery_height']/$board['bo_mobile_gallery_width']));
?>
<style>
    .col-item .ratio{--bs-aspect-ratio:<?php echo $board_img_ratio_mobile*100; ?>%}
    @media (min-width: 992px) {
        .col-item{width:<?php echo $cols_width ?>%}
        .col-item .ratio{--bs-aspect-ratio:<?php echo $board_img_ratio*100; ?>%}
    }
</style>
<!-- 게시판 목록 시작 { -->
<div id="bo_gall">

    <!-- 게시판 카테고리 시작 { -->
    <?php if ($is_category) { ?>
        <div class="mb-3">
            <?php  echo  wv_make_menu_display($bo_table,'common/scroll',explode('|', $board['bo_category_list']),'sca'); ?>
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
        <?php if ($is_checkbox) { ?>

            <div class="form-check form-check-inline mt-3">
                <input type="checkbox" id="chkall" class="form-check-input" onclick="if (this.checked) all_checked(true); else all_checked(false);">
                <label class="form-check-label" for="chkall">전체 선택 </label>
            </div>

        <?php } ?>

        <div class="board-list-wrap mt-[10px]">
            <div class="row" style="--bs-gutter-x: 3em;--bs-gutter-y: 3em">
                <?php for ($i = 0; $i < count($list); $i++) {
                    $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);

                    $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);

                    if ($thumb['src']) {
                        $img_src = $thumb['src'];
                    } else {
                        $yt_thumb = wv_get_youtube_thumb($list[$i]['wr_link1']);
                        if($yt_thumb){
                            $img_src = $yt_thumb;
                        }else{
                            $img_src = G5_IMG_URL . '/no_img.png';
                        }

                    }

                    $img_src = run_replace('thumb_image_tag', $img_src, $thumb);
                    ?>

                    <div class="col-item col-6 col-lg-auto"  >

                        <div class="position-relative">
                            <?php if ($is_checkbox) { ?>
                                <div class="form-check form-check-inline mb-2">
                                    <input type="checkbox" name="chk_wr_id[]" class="form-check-input" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                                    <label class="form-check-label" for="chk_wr_id_<?php echo $i ?>">이 게시물 선택</label>
                                </div>
                            <?php } ?>

                            <span class="visually-hidden">
                                <?php
                                if ($wr_id == $list[$i]['wr_id'])
                                    echo "<span class=\"bo_current\">열람중</span>";
                                else
                                    echo $list[$i]['num'];
                                ?>
                            </span>

                            <div class="position-relative">
                                <div class="ratio " style="border-radius: var(--wv-20);overflow: hidden;">
                                    <div>
                                        <img src="<?php echo $img_src; ?>" class="wh-100 object-fit-cover"   alt="">
                                    </div>
                                </div>

                                <div class="hstack align-items-center bo_tit mt-[1em]" style="gap:.5em">
                                    <?php if ($is_category && $list[$i]['ca_name']) { ?>
                                        <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="wv-flex-box text-white rounded-3"  style="background-color: #818181"><?php echo $list[$i]['ca_name'] ?></a>
                                    <?php } ?>
                                    <div class="hstack text-truncate" >
                                        <a href="<?php echo $list[$i]['href'] ?>" class="text-truncate <?php if ($list[$i]['is_notice']) echo "fw-900"; ?>">
                                            <?php echo $list[$i]['icon_reply'] ?>
                                            <?php
                                            if (isset($list[$i]['icon_secret'])) echo rtrim($list[$i]['icon_secret']);
                                            ?>
                                            <?php echo $list[$i]['subject'] ?>
                                        </a>
                                        <div class="flex-shrink-1  ms-3  " >
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
                                <p class="wv-line-clamp fs-09em text-[#acacac]"><?php echo utf8_strcut(strip_tags($list[$i]['wr_content']), 68, '..'); ?></p>

                                <div class="hstack justify-content-between text-[#777] mt-[10px]"  >
                                    <div class="hstack align-items-center" style="gap:.5em">
                                        <p><span class="sound_only">작성자 </span><?php echo $list[$i]['name'] ?></p>
                                        <p><i class="fa fa-clock-o fs-08em" aria-hidden="true"></i> <?php echo $list[$i]['datetime2'] ?></p>
                                        <p><i class="fa fa-eye fs-08em" aria-hidden="true"></i> <?php echo $list[$i]['wr_hit'] ?></p>
                                    </div>
                                    <div class="hstack align-items-center" style="gap:.5em">
                                        <?php if ($is_good) { ?><p><i class="fa fa-thumbs-o-up fs-08em" aria-hidden="true"></i> <?php echo $list[$i]['wr_good'] ?></p><?php } ?>
                                        <?php if ($is_nogood) { ?><p><i class="fa fa-thumbs-o-down fs-08em" aria-hidden="true"></i> <?php echo $list[$i]['wr_nogood'] ?></p><?php } ?>
                                    </div>
                                </div>


                                <a href="<?php echo $list[$i]['href'] ?>" class="stretched-link"></a>
                            </div>
                        </div>


                    </div>


                <?php } ?>
                <?php if (count($list) == 0) {
                    echo "<div class=\"col text-center py-5\">게시물이 없습니다.</div>";
                } ?>

            </div>
        </div>
        <!-- 페이지 -->
        <div class="bo-list-paging-wrap d-flex-center mt-[50px]">
            <?php echo wv_get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page='));  ?>
        </div>
        <!-- 페이지 -->


    </form>
</div>

<div class="modal fade" id="list-search-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
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
