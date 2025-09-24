<?php
global $g5;
?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?>
        {
        }

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
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">관리자 정보</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>



                    <div class="wv-offcanvas-body col">
 


                        <div class="vstack   mt-[19px]" style="row-gap:var(--wv-20)">
                            <div>
                                <div class="hstack justify-content-between" >
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">관리자명</p>
                                </div>
                                <div class="mt-[12px]" style="row-gap:var(--wv-8)">
                                   <p class="fs-[16/22/-0.64/500/#0D171B]"><?php echo $row['mb_mb_name']; ?></p>
                                </div>
                            </div>

                            <div class="wv-mx-fit" style="height: 6px;background-color: #efefef"></div>

                            <div>
                                <div class="hstack justify-content-between" >
                                    <p class="fs-[16/22/-0.64/600/#0D171B]  ">관리자 전화번호</p>
                                </div>
                                <div class="mt-[12px]" style="row-gap:var(--wv-8)">
                                    <p class="fs-[16/22/-0.64/500/#0D171B] ff-montserrat"><?php echo $row['mb_mb_hp']?$row['mb_mb_hp']:'미등록'; ?></p>
                                </div>
                            </div>


                        </div>

                        <div class="fs-[12/17/-0.48/500/#97989C] mt-[12px] vstack" style="row-gap:var(--wv-8)">
                            <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>사업자등록증이 변경되었나요? 고객센터를 통해 진행해 주세요.</p></div>
                            <div class="hstack align-items-start" style="gap:var(--wv-8)"><span class="fs-12em">·</span> <p>등록된 사업자 정보와 실제 관리자 정보가 상이한 경우, <br>정확한 확인을 위해 고객센터로 문의해 주세요. </p></div>
                        </div>

                    </div>

                    <div class="mt-auto col-auto pb-[50px] hstack gap-[6px]">
                        <a href="#" class="w-full h-[54px] fs-[16/22/-0.64/700/#FC5555]   wv-flex-box  transition " style="border: var(--wv-1) solid #fc5555;background: #fff;">관리자 변경 요청하기</a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>


        $(document).ready(function () {
            $(".member_leaver",$skin).click(function (e) {
                if(confirm('정말 회원에서 탈퇴 하시겠습니까?')){
                    return true;
                }else{
                    e.stopImmediatePropagation(); // 다른 click 핸들러 실행 안됨
                    e.stopPropagation(); // 다른 click 핸들러 실행 안
                }
            })
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).ajaxForm({
                reload: false,
                // reload_ajax:true,
                success: function () {
                    var $offcanvas = $skin.closest('.wv-offcanvas');
                    $offcanvas.offcanvas('hide');
                }
            })
        })
    </script>
</div>