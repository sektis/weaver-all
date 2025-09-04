<?php
namespace weaver;

class Menu extends Makeable{

    private $menu = array();
    private $path_conversion_array = array();
    private $require_attr = array('name','url');
    private $add_global_var = array('wv_page_id','ca_id');

    private $wvd_count = 1;
    private $id_count = 1;

    private $active_id = '';
    private $expand_ids = array();

    private $arr_ref = array();
    private $arr_parent_ref = array();
    private $arr_id = array();
    private $arr_url = array();
    private $arr_url_without_wvd = array();
    private $arr_fragment = array();
    private $arr_url_child = array();
    private $arr_url_root = array();
    private $arr_path = array();
    private $arr_qstr = array(); 



    public function __construct() {

        $this->setConversionPath('/bbs/write.php','/bbs/board.php');
        $this->setConversionPath('/shop/index.php','/index.php');
    }

    public function init_once(){
        wv_add_qstr(array('wvd', 'wvt', 'wvs'));
    }

    public function add_global_var($var){
        $this->add_global_var[] = $var;
        $this->add_global_var = array_unique($this->add_global_var);

    }

  

    public function getGnuboardMenu(){
        global $g5;
        $menu_list = array();



        $sql = " select * from {$g5['menu_table']} where me_use=1 order by me_code,me_id ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++)
        {

            $depth=strlen($row['me_code'])/2;
            $parent_id = substr($row['me_code'],0,($depth-1)*2);

            $arr = array('id'=>$row['me_code'],'name'=>$row['me_name'],'order'=>$row['me_order'],'url'=>$row['me_link']);

            $this->append($arr,$parent_id);
        }
//        dd($this->getMenu());

    }




    public function setConversionPath($org_path,$change_path)
    {
        $this->path_conversion_array[$org_path] = $change_path;
    }

    public function setMenu($menu_array,$reset=false){

        if($reset){
            $this->resetMenu();
        }
        foreach ($menu_array as $val) {
            $this->append($val);
        }

        return $this;
    }

    private function resetMenu(){
        $this->menu = array();
        $this->path_conversion_array = array();

        $this->wvd_count = 1;
        $this->id_count = 1;

        $this->active_id = '';
        $this->expand_ids = array();

        $this->arr_ref = array();
        $this->arr_parent_ref = array();
        $this->arr_id = array();
        $this->arr_url = array();
        $this->arr_url_without_wvd = array();
        $this->arr_fragment = array();
        $this->arr_url_child = array();
        $this->arr_url_root = array();
        $this->arr_path = array();
        $this->arr_qstr = array();
    }

    public function append($menu_array,$parent_id=''){
        $this->addChild($menu_array,$parent_id);
    }

    public function prepend($menu_array,$parent_id=''){
        $this->addChild($menu_array,$parent_id,'prepend');
    }

    public function insertBefore($menu_array,$sibling_id){
        $this->addSibling($menu_array,$sibling_id,'insertBefore');
    }

    public function insertAfter($menu_array,$sibling_id){
        $this->addSibling($menu_array,$sibling_id);
    }

