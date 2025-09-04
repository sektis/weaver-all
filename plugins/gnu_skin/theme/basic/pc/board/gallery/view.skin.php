<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$view = run_replace('wv_hook_board_view', $view, $board);

include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);


?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<!-- 게시물 읽기 시작 { -->
<article id="bo_v">
    <header>
        <h2 id="bo_v_title" class=" " style="font-size: 1.6em;">
            <?php if ($category_name) { ?>
                <span class="wv-flex-box text-white rounded-3 fs-07em" style="background-color: #818181"><?php echo $view['ca_name']; // 분류 출력 끝 ?></span>
            <?php } ?>
            <p class="mt-[10px]"><?php echo get_text($view['wr_subject']); // 글제목 출력?></p>
        </h2>
    </header>

    <section id="bo_v_info" class="mt-2 border-bottom">
        <div class="row g-2 align-items-end justify-content-between py-2">
            <div class="col-auto">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="pf_img rounded-circle overflow-hidden"><?php echo get_member_profile_img($view['mb_id']) ?></div>
                    </div>
                    <div class="col">
                        <div class="bo_v_author"><span class="visually-hidden">작성자</span><?php echo $view['name'] ?><?php if ($is_ip_view) { echo "&nbsp;($ip)"; } ?></div>
                        <div class="hstack" style="gap:.8em">
                            <div class="bo_v_comment"><span class="visually-hidden">댓글</span><a href="#bo_vc" class="text-decoration-none text-muted"> <i class="fa-regular fa-comment-dots"></i> <?php echo number_format($view['wr_comment']) ?>건</a></div>
                            <div class="bo_v_hit"><span class="visually-hidden">조회</span><i class="fa-regular fa-eye"></i> <?php echo number_format($view['wr_hit']) ?>회</div>
                            <div class="bo_v_datetime"><span class="visually-hidden">작성일</span><i class="fa-regular fa-clock-o"></i> <?php echo date("y-m-d H:i", strtotime($view['wr_datetime'])) ?></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="hstack view-btn-wrap align-items-end" style="gap:0">
                    <div class="">
                        <a href="<?php echo $list_href ?>" class="btn " title="목록"><i class="fa fa-list" aria-hidden="true"></i></a>
                        <?php if ($reply_href) { ?><a href="<?php echo $reply_href ?>" class="btn view-pc" title="답변"><i class="fa fa-reply" aria-hidden="true"></i></a><?php } ?>
                        <?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="btn view-pc" title="글쓰기"><i class="fas fa-pen" aria-hidden="true"></i></a><?php } ?>
                    </div>

                    <?php if($write_href or $reply_href or $update_href or $delete_href or $copy_href or $move_href or $search_href){?>
                        <div class="dropdown <?php echo (!$update_href and !$delete_href and !$move_href and !$search_href)?'view-mobile':''; ?>">
                            <button class="btn" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <ul class="vstack">
                                    <?php if ($write_href) { ?><div class="view-mobile"><a class="btn d-flex-between" href="<?php echo $write_href ?>" title="글쓰기">글쓰기<i class="fas fa-pen" aria-hidden="true"></i></a></div><?php } ?>
                                    <?php if ($reply_href) { ?><div class="view-mobile"><a class="btn d-flex-between" href="<?php echo $reply_href ?>" title="답변">답변<i class="fa fa-reply" aria-hidden="true"></i></a></div><?php } ?>
                                    <?php if ($update_href) { ?><a class="btn d-flex-between" href="<?php echo $update_href ?>">수정<i class="fa-regular fa-pen-to-square"></i></a><?php } ?>
                                    <?php if ($delete_href) { ?><a class="btn d-flex-between" href="<?php echo $delete_href ?>" onclick="del(this.href); return false;">삭제<i class="fa-regular fa-trash-can"></i></a><?php } ?>
                                    <?php if ($copy_href) { ?><a class="btn d-flex-between" href="<?php echo $copy_href ?>" onclick="board_move(this.href); return false;">복사<i class="fa-regular fa-copy"></i></a><?php } ?>
                                    <?php if ($move_href) { ?><a class="btn d-flex-between" href="<?php echo $move_href ?>" onclick="board_move(this.href); return false;">이동<i class="fa-solid fa-up-down-left-right"></i></a><?php } ?>
                                    <?php if ($search_href) { ?><a class="btn d-flex-between" href="<?php echo $search_href ?>">검색<i class="fa-solid fa-magnifying-glass"></i></a><?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php }?>
                </div>
            </div>

        </div>
    </section>



    <section id="bo_v_atc">
        <div id="bo_v_share">
            <?php include_once(G5_SNS_PATH."/view.sns.skin.php"); ?>
            <?php if ($scrap_href) { ?><a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn border" onclick="win_scrap(this.href); return false;"><i class="fa fa-bookmark"></i> 스크랩</a><?php } ?>
        </div>

        <?php
        // 파일 출력
        $v_img_count = count($view['file']);
        if($v_img_count) {
            echo "<div id=\"bo_v_img\">\n";

            for ($i=0; $i<=count($view['file']); $i++) {
                if ($view['file'][$i]['view']) {
                    //echo $view['file'][$i]['view'];
                    echo get_view_thumbnail($view['file'][$i]['view']);
                }
            }

            echo "</div>\n";
        }
        ?>

        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>
        <!-- } 본문 내용 끝 -->

        <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>


        <!--  추천 비추천 시작 { -->
        <div class="hstack justify-content-center" style="gap:1em">
            <!--  추천 비추천 시작 { -->
            <?php if ( $good_href || $nogood_href) { ?>
                <?php if ($good_href) { ?>
                    <a href="<?php echo $good_href.'&amp;'.$qstr ?>" class="px-4 py-2 d-flex-center border rounded-pill" id="good_button" title="추천">
                        <i class="fa-solid fa-thumbs-up"></i> <span class="ms-2"><?php echo number_format($view['wr_good']) ?></span>
                    </a>
                <?php } ?>
                <?php if ($nogood_href) { ?>
                    <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" class="px-4 py-2 d-flex-center border rounded-pill" id="nogood_button" title="비추천">
                        <i class="fa-solid fa-thumbs-down"></i> <span class="ms-2"><?php echo number_format($view['wr_nogood']) ?></span>
                    </a>
                <?php } ?>

            <?php } else {
                if($board['bo_use_good'] || $board['bo_use_nogood']) {?>
                    <?php if($board['bo_use_good']) { ?>
                        <div class="px-4 py-2 d-flex-center border rounded-pill" title="추천"><i class="fa-solid fa-thumbs-up"></i> <span class="ms-2"><?php echo number_format($view['wr_good']) ?></span></div>
                    <?php } ?>

                    <?php if($board['bo_use_nogood']) { ?>
                        <div class="px-4 py-2 d-flex-center border rounded-pill" title="비추천"><i class="fa-solid fa-thumbs-down"></i> <span class="ms-2"><?php echo number_format($view['wr_nogood']) ?></span></div>
                    <?php } ?>
                    <?php
                }
            }
            ?>
            <!-- }  추천 비추천 끝 -->
        </div>
        <!-- }  추천 비추천 끝 -->
    </section>

    <?php
    $cnt = 0;
    if ($view['file']['count']) {
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                $cnt++;
        }
    }
    ?>

    <?php if($cnt) { ?>
        <!-- 첨부파일 시작 { -->
        <div class="mt-3">
            <?php
            // 가변 파일
            for ($i=0; $i<count($view['file']); $i++) {
                if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
                    ?>
                    <div class="rounded-3 border my-2 p-2 fs-sm-12px">
                        <div class="row align-items-center ">
                            <div class="col-auto">
                                <i class="fa-regular fa-folder-open text-muted fs-15em px-2"></i>
                            </div>
                            <div class="col">
                                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                                    <strong><?php echo $view['file'][$i]['source'] ?></strong> <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                                </a>
                                <p class="text-muted fs-09em"><?php echo $view['file'][$i]['download'] ?>회 다운로드 | DATE : <?php echo $view['file'][$i]['datetime'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <!-- } 첨부파일 끝 -->
    <?php } ?>

    <?php if(isset($view['link'][1]) && $view['link'][1]) { ?>
        <!-- 관련링크 시작 { -->
        <div class="mt-3">
            <?php
            // 링크
            $cnt = 0;
            for ($i=1; $i<=count($view['link']); $i++) {
                if ($view['link'][$i]) {
                    $cnt++;
                    $link = cut_str($view['link'][$i], 70);
                    ?>
                    <div class="rounded-3 border my-2 p-2 fs-sm-12px">
                        <div class="row align-items-center ">
                            <div class="col-auto">
                                <i class="fa-solid fa-link text-muted fs-15em px-2"></i>
                            </div>
                            <div class="col">
                                <a href="<?php echo $view['link_href'][$i] ?>" target="_blank">
                                    <strong><?php echo $link ?></strong>
                                </a>
                                <p class="text-muted"><?php echo $view['link_hit'][$i] ?>회 연결</p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <!-- } 관련링크 끝 -->
    <?php } ?>

    <?php if ($prev_href || $next_href) { ?>
        <div class="vstack border-bottom mt-5">
            <?php if ($prev_href) { ?>
                <div class="border-top px-2 py-3">
                    <div class="row">
                        <div class="col-auto">  <span class="nb_tit"><i class="text-secondary fas fa-chevron-up me-1" aria-hidden="true"></i> 이전글</span></div>
                        <div class="col text-truncate">   <a class="text-dark text-truncate" href="<?php echo $prev_href ?>"><?php echo $prev_wr_subject;?></a></div>
                        <div class="col-auto"><span class="nb_date"><?php echo str_replace('-', '.', substr($prev_wr_date, '2', '8')); ?></span></div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($next_href) { ?>
                <div class="border-top px-2 py-3">
                    <div class="row">
                        <div class="col-auto"> <span class="nb_tit"><i class="text-secondary fas fa-chevron-down me-1" aria-hidden="true"></i> 다음글</span></div>
                        <div class="col text-truncate"> <a class="text-dark text-truncate" href="<?php echo $next_href ?>"><?php echo $next_wr_subject;?></a></div>
                        <div class="col-auto"><span class="nb_date"><?php echo str_replace('-', '.', substr($next_wr_date, '2', '8')); ?></span></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>


    <?php
    // 코멘트 입출력
    include_once(G5_BBS_PATH.'/view_comment.php');
    ?>
</article>
<!-- } 게시판 읽기 끝 -->

<script>
    <?php if ($board['bo_download_point'] < 0) { ?>
    $(function() {
        $("a.view_file_download").click(function() {
            if(!g5_is_member) {
                alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
                return false;
            }

            var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

            if(confirm(msg)) {
                var href = $(this).attr("href")+"&js=on";
                $(this).attr("href", href);

                return true;
            } else {
                return false;
            }
        });
    });
    <?php } ?>

    function board_move(href)
    {
        window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
    }
</script>

<script>
    $(function() {
        $("a.view_image").click(function() {
            window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
            return false;
        });

        // 추천, 비추천
        $("#good_button, #nogood_button").click(function() {
            var $tx;
            if(this.id == "good_button")
                $tx = $("#bo_v_act_good");
            else
                $tx = $("#bo_v_act_nogood");

            excute_good(this.href, $(this), $tx);
            return false;
        });

        // 이미지 리사이즈
        $("#bo_v_atc").viewimageresize();
    });

    function excute_good(href, $el, $tx)
    {
        $.post(
            href,
            { js: "on" },
            function(data) {
                if(data.error) {
                    alert(data.error);
                    return false;
                }

                if(data.count) {
                    $el.find("strong").text(number_format(String(data.count)));
                    if($tx.attr("id").search("nogood") > -1) {
                        $tx.text("이 글을 비추천하셨습니다.");
                        $tx.fadeIn(200).delay(2500).fadeOut(200);
                    } else {
                        $tx.text("이 글을 추천하셨습니다.");
                        $tx.fadeIn(200).delay(2500).fadeOut(200);
                    }
                }
            }, "json"
        );
    }
</script>
<!-- } 게시글 읽기 끝 -->