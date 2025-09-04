<?php
namespace weaver\store_manager;

class Store{

    protected $manager;
    protected $wr_id;
    protected $write_row = array();
    protected $ext_row = array();

    public function __construct($manager, $wr_id, $write_row, $ext_row){
        $this->manager = $manager;
        $this->wr_id = (int)$wr_id;
        $this->write_row = is_array($write_row) ? $write_row : array();
        $this->ext_row = is_array($ext_row) ? $ext_row : array();

        // 최소 보장
        if (!isset($this->write_row['wr_id'])) $this->write_row['wr_id'] = $this->wr_id;
        if (!isset($this->ext_row['wr_id'])) $this->ext_row['wr_id'] = $this->wr_id;
    }

    public function get_wr_id(){
        return $this->wr_id;
    }

    public function get_write_row(){
        return $this->write_row;
    }

    public function get_ext_row(){
        return $this->ext_row;
    }

    /**
     * 매직 접근
     * - 파트명: 프록시 반환 (렌더/값 접근)
     * - 필드명: 우선순위 write_row → ext_row
     */
    public function __get($name){
        // 파트 접근
        $parts = $this->manager->get_parts();
        if (isset($parts[$name])) {
            return new StorePartProxy($this->manager, $this->wr_id, $parts[$name], $this->ext_row);
        }

        // write 필드 우선
        if (isset($this->write_row[$name])) return $this->write_row[$name];
        // 없으면 ext 필드
        if (isset($this->ext_row[$name])) return $this->ext_row[$name];

        return null;
    }

    public function __isset($name){
        $parts = $this->manager->get_parts();
        if (isset($parts[$name])) return true;
        if (isset($this->write_row[$name])) return true;
        if (isset($this->ext_row[$name])) return true;
        return false;
    }
}
