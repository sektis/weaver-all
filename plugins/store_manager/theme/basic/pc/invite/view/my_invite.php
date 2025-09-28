<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $g5, $member, $current_member_wr_id;

// 초대 데이터 조회
$invite_manager = wv()->store_manager->made('invite');

// 나를 초대한 내역들
$invited_me = $invite_manager->get_list(array(
    'where_invite' => array('invite_member_wr_id' => '='.$current_member_wr_id),
    'order_by' => 'w.wr_datetime DESC',
    'rows' => 10
));
//$sql = "select mb_id from g5_write_member where mb_id<>'admin' order by wr_id asc limit 200";
//$result = sql_query($sql,1);
//while ($row= sql_fetch_array($result)){
//    $data=array(
//        'wr_id'=>'',
//        'wr_content'=>'/',
//        'mb_id'=>$row['mb_id'],
//        'invite'=>array(
//                'invite_member_wr_id'=>646
//        )
//    );
//    wv('store_manager')->made('invite')->set($data);
//}

?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .invite-item { padding: 12px 0; border-bottom: 1px solid #f1f3f4; }
        <?php echo $skin_selector?> .invite-item:last-child { border-bottom: none; }
        <?php echo $skin_selector?> .invite-status { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        <?php echo $skin_selector?> .invite-status.accepted { background: #dcfce7; color: #166534; }
        <?php echo $skin_selector?> .invite-status.pending { background: #fef3c7; color: #92400e; }
        <?php echo $skin_selector?> .invite-status.rejected { background: #fee2e2; color: #991b1b; }
        <?php echo $skin_selector?> .invite-status.expired { background: #f3f4f6; color: #6b7280; }

        @media (min-width: 992px) {}
        @media (max-width: 991.98px) {}
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">
            <form name="fpartsupdate" action='<?php echo wv()->store_manager->made()->plugin_url ?>/ajax.php' method="post" class="h-100 wv-form-check" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="made" value="<?php echo $made; ?>">
                <?php if ($is_list_item_mode) { ?>
                    <input type="hidden" name="<?php echo str_replace("[{$column}]", '', $field_name); ?>[id]" value="<?php echo $row['id']; ?>">
                <?php } ?>
                <?php echo $this->store->basic->render_part('wr_id', 'form');; ?>
                <div class="vstack h-100 pt-[10px]" style="">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">나의 초대현황</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col overflow-auto">
                        <!-- 나를 초대한 사람 목록 -->
                        <div class="px-3 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0 fw-semibold fs-[14/20/-0.56/600/#0D171B]">나를 초대한 사람</h6>
                                <span class="badge bg-secondary"><?php echo count($invited_me['list']); ?>명</span>
                            </div>

                            <?php if(count($invited_me['list']) > 0): ?>
                                <div class="invite-list">
                                    <?php foreach($invited_me['list'] as $invite): ?>
                                        <div class="invite-item">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold mb-1 fs-[14/20/-0.56/600/#0D171B]">
                                                        <?php echo $invite['invite']['inviter_name'] ?: $invite['mb_id']; ?>
                                                    </div>
                                                    <div class="text-muted small fs-[12/17/-0.48/400/#97989C]">
                                                        <?php echo date('Y.m.d H:i', strtotime($invite['wr_datetime'])); ?>
                                                    </div>
                                                </div>
                                                <div>
                                                <span class="invite-status <?php echo $invite['invite']['invite_status']; ?>">
                                                    <?php echo $invite['invite']['invite_status_ko']; ?>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-user-plus fa-2x mb-3 opacity-50"></i>
                                    <p class="mb-0 fs-[14/20/-0.56/400/#97989C]">초대받은 내역이 없습니다.</p>
                                    <small class="fs-[12/17/-0.48/400/#97989C]">친구의 초대를 기다려보세요!</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <button type="button" data-bs-dismiss="offcanvas" class="w-full h-[54px] fs-[16/22/-0.64/700/#FFF] wv-submit-btn transition " style="border:0;border-radius: var(--wv-4)">확인</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");

            // 오프캔버스 이벤트 리스너
            $skin.closest('.offcanvas').on('show.bs.offcanvas', function () {
                console.log('초대현황 오프캔버스 열림');
            });

            $skin.closest('.offcanvas').on('hide.bs.offcanvas', function () {
                console.log('초대현황 오프캔버스 닫힘');
            });

            // form submit 방지 (읽기 전용)
            $("form", $skin).on('submit', function(e) {
                e.preventDefault();
                $(this).closest('.offcanvas').offcanvas('hide');
            });
        });
    </script>
</div>