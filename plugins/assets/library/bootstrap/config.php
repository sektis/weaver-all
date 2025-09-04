<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
add_event('common_header','wv_assets_plugin_bootstrap_add_meta',0);

function wv_assets_plugin_bootstrap_add_meta()  {
    if(G5_IS_MOBILE)return;
    wv_add_config_meta('<meta name="viewport" content="width=device-width, initial-scale=1">');
}

add_event('wv_hook_assets_before_add_assets','wv_assets_plugin_bootstrap');

function wv_assets_plugin_bootstrap()  {
    unset(wv('assets')->css['bootstrap_bootstrap']);
}