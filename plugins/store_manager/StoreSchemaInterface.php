<?php
namespace weaver\store_manager;

/**
 * 스키마 파트 인터페이스 (PHP 5.6)
 */
interface StoreSchemaInterface{

    /**
     * 컬럼 정의: '컬럼명' => 'DDL_FRAGMENT'
     * 예: 'lat' => "DECIMAL(10,7) DEFAULT NULL"
     * @return array
     */
    public function get_columns();

    /**
     * 인덱스 정의:
     * array(
     *   array('name'=>'ix_lat_lng','type'=>'INDEX','cols'=>array('lat','lng')),
     *   ...
     * )
     * @return array
     */
    public function get_indexes();

    /**
     * 업서트 허용 컬럼 목록
     * @return array
     */
    public function get_allowed_columns();
}
