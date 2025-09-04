<?php
namespace weaver;

class Page extends Plugin {

    private $page_rewrite_dir = 'page';
    private $page_id = '';
    private $page_path = '';
    private $page_index_id = 'main';
    private $page_shop_index_id = 'main_shop';
    private $page_my_page_id_array = array();
    private $page_my_page_file_array = array('login','register_form','register');
    private $page_my_page_bo_table_array = array();
 


    protected function __construct() {

        wv_add_qstr('wv_page_id','wv_page_id');
        add_event('wv_hook_eval_action_before',array($this,'wv_hook_eval_action_before'));
        add_replace('wv_hook_act_code_index_replace',array($this,'wv_hook_act_code_index_replace'),0,1);
        add_replace('false_short_url_clean',array($this, 'page_url_make'),0,4);
    }

    public function set_page_index_id($page_id){
        $this->page_index_id = $page_id;
    }

    public function page_url_make($url){
        $parsed_url = wv_get_org_url($url,true);


        $filename = basename($parsed_url['path']);

        if(!($filename=='index.php' or $filename=='') or !isset($parsed_url['query']['wv_page_id'])){
            return $url;
        }

        $parsed_url['path'] = '/page/'.$parsed_url['query']['wv_page_id'].'/';
        unset($parsed_url['query']['wv_page_id']);

        return wv_build_url(array_filter($parsed_url));
    }

    public function wv_hook_eval_action_before(){
        global $wv_page_id,$bo_table;
        $this->page_id_register();
        $my_page_file_array = $this->page_my_page_file_array;
        $my_page_id_array = $this->page_my_page_id_array;
        if(wv_plugin_exists('menu') and wv('menu')->made('member')){

            $member_menu_paths = wv('menu')->made('member')->getMenuArray('arr_url');

            $member_menu_paths_filtered = array_map(function ($path){

                $file_name = pathinfo($path, PATHINFO_FILENAME);
                if($file_name=='index'){
                    return '';
                }
                if($file_name=='board'){

                    $aa = parse_url($path);
                    parse_str($aa['query'],$bb);
                    if($bb['bo_table']){
                        $this->page_my_page_bo_table_array[]=$bb['bo_table'];
                    }

                    return '';
                }
                return pathinfo($path, PATHINFO_FILENAME);
            },$member_menu_paths);
            $my_page_file_array = array_unique(array_merge($my_page_file_array,array_filter($member_menu_paths_filtered)));


            $member_menu_page_ids = wv('menu')->made('member')->getMenuArray('arr_qstr');
            $member_menu_page_ids_filtered = array_map(function ($qstr){
                if($qstr['wv_page_id']){
                    return $qstr['wv_page_id'];
                }
                return '';
            },array_values($member_menu_page_ids));
            $my_page_id_array = array_unique(array_merge($my_page_id_array,array_filter($member_menu_page_ids_filtered)));

        }
        if(  in_array(wv_info('file'),$my_page_file_array) or (in_array($bo_table,$this->page_my_page_bo_table_array)) or ($wv_page_id and in_array($wv_page_id,$my_page_id_array))){

            wv()->info->location_type_update('mypage');
        }
    }


    public function get_member_menu_array($page_name='마이페이지',$page_id='mypage'){
        global $g5,$member;
        $arr = array('name'=>$page_name,'url'=>'/?wv_page_id='.$page_id,'sub'=>array());

        if($member['mb_id']){
            $arr['sub'][] = array('name'=>'정보변경','url'=>G5_BBS_URL.'/member_confirm.php?url='.G5_BBS_URL.'/register_form.php','icon'=>'<i class="fa-solid fa-key"></i>');
            $arr['sub'][] = array('name'=>'로그아웃','url'=>G5_BBS_URL.'/logout.php','icon'=>'<i class="fa-solid fa-arrow-right-from-bracket"></i>');
        }

        return array($arr);
    }


    private function page_id_register(){
        global $wv_page_id,$rewrite, $bo_table, $wr_seo_title, $wr_id,$board;

        if(wv_info('type')=='index' and $this->page_index_id){
            if($wv_page_id){
                $this->page_id = $wv_page_id;
            }else{
                $this->page_id = $wv_page_id = $this->page_index_id;
            }


        }
        if(wv_info('type')=='shop_index' and $this->page_shop_index_id){
            $this->page_id = $wv_page_id = $this->page_shop_index_id;

        }

        if(in_array(wv_info('type'),array('index','shop_index')) and !in_array($this->page_id,array($this->page_index_id,$this->page_shop_index_id))){

            wv()->info->location_type_update('page');
        }

        if($rewrite==1 and wv_info('dir')=='bbs' and wv_info('file')=='board' and $bo_table==$this->page_rewrite_dir){
            if($board['bo_table']){
                $this->error("{$this->page_rewrite_dir}는 게시판 bo_table로 사용할 수 없습니다.",2);
            }
            wv()->info->location_type_update('page');
            if($wr_seo_title){
                $this->page_id = $wr_seo_title;
            }elseif($wr_id){
                $this->page_id = $wr_id;
            }else{
                $this->page_id = $wv_page_id;
            }
        }


    }

    public function set_page_id($page_id){
        $this->page_id = $page_id;
    }

    private function get_page($device_dir=''){

        if(!$device_dir){
            $device_dir = G5_IS_MOBILE?'mobile':'pc';
        }
        $page_id = str_replace('-','/',$this->page_id);

        $include_path = $this->get_theme_path('',$device_dir).'/'.$page_id.'.php';;

        if(file_exists($include_path)){
            return $include_path;
        }

        if($device_dir=='mobile'){
            return $this->get_page('pc');
        }

        return false;
    }



    public function get_page_path(){
        return $this->page_path;
    }



    public function wv_hook_act_code_index_replace($act_code){
        global $g5,$wv_page_skin_path,$wv_page_skin_url;

        if(!$this->plugin_theme_dir){
            return $act_code;
        }

        if($this->page_id){
            $page_path = $this->get_page();

            if(!$page_path)return $act_code;;

            if(in_array($this->page_id,array($this->page_index_id, $this->page_shop_index_id))){
                define('_INDEX_', true);
            }

            $this->page_path = $page_path;
            $g5['body_script'].= ' wv-page-id="'.$this->page_id.'" ';

            $wv_page_skin_path = dirname($this->page_path);
            $wv_page_skin_url = wv_path_replace_url($wv_page_skin_path);


            return wv_get_eval_code(dirname(__FILE__).'/page_index.php');
        }

    }

}
Page::getInstance();