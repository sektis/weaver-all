<?php
namespace weaver;
require_once dirname(__FILE__) . '/StoreSchemaInterface.php';
require_once dirname(__FILE__) . '/StoreSchemaBase.php';
require_once dirname(__FILE__) . '/Store.php';
require_once dirname(__FILE__) . '/StorePartProxy.php';

use weaver\store_manager\StoreSchemaInterface;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\Store;
use weaver\store_manager\StorePartProxy;

class StoreManager extends Makeable{

    protected $bo_table = '';
    protected $board = null;

    /** 업서트 허용 컬럼 (wr_id + 파트에서 합류) */
    protected $allowed_columns = array('wr_id');

    /** 바인딩된 스키마 파트 키 목록 */
    protected $bound_schema_parts = array();

    /** 파트 인스턴스 레지스트리: ['location' => object, ...] */
    protected $parts = array();

    protected $column_naming = 'auto_prefix'; // 'strict' 또는 'auto_prefix'
    protected $meta_column = array('id'  ,'ord','delete' );
    protected $file_meta_column = array('source' ,'path' ,'type','created_at' );
    protected $file_arr_key = array('name' ,'type' ,'tmp_name','error','size' );
    protected $colmap = array(); // ['location' => ['lat'=>'location_lat', ...], ...]

    protected $list_db_runtime = null; // ['enabled'=>bool,'part'=>string,'wr_id'=>int,'schema'=>object]
    /** @var array Store 객체 캐시 */
    protected $store_cache = array();

    /** @var array write_row 캐시 */
    protected $write_cache = array();

    /** @var array ext_row 캐시 */
    protected $ext_cache = array();

    /** @var array list_part 캐시 */
    protected $list_cache = array();

    /**
     * ($id, $bo_table, array $schema_parts) 시그니처
     * - Basic 파트는 항상 먼저 바인딩
     */
    public function __construct($id, $bo_table, $schema_parts = array()){
        $this->bind_board($bo_table);
        $this->ensure_base_table();

        // Basic 자동 바인딩
        $this->bind_schema('basic');

        // 추가 파트 바인딩(중복 방지)
        if (is_array($schema_parts) && count($schema_parts)) {
            foreach ($schema_parts as $key) {
                if (!in_array($key, $this->bound_schema_parts)) $this->bind_schema($key);
            }
        }
    }

    /** Makeable 훅(필요 시 1회 초기화) */
    public function init_once(){}

    /** g5_board 존재 확인 후 바인딩 */
    public function bind_board($bo_table){
        $row = $this->fetch_board_row($bo_table);
        if (!$row) {
            $this->error('존재하지 않는 게시판입니다. (bo_table=' . $bo_table . ')', 2);
        }
        $this->bo_table = $bo_table;
        $this->board    = $row;
    }

    public function get_bo_table(){
        return $this->bo_table;
    }

    public function get_board(){
        return $this->board;
    }

    /** 확장 테이블명: {prefix}store_{bo_table} */
    public function get_ext_table_name(){
        $prefix = isset($this->table_prefix) ? $this->table_prefix : 'wv_';
        return $prefix . 'store_' . $this->bo_table;
    }