    public function getActiveMenuId()
    {

        if($this->active_id){
            return $this->active_id;
        }


        $curr_url_parse = wv_get_current_url(false,true,true);

        foreach ($this->add_global_var as $val){
            if($GLOBALS[$val]){
                $curr_url_parse['query'][$val] = $GLOBALS[$val];
            }
        }

        unset($curr_url_parse['port']);
        unset($curr_url_parse['scheme']);
        unset($curr_url_parse['query']['wv_dir_var']);

        $curr_url = wv_query_sort_url($curr_url_parse);

        $curr_path = $curr_url_parse['path'];
        $curr_query = $curr_url_parse['query'];

        $find_id = $this->compareInfo($curr_url,$curr_path,$curr_query);




        if($find_id){

            $find_id = $this->getRelativeChildId($find_id);

            $top_level_url_array = $this->arr_url_root[$this->getParentId($find_id,true)];

            if(defined('_MAIN_PAGE_') and _MAIN_PAGE_){

                $top_level_url_array = $this->arr_url_root['root'];
            }


            $curr_url = $this->arr_url_without_wvd[$find_id];

            foreach ($top_level_url_array as $key=>$val){
                if(  $curr_url == $val and $this->arr_fragment[$key]){
                    $this->arr_ref[$key]['url'] =  $this->arr_fragment[$key];
                }
            }


            $this->active_id = $find_id;

            $this->changeAttr($find_id,array('active'=>'1'));


            $child_id = $find_id;
            $this->expand_ids[] = $find_id;
            while ($parent_ref = &$this->arr_parent_ref[$child_id]){

                if(!$parent_ref['id'])break;

                $this->changeAttr($parent_ref,array('expand'=>'1'));
                $this->expand_ids[] = $parent_ref['id'];
                $child_id = $parent_ref['id'];

            }

            $this->expand_ids = array_reverse($this->expand_ids);

        }


        return $find_id;

    }

    public function getMenu($id='')
    {

        if($id==''){
            $array = json_encode($this->menu);

        }else{
            $array = json_encode($this->getRef($id));
        }

        return json_decode($array,true);
    }

    public function displayMenu($skin,$menu_id='',$re_order = true,$option=array()){

        if($re_order){
            $this->menuReOrder();
        }

        $this->getActiveMenuId();

        $menu_array = $this->getMenu($menu_id);


        return $this->makeSkin($skin,$menu_array,$option);
    }

    public function displaySubMenu($skin,$re_order=true){

        $current_id = $this->getParentId($this->getActiveMenuId(),true);

        return $this->displayMenu($skin,$current_id,$re_order);

    }


    public function getParentId($current_id='',$top_parent=false){
        if($current_id==''){
            $current_id = $this->getActiveMenuId();
        }
        if(is_numeric($top_parent)){
            if($top_parent<1){
                return $current_id;
            }
            return $this->getParentId(isset($this->arr_parent_ref[$current_id]['id'])?$this->arr_parent_ref[$current_id]['id']:$current_id,$top_parent-1);
        }
        if(!$top_parent){
            return isset($this->arr_parent_ref[$current_id]['id'])?$this->arr_parent_ref[$current_id]['id']:$current_id;
        }
        return (isset($this->arr_parent_ref[$current_id]['id']))?$this->getParentId($this->arr_parent_ref[$current_id]['id'],true):$current_id;
    }

    private function getRelativeChildId($current_id){

        $current_url = $this->arr_url_without_wvd[$current_id].($this->arr_fragment[$current_id]?'#'.$this->arr_fragment[$current_id]:'');

        if(!isset($this->arr_url_child[$current_id])){
            return $current_id;
        }


        $find = array_search($current_url,$this->arr_url_child[$current_id]);



        return $find?$this->getRelativeChildId($find):$current_id;
    }

    public function displayBreadcrumb($skin, $re_order = true){

        if($re_order){
            $this->menuReOrder();
        }

        $this->getActiveMenuId();



        $menu_array = $this->getExpandArray();

        return $this->makeSkin($skin,$menu_array);
    }

    public function displayNavigation($skin,$re_order = true){

        if($re_order){
            $this->menuReOrder();
        }

        $this->getActiveMenuId();


        $menu_id = $this->active_id;


        $menu_array = array();
        $breadcrumb_array = $this->getExpandArray();

        // navigation 메뉴의 경우 active 메뉴 기준 서브메뉴가 있을경우추가해준다
        if(isset($this->arr_ref[$this->active_id]['sub'])){

            $breadcrumb_array[] = $this->arr_ref[$menu_id]['sub'][0];
        }

        foreach ($breadcrumb_array as $item) {
            $arr = array();

            if($this->arr_parent_ref[$item['id']] == $this->menu){
                $parent_ref = $this->arr_parent_ref[$item['id']];
            }else{
                $parent_ref = $this->arr_parent_ref[$item['id']]['sub'];
            }

            if(!$item['active'] and !$item['expand']){
                $arr[] = array('name'=>$this->arr_ref[$menu_id]['name'],'expand'=>1,'temp'=>1);
            }

            foreach ($parent_ref as $p_item){

                if(isset($p_item['sub'])){
                    unset($p_item['sub']);
                }

                $arr[] = $p_item;
            }

            $menu_array[] = $arr;
        }

        return $this->makeSkin($skin,$menu_array);
    }

