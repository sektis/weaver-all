<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$map_options = array(
    'clustering' => true,
    'initial_level' => 6,   // 초기 줌 레벨 (1~14, 숫자가 작을수록 확대)
    'min_level' => 4,       // 최소 줌 레벨 (최대 확대)
    'max_level' => 9       // 최대 줌 레벨 (최대 축소)
);
$location_current = wv()->location->get('current');
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap h-100"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}




        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full    " style="height: 30dvh">




        <div class="h-100 stores-map  "  >
            <?php echo   wv_widget('location/map',$map_options);?>
        </div>

            <script>
                $(document).ready(function() {

                    var $skin = $("<?php echo $skin_selector?>");
                    var $map_event_target = $(".stores-map>*",$skin);
                    var $location_current = <?php echo json_encode($location_current)?>;

                    // 검색 데이터 저장 (스위칭 시 재사용)
                    var searchData = {
                        q: '<?php echo addslashes($data['q']); ?>',
                        category_wr_id: '<?php echo (int) $data['category_wr_id']; ?>',
                        contractitem_wr_id: '<?php echo (int) $data['contractitem_wr_id']; ?>',
                        limit_km: '<?php echo (int) $data['limit_km']?$data['limit_km']:0; ?>',
                        center:$location_current
                    };
                    // 바운드 변경에 따른 매장 데이터 조회
                    function fetchStoresByBounds() {

                        var requestData = $.extend({}, searchData, {
                            action: 'get_store_list',

                        });

                        $.ajax({
                            url: '<?php echo wv()->store_manager->ajax_url?>',
                            type: 'POST',
                            dataType: 'json',
                            data: requestData,
                            success: function(response) {
                                triggerStoreUpdateEvent(response);
                            },
                        });
                    }

                    // 매장 데이터를 범용 마커 데이터로 가공하여 전송
                    function triggerStoreUpdateEvent(responseContent) {


                        $map_event_target.trigger('wv_location_map_updated', [responseContent.content]);
                    }

                    $(document).on('wv_location_map_changed', function(event, data) {

                        searchData = $.extend({}, searchData, {
                            sw_lat: data.bounds.getSouthWest().getLat(),
                            sw_lng: data.bounds.getSouthWest().getLng(),
                            ne_lat: data.bounds.getNorthEast().getLat(),
                            ne_lng: data.bounds.getNorthEast().getLng(),
                            center: {
                                lat: data.center.getLat(),
                                lng: data.center.getLng()
                            },

                        });

                        fetchStoresByBounds();
                    });
                });
            </script>


    </div>

</div>