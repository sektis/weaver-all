<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<style>
    fieldset#captcha{
        padding: 0!important;
        display: inline-block;
    }
</style>
<script>
    // 글자수 제한
    var char_min = parseInt(<?php echo $comment_min ?>); // 최소
    var char_max = parseInt(<?php echo $comment_max ?>); // 최대
</script>

<!-- 댓글 시작 { -->
<div class="mt-5" style="">

    <h5 class="border-bottom pb-2 fs-13em">댓글 <span class="text-info"><?php echo $view['wr_comment']; ?></span></h5>

    <ul class="list-group list-group-flush">
        <?php
        $cmt_amt = count($list);
        for ($i=0; $i<$cmt_amt; $i++) {
            $comment_id = $list[$i]['wr_id'];
            $cmt_depth = strlen($list[$i]['wr_comment_reply']) * 15;
            $comment = $list[$i]['content'];
            /*
            if (strstr($list[$i]['wr_option'], "secret")) {
                $str = $str;
            }
            */
            $comment = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $comment);
            $cmt_sv = $cmt_amt - $i + 1; // 댓글 헤더 z-index 재설정 ie8 이하 사이드뷰 겹침 문제 해결
            $c_reply_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=c#bo_vc_w';
            $c_edit_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=cu#bo_vc_w';
            $is_comment_reply_edit = ($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) ? 1 : 0;
            ?>

            <li id="c_<?php echo $comment_id ?>" class="px-0 list-group-item" <?php if ($cmt_depth) { ?>style="margin-left:<?php echo $cmt_depth ?>px; "<?php } ?>>
                <div class="row align-items-start">
                    <div class="col">
                        <div class="row">
                            <div class="col-auto">
                                <div class="pf_img rounded-circle overflow-hidden"><?php echo get_member_profile_img($list[$i]['mb_id']) ?></div>
                            </div>
                            <div class="col">
                                <div class="hstack" style="gap:.5em">
                                    <?php if ($cmt_depth) { ?><i class="fas fa-reply fa-rotate-180 " ></i><?php }?>
                                    <div class="fw-600"><?php echo $list[$i]['name'] ?></div>
                                    <?php if ($is_ip_view) { ?>
                                        <span class="sound_only">아이피</span>
                                        <span>(<?php echo $list[$i]['ip']; ?>)</span>
                                    <?php } ?>

                                    <div class="fs-08em hstack align-items-center">
                                        <span class="sound_only">작성일</span>
                                        <span class="bo_vc_hdinfo text-muted "><i class="far fa-clock lh-1" aria-hidden="true"></i> <time class="" datetime="<?php echo date('Y-m-d\TH:i:s+09:00', strtotime($list[$i]['datetime'])) ?>"><?php echo $list[$i]['datetime'] ?></time></span>
                                    </div>
                                    <?php
                                    include(G5_SNS_PATH.'/view_comment_list.sns.skin.php');
                                    ?>
                                </div>
                                <div class="cmt_contents pt-2">
                                    <p class="mb-1 text-break">
                                        <?php if (strstr($list[$i]['wr_option'], "secret")) { ?><img src="<?php echo $board_skin_url; ?>/img/icon_secret.gif" alt="비밀글"><?php } ?>
                                        <?php echo $comment ?>
                                    </p>
                                    <?php if($is_comment_reply_edit) {
                                        if($w == 'cu') {
                                            $sql = " select wr_id, wr_content, mb_id from $write_table where wr_id = '$c_id' and wr_is_comment = '1' ";
                                            $cmt = sql_fetch($sql);
                                            if (isset($cmt)) {
                                                if (!($is_admin || ($member['mb_id'] == $cmt['mb_id'] && $cmt['mb_id']))) {
                                                    $cmt['wr_content'] = '';
                                                }
                                                $c_wr_content = $cmt['wr_content'];
                                            }
                                        }
                                        ?>
                                    <?php } ?>
                                </div>
                                <span id="edit_<?php echo $comment_id ?>" class="bo_vc_w"></span><!-- 수정 -->
                                <span id="reply_<?php echo $comment_id ?>" class="bo_vc_w"></span><!-- 답변 -->

                                <input type="hidden" value="<?php echo strstr($list[$i]['wr_option'],"secret") ?>" id="secret_comment_<?php echo $comment_id ?>">
                                <textarea id="save_comment_<?php echo $comment_id ?>" style="display:none"><?php echo get_text($list[$i]['content1'], 0) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <?php if($is_comment_reply_edit) { ?>
                        <div class="col-auto">
                            <div class="dropdown d-inline-block fs-09em">
                                <button class="btn" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <div class="dropdown-menu dropdown-menu-end rounded-0 py-0"  >
                                    <ul class="list-group list-group-flush fs-12px">
                                        <?php if ($list[$i]['is_reply']) { ?><li class="list-group-item"><a href="<?php echo $c_reply_href;  ?>" onclick="comment_box('<?php echo $comment_id ?>', 'c'); return false;" class="d-block d-flex align-items-center justify-content-between">답변<i class="fa fa-reply" aria-hidden="true"></i></a></li><?php } ?>
                                        <?php if ($list[$i]['is_edit']) { ?><li class="list-group-item"><a href="<?php echo $c_edit_href;  ?>" onclick="comment_box('<?php echo $comment_id ?>', 'cu'); return false;" class="d-block d-flex align-items-center justify-content-between">수정<i class="fas fa-edit" aria-hidden="true"></i></a></li><?php } ?>
                                        <?php if ($list[$i]['is_del'])  { ?><li class="list-group-item"><a href="<?php echo $list[$i]['del_link'];  ?>" onclick="return comment_delete();" class="d-block d-flex align-items-center justify-content-between">삭제<i class="far fa-trash-alt"></i></a></li><?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </li>
        <?php } ?>

        <?php if ($i == 0) { //댓글이 없다면 ?><li class="list-group-item py-5  text-center">등록된 댓글이 없습니다.</li><?php } ?>
    </ul>
</div>
<!-- } 댓글 끝 -->

<?php if ($is_comment_write) {
    if($w == '')
        $w = 'c';
    ?>
    <!-- 댓글 쓰기 시작 { -->
    <div id="bo_vc_w" class="bo_vc_w mt-5">
        <form name="fviewcomment" id="fviewcomment"  action="<?php echo $comment_action_url; ?>" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
            <input type="hidden" name="w" value="<?php echo $w ?>" id="w">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
            <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
            <input type="hidden" name="sca" value="<?php echo $sca ?>">
            <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
            <input type="hidden" name="stx" value="<?php echo $stx ?>">
            <input type="hidden" name="spt" value="<?php echo $spt ?>">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <input type="hidden" name="is_good" value="">

            <span class="sound_only">내용</span>
            <?php if ($comment_min || $comment_max) { ?><strong id="char_cnt" ><span id="char_count" class="badge badge-danger mb-1"></span> 글자</strong><?php } ?>
            <div class="form-floating">
                <textarea id="wr_content" name="wr_content" maxlength="10000" required class="form-control required" title="내용" placeholder="" <?php if ($comment_min || $comment_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?php } ?>><?php echo $c_wr_content; ?></textarea>
                <label for="wr_content">댓글 내용을 입력해주세요</label>
            </div>
            <?php if ($comment_min || $comment_max) { ?><script> check_byte('wr_content', 'char_count'); </script><?php } ?>
            <script>
                $(document).on("keyup change", "textarea#wr_content[maxlength]", function() {
                    var str = $(this).val()
                    var mx = parseInt($(this).attr("maxlength"))
                    if (str.length > mx) {
                        $(this).val(str.substr(0, mx));
                        return false;
                    }
                });
            </script>

            <?php if ($is_guest) { ?>
                <div class="row g-2 mt-2">
                    <div class="col-md">
                        <div class="form-floating">
                            <input type="text" name="wr_name" value="<?php echo get_cookie("ck_sns_name"); ?>" id="wr_name" required class="form-control required" placeholder="">
                            <label for="floatingInputGrid">이름</label>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-floating">
                            <input type="password" name="wr_password" id="wr_password" required class="form-control required" placeholder="" autocomplete="new-password">
                            <label for="floatingInputGrid">비밀번호</label>
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <?php echo $captcha_html; ?>
                </div>
            <?php }?>

            <div class="row gx-2   justify-content-end mt-2">
                <div class="col-auto align-self-center">
                    <div class="form-check  ">
                        <input class="form-check-input" type="checkbox" name="wr_secret" value="secret" id="wr_secret">
                        <label class="form-check-label" for="wr_secret"><i class="fa fa-lock" aria-hidden="true"></i></label>
                    </div>
                </div>
                <?php
                if($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) {
                    ?>
                    <div class="col-auto  ">
                        <div class="h-100 d-flex-center btn" style="background-color: #bbb">
                            <span class="sound_only">SNS 동시등록</span>
                            <span id="bo_vc_send_sns"></span>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-auto">
                    <input type="submit" id="btn_submit" class="btn btn-dark  w-100" value="댓글등록">
                </div>
            </div>



        </form>
    </div>

    <script>
        var save_before = '';
        var save_html = document.getElementById('bo_vc_w').innerHTML;


        function good_and_write()
        {s
            var f = document.fviewcomment;
            if (fviewcomment_submit(f)) {
                f.is_good.value = 1;
                f.submit();
            } else {
                f.is_good.value = 0;
            }
        }

        function fviewcomment_submit(f)
        {
            var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

            f.is_good.value = 0;

            var subject = "";
            var content = "";
            $.ajax({
                url: g5_bbs_url+"/ajax.filter.php",
                type: "POST",
                data: {
                    "subject": "",
                    "content": f.wr_content.value
                },
                dataType: "json",
                async: false,
                cache: false,
                success: function(data, textStatus) {
                    subject = data.subject;
                    content = data.content;
                }
            });

            if (content) {
                alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
                f.wr_content.focus();
                return false;
            }

            // 양쪽 공백 없애기
            var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
            document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
            if (char_min > 0 || char_max > 0)
            {
                check_byte('wr_content', 'char_count');
                var cnt = parseInt(document.getElementById('char_count').innerHTML);
                if (char_min > 0 && char_min > cnt)
                {
                    alert("댓글은 "+char_min+"글자 이상 쓰셔야 합니다.");
                    return false;
                } else if (char_max > 0 && char_max < cnt)
                {
                    alert("댓글은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                    return false;
                }
            }
            else if (!document.getElementById('wr_content').value)
            {
                alert("댓글을 입력하여 주십시오.");
                return false;
            }

            if (typeof(f.wr_name) != 'undefined')
            {
                f.wr_name.value = f.wr_name.value.replace(pattern, "");
                if (f.wr_name.value == '')
                {
                    alert('이름이 입력되지 않았습니다.');
                    f.wr_name.focus();
                    return false;
                }
            }

            if (typeof(f.wr_password) != 'undefined')
            {
                f.wr_password.value = f.wr_password.value.replace(pattern, "");
                if (f.wr_password.value == '')
                {
                    alert('비밀번호가 입력되지 않았습니다.');
                    f.wr_password.focus();
                    return false;
                }
            }

            <?php if($is_guest) echo chk_captcha_js();  ?>

            set_comment_token(f);

            document.getElementById("btn_submit").disabled = "disabled";

            return true;
        }

        function comment_box(comment_id, work)
        {
            var el_id,
                form_el = 'fviewcomment',
                respond = document.getElementById(form_el);

            // 댓글 아이디가 넘어오면 답변, 수정
            if (comment_id)
            {
                if (work == 'c')
                    el_id = 'reply_' + comment_id;
                else
                    el_id = 'edit_' + comment_id;
            }
            else
                el_id = 'bo_vc_w';

            if (save_before != el_id)
            {
                if (save_before)
                {
                    document.getElementById(save_before).style.display = 'none';
                }

                document.getElementById(el_id).style.display = '';
                document.getElementById(el_id).appendChild(respond);
                //입력값 초기화
                document.getElementById('wr_content').value = '';

                // 댓글 수정
                if (work == 'cu')
                {
                    document.getElementById('wr_content').value = document.getElementById('save_comment_' + comment_id).value;
                    if (typeof char_count != 'undefined')
                        check_byte('wr_content', 'char_count');
                    if (document.getElementById('secret_comment_'+comment_id).value)
                        document.getElementById('wr_secret').checked = true;
                    else
                        document.getElementById('wr_secret').checked = false;
                }

                document.getElementById('comment_id').value = comment_id;
                document.getElementById('w').value = work;

                if(save_before)
                    $("#captcha_reload").trigger("click");

                save_before = el_id;
            }
        }

        function comment_delete()
        {
            return confirm("이 댓글을 삭제하시겠습니까?");
        }

        comment_box('', 'c'); // 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)

        <?php if($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) { ?>

        $(function() {
            // sns 등록
            $("#bo_vc_send_sns").load(
                "<?php echo G5_SNS_URL; ?>/view_comment_write.sns.skin.php?bo_table=<?php echo $bo_table; ?>",
                function() {
                    save_html = document.getElementById('bo_vc_w').innerHTML;
                }
            );
        });
        <?php } ?>
    </script>
<?php } ?>
<!-- } 댓글 쓰기 끝 -->