    private function getExpandArray(){

        $menu_array = array();
        foreach ($this->expand_ids as $id){
            $id_ref = $this->arr_ref[$id];
            unset($id_ref['sub']);
            $menu_array[] = $id_ref;

        }

        return $menu_array;
    }

    private function getBreadcrumbArray($menu_id){

        $menu_array = $this->breadcrumbLoof($menu_id,array());
        return array_reverse($menu_array);
    }

    private function breadcrumbLoof($menu_id, $result_array = array()){

        $arr_ref = $this->arr_ref[$menu_id];

        unset($arr_ref['sub']);

        $result_array[] = $arr_ref;

        if(isset($this->arr_parent_ref[$menu_id]) and $this->arr_parent_ref[$menu_id]!==$this->menu){
            $result_array = $this->breadcrumbLoof($this->arr_parent_ref[$menu_id]['id'],$result_array);
        }

        return $result_array;
    }

    public function getUniqueUrls($need_merge_array = array()){
        $arr_url = array();
        $remove_attr = array('wvd','wvs');

        foreach ($this->arr_url as $url){
            $parse = parse_url($url);
            parse_str($parse['query'], $temp);
            foreach ($temp as $key=>$val){
                if(in_array($key,$remove_attr)) {
                    unset($temp[$key]);
                }
            }

            $url = $parse['scheme'].'://'.$parse['host'].$parse['path'];
            if(count($temp)){
                $url.='?'.http_build_query($temp);
            }
            $arr_url[] = $url;
        }


        return array_unique($arr_url);
    }

    private function changeAttr(&$menu,$change_attr){

        if(!is_array($menu)){
            $menu = &$this->getRef($menu);
        }

        foreach ($change_attr as $key=>$val){
            $menu[$key] = $val;
        }

    }

    private function compareInfo($curr_url,$curr_path,$curr_qstr)
    {

        // 타겟팅된 id를 가지고있다면
        if($curr_qstr['wvt'] and in_array($curr_qstr['wvt'],$this->arr_id)){
            return $curr_qstr['wvt'];
        }

        // 완전일치하는 url 검색
        $find_id = array_search($curr_url, $this->arr_url);

        if($find_id){

            return $find_id;
        }


        // path 일치하는 배열 획득
        $compare_path = array_filter($this->arr_path, function($element) use($curr_path){
            return $element == $curr_path;
        });

        // qstr 비교해서 menu_key_qstr 의 값들을 모두 만족하는 키가 1
        if(count($compare_path)){

            $compare_qstr = array();
            foreach ($compare_path as $key=>$val){
                $qstr_arr = $this->arr_qstr[$key];

                if(@count(array_diff_assoc((array)$qstr_arr,(array)$curr_qstr))==0){
                    $compare_qstr[$key] = $qstr_arr;
                }
            }


            if(count($compare_qstr)){
                $keys = array_keys($compare_qstr);
                array_multisort(array_map('count', $compare_qstr), SORT_DESC, $compare_qstr, $keys);
                $compare_qstr = array_combine($keys, $compare_qstr);

                return key($compare_qstr);
            }
        }


        if(isset($this->path_conversion_array[$curr_path])){
            return $this->compareInfo($curr_url,$this->path_conversion_array[$curr_path],$curr_qstr);
        }


        return false;
    }

    private function getCurrentKey($array,$id){
        $parent_array = $array;
        if(@is_array($array['sub'])){
            $parent_array = $array['sub'];
        }
        foreach ($parent_array as $key=>$val){
            if($val['id']==$id){
                return $key;
            }
        }

        wv_error('not found key',2);
    }

