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

    protected $checkbox_fields = array();

    /** @var array is_ 패턴이지만 체크박스가 아닌 필드들 (제외 목록) */
    protected $non_checkbox_is_fields = array();

    protected $checkbox_patterns = array('is_', 'use_');

    protected $ajax_data_field = array('made' ,'part' ,'fields','wr_id','size' );

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

    protected function get_auto_detected_checkbox_fields(){
        $auto_fields = array();
        $patterns = $this->get_checkbox_patterns(); // ['is_', 'use_']
        $non_checkbox = $this->get_non_checkbox_pattern_fields();

        foreach ($this->columns as $name => $ddl) {
            // 패턴 중 하나라도 매치되고 제외 목록에 없으면 체크박스로 간주
            $matches_pattern = false;
            foreach ($patterns as $pattern) {
                if (strpos($name, $pattern) === 0) {
                    $matches_pattern = true;
                    break;
                }
            }

            if ($matches_pattern && !in_array($name, $non_checkbox, true)) {
                $auto_fields[] = $name;
            }
        }

        return $auto_fields;
    }

    /**
     * 명시적 정의 + 자동 감지 결합한 체크박스 필드 목록 반환
     */
    public function get_checkbox_fields(){
        $explicit = is_array($this->checkbox_fields) ? $this->checkbox_fields : array();
        $auto = $this->get_auto_detected_checkbox_fields();

        // 중복 제거하여 합침
        return array_values(array_unique(array_merge($explicit, $auto)));
    }

    public function get_non_checkbox_is_fields(){
        return is_array($this->non_checkbox_is_fields) ? $this->non_checkbox_is_fields : array();
    }

    /**
     * 체크박스 감지 패턴 반환 (is_, use_ 등)
     * @return array
     */
    public function get_checkbox_patterns(){
        return is_array($this->checkbox_patterns) ? $this->checkbox_patterns : array('is_', 'use_');
    }

    /**
     * 패턴 매치되지만 체크박스가 아닌 필드들 반환
     * get_non_checkbox_is_fields()와 동일한 역할
     * @return array
     */
    public function get_non_checkbox_pattern_fields(){
        return $this->get_non_checkbox_is_fields();
    }

    public function is_checkbox_field($field_name){
        return in_array($field_name, $this->get_checkbox_fields(), true);
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
        if (!preg_match('/^[A-Za-z0-9_\/]+$/', $column)) return '';
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
                if (!is_string($col) || !preg_match('/^[A-Za-z0-9_\/]+$/', $col)) continue;
                $chunk = $this->render_part($col, $type, $vars);
                if (strlen($chunk)) $html .= $chunk;
            }
            return $html;
        }

        if (!is_string($column) || !strlen($column)) return '';
        // 컬럼 유효성 검증: $columns에 정의된 컬럼만 허용

        if (!isset($this->columns[$column])) {
//            return "  StoreSchemaBase: column '{$column}' not defined in \$columns  ";
        }

        $tpl = $this->get_template_path($column, $type);
        if (!$tpl) return '';

        $row = (isset($vars['row']) && is_array($vars['row'])) ? $vars['row'] : array();

        // ===== 목록 파트 특수 처리 시작 =====
        $item_id = null;
        $is_list_item_mode = false;



        // ===== 목록 파트 특수 처리 끝 =====

        // 기본: 논리키
//        $value = isset($row[$column]) ? $row[$column] : '';

        // 논리키가 비면 물리키도 시도 (역호환/안전망)
//        if ($value === '' && $this->manager && method_exists($this->manager, 'get_physical_col') && strlen($this->part_key)) {
//            $physical = $this->manager->get_physical_col($this->part_key, $column);
//            if (isset($row[$physical])) $value = $row[$physical];
//        }



        if (is_array($vars) && count($vars)) {
            foreach ($vars as $__k => $__v) {
                $$__k = $__v;
            }
        }


        if ($this->is_list_part()) {
            // {part_key}_id 변수 체크 (예: menu_id, store_id)
            $id_key = $this->part_key . '_id';
            if (isset($vars[$id_key]) && $vars[$id_key] !== '') {
                $item_id = $vars[$id_key];
                $is_list_item_mode = true;

                // 해당 아이템을 $row에 직접 설정 (일반 파트처럼 접근 가능)
                if (isset($vars['list']) && is_array($vars['list']) && isset($vars['list'][$item_id])) {

                    // list에서 해당 아이템을 가져와서 $row에 병합
                    $list_item = $vars['list'][$item_id];

                    if (is_array($list_item)) {
                        $wewe=1;
                        $row = array_merge($row, $list_item);

                    }
                }
            }
        }

        if ($this->is_list_part()) {

            if($item_id === null){

                $item_id =-5;//임시번호
            }
            // 목록 파트 아이템 모드: part_key[item_id][column]
            $field_name = $this->part_key . '[' . $item_id . '][' . $column . ']';
        } else {
            // 기본 모드: part_key[column]
            $field_name = $this->part_key . '[' . $column . ']';
        }
        $part_key = $this->part_key;
        $bo_table = $this->bo_table;

        $ajax_data =array_intersect_key($vars, array_flip($this->ajax_data_field));

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
