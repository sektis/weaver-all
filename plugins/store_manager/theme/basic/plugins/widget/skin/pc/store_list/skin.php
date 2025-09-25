<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$map_options = array(
    'clustering' => true,
    'map_id' => 'store-map-main',
    'initial_level' => 6,   // 초기 줌 레벨 (1~14, 숫자가 작을수록 확대)
    'min_level' => 4,       // 최소 줌 레벨 (최대 확대)
    'max_level' => 9       // 최대 줌 레벨 (최대 축소)
);
$list_option = array(
    'view_list'=>$data['view_list'],
    'q'=>$data['q'],
    'category_wr_id'=>$data['category_wr_id'],
)
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative  h-100 "  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        #site-wrapper{height: 100dvh!important;}
        #content-wrapper>.container{height: 100%}

        <?php echo $skin_selector?> {}
        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100"  >


        <div class="vstack h-100">
            <div class="col-auto"  >
                <?php echo wv_widget('scroll_category',array('category_wr_id'=>$data['category_wr_id']));?>
            </div>


            <div class="col">
                <div class="h-100 stores-wrap">
                    <div class="h-100 stores-map">

                    </div>
                    <div class="h-100 stores-list">

                    </div>
                </div>
            </div>
        </div>



        <script>
            $(document).ready(function() {

                var $skin = $("<?php echo $skin_selector?>");

                $(document).on('wv_location_map_changeed', function(event, data) {
                    // Ajax로 매장 데이터 조회
                    fetchStoresByBounds(data);
                });


                function load_map(){
                    var map_options = <?php echo json_encode($map_options)?>;
                    $.post('<?php echo wv()->store_manager->ajax_url?>',{'action':'widget','widget':'location/map','data':map_options},function (data) {
                        $(".stores-map",$skin).html(data)
                    },'html')

                }
                function load_list(){
                    $.post('<?php echo wv()->store_manager->ajax_url?>',{'action':'widget','widget':'map_list'},function (data) {
                        $(".stores-list",$skin).html(data)
                    },'html')

                }
                load_list()


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
                            where:<?php echo json_encode($data['where'])?>
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