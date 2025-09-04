<?php
@extract($_GET);
@extract($_POST);

if(!function_exists('dd')){
    function dd($obj){
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
        die();
    }
}

function get_g5_path(){

    $chroot = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], dirname(__FILE__)));

    $find_common_file = dirname(__FILE__);
    $common_file = 'common.php';
    for ($i=1;$i<=10;$i++){
        $chk = $find_common_file.'/'.$common_file;
        if(file_exists($chk)){
            $find_common_file = $chk;
            break;
        }
        $find_common_file = dirname($find_common_file);
    }
    if($find_common_file=='/'){
        die('common.php 파일을 찾을 수 없습니다.');
    }

    $result['path'] = str_replace('\\', '/', $chroot.dirname($find_common_file));

    $server_script_name = preg_replace('/\/+/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_NAME']));
    $server_script_filename = preg_replace('/\/+/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
    $tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $server_script_name);
    $document_root = str_replace($tilde_remove, '', $server_script_filename);
    $pattern = '/.*?' . preg_quote($document_root, '/') . '/i';
    $root = preg_replace($pattern, '', $result['path']);
    $port = ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':'.$_SERVER['SERVER_PORT'];
    $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
    $user = str_replace(preg_replace($pattern, '', $server_script_filename), '', $server_script_name);
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    if(isset($_SERVER['HTTP_HOST']) && preg_match('/:[0-9]+$/', $host))
        $host = preg_replace('/:[0-9]+$/', '', $host);
    $host = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*]/", '', $host);
    $result['url'] = $http.$host.$port.$user.$root;
    return $result;


}

$g5_path = get_g5_path();



define('G5_URL', $g5_path['url'].'/adm');
define('G5_BBS_URL', $g5_path['url'].'/adm/bbs');
define('G5_ORG_URL', $g5_path['url']);
define('G5_ORG_PATH', $g5_path['path']);
define('G5_IS_ADMIN', true);

include_once G5_ORG_PATH.'/common.php';