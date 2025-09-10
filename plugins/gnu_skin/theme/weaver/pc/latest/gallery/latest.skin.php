<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
$list = run_replace('wv_hook_board_list', $list, $board);
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
$thumb_width = 297;
$thumb_height = 212;
$thumb_img_ratio = ( sprintf('%0.2f', $thumb_height/$thumb_width));
$list_count = (is_array($list) && $list) ? count($list) : 0;
?>
<div id="<?php echo $skin_id?>" class="wv-skin-latest position-relative  " style="" >
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative" style="">
        <div class="hstack align-items-end justify-content-between">
            <p class="fw-500 fs-13em"><?php echo $board['bo_subject']; ?></p>
            <a href="<?php echo get_pretty_url($bo_table); ?>">더보기</a>
        </div>
        <div class="row mt-[10px]" style="--bs-gutter-x: 3em;--bs-gutter-y: 3em">
            <?php for ($i=0; $i<count($list); $i++) {
                $list[$i] = run_replace('wv_hook_board_list_i', $list[$i], $i);

                $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);

                if($thumb['src']) {
                    $img = $thumb['src'];
                } else {
                    $img = G5_IMG_URL.'/no_img.png';
                }
                ?>
                <div class="gall-col col-6 col-lg text-truncate"  >
                    <div class="position-relative">
                        <div class="ratio " style="border-radius: var(--wv-20);overflow: hidden;--bs-aspect-ratio:<?php echo $thumb_img_ratio*100; ?>%">
                            <div>
                                <img src="<?php echo $img; ?>" class="wh-100 object-fit-cover"   alt="">
                            </div>
                        </div>

                        <div class="hstack text-truncate mt-[10px]" style="gap:.3em" >
                            <p class="text-truncate">
                                <?php
                                if (isset($list[$i]['icon_secret'])) echo rtrim($list[$i]['icon_secret']);
                                ?>
                                <?php echo $list[$i]['subject'] ?>
                            </p>
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

                        <div class="hstack align-items-center mt-[10px]" style="gap:1em">
                            <p><span class="lt_nick"><?php echo $list[$i]['name'] ?></span></p>
                            <p><?php echo $list[$i]['datetime'] ?></p>
                        </div>

                        <a href="<?php echo $list[$i]['href'] ?>" class="stretched-link"></a>
                    </div>


                </div>

            <?php }?>
            <?php if ($list_count == 0) { //게시물이 없을 때  ?>
                <div class="empty_li">게시물이 없습니다.</div>
            <?php }  ?>
        </div>
    </div>

    <script>
        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

        })
    </script>
</div>