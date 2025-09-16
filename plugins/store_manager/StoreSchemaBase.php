<?php
namespace weaver\store_manager;

/**
 * StoreSchemaBase (PHP 5.6)
 * - array_part 호환 완전 제거
 * - list_part 만 지원
 * - $columns 속성을 반드시 사용, get_columns()는 그대로 반환
 */
abstract class StoreSchemaBase implements  StoreSchemaInterface{

    protected $manager = null;

    /** @var string */
    protected $bo_table = '';

    /** @var string */
    protected $part_key = '';

    /** @var string */
    protected $plugin_theme_path = '';

    /** @var bool 목록 파트 여부 */
    public $list_part = false;

    /** @var array 컬럼 정의 (논리명 => DDL) */
    protected $columns = array();

    /** @var array 인덱스 정의 (name/type/cols[]) */
    protected $indexes = array();

    protected $store = '';

    /** @var array 업서트 허용 컬럼(논리명). 비어있으면 $columns 기준 자동 */
    protected $allowed = array();

    public function set_context($manager, $bo_table, $part_key, $plugin_theme_path = ''){
        $this->manager           = $manager;
        $this->bo_table          = (string)$bo_table;
        $this->part_key          = (string)$part_key;
        $this->plugin_theme_path = (string)$plugin_theme_path;
    }

    /** 반드시 $columns 그대로 반환 */
    public function set_store($obj){
        $this->store= $obj;
    }


    /** 반드시 $columns 그대로 반환 */
    public function get_columns(){
        return $this->columns;
    }

    public function get_indexes(){
        return $this->indexes;
    }

    public function get_allowed_columns(){
        if (is_array($this->allowed) && count($this->allowed)) {
            return $this->allowed;
        }
        $allow = array();
        foreach ($this->columns as $name => $ddl) {
            if ($name === 'id' || $name === 'wr_id' || $name === 'ord') continue;
            if (!is_string($ddl) || !strlen(trim($ddl))) continue;
            $allow[] = $name;
        }
        return $allow;
    }

    /**
     * 단일 필드 템플릿 경로 반환 (없으면 빈 문자열)
     * @param string $column
     * @param string $type 'view'|'form'
     * @return string
     */
    public function get_template_path($column, $type){
        if ($type !== 'view' && $type !== 'form') return '';
        if (!preg_match('/^[A-Za-z0-9_]+$/', $this->part_key)) return '';
        if (!preg_match('/^[A-Za-z0-9_]+$/', $column)) return '';
        if (!strlen($this->plugin_theme_path)) return '';

        $base = rtrim($this->plugin_theme_path, '/');
        $path = $base . '/' . $this->part_key . '/' . $type . '/' . $column . '.php';
        return file_exists($path) ? $path : '';
    }

    /**
     * 필드 템플릿 렌더
     * - $column: 문자열(단일 컬럼) 또는 배열(여러 컬럼 순서대로)
     * - 파일이 없으면 조용히 스킵
     *
     * @param string|array $column
     * @param string $type 'view'|'form'
     * @param array  $vars
     * @return string
     */
    public function render_part($column, $type, $vars = array()){
        // 목록 파트는 특수 처리 (menu/form.php 같은 통합 스킨)

//        $walk_function = function (&$arr,$arr2,$node) use(&$walk_function)  {
//
//
//            if(!is_array($arr)){
//                return false;
//            }
//            foreach ($arr as $k=>&$v){
//
//                wv_walk_by_ref_diff($v,$walk_function,$arr2[$k],array_merge($node,(array)$k));
//
//            }
//            if(wv_is_all_int_keys($arr)){
//
//                array_unshift($arr, array());
//
//            }
//
//
//            return false;
//        };
//        foreach ($this->get_allowed_columns() as $k=>$v){
//            wv_walk_by_ref_diff($vars['row'][$v],$walk_function);
//        }

        if ($column === '*') {
            $column = $this->get_columns_with_ddl();
        }


        if (is_array($column)) {
            $html = '';
            foreach ($column as $col) {
                if (!is_string($col) || !preg_match('/^[A-Za-z0-9_]+$/', $col)) continue;
                $chunk = $this->render_part($col, $type, $vars);
                if (strlen($chunk)) $html .= $chunk;
            }
            return $html;
        }

        if (!is_string($column) || !strlen($column)) return '';
        // 컬럼 유효성 검증: $columns에 정의된 컬럼만 허용

        if (!isset($this->columns[$column])) {
            return "  StoreSchemaBase: column '{$column}' not defined in \$columns  ";
        }

        $tpl = $this->get_template_path($column, $type);
        if (!$tpl) return '';

        $row = (isset($vars['row']) && is_array($vars['row'])) ? $vars['row'] : array();

        // 기본: 논리키
        $value = isset($row[$column]) ? $row[$column] : '';

        // 논리키가 비면 물리키도 시도 (역호환/안전망)
        if ($value === '' && $this->manager && method_exists($this->manager, 'get_physical_col') && strlen($this->part_key)) {
            $physical = $this->manager->get_physical_col($this->part_key, $column);
            if (isset($row[$physical])) $value = $row[$physical];
        }

        $field_name = $this->part_key . '[' . $column . ']';
        $part_key = $this->part_key;
        $bo_table = $this->bo_table;


        if (is_array($vars) && count($vars)) {
            foreach ($vars as $__k => $__v) { $__k = $__v; }
        }

        $skin_id = wv_make_skin_id();
        $skin_selector = wv_make_skin_selector($skin_id);
        $wv_skin_path = dirname($tpl);
        $wv_skin_url = str_replace(G5_PATH, G5_URL, $wv_skin_path);

        ob_start();
        include $tpl;
        return ob_get_clean();
    }




    public function is_list_part(){
        return $this->list_part ? true : false;
    }


    public function get_part_key() { return $this->part_key; }
    public function get_bo_table() { return $this->bo_table; }
    public function get_manager()  { return $this->manager; }
    public function get_plugin_theme_path() { return $this->plugin_theme_path; }

    public function get_columns_with_ddl(){
        $result = array();
        foreach ($this->columns as $name => $ddl) {
            if ($name === 'id' || $name === 'wr_id' || $name === 'ord') continue;
            if (!is_string($ddl) || !strlen(trim($ddl))) continue;
            $result[] = $name;
        }
        return $result;
    }

    public function make_array(&$arr){
        $arr =  array_filter((array)$arr);
        $arr['-1']='';
    }
}
