<?php
if (!defined('_GNUBOARD_')) exit;


if(!function_exists('wv_parse')){
    function wv_parse($url, $post_data = '', $add_header = array(), $attempt = 1, $session_id = '' , $use_cookie = false, $dest_file=''){

        if($dest_file){


            $fp = fopen($dest_file, 'wb');

            if(!$fp){
                return array(
                    'result' => false,
                    'content' => 'fopen 실패',
                    'last_url'=> $url
                );
            }
        }

        $parse_url = parse_url($url);
        $host      = $parse_url['scheme'].'://'.$parse_url['host'];
        $referer   = $host.$parse_url['path'];
        $headers = array();
        $headers[] = "Referer: $referer";
        $headers[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36';
        $headers[] = 'x-requested-with: XMLHttpRequest';

        if(is_array($add_header) and count($add_header)){

            $headers = array_merge($headers,$add_header);
        }

        if($post_data){
            if(is_array($post_data)){
                $post_field_string = http_build_query($post_data, '', '&');
            }else{
                $headers[] = 'Content-Type: application/json';
                $post_field_string = $post_data;
            }

        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        if($session_id) {
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_COOKIE,   session_name() . '=' . $session_id);
            $use_cookie=true;
        }

        if($use_cookie){
            @mkdir(G5_DATA_PATH.'/cookie', G5_DIR_PERMISSION);
            @chmod(G5_DATA_PATH.'/cookie', G5_DIR_PERMISSION);
            $cookieFile =  G5_DATA_PATH.'/cookie/'.$parse_url['host'].".cookie";
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }

        if($post_data){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);       //POST data
        }

        if($dest_file){
            curl_setopt($ch, CURLOPT_FILE, $fp);
        }

        $curl_result = curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error_message = curl_error( $ch );
        $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close ($ch);

        if($error_message and $attempt<3){
            return wv_parse($url, $post_data, $add_header, $attempt+1, $session_id , $use_cookie, $dest_file);
        }

        if($dest_file){
            fclose($fp);
            if(filesize($dest_file)){
                return array(
                    'result' => true,
                    'content' => $dest_file,
                );
            }
        }



        return array(
            'result' => $error_message?false:true,
            'content' => $error_message?$error_message:$curl_result,
            'last_url'=> $last_url,
            'code'=>$code,
            'cookie'=>$use_cookie?$cookieFile:null
        );
    }
}
if(!function_exists('wv_parse_download')){
    function wv_parse_download($url,$download_path){

        if(!is_dir($download_path) ) {
            @mkdir($download_path, G5_DIR_PERMISSION);
            @chmod($download_path, G5_DIR_PERMISSION);
        }


        $filename = wv_get_file_name($url);
        $url = str_replace($filename,rawurlencode($filename),$url);
        $file_basename = wv_make_file_name(wv_get_file_base_name($url));
        $dest_file = $download_path.'/'.$file_basename;

        return wv_parse($url,'','','','','',$dest_file);
    }
}
if(!function_exists('wv_make_file_name')){
    function wv_make_file_name($filename){
        $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

        $filename = get_safe_filename($filename);
        $filename = preg_replace("/\.(php|pht|phtm|htm|cgi|pl|exe|jsp|asp|inc|phar)/i", "$0-x", $filename);
        shuffle($chars_array);
        $shuffle = implode('', $chars_array);

        // 첨부파일 첨부시 첨부파일명에 공백이 포함되어 있으면 일부 PC에서 보이지 않거나 다운로드 되지 않는 현상이 있습니다. (길상여의 님 090925)
        return md5(sha1($_SERVER['REMOTE_ADDR'])).'_'.substr($shuffle,0,8).'_'.replace_filename($filename);

    }
}
if(!function_exists('wv_file_get_contents')){
    function wv_file_get_contents($url,$download_path){
        ini_set('allow_url_fopen', 'ON');
        if(!is_dir($download_path) ) {
            @mkdir($download_path, G5_DIR_PERMISSION);
            @chmod($download_path, G5_DIR_PERMISSION);
        }
        $url = urldecode($url);


        $filename = wv_get_file_name($url);
        $url = str_replace($filename,rawurlencode($filename),$url);
        $file_basename = wv_make_file_name(wv_get_file_base_name($url));
        $dest_file = $download_path.'/'.$file_basename;



        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 1,  //1200 Seconds is 20 Minutes
            )
        ));


        $content = @file_get_contents($url,false,$ctx);
        if($content){
            file_put_contents($dest_file, $content);
            if(filesize($dest_file)){

                return array(
                    'result' => true,
                    'content' => $dest_file,
                );
            }
        }



        return array(
            'result' => false,
            'content' => $url.':file_get_contents 실패',
        );
    }
}
if(!function_exists('wv_get_base_filename_form_url')){
    function wv_get_base_filename_form_url($url){

        $basename = basename($url);
        $arr = explode("?",$basename);
        return $arr[0];
    }
}
if(!function_exists('wv_get_file_base_name')){
    function wv_get_file_base_name($text){
        $file_name = wv_get_file_name($text);
        $file_extension = wv_get_file_extension($text);
        return $file_name.($file_extension?'.'.$file_extension:'');
    }
}
if(!function_exists('wv_get_file_name')){
    function wv_get_file_name($text){
        return pathinfo($text,PATHINFO_FILENAME);

    }
}
if(!function_exists('wv_get_file_extension')){
    function wv_get_file_extension($text){
        $ext = pathinfo($text,PATHINFO_EXTENSION);
        $arr = explode('?',$ext);
        return strtolower($arr[0]);
    }
}

