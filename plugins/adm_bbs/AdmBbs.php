<?php
namespace weaver;

class AdmBbs extends Plugin {

    private $adm_bbs_file = '';
    private $adm_bbs_file_path = '';
    private $ignore_bbs_file = array('login.php','login_check.php');


    protected function __construct() {
        global $adm_bbs_file,$url;
        if($adm_bbs_file and in_array($adm_bbs_file,$this->ignore_bbs_file)){
            $request_uri = str_replace('adm%2Fadm%2F','adm%2F',$_SERVER['REQUEST_URI']);
            header('Location: '.G5_ORG_URL.'/'.ltrim($request_uri,'/adm'));
        }
        $this->adm_bbs_file = $adm_bbs_file;
        if(defined('G5_ORG_PATH')){
            $this->adm_bbs_file_path = G5_ORG_PATH.'/bbs/'.$this->adm_bbs_file;
        }

        add_replace('add_mod_rewrite_rules', array($this, 'add_page_rules'), 14,1);
        add_replace('wv_hook_act_code_index_replace',array($this,'wv_hook_act_code_index_replace'));
        add_replace('wv_hook_act_code_layout_replace',array($this,'wv_hook_act_code_layout_replace'),-1,3);
        add_event('common_header',array($this,'common_header'),-1);
        add_replace('admin_menu', array($this, 'add_admin_menu'), 1, 1);
        add_replace('html_process_buffer',array($this,'adm_link_replace'),0,1);
    }

    public function adm_link_replace($buffer){

        $buffer =preg_replace_callback(
            '/<a\s+([^>]*?)\s*href=["\']([^"\']*?)\/adm\/([^"\']*?)["\']([^>]*?)>/i',
            function ($matches) {
                // class="tnb_community"가 포함된 경우만 수정
                if (preg_match('/\bclass=["\'][^"\']*\btnb_community\b[^"\']*["\']/i', $matches[1] . $matches[4])) {
                    return "<a {$matches[1]} href=\"{$matches[2]}/{$matches[3]}\"{$matches[4]}>";
                }
                return $matches[0]; // 수정하지 않고 원본 반환
            },
            $buffer
        );
        return $buffer;
    }

    public function add_admin_menu($admin_menu){
        global $adm_bbs_file,$bo_table,$sub_menu;
        if($adm_bbs_file){
            $sub_menu_key = admin_menu_find_by($bo_table, 'sub_menu');
            if($sub_menu_key){
                $sub_menu = $sub_menu_key;
            }
        }
    }

    public function add_page_rules($rules){
        $add_rules = array();
        $add_rules[] = 'RewriteRule ^adm/bbs/(.+\.php)$ '.str_replace(G5_PATH,'',dirname(__FILE__).'/adm_bbs_index.php').'?adm_bbs_file=$1  [QSA,L]';
        $add_rules[] = 'RewriteRule ^adm/(.+)/(.+)$ $1/$2  [QSA,L]';
        return implode("\n", $add_rules).($rules?"\n":"").$rules;
    }

    public function common_header(){
        global $adm_bbs_file,$token;
        if($adm_bbs_file=='delete.php'){
            set_session('ss_delete_token', $token = uniqid(time()));
        }


    }

    public function wv_hook_act_code_index_replace(){
        global $g5;

        if(!$this->adm_bbs_file) return;
        return wv_get_eval_code($this->adm_bbs_file_path);


    }

    public function wv_hook_act_code_layout_replace($act_code,$org_code,$act_path){
        global $g5,$bo_table,$config;
        if(!$this->adm_bbs_file) return;

        $config['cf_bbs_rewrite'] = 0;

        $act_path = $this->adm_bbs_file_path;
        if($bo_table){
            $_REQUEST['token'] = get_session('ss_write_'.$bo_table.'_token');
        }


        $wv_adm_common_code = wv_get_eval_code(G5_ORG_PATH.'/adm/_common.php');

        preg_match_all("`(.+)common.php['\";\s]+(.+)`isu",$wv_adm_common_code,$adm_common_matches);

        $act_code = preg_replace_callback("/@?(require|include)(_once)?([\.\s('\"]+)(\.\/)?(_common+\.php)([ '\");]+)/imu",function ($matches)use($act_path,$adm_common_matches){

            return $adm_common_matches[2][0];
        },$act_code);


        $act_code = wv_replace_include_rel_path($act_code, $act_path);


        // head check
        $act_code = preg_replace_callback("/(({)+([^{}]+)?)?((require|include)(_once)?[^\n\r]+([\.\s\/]+)?\/(board_|shop\.|_)?(head)\.php[\s');]+)(([^{}]+)?(})+)?/isu", function ($matches) {

            $return = $matches[0];

            $replace = "require_once('" . G5_ADMIN_PATH . "/admin.head.php');";

            $return = preg_replace("/(require|include)(_once)?[^\n\r]+([\.\s]+)?\/(board_|shop\.|_)?head\.php[ ');]+/imu", $replace, $return);
            $return = trim($return, "{}\n\r");
            $return = "{" . $return . "}";
            return $return;
        }, $act_code);

        // tail check
        $act_code = preg_replace_callback("/(({)+([^{}]+)?)?((require|include)(_once)?[^\n\r]+([\.\s\/]+)?\/(board_|shop\.|_)?(tail)\.php[\s');]+)(([^{}]+)?(})+)?/isu", function ($matches) {

            $return = $matches[0];
            $replace = "require_once('" . G5_ADMIN_PATH . "/admin.tail.php');";
            $return = preg_replace("/(require|include)(_once)?[^\n\r]+([\.\s]+)?\/(board_|shop\.|_)?tail\.php[ ');]+/imu", $replace, $return);
            $return = trim($return, "{}\n\r");
            $return = "{" . $return . "}";
            return $return;
        }, $act_code);



        set_include_path(dirname($act_path));

        // __FILE__ 교체
        $act_code = preg_replace("/(__FILE__)/imu", "'{$act_path}'", $act_code);
        $act_code = rtrim(trim($act_code), '<?php');

        return $act_code;

    }



}
AdmBbs::getInstance();