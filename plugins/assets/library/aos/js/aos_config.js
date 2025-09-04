// aos.js
$(document).ready(function () {
    window.aos_option = {

        // Global settings:
        disable: false,
        startEvent: 'DOMContentLoaded',
        initClassName: 'aos-init',
        animatedClassName: 'animate__animated',
        useClassNames: true,
        disableMutationObserver: false,
        debounceDelay: 50,
        throttleDelay: 99,
        offset: 120,
        duration: 800,
        easing: 'ease',
        once: true,
        mirror: false, // 스크롤 내려갔을때 에니메이션 초기화
        anchorPlacement: 'top-bottom',

    };
    AOS.init(aos_option);
})