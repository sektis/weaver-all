// function wv_get_current_location(callback) {
//     if (!navigator.geolocation) {
//         alert("이 브라우저에서는 위치 정보를 지원하지 않습니다.");
//         return;
//     }
//
//     navigator.geolocation.getCurrentPosition(
//         function (position) {
//             const lat = position.coords.latitude;
//             const lng = position.coords.longitude;
//             if(lat && lng){
//
//                 wv_get_coords_to_address(lat,lng,function (address) {
//                     callback(Object.assign({'lat': lat, 'lng': lng}, address));
//                 })
//             }
//         },
//         function (error) {
//
//         },
//         {
//             enableHighAccuracy: false,
//             timeout: 10000,
//             maximumAge: 0
//         }
//     );
// }
//
// function wv_get_address_to_coords(address, callback) {
//     const geocoder = new kakao.maps.services.Geocoder();
//
//     geocoder.addressSearch(address, function (result, status) {
//
//         if (status === kakao.maps.services.Status.OK) {
//             wv_get_coords_to_address(result[0].y,result[0].x,function (address) {
//
//                 callback(Object.assign({'lat': result[0].y, 'lng': result[0].x}, address));
//             })
//         }else{
//             callback(false);
//         }
//     })
// }
//
// function wv_get_coords_to_address(lat, lng, callback) {
//     const geocoder = new kakao.maps.services.Geocoder();
//     const coord = new kakao.maps.LatLng(lat, lng);
//
//     geocoder.coord2Address(coord.getLng(), coord.getLat(), function (result, status) {
//
//         if (status === kakao.maps.services.Status.OK) {
//             callback(result[0])
//         } else {
//             wv_get_coords_to_region(lat,lng,function (address) {
//                 callback({'lat': lat, 'lng': lng,address})
//             })
//         }
//     });
// }
//
// function wv_get_coords_to_region(lat, lng, callback) {
//     const geocoder = new kakao.maps.services.Geocoder();
//
//     geocoder.coord2RegionCode(lng, lat, function (result, status) {
//         if (status === kakao.maps.services.Status.OK) {
//             callback(result[0])
//         }else {
//             callback(false);
//         }
//     });
//
// }
// function wv_trans_sido(text) {
//     // 긴 공식명 → 2글자 약칭
//     const map = {
//         '서울특별시': '서울',
//         '부산광역시': '부산',
//         '대구광역시': '대구',
//         '인천광역시': '인천',
//         '광주광역시': '광주',
//         '대전광역시': '대전',
//         '울산광역시': '울산',
//         '세종특별자치시': '세종',
//
//         '경기도': '경기',
//         '강원특별자치도': '강원',
//         '강원도': '강원',
//         '충청북도': '충북',
//         '충청남도': '충남',
//         '전라북도': '전북',
//         '전라남도': '전남',
//         '경상북도': '경북',
//         '경상남도': '경남',
//         '제주특별자치도': '제주',
//         '제주도': '제주',
//
//         // 약식 표기 대응
//         '전북특별자치도': '전북',
//         '서울시': '서울', '부산시': '부산', '대구시': '대구', '인천시': '인천',
//         '광주시': '광주', '대전시': '대전', '울산시': '울산', '세종시': '세종'
//     };
//
//     // 긴 키부터 정렬
//     const keys = Object.keys(map).sort((a, b) => b.length - a.length);
//
//     // 정규식 패턴 생성
//     const pattern = new RegExp(keys.map(k => k.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|'), 'gu');
//
//     // 치환
//     return text.replace(pattern, match => map[match] || match);
// }
//