    private function  &getRef($id,$get_parent=false){
        if($id==''){
            return $this->menu;
        }
        if($get_parent){

            return $this->arr_parent_ref[$id];
        }

        return $this->arr_ref[$id];
    }

    private function setRef($id,&$ref,$set_parent=false){

        if($set_parent){
            $this->arr_parent_ref[$id]=&$ref;
            if(@$ref['id']){

                $this->arr_url_child[$ref['id']][$id] = $this->arr_url_without_wvd[$id].($this->arr_fragment[$id]?'#'.$this->arr_fragment[$id]:'');
                $this->arr_url_root[$this->getParentId($id,true)][$id] = $this->arr_url_without_wvd[$id];
            }

        }else{
            $this->arr_ref[$id]=&$ref;
            $this->arr_url_root['root'][$id] = $this->arr_url_without_wvd[$id];
        }
    }

    private function attrCheck(&$menu_array,$related_type,$related_id){
        global $config;

        $menu_array['url'] = wv_remove_param_url($menu_array['url']);


        if($related_id and !in_array($related_id,$this->arr_id)){
            wv_error("not found {$related_type}_id",2);
        }

        foreach ($this->require_attr as  $attr){
            if(!isset($menu_array[$attr])){

                wv_error("[{$menu_array['name']}] {$attr}(이)가 정의되지 않았습니다.",2);
            }
        }

        if(@!$menu_array['id']){
            $menu_array['id'] = $this->makeMenuId();
        }

        if(!isset($menu_array['order'])){$menu_array['order']=0;}
        if(!isset($menu_array['target'])){$menu_array['target']='_self';}


        if(array_search($menu_array['id'],$this->arr_id)){
            wv_error("[{$menu_array['name']}] id 중복",2);
        }




        $parse_url_org = $parse_url_save = wv_get_org_url($menu_array['url'],true);


        if(@$parse_url_org['fragment']){
            $this->arr_fragment[$menu_array['id']] = "#{$parse_url_org['fragment']}";
            unset($parse_url_save['fragment']);
        }

        $without_fragment_url = wv_query_sort_url($parse_url_save);


//        if(wv_is_domain($without_fragment_url) and in_array($without_fragment_url,$this->arr_url_without_wvd) and !$parse_url_org['fragment']){
        if(wv_is_url($without_fragment_url) and in_array($without_fragment_url,$this->arr_url_without_wvd) and !$parse_url_org['fragment']){


            $parse_url_org['query']['wvd'] = $this->wvd_count;
            $parse_url_save['query']['wvd'] = $this->wvd_count;
            $this->wvd_count++;
        }

        $this->arr_url_without_wvd[$menu_array['id']] = $without_fragment_url;

        $menu_array['url'] = wv_build_url($parse_url_org);
        unset($parse_url_save['scheme']);

        if('/'.basename($parse_url_save['path'])=='/index.php' and !isset($parse_url_save['query']['wv_page_id'])){
            $parse_url_save['query']['wv_page_id'] = 'main';
        }

        $this->arr_url[$menu_array['id']] =  wv_query_sort_url($parse_url_save);
        $this->arr_path[$menu_array['id']]  = isset($parse_url_org['path'])?$parse_url_org['path']:'';
        $this->arr_qstr[$menu_array['id']]  = isset($parse_url_org['query'])?$parse_url_org['query']:'';
        $this->arr_id[] = $menu_array['id'];


        if(function_exists('short_url_clean')){
            $menu_array['url'] = short_url_clean($menu_array['url']);
        }

    }

