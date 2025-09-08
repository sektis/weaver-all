<?php
/**
 * Page 플러그인 - Basic 테마 0101 스킨
 * 파일: plugins/page/theme/basic/pc/0101.php
 *
 * Location 플러그인 Map 스킨의 이벤트를 리스닝하여
 * Store Manager 매장 데이터와 연동
 */
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가



// Map 옵션 설정
$map_options = array(
    'height_wrapper' => '#content-wrapper',
    'clustering' => true,
    'map_id' => 'store-map-main',
    'initial_level' => 6,   // 초기 줌 레벨 (1~14, 숫자가 작을수록 확대)
    'min_level' => 4,       // 최소 줌 레벨 (최대 확대)
    'max_level' => 9       // 최대 줌 레벨 (최대 축소)
);
?>

<div class="wv-mx-fit" style="border-top: 1px solid #efefef">

    준비중입니다.

</div>