<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?>  .location-search-wrap {  position: relative;}
        <?php echo $skin_selector?>  .location-search-input {  letter-spacing: calc(var(--wv-0_56) * -1);}
        <?php echo $skin_selector?>  .location-search-input::placeholder {color: #cfcfcf;font-size: var(--wv-14);font-style: normal;font-weight: 500;line-height: var(-calc(var(--wv-14) * -1),var(--wv-20));letter-spacing: calc(var(--wv-0_56) * -1);}
        <?php echo $skin_selector?>  .location-search-result-wrap { z-index: 999;background-color: #fff;   position: absolute;top: calc(100% + 3px);left: 0;width: 100%;max-height: 0;transition: all .4s;overflow: hidden; font-size: var(--wv-12);font-weight: 500}
        <?php echo $skin_selector?>  .location-search-result-wrap.active {max-height: 1000px; }
        <?php echo $skin_selector?>  .location-search-result-inner {    border: 1px solid #EFEFEF;border-radius: var(--wv-4);background: #fff}
        <?php echo $skin_selector?>  .location-search-result {padding: var(--wv-6);max-height: 35dvh;background: #fff;overflow-y: auto}
        <?php echo $skin_selector?>  .location-search-result .location-search-result-item {padding: var(--wv-10) var(--wv-10)}
        <?php echo $skin_selector?>  .location-search-foot {padding: var(--wv-6);display: flex;border-top: 1px solid #efefef}

        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="">
        <div  class="location-search-wrap">
            <input type="text" class="form-control  location-search-input h-[43px] rounded-[4px] border-[1px] border-solid border-[#EFEFEF] outline-none p-[12px] bg-[#f9f9f9]"   placeholder="지역을 검색해보세요 (서울 강남구, 강남구 논현동 등)" />
            <button type="button" class="btn location-search-btn p-0 outline-none position-absolute top-50 translate-middle-y end-[12px]"><img src="<?php echo $wv_skin_url; ?>/search.png" class="w-[20px]" alt=""></button>
        </div>
        <div class="location-search-result-wrap  ">
            <div class="location-search-result-inner">
                <div class="location-search-result" style="p">

                </div>
                <div class="location-search-foot" style="p">
                    <span href="" class="ms-auto location-search-result-close cursor-pointer">닫기</span>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");

            $skin.on('click', '.location-search-btn', function(){
                getSearchList();

                //$.ajax({
                //    url: '<?php //echo wv()->location->ajax_url() ?>//',
                //    type: 'POST',
                //    dataType: 'json',
                //    data: {
                //        wv_location_action: 'api',
                //        a: 'search',
                //        q: q
                //    }
                //}).done(function(res){
                //    var list = normalizeSearchResponse(res);
                //    renderSearchResultList(list);
                //}).fail(function(){
                //    renderSearchResultList([]); // 실패 시 빈 결과 표시
                //});
            });
            function getSearchList(){
                var q = $('.location-search-input', $skin).val().trim();

                if (!q) {
                    $skin.find('.location-search-result').empty();
                    $skin.find('.location-search-result-wrap').removeClass('active');
                    return;
                }

                wv_address_search(q,function (result) {
                    renderSearchResultList(result)
                })

            }

            function renderSearchResultList(data){
                var $wrap = $('.location-search-result-wrap',$skin);
                var $box  = $('.location-search-result',$wrap);
                $box.empty();


                if (!isset(data.list) || (isset(data.list) && !data.list.length)) {
                    $box.append('<div class="px-2 py-2 text-muted">검색 결과가 없습니다.</div>');
                    $wrap.addClass('active');
                    return;
                }

                data.list.forEach(function(row){
                    // 라벨: "시/도 (시/구/군) 동/읍/면" - d2 비면 생략
                    var label = (row.road_address_name || row.address_name) + (row.place_name?' ('+row.place_name+')':'');

                    var $a = $('<a href="" class="location-search-result-item d-block  " style="cursor:pointer;"></a>')
                        .text(label)
                        .attr({
                            'data-address_name': row.address_name,
                            'data-road_address_name': row.road_address_name,
                            'data-place_name': row.place_name,
                            'data-lng': row.lng,
                            'data-lat': row.lat,
                        });

                    $box.append($a);
                });

                $wrap.addClass('active');
            }

            function searchResultClose() {
                $skin.find('.location-search-result-wrap').removeClass('active');
            }
            $skin.on('click', '.location-search-result-close', function(e){
                searchResultClose();
            });
            $skin.on('click', '.location-search-result-item', function(e){
                e.preventDefault()
                var $this = $(this);
                var eventData = {
                    address_name: $this.data('address_name'),
                    road_address_name: $this.data('road_address_name'),
                    place_name: $this.data('place_name'),
                    lng: $this.data('lng'),
                    lat: $this.data('lat'),
                };

                $(document).trigger('wv_location_search_address_select', [eventData]);
                searchResultClose();
            });
            $skin.on("keydown", function(e) {
                if (e.key === "Enter" || e.keyCode === 13) {
                    e.stopImmediatePropagation(); // 다른 click 핸들러 실행 안됨
                    e.stopPropagation(); // 다른 click 핸들러 실행 안
                    e.preventDefault();

                    getSearchList();
                }else{
                    searchResultClose();
                }
            });
            $(document).on('click',function (e) {
                var $resultWrap = $skin.find('.location-search-result-wrap');

                if ($resultWrap.hasClass('active')) {
                    var isInsideSkin = $skin.is(e.target) || $skin.has(e.target).length > 0;

                    if (!isInsideSkin) {
                        searchResultClose();
                    }
                }
            })
        })

    </script>
</div>