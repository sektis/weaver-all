<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $member_manager;
// 이번달 초대 랭킹 데이터 조회
$invite_manager = wv()->store_manager->made('invite');
$member_manager = wv()->store_manager->made('member');

// 이번달 시작일과 종료일
$start_date = date('Y-m-01 00:00:00');
$end_date = date('Y-m-t 23:59:59');

// ⚡ GROUP BY를 사용한 효율적인 집계 쿼리 (StoreManager.php에 group_by 옵션 추가 후 사용)
$ranking_options = array(
    'select' => array(
        'invite_invite_member_wr_id',
        'COUNT(*) as invite_count'
    ),
    'where_invite' => array(
        'and' => array(
            array('invite_date' => ">= '{$start_date}'"),
            array('invite_date' => "<= '{$end_date}'"),
            array('invite_member_wr_id' => "> 0")
        )
    ),
    'group_by' => 'invite_invite_member_wr_id',
    'order_by' => 'invite_count DESC',
    'rows' => 100,
    'nest' => false // 평면 데이터로 받기
);

$ranking_result = $invite_manager->get_list($ranking_options);
$ranking_data = $ranking_result['list'];

// 총 초대받은 사람 수 계산
$total_invited = 0;
foreach($ranking_data as $row) {
    $total_invited += (int)$row['invite_count'];
}


// 회원 정보와 결합
$ranking_list = array();
foreach($ranking_data as $row) {
    $member_wr_id =  $row['invite']['invite_member_wr_id'];
    $invite_count = $row['invite_count'];

    if($member_wr_id > 0 && $invite_count > 0) {

        $member_get = $member_manager->get($member_wr_id);

        if($member_get && $member_get->mb_id ) {
            $ranking_list[] = array(
                'wr_id' => $member_wr_id,
                'mb_id' => $member_get->mb_id,
                'mb_name' => $member_get->member->mb_mb_name,
                'mb_nick' => $member_get->member->mb_mb_nick,
                'mb_hp' => $member_get->member->mb_mb_hp,
                'invite_count' => $invite_count
            );
        }
    }
}
// 상위 3위 분리 (이미 ORDER BY로 정렬됨)
$top3 = array_slice($ranking_list, 0, 3);
$others = array_slice($ranking_list, 3);


