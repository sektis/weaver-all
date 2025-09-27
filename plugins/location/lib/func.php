<?php

/**
 * 거리 기반 get_list 옵션 생성
 */
if(!function_exists('wv_make_distance_options')){
    function wv_make_distance_options($lat, $lng, $option = array(), $limit_km = 50, $order = 'asc', $need_select = true, $distance_field='distance_km', $lat_field = 'location_lat', $lng_field = 'location_lng'){
        if(!is_array($option)) $option = array();

        $lat = floatval($lat);
        $lng = floatval($lng);
        $limit_km = floatval($limit_km);
        $order_dir = strtolower($order) === 'desc' ? 'DESC' : 'ASC';

        // 테이블 prefix 추가
        $lat_col = strpos($lat_field, '.') === false ? "s.{$lat_field}" : $lat_field;
        $lng_col = strpos($lng_field, '.') === false ? "s.{$lng_field}" : $lng_field;

        // 거리 계산 SQL
        $distance_sql = "6371 * acos(cos(radians({$lat})) * cos(radians({$lat_col})) * cos(radians({$lng_col}) - radians({$lng})) + sin(radians({$lat})) * sin(radians({$lat_col})))";

        // ORDER BY 설정
        $option['order_by'] = "({$distance_sql}) {$order_dir}";

        // SELECT 추가
        if($need_select){
            if(!isset($option['select'])) $option['select'] = array();
            $option['select'][] = "({$distance_sql}) AS {$distance_field}";
        }

        // WHERE 거리 제한
        if($limit_km > 0){
            if(!isset($option['where'])) $option['where'] = array();
            $option['where'][] = "({$distance_sql}) <= {$limit_km}";
        }

        return $option;
    }
}