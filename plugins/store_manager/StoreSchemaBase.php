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

    /** @var array 업서트 허용 컬럼(논리명). 비어있으면 $columns 기준 자동 */
    protected $allowed = array();

    public function set_context($manager, $bo_table, $part_key, $plugin_theme_path = ''){
        $this->manager           = $manager;
        $this->bo_table          = (string)$bo_table;
        $this->part_key          = (string)$part_key;
        $this->plugin_theme_path = (string)$plugin_theme_path;
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

        if ($this->is_list_part()) {
            return $this->render_list_part_template($type, $vars);
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

    protected function render_list_part_template($context, $vars = array()){
        if (!$this->is_list_part()) return '';

        $root = isset($this->plugin_theme_path) ? rtrim($this->plugin_theme_path, '/')
            : rtrim(dirname(__FILE__).'/theme/basic/pc', '/');

        if (isset($vars['skin_root']) && $vars['skin_root']) {
            $root = rtrim($vars['skin_root'], '/');
        }

        $context  = preg_replace('/[^a-zA-Z0-9_\/-]/', '', $context);
        $part_key = preg_replace('/[^a-zA-Z0-9_\/-]/', '', $this->part_key);

        // 목록 파트 스킨 경로 우선순위
        $candidates = array(
            $root . '/' . $part_key . '/' . $context . '.php',        // ex) menu/form.php (권장)
            $root . '/' . $context  . '/' . $part_key . '.php',       // ex) form/menu.php
            $root . '/' . $part_key . '/' . $context . '/index.php',  // ex) menu/form/index.php
        );

        $skin_path = '';
        foreach($candidates as $f){
            if (file_exists($f)) {
                $skin_path = $f;
                break;
            }
        }

        if (!$skin_path) {
            return "<!-- StoreSchemaBase: list part skin not found ({$part_key}/{$context}) -->";
        }


        // 스킨 변수 준비
        $row = isset($vars['row']) ? $vars['row'] : array();
        $list = isset($vars['list']) ? $vars['list'] : array();
        $row['list'] = $list; // 호환성
        $bo_table = $this->bo_table;
        $part = $part_key;

        // 기본 데이터가 없으면 빈 배열로 초기화
        if (!isset($row[$part_key]) || !is_array($row[$part_key])) {
            $row[$part_key] = array();
        }

        // 추가 변수들 바인딩
        if (is_array($vars)) {
            foreach($vars as $k => $v){
                if ($k !== 'row' && $k !== 'list') ${$k} = $v;
            }
        }
        if(!array_filter($row[$part_key])){
            $row[$part_key]=(array)'';
        }

        $skin_id = function_exists('wv_make_skin_id') ? wv_make_skin_id() : 'skin_'.uniqid();
        $skin_selector = function_exists('wv_make_skin_selector') ? wv_make_skin_selector($skin_id) : '.'.$skin_id;

        ob_start();
        include $skin_path;
        return ob_get_clean();
    }

    public function make_array(&$arr){
        $arr =  array_filter((array)$arr);
        $arr['-1']='';
    }
}
