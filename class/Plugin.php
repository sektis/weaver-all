<?php
namespace weaver;

class Plugin extends Weaver{


    private $made_instances = [];

    public static function getInstance() {

        $class = get_called_class();
        $plugin_name = wv_class_to_plugin_name($class,true);

        if (!isset(parent::$plugins[$plugin_name])) {
            $instance = new $class();

            $instance->plugin_init();
            $instance->config = wv()->configs->get($plugin_name);
            parent::$plugins[$plugin_name] = $instance;
        }

        return parent::$plugins[$plugin_name];
    }

    public function plugin_init($plugin_name='') {
        if($this->plugin_name){
            return;
        }
        if(!$plugin_name){
            $plugin_name = wv_class_to_plugin_name(get_called_class(),true);
        }
        $this->temp_plugin_name = $plugin_name;
        self::$plugins_props->set($plugin_name,'plugin_name',$plugin_name);
        self::$plugins_props->set($plugin_name,'plugin_path',WV_PLUGINS_PATH.'/'.$plugin_name);
        self::$plugins_props->set($plugin_name,'plugin_url',WV_PLUGINS_URL.'/'.$plugin_name);
        self::$plugins_props->set($plugin_name,'plugin_theme_path',$this->get_theme_path('basic'));
        self::$plugins_props->set($plugin_name,'plugin_theme_url',$this->get_theme_url('basic'));
        self::$plugins_props->set($plugin_name,'plugin_theme_dir_once','');
        self::$plugins_props->set($plugin_name,'plugin_theme_dir','basic');
        self::$plugins_props->set($plugin_name,'ajax_url',WV_PLUGINS_URL.'/'.$plugin_name.'/ajax.php');
        $this->theme_injection();
    }



    public function set_theme_dir($theme_dir='basic',$make_skin_once=false){

        if(!is_dir($this->plugin_path.'/theme/'.$theme_dir)){
            $this->error("{$theme_dir}테마를 찾을 수 없습니다.",2);
            return false;
        }

        if($make_skin_once){

            self::$plugins_props->set($this->plugin_name,'plugin_theme_dir_once',$theme_dir);
        }else{
            self::$plugins_props->set($this->plugin_name,'plugin_theme_dir',$theme_dir);
            self::$plugins_props->set($this->plugin_name,'plugin_theme_path',$this->get_theme_path($theme_dir));
            self::$plugins_props->set($this->plugin_name,'plugin_theme_url',$this->get_theme_url($theme_dir));
        }

    }

    public function get_theme_path($theme_dir='',$device=''){

        $path = $this->plugin_path.'/theme';

        $path.='/'.($theme_dir?$theme_dir:$this->plugin_theme_dir);
        if($device and is_dir($device)){
            $path.= '/'.(G5_IS_MOBILE?'mobile':'pc');
        }
        if(is_dir($path.'/pc') or is_dir($path.'/mobile')){
            $path.= '/'.(G5_IS_MOBILE?'mobile':'pc');
        }

        return $path;
    }

    public function get_theme_url($theme_dir=''){
        return wv_path_replace_url($this->get_theme_path($theme_dir));
    }

    protected function make_skin($skin='basic',$data='',$option=''){

        global $g5,$member,$is_member,$config;
        $org_theme='';
        if($data['theme_dir']){
            $org_theme = $this->plugin_theme_dir;
            $this->set_theme_dir($data['theme_dir']);
        }
        $wv_skin_path = $this->get_theme_path($this->plugin_theme_dir_once).'/'.$skin;
        $wv_skin_url = str_replace(G5_PATH, G5_URL, $wv_skin_path);
        $this->plugin_theme_dir_once='';
        $file = $wv_skin_path.'/skin.php';

        if (!file_exists($file)) {
            wv_error($file." 스킨 파일을 찾을 수 없습니다.",2);
        }

        $skin_id = wv_make_skin_id();
        $skin_selector = wv_make_skin_selector($skin_id);
        $skin_class = 'wv-'.$this->plugin_name.'-'.str_replace('/','-',$skin_id);

        ob_start();
//        dd($file);
        include $file;
        $content = ob_get_contents();
        ob_end_clean();
        if($org_theme){
            $this->set_theme_dir($org_theme);
        }
        return $content;
    }

    protected function get_jnjection_themes($only_name=false){
        $theme_path = $this->plugin_theme_path;
        if(in_array(basename($theme_path),array('pc','mobile'))){
            $theme_path = dirname($theme_path);
        }
        $other_plugins_path = $theme_path.'/plugins';
        $other_plugins_themes = glob($other_plugins_path.'/*', GLOB_ONLYDIR);
        if($only_name){
            $other_plugins_themes = array_map('basename',$other_plugins_themes);

        }


        return $other_plugins_themes;
//        foreach ($other_plugins_themes as $path){
//            $other_plugin_name = basename($path);
//            if(!wv_plugin_exists($other_plugin_name)){
//              continue;
//            };
//
//            wv_add_symlink($path,wv($other_plugin_name)->plugin_path.'/theme/'.$this->plugin_name);
//            if($set_theme_dir){
//                wv($other_plugin_name)->set_theme_dir($this->plugin_name);
//            }
//
//        }
    }

    protected function theme_injection(){

        $other_plugins_themes = $this->get_jnjection_themes();

        foreach ($other_plugins_themes as $path){
            $other_plugin_name = basename($path);
            if(!wv_plugin_exists($other_plugin_name)){
                $this->error("theme_injection {$other_plugin_name} 플러그인이 존재하지 않습니다.",2);
            };

            wv_add_symlink($path,wv($other_plugin_name)->plugin_path.'/theme/'.$this->plugin_name);

        }
    }

    protected function theme_injection_use($plugin=''){

        $other_plugins_themes = $this->get_jnjection_themes(1);
//        dd(array_map('basename',$other_plugins_themes));
        if($plugin and isset($other_plugins_themes[$plugin])){
            wv($plugin)->set_theme_dir($this->plugin_name);
            return;
        }
        foreach ($other_plugins_themes as $other_plugin_name){

            wv($other_plugin_name)->set_theme_dir($this->plugin_name);

        }
    }

    protected function get_instance($id=''){

        if($id!==''){
            if(!isset($this->made_instances[$id])){
                return false;
            }
            return $this->made_instances[$id];
        }
        return $this->made_instances;
    }

    protected function add_instance($id,$array){
//        $this->made_instances = array_merge_recursive($this->made_instances,array($id=>$array));
        $this->made_instances[$id] = $array;
    }

    final public function __get($name) {

        if (method_exists($this, '_custom_get')){
            $val = $this->_custom_get($name);
            if ($val !== null) return $val;
        }

        if($this->temp_plugin_name){
            $plugin_name = $this->temp_plugin_name;
        }else{
            $class = get_called_class();
            $plugin_name = wv_class_to_plugin_name($class,true);
        }


        $get_name = strtolower($name);

        if($get_name=='config'){
            return wv()->configs->get($plugin_name);
        }

        return self::$plugins_props->$plugin_name->$get_name;
    }

}