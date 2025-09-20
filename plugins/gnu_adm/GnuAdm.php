<?php
namespace weaver;

class GnuAdm extends Plugin {

    protected $dir_var = 'admin';

    public function __construct() {
         global $wv_dir_var,$wv_page_id;

         wv_dir_var_pre_check($this->dir_var);

         if($wv_dir_var==$this->dir_var){


             wv_must_login();
             wv_never_register();
             add_event('wv_hook_eval_action_before',array($this,'wv_hook_eval_action_before'),-1);

         }
    }

    public function wv_hook_eval_action_before(){

        $wv_main_menu_array = array(
            array('name' => '고객관리', 'url' => '/','sub'=>array(
                array('name' => '회원 관리', 'url' => '/?wv_page_id=0101','icon'=>wv()->gnu_adm->plugin_url.'/img/menu1.png','sub'=>array(
                    array('name' => '사장님 관리', 'url' => '/?wv_page_id=0101','sub'=>array(
                        array('name' => '사장님 관리', 'url' => '/?wv_page_id=0101_c'),
                    )),
                    array('name' => '일반 사용자 관리', 'url' => '/?wv_page_id=0102','sub'=>array(
                        array('name' => '일반 사용자 관리', 'url' => '/?wv_page_id=0102_c'),
                    )),
//                    array('name' => '계정 활동 로그', 'url' => '/?wv_page_id=0103'),
                )),
                array('name' => '매장관리', 'url' => '/?wv_page_id=0201','icon'=>wv()->gnu_adm->plugin_url.'/img/menu2.png','sub'=>array(
                    array('name' => '매장 관리', 'url' => '/?wv_page_id=0201','sub'=>array(
                        array('name' => '매장 관리', 'url' => '/?wv_page_id=0201_c'),
                    )),
                    array('name' => '서비스 관리', 'url' => '/?wv_page_id=0202'),
                    array('name' => '관리 활동 내역', 'url' => '/?wv_page_id=0203'),
                )),
                array('name' => '이벤트 관리', 'url' => '/?wv_page_id=0301','icon'=>wv()->gnu_adm->plugin_url.'/img/menu3.png','sub'=>array(
                    array('name' => '친구초대  관리', 'url' => '/?wv_page_id=0301'),
                    array('name' => '매장방문 인증 관리', 'url' => '/?wv_page_id=0302'),
                    array('name' => '적립금 및 참여 통합 이력', 'url' => '/?wv_page_id=0303'),
                    array('name' => '출금 신청 관리', 'url' => '/?wv_page_id=0304'),
                )),
                array('name' => '계약상품 관리', 'url' => '/?wv_page_id=0401','icon'=>wv()->gnu_adm->plugin_url.'/img/menu4.png','sub'=>array(
                    array('name' => '계약상품 현황', 'url' => '/?wv_page_id=0401'),
                )),
                array('name' => '고객센터', 'url' => '/?wv_page_id=0501','icon'=>wv()->gnu_adm->plugin_url.'/img/menu5.png', ),
            )),
            array('name' => '사이트 설정 / 운영관리', 'url' => '/?wv_page_id=0601','sub'=>array(
                array('name' => '계약 및 상품 설정', 'url' => '/?wv_page_id=0601','icon'=>wv()->gnu_adm->plugin_url.'/img/menu6.png'),
                array('name' => '이미지/콘텐츠 관리', 'url' => '/?wv_page_id=0701','icon'=>wv()->gnu_adm->plugin_url.'/img/menu7.png'),
                array('name' => '운영 도구 설정', 'url' => '/?wv_page_id=0801','icon'=>wv()->gnu_adm->plugin_url.'/img/menu8.png','sub'=>array(
                    array('name' => '관리자 계정 관리', 'url' => '/?wv_page_id=0801'),
                    array('name' => '엑셀 항목 설정', 'url' => '/?wv_page_id=0802'),
                )),
            )),

        );
        wv()->page->set_page_index_id('0101');

        wv('menu')->make('left_menu')->setMenu($wv_main_menu_array,true);
        $this->injection_theme_use();
    }




}
GnuAdm::getInstance();