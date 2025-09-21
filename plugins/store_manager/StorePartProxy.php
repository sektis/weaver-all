<?php
namespace weaver\store_manager;

use weaver\StoreManager;

/**
 * PHP 5.6 / list_part 전용 안정판
 * - 일반 파트/목록 파트 모두 render_* 호출 방식 유지
 * - StoreManager의 is_list_part_schema(), render_part(), decode_b64s() 등과 연동
 */
class StorePartProxy{

    protected $manager;
    /** @var int */
    protected $wr_id = 0;
    /** @var object 스키마 인스턴스 (parts\Basic 등) */
    protected $part;
    /** @var string 파트 키 (예: 'basic','store','menu') */
    protected $part_key = '';

    /** @var array write 테이블 row */
    protected $write_row = array();
    /** @var array 확장(ext) 테이블 row */
    protected $ext_row = array();
    /** @var array write + ext 병합(논리키 기준, ext 우선) */
    protected $merged_row = array();

    /** @var array|null 가상 파생키 목록 캐시 */
    protected $virtual_keys = null;

    /** @var array 목록 파트일 때의 리스트 (지연 로딩) */
    public $list = array();

    /**
     * @param StoreManager $manager
     * @param int $wr_id
     * @param object $part_schema
     * @param array $ext_row
     * @param string $part_key  (선택) 없으면 자동 추론
     */
    public function __construct($manager, $wr_id, $part_schema, $ext_row = array(), $part_key = '')
    {
        $this->manager = $manager;
        $this->wr_id   = (int)$wr_id;
        $this->part    = $part_schema;
        $this->ext_row = is_array($ext_row) ? $ext_row : array();
        $this->part_key = (string)$part_key;
    }

    /** 내부용: 파트키 안전 획득 */
    protected function get_part_key()
    {
        if ($this->part_key !== '') return $this->part_key;

        // 1) 스키마가 제공하면 그대로 사용
        if (is_object($this->part) && method_exists($this->part, 'get_part_key')) {
            $pk = (string)$this->part->get_part_key();
            if ($pk !== '') {
                $this->part_key = $pk;
                return $this->part_key;
            }
        }

        // 2) 매니저가 들고 있는 parts에서 역탐색
        if ($this->manager && method_exists($this->manager, 'get_parts')) {
            $parts = $this->manager->get_parts();
            if (is_array($parts)) {
                foreach ($parts as $k => $obj) {
                    if ($obj === $this->part) {
                        $this->part_key = (string)$k;
                        return $this->part_key;
                    }
                }
            }
        }

        // 3) 실패 시 빈 문자열
        return '';
    }

    /** 이 파트가 목록 파트인지 (list_part) */
    protected function is_list_part(){

        if ($this->manager && method_exists($this->manager, 'is_list_part_schema')) {
            return $this->manager->is_list_part_schema($this->part) ? true : false;
        }

        // 폴백: 스키마에 list_part 속성이 있으면 반영
        if (is_object($this->part) && property_exists($this->part, 'list_part')) {
            $ro = new \ReflectionObject($this->part);
            $rp = $ro->getProperty('list_part');
            $rp->setAccessible(true);
            return $rp->getValue($this->part) ? true : false;
        }

        return false;
    }

    /** 목록 파트의 리스트를 필요시 지연 로딩 */
    protected function ensure_list_rows() {
        if (!$this->is_list_part()) return array();

        if (is_array($this->list) && count($this->list)) return $this->list;

        $pkey = $this->get_part_key();


            $this->list = $this->manager->get_list_part_list($this->wr_id, $pkey);

        return $this->list;
    }

