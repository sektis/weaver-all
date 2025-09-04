<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$write = run_replace('wv_board_write', $write, $board);

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);
?>

<section id="bo_w" class="">
    <h2 class="sound_only"><?php echo $g5['title'] ?></h2>

    <!-- 게시물 작성/수정 시작 { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<?php echo $width; ?>">
        <input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
        <input type="hidden" name="w" value="<?php echo $w ?>">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
        <input type="hidden" name="sca" value="<?php echo $sca ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">

        <div class="vstack" style="row-gap: 1em">
            <?php
            $option = '';
            $option_hidden = '';
            if ($is_notice || $is_html || $is_secret || $is_mail) {
                $option = '';
                if ($is_notice) {
                    $option .= "\n" . '<div class="form-check form-check-inline"><input type="checkbox"  name="notice" id="notice" class="form-check-input"  value="1" ' . $notice_checked . '><label class="form-check-label" for="notice">공지</label></div>';
                }

                if ($is_html) {
                    if ($is_dhtml_editor) {
                        $option_hidden .= '<input type="hidden" value="html1" name="html">';
                    } else {
                        $option .= "\n" . '<div class="form-check form-check-inline"><input type="checkbox"  name="html" id="html" class="form-check-input"  value="' . $html_value . '" ' . $html_checked . ' onclick="html_auto_br(this);"><label class="form-check-label" for="html">HTML</label></div>';
                    }
                }

                if ($is_secret) {
                    if ($is_admin || $is_secret == 1) {
                        $option .= "\n" . '<div class="form-check form-check-inline"><input type="checkbox"  name="secret" id="secret" class="form-check-input"  value="secret" ' . $secret_checked . '><label class="form-check-label" for="secret">비밀글</label></div>';
                    } else {
                        $option_hidden .= '<input type="hidden" name="secret" value="secret">';
                    }
                }

                if ($is_mail) {
                    $option .= "\n" . '<div class="form-check form-check-inline"><input type="checkbox"  name="mail" id="mail" class="form-check-input"  value="mail" ' . $recv_email_checked . '><label class="form-check-label" for="mail">답변메일받기</label></div>';
                }
            }

            echo $option_hidden;
            ?>

            <?php if ($is_category) { ?>
                <div class="form-floating">
                    <select name="ca_name" id="ca_name" class="form-select required" required>
                        <option value="">분류를 선택하세요</option>
                        <?php echo $category_option ?>
                    </select>
                    <label for="ca_name" class="floatingSelect">분류</label>
                </div>
            <?php } ?>

            <?php if ($is_name) { ?>
                <div class="form-floating">

                    <input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="form-control required" placeholder="이름">
                    <label for="wr_name" class="floatingInput">이름</label>
                </div>
            <?php }?>

            <?php if ($is_password) { ?>
                <div class="form-floating">
                    <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="form-control <?php echo $password_required ?>" placeholder="비밀번호" autocomplete="new-password">
                    <label for="wr_password" class="floatingInput">비밀번호</label>
                </div>
            <?php }?>

            <?php if ($is_email) { ?>
                <div class="form-floating">
                    <input type="text" name="wr_email" value="<?php echo $email ?>" id="wr_email" class="form-control email " placeholder="이메일">
                    <label for="wr_email" class="floatingInput">이메일</label>
                </div>
            <?php }?>

            <?php if ($is_homepage) { ?>
                <div class="form-floating">
                    <input type="text" name="wr_homepage" value="<?php echo $homepage ?>" id="wr_homepage" class="form-control" size="50" placeholder="홈페이지">
                    <label for="wr_homepage" class="floatingInput">홈페이지</label>
                </div>
            <?php }?>

            <?php if ($option) { ?>
                <div class="align-items-center">
                    <label for="" class="sr-only">옵션</label>
                    <div class="col-md-9">
                        <?php echo $option ?>
                    </div>
                </div>
            <?php } ?>

            <div class="form-floating position-relative" style="z-index: 10">
                <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="form-control required"   placeholder="">
                <label for="wr_subject" class="floatingInput">제목</label>
                <?php if ($is_member) { // 임시 저장된 글 기능 ?>
                    <div class="dropdown position-absolute end-0 pe-3 top-50 translate-middle-y">
                        <script src="<?php echo G5_JS_URL; ?>/autosave.js"></script>
                        <?php if ($editor_content_js) echo $editor_content_js; ?>

                        <button type="button" id="btn_autosave" class="btn border " data-bs-toggle="dropdown" >임시 저장된 글 (<span id="autosave_count"><?php echo $autosave_count; ?></span>)</button>
                        <div  class="dropdown-menu dropdown-menu-end"  >

                            <div style="min-height: var(--wv-200)!important;overflow-y: auto">
                                <div id="autosave_pop" >
                                    <ul class=""></ul>
                                </div>
                            </div>
                            <button type="button" class="btn w-100">닫기</button>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="position-relative">
                <label for="wr_content" class="sr-only">내용</label>
                <div class="wr_content <?php echo $is_dhtml_editor ? $config['cf_editor'] : ''; ?>">
                    <?php if ($write_min || $write_max) { ?>
                        <!-- 최소/최대 글자 수 사용 시 -->
                        <p id="char_count_desc">이 게시판은 최소 <strong><?php echo $write_min; ?></strong>글자 이상, 최대 <strong><?php echo $write_max; ?></strong>글자 이하까지 글을 쓰실 수 있습니다.</p>
                    <?php } ?>
                    <?php echo $editor_html; // 에디터 사용시는 에디터로, 아니면 textarea 로 노출 ?>
                    <?php if ($write_min || $write_max) { ?>
                        <!-- 최소/최대 글자 수 사용 시 -->
                        <div id="char_count_wrap"><span id="char_count"></span>글자</div>
                    <?php } ?>
                </div>
            </div>

            <?php for ($i=1; $is_link && $i<=G5_LINK_COUNT; $i++) { ?>
                <div class="form-floating">
                    <input type="text" name="wr_link<?php echo $i ?>" value="<?php if($w=="u"){echo$write['wr_link'.$i];} ?>" id="wr_link<?php echo $i ?>" class="form-control" placeholder="">
                    <label for="wr_link<?php echo $i ?>"> 링크  #<?php echo $i ?></label>
                </div>
            <?php }?>

            <?php for ($i=0; $is_file && $i<$file_count; $i++) { ?>
                <div class="border p-3 rounded-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            파일 #<?php echo $i+1 ?>
                        </div>
                        <div class="col">
                            <input type="file" name="bf_file[<?php echo $i; ?>]" id="bf_file_<?php echo $i+1 ?>" title="파일첨부 <?php echo $i+1 ?> : 용량 <?php echo $upload_max_filesize ?> 이하만 업로드 가능" class="form-control form-control-sm">
                            <?php if ($is_file_content) { ?>
                                <div class="form-floating mt-2">
                                    <input type="text" name="bf_content[<?php echo $i; ?>]" value="<?php echo ($w == 'u') ? $file[$i]['bf_content'] : ''; ?>" title="파일 설명을 입력해주세요." class="form-control" placeholder="파일 설명을 입력해주세요.">
                                    <label for="floatingInput">파일 설명을 입력해주세요.</label>
                                </div>
                            <?php } ?>
                            <?php if($w == 'u' && $file[$i]['file']) { ?>
                                <div class="form-check form-check-inline mt-2 fs-09em">
                                    <input type="checkbox" id="bf_file_del<?php echo $i ?>" class="form-check-input" name="bf_file_del[<?php echo $i;  ?>]" value="1">
                                    <label class="form-check-label" for="bf_file_del<?php echo $i ?>"><?php echo $file[$i]['source'].'('.$file[$i]['size'].')';  ?> 파일 삭제</label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <?php if ($is_use_captcha) { //자동등록방지  ?>
                <div class="">
                    <label for="" class="col-md-2 text-md-right col-form-label">자동등록방지</label>
                    <div class="col-md-9">
                        <?php echo $captcha_html ?>
                    </div>
                </div>
            <?php }?>

            <div class="row justify-content-end g-2"  >
                <div class="col-6 col-lg-auto">
                    <a href="<?php echo get_pretty_url($bo_table); ?>" class="btn border btn-block w-100 board-write-cancel">취소</a>
                </div>
                <div class="col-6 col-lg-auto">
                    <input type="submit" value="작성완료" id="btn_submit" accesskey="s" class="btn btn-dark w-100 board-write-submit">
                </div>
            </div>
        </div>
    </form>

    <script>
        <?php if($write_min || $write_max) { ?>
        // 글자수 제한
        var char_min = parseInt(<?php echo $write_min; ?>); // 최소
        var char_max = parseInt(<?php echo $write_max; ?>); // 최대
        check_byte("wr_content", "char_count");

        $(function () {
            $("#wr_content").on("keyup", function () {
                check_byte("wr_content", "char_count");
            });
        });

        <?php } ?>
        function html_auto_br(obj) {
            if (obj.checked) {
                result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
                if (result)
                    obj.value = "html2";
                else
                    obj.value = "html1";
            } else
                obj.value = "";
        }

        function fwrite_submit(f) {
            <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

            var subject = "";
            var content = "";
            $.ajax({
                url: g5_bbs_url + "/ajax.filter.php",
                type: "POST",
                data: {
                    "subject": f.wr_subject.value,
                    "content": f.wr_content.value
                },
                dataType: "json",
                async: false,
                cache: false,
                success: function (data, textStatus) {
                    subject = data.subject;
                    content = data.content;
                }
            });

            if (subject) {
                alert("제목에 금지단어('" + subject + "')가 포함되어있습니다");
                f.wr_subject.focus();
                return false;
            }

            if (content) {
                alert("내용에 금지단어('" + content + "')가 포함되어있습니다");
                if (typeof (ed_wr_content) != "undefined")
                    ed_wr_content.returnFalse();
                else
                    f.wr_content.focus();
                return false;
            }

            if (document.getElementById("char_count")) {
                if (char_min > 0 || char_max > 0) {
                    var cnt = parseInt(check_byte("wr_content", "char_count"));
                    if (char_min > 0 && char_min > cnt) {
                        alert("내용은 " + char_min + "글자 이상 쓰셔야 합니다.");
                        return false;
                    } else if (char_max > 0 && char_max < cnt) {
                        alert("내용은 " + char_max + "글자 이하로 쓰셔야 합니다.");
                        return false;
                    }
                }
            }

            <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

            document.getElementById("btn_submit").disabled = "disabled";

            return true;
        }
    </script>
</section>
<!-- } 게시물 작성/수정 끝 -->