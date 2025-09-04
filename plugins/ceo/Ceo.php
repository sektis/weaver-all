<?php
namespace weaver;

class Ceo extends Plugin {

    protected $dir_var = 'ceo';

    public function __construct() {
         global $wv_dir_var;
         $this->plugin_init();

         wv_dir_var_pre_check('ceo');
         if($wv_dir_var==$this->dir_var){
             wv_must_login();
             wv_never_register();
             $this->theme_injection(1);

             add_event('wv_hook_eval_action_before',array($this,'wv_hook_eval_action_before'));

         }
    }


    public function wv_hook_eval_action_before(){

        $wv_main_menu_array = array(
            array('name' => '홈', 'url' => '/ceo','icon'=>WV_URL.'/img/foot_1.png'),
            array('name' => '매장관리', 'url' => '/?wv_page_id=0101','icon'=>WV_URL.'/img/foot_6.png'),
            array('name' => '서비스관리', 'url' => '/?wv_page_id=0101','icon'=>WV_URL.'/img/foot_7.png'), 
            array('name' => 'MY 계정', 'url' => '/?wv_page_id=0101','icon'=>WV_URL.'/img/foot_8.png'),

        );

        wv('menu')->made('fixed_bottom')->setMenu($wv_main_menu_array,true);
    }

}
Ceo::getInstance();