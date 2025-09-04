<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

add_event('tail_sub','wv_assets_plugin_gsap');
function wv_assets_plugin_gsap(){


    add_javascript('<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>');
    add_javascript('<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js"></script>');
    add_javascript('<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollToPlugin.min.js"></script>');
    add_javascript('<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/TextPlugin.min.js"></script>');
    add_javascript('<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/Observer.min.js"></script>');
}


