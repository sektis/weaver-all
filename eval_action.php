<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


// eval code execution
run_event('wv_hook_eval_action_before');

$wv_eval_act_path = $_SERVER['SCRIPT_FILENAME'];

$wv_eval_act_code = $wv_eval_org_code = wv_get_eval_code($wv_eval_act_path);

$wv_eval_act_code = run_replace('wv_hook_act_code_index_replace',$wv_eval_act_code,$wv_eval_org_code,$wv_eval_act_path);

$wv_eval_act_code = run_replace('wv_hook_act_code_layout_replace',$wv_eval_act_code,$wv_eval_org_code,$wv_eval_act_path);



if($wv_eval_act_code == $wv_eval_org_code){
    return;
}


$wv_common_code = wv_get_eval_code(G5_PATH.'/common.php');
preg_match_all('/unset\(\$extend_file\);(.+)ob_start\(\);(.+)/isu',$wv_common_code,$common_matches);

if($common_matches[1][0]){
    @eval($common_matches[1][0]);
}
if($common_matches[2][0]){
    ob_start();
    @eval($common_matches[2][0]);
}


$pattern = "`(?(?<=<script>)(?:(?s)(?:.(?!<\/script>))*.<\/script>)|(?P<func>\bfunction\s+([a-zA-Z0-9_]+)\s*(\((?:[^()]++|(?3))*\))\s*({(?:[^{}]++|(?4))*})))`";

$wv_eval_act_code = preg_replace_callback($pattern, function ($matches) {
    if(!$matches['func']){
        return $matches[0];
    }
    return "if (!function_exists('{$matches[2]}')) { {$matches['func']} }";
}, $wv_eval_act_code);
//dd($wv_eval_act_code);
@eval($wv_eval_act_code);
exit;