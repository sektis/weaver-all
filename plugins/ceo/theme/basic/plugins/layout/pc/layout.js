$(document).ready(function () {

    // if (/iP(hone|od|ad)/.test(navigator.userAgent)) {
    //     $(window).on('resize',wv_debounce(wv_p_check,100));
    //     function wv_p_check(){
    //         $("[class*=wv-skin] p").each(function () {
    //             var $this = $(this);
    //             $this[0].style.setProperty('--wv-parent-line-height',$this.css('line-height'));
    //         })
    //     }
    //     $("[class*=wv-skin]").loaded("p",function () {
    //         wv_p_check();
    //     })
    // }

    // 아이폰 확대방지
    if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
        const el = document.querySelector('meta[name=viewport]');

        if (el !== null) {
            let content = el.getAttribute('content');
            let re = /maximum\-scale=[0-9\.]+/g;

            if (re.test(content)) {
                content = content.replace(re, 'maximum-scale=1.0');
            } else {
                content = [content, 'maximum-scale=1.0'].join(', ')
            }

            el.setAttribute('content', content);
        }

    }
})