    /** wr_id만 PK인 베이스 테이블 생성 */
    public function ensure_base_table(){
        $table = $this->get_ext_table_name();
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$table}` (
              `wr_id` INT(11) NOT NULL,
              PRIMARY KEY (`wr_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ";
        sql_query($sql, true);
    }

    /** 매직 접근자: wv()->store_manager->location */
    protected function _custom_get($name){
        if (isset($this->parts[$name])) return $this->parts[$name];
        return null;
    }

    /**
     * 스키마 파트 바인딩
     * - 로딩 규칙: \weaver\store_manager\parts\{Ucfirst($key)}
     * - StoreSchemaInterface 구현 필수
     * - StoreSchemaBase 존재 시 컨텍스트 주입
     * - 배열 저장 파트(array_part=true)는 {ext_table}_{part} 별도 테이블에 적용
     */
    public function bind_schema($key){
        if (!strlen($key)) return $this;

        $class = '\\weaver\\store_manager\\parts\\' . ucfirst($key);

        // 베이스/인터페이스 로드 보장
        $base = dirname(__FILE__) . '/StoreSchemaBase.php';
        if (!class_exists('\\weaver\\store_manager\\StoreSchemaBase') && file_exists($base)) require_once $base;

        // 파트 클래스 로컬 로드 (오토로드 실패 대비)
        if (!class_exists($class)) {
            $file = dirname(__FILE__) . '/parts/' . ucfirst($key) . '.php';
            if (file_exists($file)) require_once $file;
        }

        if (!class_exists($class)) {
            $this->error('스키마 파트 클래스를 찾을 수 없습니다. (key=' . $key . ', class=' . $class . ')', 2);
        }

        $schema = new $class();
        if (!($schema instanceof \weaver\store_manager\StoreSchemaInterface)) {
            $this->error('스키마 파트가 StoreSchemaInterface를 구현하지 않았습니다. (class=' . $class . ')', 2);
        }

        if (method_exists($schema, 'set_context')) {
            $schema->set_context($this, $this->bo_table, $key, $this->plugin_theme_path);
        }

        // 논리 정의
        $logical_cols = $schema->get_columns(); // ['lat'=>'DECIMAL...', 'name'=>'VARCHAR...']
        $idxs         = $schema->get_indexes(); // [{name,type,cols:[논리명...]}]
        $allowed_log  = $schema->get_allowed_columns();        // ['lat','name',...]

        // 배열 저장 파트면: 별도 테이블 적용, base(ext)에는 반영하지 않음
        if ($this->is_list_part_schema($schema)){
            $this->apply_list_part_schema($key, $schema);
            $this->parts[$key] = $schema;
            if (!in_array($key, $this->bound_schema_parts)) $this->bound_schema_parts[] = $key;
            return $this;
        }

        // 물리 변환 + 충돌 검사
        $physical_cols = array();
        $this->colmap[$key] = isset($this->colmap[$key]) ? $this->colmap[$key] : array();

        foreach ($logical_cols as $lname => $ddl){
            $pname = $this->physical_col($key, $lname);
            if ($this->column_naming === 'strict') {
                foreach ($this->colmap as $pk => $map){
                    if ($pk !== $key && in_array($pname, $map)) {
                        $this->error('컬럼 충돌: ' . $pname . ' (part=' . $key . ')', 2);
                    }
                }
            }
            $this->colmap[$key][$lname] = $pname;
            $physical_cols[$pname] = $ddl;
        }

        // 인덱스의 컬럼명도 물리명으로 치환
        $physical_idxs = array();
        if (is_array($idxs)) {
            foreach ($idxs as $ix){
                $cols  = isset($ix['cols']) ? $ix['cols'] : array();
                $pcols = array();
                foreach ($cols as $c){
                    $pcols[] = $this->get_physical_col($key, $c);
                }
                $ix['cols'] = $pcols;
                if (!isset($ix['name']) || !strlen($ix['name'])) {
                    $ix['name'] = $key . '_idx_' . implode('_', $pcols);
                }
                $physical_idxs[] = $ix;
            }
        }

        // 테이블 적용은 물리 컬럼/인덱스 기준
        $this->apply_columns($physical_cols);
        $this->apply_indexes($physical_idxs);

        // allowed_columns 는 물리 기준으로 병합
        if (is_array($allowed_log) && count($allowed_log)) {
            foreach ($allowed_log as $lname) {
                $pname = $this->get_physical_col($key, $lname);
                if (!in_array($pname, $this->allowed_columns)) $this->allowed_columns[] = $pname;
            }
        }


        // 레지스트리 등록
        $this->parts[$key] = $schema;
        if (!in_array($key, $this->bound_schema_parts)) $this->bound_schema_parts[] = $key;

        return $this;
    }

    /** 현재 바운드된 스키마 파트 키 목록 */
    public function get_bound_schema_parts(){
        return $this->bound_schema_parts;
    }

    /** 허용 컬럼 재계산(바운드된 파트 기준) */
    public function rebuild_allowed_columns(){
        $this->allowed_columns = array('wr_id');
        foreach ($this->bound_schema_parts as $key) {
            $class = '\\weaver\\store_manager\\parts\\' . ucfirst($key);
            if (!class_exists($class)) {
                $file = dirname(__FILE__) . '/parts/' . ucfirst($key) . '.php';
                if (file_exists($file)) require_once $file;
            }
            if (!class_exists($class)) continue;
            $schema = new $class();

            // 목록 파트는 base ext 컬럼 허용 목록에 포함하지 않음
            if ($this->is_list_part_schema($schema)) continue;

            if (method_exists($schema, 'get_allowed_columns')) {
                $allow = $schema->get_allowed_columns();
                if (is_array($allow)) {
                    foreach ($allow as $lname) {
                        $pname = $this->get_physical_col($key, $lname);
                        if (!in_array($pname, $this->allowed_columns)) $this->allowed_columns[] = $pname;
                    }
                }
            }
        }
        return $this->allowed_columns;
    }

    /** 컬럼 적용 (MySQL5.6 → SHOW COLUMNS로 사전 체크) */
    protected function apply_columns($columns){
        if (!is_array($columns) || !count($columns)) return;

        $table = $this->get_ext_table_name();
        foreach ($columns as $col => $ddl) {
            // 빈 DDL은 스킵(생성/허용 제외)
            if (!is_string($ddl) || !strlen(trim($ddl))) continue;

            if (!$this->table_has_column($table, $col)) {
                $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$ddl}";
                sql_query($sql, true);
            } else {
                // 타입/NULL/DEFAULT 차이 감지 → MODIFY
                $current = $this->get_column_info($table, $col); // SHOW COLUMNS
                if ($this->column_definition_differs($current, $ddl)) {
                    $sql = "ALTER TABLE `{$table}` MODIFY COLUMN `{$col}` {$ddl}";
                    sql_query($sql, true);
                }
            }
        }
    }

    /** 인덱스 적용 */
    protected function apply_indexes($indexes){
        if (!is_array($indexes) || !count($indexes)) return;

        $table = $this->get_ext_table_name();
        foreach ($indexes as $ix) {
            $name = isset($ix['name']) ? $ix['name'] : '';
            $type = isset($ix['type']) ? strtoupper($ix['type']) : 'INDEX';
            $cols = isset($ix['cols']) ? $ix['cols'] : array();
            if (!strlen($name) || !is_array($cols) || !count($cols)) continue;

            if (!$this->table_has_index($table, $name)) {
                $qcols = array(); foreach ($cols as $c) { $qcols[] = '`' . $c . '`'; }
                $sql = "ALTER TABLE `{$table}` ADD {$type} `{$name}` (" . implode(',', $qcols) . ")";
                sql_query($sql, true);
            }
        }
    }

    /** 현재 확장 테이블 컬럼 목록 */
    public function get_current_table_columns(){
        $table = $this->get_ext_table_name();
        $cols = array();
        $res = sql_query("SHOW COLUMNS FROM `{$table}`");
        while ($row = sql_fetch_array($res)) {
            if (isset($row['Field'])) $cols[] = $row['Field'];
        }
        return $cols;
    }

    /**
     * 스키마 차이 계산
     * - defined: 파트들이 정의한 컬럼(= $this->allowed_columns)
     * - current: 실제 테이블 컬럼
     * @return array array('missing'=>[], 'extraneous'=>[])
     */
    public function get_schema_diff(){
        $defined = array_values(array_unique($this->allowed_columns));
        if (!in_array('wr_id', $defined)) $defined[] = 'wr_id';

        $current = $this->get_current_table_columns();

        $missing = array();
        foreach ($defined as $c) {
            if (!in_array($c, $current)) $missing[] = $c;
        }

        $extraneous = array();
        foreach ($current as $c) {
            if (!in_array($c, $defined) && $c !== 'wr_id') $extraneous[] = $c;
        }

        return array('missing' => $missing, 'extraneous' => $extraneous);
    }

    /**
     * 컬럼 정리(삭제)
     * - $columns_to_drop 비우면 현재 정의에 없는 컬럼(extraneous)을 모두 삭제
     * - wr_id는 항상 보호
     * @param array $columns_to_drop
     * @return array 실제로 삭제된 컬럼 목록
     */
    public function prune_columns($columns_to_drop = array(), $include_list_parts = true){
        $table = $this->get_ext_table_name();

        // ==== 1) 기본 확장 테이블 정리(현행 유지) ====
        if (!is_array($columns_to_drop) || !count($columns_to_drop)) {
            $diff = $this->get_schema_diff(); // defined vs current
            $columns_to_drop = isset($diff['extraneous']) ? $diff['extraneous'] : array();
        }

        $to_drop = array();
        foreach ($columns_to_drop as $c) {
            if ($c === 'wr_id') continue;
            $to_drop[] = $c;
        }

        $dropped = array();
        foreach ($to_drop as $col) {
            if ($this->table_has_column($table, $col)) {
                $sql = "ALTER TABLE `{$table}` DROP COLUMN `{$col}`";
                sql_query($sql, true);
                $dropped[] = $col;
            }
        }

        // ==== 2) 목록 파트 테이블 정리(추가) ====
        if ($include_list_parts && is_array($this->parts) && count($this->parts)) {
            foreach ($this->parts as $part_key => $schema) {
                // 목록 파트가 아니면 스킵
                if (!$this->is_list_part_schema($schema)) continue;

                // 해당 목록 파트의 정의/현재 컬럼 수집
                $array_table = method_exists($this, 'get_list_table_name')
                    ? $this->get_list_table_name($part_key)
                    : ($this->get_ext_table_name().'_'.strtolower($part_key));

                $current_cols = $this->get_current_list_table_columns($part_key);
                if (!count($current_cols)) continue;

                $defined = array(); // 파트가 정의한 "논리 컬럼명"
                if (method_exists($schema, 'get_columns')) {
                    $cols = (array)$schema->get_columns($this->bo_table);
                    foreach ($cols as $lname => $ddl) {
                        // 빈 DDL은 사실상 미사용 컬럼으로 취급(생성 안 됨) → 여기선 '정의'로 보지 않음
                        if (!is_string($ddl) || !strlen(trim($ddl))) continue;
                        $defined[] = $lname;
                    }
                }

                // extraneous = 현재 - (reserved + 정의)
                $reserved = array('id', 'wr_id','ord'); // 절대 삭제 금지
                $defined_map = array(); foreach ($defined as $_c){ $defined_map[$_c] = true; }
                $reserved_map = array(); foreach ($reserved as $_c){ $reserved_map[$_c] = true; }

                $drop_list = array();
                foreach ($current_cols as $cname) {
                    if (isset($reserved_map[$cname])) continue;
                    if (!isset($defined_map[$cname])) $drop_list[] = $cname;
                }

                foreach ($drop_list as $col) {
                    if ($this->table_has_column($array_table, $col)) {
                        $sql = "ALTER TABLE `{$array_table}` DROP COLUMN `{$col}`";
                        sql_query($sql, true);
                    }
                }
            }
        }

        return $dropped;
    }

    // StoreManager.php에 추가할 안전한 컬럼 관리 메서드들

    /**
     * 안전 모드: 추가된 컬럼만 생성하고 삭제는 하지 않음
     * 서비스 운영 중 안전하게 스키마 업데이트할 때 사용
     * @param bool $include_list_parts 목록 파트도 포함할지 여부
     * @return array 실제로 추가된 컬럼 목록
     */
    public function sync_columns_safe($include_list_parts = true){
        $added = array();

        // === 1) 일반 파트 컬럼 안전 추가 ===
        $diff = $this->get_schema_diff();
        $missing_cols = isset($diff['missing']) ? $diff['missing'] : array();

        if (count($missing_cols)) {
            // 물리 컬럼 → DDL 매핑 구성
            $add_cols = array();
            foreach ($this->parts as $pkey => $schema) {
                if ($this->is_list_part_schema($schema)) continue; // 목록 파트는 별도 처리

                $logical_cols = $schema->get_columns($this->bo_table);
                foreach ($logical_cols as $lname => $ddl) {
                    $pname = $this->get_physical_col($pkey, $lname);
                    if (in_array($pname, $missing_cols)) {
                        $add_cols[$pname] = $ddl;
                    }
                }
            }

            // 추가 실행
            $table = $this->get_ext_table_name();
            foreach ($add_cols as $col => $ddl) {
                if (!is_string($ddl) || !strlen(trim($ddl))) continue;
                if (!$this->table_has_column($table, $col)) {
                    $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$ddl}";
                    sql_query($sql, true);
                    $added[] = $col;
                }
            }
        }

        // === 2) 목록 파트 테이블 안전 추가 ===
        if ($include_list_parts && is_array($this->parts)) {
            foreach ($this->parts as $part_key => $schema) {
                if (!$this->is_list_part_schema($schema)) continue;

                $list_table = $this->get_list_table_name($part_key);
                $def_cols = $schema->get_columns($this->bo_table);

                foreach ($def_cols as $cname => $ddl) {
                    if ($cname === 'id' || $cname === 'wr_id' || $cname === 'ord') continue;
                    if (!is_string($ddl) || !strlen(trim($ddl))) continue;

                    if (!$this->table_has_column($list_table, $cname)) {
                        $sql = "ALTER TABLE `{$list_table}` ADD COLUMN `{$cname}` {$ddl}";
                        sql_query($sql, true);
                        $added[] = "{$part_key}.{$cname}";
                    }
                }
            }
        }

        return $added;
    }

    /**
     * 특정 파트의 특정 컬럼만 삭제 (명시적 삭제)
     * 개발자가 의도적으로 컬럼을 제거할 때만 사용
     * @param string $part_key 파트 키 (예: 'basic', 'location')
     * @param string|array $columns 삭제할 컬럼명(논리명 또는 물리명)
     * @param bool $is_logical true면 논리명, false면 물리명으로 처리
     * @return array 실제로 삭제된 컬럼 목록
     */
    public function drop_columns_explicit($part_key, $columns, $is_logical = true){
        if (!is_array($columns)) {
            $columns = array($columns);
        }

        $dropped = array();

        if (!isset($this->parts[$part_key])) {
            return $dropped; // 파트가 없으면 종료
        }

        $schema = $this->parts[$part_key];
        $is_list_part = $this->is_list_part_schema($schema);

        if ($is_list_part) {
            // === 목록 파트 컬럼 삭제 ===
            $table = $this->get_list_table_name($part_key);

            foreach ($columns as $col) {
                // 보호 컬럼 체크
                if ($col === 'id' || $col === 'wr_id' || $col === 'ord') {
                    continue; // 필수 컬럼은 삭제 불가
                }

                $physical_col = $is_logical ? $col : $col; // 목록 파트는 논리명=물리명

                if ($this->table_has_column($table, $physical_col)) {
                    $sql = "ALTER TABLE `{$table}` DROP COLUMN `{$physical_col}`";
                    sql_query($sql, true);
                    $dropped[] = "{$part_key}.{$physical_col}";
                }
            }

        } else {
            // === 일반 파트 컬럼 삭제 ===
            $table = $this->get_ext_table_name();

            foreach ($columns as $col) {
                $physical_col = $is_logical ? $this->get_physical_col($part_key, $col) : $col;

                // 보호 컬럼 체크
                if ($physical_col === 'wr_id') {
                    continue; // wr_id는 삭제 불가
                }

                if ($this->table_has_column($table, $physical_col)) {
                    $sql = "ALTER TABLE `{$table}` DROP COLUMN `{$physical_col}`";
                    sql_query($sql, true);
                    $dropped[] = $physical_col;

                    // allowed_columns에서도 제거
                    $key = array_search($physical_col, $this->allowed_columns);
                    if ($key !== false) {
                        unset($this->allowed_columns[$key]);
                        $this->allowed_columns = array_values($this->allowed_columns);
                    }
                }
            }
        }

        return $dropped;
    }

    /**
     * 고아 컬럼 확인 (정의에 없는 컬럼들)
     * 삭제하기 전에 어떤 컬럼들이 삭제 대상인지 미리 확인
     * @param bool $include_list_parts 목록 파트도 포함할지 여부
     * @return array 삭제 대상 컬럼 목록
     */
    public function get_orphan_columns($include_list_parts = true){
        $orphans = array();

        // === 1) 일반 파트 고아 컬럼 ===
        $diff = $this->get_schema_diff();
        $extraneous = isset($diff['extraneous']) ? $diff['extraneous'] : array();

        foreach ($extraneous as $col) {
            if ($col !== 'wr_id') { // wr_id는 항상 보호
                $orphans['main'][] = $col;
            }
        }

        // === 2) 목록 파트 고아 컬럼 ===
        if ($include_list_parts && is_array($this->parts)) {
            foreach ($this->parts as $part_key => $schema) {
                if (!$this->is_list_part_schema($schema)) continue;

                $list_table = $this->get_list_table_name($part_key);
                $def_cols = $schema->get_columns($this->bo_table);
                $def_names = array_keys($def_cols);
                $def_names[] = 'id';
                $def_names[] = 'wr_id';
                $def_names[] = 'ord';

                // 현재 테이블의 컬럼들
                $current_cols = array();
                $result = sql_query("SHOW COLUMNS FROM `{$list_table}`");
                while ($row = sql_fetch_array($result)) {
                    if (isset($row['Field'])) {
                        $current_cols[] = $row['Field'];
                    }
                }

                // 정의에 없는 컬럼 찾기
                foreach ($current_cols as $col) {
                    if (!in_array($col, $def_names)) {
                        $orphans[$part_key][] = $col;
                    }
                }
            }
        }

        return $orphans;
    }

    /**
     * 컬럼 정보 상세 조회 (디버깅/확인용)
     * @param string $part_key 파트 키 (빈 값이면 메인 테이블)
     * @return array 컬럼 정보 배열
     */
    public function get_column_info_detailed($part_key = ''){
        $info = array();

        if ($part_key === '') {
            // === 메인 확장 테이블 ===
            $table = $this->get_ext_table_name();
            $info['table'] = $table;
            $info['type'] = 'main';

            // 정의된 컬럼
            $info['defined'] = array_values(array_unique($this->allowed_columns));

            // 현재 컬럼
            $info['current'] = $this->get_current_table_columns();

            // 차이점
            $diff = $this->get_schema_diff();
            $info['missing'] = $diff['missing'];
            $info['extraneous'] = $diff['extraneous'];

        } else {
            // === 특정 파트 테이블 ===
            if (!isset($this->parts[$part_key])) {
                return array('error' => 'Part not found: ' . $part_key);
            }

            $schema = $this->parts[$part_key];
            $is_list_part = $this->is_list_part_schema($schema);

            $info['part_key'] = $part_key;
            $info['type'] = $is_list_part ? 'list_part' : 'regular_part';

            if ($is_list_part) {
                $table = $this->get_list_table_name($part_key);
                $info['table'] = $table;

                // 정의된 컬럼
                $def_cols = $schema->get_columns($this->bo_table);
                $info['defined'] = array_merge(array('id', 'wr_id', 'ord'), array_keys($def_cols));

                // 현재 컬럼
                $current = array();
                $result = sql_query("SHOW COLUMNS FROM `{$table}`");
                while ($row = sql_fetch_array($result)) {
                    if (isset($row['Field'])) {
                        $current[] = $row['Field'];
                    }
                }
                $info['current'] = $current;

                // 차이점
                $missing = array();
                foreach ($info['defined'] as $col) {
                    if (!in_array($col, $current)) {
                        $missing[] = $col;
                    }
                }

                $extraneous = array();
                foreach ($current as $col) {
                    if (!in_array($col, $info['defined'])) {
                        $extraneous[] = $col;
                    }
                }

                $info['missing'] = $missing;
                $info['extraneous'] = $extraneous;

            } else {
                $info['table'] = $this->get_ext_table_name();
                $logical_cols = $schema->get_columns($this->bo_table);
                $physical_cols = array();

                foreach ($logical_cols as $lname => $ddl) {
                    $pname = $this->get_physical_col($part_key, $lname);
                    $physical_cols[$lname] = $pname;
                }

                $info['logical_columns'] = array_keys($logical_cols);
                $info['physical_columns'] = array_values($physical_cols);
                $info['mapping'] = $physical_cols;
            }
        }

        return $info;
    }

    /** 현재 확장 테이블 인덱스 목록 반환 (PRIMARY 제외) */
    public function get_current_table_indexes(){
        $table = $this->get_ext_table_name();
        $indexes = array();
        $res = sql_query("SHOW INDEX FROM `{$table}`");
        while ($row = sql_fetch_array($res)) {
            if (!isset($row['Key_name'])) continue;
            $name = $row['Key_name'];
            if ($name === 'PRIMARY') continue;
            if (!isset($indexes[$name])) $indexes[$name] = array();
            if (isset($row['Column_name'])) $indexes[$name][] = $row['Column_name'];
        }
        return $indexes;
    }

    /** 모든 파트 정의 인덱스 합산 */
    public function get_all_defined_indexes(){
        $all = array();
        foreach ($this->bound_schema_parts as $key) {
            $class = '\\weaver\\store_manager\\parts\\' . ucfirst($key);
            if (!class_exists($class)) {
                $file = dirname(__FILE__) . '/parts/' . ucfirst($key) . '.php';
                if (file_exists($file)) require_once $file;
            }
            if (!class_exists($class)) continue;
            $schema = new $class();
            if (method_exists($schema, 'get_indexes')) {
                $ix = $schema->get_indexes($this->bo_table);
                if (is_array($ix) && count($ix)) $all = array_merge($all, $ix);
            }
        }
        return $all;
    }

    /**
     * 인덱스 차이 계산
     * - defined: 파트들이 정의한 인덱스 이름 집합
     * - current: 실제 테이블 인덱스 이름 집합
     */
    public function get_index_diff($defined_indexes){
        $defined_names = array();
        if (is_array($defined_indexes)) {
            foreach ($defined_indexes as $ix) {
                if (isset($ix['name']) && strlen($ix['name'])) $defined_names[] = $ix['name'];
            }
        }

        $current_map = $this->get_current_table_indexes();
        $current_names = array_keys($current_map);

        $missing = array();
        foreach ($defined_names as $n) {
            if (!in_array($n, $current_names)) $missing[] = $n;
        }

        $extraneous = array();
        foreach ($current_names as $n) {
            if (!in_array($n, $defined_names)) $extraneous[] = $n;
        }

        return array('missing' => $missing, 'extraneous' => $extraneous);
    }

    /**
     * 인덱스 정리(삭제)
     * - $index_names_to_drop 비우면 현재 정의에 없는 인덱스 전부 삭제
     * - PRIMARY는 건드리지 않음
     * @param array $index_names_to_drop
     * @param array $defined_indexes 현재 정의(파트 합산) 배열
     * @return array 실제로 삭제된 인덱스명 목록
     */
    public function prune_indexes($index_names_to_drop = array(), $defined_indexes = array()){
        $table = $this->get_ext_table_name();

        if (!is_array($index_names_to_drop) || !count($index_names_to_drop)) {
            $diff = $this->get_index_diff($defined_indexes);
            $index_names_to_drop = isset($diff['extraneous']) ? $diff['extraneous'] : array();
        }

        $dropped = array();
        foreach ($index_names_to_drop as $name) {
            if ($name === 'PRIMARY') continue;
            if ($this->table_has_index($table, $name)) {
                $sql = "ALTER TABLE `{$table}` DROP INDEX `{$name}`";
                sql_query($sql, true);
                $dropped[] = $name;
            }
        }
        return $dropped;
    }

    /**
     * 저장(set)
     * - 폼이 part 중첩 배열 구조여도(set만으로) 처리되도록 내부 평탄화
     * - wr_subject 비어오면 '/' 기본값 보정
     * - wv_write_board() 호출 결과가 문자열(에러)이면 즉시 error()
     * - 목록 파트(array_part=true)는 별도 테이블에 자동 저장
     * @return int wr_id
     */
    public function set($data = array()){
        global $member;
        $table = $this->get_ext_table_name();
        if (!is_array($data)) $data = array();


        // 기존 wr_id가 있으면 이전 확장로우를 미리 읽어둠(일반 파트 b64 병합/보존용)
        $existing_wr_id = isset($data['wr_id']) ? (int)$data['wr_id'] : 0;

        $this->execute_before_set_hooks($data, $existing_wr_id);




        if ($existing_wr_id <= 0) {
            if (!isset($data['wr_subject']) || !strlen(trim($data['wr_subject']))) {
                $data['wr_subject'] = '/';
            }
            $wr_id = $this->create_post_stub_and_get_wr_id($data);
            $data['wr_id'] = $wr_id;
            $existing_wr_id = $wr_id;
        }else{
            $data['w']='u';


            $this->create_post_stub_and_get_wr_id($data);
        }

        $wr_id = $existing_wr_id;

        $prev_ext_row   = $existing_wr_id > 0 ? $this->fetch_store_row($existing_wr_id) : array();



        // === 일반 파트: 배열/파일 필드 공통 처리 ===
        if (is_array($this->parts) && count($this->parts)) {
            foreach ($this->parts as $pkey => $schema) {

                $allowed = $schema->get_allowed_columns();
                $is_list_part = $this->is_list_part_schema($schema);
                if ($is_list_part) {
                    $allowed = array($pkey);
                    $def = array_merge($schema->get_columns($this->bo_table),array_flip($this->meta_column));

                    $def_cols = array(); foreach ($def as $cname => $_ddl){ $def_cols[$cname] = true; }

                    $t   = $this->get_list_table_name($pkey);
                    $wr  = (int)$existing_wr_id;

                    $sel = array_unique(array_merge(array('id','wr_id'),$schema->get_allowed_columns()));

                    $sql = "SELECT ".implode(',', array_map(function($c){ return '`'.$c.'`'; }, $sel))." FROM `{$t}` WHERE wr_id='{$wr}'";
                    $rs = sql_query($sql);
                    $list_part_rows = array();
                    while($r = sql_fetch_array($rs)){
                        $nr = array();
                        foreach ($r as $key=>$val){
                            $nr[$key]=wv_base64_decode_unserialize($val);
                        }
                        $list_part_rows[] = $nr;
                     }

                    $prev_ext_row[$pkey] = wv_base64_encode_serialize($list_part_rows);

                    $list_part_form_array_intersect=array();
                    foreach ($data[$pkey] as $i => $row) {

                        $list_part_form_array_intersect[$i] = array_intersect_key($row, $def_cols);
                    }
                    $data[$pkey] = $list_part_form_array_intersect;

                }


                if (!is_array($allowed) || !count($allowed)) continue;
                if (!isset($data[$pkey]) || !is_array($data[$pkey])) $data[$pkey] = array();



                foreach ($allowed as $logical_col) {

                    if (!isset($data[$pkey]) || !is_array($data[$pkey])) $data[$pkey] = array();

                    if($logical_col==$pkey){
                        $logical_col='';
                        $data_pkey_logical_col = &$data[$pkey];

                    }else{
                        $data_pkey_logical_col = &$data[$pkey][$logical_col];
                    }

                    $file_upload_array = wv_parse_file_array_tree($pkey,$logical_col);


                    if($file_upload_array){
                        if(!isset($data_pkey_logical_col)){
                            $data_pkey_logical_col=array();
                        }

                        $data_pkey_logical_col = wv_merge_by_key_recursive($data_pkey_logical_col,$file_upload_array);

                    }


                    $phys = $this->get_physical_col($pkey, $logical_col);

                    $prev_serialized = isset($prev_ext_row[$phys]) ? $prev_ext_row[$phys] : '';

                    $prev_decoded    = wv_base64_decode_unserialize($prev_serialized);

                    $walk_function = function (&$arr,$arr2,$node) use($is_list_part,&$data_pkey_logical_col,&$walk_function,$data,$prev_decoded) {


                        if(!is_array($arr)){
                            if(is_array($arr2)){
                                $arr=$arr2;
                            }
                            return false;
                        }

                        $parent_key = wv_array_last($node);
                        $is_old_file = wv_array_has_all_keys($this->file_meta_column ,$arr2);
                        $is_new_file = wv_array_has_all_keys($this->file_arr_key ,$arr);
                        $int_key = is_numeric($parent_key);
                        $is_new = ($int_key or $is_new_file) ;
                        if(isset($arr['id']) and $arr['id']){
                            $is_new=false;
                        }
                        $is_delete = isset($arr['delete']);




                        if($is_delete and $is_new){
                            alert('delete : id가 없습니다.');
                        }
                        if($is_new and $arr2['id']){
                            alert('insert : key 중복생성');
                        }
                        if(!$is_new and $arr['id']!=$arr2['id']){

                            alert('update : id 체크 오류');
                        }
                        $i=0;
                        foreach ($arr as $k=>&$v){
                            if(is_numeric($k) and  !$v['delete'] and array_filter($v)){
                                $v['ord']=$i;
                                $i++;
                            }
                            if(!$is_delete){

                                  wv_walk_by_ref_diff($v,$walk_function,$arr2[$k],array_merge($node,(array)$k));

                            }
                        }

                        if($is_new_file and !$is_delete){

                            $save_result = $this->save_uploaded_files($arr,'weaver/store_manager/'.$this->bo_table.'/'.date('Ym'));
                            foreach ($this->file_arr_key as $key) {
                                unset($arr[$key]);
                            }

                            $arr = array_merge($arr,$save_result);
                            if($is_old_file){
                                $this->delete_physical_paths_safely(array($arr2['path']));
                            }

                        }




                        if($is_new){

                            if(wv_empty_except_keys($arr,array('ord'))){
                                $combined = 'unset($data_pkey_logical_col'. wv_array_to_text($node,"['","']").');';

                                @eval("$combined;");
                                return false;
                            }


                            if(!$is_list_part or (($is_list_part and count($node)<2) === false)){
                                $arr['id'] = uniqid().$parent_key;
                            }
                        }else{



                            if($is_delete){

                                wv_walk_by_ref_diff($arr,function (&$arr,$arr2,$node){
                                    if(wv_array_has_all_keys($this->file_meta_column ,$arr2)){
                                        $this->delete_physical_paths_safely(array($arr2['path']));
                                    }
                                },$arr2);

                                if(!$is_list_part or (($is_list_part and count($node)<2) === false)){
                                    $combined = 'unset($data_pkey_logical_col'. wv_array_to_text($node,"['","']").');';

                                    @eval("$combined;");
                                }
                                return false;
                            }else{
                                if($is_old_file){
                                    $arr = array_merge($arr2,$arr);
                                }
                            }

                        }



                        return false;

                    };

                    wv_walk_by_ref_diff($data_pkey_logical_col,$walk_function,$prev_decoded);

                    if(is_array($data_pkey_logical_col) and !count($data_pkey_logical_col)){
                        $data_pkey_logical_col = '';
                    }


                }


                if ($is_list_part) {
                    foreach ($data_pkey_logical_col as $row){
                        if($row['id'] and $row['delete']){
                            sql_query("DELETE FROM `{$t}` WHERE wr_id='".intval($wr)."' AND id='".intval($row['id'])."'");
                            continue;
                        }

                        $sets = array();


                        $sets['ord'] = sql_escape_string((string)$row['ord']);

                        foreach ($def_cols as $col => $_u){

                            if ($col==='id' or $col=='delete'  ) continue;

                            if(is_array($row[$col])){
                                $row[$col] = wv_base64_encode_serialize($row[$col]);
                            }
                            $sets[$col] = sql_escape_string($row[$col]);
                        }
                        if($row['id']){
                            sql_query("UPDATE `{$t}` SET ".wv_array_to_sql_set($sets)." WHERE id='".intval($row['id'])."' AND wr_id='".intval($wr)."'", true);
                        }elseif(array_filter($row)){
                            $sets['wr_id'] = $wr;
                            sql_query("INSERT INTO `{$t}` SET ".wv_array_to_sql_set($sets), true);
                        }

                    }
                }
            }
        }


        // === 평면화: 일반 파트만 물리 컬럼으로 펼치고 part 키 제거(목록 파트 제외) ===
        if (count($this->parts)) {
            foreach ($this->parts as $key => $schema) {
                if (!isset($data[$key]) || !is_array($data[$key])) continue;
                if ($this->is_list_part_schema($schema)) continue;

                foreach ($data[$key] as $k => $v) {
                    $phys = $this->get_physical_col($key, $k);
                    if (!array_key_exists($phys, $data)) $data[$phys] = $v;
                }
                unset($data[$key]);
            }
        }




        // === 확장테이블 업서트(허용 컬럼만) ===
        $filtered = array('wr_id' => $wr_id);
        foreach ($data as $k => $v) {
            if (in_array($k, $this->allowed_columns)) $filtered[$k] = $v;
        }

        if (count($filtered) > 1) {
            $cols = array(); $vals = array(); $updates = array();
            foreach ($filtered as $k => $v) {
                $cols[] = "`{$k}`";
                if(is_array($v)){
                    $v=wv_base64_encode_serialize($v);
                }

                if ($v === null || (is_string($v) && strtoupper($v) === 'NULL')) {
                    $vals[] = "''";
                    if ($k !== 'wr_id') $updates[] = "`{$k}`=''";
                } else {
                    $vals[] = "'" . sql_escape_string($v) . "'";
                    if ($k !== 'wr_id') $updates[] = "`{$k}`=VALUES(`{$k}`)";
                }
            }

            $sql = "INSERT INTO `{$table}` (".implode(',', $cols).") VALUES (".implode(',', $vals).")
                ON DUPLICATE KEY UPDATE ".(count($updates) ? implode(',', $updates) : "`wr_id`=`wr_id`");

            sql_query($sql, true);
        }
        $this->execute_after_set_hooks($data, $wr_id);
        // === 목록 파트 저장 ===
        $this->clear_cache($wr_id);

        return $wr_id;
    }

    /** 삭제 */
    public function delete($wr_id){
        global $g5;
        $wr_ids = (array) $wr_id;
        $options = array(
            'where' =>    array(
                " w.wr_id in ('" . implode("','", $wr_ids) . "') ",
            ),
            'order_by' => 'w.wr_id asc',
            'rows' => 1000,  // 최대 1000개까지,

        );
        $result = $this->get_list($options);

        $table = $this->get_ext_table_name();

        foreach ($result['list'] as $row){
            $this->execute_before_delete_hooks($row, $row['wr_id']);
            if (is_array($this->parts) && count($this->parts)) {
                foreach ($this->parts as $pkey => $schema) {
                    $arr = $arr2 = $row[$pkey];
                    wv_walk_by_ref_diff($arr,function (&$arr,$arr2,$node){
                        if(wv_array_has_all_keys($this->file_meta_column ,$arr2)){
                            $this->delete_physical_paths_safely(array($arr2['path']));
                        }
                    },$arr2);
                    $is_list_part = $this->is_list_part_schema($schema);
                    if ($is_list_part) {
                        $t   = $this->get_list_table_name($pkey);
                        sql_query("DELETE FROM `{$t}` WHERE wr_id='{$row['wr_id']}'");
                    }else{
                        sql_query("DELETE FROM `{$table}` WHERE wr_id='{$row['wr_id']}'");
                    }
                }
            }
            wv_delete_board_row($this->bo_table,$row['wr_id']);
            $this->execute_after_delete_hooks($row, $row['wr_id']);
        }
        $write_table = $this->get_write_table_name();
        // 게시판의 글 수
        $sql = " select count(*) as cnt from {$write_table} where wr_is_comment = 0 ";
        $row = sql_fetch($sql);
        $bo_count_write = $row['cnt'];

        // 게시판의 코멘트 수
        $sql = " select count(*) as cnt from {$write_table} where wr_is_comment = 1 ";
        $row = sql_fetch($sql);
        $bo_count_comment = $row['cnt'];

        $sql = " select a.wr_id, (count(b.wr_parent) - 1) as cnt from {$write_table} a, {$write_table} b where a.wr_id=b.wr_parent and a.wr_is_comment=0 group by a.wr_id ";
        $result = sql_query($sql);
        for ($i = 0; $row = sql_fetch_array($result); $i++) {

            sql_query(" update {$write_table} set wr_comment = '{$row['cnt']}' where wr_id = '{$row['wr_id']}' ");
        }



        $sql = " update {$g5['board_table']}
                set bo_count_write = '{$bo_count_write}',
                    bo_count_comment = '{$bo_count_comment}' 
              where bo_table = '{$this->bo_table}' ";
        sql_query($sql,1);

        wv_json_exit(array('msg'=>'삭제완료','reload'=>1));

    }

    protected function execute_before_set_hooks(&$data, $wr_id) {
        if (!is_array($this->parts) || !count($this->parts)) {
            return;
        }

        foreach ($this->parts as $part_key => $schema) {
            if (!is_object($schema) || !method_exists($schema, 'before_set')) {
                continue;
            }

            try {
                // before_set(데이터, 수정여부, wr_id, 파트키, 매니저)
                $schema->before_set($data, $wr_id, $part_key, $this);

                // 로그 (선택사항)
                if (function_exists('write_log')) {
                    write_log("StoreManager: {$part_key} before_set executed", G5_DATA_PATH . '/log/store_hooks.log');
                }

            } catch (\Exception $e) {

            }
        }
    }

    /**
     * After Set 훅 실행
     */
    protected function execute_after_set_hooks($data,$wr_id) {
        if (!is_array($this->parts) || !count($this->parts)) {
            return;
        }

        foreach ($this->parts as $part_key => $schema) {
            if (!is_object($schema) || !method_exists($schema, 'after_set')) {
                continue;
            }

            try {
                // after_set(데이터, 수정여부, wr_id, 파트키, 매니저)
                $schema->after_set($data, $wr_id, $part_key, $this);

                // 로그 (선택사항)
                if (function_exists('write_log')) {
                    write_log("StoreManager: {$part_key} after_set executed", G5_DATA_PATH . '/log/store_hooks.log');
                }

            } catch (\Exception $e) {
                // 에러 처리 (after_set은 보통 중단하지 않음)
                $error_msg = "StoreManager after_set error in {$part_key}: " . $e->getMessage();
                if (function_exists('write_log')) {
                    write_log($error_msg, G5_DATA_PATH . '/log/store_errors.log');
                }
            }
        }
    }


    protected function execute_before_delete_hooks(&$data, $wr_id) {
        if (!is_array($this->parts) || !count($this->parts)) {
            return;
        }

        foreach ($this->parts as $part_key => $schema) {
            if (!is_object($schema) || !method_exists($schema, 'before_delete')) {
                continue;
            }

            try {
                // before_set(데이터, 수정여부, wr_id, 파트키, 매니저)
                $schema->before_delete($data, $wr_id, $part_key, $this);

                // 로그 (선택사항)
                if (function_exists('write_log')) {
                    write_log("StoreManager: {$part_key} before_set executed", G5_DATA_PATH . '/log/store_hooks.log');
                }

            } catch (\Exception $e) {
                // 에러 처리
                $error_msg = "StoreManager before_set error in {$part_key}: " . $e->getMessage();
                if (function_exists('write_log')) {
                    write_log($error_msg, G5_DATA_PATH . '/log/store_errors.log');
                }

                // 필요시 에러로 중단
                if (method_exists($schema, 'is_before_set_critical') && $schema->is_before_set_critical()) {
                    $this->error($error_msg);
                }
            }
        }
    }

    protected function execute_after_delete_hooks($data,$wr_id) {
        if (!is_array($this->parts) || !count($this->parts)) {
            return;
        }

        foreach ($this->parts as $part_key => $schema) {
            if (!is_object($schema) || !method_exists($schema, 'after_delete')) {
                continue;
            }

            try {
                // after_set(데이터, 수정여부, wr_id, 파트키, 매니저)
                $schema->after_delete($data, $wr_id, $part_key, $this);

                // 로그 (선택사항)
                if (function_exists('write_log')) {
                    write_log("StoreManager: {$part_key} after_set executed", G5_DATA_PATH . '/log/store_hooks.log');
                }

            } catch (\Exception $e) {
                // 에러 처리 (after_set은 보통 중단하지 않음)
                $error_msg = "StoreManager after_set error in {$part_key}: " . $e->getMessage();
                if (function_exists('write_log')) {
                    write_log($error_msg, G5_DATA_PATH . '/log/store_errors.log');
                }
            }
        }
    }



    /** 단건 조회 → Store 객체 (write + ext row 동시 보유) */
    public function get($wr_id=''){
        $wr_id = (int)$wr_id;

        // ✅ 캐시에서 먼저 확인
        if (isset($this->store_cache[$wr_id])) {
            return $this->store_cache[$wr_id];
        }


        // write/ext 먼저 로드
        $write_row = $this->fetch_write_row_cached($wr_id);
        $ext_row   = $this->fetch_store_row_cached($wr_id);
        if (!isset($write_row['wr_id'])) $write_row['wr_id'] = $wr_id;
        if (!isset($ext_row['wr_id']))   $ext_row['wr_id']   = $wr_id;

        // 컨테이너
        $store = new Store($this, $wr_id, $write_row, $ext_row);

        // 목록 파트 데이터 미리 모으기
        $ap = $this->fetch_list_part_rows_for_wr_ids_cached(array($wr_id));

        // ✅ 모든 파트(일반 + 목록) 프록시로 감싸기
        foreach ($this->parts as $pkey => $schema) {
            $schema->set_store($store);
            $proxy = new \weaver\store_manager\StorePartProxy($this, $wr_id, $schema, $ext_row, $pkey);
            if ($this->is_list_part_schema($schema)) {

                $proxy->list = isset($ap[$pkey][$wr_id]) ? $ap[$pkey][$wr_id] : array();
            }
            $store->$pkey = $proxy;
        }
        $this->store_cache[$wr_id] = $store;

        return $store;
    }

    public function get_parts(){
        return $this->parts; // bind_schema에서 채운 parts 배열
    }




    /** 유틸: 컬럼 존재 여부 */
    protected function table_has_column($table, $column){
        $sql = "SHOW COLUMNS FROM `{$table}` LIKE '" . sql_escape_string($column) . "'";
        $row = sql_fetch($sql);
        return $row ? true : false;
    }

    /** 유틸: 인덱스 존재 여부 */
    protected function table_has_index($table, $index_name){
        $sql = "SHOW INDEX FROM `{$table}` WHERE Key_name = '" . sql_escape_string($index_name) . "'";
        $row = sql_fetch($sql);
        return $row ? true : false;
    }

    /** g5_board 조회 */
    protected function fetch_board_row($bo_table){
        global $g5;
        $table = isset($g5['board_table']) ? $g5['board_table'] : G5_TABLE_PREFIX . 'board';
        $sql = "SELECT * FROM {$table} WHERE bo_table = '{$bo_table}' LIMIT 1";
        $row = sql_fetch($sql);
        return $row ? $row : null;
    }

    /**
     * 게시글 자동 생성 스텁
     * - common.lib.php의 wv_write_board() 호출
     * - 성공 시 wr_id 정수 리턴, 실패 시 error()
     */
    protected function create_post_stub_and_get_wr_id($data=array()){
        if (!function_exists('wv_write_board')) {
            $this->error('wv_write_board 함수를 찾을 수 없습니다.', 2);
        }
        $wr_id = wv_write_board($this->bo_table, $data);
        if ((int)$wr_id == false) {
            $this->error($wr_id, 2);
        }
        return (int)$wr_id;
    }

    public function get_write_table_name(){
        global $g5;
        $write_table = isset($g5['write_prefix']) ? $g5['write_prefix'].$this->bo_table : G5_TABLE_PREFIX.'write_'.$this->bo_table;
        return $write_table;
    }

    public function fetch_write_row($wr_id){
        $wr_id = (int)$wr_id;
        if ($wr_id <= 0) return array();
        $table = $this->get_write_table_name();
        $sql = "SELECT * FROM `{$table}` WHERE wr_id = {$wr_id} LIMIT 1";
        $row = sql_fetch($sql);
        return $row ? $row : array();
    }

    public function fetch_store_row($wr_id){
        $wr_id = (int)$wr_id;
        if ($wr_id <= 0) return array();
        $table = $this->get_ext_table_name();
        $sql = "SELECT * FROM `{$table}` WHERE wr_id = {$wr_id} LIMIT 1";
        $row = sql_fetch($sql);
        return $row ? $row : array('wr_id' => $wr_id);
    }

    public function physical_col($part_key, $logical){
        if($logical==''){
            return $part_key;
        }
        if ($this->column_naming === 'auto_prefix') return $part_key . '_' . $logical;
        return $logical; // strict일 땐 그대로(충돌 감지)
    }

    public function get_physical_col($part_key, $logical){
        return isset($this->colmap[$part_key][$logical]) ? $this->colmap[$part_key][$logical] : $this->physical_col($part_key, $logical);
    }

    public function get_part_allowed_physical($part_key){
        return isset($this->colmap[$part_key]) ? array_values($this->colmap[$part_key]) : array();
    }

    protected function get_column_info($table, $col){
        $row = sql_fetch("SHOW COLUMNS FROM `{$table}` LIKE '".sql_escape_string($col)."'");
        return $row ? $row : null; // keys: Field, Type, Null, Key, Default, Extra
    }

    protected function column_definition_differs($cur, $ddl){
        if (!$cur) return true;

        // cur: ['Type'=>'varchar(255)','Null'=>'YES','Default'=>null,'Extra'=>...]
        $cur_type = isset($cur['Type']) ? strtolower(trim($cur['Type'])) : '';
        $cur_null = isset($cur['Null']) ? strtoupper(trim($cur['Null'])) : 'YES';
        $cur_defv = array_key_exists('Default', $cur) ? $cur['Default'] : null;

        $ddl_norm = strtoupper(preg_replace('/\s+/', ' ', trim($ddl)));

        // 기대 타입 추출 (예: VARCHAR(255), DECIMAL(10,7))
        if (preg_match('/^\s*([A-Z0-9_]+(?:\(\s*\d+(?:\s*,\s*\d+)?\s*\))?)/i', $ddl, $m)) {
            $exp_type = strtolower($m[1]);
        } else {
            $exp_type = '';
        }

        // NULL 허용 여부
        $exp_null_yes = (strpos($ddl_norm, 'NOT NULL') === false); // NOT NULL 없으면 YES로 간주

        // DEFAULT NULL 명시 여부
        $exp_def_null = (strpos($ddl_norm, 'DEFAULT NULL') !== false);

        // 비교(느슨한): 타입, NULL 여부, DEFAULT NULL
        if ($exp_type && $cur_type !== $exp_type) return true;
        if (($cur_null === 'YES') !== $exp_null_yes) return true;

        if ($exp_def_null) {
            if ($cur_defv !== null) return true;
        }

        return false;
    }


    public function get_list($opts = array()){
        global $g5;

        $bo_table = isset($this->bo_table) ? $this->bo_table : '';
        if(!$bo_table){
            return array('list'=>array(), 'total_count'=>0, 'total_page'=>0, 'page'=>1, 'page_rows'=>0, 'from_record'=>0);
        }

        // 옵션 기본값
        $defaults = array(
            'page'       => 1,
            'rows'       => 20,
            'where'      => array(),
            'select_w'   => 'w.*',
            'select_s'   => 'auto',
            'order_by'   => 'w.wr_id DESC',
            'nest_parts' => true,
            // JOIN 옵션 (단일 or 배열의 배열)
            'join'       => array(),
            'join_member'  =>  array(
                'table'  => $g5['member_table'],
                'on'     => 'mb_id',
                'select' => 'jm.mb_name, jm.mb_level, jm.mb_datetime',
                'type'   => 'LEFT',
                'as'     => 'jm'
            ),
            'list_url'=>'',
            'write_pages'=>5,
            'with_list_part'=>false,
        );
        foreach($defaults as $k=>$v){
            if(!isset($opts[$k])) $opts[$k] = $v;
        }

        $page     = (int)$opts['page']; if($page < 1) $page = 1;
        $rows     = (int)$opts['rows']; if($rows < 1) $rows = 20;
        $select_w = trim($opts['select_w']);
        $select_s_opt = trim($opts['select_s']);
        $order_by = trim($opts['order_by']);
        $nest     = (bool)$opts['nest_parts'];

        // 테이블명
        $write_table = $this->get_write_table_name();
        $base_table  = $this->get_ext_table_name();

        // WHERE 생성(공통/개별 write/base)
        $where_all = array();

        if($opts['where']){
            if(is_array($opts['where'])){
                foreach($opts['where'] as $w){
                    $w = trim($w);
                    if($w !== '') $where_all[] = '(' . $w . ')';
                }
            }else{
                $w = trim($opts['where']);
                if($w !== '') $where_all[] = '(' . $w . ')';
            }
        }

        $ext_columns=array();

        // 목록 파트 WHERE: where_{part} 옵션 처리 (EXISTS)
        if(is_array($this->parts) && count($this->parts)){
            foreach($this->parts as $pkey => $schema){
                $where_key = 'where_'.strtolower($pkey);
                $conds = $opts[$where_key];
                $def = $schema->get_columns($this->bo_table);
                if($conds){

                    $walk_function = function (&$arr,$arr2,$node) use(&$conds,&$walk_function,$def,$pkey) {

                        $parent_key = wv_array_last($node);

                        if(!is_array($arr)){
                            if(!array_key_exists($parent_key,$def)){
                                $combined = 'unset($conds'. wv_array_to_text($node,"['","']").');';

                                @eval("$combined;");
                                return false;
                            }
                            $physical_col = $this->get_physical_col($pkey, $parent_key);
                            $arr ="($physical_col {$arr})";
                            return false;
                        }

                        foreach ($arr as $k=>&$v){

                            wv_walk_by_ref_diff($v,$walk_function,array(),array_merge($node,(array)$k));

                        }

                        if(in_array($parent_key,array('and','or'))){
                            $arr = implode(" {$parent_key} ",$arr);
                            return false;
                        }else{

                            $arr = implode(" and ",array_filter($arr));

                        }

                        return false;

                    };

                    wv_walk_by_ref_diff($conds,$walk_function,array());


                    if($this->is_list_part_schema($schema)){
                        $list_part_tbl = $this->get_list_table_name($pkey);
                        $where_all[] = "(EXISTS (SELECT 1 FROM `{$list_part_tbl}` t WHERE t.wr_id = w.wr_id AND ({$conds})))";
                    }else{
                        $where_all[] = '(' . $conds . ')';
                    }
                }

                $select_key = 'select_'.strtolower($pkey);
                $conds_select = $opts[$select_key];
                if($conds_select){

                    $walk_function = function (&$arr,$arr2,$node) use(&$conds_select,&$walk_function,$def,$pkey) {

                        $parent_key = wv_array_last($node);

                        if(!is_array($arr)){
                            if(!array_key_exists($parent_key,$def) or $def[$parent_key]){
                                $combined = 'unset($conds'. wv_array_to_text($node,"['","']").');';

                                @eval("$combined;");
                                return false;
                            }

                            return false;
                        }

                        foreach ($arr as $k=>&$v){

                            wv_walk_by_ref_diff($v,$walk_function,array(),array_merge($node,(array)$k));

                        }



                        return false;

                    };

                    wv_walk_by_ref_diff($conds_select,$walk_function,array());

                    $ext_columns[$pkey]=$conds_select;


                }
            }
        }


        $where_sql = $where_all ? implode(' AND ', $where_all) : '1';

        // --- JOIN 처리 시작 ---


        // 사용자 조인 정규화
        $user_joins = array();
        if (isset($opts['join'])) {
            if (is_array($opts['join'])) {
                if (isset($opts['join']['table'])) {
                    $user_joins = array($opts['join']);
                } else if (isset($opts['join'][0]) && is_array($opts['join'][0])) {
                    $user_joins = $opts['join'];
                }
            }
        }
        $joins = array_merge(array($opts['join_member']), $user_joins);

        $join_sql = '';
        $join_selects = array();
        $join_alias_count = 0;

        // 캐시
        static $join_columns_cache = array();

        foreach($joins as $j){
            $join_alias_count++;

            $j_table = isset($j['table']) ? $j['table'] : '';
            if ($j_table === '') continue;

            $j_type = isset($j['type']) ? strtoupper(trim($j['type'])) : 'LEFT';
            if ($j_type !== 'LEFT' && $j_type !== 'INNER' && $j_type !== 'RIGHT') $j_type = 'LEFT';

            $j_as = isset($j['as']) && $j['as'] !== '' ? $j['as'] : ('j'.$join_alias_count);

            // on 처리
            $j_on = '';
            if (isset($j['on']) && trim($j['on']) !== '') {
                $on_val = trim($j['on']);
                if (strpos($on_val, '=') !== false) {
                    $j_on = $on_val;
                } else {
                    $j_on = 'w.'.$on_val.' = '.$j_as.'.'.$on_val;
                }
            } else if (isset($j['on_col']) && trim($j['on_col']) !== '') {
                $col  = trim($j['on_col']);
                $j_on = 'w.'.$col.' = '.$j_as.'.'.$col;
            }
            if ($j_on === '') continue;

            // select 처리
            if (isset($j['select']) && trim($j['select']) !== '') {
                $sel = trim($j['select']);

                // 1) '*' 단독이면 컬럼을 전개하면서 alias_col 로 별칭
                if ($sel === '*') {
                    if (!isset($join_columns_cache[$j_table])) {
                        $cols = array();
                        $rs = sql_query("SHOW COLUMNS FROM {$j_table}");
                        while ($c = sql_fetch_array($rs)) {
                            if (isset($c['Field'])) $cols[] = $c['Field'];
                            else if (isset($c[0]))   $cols[] = $c[0];
                        }
                        $join_columns_cache[$j_table] = $cols;
                    }
                    $cols = $join_columns_cache[$j_table];
                    foreach ($cols as $_c) {
                        $join_selects[] = $j_as.'.'.$_c.' AS '.$j_as.'_'.$_c;
                    }

                } else if (strpos($sel, ',') !== false) {
                    // 2) 콤마 구분 나열이면 각 토큰을 안전하게 전개
                    $tokens = explode(',', $sel);
                    foreach ($tokens as $tok) {
                        $tok = trim($tok);
                        if ($tok === '') continue;

                        // 이미 AS 있으면 그대로
                        if (stripos($tok, ' as ') !== false) {
                            $join_selects[] = $tok;
                            continue;
                        }
                        // 점이 없으면 alias와 안전 별칭 부여
                        if (strpos($tok, '.') === false) {
                            $join_selects[] = $j_as.'.'.$tok.' AS '.$j_as.'_'.$tok;
                        } else {
                            $parts = explode('.', $tok, 2);
                            if (count($parts) === 2) {
                                $join_selects[] = $tok.' AS '.$parts[0].'_'.$parts[1];
                            } else {
                                $join_selects[] = $tok;
                            }
                        }
                    }

                } else {
                    // 3) 단일 토큰
                    if (stripos($sel, ' as ') !== false) {
                        $join_selects[] = $sel;
                    } else if (strpos($sel, '.') === false) {
                        $join_selects[] = $j_as.'.'.$sel.' AS '.$j_as.'_'.$sel;
                    } else {
                        $parts = explode('.', $sel, 2);
                        if (count($parts) === 2) {
                            $join_selects[] = $sel.' AS '.$parts[0].'_'.$parts[1];
                        } else {
                            $join_selects[] = $sel;
                        }
                    }
                }
            }

            $join_sql .= " {$j_type} JOIN {$j_table} {$j_as} ON ({$j_on}) ";
        }
        // --- JOIN 처리 끝 ---

        // 전체 카운트 (JOIN 반영)
        $sql_cnt =
            " SELECT COUNT(*) AS cnt
          FROM {$write_table} AS w
          LEFT JOIN {$base_table} AS s ON s.wr_id = w.wr_id
          {$join_sql}
          WHERE {$where_sql} ";
        $row_cnt = sql_fetch($sql_cnt);
        $total_count = isset($row_cnt['cnt']) ? (int)$row_cnt['cnt'] : 0;

        $total_page = $rows > 0 ? (int)ceil($total_count / $rows) : 0;
        if($total_page > 0 && $page > $total_page) $page = $total_page;
        $from_record = ($page - 1) * $rows; if($from_record < 0) $from_record = 0;

        // 확장테이블 선택 컬럼 산출 (select_s='auto' 또는 's.*' 또는 빈 문자열일 때)
        $ext_select_cols = array();
        $select_s = '';
        if($select_s_opt === '' || $select_s_opt === 'auto' || strtolower($select_s_opt) === 's.*'){
            // 1) 실제 존재하는 확장테이블 컬럼
            $existing = $this->get_current_table_columns();
            $existing_map = array();
            foreach($existing as $c){ $existing_map[$c] = true; }

            // 2) 파트가 정의한 물리컬럼(빈 DDL은 테이블에 없으므로 1)과 교집합 해야 함)
            $phys_map = array();
            if (is_array($this->colmap)){
                foreach($this->colmap as $part_key => $map){
                    if (!is_array($map)) continue;
                    foreach($map as $lname => $pname){
                        if (is_string($pname) && strlen($pname)) $phys_map[$pname] = true;
                    }
                }
            }

            // 3) 교집합 만들기 + wr_id 제외
            foreach($phys_map as $pcol => $_){
                if ($pcol === 'wr_id') continue;
                if (isset($existing_map[$pcol])) $ext_select_cols[] = $pcol;
            }

            if (count($ext_select_cols)){
                $tmp = array();
                foreach ($ext_select_cols as $c) $tmp[] = 's.'.$c;
                $select_s = implode(', ', $tmp);
            } else {
                $select_s = '';
            }
        } else {
            $select_s = $select_s_opt; // 사용자가 직접 지정한 select_s 사용
        }

        // SELECT
        if($select_w === '') $select_w = 'w.*';
        $select_sql = $select_w;
        if ($select_s !== '') $select_sql .= ', '.$select_s;

        // 조인 select 추가
        if(count($join_selects)){
            $select_sql .= ', '.implode(', ', $join_selects);
        }

        $order_sql = $order_by !== '' ? " ORDER BY {$order_by} " : '';
        $limit_sql = $rows > 0 ? " LIMIT {$from_record}, {$rows} " : '';

        $sql =
            " SELECT {$select_sql}
          FROM {$write_table} AS w
          LEFT JOIN {$base_table} AS s ON s.wr_id = w.wr_id
          {$join_sql}
          WHERE {$where_sql} {$order_sql} {$limit_sql} ";

        $q = sql_query($sql);
        if(!$q){
            alert('get_list실패');
        }
        $list = array();

        // 중첩 대상 맵 (선택된 확장 컬럼만 우선 사용, 없으면 테이블 전체 컬럼으로 fallback)
        $ext_cols_map = array();
        if ($nest){
            if (count($ext_select_cols)){
                foreach ($ext_select_cols as $c) $ext_cols_map[$c] = true;
            } else {
                $ext_cols = $this->get_current_table_columns();
                foreach ($ext_cols as $c) $ext_cols_map[$c] = true;
            }
        }

        while ($r = sql_fetch_array($q)) {
            $row = $r;

            // 1) 평면 → 중첩 (목록 파트는 제외)
            if ($nest && is_array($this->parts) && count($this->parts)) {
                foreach ($this->parts as $pkey => $schema) {
                    if ($this->is_list_part_schema($schema)) continue; // 목록 파트는 별도 테이블
                    $allowed = method_exists($schema, 'get_allowed_columns') ? (array)$schema->get_allowed_columns() : array();
                    if (!count($allowed)) continue;

                    if (!isset($row[$pkey]) || !is_array($row[$pkey])) $row[$pkey] = array();

                    foreach ($allowed as $lname) {
                        $pname = $this->get_physical_col($pkey, $lname);
                        if (array_key_exists($pname, $row)) {
                            $row[$pkey][$lname] = $row[$pname];
                            unset($row[$pname]); // 상위 평면 키 제거
                        }
                    }
                }
            }

            // 2) b64s 디코드 (중첩 컬럼에 한정)
            if ($nest && is_array($this->parts) && count($this->parts)) {
                foreach ($this->parts as $pkey => $schema) {
                    if (!isset($row[$pkey]) || !is_array($row[$pkey])) continue;

                    foreach ($ext_columns[$pkey] as $ex_k=>$ex_v){
                        $row[$pkey][$ex_k]=$ex_v;
                    }
                    foreach ($row[$pkey] as $_k => $_v) {
                        if (is_string($_v) && $_v !== '' && method_exists($this, 'decode_b64s')) {
                            $try = $this->decode_b64s($_v);
                            if (is_array($try)) $row[$pkey][$_k] = $try;
                        }

                    }
                }
            }

            $this->inject_value_maps_into_row($row);

            $list[] = $row;


        }

// --- 목록 파트 주입: $row['menu'] = [ {...}, {...} ] ---
        $with_list_part = $opts['with_list_part'];
        $target_list_parts = array();
        if ($with_list_part === false) {
            $target_list_parts = array(); // 목록파트 없음
        } else if ($with_list_part === '*' || $with_list_part === true) {
            // 모든 목록파트 (null 전달하면 기존 동작)
            $target_list_parts = null;
        } else if (is_string($with_list_part) && $with_list_part !== '') {
            // 특정 파트들만
            $requested_parts = array_map('trim', explode(',', $with_list_part));
            foreach($requested_parts as $pkey) {
                if($pkey !== '' && isset($this->parts[$pkey]) && $this->is_list_part_schema($this->parts[$pkey])) {
                    $target_list_parts[] = $pkey;
                }
            }
        }

// 목록파트 데이터 로딩
        if($target_list_parts !== array() && count($list) > 0) {
            $wr_ids = array();
            foreach($list as $row) {
                $wr_id = (int)$row['wr_id'];
                if($wr_id > 0) $wr_ids[] = $wr_id;
            }

            if(count($wr_ids) > 0) {
                // target_parts 전달로 필요한 쿼리만 실행
                $list_part_data = $this->fetch_list_part_rows_for_wr_ids($wr_ids, $ext_columns, $target_list_parts);

                // 결과를 각 행에 추가
                foreach($list as &$row) {
                    $wr_id = (int)$row['wr_id'];
                    foreach($list_part_data as $pkey => $part_data) {
                        if(isset($part_data[$wr_id])) {
                            $row[$pkey] = $part_data[$wr_id];
                        } else {
                            $row[$pkey] = array();
                        }
                    }
                }
            }
        }

// --- 목록 파트 주입 끝 ---

        return array(
            'list'         => $list,
            'total_count'  => $total_count,
            'total_page'   => $total_page,
            'page'         => $page,
            'page_rows'    => $rows,
            'from_record'  => $from_record,
            'write_table'  => $write_table,
            'base_table'   => $base_table,
            'sql'          => $sql,
            'sql_count'    => $sql_cnt,
            'paging'       => $opts['list_url']?wv_get_paging($opts['write_pages'], $page, $total_page, $opts['list_url']):''
        );
    }

    /**
     * WHERE 보조: "field = 'x'" 같은 조각에 별칭 접두어를 안전하게 붙임
     * 예) qualify_where("wr_id > 0", "w") → "w.wr_id > 0"
     * 매우 단순한 방식이라, 복잡한 SQL에는 직접 별칭을 명시하는 것을 권장
     */
    protected function qualify_where($expr, $alias){
        $expr = preg_replace('/\b(wr_id|mb_id|wr_datetime|wr_subject|wr_hit|wr_good|wr_nogood|wr_comment)\b/', $alias.'.$1', $expr);
        return $expr;
    }

    protected function get_ext_select_columns($exclude_cols = array()){
        // 1) 실제 존재하는 확장테이블 컬럼
        $existing = $this->get_current_table_columns();
        $existing_map = array();
        foreach($existing as $c){ $existing_map[$c] = true; }

        // 2) 파트가 정의한 물리컬럼 합집합 (bind_schema()에서 $this->colmap 채워짐)
        $phys_map = array();
        if (is_array($this->colmap)){
            foreach($this->colmap as $part => $map){
                if (!is_array($map)) continue;
                foreach($map as $lname => $pname){
                    if (is_string($pname) && strlen($pname)) $phys_map[$pname] = true;
                }
            }
        }

        // 3) 교집합 만들기
        $cols = array();
        foreach($phys_map as $pcol => $_){
            if (isset($existing_map[$pcol])) $cols[] = $pcol;
        }

        // 4) 제외 처리 (wr_id 등)
        if (is_array($exclude_cols) && count($exclude_cols)){
            $ex = array(); foreach($exclude_cols as $e){ $ex[$e] = true; }
            $filtered = array();
            foreach($cols as $c){ if (!isset($ex[$c])) $filtered[] = $c; }
            $cols = $filtered;
        }

        return $cols;
    }




    protected function get_schema_value_maps_cached($schema){
        static $cache = array();
        $cls = is_object($schema) ? get_class($schema) : '';
        if (!$cls) return array();
        if (isset($cache[$cls])) return $cache[$cls];

        $allowed = method_exists($schema,'get_allowed_columns') ? $schema->get_allowed_columns() : array();
        $allowed = is_array($allowed) ? array_values($allowed) : array();

        $maps = array();
        $ref = new \ReflectionObject($schema);
        foreach($ref->getProperties() as $p){
            $name = $p->getName();

            // ★ base 찾기: 허용 컬럼명 + '_' 로 시작하는지 검사
            $base = '';
            foreach($allowed as $col){
                if (strpos($name, $col.'_') === 0) { $base = $col; break; }
            }
            if ($base === '') continue;

            $p->setAccessible(true);
            $val = $p->getValue($schema);
            if (is_array($val)) {
                $maps[$name] = array('base'=>$base, 'map'=>$val);
            }
        }
        $cache[$cls] = $maps;
        return $maps;
    }

