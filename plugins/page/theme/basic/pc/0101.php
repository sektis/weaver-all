<?php
/**
 * Page 플러그인 - Basic 테마 0101 스킨
 * 파일: plugins/page/theme/basic/pc/0101.php
 *
 * Location 플러그인 Map 스킨의 이벤트를 리스닝하여
 * Store Manager 매장 데이터와 연동
 */
$data = array(
    'height_wrapper'=>'#content-wrapper'
);
echo wv_widget('content/map',$data);
?>

