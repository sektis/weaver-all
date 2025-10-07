<?php
global $g5,$config;
global $current_member_wr_id,$member;
set_session("wv_cert_type",    '');
set_session("wv_cert_no",      '');
set_session("wv_cert_hash",    '');
set_session("wv_cert_adult",   '');
set_session("wv_cert_birth",   '');
set_session("wv_cert_sex",     '');
set_session('wv_cert_dupinfo', '');
add_javascript('<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>', 0);
switch($config['cf_cert_hp']) {
    case 'kcb':
        $cert_url = G5_OKNAME_URL.'/hpcert1.php';
        $cert_type = 'kcb-hp';
        break;
    case 'kcp':
        $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php';
        $cert_type = 'kcp-hp';
        break;
    case 'lg':
        $cert_url = G5_LGXPAY_URL.'/AuthOnlyReq.php';
        $cert_type = 'lg-hp';
        break;

}
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100   bg-white overflow-x-hidden"   style="">
    <style>
        <?php echo $skin_selector?> {}

        @media (min-width: 992px) {
        }

        @media (max-width: 991.98px) {
        }
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form id="fregisterform" name="fregisterform" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if ($is_list_item_mode) { ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
                <input type="hidden" name="cert_type" value="">
                <input type="hidden" name="cert_no" value="">
                <input type="hidden" name="mb_hp" value="">
                <input type="hidden" name="mb_name" value="">
                <div class="vstack h-100 pt-[10px]" style="">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">이름</p>
                                </div>
                                <div class="col text-end">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col">

                        <div class="border" style="border-radius: var(--wv-4);">
                            <div style="padding: var(--wv-16) var(--wv-12)  ">
                                <p class="fs-[16/22/-0.64/700/#0D171B]"><?php echo $row['mb_mb_name']; ?></p>
                                <div class="fs-[12/17/-0.48/500/#0D171B] mt-[6px]">
                                    <span>인증한 날짜</span>
                                    <span class="ff-Roboto"><?php echo $this->store->member->cert_date; ?></span>
                                </div>
                            </div>
                            <div class=" " style="height: var(--wv-1);background-color: #efefef"></div>
                            <p class="fs-[11/18/-0.44/500/#97989C]" style="padding: var(--wv-12)  ">
                                "덤이요"는 휴대폰 본인인증을 통해 확인된 정보로 가입, 로그인, 포인트 출금 등의 서비스를 제공하고 있습니다. 만약 본인 인증 이력이 없거나 이름이 변경된 경우, 보다 정확한 정보를 반영하여 서비스 이용에 불편함이 없도록 아래 버튼을 눌러 본인인증 정보를 업데이트해 주세요.
                            </p>
                        </div>

                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <button type="button" id="win_hp_cert" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">본인 인증 시작하기</button>
                    </div>

                    <div class="wv-mx-fit">
                        <?php echo wv_widget('content/privacy_notice'); ?>
                    </div>

                    <div class=" " style="background-color: #f9f9f9"><div class="m" style="height: 2px;background-color: #efefef"></div></div>

                    <div class="wv-mx-fit">
                        <?php echo wv_widget('content/copyright'); ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");

            if (typeof window.certify_win_open === 'function') {
                const originalCertify = window.certify_win_open;

                window.certify_win_open = function(type, url, event) {
                    originalCertify.call(this, type, url, event);

                    // 팝업 감지 시작
                    setTimeout(() => {
                        watchCertifyPopup(type);
                    }, 300);
                };
            }

            var params = "";
            var pageTypeParam = "pageType=register";
            $("#win_hp_cert").click(function() {
                if(!cert_confirm()) return false;

                $.post("<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/edit_mb_name_reset.php",{mb_id:'<?php echo $member['mb_id']?>'},function () {
                    params = "?" + pageTypeParam;
                    certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>"+params);
                })

                return;
            });

            function watchCertifyPopup(type) {
                const windowName = type === 'kcb-ipin' ? 'kcbPop' : 'auth_popup';
                const popup = window.open('', windowName);

                if (popup && !popup.closed) {
                    const checkInterval = setInterval(() => {
                        if (popup.closed) {
                            clearInterval(checkInterval);
                            onCertifyComplete(type);
                        }
                    }, 1000);
                }
            }


            function onCertifyComplete(type) {

                // 인증 완료 후 서버에서 상태 확인
                if(!$("input[name=cert_no]",$skin).val()){

                    return false;
                }

                wv_ajax("<?php echo wv_path_replace_url(dirname(__FILE__)) ?>/edit_mb_name_cert.php", {
                    reload_ajax: '<?php echo $skin_selector?> form',
                    ajax_option: {
                        success: function(response) {
                            alert('완료')


                        },
                      
                    }
                }, {mb_id:'<?php echo $member['mb_id']?>',member_wr_id:'<?php echo $current_member_wr_id?>',mb_name:$("input[name=mb_name]",$skin).val(),mb_hp:$("input[name=mb_hp]",$skin).val()});
                    

            }
        })
    </script>
</div>