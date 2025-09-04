<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 스킨디렉토리를 SELECT 형식으로 얻음
function wv_get_skin_select($skin_gubun, $id, $name, $selected = '', $event = '')
{
    global $config;

    $skins = array();
    $skin_folder = wv('gnu_skin')->get_skin_path('pc',$skin_gubun);

    if(is_dir($skin_folder)){

        $dirs = get_skin_dir($skin_gubun, wv('gnu_skin')->get_skin_path());

        if (!empty($dirs)) {
            foreach ($dirs as $dir) {
                $skins[] = 'weaver/' . $dir;
            }
        }
    }

    if (defined('G5_THEME_PATH') && $config['cf_theme']) {
        $dirs = get_skin_dir($skin_gubun, G5_THEME_PATH . '/' . G5_SKIN_DIR);
        if (!empty($dirs)) {
            foreach ($dirs as $dir) {
                $skins[] = 'theme/' . $dir;
            }
        }
    }

    $skins = array_merge($skins, get_skin_dir($skin_gubun));

    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i = 0; $i < count($skins); $i++) {
        if ($i == 0) {
            $str .= "<option value=\"\">선택</option>";
        }
        if (preg_match('#^theme/(.+)$#', $skins[$i], $match)) {
            $text = '(테마) ' . $match[1];
        }else if (preg_match('#^weaver/(.+)$#', $skins[$i], $match)) {
            $text = '(위버) ' . $match[1];
        } else {
            $text = $skins[$i];
        }

        $str .= option_selected($skins[$i], $selected, $text);
    }
    $str .= "</select>";
    return $str;
}

// 모바일 스킨디렉토리를 SELECT 형식으로 얻음
function wv_get_mobile_skin_select($skin_gubun, $id, $name, $selected = '', $event = '')
{
    global $config;

    $skins = array();


    $skin_folder = wv('gnu_skin')->get_skin_path('mobile',$skin_gubun);

    if(is_dir($skin_folder)){
        $dirs = get_skin_dir($skin_gubun, wv('gnu_skin')->get_skin_path('mobile'));
        if (!empty($dirs)) {
            foreach ($dirs as $dir) {
                $skins[] = 'weaver/' . $dir;
            }
        }
    }

    if (defined('G5_THEME_PATH') && $config['cf_theme']) {
        $dirs = get_skin_dir($skin_gubun, G5_THEME_MOBILE_PATH . '/' . G5_SKIN_DIR);
        if (!empty($dirs)) {
            foreach ($dirs as $dir) {
                $skins[] = 'theme/' . $dir;
            }
        }
    }

    $skins = array_merge($skins, get_skin_dir($skin_gubun, G5_MOBILE_PATH . '/' . G5_SKIN_DIR));

    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i = 0; $i < count($skins); $i++) {
        if ($i == 0) {
            $str .= "<option value=\"\">선택</option>";
        }
        if (preg_match('#^theme/(.+)$#', $skins[$i], $match)) {
            $text = '(테마) ' . $match[1];
        } else if (preg_match('#^weaver/(.+)$#', $skins[$i], $match)) {
            $text = '(위버) ' . $match[1];
        } else {
            $text = $skins[$i];
        }

        $str .= option_selected($skins[$i], $selected, $text);
    }
    $str .= "</select>";
    return $str;
}

// 스킨경로를 얻는다
function wv_get_skin_dir($skin, $skin_path = G5_SKIN_PATH)
{
    global $g5;

    $result_array = array();



    $dirname = $skin_path . '/' . $skin . '/';
    if (!is_dir($dirname)) {
        return array();
    }

    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        if (is_dir($dirname . $file)) {
            $result_array[] = $file;
        }
    }
    closedir($handle);
    sort($result_array);


    if($skin_path==G5_SKIN_PATH){
        $skins = array();
        $skin_devices = array('pc','mobile');
        $skin_gubun = 'shop';

        foreach ($skin_devices as $skin_device){
            $skin_folder = wv('gnu_skin')->get_skin_path($skin_device,$skin_gubun);

            if(is_dir($skin_folder)){
                $dirs = get_skin_dir($skin_gubun, wv('gnu_skin')->get_skin_path($skin_device));
                if (!empty($dirs)) {
                    foreach ($dirs as $dir) {
                        $skins[] = 'weaver/' . $dir;
                    }
                }
            }

        }
        $result_array = array_merge_recursive($result_array,$skins);
    }


    return $result_array;
}

function wv_get_skin_path($path){

    preg_match("`(?:(?:.+)?/skin/([a-zA-Z0-9_]+)/?(.+)/)?([^/]+)\.skin\.php$`",$path,$matches);

    if(!($matches[3]) or in_array($matches[1],array('board'))){
        return $path;
    }

    if(!$matches[1]){
        $matches[1] = wv_info('dir');
    }


    $replace_skin_path = wv('gnu_skin')->skin_check($matches[1],$matches[3],$matches[2]);
    return $replace_skin_path?$replace_skin_path:$path;





}