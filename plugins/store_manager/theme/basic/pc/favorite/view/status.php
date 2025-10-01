<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * Favorite 파트 - 찜하기 하트 버튼
 *
 * 사용법:
 * <?php echo $favorite_manager->render_part('status', 'view', array(
 *     'row' => array('favorite' => $favorite_data),
 *     'mb_id' => $member['mb_id'],
 *     'store_wr_id' => $store_wr_id
 * )); ?>
 *
 * $favorite_data가 있으면 찜된 상태, 없으면 찜 안된 상태
 */

// 변수 설정
$mb_id = isset($mb_id) ? $mb_id : (isset($member['mb_id']) ? $member['mb_id'] : '');
$store_wr_id = isset($store_wr_id) ? $store_wr_id : 0;

// 찜 여부 확인
$is_favorited = false;
$favorite_id = 0;

if (isset($row['favorite']) && is_array($row['favorite'])) {
    // 목록 파트이므로 배열 형태로 올 수 있음
    if (isset($row['favorite'][0])) {
        $is_favorited = true;
        $favorite_id = isset($row['favorite'][0]['id']) ? $row['favorite'][0]['id'] : 0;
    }
} elseif (isset($row['id']) && $row['id']) {
    // 직접 전달된 경우
    $is_favorited = true;
    $favorite_id = $row['id'];
}

// AJAX URL
$ajax_url = wv()->store_manager->plugin_url . '/ajax.php';
?>

<div id="<?php echo $skin_id; ?>" class="wv-favorite-heart">
    <style>
        <?php echo $skin_selector; ?> { display:inline-block; cursor:pointer; position: relative; z-index:99; }
        <?php echo $skin_selector; ?> .heart-img { transition: all 0.2s ease; }
        <?php echo $skin_selector; ?>:hover .heart-img { transform: scale(1.1); }
    </style>

    <?php
    // AJAX 데이터 구성

    if ($is_favorited) {
        // 찜 취소: 기존 wr_id + delete=1
        // 목록 파트의 wr_id를 알아야 함
        $favorite_wr_id = isset($row['favorite'][0]['wr_id']) ? $row['favorite'][0]['wr_id'] : 0;

        $ajax_data = array(
            'action' => 'update',
            'made' => 'favorite_store',
            'wr_id' => $favorite_wr_id,  // 기존 게시글 ID
            'favorite' => array(
                $favorite_id => array(  // 기존 favorite ID 사용
                    'id' => $favorite_id,
                    'delete' => 1
                )
            )
        );
    } else {
        // 찜 추가: wr_id 빈값 전달 → 새 게시글 생성
        // 중요: isset 체크를 위해 wr_id 키는 반드시 포함해야 함
        // 중요: 목록 파트 신규 항목은 문자열 키를 사용해야 함 (숫자 키는 기존 ID로 인식됨)
        $ajax_data = array(
            'action' => 'update',
            'made' => 'favorite_store',
            'wr_id' => '',  // ← 빈값이지만 키는 필수! (StoreManager.php의 isset 체크 통과용)
            'favorite' => array(
                'new' => array(  // ← 문자열 키 사용! (음수나 양수는 기존 ID로 인식됨)
                    'mb_id' => $mb_id,
                    'store_wr_id' => $store_wr_id,
                    'created_at' => G5_TIME_YMDHIS
                )
            )
        );
    }
    ?>

    <button
            type="button"
            class="btn btn-link p-0 border-0 favorite-toggle-btn"
            data-wv-ajax-url="<?php echo $ajax_url; ?>"
            data-wv-ajax-data='<?php echo json_encode($ajax_data); ?>'
            data-wv-ajax-option='reload_ajax:true'
    >
        <?php if ($is_favorited): ?>
            <img src="<?php echo $this->manager->plugin_url; ?>/img/heart_on.png" class="heart-img w-[18px]" alt="찜 취소">
        <?php else: ?>
            <img src="<?php echo $this->manager->plugin_url; ?>/img/heart_off.png" class="heart-img w-[18px]" alt="찜하기">
        <?php endif; ?>
    </button>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector; ?>");

            // AJAX 성공 시 이미지 교체 (즉각 반응용)
            $skin.find('.favorite-toggle-btn').on('wv-ajax-success', function(e, response){
                var $img = $(this).find('.heart-img');
                var currentSrc = $img.attr('src');

                if (currentSrc.includes('heart_off.png')) {
                    $img.attr('src', currentSrc.replace('heart_off.png', 'heart_on.png'));
                    $img.attr('alt', '찜 취소');
                } else {
                    $img.attr('src', currentSrc.replace('heart_on.png', 'heart_off.png'));
                    $img.attr('alt', '찜하기');
                }
            });
        });
    </script>
</div>