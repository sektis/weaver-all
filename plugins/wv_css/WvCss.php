<?php
namespace weaver;

class WvCss extends Plugin{

    public function __construct() {
        add_event('tail_sub',array($this,'add_event_tail_sub'),0);
        add_replace('html_process_buffer',array($this,'body_add_class'),0,1);

    }

    public function add_event_tail_sub(){

        add_stylesheet('<link rel="stylesheet" href="'.$this->plugin_url.'/wv_tailwind.css'.'?ver='.G5_CSS_VER.'">');
        add_stylesheet('<link rel="stylesheet" href="'.$this->plugin_url.'/wv_responsive.css'.'?ver='.G5_CSS_VER.'">');
    }

    public function body_add_class($buffer){
        $pattern = '/<body([^>]*)>/i';

        // 기존 class 속성이 있는 경우 처리
        if (preg_match('/<body[^>]*\sclass=["\']([^"\']*)["\']/i', $buffer)) {
            $replacement = '<body\1 class="\2 wv-wrap">';
        } else {
            $replacement = '<body\1 class="wv-wrap">';
        }

        return preg_replace($pattern, $replacement, $buffer);

    }

}
WvCss::getInstance();