?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap h-100"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .medal-container { display: flex; align-items: end; justify-content: center; gap: var(--wv-11); margin-bottom: var(--wv-30); min-height: var(--wv-200); }
        <?php echo $skin_selector?> .medal-box { position: relative; background: white; border-radius: var(--wv-12); padding: var(--wv-20) var(--wv-5) var(--wv-10); text-align: center; box-shadow: 0 var(--wv-4) var(--wv-12) rgba(0,0,0,0.1); width: var(--wv-195); }
        <?php echo $skin_selector?> .medal-box.gold { border: var(--wv-3) solid #FFD700; height: var(--wv-180); order: 2; }
        <?php echo $skin_selector?> .medal-box.gold .reward-badge{ background-color:  #FFD700}
        <?php echo $skin_selector?> .medal-box.gold .medal-count{ background-color:  #ffefaa;color:#f1ab12}
        <?php echo $skin_selector?> .medal-box.silver { border: var(--wv-3) solid #C0C0C0; height: var(--wv-160); order: 1; }
        <?php echo $skin_selector?> .medal-box.silver .reward-badge{ background-color:  #C0C0C0}
        <?php echo $skin_selector?> .medal-box.silver .medal-count{ background-color:  #ced9df;color:#83949d}
        <?php echo $skin_selector?> .medal-box.bronze { border: var(--wv-3) solid #CD7F32; height: var(--wv-160); order: 3; }
        <?php echo $skin_selector?> .medal-box.bronze .reward-badge{ background-color:  #CD7F32}
        <?php echo $skin_selector?> .medal-box.bronze .medal-count{ background-color:  #ebd5b8;color:#a78a65}
        <?php echo $skin_selector?> .crown { position: absolute; top: calc(-1 * var(--wv-20)); left: 50%; transform: translateX(-50%);  ;  ; color: #FFD700; font-size: var(--wv-24); }
        <?php echo $skin_selector?> .medal-rank { position: absolute; top: calc(-1 * var(--wv-10)); left: 50%; transform: translateX(-50%);   color: white; font-weight: 800; font-size: var(--wv-12);  }

        <?php echo $skin_selector?> .medal-content { margin-top: var(--wv-15); }
        <?php echo $skin_selector?> .medal-name { font-size: var(--wv-14); font-weight: 600; color: #333; margin-bottom: var(--wv-8); }
        <?php echo $skin_selector?> .medal-count { padding: var(--wv-3) var(--wv-12);display: inline-block;border-radius: var(--wv-4) }
        <?php echo $skin_selector?> .reward-badge { position: absolute; bottom: 0; left: 0; right: 0; background: #FFD700; color: white;   padding: var(--wv-3); border-radius: 0 0 var(--wv-8) var(--wv-8); }
        <?php echo $skin_selector?> .total-count { text-align: center; font-size: var(--wv-16); font-weight: 600; color: #333; padding: var(--wv-12); }
        <?php echo $skin_selector?> .ranking-list { }
        <?php echo $skin_selector?> .ranking-item { display: flex; align-items: center; height: var(--wv-64); border-bottom: var(--wv-1) solid #f1f3f4; }
        <?php echo $skin_selector?> .ranking-item:last-child { border-bottom: none; }
        <?php echo $skin_selector?> .ranking-rank { font-size: var(--wv-16); font-weight: bold; color: #666; min-width: var(--wv-30); }
        <?php echo $skin_selector?> .ranking-info { flex: 1; margin-left: var(--wv-15); }
        <?php echo $skin_selector?> .ranking-name { font-size: var(--wv-14); font-weight: 600; color: #333; margin-bottom: var(--wv-2); }
        <?php echo $skin_selector?> .ranking-id { font-size: var(--wv-12); color: #999; }
        <?php echo $skin_selector?> .ranking-count { font-size: var(--wv-16); font-weight: bold; color: #333; }
        <?php echo $skin_selector?> .empty-message { text-align: center; padding: var(--wv-40) var(--wv-20); color: #999; font-size: var(--wv-14); }

        @media (min-width: 992px) {}

        @media (max-width: 991.98px) { }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100" style="">

        <!-- 상위 3위 메달 -->
        <div class="medal-container">
            <?php
            $medal_types = array('silver', 'gold', 'bronze');
            $medal_icons = array('👑', '👑', '👑');

            for($i = 0; $i < 3; $i++):
                $rank = ($medal_types[$i] == 'gold') ? 0 : (($medal_types[$i] == 'silver') ? 1 : 2);
                $user = isset($top3[$rank]) ? $top3[$rank] : null;
                ?>
                <div class="medal-box <?php echo $medal_types[$i]; ?>">
                    <?php if($medal_types[$i] == 'gold'): ?>
                        <div class="crown"><img src="<?php echo $this->manager->plugin_url; ?>/img/crown_1.png" class="w-[40px]" alt=""></div>
                    <?php endif; ?>
                    <?php if($medal_types[$i] == 'silver'): ?>
                        <div class="crown"><img src="<?php echo $this->manager->plugin_url; ?>/img/crown_2.png" class="w-[40px]" alt=""></div>
                    <?php endif; ?>
                    <?php if($medal_types[$i] == 'bronze'): ?>
                        <div class="crown"><img src="<?php echo $this->manager->plugin_url; ?>/img/crown_3.png" class="w-[40px]" alt=""></div>
                    <?php endif; ?>
                    <div class="medal-rank <?php echo $medal_types[$i]; ?>">
                        <?php echo $rank + 1; ?>
                    </div>

                    <div class="medal-content">
                        <?php if($user): ?>
                            <div class="medal-name fs-[16/22/-0.64/600/#0D171B]">
                                <?php echo $user['mb_nick'] ? $user['mb_nick'] : $user['mb_name']; ?>
                            </div>
                            <div class="medal-count fs-[16/22/-0.64/600/]">
                                <?php echo number_format($user['invite_count']); ?>명
                            </div>
                        <?php else: ?>
                            <div class="medal-name">-</div>
                            <div class="medal-count">0명</div>
                        <?php endif; ?>
                    </div>


                        <div class="reward-badge fs-[14/20/-0.14/500/#FFF]">30만원</div>

                </div>
            <?php endfor; ?>
        </div>

        <!-- 총 초대받은 사람 수 -->
        <div class=" " style="height: 2px;background-color: #efefef"></div>
        <div class="total-count hstack fs-[12/17/-0.48/600/#97989C]" style="gap:var(--wv-4)">
            <span class="text-[#0D171B]">총 <?php echo number_format(count($ranking_list)); ?>명</span>
            <span class="">참여중</span>
            <span class="ms-auto">친구 초대 수</span>
        </div>
        <div class=" " style="height: 2px;background-color: #efefef"></div>

        <!-- 4위 이하 목록 -->
        <?php if(count($others) > 0) {?>
            <div class="ranking-list">
                <?php foreach($others as $index => $user): ?>
                    <div class="ranking-item">
                        <div class="ranking-rank fs-[14/20/-0.56/600/#97989C]"><?php echo $index + 4; ?></div>
                        <div class="ranking-info hstack" style="gap:var(--wv-4)">
                            <div class="ranking-name">
                                <?php echo $user['mb_nick'] ? $user['mb_nick'] : $user['mb_name']; ?>
                            </div>
                            <p class="fs-[12/17/-0.48/500/#97989C]"><?php echo wv_store_manager_mask_number(substr($user['mb_hp'],-4)); ?></p>
                        </div>
                        <div class="ranking-count fs-[16/22/-0.64/600/#6C7DF3]"><?php echo number_format($user['invite_count']); ?>명</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php }else {
             ?>
            <div class="empty-message">
                아직 이번 달 초대 내역이 없습니다.
            </div>
        <?php } ?>

    </div>

    <script>
        $(document).ready(function (){
            var $skin = $("<?php echo $skin_selector?>");

        })
    </script>
</div>