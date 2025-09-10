<?php
namespace weaver;
/*
 * 게시판 외 특정스킨파일하나만 적용하고싶을 경우 해당 스킨파일이 정의된곳에 get_skin_path 메소드를 사용해 직접 정의
 * ex ) $login_file = wv('gnu_skin')->get_skin_path(G5_IS_MOBILE?'mobile':'pc','member','basic','/login.skin.php');
 * 혹은 해당 스킨파일 상단에 위 파일을 include 후에 return 하기
 */
class GnuSkin extends Plugin {

    private $gnu_skin_use_array = array();
    private $gnu_skin_dir_array = array();
    private $gnu_skin_default_array = array('member','new','search','faq','shop');
    private $symlink_path_array = array();


    protected function __construct() {

        set_error_handler(array($this, 'gnu_skin_include_handler'));
//        add_replace('wv_hook_act_code_index_replace',array($this,'admin_get_skin_func_replace'),-1,1);
        add_replace('wv_hook_act_code_index_replace',array($this,'skin_file_parse'),-1,3);
        $this->skin_config_check();


    }

    /**
     * wv('gnu_skin')->add_symlink($this->plugin_path.'/skin/exam','board','exam');
     */
    public function add_symlink($org_path,$skin_gubun,$skin_dir,$device='pc', $file_name = ''){

        $wv_skin_link = $this->plugin_theme_path;

        if($device){
            $wv_skin_link.='/'.$device;
        }
        if($skin_gubun){
            $wv_skin_link.='/'.$skin_gubun;
        }
        if($skin_dir){
            $wv_skin_link.='/'.$skin_dir;
        }
        if($file_name){
            $wv_skin_link.='/'.$file_name;
        }
        if(!file_exists($org_path)){
            wv_error('스킨을 찾을 수 없습니다. : '.$org_path,2);
        }

        if(file_exists($wv_skin_link)){

            if(is_link($wv_skin_link)){return;}
            else{wv_error('이미 존재하는 스킨 명입니다. : '.$skin_dir,2);}
        }

        if(!symlink($org_path, $wv_skin_link)){
            wv_error('symlink error',2);
        }


    }

    private function skin_config_check(){
        global $config,$default;
        if(!is_array($default))$default=array();
        $gnu_config = array_merge($config,$default);

        foreach ($this->gnu_skin_default_array as $cf_name){
            $prefix = 'cf';
            if($cf_name=='shop'){
                $prefix = 'de';
            }

            $arr = explode('/',$gnu_config["{$prefix}_{$cf_name}_skin"]);
            if($arr[0]=='weaver'){
                $this->set_use_skin($cf_name,'',$arr[1]);
            }

            $arr = explode('/',$gnu_config["{$prefix}_mobile_{$cf_name}_skin"]);
            if($arr[0]=='weaver'){
                $this->set_use_skin($cf_name,'',$arr[1],'mobile');
            }




         }

    }

    public function set_use_skin($dir,$file='',$skin='basic',$device='pc'){

        $file = (array) $file;

        if(count(array_filter($file))==0){
            $this->gnu_skin_use_array["{$dir}_{$device}"] = array();
        }else{
            if(!(isset($this->gnu_skin_use_array["{$dir}_{$device}"]) and count(array_filter($this->gnu_skin_use_array["{$dir}_{$device}"]))==0)){
                $this->gnu_skin_use_array =  array_merge_recursive($this->gnu_skin_use_array,array(
                    "{$dir}_{$device}" => $file
                ));
            }
        }


        for($i=0;$i<count($file);$i++){
            $skin_dir_key = "{$dir}_{$device}";

            if($file[$i]){
                $skin_dir_key.= "_{$file[$i]}";
            }

            $this->gnu_skin_dir_array[$skin_dir_key] = $skin;
        }



    }

