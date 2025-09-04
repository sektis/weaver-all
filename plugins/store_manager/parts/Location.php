<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Location extends StoreSchemaBase implements StoreSchemaInterface{

    protected $columns = array(
        'lat' => "DECIMAL(10,7) DEFAULT NULL",
        'lng' => "DECIMAL(10,7) DEFAULT NULL",
        'region_1depth_name' => "VARCHAR(255) DEFAULT NULL",
        'region_2depth_name' => "VARCHAR(255) DEFAULT NULL",
        'region_3depth_name' => "VARCHAR(255) DEFAULT NULL",
        'address_name' => "VARCHAR(255) DEFAULT NULL",
        'address'=>''
    );

    public function get_indexes(){
        return array(
            array('name' => 'ix_lat_lng', 'type' => 'INDEX', 'cols' => array('lat','lng')),
            array('name' => 'ix_region_depth', 'type' => 'INDEX', 'cols' => array('region_1depth_name','region_2depth_name','region_3depth_name'))
        );
    }
}