//    protected function inject_value_maps_into_row(&$row){
//        // nest_parts=true 기준: $row['{part}']['{logical}'] 구조
//        if (!is_array($this->parts) || !count($this->parts)) return;
//
//        foreach($this->parts as $pkey => $schema){
//            // 목록 파트는 건너뜀(원하면 별도 정의 가능)
//            if ($this->is_list_part_schema($schema)) continue;
//
//            if (!isset($row[$pkey]) || !is_array($row[$pkey])) continue;
//
//            $maps = $this->get_schema_value_maps_cached($schema);
//            if (!count($maps)) continue;
//
//            foreach($maps as $vname => $spec){
//                $base = $spec['base'];
//                $map  = $spec['map'];
//                $code = isset($row[$pkey][$base]) ? $row[$pkey][$base] : null;
//                $row[$pkey][$vname] = isset($map[$code]) ? $map[$code] : null;
//            }
//        }
//    }

    protected function inject_value_maps_into_row(&$row){
        if (!is_array($this->parts) || !count($this->parts)) return;

        foreach($this->parts as $pkey => $schema){
            if ($this->is_list_part_schema($schema)) continue;
            if (!isset($row[$pkey]) || !is_array($row[$pkey])) continue;

            // ✅ StorePartProxy 로직 재사용
            $proxy = new \weaver\store_manager\StorePartProxy($this, 0, $schema, array(), $pkey);
            $proxy->apply_value_maps($row[$pkey],$row);
        }
    }

    // 업로드 수집: name="파트명[컬럼][]" 만 지원(요청 스펙)
    protected function collect_uploaded_for_field($part_key, $logical_col){
        $F = $this->get_files_tree_for_part($part_key);
        if (!$F || !isset($F['name']) || !is_array($F['name'])) return array();

        // 공백 등 보정된 실제 키 검색
        $real = array_key_exists($logical_col, $F['name']) ? $logical_col : '';
        if ($real === '') {
            $target = trim((string)$logical_col);
            foreach ($F['name'] as $k => $_v){
                if (trim((string)$k) === $target) { $real = (string)$k; break; }
            }
        }
        if ($real === '' || !isset($F['name'][$real])) return array();

        // 스칼라만 허용
        if (is_array($F['name'][$real])) return array();

        $spec = array(
            'name'     => $F['name'][$real],
            'type'     => isset($F['type'][$real]) ? $F['type'][$real] : null,
            'tmp_name' => isset($F['tmp_name'][$real]) ? $F['tmp_name'][$real] : null,
            'error'    => isset($F['error'][$real]) ? $F['error'][$real] : null,
            'size'     => isset($F['size'][$real]) ? $F['size'][$real] : null,
        );
        $list = $this->normalize_files_spec($spec);
        if (!count($list)) return array();

        $subdir = 'weaver/store_manager/'.$this->bo_table.'/'.date('Ym');
        return $this->save_uploaded_files($list, $subdir);
    }

    protected function normalize_files_spec($spec){
        $out = array();
        if (is_array($spec['name'])) {
            $n = count($spec['name']);
            for ($i=0;$i<$n;$i++){
                $out[] = array(
                    'name'     => $spec['name'][$i],
                    'type'     => $spec['type'][$i],
                    'tmp_name' => $spec['tmp_name'][$i],
                    'error'    => $spec['error'][$i],
                    'size'     => $spec['size'][$i],
                );
            }
        } else {
            $out[] = $spec;
        }
        // 유효 파일만
        $ok = array();
        foreach ($out as $f){
            $errv = isset($f['error']) ? (int)$f['error'] : UPLOAD_ERR_NO_FILE;
            if ($errv !== UPLOAD_ERR_OK) continue;
            if (!isset($f['tmp_name']) || !is_uploaded_file($f['tmp_name'])) continue;
            if (!isset($f['size']) || (int)$f['size'] <= 0) continue;
            $ok[] = $f;
        }
        return $ok;
    }

    protected function sanitize_filename($name){
        $name = preg_replace('/[^\w\.\-\(\)\[\]\s가-힣]/u', '_', (string)$name);
        if (strlen($name) > 120) $name = substr($name, -120);
        return $name ? $name : 'file';
    }

    protected function unique_filename($dir, $ext){
        do {
            $rnd = substr(md5(uniqid('', true)), 0, 12);
            $fname = date('Ymd_His').'_'.$rnd.'.'.$ext;
        } while (file_exists($dir.'/'.$fname));
        return $fname;
    }

    protected function encode_b64s($value){
        if (function_exists('wv_base64_encode_serialize')) return wv_base64_encode_serialize($value);
        return base64_encode(serialize($value));
    }

    public function decode_b64s($str){
        if (!is_string($str) || $str === '') return null;
        if (function_exists('wv_base64_decode_unserialize')) return wv_base64_decode_unserialize($str);
        $v = @unserialize(@base64_decode($str));
        return $v;
    }

    public function is_list_part_schema($schema){
        if (!is_object($schema)) return false;


        if (is_callable(array($schema, 'is_list_part'))) {
            $v = $schema->is_list_part();
            return $v ? true : false;
        }

        return false;
    }

    // {ext_table}_{part}
    public function get_list_table_name($part_key){
        return $this->get_ext_table_name().'_'.strtolower($part_key);
    }

    // 기존 ensure_array_table → rename & 그대로 사용
    protected function ensure_list_table($part_key, $schema = null){
        $table = $this->get_list_table_name($part_key);

        // 1) 베이스: id, wr_id, ord + 인덱스
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `wr_id` INT(11) NOT NULL,
            `ord` INT(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `wr_id_ord_idx` (`wr_id`, `ord`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ";
        sql_query($sql, true);

        // 2) 혹시 기존 테이블에 ord/인덱스 없으면 보강
        if(!$this->table_has_column($table, 'ord')){
            sql_query("ALTER TABLE `{$table}` ADD COLUMN `ord` INT(11) DEFAULT NULL", true);
        }
        if(!$this->table_has_index($table, 'wr_id_ord_idx')){
            sql_query("ALTER TABLE `{$table}` ADD INDEX `wr_id_ord_idx` (`wr_id`, `ord`)", true);
        }

        // 3) 파트 정의 컬럼 반영
        if($schema && method_exists($schema, 'get_columns')){
            $cols = (array)$schema->get_columns($this->bo_table);
            foreach($cols as $name => $ddl){
                if($name === 'id' || $name === 'wr_id' || $name === 'ord') continue;
                if(!is_string($ddl) || !strlen(trim($ddl))) continue;

                if(!$this->table_has_column($table, $name)){
                    sql_query("ALTER TABLE `{$table}` ADD COLUMN `{$name}` {$ddl}", true);
                }else{
                    $cur = $this->get_column_info($table, $name);
                    if($this->column_definition_differs($cur, $ddl)){
                        sql_query("ALTER TABLE `{$table}` MODIFY COLUMN `{$name}` {$ddl}", true);
                    }
                }
            }
        }
    }

    protected function apply_list_part_schema($part_key, $schema){
        $this->ensure_list_table($part_key, $schema);
        $t = $this->get_list_table_name($part_key);

        if(method_exists($schema, 'get_indexes')){
            $idx = (array)$schema->get_indexes($this->bo_table);
            foreach($idx as $ix){
                $name = isset($ix['name']) ? $ix['name'] : '';
                $type = isset($ix['type']) ? strtoupper($ix['type']) : 'INDEX';
                $cols = isset($ix['cols']) ? $ix['cols'] : array();
                if(!$name || !count($cols)) continue;

                if(!$this->table_has_index($t, $name)){
                    $qcols = array();
                    foreach($cols as $c){ $qcols[] = '`'.$c.'`'; }
                    sql_query("ALTER TABLE `{$t}` ADD {$type} `{$name}` (".implode(',', $qcols).")", true);
                }
            }
        }
    }

    protected function get_current_list_table_columns($part_key){
        $table = $this->get_list_table_name($part_key);
        $cols = array();
        $res = sql_query("SHOW COLUMNS FROM `{$table}`");
        while ($row = sql_fetch_array($res)) {
            if (isset($row['Field'])) $cols[] = $row['Field'];
        }
        return $cols;
    }

// StoreManager.php에서 기존 메서드 수정

    protected function fetch_list_part_rows_for_wr_ids($wr_ids, $ext_columns = array(), $target_parts = null) {
        $out = array();
        if(!is_array($this->parts) || !count($this->parts)) return $out;
        if(!is_array($wr_ids) || !count($wr_ids)) return $out;

        $ids = array();
        foreach($wr_ids as $id){ $id = (int)$id; if($id>0) $ids[] = $id; }
        if(!count($ids)) return $out;
        $in = implode(',', $ids);

        // target_parts가 지정되면 해당 파트들만, 아니면 모든 목록파트
        $parts_to_process = array();
        if (is_array($target_parts) && count($target_parts)) {
            // 지정된 파트들만
            foreach($target_parts as $pkey) {
                if(isset($this->parts[$pkey]) && $this->is_list_part_schema($this->parts[$pkey])) {
                    $parts_to_process[$pkey] = $this->parts[$pkey];
                }
            }
        } else {
            // 기존 방식: 모든 목록파트
            foreach($this->parts as $pkey => $schema) {
                if($this->is_list_part_schema($schema)) {
                    $parts_to_process[$pkey] = $schema;
                }
            }
        }

        foreach($parts_to_process as $pkey => $schema) {
            $t = $this->get_list_table_name($pkey);
            $def = $schema->get_columns($this->bo_table);
            $cols = array('id','wr_id');
            foreach($def as $cname => $_ddl){ $cols[] = $cname; }
            $cols = array_unique($cols);

            $existing = array();
            $rs = sql_query("SHOW COLUMNS FROM `{$t}`");
            while($c = sql_fetch_array($rs)){
                $existing[] = isset($c['Field']) ? $c['Field'] : $c[0];
            }
            $emap = array(); foreach($existing as $_c){ $emap[$_c]=true; }
            $sel = array(); foreach($cols as $_c){ if(isset($emap[$_c])) $sel[] = '`'.$_c.'`'; }
            if(!count($sel)) continue;

            $sql = "SELECT ".implode(',', $sel)." FROM `{$t}` WHERE wr_id IN ({$in}) ORDER BY wr_id ASC, ord ASC, id ASC";
            $qry = sql_query($sql);

            while($r = sql_fetch_array($qry)){
                $wid = (int)$r['wr_id'];
                $row = array();
                if (isset($r['id']))  $row['id']  = (int)$r['id'];
                if (isset($r['ord'])) $row['ord'] = (int)$r['ord'];
                foreach($def as $cname => $_ddl){
                    if(isset($r[$cname])) $row[$cname] = wv_base64_decode_unserialize($r[$cname]);
                }
                if(isset($ext_columns[$pkey])) {
                    foreach ($ext_columns[$pkey] as $ex_k=>$ex_v){
                        $row[$ex_k]=$ex_v;
                    }
                }
                $out[$pkey][$wid][] = $row;
            }
        }
        return $out;
    }
    public function get_list_part_list($wr_id, $part_key){
        $wr_id = (int)$wr_id;
        if($wr_id <= 0) return array();

        // fetch_list_part_rows_for_wr_ids 재사용
        $all_data = $this->fetch_list_part_rows_for_wr_ids(array($wr_id));

        // 해당 part_key와 wr_id의 데이터 반환
        return isset($all_data[$part_key][$wr_id]) ? $all_data[$part_key][$wr_id] : array();
    }
//    public function get_list_part_list($wr_id, $part_key){
//        $wr_id = (int)$wr_id; if($wr_id <= 0) return array();
//        if (!isset($this->parts[$part_key])) return array();
//        $schema = $this->parts[$part_key];
//        if (!$this->is_list_part_schema($schema)) return array();
//
//        $t   = $this->get_list_table_name($part_key);
//        $def = $schema->get_columns($this->bo_table);
//
//        // 실제 존재 컬럼 맵
//        $emap = array();
//        $rs = sql_query("SHOW COLUMNS FROM `{$t}`");
//        while ($c = sql_fetch_array($rs)){
//            $fname = isset($c['Field']) ? $c['Field'] : (isset($c[0]) ? $c[0] : '');
//            if ($fname) $emap[$fname] = true;
//        }
//
//        $sel = array('`id`','`wr_id`');
//        $has_ord = isset($emap['ord']);
//        if ($has_ord) $sel[] = '`ord`';
//        foreach ($def as $cname => $_ddl){ if (isset($emap[$cname])) $sel[] = '`'.$cname.'`'; }
//        if (count($sel) <= 2) return array();
//
//        $order = $has_ord ? "ORDER BY `ord` ASC, `id` ASC" : "ORDER BY `id` ASC";
//        $q = sql_query("SELECT ".implode(',', $sel)." FROM `{$t}` WHERE wr_id='{$wr_id}' {$order}");
//
//        $out = array();
//        while ($r = sql_fetch_array($q)){
//            $row = array('id' => isset($r['id']) ? (int)$r['id'] : 0);
//            if ($has_ord && isset($r['ord'])) $row['ord'] = (int)$r['ord'];
//            foreach ($def as $cname => $_ddl){
//                if (!isset($r[$cname])) continue;
//                $val = $r[$cname];
//                if (is_string($val) && $val !== '' && method_exists($this, 'decode_b64s')) {
//                    $try = $this->decode_b64s($val);
//                    if (is_array($try)) $val = $try;
//                }
//                $row[$cname] = $val;
//            }
//            $out[] = $row;
//        }
//        return $out;
//    }

    public function field_value_compare_merge($new, $old /* , ...$keys */){
        $args = func_get_args();
        $new_value  = array_shift($args);
        $old_value  = array_shift($args);
        $start_path = $args;

        $is_numeric_key = function($k){
            return is_int($k) || (is_string($k) && preg_match('/^\d+$/', $k));
        };

        // "모든 키가 숫자"인 배열
        $is_list_array = function($arr) use ($is_numeric_key){
            if (!is_array($arr) || !count($arr)) return false;
//            dd($arr);
            foreach (array_keys($arr) as $k){
                if (!$is_numeric_key($k)) {

                    return false;
                }
            }
            return true;
        };

        // 자식 중에 "리스트배열"이 하나라도 있으면 현재 노드는 '리스트의 부모'로 간주
        // → 현재 레벨은 assoc로 내려가고, 자식 레벨에서 리스트 병합 수행
        $has_list_children = function($arr) use ($is_list_array){
            if (!is_array($arr)) return false;
            foreach ($arr as $v){
                if (is_array($v) && $is_list_array($v)) return true;
            }
            return false;
        };

        // 신규 항목( id 없음 )이 "사실상 비어있는지" 판정
        $is_effectively_empty = null;
        $is_effectively_empty = function($node) use (&$is_effectively_empty){
            if (is_array($node)) {
                foreach ($node as $k => $v) {
                    if ($k === 'id' || $k === 'ord' || $k === 'delete') continue;
                    if (is_array($v)) {
                        if (!$is_effectively_empty($v)) return false;
                    } else {
                        if (is_string($v)) { if (trim($v) !== '') return false; }
                        else if (is_numeric($v)) { return false; }
                        else if (is_bool($v)) { if ($v) return false; }
                        else if (!empty($v)) { return false; }
                    }
                }
                return true;
            }
            if (is_string($node)) return trim($node) === '';
            if (is_numeric($node)) return false;
            if (is_bool($node)) return !$node;
            return empty($node);
        };




        // 🔸 삭제 대상의 물리 경로를 수집하는 클로저 (필요 최소 로직만)
        $collect_delete_paths = function($node){
            $paths = array();

            // 1) files 배열 형태 지원
            if (is_array($node) && isset($node['files']) && is_array($node['files'])){
                foreach ($node['files'] as $f){
                    if (is_array($f) && isset($f['path']) && $f['path'] !== ''){
                        $paths[] = $f['path'];
                    }
                }
            }

            // 2) 단일 파일 메타 형태 지원 (source/path/type/created_at 가 같은 레벨에 있는 경우)
            if (is_array($node) && isset($node['path']) && $node['path'] !== ''){
                $paths[] = $node['path'];
            }

            // 3) 혹시 더 깊은 곳에 섞여있을 수 있으므로 얕은 재귀 탐색(최소)
            if (is_array($node)){
                foreach ($node as $v){
                    if (is_array($v)){
                        $sub = call_user_func($GLOBALS['__wv_collect_paths__'], $v);
                        if ($sub && is_array($sub)){
                            foreach ($sub as $p){ $paths[] = $p; }
                        }
                    }
                }
            }

            // 중복 제거
            if (!empty($paths)) $paths = array_values(array_unique($paths));
            return $paths;
        };
        // PHP 5.6에서 클로저 내부 재귀를 위해 글로벌 핸들 사용
        $GLOBALS['__wv_collect_paths__'] = $collect_delete_paths;

        // 연관배열 병합: new 키는 덮어쓰기/재귀, 누락키는 old 유지
        $merge_assoc = function($new_node, $old_node) use (&$merge_node,$collect_delete_paths){
            $result = is_array($old_node) ? $old_node : array();

            if (!is_array($new_node)) return $new_node !== null ? $new_node : $old_node;

            foreach ($new_node as $k => $v){

                $result[$k] = $merge_node(
                    $v,
                    (is_array($old_node) && array_key_exists($k, $old_node)) ? $old_node[$k] : null
                );
            }

            if (is_array($old_node)){
                foreach ($old_node as $k => $v){
                    if (!array_key_exists($k, $new_node)){
                        $result[$k] = $v;
                    }
                }
            }
            $old_paths = $collect_delete_paths($old_list_orig);

            if ($old_paths){
                $new_paths = $collect_delete_paths($result);
                $keep = array_flip($new_paths);
                $to_del = array();
                foreach ($old_paths as $p){ if (!isset($keep[$p])) $to_del[$p] = true; }
                if ($to_del && method_exists($this, 'delete_physical_paths_safely')){
                    $this->delete_physical_paths_safely(array_keys($to_del));
                }
            }

            return $result;
        };



        // 리스트 병합:
        // - id 있으면 매칭/삭제/병합
        // - id 없으면 항상 '신규'로 보고 (숫자키도 id로 쓰지 않음) old의 max(id)+1 부여
        // - ord는 new 리스트 순서대로 1부터
        // - new에 없는 old는 뒤에 이어붙이고 ord 연속
// ... (윗부분 동일)
        $merge_list = function($new_list, $old_list) use (&$merge_node, &$is_effectively_empty,$collect_delete_paths){
            $result    = array();
            $old_by_id = array();
            $max_id    = 0;
            $old_list_orig = $old_list;
            if (is_string($old_list)) {
                $decoded = wv_base64_decode_unserialize($old_list);
                if (is_array($decoded)) $old_list = $decoded;
            }

            if (is_array($old_list)){
                foreach ($old_list as $ov){

                    $oid = isset($ov['id']) ? $ov['id'] : null;

                    if (is_numeric($oid)){

                        $old_by_id[(string)$oid] = $ov;
                        if ($oid > $max_id) $max_id = $oid;
                    }
                }
            }

            if (!is_array($new_list)) $new_list = array();

            $next_id = $max_id;
            $seq_ord = 0;

            // 런타임(파트/테이블/스키마/wr_id) 컨텍스트
            $rt = (is_array($this->list_db_runtime) ? $this->list_db_runtime : null);
            $db_ready = ($rt && !empty($rt['enabled']) && !empty($rt['part']) && !empty($rt['wr_id']) && $rt['schema']);

            $t = $db_ready ? $this->get_list_table_name($rt['part']) : null;
            $def_cols_map = array();
            if ($db_ready) {
                $def = (array)$rt['schema']->get_columns($this->bo_table);
                foreach ($def as $cn => $_ddl){ $def_cols_map[$cn] = true; }

            }



            // 헬퍼: 컬럼값을 DB에 넣을 값으로 직렬화/문자화
            $to_db_value = function($col, $val, $prev_row){
                // 삭제 플래그면 빈 배열 직렬화(래퍼 없음)
                if (is_array($val) && isset($val['delete']) &&
                    ($val['delete'] === 1 || $val['delete'] === '1' || $val['delete'] === true || $val['delete'] === 'on' || $val['delete'] === 'Y' || $val['delete'] === 'y')) {
                    return $this->encode_b64s(array());
                }

                // 과거 호환: files 래퍼가 오면 벗겨서 저장
                if (is_array($val) && isset($val['files']) && is_array($val['files'])) {
                    $val = $val['files'];
                }

                // 배열(단일 메타 or 메타 배열 or 일반 배열) 그대로 직렬화 저장
                if (is_array($val)) {
                    return $this->encode_b64s($val);
                }

                // 스칼라는 문자열로
                return (string)$val;
            };
;
;
;
;

            foreach ($new_list as $nk => $nv){
                if (!is_array($nv)) continue;

                $id  = (isset($nv['id']) && is_numeric($nv['id'])) ? (int)$nv['id'] : null;
                $del = isset($nv['delete']) ? (bool)$nv['delete'] : false;


                if ($id !== null){

                    if ($del){

                        // ✅ 물리 파일 삭제: old에 같은 id가 존재할 때만 처리
                        if (isset($old_by_id[(string)$id])){
                            $old_item = $old_by_id[(string)$id];

                            // 삭제 경로 수집
                            if($db_ready){
                                $paths = array();
                                $paths = array_merge($paths, $collect_delete_paths($old_item));



                            }else{
                                $paths = array_merge($collect_delete_paths($old_item), $collect_delete_paths($nv));
                            }

                            // delete_physical_paths_safely()가 존재하면 호출 (없어도 치명 오류 방지)
                            if (!empty($paths) && method_exists($this, 'delete_physical_paths_safely')){
                                $this->delete_physical_paths_safely($paths /* , $keep_paths = array() */);
                            }

                            if ($db_ready) {
                                sql_query("DELETE FROM `{$t}` WHERE wr_id='".intval($rt['wr_id'])."' AND id='".intval($id)."'");
                            }

                            unset($old_by_id[(string)$id]);
                        }
                        continue;
                    }


                    $old_item = isset($old_by_id[(string)$id]) ? $old_by_id[(string)$id] : null;
                    $merged   = $merge_node($nv, $old_item);
                    $merged['id'] = $id;

                    $seq_ord += 1;
                    $merged['ord'] = $seq_ord;

                    if ($db_ready) {
                        $sets = array("`ord`='".sql_escape_string((string)$seq_ord)."'");
                        foreach ($def_cols_map as $col => $_u){
                            if ($col==='id' || $col==='wr_id' || $col==='ord') continue;
                            if (array_key_exists($col, $merged)) {
                                $dbv = $to_db_value($col, $merged[$col], $old_item);
                                $sets[] = "`{$col}`='".sql_escape_string($dbv)."'";
                            }
                            // 없으면 그대로 두기(업데이트 안 함)
                        }
                        if (count($sets)) {
                            $usql = "UPDATE `{$t}` SET ".implode(',', $sets)." WHERE id='".intval($id)."' AND wr_id='".intval($rt['wr_id'])."'";
                            sql_query($usql, true);
                        }
                    }

                    $result[] = $merged;
                    if (isset($old_by_id[(string)$id])) unset($old_by_id[(string)$id]);
                } else {
                    // 신규: 숫자키는 id로 사용하지 않음(요구사항)
                    if ($is_effectively_empty($nv)) {
                        continue;
                    }
                    $next_id += 1;
                    $merged = $merge_node($nv, null);
                    $merged['id'] = $next_id;

                    $seq_ord += 1;
                    $merged['ord'] = $seq_ord;

                    if ($db_ready) {
                        $fields = array('`wr_id`','`ord`');
                        $values = array("'".intval($rt['wr_id'])."'", "'".sql_escape_string((string)$seq_ord)."'");

                        foreach ($def_cols_map as $col => $_u){
                            if ($col==='id' || $col==='wr_id' || $col==='ord') continue;
                            if (!array_key_exists($col, $merged)) continue;
                            $dbv = $to_db_value($col, $merged[$col], array());
                            $fields[] = '`'.$col.'`';
                            $values[] = "'".sql_escape_string($dbv)."'";
                        }

                        if (count($fields) > 2) {

                            $isql = "INSERT INTO `{$t}` (".implode(',', $fields).") VALUES (".implode(',', $values).")";

                            sql_query($isql, true);
                        }
                    }

                    $result[] = $merged;
                }
            }
//            dd($result);
            // new에 언급되지 않은 기존(old) 항목 보존 (ord 연속)
            foreach ($old_by_id as $remain){
                $seq_ord += 1;
                $remain['ord'] = $seq_ord;
                if ($db_ready) {
                    $rid = isset($remain['id']) ? (int)$remain['id'] : 0;
                    if ($rid > 0) {
                        $usql = "UPDATE `{$t}` SET `ord`='".sql_escape_string((string)$seq_ord)."' WHERE id='".intval($rid)."' AND wr_id='".intval($rt['wr_id'])."'";
                        sql_query($usql, true);
                    }
                }

                $result[] = $remain;
            }

            $old_paths = $collect_delete_paths($old_list_orig);
            if ($old_paths) {
                $new_paths = $collect_delete_paths($result);
                $keep = array_flip($new_paths);
                $to_del = array();
                foreach ($old_paths as $p){ if (!isset($keep[$p])) $to_del[$p] = true; }
                if ($to_del && method_exists($this, 'delete_physical_paths_safely')){
                    $this->delete_physical_paths_safely(array_keys($to_del));
                }
            }


            return $result;
        };


        $merge_node = function($new_node, $old_node) use (&$merge_node, $merge_list, $merge_assoc, $is_list_array, $has_list_children){

            if (is_array($new_node)){


                if ($is_list_array($new_node) && !$has_list_children($new_node)){
                    return $merge_list($new_node, $old_node);
                } else {

                    return $merge_assoc($new_node, $old_node);
                }
            }

            return $new_node !== null ? $new_node : $old_node;
        };

        // 시작 경로 노드 가져오기
        $get_node_from = function($root, $path){
            $node = $root;
            foreach ($path as $step){
                if (is_array($node) && array_key_exists($step, $node)){
                    $node = $node[$step];
                } else {
                    $node = null;
                    break;
                }
            }
            return $node;
        };

        $new_start = $get_node_from($new_value, $start_path);
        $old_start = $get_node_from($old_value, $start_path);

        return $merge_node($new_start, $old_start);
    }

    protected function save_uploaded_files($files, $subdir){
        // data 경로
        $base = defined('G5_DATA_PATH') ? G5_DATA_PATH : (defined('G5_PATH') ? G5_PATH.'/data' : dirname(__FILE__).'/../../data');
        $dir  = rtrim($base, '/').'/'.trim($subdir, '/');

        if (!is_dir($dir)) {
            @mkdir($dir, G5_DIR_PERMISSION, true);
            @chmod($dir, G5_DIR_PERMISSION);
        }

        $just_one = false;
        if(isset($files['tmp_name'])){
            $files = array($files);
            $just_one=true;
        }
        $out = array();
        foreach ($files as $f){
            $src = $this->sanitize_filename($f['name']);
            $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

            // 이미지 타입 검사(이미지 아니어도 저장할거면 이 체크 제거)
            if (!in_array($ext, array('jpg','jpeg','png','gif','webp'))) continue;

            $save = $this->unique_filename($dir, $ext);
            if (!@move_uploaded_file($f['tmp_name'], $dir.'/'.$save)) continue;
            @chmod($dir.'/'.$save, G5_FILE_PERMISSION);

            // 타입 숫자 (IMAGETYPE_*)
            $itype = 0;
            if (function_exists('exif_imagetype')) $itype = @exif_imagetype($dir.'/'.$save);
            if (!$itype && function_exists('getimagesize')) {
                $info = @getimagesize($dir.'/'.$save);
                if (is_array($info) && isset($info[2])) $itype = (int)$info[2];
            }

            $rel  = '/data/'.trim($subdir,'/').'/'.$save;
            $out[] = array(
                'source'     => $src,
                'path'       => $rel,
                'type'       => (int)$itype,
                'created_at' => defined('G5_TIME_YMDHIS') ? G5_TIME_YMDHIS : date('Y-m-d H:i:s')
            );
        }
        if($just_one){
            return $out[0];
        }
        return $out;
    }

    // 물리 경로로 변환(G5_DATA_PATH 하위만 허용) 후 안전 삭제
    protected function delete_physical_paths_safely($paths){
        if (!is_array($paths) || !count($paths)) return;

        $base = defined('G5_DATA_PATH') ? rtrim(G5_DATA_PATH, '/') : (defined('G5_PATH') ? rtrim(G5_PATH,'/').'/data' : '');
        if ($base === '') return;

        foreach ($paths as $rel){
            $rel = ltrim($rel, '/'); // 예: data/weaver/...
            // 허용 루트 강제: data/ 로 시작하지 않으면 스킵
            if (strpos($rel, 'data/') !== 0) continue;

            $abs = $base . '/' . substr($rel, strlen('data/')); // base/data + 나머지
            $abs = preg_replace('#/+#', '/', $abs);

            // 경로 탈출 방지
            $real_base = realpath($base);
            $real_abs  = @realpath($abs);
            if ($real_base && $real_abs && strpos($real_abs, $real_base) === 0) {
                @unlink($real_abs);
            }
        }
    }


    /**
     * location 플러그인의 region depth 스킨 렌더링
     * @param array $options 스킨 옵션
     * @return string 렌더링된 HTML
     */
    public function render_location_region_selector($options = array()){
        // location 플러그인이 있는지 확인
        if (!wv()->has_plugin('location')) {
            return '<!-- Location 플러그인이 활성화되지 않았습니다. -->';
        }

        $defaults = array(
            'skin_type' => 'region/depth',
            'multiple' => true,
            'max_count' => 3,
            'theme' => 'basic',
            'device' => 'pc'
        );

        $options = array_merge($defaults, $options);

        // location 플러그인의 Widget 렌더링 호출
        return wv()->location->render_widget($options['skin_type'], $options);
    }

    /**
     * 주소 검색 API 프록시
     * @param string $query 검색어
     * @return array 검색 결과
     */
    public function search_address($query){
        if (!wv()->has_plugin('location')) {
            return array();
        }

        // location 플러그인의 API 호출
        return wv()->location->api_search($query);
    }

    /**
     * 현재 위치 기반 주소 정보 가져오기
     * @param float $lat 위도
     * @param float $lng 경도
     * @return array 주소 정보
     */
    public function get_address_by_coords($lat, $lng){
        if (!wv()->has_plugin('location')) {
            return array();
        }

        return wv()->location->coord_to_address($lat, $lng);
    }

    /**
     * location 파트 렌더링 (통합 주소 선택기 포함)
     * @param int $wr_id
     * @param string $context form|view|edit
     * @param array $vars 추가 변수
     * @return string
     */
    public function render_location_part($wr_id = 0, $context = 'form', $vars = array()){
        // location 플러그인 연동 변수 추가
        $vars['location_plugin_available'] = wv()->has_plugin('location');
        $vars['location_ajax_url'] = wv()->has_plugin('location') ? wv()->location->ajax_url() : '';

        return $this->render_part('location', $wr_id, $context, $vars);
    }

    public function rsync_store($bo_table){
        global $g5;
return;

        $write_table = $g5['write_prefix'].$bo_table;
        $sql = "select a.*,b.mb_3 from $write_table as a left join g5_member as b  on  a.mb_id=b.mb_id where wr_is_comment=0   order by wr_id asc";
        $result = sql_query($sql);

        while ($row = sql_fetch_array($result)){
            $data =$row;

            $data['wr_id']=$row['wr_id'];
            $data['wr_subject']=$row['wr_subject'];
            $data['wr_content']=strip_tags($row['wr_content']);

            // store
            $data['store']['name']=$row['wr_subject'];
            $data['store']['category']=$this->parts['store']->get_category_index($row['ca_name']);
            $data['store']['tel']=$row['wr_tel'];
            $data['store']['notice']=strip_tags($row['wr_content']);
            $images = get_file($bo_table,$row['wr_id']);
            foreach ($images as $k=>$v){
                if(!$v['view'])continue;
                $path_arr = explode('/data/',$v['path']);
                $data['store']['image'][$k]=array(
                    'source'=>$v['source'],
                    'path'=>'https://dum2yo.com/data/'.$path_arr[1].'/'.$v['file'],
                    'type'=>$v['type'],
                    'ord'=>$k
                );
            }

            //contract
            $data['contract']['cont_pdt_type'] = 1;
            $data['contract']['biz_num'] = wv_format_biznum($row['mb_3']);
            $data['contract']['mb_id'] = $row['mb_id'];

            //location

            $data['location'] = wv()->location->coords($row['map_lat'],$row['map_lng']);


            //biz
            $data['biz']['parking']=get_text($row['wr_6']);

            $sql2 = "select * from $write_table where wr_is_comment=1 and wr_parent='{$row['wr_id']}' order by wr_id asc, wr_order asc";

            $result2 = sql_query($sql2);
            $i=0;
            while ($row2 = sql_fetch_array($result2)){
                $data['menu'][$i]['name'] = $row2['wr_subject'];
                $data['menu'][$i]['prices'][0] = str_replace(',','',$row2['wr_10']);
                $data['menu'][$i]['is_main'] = $row2['wr_1']=='대표메뉴'?1:0;
                $data['menu'][$i]['use_eum'] = $row2['wr_use_eum'];
                $data['menu'][$i]['ord'] = $i;

                $i++;
            }
            //menu

            wv()->store_manager->made('sub01_01')->set($data);

        }

    }

    public function rsync_member(){
        global $g5;

return;
        $write_table = $this->get_write_table_name();

        $sql = "select * from g5_member where mb_id<>'admin'   order by mb_no asc  ";
        $result = sql_query($sql);

        while ($row = sql_fetch_array($result)){
            $data =$row;

            $data['mb_id']=$row['mb_id'];
            $data['wr_name']=$row['mb_name'];
            $data['wr_password']=$row['mb_password'];
            $data['wr_subject']='/';
            $data['wr_content']='/';
            if($row['mb_level']==3){
                $data['member']['is_ceo']='1';
            }

            $this->set($data);

        }

    }

    ///////////////캐싱적용
    /** 캐싱이 적용된 write_row 조회 */
    protected function fetch_write_row_cached($wr_id) {
        $wr_id = (int)$wr_id;

        if (isset($this->write_cache[$wr_id])) {
            return $this->write_cache[$wr_id];
        }

        $row = $this->fetch_write_row($wr_id);
        $this->write_cache[$wr_id] = $row;

        return $row;
    }

    /** 캐싱이 적용된 ext_row 조회 */
    protected function fetch_store_row_cached($wr_id) {
        $wr_id = (int)$wr_id;

        if (isset($this->ext_cache[$wr_id])) {
            return $this->ext_cache[$wr_id];
        }

        $row = $this->fetch_store_row($wr_id);
        $this->ext_cache[$wr_id] = $row;

        return $row;
    }

    /** 캐싱이 적용된 list_part 조회 */
    protected function fetch_list_part_rows_for_wr_ids_cached($wr_ids) {
        if (!is_array($wr_ids)) $wr_ids = array($wr_ids);

        $cached_data = array();
        $uncached_ids = array();

        // 캐시된 데이터와 미캐시 ID 분리
        foreach ($wr_ids as $wr_id) {
            $wr_id = (int)$wr_id;
            if (isset($this->list_cache[$wr_id])) {
                $cached_data[$wr_id] = $this->list_cache[$wr_id];
            } else {
                $uncached_ids[] = $wr_id;
            }
        }

        // 미캐시 데이터 조회
        if (count($uncached_ids)) {
            $new_data = $this->fetch_list_part_rows_for_wr_ids($uncached_ids);

            // 캐시에 저장
            foreach ($uncached_ids as $wr_id) {
                $this->list_cache[$wr_id] = array();
                foreach ($this->parts as $pkey => $schema) {
                    if ($this->is_list_part_schema($schema)) {
                        $this->list_cache[$wr_id][$pkey] = isset($new_data[$pkey][$wr_id]) ? $new_data[$pkey][$wr_id] : array();
                    }
                }
            }

            // 결과 병합
            foreach ($new_data as $pkey => $wr_data) {
                foreach ($wr_data as $wr_id => $list_data) {
                    if (!isset($cached_data[$wr_id])) $cached_data[$wr_id] = array();
                    $cached_data[$wr_id][$pkey] = $list_data;
                }
            }
        }

        // 기존 포맷으로 변환
        $result = array();
        foreach ($cached_data as $wr_id => $wr_data) {
            foreach ($wr_data as $pkey => $list_data) {
                $result[$pkey][$wr_id] = $list_data;
            }
        }

        return $result;
    }

    /** 캐시 클리어 메서드들 */
    public function clear_cache($wr_id = null) {
        if ($wr_id !== null) {
            $wr_id = (int)$wr_id;
            unset($this->store_cache[$wr_id]);
            unset($this->write_cache[$wr_id]);
            unset($this->ext_cache[$wr_id]);
            unset($this->list_cache[$wr_id]);
        } else {
            $this->store_cache = array();
            $this->write_cache = array();
            $this->ext_cache = array();
            $this->list_cache = array();
        }
    }

    public function clear_store_cache($wr_id) {
        $wr_id = (int)$wr_id;
        unset($this->store_cache[$wr_id]);
    }

    public function clear_write_cache($wr_id) {
        $wr_id = (int)$wr_id;
        unset($this->write_cache[$wr_id]);
    }

    public function clear_ext_cache($wr_id) {
        $wr_id = (int)$wr_id;
        unset($this->ext_cache[$wr_id]);
    }

    public function clear_list_cache($wr_id) {
        $wr_id = (int)$wr_id;
        unset($this->list_cache[$wr_id]);
    }



    /** 캐시 상태 확인 (디버깅용) */
    public function get_cache_info() {
        return array(
            'store_cache_count' => count($this->store_cache),
            'write_cache_count' => count($this->write_cache),
            'ext_cache_count' => count($this->ext_cache),
            'list_cache_count' => count($this->list_cache),
            'cached_wr_ids' => array_keys($this->store_cache)
        );
    }

    /** 메모리 사용량이 많을 때 캐시 제한 */
    protected function limit_cache_size($max_items = 100) {
        if (count($this->store_cache) > $max_items) {
            // LRU 방식으로 오래된 캐시부터 제거
            $this->store_cache = array_slice($this->store_cache, -$max_items, null, true);
            $this->write_cache = array_slice($this->write_cache, -$max_items, null, true);
            $this->ext_cache = array_slice($this->ext_cache, -$max_items, null, true);
            $this->list_cache = array_slice($this->list_cache, -$max_items, null, true);
        }
    }
}

StoreManager::getInstance();
