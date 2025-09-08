<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


if(wv_is_ajax() ){
    global $skin_id,$skin_selector;
    $skin_id = wv_make_skin_id();
    $skin_selector = wv_make_skin_selector($skin_id);
    add_event('alert','wv_assets_plugin_weaver_ajax',0,4);

    global $no_layout;


    if($no_layout){
        add_replace('wv_hook_act_code_layout_replace','wv_assets_plugin_weaver_ajax_no_layout',-1,3);
    }

    if(  class_exists('html_process')){
        ob_start();
        register_shutdown_function(function (){

            class HtmlProcExtend extends html_process{
                public function get_js(){
                    if(method_exists($this,'getInstance')){
                        $parent = self::getInstance();
                        return $parent::$js;
                    }else{
                        return $this->js;
                    }


                }
                public function get_css(){
                    if(method_exists($this,'getInstance')){
                        $parent = self::getInstance();
                        return $parent::$css;
                    }else{
                        return $this->css;
                    }


                }
            }
            $test = new HtmlProcExtend();
            $GLOBALS['wv_ajax_cont'] = ob_get_clean();
            global $html_process,$wv_ajax_assets;


            if($GLOBALS['wv_ajax_cont'] and !wv_is_json($GLOBALS['wv_ajax_cont'])){

                if(is_object($html_process)){
                    $reflectionClass = new ReflectionClass($html_process);
                    $property_js = $reflectionClass->getProperty('js');
                    $property_js->setAccessible(true);
                    $js = $property_js->getValue($html_process);

                    $property_css = $reflectionClass->getProperty('css');
                    $property_css->setAccessible(true);
                    $css = $property_css->getValue($html_process);

                }else{
                    $js= $test->get_js();
                    $css = $test->get_css();

                }
                foreach ($js as $key=>$val){
                    $wv_ajax_assets.=$val[1].PHP_EOL;
                };
                foreach ($css as $key=>$val){
                    $wv_ajax_assets.=$val[1].PHP_EOL;
                };

            }


        });
        register_shutdown_function(function (){
            global $wv_ajax_add_resource,$wv_ajax_cont,$wv_ajax_assets;
            if($wv_ajax_add_resource!==false){
                echo $wv_ajax_assets;
            }
            echo $wv_ajax_cont;

        });
    }



}
function wv_assets_plugin_weaver_ajax_no_layout($act_code,$org_code,$act_path){

    $act_code = preg_replace_callback("/(({)+([^{}]+)?)?((require|include)(_once)?[^\n\r]+([\.\s\/]+)?\/(board_|shop\.|_)?(head)(\.sub)?\.php[\s');]+)(([^{}]+)?(})+)?/isu",function ($matches)  {
        return '';
    },$act_code);

    $act_code = preg_replace_callback("/(({)+([^{}]+)?)?((require|include)(_once)?[^\n\r]+([\.\s\/]+)?\/(board_|shop\.|_)?(tail)(\.sub)?\.php[\s');]+)(([^{}]+)?(})+)?/isu",function ($matches){

        return '';
    },$act_code);

    return $act_code;
}
function wv_assets_plugin_weaver_ajax($msg, $url, $error, $post){


    $is_json_request = false;

    $msg = (string) $msg;

    if(strpos($_SERVER['HTTP_ACCEPT'],'application/json')!==false){
        $is_json_request = true;
    }

    if($error){
        global $wv_ajax_add_resource;
        $wv_ajax_add_resource=false;
        $msg = str_replace('\\n', "\n", $msg);
        wv_abort(400,$msg,$url);
    }

    if($is_json_request){
        wv_json_exit(array('result'=>true,'content'=>$msg));
    }else{
        wv_abort(200,$msg,$url);
    }
}


