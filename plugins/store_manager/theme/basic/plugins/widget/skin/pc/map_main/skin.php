<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap "  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}




        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full  " style="height: 30dvh">
        <?php


        // Map 옵션 설정
        $map_options = array(
        'height_wrapper' => $skin_selector,
        'clustering' => true,
        'map_id' => 'store-map-main',
        'initial_level' => 6,   // 초기 줌 레벨 (1~14, 숫자가 작을수록 확대)
        'min_level' => 4,       // 최소 줌 레벨 (최대 확대)
        'max_level' => 9       // 최대 줌 레벨 (최대 축소)
        );
        $map_options = array_merge($map_options,$data);

        ?>



            <!-- Location 플러그인 Map 스킨 호출 -->
            <div class="map-container" style="height:30dvh">
                <?php echo   wv_widget('location/map');?>
            </div>



            <script>
                $(document).ready(function() {

                    var $skin = $("<?php echo $skin_selector?>");
                    /**
                     * 🗺️ 지도 변경 이벤트 (통합)
                     * 지도 이동, 줌 변경시 모두 이 이벤트로 처리
                     */
                    $(document).on('wv_location_map_changeed', function(event, data) {


                        // Ajax로 매장 데이터 조회
                        fetchStoresByBounds(data);
                    });

                    /**
                     * 📡 Ajax로 지도 영역 내 매장 조회
                     */
                    function fetchStoresByBounds(data) {
                        var ajaxUrl = '<?php echo wv()->store_manager->made('sub01_01')->plugin_url?>/ajax.php';

                        $.ajax({
                            url: ajaxUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'get_stores_by_bounds',
                                sw_lat: data.bounds.sw_lat,
                                sw_lng: data.bounds.sw_lng,
                                ne_lat: data.bounds.ne_lat,
                                ne_lng: data.bounds.ne_lng,
                                curr_coords: data.center,
                            },
                            success: function(response) {

                                if (response.result && response.content && response.content.lists) {
                                    // 새로운 이벤트 발생: Map 스킨에서 마커 처리
                                    triggerStoreUpdateEvent(response);
                                }
                            },
                            error: function(xhr, status, error) {
                            }
                        });
                    }

                    // 0101 스킨 수정
                    function triggerStoreUpdateEvent(responseContent) {
                        var eventData = {
                            lists: responseContent.content.lists,

                            count: responseContent.content.count,
                            category_icon_wrap: responseContent.content.category_icon_wrap,
                            category_icon_wrap_on: responseContent.content.category_icon_wrap_on,

                            timestamp: new Date().getTime()
                        };

                        $(document).trigger('wv_location_place_updated', [eventData]);

                    }

                });
            </script>


    </div>

</div>