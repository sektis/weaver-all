<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


wv()->load(array('wv_css','theme','adm_bbs','location'));
wv('assets')->add_library(array('weaver','weaver_ajax','weaver_bf_file','bootstrap','hc_sticky','font_awesome','swiper11','animate_css'));
wv('assets')->add_font('pretendard');
wv('layout')->set_theme_dir('basic');
wv('layout')->set_use_header_footer(true);
wv('layout')->set_must_add_site_wrapper(true);
wv('page')->set_theme_dir('basic');
wv('menu')->set_theme_dir('basic');
wv('widget')->set_theme_dir('basic');
wv('gnu_skin')->set_theme_dir('basic');
wv('gnu_skin')->set_use_skin('member',array('login','register','register_form','register_result','password_lost'));
wv('gnu_skin')->use_social_skin();
wv()->load(array('ceo','gnu_adm'));


$wv_main_menu_array = array(
    array('name' => '홈', 'url' => '/','icon'=>WV_URL.'/img/foot_1.png'),
    array('name' => '<span class="text-[#FF5F5A]">DUM</span> 매장', 'url' => '/?wv_page_id=0101','icon'=>WV_URL.'/img/foot_2.png'),
    array('name' => '<span class="text-[#FF774D]">DUM</span> 포장', 'url' => '/?wv_page_id=0102','icon'=>WV_URL.'/img/foot_3.png'),
    array('name' => '현금이벤트', 'url' => '/?wv_page_id=0103','icon'=>WV_URL.'/img/foot_4.png'),
    array('name' => '마이페이지', 'url' => '/?wv_page_id=0104','icon'=>WV_URL.'/img/foot_5.png'),

);
wv()->store_manager->make('sub01_01','sub01_01',array('biz','store','location','menu','dayoffs','tempdayoffs'));
wv()->store_manager->make('wv_cont_manager','wv_cont_manager',array('contractmanager'))->prune_columns();
wv()->store_manager->made('sub01_01')->rsync_mapping('sub01_01');
//dd(wv()->store_manager->make('','restaurant')->bind_schema('location')->location->render_part('address_name','view'));

wv('menu')->make('fixed_bottom')->setMenu($wv_main_menu_array);


