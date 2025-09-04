<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $config;
?>

<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative" style="" >
    <style>
        <?php echo $skin_selector?> {}
        @media (max-width: 991.98px) {

        }
    </style>
    <script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $config['cf_kakao_js_apikey']?>&libraries=services"></script>
    <div  class="kakao-map <?php echo $data['map_class']; ?>" ></div>


    <script>

        $(document).ready(function (){

            var $skin = $("<?php echo $skin_selector?>");
            var mapContainer = $("<?php echo $skin_selector?> .kakao-map")[0];


            var geocoder = new kakao.maps.services.Geocoder();


            geocoder.addressSearch('<?php echo $data['address']?>', function(result, status) {

                if (status === kakao.maps.services.Status.OK) {
                    var coords = new kakao.maps.LatLng(result[0].y, result[0].x);
                    var map = new kakao.maps.Map(mapContainer, {
                        center: coords, // 지도의 중심좌표
                        level: 2 // 지도의 확대 레벨
                    });

                    var marker = new kakao.maps.Marker({
                        // 지도 중심좌표에 마커를 생성합니다
                        position: map.getCenter()
                    });

                    marker.setMap(map);


                    //var imageSrc = '<?php //echo $wv_page_skin_url?>///img/map_marker.png', // 마커이미지의 주소입니다
                    //    imageSize = new kakao.maps.Size(200, 48), // 마커이미지의 크기입니다
                    //    imageOption = {offset: new kakao.maps.Point(42, 40)}; // 이미지넓이/2, 이미지높이
                    //
                    // var markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize, imageOption);
                    //
                    //
                    // var marker = new kakao.maps.Marker({
                    //     position: coords,
                    //     image: markerImage // 마커이미지 설정
                    // });
                    //
                    // marker.setMap(map);

                }
            });

        })

    </script>
</div>