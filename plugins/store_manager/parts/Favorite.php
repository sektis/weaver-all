<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

/**
 * Favorite 파트 클래스
 * - 찜하기 기능
 * - list_part: 목록 파트 (1:N 관계)
 */
class Favorite extends StoreSchemaBase implements StoreSchemaInterface {

    public $list_part = true; // 목록 파트

    /**
     * 컬럼 정의 - 속성으로 정의
     */
    protected $columns = array(
        'mb_id' => "VARCHAR(255) NOT NULL COMMENT '회원 ID'",
        'store_wr_id' => "INT(11) NOT NULL COMMENT '매장 wr_id'",
        'created_at' => "DATETIME NOT NULL COMMENT '찜한 날짜'"
    );

    /**
     * 인덱스 정의
     */
    public function get_indexes(){
        return array(
            array(
                'name' => 'mb_id_store_idx',
                'type' => 'UNIQUE',
                'cols' => array('mb_id', 'store_wr_id')
            ),
            array(
                'name' => 'store_wr_id_idx',
                'type' => 'INDEX',
                'cols' => array('store_wr_id')
            )
        );
    }

    /**
     * 허용 컬럼
     */
    public function get_allowed_columns(){
        return array('mb_id', 'store_wr_id', 'created_at');
    }
}