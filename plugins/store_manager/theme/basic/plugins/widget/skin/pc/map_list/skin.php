<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// AJAX로 전달받은 지도 영역 정보
$bounds = isset($_REQUEST['bounds']) ? $_REQUEST['bounds'] : null;
$center = isset($_REQUEST['center']) ? $_REQUEST['center'] : null;
$level = isset($_REQUEST['level']) ? intval($_REQUEST['level']) : 6;

// 매장 데이터 조회 (예시 - 실제 store_manager 메서드로 교체 필요)
$stores = array();
if ($bounds) {
    // TODO: store_manager에서 해당 영역의 매장 조회
    // $stores = wv()->store_manager->get_stores_in_bounds($bounds);

    // 임시 데모 데이터
    $stores = array(
        (object)array(
            'wr_id' => 1,
            'name' => '매장 A',
            'address' => '부산 영도구 동삼동 123-45',
            'category' => '카페',
            'phone' => '051-123-4567',
            'lat' => 35.0716,
            'lng' => 129.0574
        ),
        (object)array(
            'wr_id' => 2,
            'name' => '매장 B',
            'address' => '부산 영도구 영선동 456-78',
            'category' => '음식점',
            'phone' => '051-987-6543',
            'lat' => 35.0756,
            'lng' => 129.0614
        )
    );
}
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget store-list-widget">
    <style>
        <?php echo $skin_selector?> {}

        <?php echo $skin_selector?> .store-item {
            padding: var(--wv-16);
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        <?php echo $skin_selector?> .store-item:hover {
                                        background-color: #f8f9fa;
                                    }

        <?php echo $skin_selector?> .store-item:last-child {
                                        border-bottom: none;
                                    }

        <?php echo $skin_selector?> .store-name {
                                        font-size: var(--wv-16);
                                        font-weight: 600;
                                        margin-bottom: var(--wv-4);
                                        color: #333;
                                    }

        <?php echo $skin_selector?> .store-category {
                                        display: inline-block;
                                        background-color: #007bff;
                                        color: white;
                                        padding: var(--wv-2) var(--wv-8);
                                        border-radius: var(--wv-12);
                                        font-size: var(--wv-12);
                                        margin-bottom: var(--wv-8);
                                    }

        <?php echo $skin_selector?> .store-address {
                                        color: #666;
                                        font-size: var(--wv-14);
                                        margin-bottom: var(--wv-4);
                                        line-height: 1.4;
                                    }

        <?php echo $skin_selector?> .store-phone {
                                        color: #007bff;
                                        font-size: var(--wv-14);
                                        font-weight: 500;
                                    }

        <?php echo $skin_selector?> .store-empty {
                                        text-align: center;
                                        padding: var(--wv-40);
                                        color: #666;
                                    }

        <?php echo $skin_selector?> .store-count {
                                        padding: var(--wv-12) var(--wv-16);
                                        background-color: #f8f9fa;
                                        border-bottom: 1px solid #e9ecef;
                                        font-size: var(--wv-14);
                                        font-weight: 500;
                                        color: #666;
                                    }
    </style>

    <?php if (!empty($stores)): ?>
        <div class="store-count">
            총 <?php echo count($stores); ?>개의 매장
        </div>

        <?php foreach ($stores as $store): ?>
            <div class="store-item"
                 data-store-id="<?php echo $store->wr_id; ?>"
                 data-lat="<?php echo $store->lat; ?>"
                 data-lng="<?php echo $store->lng; ?>">

                <div class="store-name"><?php echo $store->name; ?></div>

                <?php if ($store->category): ?>
                    <div class="store-category"><?php echo $store->category; ?></div>
                <?php endif; ?>

                <div class="store-address"><?php echo $store->address; ?></div>

                <?php if ($store->phone): ?>
                    <div class="store-phone">📞 <?php echo $store->phone; ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="store-empty">
            <i class="fa-solid fa-store-slash" style="font-size: 48px; color: #ddd; margin-bottom: 16px;"></i>
            <p>이 지역에 등록된 매장이 없습니다.</p>
        </div>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            var $widget = $("<?php echo $skin_selector?>");

            // 매장 아이템 클릭 시 지도에서 해당 매장으로 이동
            $widget.on('click', '.store-item', function() {
                var storeId = $(this).data('store-id');
                var lat = $(this).data('lat');
                var lng = $(this).data('lng');

                // 부모 창(지도)에 매장 위치로 이동 이벤트 발송
                $(document).trigger('wv_store_list_item_clicked', {
                    store_id: storeId,
                    lat: lat,
                    lng: lng
                });

                // 매장 목록 패널 닫기
                $('.store-list').fadeOut(300);
            });
        });
    </script>
</div>