<?php
if(!function_exists('wv_make_distance_select_sql')){
    function wv_make_distance_select_sql($base_lat, $base_lng, $lat_col = 's.location_lat', $lng_col = 's.location_lng', $alias = 'distance_km'){
        $base_lat = floatval($base_lat);
        $base_lng = floatval($base_lng);

        // 하버사인 공식으로 거리 계산 (km 단위)
        $distance_sql = "
            (6371 * acos(
                cos(radians({$base_lat})) * 
                cos(radians({$lat_col})) * 
                cos(radians({$lng_col}) - radians({$base_lng})) + 
                sin(radians({$base_lat})) * 
                sin(radians({$lat_col}))
            )) AS {$alias}
        ";

        return $distance_sql;
    }
}

/**
 * 현재 위치 기준 거리 정렬 옵션 생성 헬퍼
 * @param bool $include_select 거리 컬럼을 select에 포함할지 여부
 * @return array|false get_list 옵션 또는 false
 */
if(!function_exists('wv_make_current_location_distance_options')){
    function wv_make_current_location_distance_options($curr_location){
        // 현재 위치 가져오기


        if(!isset($curr_location['lat']) || !isset($curr_location['lng'])){
            return false;
        }

        $lat = $curr_location['lat'];
        $lng = $curr_location['lng'];

        $options = array(
            'order_by' => wv_make_distance_order_sql($lat, $lng)
        );

        if($include_select){
            $options['select_s'] = wv_make_distance_select_sql($lat, $lng);
        }

        return $options;
    }
}

/**
 * 특정 좌표 기준 거리 정렬 옵션 생성 헬퍼
 * @param float $base_lat 기준 위도
 * @param float $base_lng 기준 경도
 * @param bool $include_select 거리 컬럼을 select에 포함할지 여부
 * @return array get_list 옵션
 */
if(!function_exists('wv_make_coords_distance_options')){
    function wv_make_coords_distance_options($base_lat, $base_lng, $include_select = true){
        $options = array(
            'order_by' => wv_make_distance_order_sql($base_lat, $base_lng)
        );

        if($include_select){
            $options['select_s'] = wv_make_distance_select_sql($base_lat, $base_lng);
        }

        return $options;
    }
}

if(!function_exists('wv_make_distance_order_sql')){
    function wv_make_distance_order_sql($base_lat, $base_lng, $lat_col = 's.location_lat', $lng_col = 's.location_lng'){
        $base_lat = floatval($base_lat);
        $base_lng = floatval($base_lng);

        // 하버사인 공식으로 거리 계산 (km 단위)
        $distance_sql = "
            (6371 * acos(
                cos(radians({$base_lat})) * 
                cos(radians({$lat_col})) * 
                cos(radians({$lng_col}) - radians({$base_lng})) + 
                sin(radians({$base_lat})) * 
                sin(radians({$lat_col}))
            ))
        ";

        return "{$distance_sql} ASC";
    }
}