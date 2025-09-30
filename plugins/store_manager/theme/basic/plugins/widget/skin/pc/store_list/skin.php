<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 전달받은 옵션 처리
$map_options = array(
    'clustering' => true,
    'initial_level' => 6,   // 초기 줌 레벨 (1~14, 숫자가 작을수록 확대)
    'min_level' => 4,       // 최소 줌 레벨 (최대 확대)
    'max_level' => 9       // 최대 줌 레벨 (최대 축소)
);
if($data['map_otion']){
    $map_options = array_merge($map_options,$data['map_otion']);
}

$location_current = wv()->location->get('current');

// 초기 표시 모드 결정 (list_view가 있으면 목록, 없으면 지도)
$initial_mode = $data['view_type'] ? $data['view_type'] : 'map';
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative h-100" style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>">
    <style>
        #site-wrapper{height: 100dvh!important;}
        #content-wrapper>.container{height: 100%}

        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .view-toggle-btn{position:absolute;bottom: var(--wv-43);left:50%;transform:translateX(-50%);z-index:1000;background:#0d171b;border-radius:var(--wv-43);padding:var(--wv-8) var(--wv-18);cursor:pointer;box-shadow: 0 var(--wv-4) var(--wv-4) 0 rgba(103, 103, 103, 0.25);;backdrop-filter:blur(4px);transition:all .2s;
                                        color:#fff;font-size: var(--wv-12);font-weight:500}
        <?php echo $skin_selector?> .stores-wrap {position: relative;}
        <?php echo $skin_selector?> .stores-map, <?php echo $skin_selector?> .stores-list{position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;visibility:hidden;transition:all .3s ease}
        <?php echo $skin_selector?> .stores-map.active, <?php echo $skin_selector?> .stores-list.active {opacity: 1;visibility: visible;}
        <?php echo $skin_selector?> .list-each {position: absolute;bottom: var(--wv-17);left:50%;transform: translateX(-50%);border-radius: var(--wv-4);background: #fff;box-shadow: 0 0 var(--wv-4) 0 rgba(67, 67, 67, 0.25);width: 90%;z-index: 1001;padding: var(--wv-12) var(--wv-10) var(--wv-14)}

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100">
        <div class="vstack h-100">
            <div class="col-auto">
                <?php echo wv_widget('scroll_category', array('category_wr_id' => $data['category_wr_id'])); ?>
            </div>

            <div class="col">
                <div class="h-100 stores-wrap">

                    <div class="h-100 stores-map <?php echo $initial_mode === 'map' ? 'active' : ''; ?>">

                    </div>

                    <div class="list-each" style="display: none"></div>

                    <!-- 목록 영역 -->
                    <div class="h-100 stores-list <?php echo $initial_mode === 'list' ? 'active' : ''; ?>">

                    </div>


                    <label class="view-toggle-btn"   data-on-value='<img src="<?php echo wv()->store_manager->plugin_url; ?>/img/burger_white.png" class="w-[16px]"> 목록으로 보기'  data-off-value='<img src="<?php echo wv()->store_manager->plugin_url; ?>/img/map_white.png" class="w-[16px]"> 지도로 보기'>
                        <input class="d-none view-toggle" type="checkbox" value="1" <?php echo $initial_mode=='map' ? 'checked' : ''; ?>>
                        <span class="hstack" style="gap:var(--wv-5)"></span>
                    </label>

                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                var $skin = $("<?php echo $skin_selector?>");
                var currentMode = '<?php echo $initial_mode; ?>'; // 'map' 또는 'list'
                var map_loaded = false;
                var $map_event_target = $(".stores-map>*",$skin);
                var $list_each = $(".list-each",$skin);
                var $location_current = <?php echo json_encode($location_current)?>;

                // 검색 데이터 저장 (스위칭 시 재사용)
                var searchData = {
                    q: '<?php echo addslashes($data['q']); ?>',
                    category_wr_id: '<?php echo (int) $data['category_wr_id']; ?>',
                    contractitem_wr_id: '<?php echo (int) $data['contractitem_wr_id']; ?>',
                    limit_km: '<?php echo (int) $data['limit_km']?$data['limit_km']:0; ?>',
                    town:'<?php echo $data['town']?$data['town']:''; ?>',
                    center:$location_current
                };


                $('.scroll-category-link',$skin).click(function () {
                    searchData.category_wr_id = $(this).data('category-wr-id');
                    view_reload()
                })


                // 초기 로드
                if (currentMode === 'map') {
                    loadMap();
                } else {
                    loadList();
                }

                // 전환 버튼 이벤트
                $('.view-toggle', $skin).change(function() {
                    toggleView($(this).is(':checked')?'map':'list');
                });

                // 지도 로드 함수
                function loadMap() {
                    var mapOptions = <?php echo json_encode($map_options); ?>;

                    $.post('<?php echo wv()->store_manager->ajax_url?>', {'action': 'widget','widget': 'location/map','data': mapOptions}, function(data) {
                        $(".stores-map", $skin).html(data);
                        map_loaded=true;
                        $map_event_target = $(".stores-map>*",$skin);

                    }, 'html');
                }

                // 목록 로드 함수
                function loadList() {
                    $(".stores-list", $skin).html('');
                    var listData = $.extend({}, searchData, {action: 'get_store_list',widget: 'map_list'});

                    $.post('<?php echo wv()->store_manager->ajax_url?>', listData, function(data) {
                        $(".stores-list", $skin).html(data);

                    }, 'html');
                }

                // 뷰 전환 함수
                function toggleView(mode) {
                    var $mapArea = $('.stores-map', $skin);
                    var $listArea = $('.stores-list', $skin);

                    if (mode === 'map') {
                        // 목록 → 지도
                        $listArea.removeClass('active');
                        $mapArea.addClass('active');

                        currentMode = 'map';
                    } else {
                        // 지도 → 목록
                        $mapArea.removeClass('active');
                        $listArea.addClass('active');
                        currentMode = 'list';
                    }
                    view_reload()
                }

                function view_reload(){

                    if(currentMode=='map'){
                        // 지도가 로드되지 않았다면 로드
                        if (!map_loaded) {
                            loadMap();
                        }else{
                            fetchStoresByBounds();
                        }
                        console.log('map loaded');

                    }else{
                        loadList();
                        console.log('list loaded');
                    }
                }

                // 지도 이벤트 리스너
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

                    view_reload()
                });
                // 지도 이벤트 리스너
                $(document).on('wv_location_map_change_start', function(event, data) {

                    view_list_each();
                });

                // 지도 마커 클릭 이벤트 리스너
                $(document).on('wv_location_map_marker_clicked', function(event, data) {

                    view_list_each(data.place.list_each)

                });

                function view_list_each(html){
                    if(html){
                        $list_each.html(html);
                        $list_each.show();
                    }else{
                        $list_each.hide();
                    }
                }

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

            });

        </script>
    </div>
</div>