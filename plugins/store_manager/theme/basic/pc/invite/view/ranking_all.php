<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
global $member_manager;
// 누적 초대 랭킹 데이터 조회
$invite_manager = wv()->store_manager->made('invite');
$member_manager = wv()->store_manager->made('member');

// ⚡ GROUP BY를 사용한 효율적인 집계 쿼리 (전체 누적)
$ranking_options = array(
    'select' => array(
        'invite_invite_member_wr_id',
        'COUNT(*) as invite_count'
    ),
    'where_invite' => array(
        'invite_member_wr_id' => "> 0"
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
        $member = $member_manager->get($member_wr_id);

        if($member && $member->mb_id ) {
            $ranking_list[] = array(
                'wr_id' => $member_wr_id,
                'mb_id' => $member->mb_id,
                'mb_name' => $member->member->mb_mb_name,
                'mb_nick' => $member->member->mb_mb_nick,
                'mb_hp' => $member->member->mb_mb_hp,
                'invite_count' => $invite_count
            );
        }
    }
}
?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap h-100"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .medal-content { margin-top: var(--wv-15); }
        <?php echo $skin_selector?> .medal-name { font-size: var(--wv-14); font-weight: 600; color: #333; margin-bottom: var(--wv-8); }
        <?php echo $skin_selector?> .medal-count { padding: var(--wv-3) var(--wv-12);display: inline-block;border-radius: var(--wv-4) }
        <?php echo $skin_selector?> .reward-badge { position: absolute; bottom: 0; left: 0; right: 0; background: #FFD700; color: white;   padding: var(--wv-3); border-radius: 0 0 var(--wv-8) var(--wv-8); }
        <?php echo $skin_selector?> .total-count { text-align: center; font-size: var(--wv-16); font-weight: 600; color: #333; padding: var(--wv-12); }
        <?php echo $skin_selector?> .ranking-list { }
        <?php echo $skin_selector?> .ranking-item { display: flex; align-items: center; height: var(--wv-64);  ; padding:0 var(--wv-16);gap: var(--wv-29)}
        <?php echo $skin_selector?> .ranking-item:last-child { border-bottom: none; }
        <?php echo $skin_selector?> .ranking-rank { font-size: var(--wv-16); font-weight: bold; color: #666;  ; }
        <?php echo $skin_selector?> .ranking-info { flex: 1;   }
        <?php echo $skin_selector?> .ranking-name { font-size: var(--wv-14); font-weight: 600; color: #333; margin-bottom: var(--wv-2); }
        <?php echo $skin_selector?> .ranking-hp { color:#97989C }
        <?php echo $skin_selector?> .ranking-id { font-size: var(--wv-12); color: #999; }
        <?php echo $skin_selector?> .ranking-count { font-size: var(--wv-16); font-weight: bold; color: #6C7DF3; }
        <?php echo $skin_selector?> .empty-message { text-align: center; padding: var(--wv-40) var(--wv-20); color: #999; font-size: var(--wv-14); }
        <?php echo $skin_selector?> .ranking-item.rank-1 {background-color: #f4cb14;}
        <?php echo $skin_selector?> .ranking-item.rank-1 .ranking-name {color:#fff }
        <?php echo $skin_selector?> .ranking-item.rank-1 .ranking-hp {color:#fff }
        <?php echo $skin_selector?> .ranking-item.rank-1 .ranking-count {color:#fff!important; }
        <?php echo $skin_selector?> .ranking-item.rank-2 {background-color: #b3c1c9; }
        <?php echo $skin_selector?> .ranking-item.rank-2 .ranking-name {color:#fff }
        <?php echo $skin_selector?> .ranking-item.rank-2 .ranking-hp {color:#fff }
        <?php echo $skin_selector?> .ranking-item.rank-2 .ranking-count {color:#fff !important}
        <?php echo $skin_selector?> .ranking-item.rank-3 {background-color: #bfa480; }
        <?php echo $skin_selector?> .ranking-item.rank-3 .ranking-name {color:#fff }
        <?php echo $skin_selector?> .ranking-item.rank-3 .ranking-hp {color:#fff }
        <?php echo $skin_selector?> .ranking-item.rank-3 .ranking-count {color:#fff!important; }
        @media (min-width: 992px) {}

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full h-100" style="">

        <!-- 총 참여자 수 -->
        <div class=" " style="height: var(--wv-2);background-color: #efefef"></div>
        <div class="total-count hstack fs-[12/17/-0.48/600/#97989C]" style="gap:var(--wv-4)">
            <span class="text-[#0D171B]">총 <?php echo number_format(count($ranking_list)); ?>명</span>
            <span class="">참여중</span>
            <span class="ms-auto">친구 초대 수</span>
        </div>

        <!-- 전체 랭킹 목록 -->
        <?php if(count($ranking_list) > 0): ?>
            <div class="ranking-list wv-mx-fit">
                <?php foreach($ranking_list as $index => $user):
                    $rank = $index + 1;
                    $rank_class = '';
                    $rank_crown = '';

                    if($rank <= 3) {
                        $rank_class = 'rank-' . $rank;
                        $rank_crown = 'top-rank rank-' . $rank;
                    }
                    ?>
                    <div class="ranking-item <?php echo $rank_class; ?>">
                        <div class="ranking-rank <?php echo $rank_crown; ?> fs-[14/20/-0.56/600/#97989C]">
                            <?php echo $rank; ?>
                        </div>
                        <div class="ranking-info hstack" style="gap:var(--wv-6)">
                            <div class="ranking-name">
                                <?php echo $user['mb_nick'] ? $user['mb_nick'] : $user['mb_name']; ?>
                            </div>
                            <p class="fs-[12/17/-0.48/500/] ranking-hp"><?php echo wv_store_manager_mask_number(substr($user['mb_hp'],-4)); ?></p>
                        </div>
                        <div class="ranking-count fs-[16/22/-0.64/600/]">
                            <?php echo number_format($user['invite_count']); ?>명
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-message">
                아직 초대 내역이 없습니다.
            </div>
        <?php endif; ?>

    </div>

    <script>
        $(document).ready(function (){
            var $skin = $("<?php echo $skin_selector?>");
        })
    </script>
</div>