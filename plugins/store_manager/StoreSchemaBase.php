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

    protected $ajax_data_field = array('action','made','part','field','wr_id');

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

        global $member;

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
        if (!$tpl) {
            wv_error("{$type} / {$column} not found",1);
        }

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

        // ===== 1. 리로드용 데이터 생성 (extract 전에!) =====
        $reload_ajax_data = array(
            'url' => $this->manager->plugin_url . '/ajax.php',  // ← url 포함!
            'action' => 'render_part',
            'made' => $this->manager->get_make_id(),
            'part' => $this->part_key,
            'column' => $column,
            'type' => $type
        );

        // $vars에서 '_id'로 끝나는 키의 스칼라 값만 추가
        if (is_array($vars) && count($vars)) {
            foreach ($vars as $key => $value) {
                // 스칼라 값이고, 키가 '_id'로 끝나는 경우만
                if ((is_scalar($value) || is_null($value)) && substr($key, -3) === '_id') {
                    $reload_ajax_data[$key] = $value;
                }
            }
        }

        // 템플릿 변수에 추가 (하나만!)
        $vars['reload_ajax_data'] = $reload_ajax_data;


        if (is_array($vars) && count($vars)) {
            foreach ($vars as $__k => $__v) {
                $$__k = $__v;
            }
        }

        if ($this->is_list_part()) {
            // {part_key}_id 변수 체크 (예: menu_id, store_id)
            $id_key = $this->part_key . '_id';


            if (key_exists($id_key,$vars)){
                $is_list_item_mode = true;
            }
            if (key_exists($id_key,$vars) && $vars[$id_key] !== '') {

                $item_id = $vars[$id_key];

                // 해당 아이템을 $row에 직접 설정 (일반 파트처럼 접근 가능)
                if (isset($vars['list']) && is_array($vars['list']) && isset($vars['list'][$item_id])) {

                    // list에서 해당 아이템을 가져와서 $row에 병합
                    $list_item = $vars['list'][$item_id];

                    if (is_array($list_item)) {
                        $row = array_merge($row, $list_item);

                    }
                }
            }

            if($item_id=='skeleton'){
                $row['id']='skeleton';
            }
        }

        if ($this->is_list_part()) {

            $item_key = $item_id;



//            if($is_list_item_mode and $item_id===null){
//                $item_key=-1;
//            }
//            if(!$is_list_item_mode or  $item_id==='skeleton'){
//                $item_key =-1;//임시번호
//            }
            if(!$item_id or $item_id==='skeleton'){
                $item_key =-1;//임시번호
            }

            // 목록 파트 아이템 모드: part_key[item_id][column]
            $field_name = $this->part_key . '[' . $item_key . '][' . $column . ']';

            if(!in_array($column,$this->get_allowed_columns())){

                $field_name = str_replace("[{$column}]",'',$field_name);

            }

        } else {
            // 기본 모드: part_key[column]
            $field_name = $this->part_key . '[' . $column . ']';
        }

        $part_key = $this->part_key;
        $bo_table = $this->bo_table;


        $ajax_data =array_intersect_key($vars, array_flip($this->ajax_data_field));
        $ajax_data_base = array(
            'action' => $type,
            'made' => $this->manager->get_make_id(),
            'part' => $this->part_key,
            'column' => $column,
            'type' => $type,
            'wr_id'=>$row['wr_id'],
            'mb_id'=>$member['mb_id']
        );


        if ($this->is_list_part()) {
            $temp_row_id = $row['id']?$row['id']:0;
            $ajax_data_base[$this->part_key][$temp_row_id]['id'] = $row['id'];
            $ajax_data_base[$this->part_key][$temp_row_id][$column] = $row[$column]?$row[$column]:$vars[$column];
        } else {
            if(isset($row[$column]) and is_array($row[$column]) and $row[$column]['id']){
                $ajax_data_base[$this->part_key][$column]['id'] = $row[$column]['id'];
            }

        }
        $ajax_data = array_merge($ajax_data_base, $ajax_data);

//        if($column=='profile_image'){
//            dd($ajax_data_base);
//        }


        if($this->is_list_part() and $type=='view'){
            unset($row['list'][-1]);
            unset($row[$part_key][-1]);
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
        $arr['-1']=array('id'=>'skeleton');
    }
}
