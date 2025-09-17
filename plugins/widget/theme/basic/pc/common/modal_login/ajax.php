<?php
include_once '_common.php';
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
$view= get_write($write_table,$wr_id);

$youtube_id = wv_get_youtube_id($view['wr_1'],true);

?>
<div class="position-relative">
	<div>
      <?php echo wv_movie_display($view['wr_1']);?>
    </div>


</div>