    public function skin_check($dir,$file,$skin){

        $device= G5_IS_MOBILE?'mobile':'pc';
        $sch_key = "{$dir}_$device";
        $is_wv_skin = false;
        $sch_arr = array_filter($this->gnu_skin_use_array[$sch_key]);
        $skin_arr = explode('/',$skin);

        if(in_array($file,$sch_arr) or (is_array(count($sch_arr)==0) and count($sch_arr)==0)  ){
            $is_wv_skin = true;
            $sch_dir_key = "{$sch_key}_{$file}";
            if($this->gnu_skin_dir_array[$sch_dir_key]){
                $skin = $this->gnu_skin_dir_array[$sch_dir_key];
            }else{
                $skin = $this->gnu_skin_dir_array[$sch_key];
            }
        }

        if(count($skin_arr)==2 and $skin_arr[0]=='weaver'){
            $is_wv_skin = true;
            $skin = $skin_arr[1];
        }

        if(!$is_wv_skin){
            return false;
        }

       $skin_path = $this->get_skin_path($device,$dir,$skin,"{$file}.skin.php");

       if(file_exists($skin_path)){
           return $skin_path;
       }

        return false;
    }



    public function gnu_skin_include_handler($errno, $errstr, $errfile, $errline){


        $errstr = htmlspecialchars($errstr);

        if($errno!=2 or !preg_match('`No such file or directory`',$errstr))return false;

        preg_match('/((require|include)(_once)?)\(([^)]+)\)/', $errstr, $gnu_skin_matches);

        if (!isset($gnu_skin_matches[4])) return false;

        $org_include_path = $include_path = trim($gnu_skin_matches[4], "'\""); // 작은따옴표 또는 큰따옴표 제거
        $include_path = str_replace(G5_PATH,'',$include_path);
        $include_path = ltrim($include_path,'/');
        $include_path_arr = explode('/',$include_path);

        if($include_path_arr[0]!='skin' or $include_path_arr[2]!='weaver')return false;
        $device= G5_IS_MOBILE?'mobile':'pc';
        $skin_gubun = $include_path_arr[1];
        $skin_dir = $include_path_arr[3];
        $file_name = $include_path_arr[4];

        $skin_path = $this->get_skin_path($device,$skin_gubun,$skin_dir,$file_name);


        $skin_path_var_name = "{$skin_gubun}_skin_path";
        $skin_url_var_name = "{$skin_gubun}_skin_url";

        if(!file_exists($skin_path)) {
            return false;
//            $skin_path   = get_skin_path($skin_gubun, "basic").'/'.$file_name;
        }


        $skin_id = wv_make_skin_id();
        $skin_selector = wv_make_skin_selector($skin_id);

        $bt = debug_backtrace();

        $find_refs = false;
        foreach ($bt[0]['args'] as $key=>$val){
            if(is_array($val)){
                $find_refs = true;
                $temp_skin_id = $val['skin_id'];
                if(isset($val['_GET'])){
                    extract($GLOBALS);
                    extract($val['_GET']);
                    extract($val['_POST']);
                }else{
                    extract($val);
                }
            }
        }

        if($temp_skin_id){
            if (defined($temp_skin_id)) return;
            define($temp_skin_id, TRUE);
        }

        $wv_skin_path = $$skin_path_var_name = dirname($skin_path);
        $wv_skin_url = $$skin_url_var_name = str_replace(G5_PATH, G5_URL, $wv_skin_path);;

        if(!$find_refs){
            wv_add_symlink(dirname($skin_path),dirname($org_include_path));
            return true;
        }

        set_error_handler(array($this, 'gnu_skin_include_handler'));
//        $before_include_vars = get_defined_vars();
        switch ($gnu_skin_matches[1]){
            case 'include':
                include $skin_path;
                break;
            case 'include_once':
                include_once $skin_path;
                break;
            case 'require':
                require $skin_path;
                break;
            case 'require_once':
                require_once $skin_path;
                break;
        }
//        $after_include_vars = get_defined_vars();
//
//        $local_vars = array();
//
//        foreach ($after_include_vars as $key => $value) {
//            if($key=='before_include_vars')continue;
//            if ($before_include_vars[$key] != $value) {
//                $local_vars[$key] = $after_include_vars[$key];
//            }
//        }
//
//        wv_local_var_to_global($local_vars);

        return true;
    }