    private function addSibling($menu_array,$sibling_id='',$mode='insertAfter'){

        $this->attrCheck($menu_array,'sibling',$sibling_id);

        $sub_menu_array = array();


        if(isset($menu_array['sub'])){
            $sub_menu_array = $menu_array['sub'];
            unset($menu_array['sub']);
        }



        $parent_ref = &$this->getRef($sibling_id,true);
        $sibling_ref = &$this->getRef($sibling_id);

        $sibling_key = $this->getCurrentKey($parent_ref,$sibling_ref['id']);


        $this->insertArray($parent_ref,$sibling_key+($mode=='insertBefore'?0:1),$menu_array);


        $current_key = $this->getCurrentKey($parent_ref,$menu_array['id']);



        if(is_array($parent_ref['sub'])){
            $this->setRef($menu_array['id'],$parent_ref['sub'][$current_key]);
        }else{
            $this->setRef($menu_array['id'],$parent_ref[$current_key]);
        }




        $this->setRef($menu_array['id'],$parent_ref,true);




        if(count($sub_menu_array)){
            foreach ($sub_menu_array as $sub_menu){
                $this->append($sub_menu, $menu_array['id']);
            }
        }
    }

    private function addChild($menu_array,$parent_id='',$mode='append'){

        $this->attrCheck($menu_array,'parent',$parent_id);

        $sub_menu_array = array();

        if(isset($menu_array['sub'])){
            $sub_menu_array = $menu_array['sub'];
            unset($menu_array['sub']);
        }

        $parent_ref =  &$this->getRef($parent_id);

        if($parent_id and !is_array($parent_ref['sub'])){
            $parent_ref['sub'] = array();
        }

        $insert_function = 'array_push';
        if($mode=='prepend'){
            $insert_function = 'array_unshift';
        }


        if($parent_id){
            $insert_function($parent_ref['sub'],$menu_array);
        }else{
            $insert_function($parent_ref,$menu_array);
        }



        $current_key = $this->getCurrentKey($parent_ref,$menu_array['id']);

        if($parent_id){
            $this->setRef($menu_array['id'],$parent_ref['sub'][$current_key]);
        }else{
            $this->setRef($menu_array['id'],$parent_ref[$current_key]);
        }

        $this->setRef($menu_array['id'],$parent_ref,true);

        if(@count($sub_menu_array)){
            foreach ($sub_menu_array as $sub_menu){
                $this->append($sub_menu, $menu_array['id']);
            }
        }
    }

    private function insertArray(&$arr, $idx, $add){

        if($arr==$this->menu){
            $arr_copy = $arr;
        }else{
            $arr_copy = $arr['sub'];

        }

        $arr_front = array_slice($arr_copy, 0, $idx); //처음부터 해당 인덱스까지 자름
        $arr_end = array_slice($arr_copy, $idx); //해당인덱스 부터 마지막까지 자름

        $arr_front[] = $add;//새 값 추가

        $merge_array = array_merge($arr_front, $arr_end);

        if($arr==$this->menu){
            $arr = $merge_array;
        }else{
            $arr['sub'] = $merge_array;

        }


    }

    private function menuReOrder(){

        $re_order_complete = array();
        foreach ($this->arr_parent_ref as &$parent_ref) {
            if (isset($parent_ref['id'])) {
                $parent_id = $parent_ref['id'];
            } else {
                $parent_id = 'root';
            }
            if (in_array($parent_id, $re_order_complete)) {
                continue;
            }

            $re_order_complete[] = $parent_id;
            if(isset($parent_ref['sub'])){
                $parent_array = &$parent_ref['sub'];
            }else{
                $parent_array = &$parent_ref;
            }


            $order = $this->array_column($parent_array, 'order');
            $unique_chk = array_unique($order);


            if(count($unique_chk)<2){
                continue;
            }

            usort($parent_array, function ($item1, $item2) {
                return $item1['order'] < $item2['order'] ? -1 : 1;
            });

        }

    }

    private function makeSkin($skin,$data,$option=array())
    {
        return $this->make_skin($skin,$data,$option);

    }

    private function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }

    private function makeMenuId(){
        $id = "wv-{$this->id_count}";
        if(in_array($id, $this->arr_id)){

            $this->id_count++;
            return $this->makeMenuId();
        }

        return $id;
    }

    public function getMenuArray($property_name){
        if(isset($this->{$property_name})){
            return $this->{$property_name};
        }

        wv_error('존재하지 않는 클래스 속성 : '.$property_name,2);
    }

}
Menu::getInstance();