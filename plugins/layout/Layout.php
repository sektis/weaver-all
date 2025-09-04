<?php
namespace weaver;

class Layout extends Plugin {

    private $layout_skin = '';

    private $use_header_footer = true;
    private $must_add_site_wrapper = true;
    private $layout_id = 'common';

    private $layout_path = '';
    private $layout_head_path = '';
    private $layout_tail_path = '';

    private $layout_org_head_code = '';
    private $layout_org_tail_code = '';

    private $layout_type_file_rel = array(
        'board'=>'bo_table',
        'content'=>'co_id',
        'page'=>'wv_page_id'
    );



    protected function __construct() {

        add_replace('wv_index_file',array($this,'other_builder_check'),0,1);
        add_replace('wv_hook_act_code_layout_replace',array($this,'wv_hook_act_code_layout_replace'),0,3);

    }

    public function other_builder_check($server_script){
        global $na_extend_file;

        if($na_extend_file){
            return $na_extend_file;
        }
        return $server_script;
    }

    public function set_use_header_footer($bool){

        $this->use_header_footer = $bool;
    }

    public function set_must_add_site_wrapper($bool){

        $this->must_add_site_wrapper = $bool;
    }

    public function set_layout_id($layout_id){
        $this->layout_id = $layout_id;
    }

    private function get_layout($type='',$device_dir=''){

        if(!$device_dir){
            $device_dir = G5_IS_MOBILE?'mobile':'pc';
        }
        $filename = $type?$type:$this->layout_id;
        $filename = str_replace('-','/',$filename);

        $include_path = $this->get_theme_path('',$device_dir).'/'.$filename.'.php';;

        if(file_exists($include_path)){
            return $include_path;
        }

        if($device_dir=='mobile'){
            return $this->get_layout($type,'pc');
        }

        return false;
    }


    public function image($file){
        return wv_path_replace_url(dirname(__FILE__).'/img/'.$file);
    }


    public function get_layout_path(){
        return $this->layout_path;
    }

    public function get_layout_head_path(){
        return $this->layout_head_path;
    }
    public function get_layout_tail_path(){
        return $this->layout_tail_path;
    }

    public function get_org_head_code(){
        return $this->layout_org_head_code;
    }
    public function get_org_tail_code(){
        return $this->layout_org_tail_code;
    }



