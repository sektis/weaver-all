<?php
namespace weaver\store_manager;

class Store{

    protected $manager;
    protected $wr_id;
    protected $write_row = array();
    protected $ext_row = array();
    protected $proxy_cache = array(); // ✅ 캐시 추가
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
// Store.php에서 member 필드 접근 개선
    public function __get($name){
        $parts = $this->manager->get_parts();
        if (isset($parts[$name])) {
            // ✅ 캐시 확인
            if (isset($this->proxy_cache[$name])) {
                return $this->proxy_cache[$name];
            }

            // ✅ 없으면 생성 후 캐시
            $proxy = new StorePartProxy($this->manager, $this->wr_id, $parts[$name], $this->ext_row, $name);

            // 목록 파트면 데이터 로드
            if ($this->manager->is_list_part_schema($parts[$name])) {
                $proxy->list = $this->manager->get_list_part_list($this->wr_id, $name);
            }

            $this->proxy_cache[$name] = $proxy;
            return $proxy;
        }

        // member 필드 매핑 등 기존 로직 유지
        if (strpos($name, 'mb_') === 0) {
            $prefixed_name = 'mb_' . $name;
            if (isset($this->write_row[$prefixed_name])) {
                return $this->write_row[$prefixed_name];
            }
        }

        if (isset($this->write_row[$name])) return $this->write_row[$name];
        if (isset($this->ext_row[$name])) return $this->ext_row[$name];

        return null;
    }
}