    /** write/ext 최신 로우 병합 (일반 파트 기준; 목록 파트는 list 별도) */
    public function ensure_rows($name='')
    {
        if (count($this->merged_row) and ($name and isset($this->merged_row[$name]))) {
            return $this->merged_row;
        }

        // 1) 원본 로딩

        if ($this->wr_id > 0) {
            if ($this->manager && method_exists($this->manager, 'fetch_write_row_cached')) {
                $this->write_row = $this->manager->fetch_write_row_cached($this->wr_id);
            }
            // ext_row가 skeletal이면 갱신
            $need_ext = (!is_array($this->ext_row) || !count($this->ext_row) ||
                (isset($this->ext_row['wr_id']) && count($this->ext_row) === 1));
            if ($need_ext && $this->manager && method_exists($this->manager, 'fetch_store_row_cached')) {
                $this->ext_row = $this->manager->fetch_store_row_cached($this->wr_id);
            }
        }

        // 2) write 우선
        $merged = is_array($this->write_row) ? $this->write_row : array();

        // 3) 허용(논리) 컬럼만 ext에서 역매핑
        $allowed = array();
        if (is_object($this->part) && method_exists($this->part, 'get_allowed_columns')) {
            $allowed = (array)$this->part->get_allowed_columns();
        }

        $pkey = $this->get_part_key();
        if (!$this->is_list_part()) {
            foreach ($allowed as $logical) {
                $physical = $logical;
                if ($pkey !== '' && $this->manager && method_exists($this->manager, 'get_physical_col')) {
                    $physical = $this->manager->get_physical_col($pkey, $logical);
                }
                if (isset($this->ext_row[$physical])) {
                    $merged[$logical] = $this->ext_row[$physical];
                } elseif (isset($this->ext_row[$logical])) {
                    // 과거 호환: 논리키가 그대로 저장돼 있던 경우
                    $merged[$logical] = $this->ext_row[$logical];
                }
            }
        }

        // 4) b64s 자동 디코딩
        if (is_array($merged) && $this->manager && method_exists($this->manager, 'decode_b64s')) {
            foreach ($merged as $k => $v) {
                if (is_string($v) && $v !== '') {
                    $try = $this->manager->decode_b64s($v);
                    if (is_array($try)) $merged[$k] = $try;
                }
            }
        }

        if (!isset($merged['wr_id']) && $this->wr_id > 0) {
            $merged['wr_id'] = $this->wr_id;
        }

        if($name){
            $merged[$name]='';
        }


        // 5) 값 맵핑 적용 (가상 파생키 생성)
        $this->apply_value_maps($merged);

        $this->merged_row = $merged;
        return $this->merged_row;
    }

    /** 체인 접근 */
    public function __get($name)
    {

        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) return null;

        // 목록 파트의 list 바로 접근 허용
        if ($name === 'list' && $this->is_list_part()) {

            return $this->ensure_list_rows();
        }


        // 가상 파생키 허용
        $virtual = $this->get_virtual_keys();


        $row = $this->ensure_rows($name);

        if ($name === 'row' ) {
            return $row;
        }
        if (isset($row[$name])) {
            return $row[$name];
        }

        // 일반 허용 컬럼만
        $allowed = array();
        if (is_object($this->part) && method_exists($this->part, 'get_allowed_columns')) {
            $allowed = (array)$this->part->get_allowed_columns();
        }
        if ($allowed && !in_array($name, $allowed, true)) return null;


        return isset($row[$name]) ? $row[$name] : null;
    }

    public function __isset($name)
    {
        $virtual = $this->get_virtual_keys();
        if (in_array($name, $virtual, true)) {
            $row = $this->ensure_rows();
            return isset($row[$name]);
        }
        $row = $this->ensure_rows();
        return isset($row[$name]);
    }


    public function render_part($arg1, $arg2 = null, $arg3 = array()){
        // CSS/JS 자산 로딩


        $pkey = $this->get_part_key();

        if ($this->is_list_part()) {
            // 목록 파트: row/list 데이터 준비
            $row = $this->ensure_rows();
            $row[$pkey] = $this->ensure_list_rows();

            if (method_exists($this->part, 'make_array')) {
                $this->part->make_array($row[$pkey]);
            }

            // vars에 list 데이터 추가
            $vars = is_array($arg3) ? $arg3 : array();
            $vars = array_merge(array('row' => $row, 'list' => $row[$pkey]), $vars);

            // StoreSchemaBase로 위임 (일반파트와 동일하게)
            if (is_object($this->part) && method_exists($this->part, 'render_part')) {
                return $this->part->render_part($arg1, $arg2, $vars);
            }
            return '';
        }

        // 일반 파트: row 데이터 준비 후 StoreSchemaBase로 위임
        $row = $this->ensure_rows();



        if (is_object($this->part) && method_exists($this->part, 'render_part')) {
            return $this->part->render_part($arg1, $arg2, array_merge(array('row' => $row), is_array($arg3) ? $arg3 : array()));
        }

        return '';
    }


    /** 스키마의 맵핑 프로퍼티 → 가상 파생키로 확장 */