    public function wv_hook_act_code_layout_replace($act_code,$org_code,$act_path){
        global $g5,$wv_layout_skin_path,$wv_layout_skin_url,$member;

        $act_code_save = $act_code;

//        if(wv_is_ajax())return $act_code;
//        if(wv_info('type')=='plugin')return $act_code;
        if(!$this->plugin_theme_dir)return $act_code;
        $layout_file_check = '';


        if( isset($this->layout_type_file_rel[wv_info('type')])){
            $global_var_name = $this->layout_type_file_rel[wv_info('type')];
            global $$global_var_name;
            $check_layout_file = wv_info('type').'-'.$$global_var_name;

            $layout_file_check =  $this->get_layout($check_layout_file);

            if($layout_file_check){

                $this->layout_id = $check_layout_file;
            }
        }


        if(!$layout_file_check){
            $layout_file_check =  $this->get_layout(wv_info('type'));

            if($layout_file_check){

                $this->layout_id = wv_info('type');
            }
        }

        if(!$layout_file_check and wv_info('dir')){
            $layout_file_check =  $this->get_layout(wv_info('dir'));

            if($layout_file_check){

                $this->layout_id = wv_info('dir');
            }
        }




        if($this->layout_id or $this->use_header_footer){

            if($this->use_header_footer){

                $this->layout_head_path = $this->get_layout('head');
                $this->layout_tail_path = $this->get_layout('tail');

                if(!$this->layout_head_path or !$this->layout_tail_path){
                    return $act_code;
                }
            }

            if($this->layout_id){

                $this->layout_path = $this->get_layout();
                if(!$this->layout_path){
                    $this->error("{$this->plugin_theme_dir}에 {$this->layout_id} 파일이 없습니다. ",2);
                    return $act_code;
                }
            }


            $wv_layout_skin_path = dirname($this->layout_head_path);
            $wv_layout_skin_url = wv_path_replace_url($wv_layout_skin_path);

            if(wv_info('type')!='admin' and !wv_is_ajax()){
                add_stylesheet('<link rel="stylesheet" href="'.$wv_layout_skin_url.'/layout.css?ver='.G5_CSS_VER.'">', 99);
                add_javascript('<script src="'.$wv_layout_skin_url.'/layout.js?ver='.G5_JS_VER.'"></script>',99);
            }

            if($act_code==$org_code){
                $final_code_check = wv_get_final_eval_code($act_code);

                $act_code = $final_code_check['code'];

                if($final_code_check['path']){
                    $act_path = $final_code_check['path'];

                }
            }


            // include 변수 체크
            $act_code = wv_replace_include_var_path($act_code);

            // include 상대경로 체크
            $act_code = wv_replace_include_rel_path($act_code,$act_path);



            $header_check = false;
            // head check
            $act_code = preg_replace_callback("/(({)+([^{}]+)?)?((require|include)(_once)?[^\n\r]+([\.\s\/]+)?\/(board_|shop\.|_)?(head)\.php[\s');]+)(([^{}]+)?(})+)?/isu",function ($matches) use(&$header_check){
                $header_check =true;
                $this->layout_org_head_code.=$matches[0];
                $return = $matches[0];
                $replace='';
                $replace.= "include_once('".dirname(__FILE__)."/layout_extend.php');";
                $replace.= $this->use_header_footer?"include_once('".dirname(__FILE__)."/layout_head.php');":"$0";
                $replace.= 'echo $wv_layout_head_extend;';
                $return = preg_replace("/(require|include)(_once)?[^\n\r]+([\.\s]+)?\/(board_|shop\.|_)?head\.php[ ');]+/imu",$replace,$return);
                $return = trim($return,"{}\n\r");
                $return = "{".$return."}";
                return $return;
            },$act_code);


            if($header_check==false){
                if(wv_info('type')!='admin' and $this->must_add_site_wrapper){
                    add_replace('html_process_buffer',array($this,'body_add_class'),0,1);
                }

                return $act_code_save;
            }


            // tail check
            $act_code = preg_replace_callback("/(({)+([^{}]+)?)?((require|include)(_once)?[^\n\r]+([\.\s\/]+)?\/(board_|shop\.|_)?(tail)\.php[\s');]+)(([^{}]+)?(})+)?/isu",function ($matches){
                $this->layout_org_tail_code.=$matches[0];
                $return = $matches[0];
                $replace='';
                $replace.= "include_once('".dirname(__FILE__)."/layout_extend.php');";
                $replace.= 'echo $wv_layout_tail_extend;';
                $replace.= $this->use_header_footer?"include_once('".dirname(__FILE__)."/layout_tail.php');":"$0";
                $return = preg_replace("/(require|include)(_once)?[^\n\r]+([\.\s]+)?\/(board_|shop\.|_)?tail\.php[ ');]+/imu",$replace,$return);
                $return = trim($return,"{}\n\r");
                $return = "{".$return."}";
                return $return;
            },$act_code);

            if($act_code==$org_code){
                return $org_code;
            }


            $g5['body_script'].= ' wv-layout-id="'.$this->layout_id.'" ';
            if($member['mb_id']){
                $g5['body_script'].= ' wv-login ';
            }

            set_include_path(dirname($act_path));

            // __FILE__ 교체
            $act_code = preg_replace("/(__FILE__)/imu","'{$act_path}'",$act_code);
            $act_code = rtrim(trim($act_code),'<?php');


            return $act_code;
        }

    }

    public function body_add_class($html){

        return preg_replace_callback('/<body\b([^>]*)>(.*?)<\/body>/is', function($m) {
            $body_attrs = $m[1]; // body의 속성들 (공백 포함)
            $inner      = $m[2]; // body 안쪽 전체

            // ---- 동작 모드 선택 ----
            // A모드: "body의 첫 요소가 #site-wrapper면 OK, 아니면 랩핑" (중첩에 이미 존재해도 새로 랩핑)
            // B모드: "문서 내에 #site-wrapper가 하나라도 있으면 그대로 두고, 아예 없을 때만 랩핑"
            // 필요에 맞게 $MODE만 바꿔 쓰세요.
            $MODE = 'B'; // 'A' 또는 'B'

            // A모드: 첫 요소가 #site-wrapper인지 확인 (주석/공백은 무시)
            $is_first_site_wrapper = (bool) preg_match(
                '/^\s*(?:<!--.*?-->\s*)*<div\b[^>]*\bid\s*=\s*([\'"])site-wrapper\1[^>]*>/is',
                $inner
            );

            // B모드: body 안 어딘가에 #site-wrapper가 이미 있는지 (중복 랩핑 방지용)
            $exists_anywhere = (bool) preg_match(
                '/<div\b[^>]*\bid\s*=\s*([\'"])site-wrapper\1[^>]*>/i',
                $inner
            );

            $need_wrap = false;

            if ($MODE === 'A') {
                // 첫 요소가 아니라면 랩핑
                $need_wrap = !$is_first_site_wrapper;
            } else { // $MODE === 'B'
                // 문서 내에 아예 없을 때만 랩핑
                $need_wrap = !$exists_anywhere;
            }

            if ($need_wrap) {
                // body의 기존 내용을 통째로 랩핑
                $inner = "\n<div id=\"site-wrapper\">\n" . $inner . "\n</div>\n";
            }

            return '<body' . $body_attrs . '>' . $inner . '</body>';
        }, $html);
    }
}
Layout::getInstance();