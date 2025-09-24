<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{font-size: var(--wv-14);font-weight: 600;}
        <?php echo $skin_selector?> .text3{font-size: var(--wv-14);font-weight: 400;}
        <?php echo $skin_selector?> .text2{font-size: var(--wv-14);font-weight: 500;color:#97989C;width: var(--wv-60)}

        @media (min-width: 992px) {
        }

        @media (max-width: 991.98px) {
        }
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
                        <div class="row align-items-center">

                            <div class="col-auto">
                                <div class="vstack" style="row-gap: var(--wv-5)">
                                    <p><?php echo $row['mb_mb_name'] ?> (<?php echo $row['mb_id']; ?>)</p>
                                    <p class="fs-[13/17/-0.52/500/#97989C]">최근 접속일 | <?php echo $row['mb_mb_today_login']; ?></p>
                                </div>
                            </div>

                            <div class="col text-end">
                                <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>

                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col"  >

                        <div class="vstack" style="row-gap: var(--wv-16)">

                            <div class="border rounded-[4px] p-[14px] vstack  " style="row-gap: var(--wv-16)">
                                <div class="hstack gap-[8px]">
                                    <p class="text1 left-text1">개인정보</p>
                                    <?php if($row['is_cert']){ ?>
                                        <div class="hstack gap-[4px]">
                                            <div class="wv-ratio-circle w-[14px] bg-[#29cc6a] text-white">
                                                <div class="d-flex-center"><i class="fa-solid fa-check fs-06em"></i></div>
                                            </div>
                                            <p class="fs-[14/20/-0.56/500/#29CC6A]">본인인증 확인</p>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="hstack gap-[33px]">
                                    <p class="text2 ">이름</p>
                                    <p class="text3"><?php echo $row['mb_mb_name']; ?></p>
                                </div>
                                <div class="hstack gap-[33px]">
                                    <p class="text2 ">생년월일</p>
                                    <p class="text3"><?php echo $row['mb_mb_birth']?date('Y / m / d',strtotime($row['mb_mb_birth'])):''; ?></p>
                                </div>
                                <div class="hstack gap-[33px]">
                                    <p class="text2 ">휴대폰번호</p>
                                    <p class="text3"><?php echo wv_mask_number($row['mb_mb_hp']); ?></p>
                                </div>
                            </div>

                            <div class="border rounded-[4px] p-[14px] vstack  " style="row-gap: var(--wv-16)">
                                <div class="hstack gap-[8px]">
                                    <p class="text1 left-text1">이용 정보</p>
                                </div>
                                <div class="hstack gap-[33px]">
                                    <p class="text2 ">가입일자</p>
                                    <p class="text3"><?php echo date('Y / m / d',strtotime($row['mb_mb_datetime'])); ?></p>
                                </div>
                                <div class="hstack gap-[33px]">
                                    <p class="text2 ">닉네임</p>
                                    <p class="text3"><?php echo $row['mb_mb_nick']; ?></p>
                                </div>
                                <div class="hstack gap-[33px]">
                                    <p class="text2 ">계좌번호</p>
                                    <p class="text3"><?php echo $row['member']['bank_info']; ?></p>
                                </div>
                            </div>

                            <div class="hstack">
                                <p class="text1 w-[75px]">계정 관리</p>
                                <div class="col"><?php echo $this->store->member->render_part('mb_password_init','form',array('row'=>$row)); ?></div>
                            </div>

                            <div class="hstack">
                                <p class="text1 w-[75px]">계정 상태</p>
                                <div class="col"><?php echo $this->store->member->render_part('active','form',array('row'=>$row)); ?></div>
                            </div>

                            <div class="hstack">
                                <p class="text1 w-[75px]">관리자 메모</p>
                                <div class="col"><?php echo $this->store->member->render_part('admin_memo','form',array('row'=>$row)); ?></div>
                            </div>

                        </div>
                    </div>

                    <div class="mt-auto pb-[50px]">
                        <button type="submit" class="wv-submit-btn transition "  >확인</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).ajaxForm({

            })
        })
    </script>
</div>




