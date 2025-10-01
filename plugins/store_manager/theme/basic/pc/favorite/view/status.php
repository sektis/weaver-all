<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * Favorite 파트 - 찜하기 하트 버튼
 *
 * 사용법:
 * $wr_ids = $favorite_manager->get_simple_list($member['mb_id'], array('favorite' => array('store_wr_id' => $row['wr_id'])));
 * echo $favorite_manager->get($wr_ids['wr_id'])->favorite->render_part('status','view',array(
 *     'favorite_id' => $wr_ids['favorite_id'],
 *     'store_wr_id' => $row['wr_id']
 * ));
 */

// 변수
global $member;
if(!$row['id']){

    $manager = wv()->store_manager->made('favorite_store');
    $wr_ids = $manager->get_simple_list($member['mb_id'], array('favorite' => array('store_wr_id' => $store_wr_id)));
    if($wr_ids["{$this->part_key}_id"]){
        $row = ($manager->get($wr_ids['wr_id'])->favorite->get_item($wr_ids["{$this->part_key}_id"]));

    }
}

$mb_id = isset($member['mb_id']) ? $member['mb_id'] : '';
$favorite_id = isset($favorite_id) ? $favorite_id : 0;
$store_wr_id = isset($store_wr_id) ? $store_wr_id : 0;

// 찜 여부
$is_favorited = $favorite_id > 0;

// AJAX URL
$ajax_url = wv()->store_manager->plugin_url . '/ajax.php';


?>

<div id="<?php echo $skin_id; ?>" class="wv-favorite-heart" <?php echo wv_display_reload_data($reload_ajax_data);; ?>>
    <style>
        <?php echo $skin_selector; ?> { display:inline-block; cursor:pointer; position: relative; z-index:99; }
        <?php echo $skin_selector; ?> .heart-img { transition: all 0.2s ease; }
        <?php echo $skin_selector; ?>:hover .heart-img { transform: scale(1.1); }
    </style>

    <?php
    // AJAX 데이터 구성
    if ($row['id']) {
        // 찜 취소
        $ajax_data = array(
            'action' => 'update_render',
            'part'=> $this->part_key,
            'made' => $this->manager->get_make_id(),
            'wr_id' => $row['wr_id'],
            'favorite' => array(
                $favorite_id => array(
                    'mb_id' => $mb_id,
                    'store_wr_id' => $store_wr_id,
                    'id' => $favorite_id,
                    'delete' => 1
                )
            )
        );
    } else {
        // 찜 추가
        $ajax_data = array(
            'action' => 'update_render',
            'part'=> $this->part_key,
            'made' => $this->manager->get_make_id(),
            'wr_id' => '',  // 빈값 필수!
            'favorite' => array(
                array(
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
            data-wv-ajax-option='replace_with:<?php echo $skin_selector?>'
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

            // AJAX 성공 시 이미지 교체 (즉각 반응)
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