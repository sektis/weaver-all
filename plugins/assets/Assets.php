<?php
namespace weaver;
/*
font weight 별 명칭
100	Thin (Hairline)
200	Extra Light (Ultra Light)
300	Light
400	Normal (Regular)
500	Medium
600	Semi Bold (Demi Bold)
700	Bold
800	Extra Bold (Ultra Bold)
900	Black (Heavy)
950	Extra Black (Ultra Black)
 */

class Assets extends Plugin {

    public $js = array();
    public $css = array();
    public $link = array();
    public $script = array();


    protected function __construct() {
        add_event('tail_sub',array($this,'add_event_tail_sub'),0);
    }



    public function add_all_library($ignore = ''){
        $this->add_all('library',$ignore);
    }

    public function add_all_font($ignore = ''){
        $this->add_all('font',$ignore);
    }

    private function add_all($dir,$ignore = ''){
        $ignore = (array) $ignore;
        $vendor_array = wv_glob(dirname(__FILE__).'/'.$dir.'/','*',false);

        foreach ($vendor_array as $vendor){
            $vendor_name = basename($vendor);
            if(in_array($vendor_name,$ignore))continue;
            $this->vendor_load($vendor_name,$dir);
        }
    }



    public function add_library($vendor_array){
        $this->add($vendor_array,'library');
    }

    public function add_font($vendor_array){
        $this->add($vendor_array,'font');
    }

    private function add($vendor_array,$dir){
        $vendor_array = (array) $vendor_array;
        foreach ($vendor_array as $vendor){
            $this->vendor_load($vendor,$dir);
        }
    }

    private function vendor_load($vendor,$dir){

        $asset_include_path = dirname(__FILE__).'/'.$dir.'/'.$vendor;
        $asset_config_path = $asset_include_path.'/config.php';

        if(!is_dir($asset_include_path)){
            $this->error("{$vendor} {$dir} not found",2);
        }

        if(file_exists($asset_config_path)){

            include_once $asset_config_path;
        }

        $find_files = wv_glob($asset_include_path,'*.{css,js}');

        if(!count($find_files))return;

        foreach ($find_files as $file){
            $file_info = pathinfo($file);

            $file_name = $file_info['filename'];
            $file_name_remove_min = rtrim($file_name,'.min');
            $file_ext = $file_info['extension'];
            $key_name = $vendor.'_'.$file_name_remove_min;

            if(@isset($this->$file_ext[$key_name]) and $file_name==$file_name_remove_min)continue;

            if($file_ext=='css'){
                $this->css[$key_name]  = $file;
            }else if($file_ext=='js'){
                $this->js[$key_name]  = $file;
            }

        }


    }

    public function add_event_tail_sub(){

        run_event('wv_hook_assets_before_add_assets');

        foreach ($this->js as $js){
            add_javascript('<script src="'.wv_path_replace_url($js).'?ver='.G5_JS_VER.'"></script>');
        }
        foreach ($this->css as $css){

            add_stylesheet('<link rel="stylesheet" href="'.wv_path_replace_url($css).'?ver='.G5_CSS_VER.'">');
        }

    }
}
Assets::getInstance();