    public function get_skin_path($device='pc',$skin_gubun='',$skin_dir = '',$file_name = '',$theme_dir=''){

        $wv_skin_path = $this->get_theme_path($theme_dir,$device);


        if($skin_gubun){
            $wv_skin_path.='/'.$skin_gubun;
        }
        if($skin_dir){
            $wv_skin_path.='/'.$skin_dir;
        }
        if($file_name){
            $wv_skin_path.='/'.$file_name;
        }
        if(!file_exists($wv_skin_path)){
            $link_check=false;
            if(is_link($wv_skin_path)){
                $link_check=true;
                unlink($wv_skin_path);
            }
            $dir_name_path = dirname($wv_skin_path);
            if(is_link($dir_name_path)){
                $link_check=true;
                unlink($dir_name_path);
            }
            if(!$link_check and $theme_dir!='basic'){
                return $this->get_skin_path($device,$skin_gubun,$skin_dir,$file_name,'basic');
            }
        }

        return $wv_skin_path;

    }


    public function skin_file_parse($act_code,$org_code,$act_path){


        $pattern = "`((?:\\$)?[a-zA-Z_][a-zA-Z0-9_]*)?(?(1)\s*(?:\.)?\s*)'(?:\/)?([^']*)\.skin\.php'`";
        if(!preg_match($pattern,$act_code)){
            return $act_code;
        }

        $final_code_check = wv_get_final_eval_code($act_code);

        $act_code = $final_code_check['code'];


        $act_code = preg_replace_callback($pattern,function ($matches){

            $return = "wv_get_skin_path(".$matches[0].")";

            return $return;
        },$act_code);

        return $act_code;
    }

    public function admin_get_skin_func_replace($act_code){

        if(!defined('G5_IS_ADMIN'))return $act_code;


        $find_select_func = false;

        $act_code = preg_replace_callback("/get_skin_select\(/isu",function ($matches) use(&$find_select_func){
            $find_select_func = true;
            return 'wv_get_skin_select(';
        },$act_code);

        $act_code = preg_replace_callback("/get_mobile_skin_select\(/isu",function ($matches) use(&$find_select_func){
            $find_select_func = true;
            return 'wv_get_mobile_skin_select(';
        },$act_code);

        $act_code = preg_replace_callback("/get_skin_dir\(/isu",function ($matches) use(&$find_select_func){
            $find_select_func = true;
            return 'wv_get_skin_dir(';
        },$act_code);

        if($find_select_func){
            $wv_adm_common_code = wv_get_eval_code(G5_ADMIN_PATH.'/_common.php');

            preg_match_all("`(.+)common.php['\";\s]+(.+)`isu",$wv_adm_common_code,$adm_common_matches);

            $act_code = preg_replace_callback("/@?(require|include)(_once)?([\.\s('\"]+)(\.\/)?(_common+\.php)([ '\");]+)/imu",function ($matches)use($adm_common_matches){
                return rtrim(trim($adm_common_matches[0][0]),'?>');
            },$act_code);

        }



        return $act_code;
    }

    public function use_social_skin(){
        global $config;
        if( $config['cf_theme'] ){
            $cf_theme = trim($config['cf_theme']);
            $dir = G5_SOCIAL_LOGIN_DIR;

            $theme_path = G5_PATH.'/'.G5_THEME_DIR.'/'.$cf_theme;

            if(!is_dir($theme_path)){
                wv_error('테마 경로를 찾을 수 없습니다. : '.$theme_path,2);
            }
            $social_path = $this->get_skin_path(G5_IS_MOBILE?'mobile':'pc','social');

            if(!is_dir($social_path)){
                wv_error('social 스킨 경로를 찾을 수 없습니다. : '.$social_path,2);
            }

            wv_add_symlink($social_path,$theme_path.'/'.G5_SKIN_DIR.'/'.G5_SOCIAL_LOGIN_DIR);
        }
    }

}
GnuSkin::getInstance();