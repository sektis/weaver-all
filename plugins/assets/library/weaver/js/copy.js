/**
 * 범용 클립보드 복사 함수 (하이브리드 앱 + 웹브라우저 지원)
 *
 * @param {string} text - 복사할 텍스트
 * @param {object} options - 옵션 설정
 * @param {function} options.success_callback - 성공시 콜백
 * @param {function} options.error_callback - 실패시 콜백
 * @param {boolean} options.show_manual_on_fail - 실패시 수동복사 모달 표시 여부 (기본: true)
 * @param {boolean} options.show_alert - alert 메시지 표시 여부 (기본: true)
 * @param {string} options.success_message - 성공 메시지 (기본: '복사되었습니다.')
 * @param {string} options.error_message - 실패 메시지 (기본: '복사에 실패했습니다.')
 */
function wv_copy_to_clipboard(text, options) {
    // 기본 옵션 설정
    options = options || {};
    var success_callback = options.success_callback || null;
    var error_callback = options.error_callback || null;
    var show_manual_on_fail = options.show_manual_on_fail !== false;
    var show_alert = options.show_alert !== false;
    var success_message = options.success_message || '복사되었습니다.';
    var error_message = options.error_message || '복사에 실패했습니다.';

    console.log('wv_copy_to_clipboard 시작:', text);

    // 1. 하이브리드 앱 네이티브 브릿지 시도
    if (typeof window.webkit !== 'undefined' && window.webkit.messageHandlers && window.webkit.messageHandlers.copyToClipboard) {
        console.log('iOS 하이브리드 앱 복사 사용');
        window.webkit.messageHandlers.copyToClipboard.postMessage(text);
        wv_copy_success_handler(success_message, success_callback, show_alert);
        return;
    }

    if (typeof window.Android !== 'undefined' && window.Android.copyToClipboard) {
        console.log('Android 하이브리드 앱 복사 사용');
        window.Android.copyToClipboard(text);
        wv_copy_success_handler(success_message, success_callback, show_alert);
        return;
    }

    // 2. 최신 브라우저 Clipboard API (HTTPS 환경)
    if (navigator.clipboard && navigator.clipboard.writeText && (window.isSecureContext || location.protocol === 'https:')) {
        console.log('Clipboard API 사용 시도');
        navigator.clipboard.writeText(text).then(function() {
            console.log('Clipboard API 성공');
            wv_copy_success_handler(success_message, success_callback, show_alert);
        }).catch(function(err) {
            console.log('Clipboard API 실패, fallback 시도:', err);
            wv_copy_fallback(text, success_message, error_message, success_callback, error_callback, show_manual_on_fail, show_alert);
        });
        return;
    }

    console.log('Fallback 방식 사용');
    // 3. HTTP 환경이나 구형 브라우저용 Fallback
    wv_copy_fallback(text, success_message, error_message, success_callback, error_callback, show_manual_on_fail, show_alert);
}

/**
 * Fallback 복사 방식 (execCommand 사용)
 */
function wv_copy_fallback(text, success_message, error_message, success_callback, error_callback, show_manual_on_fail, show_alert) {
    console.log('wv_copy_fallback 시작');

    // 브라우저 감지
    var is_ios = /iPad|iPhone|iPod/.test(navigator.userAgent);
    var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

    console.log('브라우저 정보:', {
        ios: is_ios,
        safari: is_safari,
        firefox: is_firefox,
        userAgent: navigator.userAgent
    });

    // 임시 textarea 생성
    var text_area = document.createElement('textarea');
    text_area.value = text;

    // 화면에서 숨기기 (하지만 접근 가능하게)
    text_area.style.position = 'fixed';
    text_area.style.top = '0';
    text_area.style.left = '0';
    text_area.style.width = '2em';
    text_area.style.height = '2em';
    text_area.style.padding = '0';
    text_area.style.border = 'none';
    text_area.style.outline = 'none';
    text_area.style.boxShadow = 'none';
    text_area.style.background = 'transparent';
    text_area.style.opacity = '0';
    text_area.style.zIndex = '-1';

    document.body.appendChild(text_area);

    var successful = false;

    try {
        // iOS Safari 특별 처리
        if (is_ios) {
            console.log('iOS Safari 처리 모드');
            text_area.contentEditable = true;
            text_area.readOnly = false;
            var range = document.createRange();
            range.selectNodeContents(text_area);
            var selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            text_area.setSelectionRange(0, 999999);
        } else {
            console.log('일반 브라우저 처리 모드');
            text_area.focus();
            text_area.select();

            // Firefox 추가 처리
            if (is_firefox) {
                text_area.setSelectionRange(0, text.length);
            }
        }

        // execCommand 시도
        console.log('execCommand 시도');
        successful = document.execCommand('copy');
        console.log('execCommand 결과:', successful);

    } catch (err) {
        console.log('execCommand 예외:', err);
        successful = false;
    }

    // 임시 엘리먼트 제거
    document.body.removeChild(text_area);

    if (successful) {
        console.log('복사 성공');
        wv_copy_success_handler(success_message, success_callback, show_alert);
    } else {
        console.log('복사 실패');
        wv_copy_error_handler(text, error_message, error_callback, show_manual_on_fail, show_alert);
    }
}