if(!function_exists('wv_get_naver_restaurant_list')){
    function wv_get_naver_restaurant_list($sch_text,$sch_lank='',$display=100){
        if($display>100){$display=100;}
        $naver_place_query = '{"operationName":"getRestaurantList","variables":{"restaurantListInput":{"query":"'.$sch_text.'","start":1,"display":'.$display.',"takeout":null,"orderBenefit":null,"filterOpening":null,"rank":"'.$sch_lank.'","isNmap":false,"deviceType":"pc"},"restaurantListFilterInput":{"query":"'.$sch_text.'","rank":"'.$sch_lank.'","display":'.$display.',"start":1,"isCurrentLocationSearch":null},"isNmap":false,"isBounds":false},"query":"query getRestaurantList($restaurantListInput: RestaurantListInput, $restaurantListFilterInput: RestaurantListFilterInput, $isNmap: Boolean!, $isBounds: Boolean!) {\n  restaurants: restaurantList(input: $restaurantListInput) {\n    items {\n      apolloCacheId\n      coupon {\n        ...CouponItems\n        __typename\n      }\n      ...CommonBusinessItems\n      ...RestaurantBusinessItems\n      __typename\n    }\n    ...RestaurantCommonFields\n    nlu {\n      ...NluFields\n      __typename\n    }\n    optionsForMap @include(if: $isBounds) {\n      ...OptionsForMap\n      __typename\n    }\n    searchGuide @include(if: $isNmap) {\n      ...SearchGuide\n      __typename\n    }\n    __typename\n  }\n  filters: restaurantListFilter(input: $restaurantListFilterInput) {\n    ...RestaurantFilter\n    __typename\n  }\n}\n\nfragment CommonBusinessItems on BusinessSummary {\n  id\n  dbType\n  name\n  businessCategory\n  category\n  description\n  hasBooking\n  hasNPay\n  x\n  y\n  distance\n  imageUrl\n  imageCount\n  phone\n  virtualPhone\n  routeUrl\n  streetPanorama {\n    id\n    pan\n    tilt\n    lat\n    lon\n    __typename\n  }\n  roadAddress\n  address\n  commonAddress\n  blogCafeReviewCount\n  bookingReviewCount\n  totalReviewCount\n  bookingUrl\n  bookingBusinessId\n  talktalkUrl\n  detailCid {\n    c0\n    c1\n    c2\n    c3\n    __typename\n  }\n  options\n  promotionTitle\n  agencyId\n  businessHours\n  newOpening\n  markerId @include(if: $isNmap)\n  markerLabel @include(if: $isNmap) {\n    text\n    style\n    __typename\n  }\n  imageMarker @include(if: $isNmap) {\n    marker\n    markerSelected\n    __typename\n  }\n  __typename\n}\n\nfragment OptionsForMap on OptionsForMap {\n  maxZoom\n  minZoom\n  includeMyLocation\n  maxIncludePoiCount\n  center\n  spotId\n  keepMapBounds\n  __typename\n}\n\nfragment NluFields on Nlu {\n  queryType\n  user {\n    gender\n    __typename\n  }\n  queryResult {\n    ptn0\n    ptn1\n    region\n    spot\n    tradeName\n    service\n    selectedRegion {\n      name\n      index\n      x\n      y\n      __typename\n    }\n    selectedRegionIndex\n    otherRegions {\n      name\n      index\n      __typename\n    }\n    property\n    keyword\n    queryType\n    nluQuery\n    businessType\n    cid\n    branch\n    forYou\n    franchise\n    titleKeyword\n    location {\n      x\n      y\n      default\n      longitude\n      latitude\n      dong\n      si\n      __typename\n    }\n    noRegionQuery\n    priority\n    showLocationBarFlag\n    themeId\n    filterBooking\n    repRegion\n    repSpot\n    dbQuery {\n      isDefault\n      name\n      type\n      getType\n      useFilter\n      hasComponents\n      __typename\n    }\n    type\n    category\n    menu\n    context\n    __typename\n  }\n  __typename\n}\n\nfragment SearchGuide on SearchGuide {\n  queryResults {\n    regions {\n      displayTitle\n      query\n      region {\n        rcode\n        __typename\n      }\n      __typename\n    }\n    isBusinessName\n    __typename\n  }\n  queryIndex\n  types\n  __typename\n}\n\nfragment CouponItems on Coupon {\n  total\n  promotions {\n    promotionSeq\n    couponSeq\n    conditionType\n    image {\n      url\n      __typename\n    }\n    title\n    description\n    type\n    couponUseType\n    __typename\n  }\n  __typename\n}\n\nfragment RestaurantFilter on RestaurantListFilterResult {\n  filters {\n    index\n    name\n    value\n    multiSelectable\n    defaultParams {\n      age\n      gender\n      day\n      time\n      __typename\n    }\n    items {\n      index\n      name\n      value\n      selected\n      representative\n      displayName\n      clickCode\n      laimCode\n      type\n      icon\n      __typename\n    }\n    __typename\n  }\n  votingKeywordList {\n    items {\n      name\n      value\n      icon\n      clickCode\n      __typename\n    }\n    menuItems {\n      name\n      value\n      icon\n      clickCode\n      __typename\n    }\n    total\n    __typename\n  }\n  optionKeywordList {\n    items {\n      name\n      value\n      icon\n      clickCode\n      __typename\n    }\n    total\n    __typename\n  }\n  __typename\n}\n\nfragment RestaurantCommonFields on RestaurantListResult {\n  restaurantCategory\n  queryString\n  siteSort\n  selectedFilter {\n    order\n    rank\n    tvProgram\n    region\n    brand\n    menu\n    food\n    mood\n    purpose\n    sortingOrder\n    takeout\n    orderBenefit\n    cafeFood\n    day\n    time\n    age\n    gender\n    myPreference\n    hasMyPreference\n    cafeMenu\n    cafeTheme\n    theme\n    voting\n    filterOpening\n    keywordFilter\n    property\n    realTimeBooking\n    __typename\n  }\n  rcodes\n  location {\n    sasX\n    sasY\n    __typename\n  }\n  total\n  __typename\n}\n\nfragment RestaurantBusinessItems on RestaurantListSummary {\n  categoryCodeList\n  visitorReviewCount\n  visitorReviewScore\n  imageUrls\n  bookingHubUrl\n  bookingHubButtonName\n  visitorImages {\n    id\n    reviewId\n    imageUrl\n    profileImageUrl\n    nickname\n    __typename\n  }\n  visitorReviews {\n    id\n    review\n    reviewId\n    __typename\n  }\n  foryouLabel\n  foryouTasteType\n  microReview\n  tags\n  priceCategory\n  broadcastInfo {\n    program\n    date\n    menu\n    __typename\n  }\n  michelinGuide {\n    year\n    star\n    comment\n    url\n    hasGrade\n    isBib\n    alternateText\n    hasExtraNew\n    region\n    __typename\n  }\n  broadcasts {\n    program\n    menu\n    episode\n    broadcast_date\n    __typename\n  }\n  tvcastId\n  naverBookingCategory\n  saveCount\n  uniqueBroadcasts\n  isDelivery\n  deliveryArea\n  isCvsDelivery\n  isTableOrder\n  isPreOrder\n  isTakeOut\n  bookingDisplayName\n  bookingVisitId\n  bookingPickupId\n  popularMenuImages {\n    name\n    price\n    bookingCount\n    menuUrl\n    menuListUrl\n    imageUrl\n    isPopular\n    usePanoramaImage\n    __typename\n  }\n  newBusinessHours {\n    status\n    description\n    __typename\n  }\n  baemin {\n    businessHours {\n      deliveryTime {\n        start\n        end\n        __typename\n      }\n      closeDate {\n        start\n        end\n        __typename\n      }\n      temporaryCloseDate {\n        start\n        end\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  yogiyo {\n    businessHours {\n      actualDeliveryTime {\n        start\n        end\n        __typename\n      }\n      bizHours {\n        start\n        end\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n  realTimeBookingInfo {\n    description\n    hasMultipleBookingItems\n    bookingBusinessId\n    bookingUrl\n    itemId\n    itemName\n    timeSlots {\n      date\n      time\n      timeRaw\n      available\n      __typename\n    }\n    __typename\n  }\n  __typename\n}"}';
        $naver_place_url = "https://pcmap-api.place.naver.com/graphql";
        $result = wv_parse($naver_place_url, $naver_place_query);

        $json_decode = json_decode($result['content'],1);
        return $json_decode['data']['restaurants']['items'];
    }
}
if(!function_exists('wv_get_naver_place_list')){
    function wv_get_naver_place_list($sch_text,$display=100,$use_bounds=true){
        if($display>100){$display=100;}
        $bounds = '124;33;132;43';
        if(!$use_bounds){
            $bounds='';
        }
        $naver_place_query = '{"operationName":"getPlacesList","variables":{"useReverseGeocode":false,"input":{"query":"'.$sch_text.'","start":1,"display":'.$display.',"sortingOrder":"precision","adult":false,"spq":false,"queryRank":"","bounds":"'.$bounds.'","x":"","y":"","deviceType":"pc"},"isNmap":false,"isBounds":false},"query":"query getPlacesList($input: PlacesInput, $isNmap: Boolean!, $isBounds: Boolean!, $reverseGeocodingInput: ReverseGeocodingInput, $useReverseGeocode: Boolean = false) {\n  businesses: places(input: $input) {\n    total\n    items {\n      id\n      name\n      normalizedName\n      category\n      detailCid {\n        c0\n        c1\n        c2\n        c3\n        __typename\n      }\n      categoryCodeList\n      dbType\n      distance\n      roadAddress\n      address\n      fullAddress\n      commonAddress\n      bookingUrl\n      phone\n      virtualPhone\n      businessHours\n      daysOff\n      imageUrl\n      imageCount\n      x\n      y\n      poiInfo {\n        polyline {\n          shapeKey {\n            id\n            name\n            version\n            __typename\n          }\n          boundary {\n            minX\n            minY\n            maxX\n            maxY\n            __typename\n          }\n          details {\n            totalDistance\n            arrivalAddress\n            departureAddress\n            __typename\n          }\n          __typename\n        }\n        polygon {\n          shapeKey {\n            id\n            name\n            version\n            __typename\n          }\n          boundary {\n            minX\n            minY\n            maxX\n            maxY\n            __typename\n          }\n          __typename\n        }\n        __typename\n      }\n      subwayId\n      markerId @include(if: $isNmap)\n      markerLabel @include(if: $isNmap) {\n        text\n        style\n        stylePreset\n        __typename\n      }\n      imageMarker @include(if: $isNmap) {\n        marker\n        markerSelected\n        __typename\n      }\n      oilPrice @include(if: $isNmap) {\n        gasoline\n        diesel\n        lpg\n        __typename\n      }\n      isPublicGas\n      isDelivery\n      isTableOrder\n      isPreOrder\n      isTakeOut\n      isCvsDelivery\n      hasBooking\n      naverBookingCategory\n      bookingDisplayName\n      bookingBusinessId\n      bookingVisitId\n      bookingPickupId\n      baemin {\n        businessHours {\n          deliveryTime {\n            start\n            end\n            __typename\n          }\n          closeDate {\n            start\n            end\n            __typename\n          }\n          temporaryCloseDate {\n            start\n            end\n            __typename\n          }\n          __typename\n        }\n        __typename\n      }\n      yogiyo {\n        businessHours {\n          actualDeliveryTime {\n            start\n            end\n            __typename\n          }\n          bizHours {\n            start\n            end\n            __typename\n          }\n          __typename\n        }\n        __typename\n      }\n      isPollingStation\n      hasNPay\n      talktalkUrl\n      visitorReviewCount\n      visitorReviewScore\n      blogCafeReviewCount\n      bookingReviewCount\n      streetPanorama {\n        id\n        pan\n        tilt\n        lat\n        lon\n        __typename\n      }\n      naverBookingHubId\n      bookingHubUrl\n      bookingHubButtonName\n      newOpening\n      newBusinessHours {\n        status\n        description\n        dayOff\n        dayOffDescription\n        __typename\n      }\n      coupon {\n        total\n        promotions {\n          promotionSeq\n          couponSeq\n          conditionType\n          image {\n            url\n            __typename\n          }\n          title\n          description\n          type\n          couponUseType\n          __typename\n        }\n        __typename\n      }\n      mid\n      __typename\n    }\n    optionsForMap @include(if: $isBounds) {\n      ...OptionsForMap\n      displayCorrectAnswer\n      correctAnswerPlaceId\n      __typename\n    }\n    searchGuide {\n      queryResults {\n        regions {\n          displayTitle\n          query\n          region {\n            rcode\n            __typename\n          }\n          __typename\n        }\n        isBusinessName\n        __typename\n      }\n      queryIndex\n      types\n      __typename\n    }\n    queryString\n    siteSort\n    __typename\n  }\n  reverseGeocodingAddr(input: $reverseGeocodingInput) @include(if: $useReverseGeocode) {\n    ...ReverseGeocodingAddr\n    __typename\n  }\n}\n\nfragment OptionsForMap on OptionsForMap {\n  maxZoom\n  minZoom\n  includeMyLocation\n  maxIncludePoiCount\n  center\n  spotId\n  keepMapBounds\n  __typename\n}\n\nfragment ReverseGeocodingAddr on ReverseGeocodingResult {\n  rcode\n  region\n  __typename\n}"}';
        $naver_place_url = "https://pcmap-api.place.naver.com/graphql";

        $result = wv_parse($naver_place_url, $naver_place_query);

        $json_decode = json_decode($result['content'],1);
        return $json_decode['data']['businesses']['items'];
    }
}