//    protected function get_schema_value_maps()    {
//        static $cache = array();
//        $cls = is_object($this->part) ? get_class($this->part) : '';
//        if (!$cls) return array();
//        if (isset($cache[$cls])) return $cache[$cls];
//
//        $allowed = array();
//        if (is_object($this->part) && method_exists($this->part, 'get_allowed_columns')) {
//            $allowed = (array)$this->part->get_allowed_columns();
//        }
//        $allowed = is_array($allowed) ? array_values($allowed) : array();
//
//        $maps = array(); // 예: ['cont_pdt_type_text' => ['base'=>'cont_pdt_type','map'=>[1=>'매장',2=>'포장']]]
//
//        $ref = new \ReflectionObject($this->part);
//        $props = $ref->getProperties();
//        foreach ($props as $p) {
//            $name = $p->getName();
//
//            // 허용 컬럼 중 "col_" prefix로 시작하면 base로 인정
//            $base = '';
//            foreach ($allowed as $col) {
//                if (strpos($name, $col . '_') === 0) { $base = $col; break; }
//            }
//            if ($base === '') continue;
//
//            $p->setAccessible(true);
//            $val = $p->getValue($this->part);
//            if (is_array($val)) {
//                $maps[$name] = array('base' => $base, 'map' => $val);
//            }
//        }
//
//        $cache[$cls] = $maps;
//        return $maps;
//    }

    protected function get_schema_value_maps()
    {
        static $cache = array();
        $cls = is_object($this->part) ? get_class($this->part) : '';
        if (!$cls) return array();
        if (isset($cache[$cls])) return $cache[$cls];

        // ✅ 단순화: column_extend 메서드만 체크
        $maps = array();
        if (method_exists($this->part, 'column_extend')) {
            $maps['column_extend'] = array('type' => 'extend_method');
        }

        $cache[$cls] = $maps;
        return $maps;
    }
    //    protected function apply_value_maps(&$row)
//    {
//        $maps = $this->get_schema_value_maps();
//        $virt = array();
//        foreach ($maps as $vname => $spec) {
//            $base = isset($spec['base']) ? $spec['base'] : '';
//            $map  = isset($spec['map'])  ? $spec['map']  : array();
//            $code = isset($row[$base]) ? $row[$base] : '';
//            $row[$vname] = isset($map[$code]) ? $map[$code] : '';
//            $virt[$vname] = true;
//        }
//        $this->virtual_keys = array_keys($virt);
//    }

    public function apply_value_maps(&$row)
    {
        if (is_object($this->part) && method_exists($this->part, 'column_extend')) {

            $extended = $this->part->column_extend($row);

            if (is_array($extended)) {
                foreach($extended as $key => $value) {
                    if(isset($row[$this->part_key])){

                        $row[$this->part_key][$key]=$value;
                    }else{
                        $row[$key] = $value;
                    }

                }
            }
        }
    }

    protected function get_virtual_keys()
    {
        if ($this->virtual_keys === null) {
            $tmp = array();
            $maps = $this->get_schema_value_maps();
            foreach ($maps as $vname => $_) { $tmp[] = $vname; }
            $this->virtual_keys = $tmp;
        }
        return $this->virtual_keys;
    }

    /**
     * 목록파트 특정 id 항목 접근
     */
    public function get_item($item_id){
        if (!$this->is_list_part()) return null;
        $list = $this->ensure_list_rows();
        return isset($list[$item_id]) ? $list[$item_id] : null;
    }

    /**
     * 목록파트 id 목록 조회
     */
    public function get_ids(){
        if (!$this->is_list_part()) return array();
        $list = $this->ensure_list_rows();
        return array_keys($list);
    }

    /**
     * 목록파트 개수 조회
     */
    public function count(){
        if (!$this->is_list_part()) return 0;
        $list = $this->ensure_list_rows();
        return count($list);
    }
}