/**
 * 복사 성공 처리
 */
function wv_copy_success_handler(message, callback, show_alert) {
    if (callback && typeof callback === 'function') {
        callback(true, message);
    }

    if (show_alert) {
        if (typeof alert === 'function') {
            alert(message);
        } else {
            wv_show_toast_message(message, 'success');
        }
    }
}

/**
 * 복사 실패 처리
 */
function wv_copy_error_handler(text, message, callback, show_manual_on_fail, show_alert) {
    if (callback && typeof callback === 'function') {
        callback(false, message);
    }

    if (show_manual_on_fail) {
        wv_show_manual_copy_modal(text);
    } else if (show_alert) {
        if (typeof alert === 'function') {
            alert(message + ' 코드를 직접 선택해서 복사해주세요.');
        } else {
            wv_show_toast_message(message, 'error');
        }
    }
}

/**
 * 수동 복사를 위한 모달 표시
 */
function wv_show_manual_copy_modal(text) {
    // 기존 모달이 있으면 제거
    var existing_modal = document.getElementById('wv_copy_modal');
    if (existing_modal) {
        document.body.removeChild(existing_modal);
    }

    // 새 모달 생성
    var modal = document.createElement('div');
    modal.id = 'wv_copy_modal';
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:10000;display:flex;align-items:center;justify-content:center;';

    var modal_content = document.createElement('div');
    modal_content.style.cssText = 'background:white;padding:20px;border-radius:8px;max-width:300px;text-align:center;margin:20px;';

    modal_content.innerHTML =
        '<p style="margin:0 0 15px 0;font-weight:600;">코드를 복사해주세요</p>' +
        '<input type="text" value="' + text + '" readonly style="width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;text-align:center;font-size:16px;margin-bottom:15px;" onclick="this.select();">' +
        '<button onclick="wv_close_copy_modal()" style="background:#007bff;color:white;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;">확인</button>';

    modal.appendChild(modal_content);
    document.body.appendChild(modal);

    // 모달 바깥 클릭시 닫기
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            wv_close_copy_modal();
        }
    });

    // 5초 후 자동 닫기
    setTimeout(function() {
        wv_close_copy_modal();
    }, 10000);
}

/**
 * 수동 복사 모달 닫기
 */
function wv_close_copy_modal() {
    var modal = document.getElementById('wv_copy_modal');
    if (modal && document.body.contains(modal)) {
        document.body.removeChild(modal);
    }
}

/**
 * 토스트 메시지 표시
 */
function wv_show_toast_message(message, type) {
    var toast = document.createElement('div');
    toast.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;padding:12px 20px;border-radius:8px;color:white;font-size:14px;font-weight:600;';

    if (type === 'success') {
        toast.style.backgroundColor = '#10B981';
    } else {
        toast.style.backgroundColor = '#EF4444';
    }

    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(function() {
        if (document.body.contains(toast)) {
            document.body.removeChild(toast);
        }
    }, 2000);
}

/**
 * 간편 사용을 위한 래퍼 함수들
 */

// 기본 복사 (alert 메시지와 함께)
function wv_copy_text(text) {
    wv_copy_to_clipboard(text);
}

// 콜백과 함께 복사
function wv_copy_with_callback(text, success_fn, error_fn) {
    wv_copy_to_clipboard(text, {
        success_callback: success_fn,
        error_callback: error_fn
    });
}

// 조용한 복사 (메시지 없이)
function wv_copy_silent(text, callback) {
    wv_copy_to_clipboard(text, {
        success_callback: callback,
        error_callback: callback,
        show_alert: false,
        show_manual_on_fail: false
    });
}


