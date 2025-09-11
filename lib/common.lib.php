<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
register_shutdown_function(function (){
    $err  = error_get_last();

    if($err and $err['type']==1 ){
        ob_clean();

        $err_arr = explode('Stack trac',$err['message']);

        wv_abort(500,$err_arr[0]);
    }
});

/** 코어함수 */
if(!function_exists('wv')){
    function wv($plugin_name=''){
        static $weaver = null;
        if( $weaver === null ){
            $weaver = weaver\Weaver::getInstance();
        }

        if($plugin_name){
            return $weaver->load($plugin_name);
        }

        return $weaver;
    }
}
if(!function_exists('wv_info')){
    function wv_info($info){
        return wv()->info->$info;
    }
}
if(!function_exists('wv_load')){
    function wv_load($plugin){
        return wv()->load($plugin);
    }
}
if(!function_exists('wv_error')){
    function wv_error($msg,$level=0){

        $bt = debug_backtrace();
        $plugin_name = basename(dirname($bt[0]['file']));

        if(wv_plugin_exists($plugin_name) and wv($plugin_name)){

            wv($plugin_name)->error($msg,$level);
            return;
        }
        wv()->error($msg,$level);
    }
}

if(!function_exists('wv_is_hook_executed')){
    function wv_is_hook_executed($tag){
        class Test extends Hook{
            function check($tag){
                $parent = self::getInstance();

                return isset($parent->actions[$tag])?true:false;
            }
        }
        $test = new Test();
        return $test->check($tag);

    }
}
if(!function_exists('wv_make_skin_id')){
    function wv_make_skin_id(){
        return 'skin-'.uniqid();
    }
}
if(!function_exists('wv_make_skin_selector')){
    function wv_make_skin_selector($skin_id){
        return '#'.$skin_id;
    }
}
//임시
if(!function_exists('wv_plugin_exists')){
    function wv_plugin_exists($plugin_name){

        return file_exists(WV_PLUGINS_PATH.'/'.$plugin_name.'/plugin.php');
    }
}
if(!function_exists('wv_class_to_plugin_name')){
    function wv_class_to_plugin_name($class_name, $str_lower = false){

        $calledClass_explode = explode('\\', $class_name);
        $plugin_name = end($calledClass_explode);
        if($str_lower){
            $plugin_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $plugin_name));
        }
        $plugin_name = str_replace('_make','',$plugin_name);
        return  $plugin_name;

    }
}
if(!function_exists('wv_plugin_name_to_class')){
    function wv_plugin_name_to_class($string){
        // 언더스코어를 공백으로 바꿈
        $string = str_replace('_', ' ', $string);
        // 각 단어의 첫 글자를 대문자로 바꿈
        $string = ucwords($string);
        // 공백을 제거하고 연결
        $string = str_replace(' ', '', $string);
        return $string;
    }
}


/**공통함수*/
if(!function_exists('array_map_deep')){
    function array_map_deep($fn, $array){
        if(is_array($array)) {
            foreach($array as $key => $value) {
                if(is_array($value)) {
                    $array[$key] = array_map_deep($fn, $value);
                } else {
                    $array[$key] = call_user_func($fn, $value);
                }
            }
        } else {
            $array = call_user_func($fn, $array);
        }

        return $array;
    }
}
if(!function_exists('dd')){
    function dd($obj){
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
        die();
    }
}
if(!function_exists('wv_abort')){
    function wv_abort($code = NULL,$text='',$url='') {
        header("Content-Type: text/html; charset=UTF-8");
        if ($code !== NULL) {

            if($text==''){
                switch ($code) {
                    case 100: $text = 'Continue'; break;
                    case 101: $text = 'Switching Protocols'; break;
                    case 200: $text = 'OK'; break;
                    case 201: $text = 'Created'; break;
                    case 202: $text = 'Accepted'; break;
                    case 203: $text = 'Non-Authoritative Information'; break;
                    case 204: $text = 'No Content'; break;
                    case 205: $text = 'Reset Content'; break;
                    case 206: $text = 'Partial Content'; break;
                    case 300: $text = 'Multiple Choices'; break;
                    case 301: $text = 'Moved Permanently'; break;
                    case 302: $text = 'Moved Temporarily'; break;
                    case 303: $text = 'See Other'; break;
                    case 304: $text = 'Not Modified'; break;
                    case 305: $text = 'Use Proxy'; break;
                    case 400: $text = 'Bad Request'; break;
                    case 401: $text = 'Unauthorized'; break;
                    case 402: $text = 'Payment Required'; break;
                    case 403: $text = 'Forbidden'; break;
                    case 404: $text = 'Not Found'; break;
                    case 405: $text = 'Method Not Allowed'; break;
                    case 406: $text = 'Not Acceptable'; break;
                    case 407: $text = 'Proxy Authentication Required'; break;
                    case 408: $text = 'Request Time-out'; break;
                    case 409: $text = 'Conflict'; break;
                    case 410: $text = 'Gone'; break;
                    case 411: $text = 'Length Required'; break;
                    case 412: $text = 'Precondition Failed'; break;
                    case 413: $text = 'Request Entity Too Large'; break;
                    case 414: $text = 'Request-URI Too Large'; break;
                    case 415: $text = 'Unsupported Media Type'; break;
                    case 500: $text = 'Internal Server Error'; break;
                    case 501: $text = 'Not Implemented'; break;
                    case 502: $text = 'Bad Gateway'; break;
                    case 503: $text = 'Service Unavailable'; break;
                    case 504: $text = 'Gateway Time-out'; break;
                    case 505: $text = 'HTTP Version not supported'; break;
                    default:
                        exit('Unknown http status code "' . htmlentities($code) . '"');
                        break;
                }
            }


            if($url){
                $code = 302;
                $text.= '@@@'.$url;
            }

            $text = (string) $text;

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' .  base64_encode($text));

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }
        ob_end_clean();
        die($text);

    }

}
if(!function_exists('wv_array_recursive_diff')){
    function wv_array_recursive_diff($array1, $array2,$ignore_text=array(),$array1_value_empty=false) {
        $difference=array();
        $ignore_text = (array) $ignore_text;


        foreach($array1 as $key => $value) {

            if( is_array($value) ) {
                if(is_numeric($key)){
                    $array2_keys = array_keys($array2);

                    if(preg_replace("/[0-9]/","", implode('',$array2_keys))==''){
                        $num_array_chk = false;
                        foreach ($array2_keys as $k=>$v){

                            $new_diff = wv_array_recursive_diff($value, $array2[$v],$ignore_text,$array1_value_empty);

                            if(empty($new_diff) ) {
                                $num_array_chk=true;
                                break;
                            };
                        }

                        if(!$num_array_chk){
                            $difference[$key] = $value;
                        }
                    }
                }elseif( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                    $difference[$key] = $value;
                } else {

                    $new_diff = wv_array_recursive_diff($value, $array2[$key],$ignore_text,$array1_value_empty);

                    if( !empty($new_diff) )
                        $difference[$key] = $new_diff;
                }
            } else if( !array_key_exists($key,$array2) || trim(str_replace(' ','',$array2[$key]))  !== trim(str_replace(' ','',$value)) ) {
                if($array1_value_empty and trim($value)==''){
                    continue;
                }

                if(in_array(trim($array2[$key]),$ignore_text)){
                    continue;
                }
                $difference[$key] = $value;

            }
        }

        return $difference;
    }
}
if(!function_exists('wv_add_qstr')){
    function wv_add_qstr($add_qstr,$allow_var='',$allow_rule="/[\<\>\'\"\\\'\\\"\%\=\(\)\^\*]/"){
        global $qstr ;

        $add_qstr = (array) $add_qstr;
        $allow_var = (array) $allow_var;

        $default_rule = "/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*]/";

        foreach ($add_qstr as $val){
            global $$val;

            if ($$val)  {

                $$val = clean_xss_tags(trim($$val));

                $rule = $default_rule;

                if ($$val) {

                    if(in_array($val,$allow_var)){
                        $rule = $allow_rule;
                    }

                    $$val = preg_replace($rule, "", $$val);
                    $qstr .= '&amp;'.$val.'=' . urlencode($$val);
                }

            } else {
                $$val = '';
            }
        }
    }
}
if(!function_exists('wv_json_exit')){
    function wv_json_exit($arr,$result=false)
    {
        if(!is_array($arr)){
            $arr = array('result'=>$result,'content'=>$arr);
        }
        if(!isset($arr['result'])){
            $arr['result']=true;
        }

        echo json_encode($arr);
        exit;
    }
}
if(!function_exists('wv_add_config_meta')){
    function wv_add_config_meta($meta_tag){
        global $config;
        if(is_array($meta_tag)){
            $meta_tag = implode("\n",$meta_tag);
        }
        $config['cf_add_meta'] = $config['cf_add_meta'].$meta_tag;
    }
}
if(!function_exists('wv_path_replace_url')){
    function wv_path_replace_url($path){
        $path = str_replace("\\","/",$path);
        $path = str_replace(G5_PATH,'',$path);
        return G5_URL.$path;
    }
}
if(!function_exists('wv_is_base64_encoded')){
    function wv_is_base64_encoded($data){
        // 기본 체크
        if (!is_string($data) || empty($data)) {
            return false;
        }

        // ✅ 숫자만 있으면 false
        if (preg_match('/^[0-9]+$/', $data)) {
            return false;
        }

        // ✅ 너무 짧으면 false (최소 8자리)
        if (strlen($data) < 8) {
            return false;
        }

        // ✅ 영문자만 있고 8자 미만이면 false (일반 단어)
        if (strlen($data) < 12 && preg_match('/^[a-zA-Z]+$/', $data)) {
            return false;
        }

        // base64 문법 체크
        if (!preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $data)) {
            return false;
        }

        // 길이 체크 (4의 배수)
        if (strlen($data) % 4 !== 0) {
            return false;
        }

        // 디코딩 테스트
        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            return false;
        }

        // 재인코딩 일치 체크
        return base64_encode($decoded) === $data;
    }
}
if(!function_exists('wv_is_serialized')){
    function wv_is_serialized( $data, $strict = true ) {
        // If it isn't a string, it isn't serialized.
        if ( ! is_string( $data ) ) {
            return false;
        }
        $data = trim( $data );
        if ( 'N;' === $data ) {
            return true;
        }
        if ( strlen( $data ) < 4 ) {
            return false;
        }
        if ( ':' !== $data[1] ) {
            return false;
        }
        if ( $strict ) {
            $lastc = substr( $data, -1 );
            if ( ';' !== $lastc && '}' !== $lastc ) {
                return false;
            }
        } else {
            $semicolon = strpos( $data, ';' );
            $brace     = strpos( $data, '}' );
            // Either ; or } must exist.
            if ( false === $semicolon && false === $brace ) {
                return false;
            }
            // But neither must be in the first X characters.
            if ( false !== $semicolon && $semicolon < 3 ) {
                return false;
            }
            if ( false !== $brace && $brace < 4 ) {
                return false;
            }
        }
        $token = $data[0];
        switch ( $token ) {
            case 's':
                if ( $strict ) {
                    if ( '"' !== substr( $data, -2, 1 ) ) {
                        return false;
                    }
                } elseif ( false === strpos( $data, '"' ) ) {
                    return false;
                }
            // Or else fall through.
            case 'a':
            case 'O':
            case 'E':
                return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$end/", $data );
        }
        return false;
    }
}
if(!function_exists('wv_base64_decode_unserialize')){
    function wv_base64_decode_unserialize($data){

        if(wv_is_base64_encoded($data)){
            $data = base64_decode($data);
        }

        if(wv_is_serialized($data)){
            $data = unserialize($data);
        }

        return $data;
    }
}
if(!function_exists('wv_base64_encode_serialize')){
    function wv_base64_encode_serialize($data){



        if(!wv_is_serialized($data)){
            $data = serialize($data);
        }

        if(!wv_is_base64_encoded($data)){
            $data = base64_encode($data);
        }

        return $data;
    }
}
if(!function_exists('wv_text_to_php_array_code')){
    function wv_text_to_php_array_code($text,$use_array_text=false,$separator1 = PHP_EOL,$separator2 = ':'){
        $arr1 = explode($separator1,$text);
        $arr2 = array();
        $str = '';

        foreach ($arr1 as $each){
            if($each=='')continue;

            $temp = explode($separator2,$each);

            $e_key=trim($temp[0]);
            $e_val=trim($temp[1]);
            if($e_key=='' and $e_val=='')continue;
            $arr2[] = "'{$e_key}' => '{$e_val}'";
        }
        if($use_array_text){
            $str.= 'array('.PHP_EOL;
            $str.= implode(',<br>',$arr2).PHP_EOL;
            $str.= ');';
            return $str;

        }
        return $arr2;
    }
}
if(!function_exists('wv_glob')){
    function wv_glob($base, $pattern, $recursive=true, $flags = 0 ) {
        $flags = $flags & ~GLOB_NOCHECK;


        if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
            $base .= DIRECTORY_SEPARATOR;
        }

        $files = glob($base.$pattern, $flags|GLOB_BRACE);
        if (!is_array($files)) {
            $files = array();
        }


        $dirs = glob($base.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK|GLOB_BRACE);
        if (!is_array($dirs)) {
            return $files;
        }

        if($recursive){

            foreach ($dirs as $dir) {
                $dirFiles = wv_glob($dir, $pattern, $recursive, $flags);

                $files = array_merge($files, $dirFiles);
            }
        }


        return $files;
    }

}
if(!function_exists('wv_char_loop')){
    function wv_char_loop($char,$count=99,$init=true){
        static $i=1;
        static $text='';
        if($init){
            $i=1;
            $text='';
        }
        if($count==0){
            return $text;
        }
        $count--;
        $text.=$char;
        return  wv_char_loop($char,$count,false);
    }

}
if(!function_exists('wv_array_group_by')){
    function wv_array_group_by($key, $data) {
        $result = array();

        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }
}
if(!function_exists('wv_get_rand_str')){
    function wv_get_rand_str($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
if(!function_exists('wv_parent_dir_check')){
    function wv_parent_dir_check($curr_path,$find_dir){
        if($_SERVER['DOCUMENT_ROOT']==$curr_path)return false;
        if(basename($curr_path)!=$find_dir)return wv_parent_dir_check(dirname($curr_path),$find_dir);
        return true;
    }
}
if(!function_exists('wv_get_eval_code')){
    function wv_get_eval_code($file){
        if(!file_exists($file)){
            return false;
        }
        $pattern = array("/^([\s\n\r]+)?&lt;\?php/iu","/&nbsp;/imu","/&lt;/imu","/&gt;/imu");
        $replace = array(""," ","<",">");
        $code = @show_source($file,1);
        $code = str_replace("<br />","\r",$code);
        $code = strip_tags($code);
        $code = preg_replace($pattern,$replace,$code);
        $code = html_entity_decode($code);
        return $code;
    }
}
if(!function_exists('wv_get_code_from_file')){
    function wv_get_code_from_file($code,$file_path){
        $eval_code = wv_get_eval_code($file_path);
        $code = preg_quote($code, '/');
        preg_match_all("/$code/i", $eval_code, $matches);

        return $matches[0];
    }
}
if(!function_exists('wv_get_return_path')){
    function wv_get_return_path($code){
        preg_match_all("/if\s*\(([^\n\r]+)\)\s*{((?:(?!if).)+)[require|include]_once[\s(]+(.+?)[\s)]+;.+?return;.+?[^\n\r]+/isu",$code,$return_conditions);

        if(isset($return_conditions[1]) and count($return_conditions[1])){
            for($i=0;$i<count($return_conditions[1]);$i++){
                $eval_result = @eval('return '.$return_conditions[1][$i].';');
                if($eval_result){
                    return @eval('return '.$return_conditions[3][$i].';');


                }
            }

        }
        return false;
    }
}
if(!function_exists('wv_get_final_eval_code')){
    function wv_get_final_eval_code($code,$path=''){
        $new_include_path = wv_get_return_path($code);
        if($new_include_path){
            $new_code = wv_get_eval_code($new_include_path);

            return wv_get_final_eval_code($new_code,$new_include_path);
        }
        return array('code'=>$code,'path'=>$path);
    }
}
if(!function_exists('wv_remove_comments')){
    function wv_remove_comments($code) {
        // 정규식 패턴 (//, # 한 줄 주석 + /* ... */ 블록 주석 제거)
        $comment_pattern = [
            '/\/\/[^\r\n]*/',       // `//`로 시작하는 한 줄 주석
            '/# [^\r\n]*/',         // `#`으로 시작하는 한 줄 주석
            '/\/\*[\s\S]*?\*\//'    // `/* ... */` 블록 주석
        ];

        // 주석 제거
        return preg_replace($comment_pattern, '', $code);
    }
}
if(!function_exists('wv_replace_include_rel_path')){
    function wv_replace_include_rel_path($act_code,$act_path){
//        $act_code = preg_replace_callback("/@?(require|include)(_once)?([\.\s('\"]+)(\.\/)?([a-z_\.A-Z]+\.php)([ '\");]+)/imu",function ($matches)use($act_path){

        $clean_code = wv_remove_comments($act_code);
        $code_changed = false;
        $clean_code = preg_replace_callback("/(?<!\/\/)\b(require|include)(_once)?([\.\s('\"]+)(\.\/)?([a-z_\.A-Z]+\.php)([ '\");]+)/imu",function ($matches)use($act_path,&$code_changed){
            if(!$matches[5]){
                return $matches[0];
            }


            $dir_name = dirname($act_path);
            $relative_path = $dir_name.'/'.$matches[5];
            $relative_code = wv_get_eval_code($relative_path);

            preg_match("`return;`isu",$relative_code,$return_code_match);
            if($return_code_match){

                return "{$matches[1]}{$matches[2]}('".$relative_path."');";
            }



            if(preg_match("/\?>(?:(?!<\?php).)+$/isu",$relative_code)){

                $relative_code.='<?php';

            }


            $relative_code = wv_replace_include_var_path($relative_code,$dir_name);
            $code_changed = true;

            return $relative_code;
        },$clean_code);

        if($code_changed){
            return $clean_code;
        }

        return $act_code;
    }
}
if(!function_exists('wv_replace_include_var_path')){
    function wv_replace_include_var_path($act_code,$dir_name=''){
        if($dir_name){
            $dir_name = rtrim($dir_name,'/').'/';
        }
        $act_code = preg_replace_callback("/(@?)(require|include)(_once)?([\s(]+)([\$]+[a-zA-Z0-9_]+)(\[[a-zA-Z0-9_'\"]+\])([\s)]+);/isu",function ($matches)use($dir_name){

            $var_path = @eval('global '.$matches[5].';return '.$matches[5].$matches[6].';');
            if($var_path==''){
                return $matches[0];
            }

            $var_code = $matches[1].$matches[2].$matches[3].$matches[4]."'{$dir_name}{$var_path}'".$matches[7].';';

            return $var_code;
        },$act_code);
        return $act_code;
    }
}
if(!function_exists('wv_get_youtube_id')){
    function wv_get_youtube_id($html,$only_one=true)
    {
        $pattern = '/^[a-zA-Z0-9_-]{11}$/';
        if(preg_match($pattern, $html)){
            return $html;
        }
        $regex = '#(?:https?://|//)?(?:www\.|m\.|.+\.)?(?:youtu\.be/|youtube\.com/(?:embed/|v/|shorts/|feeds/api/videos/|watch\?v=|watch\?.+&v=))([\w-]{11})(?![\w-])#';
        preg_match_all($regex, $html, $matches);
        if(!isset($matches[1]))return false;
        if($only_one) return $matches[1][0];
        return $matches[1];
    }
}
if(!function_exists('wv_get_youtube_thumb')){
    function wv_get_youtube_thumb($html,$quality=0,$youtube_id=''){

        if(!$youtube_id){
            $youtube_id = wv_get_youtube_id($html,true);
        }
        $quality_arr = array(
            0=>array('name'=>'maxresdefault','size'=>array(1280,1920)),
            1=>array('name'=>'sddefault','size'=>array(640)),
            2=>array('name'=>'hqdefault','size'=>array(480)),
            3=>array('name'=>'mqdefault','size'=>array(320)),
            4=>array('name'=>'default','size'=>array(120)),
        );

        return "https://img.youtube.com/vi/{$youtube_id}/{$quality_arr[$quality]['name']}.jpg";
    }
}
if(!function_exists('wv_movie_get_type')){
    function wv_movie_get_type($url){
        $wr_1_parse = parse_url($url);
        $ext = pathinfo($wr_1_parse['path'], PATHINFO_EXTENSION);
        if($ext=='mp4'){
            return 'mp4';
        }
        $youtube_id = wv_get_youtube_id($url);
        if($youtube_id){
            return 'youtube';
        }
        return '';
    }
}
if(!function_exists('wv_movie_get_src')){
    function wv_movie_get_src($url){

        $type = wv_movie_get_type($url);

        if($type=='mp4'){
            $src = $url;
        }else{

            if($type=='youtube'){
                $youtube_id = wv_get_youtube_id($url);

                $src="https://www.youtube.com/embed/{$youtube_id}";
            }else{
                $src=$url;
            }

        }
        return $src;
    }
}
if(!function_exists('wv_movie_display')){
    function wv_movie_display($url,$extend_option=array()){

        $option = array(
            'ratio'=>'56.25%',
            'param_youtube'=>'mute=0&autoplay=1&loop=1',
            'param_video'=>'muted  autoplay playsinline'
        );

        $option = array_merge($option,$extend_option);

        $type = wv_movie_get_type($url);

        if(!$type)return '';

        if( preg_match("`\/shorts\/`",$url)){
            $option['ratio']='177.14%';
            $option['max_width']='500px';
        }

        if($type=='mp4'){
            $str ='
            <div class="ratio mx-auto  " style="--bs-aspect-ratio:'.$option['ratio'].'">
                <video '.$option['param_video'].'>
                    <source src="'.$url.'" type="video/mp4" />
                </video>
            </div>';
        }else{
            $src = wv_movie_get_src($url).'?'.$option['param_youtube'];
            $str = '
            <div class="ratio mx-auto  " style="'.($option['ratio']?"--bs-aspect-ratio:{$option['ratio']};":'').($option['ratio']?"max-width:{$option['max_width']};":'').'">
                        <iframe  src="'.$src.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  ></iframe>
                    </div>';
        }
        return $str;
    }
}
if(!function_exists('wv_page_url')){
    function wv_page_url($wv_page_id,$query=array(),$wvd=''){
        $query_arr = $query;

        $page_qstr = "/?";
        $page_qstr_arr = array('wv_page_id'=>$wv_page_id);

        if($wvd){
            $page_qstr_arr['wvd']=$wvd;
//            $page_qstr.="&wvd={$wvd}";
        }
        if($query){
            if(!is_array($query)){

                parse_str($query_arr,$query_arr);

            }

            $page_qstr_arr = wv_merge_without_override($page_qstr_arr,$query_arr);

        }

        $page_qstr.=http_build_query(array_unique($page_qstr_arr));

        if(function_exists('short_url_clean')){
            return short_url_clean(G5_URL.$page_qstr);
        }
        return G5_URL.$page_qstr;
    }
}
if(!function_exists('wv_date_empty_chk')){
    function wv_date_empty_chk($date){
        $pattern = array("0", ":", "-"," ");
        $replace = array("", "", "","");
        $str_replace = str_replace($pattern,$replace,$date);
        if($str_replace==''){
            return '';
        }
        return $date;
    }
}
if(!function_exists('wv_str_split')){
    function wv_str_split($str, $bundle=1){
        $chip = array();
        for ($i=0; $i < mb_strlen($str); $i += $bundle){
            array_push($chip, mb_substr($str,$i,$bundle));
        }
        return $chip;
    }
}
if(!function_exists('wv_str_get_byte')){
    function wv_str_get_byte($content){
        $sms_content_length = 0;
        foreach(wv_str_split($content) as $word){
            $sms_content_length += (mb_ord($word) < 128) ? 1 : 2;
        }
        return $sms_content_length;
    }
}
if(!function_exists('wv_sms_send')){
    function wv_sms_send($send_list=array(),$wr_reply,$wr_message,$reservation_send = array()){

        global $g5,$config;
        include_once(G5_SMS5_PATH.'/sms5.lib.php');

        $wr_reply   = preg_replace('#[^0-9\-]#', '', trim($wr_reply));
        $wr_message = clean_xss_tags(trim($wr_message));

        if (!$wr_reply)
            return('회신 번호를 숫자, - 로 입력해주세요.');

        if(!check_vaild_callback($wr_reply))
            return('회신 번호를 올바르게 입력해 주십시오.');

        if (!$wr_message)
            return('메세지를 입력해주세요.');

        if (!trim($send_list))
            return('문자 메세지를 받을 휴대폰번호를 입력해주세요.');

        $list = array();
        $hps = array();

        $send_list = explode('/', $send_list);
        $wr_overlap = 1; // 중복번호를 체크함
        $overlap = 0;
        $duplicate_data = array();
        $duplicate_data['hp'] = array();
        $str_serialize = "";
        while ($row = array_shift($send_list))
        {
            $item = explode(',', $row);

            for ($i=1, $max = count($item); $i<$max; $i++)
            {
                if (!trim($item[$i])) continue;

                switch ($item[0])
                {
                    case 'g': // 그룹전송
                        $qry = sql_query("select * from {$g5['sms5_book_table']} where bg_no='$item[1]' and bk_receipt=1");
                        while ($row = sql_fetch_array($qry))
                        {
                            $row['bk_hp'] = get_hp($row['bk_hp'], 0);

                            if(!$row['bk_hp']) continue;

                            if ($wr_overlap && array_overlap($hps, $row['bk_hp'])) {
                                $overlap++;
                                array_push( $duplicate_data['hp'], $row['bk_hp'] );
                                continue;
                            }

                            array_push($list, $row);
                            array_push($hps, $row['bk_hp']);
                        }
                        break;

                    case 'l':
                        $mb_level = $item[$i];

                        $qry = sql_query("select mb_id, mb_name, mb_nick, mb_hp from {$g5['member_table']} where mb_level='$mb_level' and mb_sms=1 and not (mb_hp='')");
                        while ($row = sql_fetch_array($qry))
                        {
                            $name = $row['mb_nick'];
                            $hp = get_hp($row['mb_hp'], 0);
                            $mb_id = $row['mb_id'];

                            if(!$hp) continue;

                            if ($wr_overlap && array_overlap($hps, $hp)) {
                                $overlap++;
                                array_push( $duplicate_data['hp'], $row['bk_hp'] );
                                continue;
                            }

                            $row = sql_fetch("select bg_no, bk_no from {$g5['sms5_book_table']} where mb_id='{$row['mb_id']}'");
                            $bg_no = $row['bg_no'];
                            $bk_no = $row['bk_no'];

                            array_push($list, array('bk_hp' => $hp, 'bk_name' => $name, 'mb_id' => $mb_id, 'bg_no' => $bg_no, 'bk_no' => $bk_no));
                            array_push($hps, $hp);
                        }
                        break;

                    case 'h': // 권한(mb_leve) 선택

                        $item[$i] = explode(':', $item[$i]);
                        $hp = get_hp($item[$i][1], 0);
                        $name = $item[$i][0];

                        if(!$hp) continue;

                        if ($wr_overlap && array_overlap($hps, $hp)) {
                            $overlap++;
                            array_push( $duplicate_data['hp'], $row['bk_hp'] );
                            continue;
                        }

                        array_push($list, array('bk_hp' => $hp, 'bk_name' => $name));
                        array_push($hps, $hp);
                        break;

                    case 'p': // 개인 선택

                        $row = sql_fetch("select * from {$g5['sms5_book_table']} where bk_no='$item[$i]'");
                        $row['bk_hp'] = get_hp($row['bk_hp'], 0);

                        if(!$row['bk_hp']) continue;

                        if ($wr_overlap && array_overlap($hps, $row['bk_hp'])) {
                            $overlap++;
                            array_push( $duplicate_data['hp'], $row['bk_hp'] );
                            continue;
                        }
                        array_push($list, $row);
                        array_push($hps, $row['bk_hp']);
                        break;
                }
            }
        }

        if( count($duplicate_data['hp']) ){ //중복된 번호가 있다면
            $duplicate_data['total'] = $overlap;
            $str_serialize = serialize($duplicate_data);
        }

        $wr_total = count($list);

// 예약전송
        if ($wr_by && $wr_bm && $wr_bd && $wr_bh && $wr_bi) {
            $wr_booking = "$wr_by-$wr_bm-$wr_bd $wr_bh:$wr_bi";
            $booking = $wr_by.$wr_bm.$wr_bd.$wr_bh.$wr_bi;
        } else {
            $wr_booking = '';
            $booking = '';
        }

        if ($config['cf_sms_use'] != 'icode') {
            alert('기본환경설정에서 icode sms 사용이 비활성화 되어 있습니다.');
        }



        $reply = str_replace('-', '', trim($wr_reply));
        $wr_message = conv_unescape_nl($wr_message);

        $SMS = new SMS5;

        if($config['cf_sms_type'] == 'LMS') {
            $port_setting = get_icode_port_type($config['cf_icode_id'], $config['cf_icode_pw']);

            if($port_setting !== false) {
                $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $port_setting);

                $wr_success = 0;
                $wr_failure = 0;
                $count      = 0;

                $row2 = sql_fetch("select max(wr_no) as wr_no from {$g5['sms5_write_table']}");
                if ($row2)
                    $wr_no = $row2['wr_no'] + 1;
                else
                    $wr_no = 1;

                for($i=0; $i<$wr_total; $i++) {
                    $strDest = array();
                    $strDest[]   = $list[$i]['bk_hp'];
                    $strCallBack = $reply;
                    $strCaller   = $config['cf_title'];
                    $strSubject  = '';
                    $strURL      = '';
                    $strData     = $wr_message;
                    if( !empty($list[$i]['bk_name']) ){
                        $strData    = str_replace("{이름}", $list[$i]['bk_name'], $strData);
                    }
                    $strDate = $booking;
                    $nCount = 1;

                    $result = $SMS->Add($strDest, $strCallBack, $strCaller, $strSubject, $strURL, $strData, $strDate, $nCount);

                    if($result) {
                        $result = $SMS->Send();

                        if ($result) //SMS 서버에 접속했습니다.
                        {
                            foreach ($SMS->Result as $result)
                            {
                                list($phone, $code) = explode(":", $result);

                                if (substr($code,0,5) == "Error")
                                {
                                    $hs_code = substr($code,6,2);

                                    switch ($hs_code) {
                                        case '02':	 // "02:형식오류"
                                            $hs_memo = "형식이 잘못되어 전송이 실패하였습니다.";
                                            break;
                                        case '23':	 // "23:인증실패,데이터오류,전송날짜오류"
                                            $hs_memo = "데이터를 다시 확인해 주시기바랍니다.";
                                            break;
                                        case '97':	 // "97:잔여코인부족"
                                            $hs_memo = "잔여코인이 부족합니다.";
                                            break;
                                        case '98':	 // "98:사용기간만료"
                                            $hs_memo = "사용기간이 만료되었습니다.";
                                            break;
                                        case '99':	 // "99:인증실패"
                                            $hs_memo = "인증 받지 못하였습니다. 계정을 다시 확인해 주세요.";
                                            break;
                                        default:	 // "미 확인 오류"
                                            $hs_memo = "알 수 없는 오류로 전송이 실패하였습니다.";
                                            break;
                                    }
                                    $wr_failure++;
                                    $hs_flag = 0;
                                }
                                else
                                {
                                    $hs_code = $code;
                                    $hs_memo = get_hp($phone, 1)."로 전송했습니다.";
                                    $wr_success++;
                                    $hs_flag = 1;
                                }

                                $row = $list[$i];
                                $row['bk_hp'] = get_hp($row['bk_hp'], 1);

                                $log = array_shift($SMS->Log);
                                $log = @iconv('euc-kr', 'utf-8', $log);

                                sql_query("insert into {$g5['sms5_history_table']} set wr_no='$wr_no', wr_renum=0, bg_no='{$row['bg_no']}', mb_id='{$row['mb_id']}', bk_no='{$row['bk_no']}', hs_name='".addslashes($row['bk_name'])."', hs_hp='{$row['bk_hp']}', hs_datetime='".G5_TIME_YMDHIS."', hs_flag='$hs_flag', hs_code='$hs_code', hs_memo='".addslashes($hs_memo)."', hs_log='".addslashes($log)."'", false);
                            }

                            $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
                        }
                    }
                }

                sql_query("insert into {$g5['sms5_write_table']} set wr_no='$wr_no', wr_renum=0, wr_reply='$wr_reply', wr_message='$wr_message', wr_success='$wr_success', wr_failure='$wr_failure', wr_memo='$str_serialize', wr_booking='$wr_booking', wr_total='$wr_total', wr_datetime='".G5_TIME_YMDHIS."'");
            }
        } else {
            $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
            $result = $SMS->Add2($list, $reply, '', '', $wr_message, $booking, $wr_total);

            if ($result)
            {
                $result = $SMS->Send();

                if ($result) //SMS 서버에 접속했습니다.
                {
                    $row = sql_fetch("select max(wr_no) as wr_no from {$g5['sms5_write_table']}");
                    if ($row)
                        $wr_no = $row['wr_no'] + 1;
                    else
                        $wr_no = 1;

                    sql_query("insert into {$g5['sms5_write_table']} set wr_no='$wr_no', wr_renum=0, wr_reply='$wr_reply', wr_message='$wr_message', wr_booking='$wr_booking', wr_total='$wr_total', wr_datetime='".G5_TIME_YMDHIS."'");

                    $wr_success = 0;
                    $wr_failure = 0;
                    $count      = 0;

                    foreach ($SMS->Result as $result)
                    {
                        list($phone, $code) = explode(":", $result);

                        if (substr($code,0,5) == "Error")
                        {
                            $hs_code = substr($code,6,2);

                            switch ($hs_code) {
                                case '02':	 // "02:형식오류"
                                    $hs_memo = "형식이 잘못되어 전송이 실패하였습니다.";
                                    break;
                                case '23':	 // "23:인증실패,데이터오류,전송날짜오류"
                                    $hs_memo = "데이터를 다시 확인해 주시기바랍니다.";
                                    break;
                                case '97':	 // "97:잔여코인부족"
                                    $hs_memo = "잔여코인이 부족합니다.";
                                    break;
                                case '98':	 // "98:사용기간만료"
                                    $hs_memo = "사용기간이 만료되었습니다.";
                                    break;
                                case '99':	 // "99:인증실패"
                                    $hs_memo = "인증 받지 못하였습니다. 계정을 다시 확인해 주세요.";
                                    break;
                                default:	 // "미 확인 오류"
                                    $hs_memo = "알 수 없는 오류로 전송이 실패하였습니다.";
                                    break;
                            }
                            $wr_failure++;
                            $hs_flag = 0;
                        }
                        else
                        {
                            $hs_code = $code;
                            $hs_memo = get_hp($phone, 1)."로 전송했습니다.";
                            $wr_success++;
                            $hs_flag = 1;
                        }

                        $row = array_shift($list);
                        $row['bk_hp'] = get_hp($row['bk_hp'], 1);

                        $log = array_shift($SMS->Log);
                        $log = @iconv('euc-kr', 'utf-8', $log);

                        sql_query("insert into {$g5['sms5_history_table']} set wr_no='$wr_no', wr_renum=0, bg_no='{$row['bg_no']}', mb_id='{$row['mb_id']}', bk_no='{$row['bk_no']}', hs_name='".addslashes($row['bk_name'])."', hs_hp='{$row['bk_hp']}', hs_datetime='".G5_TIME_YMDHIS."', hs_flag='$hs_flag', hs_code='$hs_code', hs_memo='".addslashes($hs_memo)."', hs_log='".addslashes($log)."'", false);
                    }
                    $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

                    sql_query("update {$g5['sms5_write_table']} set wr_success='$wr_success', wr_failure='$wr_failure', wr_memo='$str_serialize' where wr_no='$wr_no' and wr_renum=0");
                }
                else return ("에러: SMS 서버와 통신이 불안정합니다.");
            }
            else return ("에러: SMS 데이터 입력도중 에러가 발생하였습니다.");
        }
        return true;
    }

}
if(!function_exists('wv_mask_name')){
    function wv_mask_name($name,$blind_char ='*') {
        $length = mb_strlen($name);

        // 두 글자 이하의 이름 처리
        if ($length <= 2) {
            return mb_substr($name, 0, 1) . str_repeat('*', $length - 1);
        }

        // 첫 글자와 마지막 글자 유지, 나머지 문자 대체
        $firstChar = mb_substr($name, 0, 1);
        $lastChar = mb_substr($name, $length - 1, 1);
        $maskedPart = str_repeat($blind_char, $length - 2);

        return $firstChar . $maskedPart . $lastChar;
    }
}
if(!function_exists('wv_mask_number')){
    function wv_mask_number($number,$blind_char ='*'){
        $hyphen_number = hyphen_hp_number($number);
        $parts = explode('-', $hyphen_number);
        if (count($parts) === 3) {
            $parts[1] = str_repeat($blind_char, strlen($parts[1]));
            return implode('-', $parts);
        }
        return $hyphen_number;
    }

}
if(!function_exists('wv_only_number')){
    function wv_only_number($text){
        return preg_replace('/\D/', '', $text);
    }

}
if(!function_exists('wv_add_symlink')){
    function wv_add_symlink($target,$link) {
        if(!file_exists($target)){
            if(is_link($link)){wv_del_symlink($link);}
            wv_error('경로를 찾을 수 없습니다. : '.$target,2);
        }

        if(is_link($link)){
            $read_link = readlink($link);
            if($read_link==$target){
              return true;
            }
            wv_del_symlink($link);

        }

        @mkdir(dirname($link), G5_DIR_PERMISSION,true);
        @chmod(dirname($link), G5_DIR_PERMISSION);


        if(!symlink($target, $link)){
            $error = error_get_last();
            wv_error($error['message'],2);
        }
    }
}
if(!function_exists('wv_del_symlink')){
    function wv_del_symlink($link) {
        if(is_link($link)){
            unlink($link);
        }
    }
}
if(!function_exists('wv_remove_last_part')){
    function wv_remove_last_part($string,$char='-'){
        $char = preg_quote($char);
        return preg_replace("/$char\w+$/", '', $string);
    }
}
if(!function_exists('wv_get_hash_type')){
    /*
     *   login_password_check 함수 return 위에 아래코드 넣기
     if(wv_get_hash_type($hash)=='md5' and md5($pass)===$hash){

        if( ! isset($mb['mb_password2']) ){
            $sql = "ALTER TABLE `{$g5['member_table']}` ADD `mb_password2` varchar(255) NOT NULL default '' AFTER `mb_password`";
            sql_query($sql);
        }

        $new_password = create_hash($pass);
        $sql = " update {$g5['member_table']} set mb_password = '$new_password', mb_password2 = '$hash' where mb_id = '$mb_id' ";
        sql_query($sql);
        return true;
    }
     */
    function wv_get_hash_type($hash) {
        if (preg_match('/^[a-f0-9]{32}$/', $hash)) {
            return 'md5';
        } elseif (preg_match('/^\\*([A-F0-9]{40})$/', $hash)) {
            return 'sql_password';
        }   else {
            return '';
        }
    }
}
if(!function_exists('wv_get_file_type')){
    function wv_get_file_type($filePath) {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $path = parse_url($filePath, PHP_URL_PATH);
        $filePath =  $_SERVER['DOCUMENT_ROOT'] . $path;
        $mimeType = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        if (strpos($mimeType, 'image/') === 0) {
            return "image";
        } elseif (strpos($mimeType, 'video/') === 0) {
            return "movie";
        } else {
            return false;
        }
    }
}
if(!function_exists('wv_is_json')){
    function wv_is_json($string) {
        json_decode(stripslashes($string));
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
if(!function_exists('wv_loop_clone')){
    function wv_loop_clone($arr,$length){
        if(!is_array($arr)){
            return $arr;
        }
        $arr_count = count($arr);
        if($arr_count < $length){
            $output = array_slice($arr, 0,$length-$arr_count);
            $arr = array_merge_recursive($arr,$output);

            return wv_loop_clone($arr,$length);
        }
        return $arr;
    }
}
if(!function_exists('wv_get_instagram_profile')){
    /**
     * 페이스북 개발자페이지에서 비즈니스 유형으로 앱만들기 https://developers.facebook.com/apps
     * Instagram Basic Display 제품 추가
     * 계정추가 api 설정에서 액세스 토큰 생성에 계정 추가
     * 앱상태는 개발(라이브X)상태여야 함
     */
function wv_get_instagram_profile($user_id,$access_token,$fields="name,username,profile_pic,follower_count,is_user_follow_business,is_business_follow_user"){

    $url = "https://graph.instagram.com/$user_id?fields={$fields}&access_token=$access_token";
    $res = wv_parse($url);
    $insta_pic = json_decode($res['content'],1);
    return $insta_pic;
}
}
if(!function_exists('wv_get_instagram_feed')){
    /**
     * 페이스북 개발자페이지에서 기타,비즈니스 유형으로 앱만들기 https://developers.facebook.com/apps
     * Instagram Basic Display 제품 추가
     * 역할에서 테스트아이디로 현재 계정 추가
     * 계정추가 api 설정에서 액세스 토큰 생성에 계정 추가
     * 앱상태는 개발(라이브X)상태여야 함
     */
    function wv_get_instagram_feed($user_id,$access_token,$fields="id,caption,media_type,media_url,like_count,owner,username,permalink,thumbnail_url,timestamp"){

        $url = "https://graph.instagram.com/$user_id/media?fields={$fields}&access_token=$access_token";
        $res = wv_parse($url);

        $insta_pic = json_decode($res['content'],1);
        return $insta_pic;
    }
}
if(!function_exists('wv_good_check')){
    function wv_board_good_check($bo_table,$wr_id,$flag='good'){
        global $g5,$member;
        $sql = " select bg_id  from {$g5['board_good_table']} where mb_id='{$member['mb_id']}' and bo_table='{$bo_table}' and wr_id='{$wr_id}' and bg_flag='{$flag}'";
        $row = sql_fetch($sql);
        return $row['bg_id']?true:false;
    }
}
if(!function_exists('wv_local_var_to_global')){
    function wv_local_var_to_global($vars) {
        foreach ($vars as $key => $value) {
            $GLOBALS[$key] = $value;
        }
    }
}
if(!function_exists('wv_get_days_since')){
    function wv_get_days_since($date) {
        $startDate = new DateTime($date);
        $currentDate = new DateTime();
        $interval = $startDate->diff($currentDate);

        // 도래한 날짜일 경우 음수, 미도래일 경우 양수로 설정
        $days = $interval->days;
        if ($startDate < $currentDate) {
            $days *= -1;
        }

        return $days;
    }
}
if(!function_exists('wv_trans_sido')){
    function wv_trans_sido($text) {
        // 긴 공식명 → 2글자 약칭
        $map = array(
            '서울특별시'     => '서울',
            '부산광역시'     => '부산',
            '대구광역시'     => '대구',
            '인천광역시'     => '인천',
            '광주광역시'     => '광주',
            '대전광역시'     => '대전',
            '울산광역시'     => '울산',
            '세종특별자치시' => '세종',

            '경기도'         => '경기',
            '강원특별자치도' => '강원',
            '강원도'         => '강원',
            '충청북도'       => '충북',
            '충청남도'       => '충남',
            '전라북도'       => '전북',
            '전라남도'       => '전남',
            '경상북도'       => '경북',
            '경상남도'       => '경남',
            '제주특별자치도' => '제주',
            '제주도'         => '제주',

            // 약식 표기 대응 (있을 때만 매칭)
            '전북특별자치도' => '전북',
            '서울시' => '서울', '부산시' => '부산', '대구시' => '대구', '인천시' => '인천',
            '광주시' => '광주', '대전시' => '대전', '울산시' => '울산', '세종시' => '세종',
        );

        // 긴 키부터 매칭되도록 길이 기준 내림차순
        uksort($map, function($a, $b){
            $la = mb_strlen($a, 'UTF-8');
            $lb = mb_strlen($b, 'UTF-8');
            if ($la === $lb) return 0;
            return ($la < $lb) ? 1 : -1; // 길이 큰 것이 먼저
        });

        // 정규식 패턴 생성
        $keys = array_keys($map);
        foreach ($keys as &$k) {
            $k = preg_quote($k, '~');
        }
        $pattern = '~' . implode('|', $keys) . '~u';

        // 치환
        return preg_replace_callback($pattern, function($m) use ($map){
            $k = $m[0];
            return isset($map[$k]) ? $map[$k] : $k;
        }, $text);
    }
}
if(!function_exists('wv_dir_var_pre_check')){
    function wv_dir_var_pre_check($dirs){
        $common_file_check = wv_get_code_from_file('if($_REQUEST[\'wv_dir_var\']){$result[\'url\'].=\'/\'.$_REQUEST[\'wv_dir_var\'];}',G5_PATH.'/common.php');
        if(!count($common_file_check)){
            wv()->error(G5_PATH.'/common.php 파일  상단 g5_path()함수 return 전에 다음코드를 추가하세요. <br> if($_REQUEST[\'wv_dir_var\']){$result[\'url\'].=\'/\'.$_REQUEST[\'wv_dir_var\'];}',2);
        }

        $htaccess_file_check = wv_get_code_from_file(implode((array)$dirs,'|'),G5_PATH.'/.htaccess');
        if(!count($htaccess_file_check)){
            wv()->error(G5_PATH.'/.htaccess파일 RewriteRule ^ - [L] 아래에 다음코드를 추가하세요. <br>'.'RewriteRule ^('.implode((array)$dirs,'|').')/?(.*)$ /$2?wv_dir_var=$1 [QSA,L]',2);
        }
    }
}
if(!function_exists('wv_must_login')){
    function wv_must_login(){
        global $member;
        $prefix = array('login','register','password');
        $strtok = strtok(wv_info('file'),'_');
        if(!$member['mb_id']  and !wv_is_ajax() and wv_info('dir')!='plugin' and !in_array($strtok,$prefix)){
            goto_url( G5_BBS_URL.'/login.php?'.$qstr.'&amp;url='.urlencode(wv_get_current_url()));
        }
    }
}
if(!function_exists('wv_never_register')){
    function wv_never_register(){
        global $member;
        $prefix = array('register');
        $strtok = strtok(wv_info('file'),'_');
        if(!$member['mb_id'] and in_array($strtok,$prefix)){
            alert('회원가입이 불가합니다.',G5_URL);
        }
    }
}
if(!function_exists('wv_merge_without_override')){
    function wv_merge_without_override($a, $b) {
        foreach ($b as $k => $v) {
            if (!array_key_exists($k, $a)) {
                $a[$k] = $v;
            }
        }
        return $a;
    }
}
if(!function_exists('wv_merge_by_key_recursive')){
    function wv_merge_by_key_recursive(array $left, array $right){
        foreach($right as $k => $v){
            if(array_key_exists($k, $left)){
                if(is_array($left[$k]) && is_array($v)){
                    $left[$k] = wv_merge_by_key_recursive($left[$k], $v);
                } else {
                    // 충돌 정책: 오른쪽으로 교체 (원하면 주석 아래 줄을 바꾸세요)
                    $left[$k] = $v;
                    // 왼쪽 유지하려면 위 줄 대신 다음 줄:
                    // $left[$k] = $left[$k];
                }
            } else {
                $left[$k] = $v;
            }
        }
        return $left;
    }

}
if(!function_exists('wv_arr_to_text')){
    function wv_array_to_text($arr){
        $args = func_get_args();
        $arr = array_shift($args);
        $sep = $left = array_shift($args);

        $right = array_shift($args);
        if($right){
            $sep=$right.$left;
        }
        return ($right?$left:'').implode($sep,$arr).$right;


    }
}
if(!function_exists('wv_text_to_array')){
    function wv_text_to_array($text){
        $args = func_get_args();

        $text = array_shift($args);
        $sep = $left = array_shift($args);

        $right = array_shift($args);
        if($right){
            $sep=$right.$left;
        }
        return explode($sep,rtrim(ltrim($text,$left),$right));
    }
}
if(!function_exists('wv_arr_get_from_path')){
    function wv_arr_get_from_path($arr, $path, $default = null){

        $path_str = wv_array_to_text($path,"['","']");
        $combined = '$arr'  . $path_str;
        return  @eval("return  $combined;");

//        foreach ($path as $k) {
//            if (is_array($arr) && array_key_exists($k, $arr)) {
//                $arr = $arr[$k];
//            } else {
//                return $default;
//            }
//        }
//        return $arr;
    }
}
if(!function_exists('wv_parse_file_array_tree')){
    function wv_parse_file_array_tree($root /* , ...$keys */){

        $args = func_get_args();
        $root_key = array_shift($args);
        $start_path = $args;

        // ✅ 목록파트 호환: 빈 스텝(''·null) 제거 → 비면 루트부터 순회
        $clean_path = array();
        foreach ($start_path as $step) {
            if ($step === '' || $step === null) continue;
            $clean_path[] = $step;
        }
        $start_path = $clean_path; // []이면 루트

        // $_FILES 존재 가드
        if (!isset($_FILES[$root_key]) || !isset($_FILES[$root_key]['name'])) {
            return array();
        }

        $get_prop = function($prop, $path) use ($root_key){
            if (!isset($_FILES[$root_key][$prop])) return null;
            $node = $_FILES[$root_key][$prop];
            foreach ($path as $step){
                if (is_array($node) && array_key_exists($step, $node)) {
                    $node = $node[$step];
                } else {
                    return null;
                }
            }
            return $node;
        };

        $build_leaf = function($path) use ($get_prop,$root_key){
            $assoc = array(
                'name'     => $get_prop('name',     $path),
                'type'     => $get_prop('type',     $path),
                'tmp_name' => $get_prop('tmp_name', $path),
                'error'    => $get_prop('error',    $path),
                'size'     => $get_prop('size',     $path),
            );
            if (!is_array($assoc)) return null;
            $err = isset($assoc['error']) ? (int)$assoc['error'] : UPLOAD_ERR_NO_FILE;
            $nm  = isset($assoc['name']) ? $assoc['name'] : '';
            $tmp = isset($assoc['tmp_name']) ? $assoc['tmp_name'] : '';
            if ($err === UPLOAD_ERR_NO_FILE) return null;
            if ($nm === '' && $tmp === '') return null;

            wv_arr_get_from_path($_POST,array_merge(array($root_key),$path));
            return $assoc;
        };



        $walk = function($path) use (&$walk, $start_path, $get_prop, $build_leaf){

            // 루트 or 하위에서 'name' 노드 얻기
            $names_node = $get_prop('name', $path);

            // ✅ 경로가 없으면 빈 배열(병합 안전)
            if ($names_node === null) return array();

            // 하위가 더 있으면 재귀 순회
            if (is_array($names_node)) {
                $tmp = array();
                foreach ($names_node as $k => $_v) {

                    // 더 내려갈 수 있는 구조면 재귀
                    if (is_array($_v)) {
                        $child = $walk(array_merge($path, array($k)));
                        // 빈 배열은 건너뜀(쓸모없는 빈 깡통 제거)
                        if ($child !== null && $child !== array()) {
                            $tmp[$k] = $child;
                        }
                        continue;
                    }

                    // leaf: 동일 경로의 5개 파일 속성 모음
                    $leaf_path = array_merge($path, array($k));

                    $assoc = $build_leaf($leaf_path);

                    if ($assoc !== null) $tmp[$k] = $assoc;
                }
                return $tmp;
            }

            // 단일 leaf (루트가 스칼라일 수도 있음)
            $assoc = $build_leaf($path);
            if ($assoc === null) return array();

            // 루트 leaf면 키 없이 그대로 반환, 하위 leaf면 마지막 키에 매핑
            $last_key = count($path) ? $path[count($path)-1] : null;

            if($last_key==$start_path[0]){

                $last_key=null;
            }
            return $last_key === null ? $assoc : array($last_key => $assoc);
        };

        // ✅ start_path가 비어있으면 루트([])부터 순회
        return $walk($start_path);
    }
}
if(!function_exists('wv_walk_by_ref')){
    function wv_walk_by_ref_diff(&$arr, $func,  $arr2=array(),  $node = array()){



        if($func($arr,$arr2,$node)===false){
            return false;
        }

        foreach ($arr as $k => &$v){
            $cur_node = $node;
            $cur_node[] = $k;   // 전체 경로


                wv_walk_by_ref_diff($v,$func,isset($arr2[$k])?$arr2[$k]:array(),$cur_node);



        }
        unset($v); // ★ 참조 foreach 후 반드시 해제 (레퍼런스 끌고 다니는 버그 방지)
        return true;

    }
}
if(!function_exists('wv_array_last')){
    function wv_array_last($arr) {
        if (empty($arr)) return null;
        $v = end($arr);
        reset($arr);
        return $v;
    }
}
if(!function_exists('wv_array_has_all_keys')){
    function wv_array_has_all_keys($required_keys, $arr){
        if (!is_array($required_keys) || !is_array($arr)) return false;
        foreach ($required_keys as $k){
            if (!array_key_exists($k, $arr)) return false; // null 값이어도 키만 있으면 OK
        }
        return true;
    }
}
if(!function_exists('wv_array_to_sql_set')){
    function wv_array_to_sql_set($arr, $conn=null){
        $parts = array();
        foreach($arr as $k => $v){
            if ($v === null){
                $parts[] = "`{$k}` = NULL";
            }
            elseif (is_numeric($v) && !preg_match('/^0[0-9]/', (string)$v)){
                // 숫자 그대로, 단 "01" 같은 문자열은 따옴표 필요
                $parts[] = "`{$k}` = {$v}";
            }
            else{
                $safe = $conn ? mysqli_real_escape_string($conn, $v) : addslashes($v);
                $parts[] = "`{$k}` = '{$safe}'";
            }
        }
        return implode(', ', $parts);
    }

}
if(!function_exists('wv_empty_except_keys')){
    function wv_empty_except_keys($arr, $exclude_keys = array()){
        // 제외할 키 제거
        $arr = array_diff_key($arr, array_flip($exclude_keys));

        // 의미있는 값만 남기기: ''와 null 제거 (0/'0'은 남김)
        $arr = array_filter($arr, function($v){
            return $v !== '' && $v !== null && !(is_array($v) && count($v) === 0);
        });

        // 남은 게 없으면 비었다고 판단
        return count($arr) === 0;
    }
}


if(!function_exists('wv_format_biznum')){
    function wv_format_biznum($str)
    {
        // 숫자만 남기기
        $num = preg_replace('/[^0-9]/', '', $str);

        // 10자리(일반 사업자번호) 기준
        if (strlen($num) == 10) {
            return preg_replace('/(\d{3})(\d{2})(\d{5})/', '$1-$2-$3', $num);
        }

        // 길이가 안 맞으면 원본 리턴 (필요시 빈값 반환하도록 수정 가능)
        return $str;
    }
}


/**
 * 쇼핑몰 관련 함수
 */
if(!function_exists('get_it_thumbnail_array')){
    function get_it_thumbnail_array($it,$thumb_w=100,$thumb_h=100){
        global $default;

        $big_img_count = 0;
        $big_imgs = array();
        $thumbnails = array();

        for($i=1; $i<=10; $i++) {
            if(!$it['it_img'.$i])
                continue;

            $img = get_src(get_it_thumbnail($it['it_img'.$i], $default['de_mimg_width'], $default['de_mimg_height']));

            if($img) {
                // 썸네일
                $thumb = get_src(get_it_thumbnail($it['it_img'.$i], $thumb_w, $thumb_h,'',true));
                $thumbnails[] = array(
                    'href'=>G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$i,
                    'src'=>$thumb
                );
                $big_img_count++;
                $big_imgs[] = array(
                    "href" => G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$i,
                    "src" => $img
                );

            }
        }
        if($big_img_count == 0) {
            $big_imgs[] = array(
                "href" => 'javascript:;',
                "src" => G5_SHOP_URL.'/img/no_image.gif'
            );
        }

        return array('big_imgs'=>$big_imgs,'thumbnails'=>$thumbnails);
    }
}
if(!function_exists('wv_is_all_int_keys')){
    function wv_is_all_int_keys(array $arr){
        return count(array_filter(array_keys($arr), 'is_int')) === count($arr);
    }
}



/*************************************************************************
 **  이미지 관련 함수
 *************************************************************************/
if(!function_exists('wv_get_src')){
    function wv_get_src($html,$only_one=false){
        preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $html, $matches);
        if($only_one){
            return $matches[1][0];
        }
        return $matches[1];
    }
}
if(!function_exists('wv_thumbnail')){
    function wv_thumbnail($path, $width, $height='',$is_create=false,$crop=true) {
        include_once(G5_LIB_PATH."/thumbnail.lib.php");
        $path = str_replace(G5_URL,G5_PATH,$path);
        $explode = explode("/", $path);
        $filename = end($explode);

        $path = dirname($path);
        $target_path_default = G5_DATA_PATH.'/thumb';
        $target_path = $target_path_default.str_replace(G5_PATH,'',$path);

        @mkdir($target_path_default, G5_DIR_PERMISSION,true);
        @chmod($target_path_default, G5_DIR_PERMISSION);

        @mkdir($target_path, G5_DIR_PERMISSION,true);
        @chmod($target_path, G5_DIR_PERMISSION);

        $tname = thumbnail($filename, $path, $target_path, $width, $height, false, $crop);
        if(!$tname) return false;
        $target_path = str_replace( '', '/', $target_path );
        $target_path = ( ( isset($_SERVER['HTTPS']) ) ? 'https://' : 'http://'  ) . str_replace( $_SERVER['DOCUMENT_ROOT'] , $_SERVER['SERVER_NAME'] . '' , $target_path);

//    G5_DATA_URL.'/file/'
        return $target_path."/".$tname;
    }
}
if(!function_exists('wv_thumbnail_delete')){
    function wv_thumbnail_delete($path) {
        global $config;

        $path = str_replace(G5_URL,'',$path);
        $path = str_replace(G5_PATH,'',$path);

        if(preg_match("/\.({$config['cf_image_extension']})$/i", $path)) {
            $fn = preg_replace("/\.[^\.]+$/i", "", basename($path));
            $files = glob(G5_DATA_PATH.'/thumb'.dirname($path).'/thumb-'.$fn.'*');

            if (is_array($files)) {
                foreach ($files as $filename)
                    unlink($filename);
            }
        }


    }
}
if(!function_exists('wv_bf_no_thumbnail')){
    function wv_bf_no_thumbnail($bo_table, $wr_id, $thumb_width, $thumb_height, $bf_no=0,$is_create=false, $is_crop=false,  $crop_mode='center', $is_sharpen=false, $um_value='80/0.5/3')
    {
        include_once(G5_LIB_PATH.'/thumbnail.lib.php');
        global $g5, $config; 
        $filename = $alt = $data_path = '';
        $edt = false;

        $row = get_board_file_db($bo_table, $wr_id, 'bf_file, bf_content', "and bf_no = '{$bf_no}' and bf_type in (1, 2, 3, 18) ", true);

        $empty_array = array('src'=>'', 'ori'=>'', 'alt'=>'');

        if(isset($row['bf_file']) && $row['bf_file']) {
            $filename = $row['bf_file'];
            $filepath = G5_DATA_PATH.'/file/'.$bo_table;
            $alt = get_text($row['bf_content']);
        }

        if(!$filename)
            return $empty_array;

        if( $thumbnail_info = run_replace('get_list_thumbnail_info', array(), array('bo_table'=>$bo_table, 'wr_id'=>$wr_id, 'data_path'=>$data_path, 'edt'=>$edt, 'filename'=>$filename, 'filepath'=>$filepath, 'thumb_width'=>$thumb_width, 'thumb_height'=>$thumb_height, 'is_create'=>$is_create, 'is_crop'=>$is_crop, 'crop_mode'=>$crop_mode, 'is_sharpen'=>$is_sharpen, 'um_value'=>$um_value)) ){
            return $thumbnail_info;
        }

        $tname = thumbnail($filename, $filepath, $filepath, $thumb_width, $thumb_height, $is_create, $is_crop, $crop_mode, $is_sharpen, $um_value);

        if($tname) {
            if($edt) {
                // 오리지날 이미지
                $ori = G5_URL.$data_path;
                // 썸네일 이미지
                $src = G5_URL.str_replace($filename, $tname, $data_path);
            } else {
                $ori = G5_DATA_URL.'/file/'.$bo_table.'/'.$filename;
                $src = G5_DATA_URL.'/file/'.$bo_table.'/'.$tname;
            }
        } else {
            return $empty_array;
        }

        $thumb = array("src"=>$src, "ori"=>$ori, "alt"=>$alt);

        return $thumb;
    }

}




/*************************************************************************
 **  부트스트랩 함수
 *************************************************************************/
if(!function_exists('wv_get_paging')){
    function wv_get_paging($write_pages, $cur_page, $total_page, $url, $add="")
    {
        //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
        $url = preg_replace('#(&amp;)?page=[0-9]*#', '', $url);
        $url .= substr($url, -1) === '?' ? 'page=' : '&amp;page=';

        if($total_page<2)return '';

        $str = '';
//        if ($cur_page > 1) {
        if (true) {
            $str .= '<div class="wv-ratio-circle border w-[32px] fs-08em"><a href="'.$url.'1'.$add.'" class="d-flex-center" style="color:#797979"><i class="fa-solid fa-angles-left lh-0"></i></a></div>'.PHP_EOL;
        }

        $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
        $end_page = $start_page + $write_pages - 1;

        if ($end_page >= $total_page) $end_page = $total_page;

//        if ($start_page > 1) {
        if (true) {
            $str .= '<div class="wv-ratio-circle border w-[32px] fs-08em"><a href="'.$url.($start_page-1).$add.'" class="d-flex-center" style="color:#797979"><i class="fa-solid fa-angle-left lh-0"></i></a></div>'.PHP_EOL;
        }

//        if ($total_page > 1) {
        if (true) {
            for ($k=$start_page;$k<=$end_page;$k++) {
                if ($cur_page != $k)
                    $str .= '<div class="wv-ratio-circle border  w-[32px]"> <a href="'.$url.$k.$add.'" class="d-flex-center">'.$k.'<span class="sound_only">페이지</span></a> </div>'.PHP_EOL;
                else
                    $str .= '<div class="wv-ratio-circle border  w-[32px] active"><a href="javascript:;" class="fw-700 d-flex-center">'.$k.'<span class="sound_only">페이지</span></a></div>'.PHP_EOL;
            }
        }

//        if ($total_page > $end_page) {
        if (true) {
            $str .= '<div class="wv-ratio-circle border w-[32px] fs-08em"><a href="'.$url.($end_page+1).$add.'" class="d-flex-center" style="color:#797979"><i class="fa-solid fa-angle-right lh-0"></i></a></div>'.PHP_EOL;
        }

//        if ($cur_page < $total_page) {
        if (true) {
            $str .= '<div class="wv-ratio-circle border w-[32px] fs-08em" ><a href="'.$url.$total_page.$add.'" class="d-flex-center" style="color:#797979"><i class="fa-solid fa-angles-right lh-0"></i></a></div>'.PHP_EOL;
        }

        if ($str)
            return "<div class=\"wv-pagination hstack gap-[10px] align-items-center \" style='--bs-border-color:#d9d9d9'>{$str}</div>";
        else
            return "";
    }
}

if(!function_exists('wv_get_sideview')){
    function wv_get_sideview($mb_id, $name='', $email='', $homepage='')
    {
        global $config;
        global $g5;
        global $bo_table, $sca, $is_admin, $member;

        $email_enc = new str_encrypt();
        $email = $email_enc->encrypt($email);
        $homepage = set_http(clean_xss_tags($homepage));

        $name     = get_text($name, 0, true);
        $email    = get_text($email);
        $homepage = get_text($homepage);

        $tmp_name = "";
        if ($mb_id) {
            //$tmp_name = "<a href=\"".G5_BBS_URL."/profile.php?mb_id=".$mb_id."\" class=\"sv_member\" title=\"$name 자기소개\" rel="nofollow" target=\"_blank\" onclick=\"return false;\">$name</a>";

            $tmp_name = '<a href="'.G5_BBS_URL.'/profile.php?mb_id='.$mb_id.'" class="sv_member text-muted card-link" title="'.$name.' 자기소개" target="_blank" data-bs-toggle="dropdown"  rel="nofollow" onclick="return false;">';

            if ($config['cf_use_member_icon']) {
                $mb_dir = substr($mb_id,0,2);
                $icon_file = G5_DATA_PATH.'/member/'.$mb_dir.'/'.$mb_id.'.gif';

                if (file_exists($icon_file)) {
                    $width = $config['cf_member_icon_width'];
                    $height = $config['cf_member_icon_height'];
                    $icon_file_url = G5_DATA_URL.'/member/'.$mb_dir.'/'.$mb_id.'.gif';
                    $tmp_name .= '<span class="profile_img"><img src="'.$icon_file_url.'" width="'.$width.'" height="'.$height.'" alt=""></span>';

                    if ($config['cf_use_member_icon'] == 2) // 회원아이콘+이름
                        $tmp_name = $tmp_name.' '.$name;
                } else {
                    if( defined('G5_THEME_NO_PROFILE_IMG') ){
                        $tmp_name .= G5_THEME_NO_PROFILE_IMG;
                    } else if( defined('G5_NO_PROFILE_IMG') ){
                        $tmp_name .= G5_NO_PROFILE_IMG;
                    }
                    if ($config['cf_use_member_icon'] == 2) // 회원아이콘+이름
                        $tmp_name = $tmp_name.' '.$name;
                }
            } else {
                $tmp_name = $tmp_name.' '.$name;
            }
            $tmp_name .= '</a>';

            $title_mb_id = '['.$mb_id.']';
        } else {
            if(!$bo_table)
                return $name;

            $tmp_name = '<a href="'.G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;sca='.$sca.'&amp;sfl=wr_name,1&amp;stx='.$name.'" title="'.$name.' 이름으로 검색" data-bs-toggle="dropdown"  class="sv_guest text-muted card-link" rel="nofollow" onclick="return false;">'.$name.'</a>';
            $title_mb_id = '[비회원]';
        }

        $str = "<div class=\"dropdown d-inline-block\">\n";
        $str .= $tmp_name."\n";

        $str2 = "<div class=\"dropdown-menu\">\n";
        if($mb_id)
            $str2 .= "<a class=\"dropdown-item\" href=\"".G5_BBS_URL."/memo_form.php?me_recv_mb_id=".$mb_id."\" onclick=\"win_memo(this.href); return false;\">쪽지보내기</a>\n";
        if($email)
            $str2 .= "<a class=\"dropdown-item\" href=\"".G5_BBS_URL."/formmail.php?mb_id=".$mb_id."&amp;name=".urlencode($name)."&amp;email=".$email."\" onclick=\"win_email(this.href); return false;\">메일보내기</a>\n";
        if($homepage)
            $str2 .= "<a class=\"dropdown-item\" href=\"".$homepage."\" target=\"_blank\">홈페이지</a>\n";
        if($mb_id)
            $str2 .= "<a class=\"dropdown-item\" href=\"".G5_BBS_URL."/profile.php?mb_id=".$mb_id."\" onclick=\"win_profile(this.href); return false;\">자기소개</a>\n";
        if($bo_table) {
            if($mb_id)
                $str2 .= "<a class=\"dropdown-item\" href=\"".G5_BBS_URL."/board.php?bo_table=".$bo_table."&amp;sca=".$sca."&amp;sfl=mb_id,1&amp;stx=".$mb_id."\">아이디로 검색</a>\n";
            else
                $str2 .= "<a class=\"dropdown-item\" href=\"".G5_BBS_URL."/board.php?bo_table=".$bo_table."&amp;sca=".$sca."&amp;sfl=wr_name,1&amp;stx=".$name."\">이름으로 검색</a>\n";
        }
        if($mb_id)
            $str2 .= "<a class=\"dropdown-item\" href=\"".G5_BBS_URL."/new.php?mb_id=".$mb_id."\" class=\"link_new_page\" onclick=\"check_goto_new(this.href, event);\">전체게시물</a>\n";
        if($is_admin == "super" && $mb_id) {
            $str2 .= "<a class=\"dropdown-item\" href=\"".G5_ADMIN_URL."/member_form.php?w=u&amp;mb_id=".$mb_id."\" target=\"_blank\">회원정보변경</a>\n";
            $str2 .= "<a class=\"dropdown-item\" href=\"".G5_ADMIN_URL."/point_list.php?sfl=mb_id&amp;stx=".$mb_id."\" target=\"_blank\">포인트내역</a>\n";
        }
        $str2 .= "</div>\n";
        $str .= $str2;


        $str .= "</div>";

        return $str;
    }
}
if(!function_exists('wv_get_category_dropdown')){
    function wv_get_category_dropdown($categories,$selected='',$category_href,$title='분류 : ',$all_title='전체',$button_class='btn border dropdown-toggle'){
        $category_option ='';
        array_unshift($categories,$all_title);
        $html = '';
        $html.= "<div class=\"dropdown\">";
        $html.= "<button class=\"{$button_class}\" type=\"button\" id=\"dropdownMenuButton\" data-bs-toggle=\"dropdown\" >";

        if($selected){
            $html.= $title.$selected;
        }else{
            $html.= $title.$all_title;
            $selected = $all_title;
        }

        $html.= " </button>";
        $html.= "<div class=\"dropdown-menu dropdown-menu-start\" aria-labelledby=\"dropdownMenuButton\">";
        for ($i=0; $i<count($categories); $i++) {
            $category_option='';
            $category = trim($categories[$i]);
            $category_var = $category;
            if ($category=='') continue;
            if ($category==$selected) { // 현재 선택된 카테고리라면
                $category_option .= 'disabled';
            }
            if($category==$all_title){
                $category_var = '';
            }
            $html .= '<a href="'.($category_href.urlencode($category_var)).'" class="dropdown-item '.$category_option.'">'.$category.'</a>';


        }
        $html.= " </div>";
        $html.= " </div>";

        return $html;
    }
}


/*************************************************************************
 **  URL 관련 함수
 *************************************************************************/
if(!function_exists('wv_get_current_url')){
    function wv_get_current_url($short_url = true, $remove_rewrite_val = false, $return_parse_array = false){


        $url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';

        $url.=$_SERVER['HTTP_HOST'];

        if(isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT']!=80){
            $url.=":{$_SERVER['SERVER_PORT']}";
        }

        if($short_url){
            $url.=$_SERVER['REQUEST_URI'];
        }else{
            $url.=($_REQUEST['wv_dir_var']?"/{$_REQUEST['wv_dir_var']}":"").$_SERVER['SCRIPT_NAME'].((isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING']) ? "?".$_SERVER['QUERY_STRING'] : '');
        }

        $parse = wv_parse_url($url);

        if(wv_plugin_exists('page') and $parse['path']=='/bbs/board.php' and $parse['query']['bo_table']=='page' and $parse['query']['wr_seo_title']){
            $parse['path']='/index.php';
            $parse['query']['wv_page_id']=$parse['query']['wr_seo_title'];

            unset($parse['query']['bo_table']);
            unset($parse['query']['wr_seo_title']);

        }
        if($remove_rewrite_val){

            unset($parse['query']['rewrite']);


        }

        return $return_parse_array?$parse:wv_build_url($parse);
    }
}
if(!function_exists('wv_exist_url')){
    function wv_exist_url($url){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        $result = curl_exec($curl);
        $return = false;
        if ($result !== false) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode != 404) {
                $return = true;
            }
        }

        return $return;
    }
}
if(!function_exists('wv_is_domain')){
    function wv_is_domain($domain_name){
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }
}
if(!function_exists('wv_is_external_url')){
    function wv_is_external_url($url){

        return stripos(preg_replace('/^https?:\/\//i', '', $url), $_SERVER['HTTP_HOST']) ===false ;
    }
}
if(!function_exists('wv_is_url')){
    function wv_is_url($url){
        return (filter_var($url, FILTER_VALIDATE_URL) !== false);

    }
}
if(!function_exists('wv_get_org_url')){
    function wv_get_org_url($url, $return_parse_array = false){

        global $config;


        if(!is_string($url)){
            alert('wv_get_org_url : url만 가능합니다.');
        }

        $url = wv_add_scheme($url);


        $parse = wv_parse_url($url);


        if(!$parse['host'] or !in_array($parse['host'],array($_SERVER['HTTP_HOST'],preg_replace('/^https?:\/\//i', '', G5_URL)))){
            return $return_parse_array?$parse:$url;
        }


        $explode = array_filter(explode('/',$parse['path']));
        $explode[] = 'index.php';
        $path_index_file = '/'.implode('/',$explode);


        if(is_file(G5_PATH.str_replace(isset($_REQUEST['wv_dir_var'])?"/{$_REQUEST['wv_dir_var']}":"","",$path_index_file))  ){
            $parse['path'] = $path_index_file;
        }


        if(is_dir(G5_PATH.$parse['path']) or is_file(G5_PATH.$parse['path']) or !$config['cf_bbs_rewrite']){

            return $return_parse_array?$parse:wv_build_url($parse);
        }

        $path = ltrim($parse['path'],'/');

        if(!isset($parse['query'])){
            $parse['query'] = array();
        }


        $path_explode = explode('/',$path);

        if($path_explode[0]=='page'){

            if(preg_match("'^page/([0-9a-zA-Z_]+)$'",$path,$match)){

                $parse['path'] = '/index.php';
                $parse['query'] = array_merge(array('wv_page_id'=>$match[1]),$parse['query']);

            }

        }elseif($path_explode[0]=='shop'){

            if(preg_match("'^shop/list-([0-9a-z]+)$'",$path,$match)){

                $parse['path'] = '/shop/list.php';
                $parse['query'] = array_merge(array('ca_id'=>$match[1]),$parse['query']);

            }elseif(preg_match("'^shop/type-([0-9a-z]+)$ '",$path,$match)){

                $parse['path'] = '/shop/listtype.php';
                $parse['query'] = array_merge(array('type'=>$match[1]),$parse['query']);

            }elseif(preg_match("'^shop/([0-9a-zA-Z_\-]+)$'",$path,$match)){

                $parse['path'] = '/shop/item.php';
                $parse['query'] = array_merge(array('it_id'=>$match[1]),$parse['query']);

            }elseif(preg_match("'^shop/([^/]+)/$'",$path,$match)){

                $parse['path'] = '/shop/item.php';
                $parse['query'] = array_merge(array('it_seo_title'=>$match[1]),$parse['query']);

            }
        }elseif($path_explode[0]=='content'){
            if(preg_match("'^content/([0-9a-zA-Z_]+)$'",$path,$match)){

                $parse['path'] = '/bbs/content.php';
                $parse['query'] = array_merge(array('co_id'=>$match[1]),$parse['query']);

            }elseif(preg_match("'^content/([^/]+)/$'",$path,$match)){

                $parse['path'] = '/bbs/content.php';
                $parse['query'] = array_merge(array('co_seo_title'=>$match[1]),$parse['query']);

            }
        }elseif($path_explode[0]=='rss'){
            if(preg_match("'^rss/([0-9a-zA-Z_]+)$'",$path,$match)){

                $parse['path'] = '/bbs/rss.php';
                $parse['query'] = array_merge(array('bo_table'=>$match[1]),$parse['query']);

            }
        }else{
            if(preg_match("'^([0-9a-zA-Z_]+)$'",$path,$match)){

                $parse['path'] = '/bbs/board.php';
                $parse['query'] = array_merge(array('bo_table'=>$match[1]),$parse['query']);

            }elseif(preg_match("'^([0-9a-zA-Z_]+)/([^/]+)/$'",$path,$match)){

                $parse['path'] = '/bbs/board.php';
                $parse['query'] = array_merge(array('bo_table'=>$match[1],'wr_seo_title'=>$match[2]),$parse['query']);

            }elseif(preg_match("'^([0-9a-zA-Z_]+)/write$'",$path,$match)){

                $parse['path'] = '/bbs/write.php';
                $parse['query'] = array_merge(array('bo_table'=>$match[1]),$parse['query']);

            }elseif(preg_match("'^([0-9a-zA-Z_]+)/([0-9]+)$'",$path,$match)){

                $parse['path'] = '/bbs/board.php';
                $parse['query'] = array_merge(array('bo_table'=>$match[1],'wr_id'=>$match[2]),$parse['query']);

            }
        }



        return $return_parse_array?$parse:wv_build_url($parse);


    }
}
if(!function_exists('wv_parse_url')){
    function wv_parse_url( $url , $parse_str = true , $wv_replace_scheme_array = array('mailto','tel','javascript') ) {

        $url = urlencode($url);
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = urldecode($url);
        $parsed = parse_url($url);

        if(@in_array($parsed['host'],$wv_replace_scheme_array)){
            $parsed['scheme'] = $parsed['host'];
            $parsed['path'] = ' '.$parsed['port'].$parsed['path'];
            unset($parsed['port']);
            unset($parsed['host']);
        }




        if($parse_str and (isset($parsed['query']) and $parsed['query'])){
            parse_str($parsed['query'],$parse_str);
            $parse_str_new = array();
            foreach ($parse_str as $key=>$val){
                $key = str_replace('amp;','',$key);
                $parse_str_new[$key]=$val;
            }
            $parsed['query'] = $parse_str_new;

        }

        return $parsed;

    }
}
if(!function_exists('wv_build_url')){
    function wv_build_url( $parts) {
        if(isset($_REQUEST['wv_dir_var']) and strtok($parts['path'],'/')!=$_REQUEST['wv_dir_var'] and !wv_is_external_url($parts['host'])){
            $parts['path']="/{$_REQUEST['wv_dir_var']}".$parts['path'];
        }
        if(defined('WV_SUB_DIR') and WV_SUB_DIR and strtok($parts['path'],'/')!=WV_SUB_DIR and !wv_is_external_url($parts['host'])){
            $parts['path']="/".WV_SUB_DIR.$parts['path'];
        }
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            ((isset($parts['user']) || (isset($parts['host']) and isset($parts['scheme']))) ? '//' : '') .
            (isset($parts['user']) ? "{$parts['user']}" : '') .
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
            (isset($parts['user']) ? '@' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? $parts['path']:'') .
            ((isset($parts['query']) and is_array($parts['query']) and @count($parts['query'])) ? "?".(is_array($parts['query'])?http_build_query($parts['query']):$parts['query']) : (isset($parts['fragment'])?'?':'')) .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }
}
if(!function_exists('wv_remove_param_url')){
    function wv_remove_param_url($parse,$remove_param=array('wvd'), $return_parse_array = false){

        if(is_string($parse)){
            $parse = str_replace('&amp;', '&', $parse);
            $parse = wv_parse_url($parse);
        }

        if(!is_array($remove_param)){
            $remove_param = array($remove_param);
        }



        foreach ($remove_param as $param){
            unset($parse['query'][$param]);
        }
        $parse = array_filter($parse);

        return $return_parse_array?$parse:wv_build_url($parse);

    }
}
if(!function_exists('wv_change_param_url')){
    function wv_change_param_url($parse, $param, $return_parse_array = false){

        if(is_string($parse)){
            $parse = str_replace('&amp;', '&', $parse);
            $parse = wv_parse_url($parse);
        }


        if(!isset($parse['query'])){
            $parse['query'] = array();
        }

        $parse['query'] =  array_merge($parse['query'], $param);

        $parse = array_filter($parse);

        return $return_parse_array?$parse:wv_build_url($parse);


    }
}
if(!function_exists('wv_query_sort_url')){
    function wv_query_sort_url($parse, $return_parse_array = false){
        if(is_string($parse)){

            $parse = str_replace('&amp;', '&', $parse);
            $parse = wv_parse_url($parse);
        }


        if(!isset($parse['query'])){
            $parse['query'] = array();
        }

        ksort($parse['query']);
        $parse = array_filter($parse);

        return $return_parse_array?$parse:wv_build_url($parse);
    }
}
if(!function_exists('wv_add_scheme')){
    function wv_add_scheme($url, $scheme = 'http'){
//        dd(FILTER_SANITIZE_URL);
        $url = urlencode($url);
        $url = ltrim(filter_var($url, FILTER_SANITIZE_URL),'/');
        $url = urldecode($url);

        $parsed = wv_parse_url($url);



        if(@$parsed['scheme'] and !in_array($parsed['scheme'],array('http','https'))){
            return $url;
        }

        $is_external = wv_is_external_url($url);
        $server_scheme = wv_is_secure(true);
        if(wv_is_url($url) ){
            if($is_external){
                return $url;
            }

            if($parsed['scheme'] != $server_scheme){
                $parsed['scheme'] = $server_scheme;
            }

            return wv_build_url($parsed);
        }

        $check_path = '/'.ltrim($url,'/');

        if(is_dir(G5_PATH.$check_path) or is_file(G5_PATH.$check_path)){
            return $scheme.'://'.$_SERVER['HTTP_HOST'].$check_path;
        }

        $path_explode = explode('/',$url);

        if($path_explode[0]!='localhost' and strpos($path_explode[0],'.')===false){
//            $url = $_SERVER['HTTP_HOST'].'/'.ltrim(str_replace(array($_SERVER['HTTP_HOST'],preg_replace('/^https?:\/+/i', '', G5_DOMAIN) ), '', preg_replace('/^https?:\/+/i', '', $url)),'/');
            $url = $_SERVER['HTTP_HOST'].'/'.ltrim(preg_replace('/^https?:\/+/i', '', $url),'/');
        }

        return wv_add_scheme($scheme.'://'.$url);



    }
}
if(!function_exists('wv_is_secure')){
    function wv_is_secure($return_text = false) {
        $bool = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

        if(!$return_text){
            return $bool;
        }

        return $bool?'https':'http';
    }
}
if(!function_exists('wv_is_ajax')){
    function wv_is_ajax(){
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')?true:false;
    }
}


/**최신글*/
if(!function_exists('wv_latest')){
    function wv_latest($skin_dir='', $bo_table, $rows=10, $subject_len=40, $cache_time=1, $where='', $order='', $options='')
    {
        global $g5;

        if (!$skin_dir) $skin_dir = 'basic';

        $time_unit = 3600;  // 1시간으로 고정

        if(preg_match('#^theme/(.+)$#', $skin_dir, $match)) {
            if (G5_IS_MOBILE) {
                $latest_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                if(!is_dir($latest_skin_path))
                    $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
            } else {
                $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
            }
            $skin_dir = $match[1];
        } else {
            if(G5_IS_MOBILE) {
                $latest_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
                $latest_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
            } else {
                $latest_skin_path = G5_SKIN_PATH.'/latest/'.$skin_dir;
                $latest_skin_url  = G5_SKIN_URL.'/latest/'.$skin_dir;
            }
        }

        $caches = false;

        if(G5_USE_CACHE) {
            $cache_file_name = "latest-{$bo_table}-{$skin_dir}-{$rows}-{$subject_len}-".g5_cache_secret_key();
            $caches = g5_get_cache($cache_file_name, (int) $time_unit * (int) $cache_time);
            $cache_list = isset($caches['list']) ? $caches['list'] : array();
            g5_latest_cache_data($bo_table, $cache_list);
        }

        if( $caches === false ){

            $list = array();

            $board = get_board_db($bo_table, true);

            if( ! $board ){
                return '';
            }

            $bo_subject = get_text($board['bo_subject']);

            $tmp_write_table = $g5['write_prefix'] . $bo_table; // 게시판 테이블 전체이름

            $where = trim($where);
            $order = trim($order);

            $where_sql = '';
            if($where){
                $where_sql = " and ({$where})";
            }

            $order_sql = 'order by wr_num';
            if($order){
                $order_sql = "order by {$order}, wr_num";
            }
            $sql = " select * from {$tmp_write_table} where wr_is_comment = 0 {$where_sql} {$order_sql} limit 0, {$rows} ";

            $result = sql_query($sql);
            for ($i=0; $row = sql_fetch_array($result); $i++) {
                try {
                    unset($row['wr_password']);     //패스워드 저장 안함( 아예 삭제 )
                } catch (Exception $e) {
                }
                $row['wr_email'] = '';              //이메일 저장 안함
                if (strstr($row['wr_option'], 'secret')){           // 비밀글일 경우 내용, 링크, 파일 저장 안함
                    $row['wr_content'] = $row['wr_link1'] = $row['wr_link2'] = '';
                    $row['file'] = array('count'=>0);
                }
                $list[$i] = get_list($row, $board, $latest_skin_url, $subject_len);

                $list[$i]['first_file_thumb'] = (isset($row['wr_file']) && $row['wr_file']) ? get_board_file_db($bo_table, $row['wr_id'], 'bf_file, bf_content', "and bf_type in (1, 2, 3, 18) ", true) : array('bf_file'=>'', 'bf_content'=>'');
                $list[$i]['bo_table'] = $bo_table;
                // 썸네일 추가
                if($options && is_string($options)) {
                    $options_arr = explode(',', $options);
                    $thumb_width = $options_arr[0];
                    $thumb_height = $options_arr[1];
                    $thumb = get_list_thumbnail($bo_table, $row['wr_id'], $thumb_width, $thumb_height, false, true);
                    // 이미지 썸네일
                    if($thumb['src']) {
                        $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'">';
                        $list[$i]['img_thumbnail'] = '<a href="'.$list[$i]['href'].'" class="lt_img">'.$img_content.'</a>';
                        // } else {
                        //     $img_content = '<img src="'. G5_IMG_URL.'/no_img.png'.'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'" class="no_img">';
                    }
                }

                if(! isset($list[$i]['icon_file'])) $list[$i]['icon_file'] = '';
            }
            g5_latest_cache_data($bo_table, $list);

            if(G5_USE_CACHE) {

                $caches = array(
                    'list' => $list,
                    'bo_subject' => sql_escape_string($bo_subject),
                );

                g5_set_cache($cache_file_name, $caches, (int) $time_unit * (int) $cache_time);
            }
        } else {
            $list = $cache_list;
            $bo_subject = (is_array($caches) && isset($caches['bo_subject'])) ? $caches['bo_subject'] : '';
        }

        $skin_id = 'skin-'.uniqid();
        $skin_selector = '#'.$skin_id;

        ob_start();
        include $latest_skin_path.'/latest.skin.php';
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
if(!function_exists('wv_latest_data')){
    function wv_latest_data($bo_table, $rows=10, $subject_len=40, $where='', $order='')
    {
        global $g5;


        $list = array();

        $board = get_board_db($bo_table, true);

        if( ! $board ){
            return '';
        }

        $bo_subject = get_text($board['bo_subject']);

        $tmp_write_table = $g5['write_prefix'] . $bo_table; // 게시판 테이블 전체이름

        $where = trim($where);
        $order = trim($order);

        $where_sql = '';
        if($where){
            $where_sql = " and ({$where})";
        }

        $order_sql = 'order by wr_num';
        if($order){
            $order_sql = "order by {$order}, wr_num";
        }
        $sql = " select * from {$tmp_write_table} where wr_is_comment = 0 {$where_sql} {$order_sql} limit 0, {$rows} ";

        $result = sql_query($sql);
        for ($i=0; $row = sql_fetch_array($result); $i++) {
            try {
                unset($row['wr_password']);     //패스워드 저장 안함( 아예 삭제 )
            } catch (Exception $e) {
            }
            $row['wr_email'] = '';              //이메일 저장 안함
            if (strstr($row['wr_option'], 'secret')){           // 비밀글일 경우 내용, 링크, 파일 저장 안함
                $row['wr_content'] = $row['wr_link1'] = $row['wr_link2'] = '';
                $row['file'] = array('count'=>0);
            }
            $list[$i] = get_list($row, $board, $latest_skin_url, $subject_len);

            $list[$i]['first_file_thumb'] = (isset($row['wr_file']) && $row['wr_file']) ? get_board_file_db($bo_table, $row['wr_id'], 'bf_file, bf_content', "and bf_type in (1, 2, 3, 18) ", true) : array('bf_file'=>'', 'bf_content'=>'');
            $list[$i]['bo_table'] = $bo_table;
            // 썸네일 추가
            if($options && is_string($options)) {
                $options_arr = explode(',', $options);
                $thumb_width = $options_arr[0];
                $thumb_height = $options_arr[1];
                $thumb = get_list_thumbnail($bo_table, $row['wr_id'], $thumb_width, $thumb_height, false, true);
                // 이미지 썸네일
                if($thumb['src']) {
                    $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'">';
                    $list[$i]['img_thumbnail'] = '<a href="'.$list[$i]['href'].'" class="lt_img">'.$img_content.'</a>';
                    // } else {
                    //     $img_content = '<img src="'. G5_IMG_URL.'/no_img.png'.'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'" class="no_img">';
                }
            }

            if(! isset($list[$i]['icon_file'])) $list[$i]['icon_file'] = '';
        }

        return $list;
    }
}

if(!function_exists('wv_latest_group_cache_name')){
    function wv_latest_group_cache_name($bo_tables){
        $arr = array();
        foreach ($bo_tables as $bo_table){
            $arr[] = $bo_table.'__group';
        }
        return implode('-',$arr);
    }
}
if(!function_exists('wv_latest_group_from_new')){
    function wv_latest_group_from_new($skin_dir='', $gr_id, $rows=10, $subject_len=40, $cache_time=1, $options='')
    {
        global $g5;

        if (!$skin_dir) $skin_dir = 'basic';

        $time_unit = 3600;  // 1시간으로 고정

        $row = sql_fetch("SELECT GROUP_CONCAT(bo_table SEPARATOR ',') as bo_tables FROM {$g5['board_table']} WHERE gr_id='{$gr_id}'");
        $bo_tables = explode(',',$row['bo_tables']);
        $cache_title = wv_latest_group_cache_name($bo_tables);

        if(preg_match('#^theme/(.+)$#', $skin_dir, $match)) {
            if (G5_IS_MOBILE) {
                $latest_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                if(!is_dir($latest_skin_path))
                    $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
            } else {
                $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
            }
            $skin_dir = $match[1];
        } else {
            if(G5_IS_MOBILE) {
                $latest_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
                $latest_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
            } else {
                $latest_skin_path = G5_SKIN_PATH.'/latest/'.$skin_dir;
                $latest_skin_url  = G5_SKIN_URL.'/latest/'.$skin_dir;
            }
        }

        $caches = false;

        if(G5_USE_CACHE) {
            $cache_file_name = "latest-{$cache_title}-{$skin_dir}-{$rows}-{$subject_len}-".g5_cache_secret_key();
            $caches = g5_get_cache($cache_file_name, (int) $time_unit * (int) $cache_time);
            $cache_list = isset($caches['list']) ? $caches['list'] : array();
        }

        if( $caches === false ){

            $list = array();



            $sql_common = " from {$g5['board_new_table']} a, {$g5['board_table']} b, {$g5['group_table']} c where a.bo_table = b.bo_table and b.gr_id = c.gr_id and b.bo_use_search = 1 ";
            $sql_common .= " and b.gr_id = '$gr_id' ";
            $sql_common .= " and a.wr_id = a.wr_parent ";
            $sql_order = " order by a.bn_id desc ";
            $sql = " select a.*, b.*, c.gr_subject, c.gr_id {$sql_common} {$sql_order} limit 0, {$rows}";

            $result = sql_query($sql,1);

            for ($i=0; $row=sql_fetch_array($result); $i++) {



                $bo_subject = get_text($row['bo_subject']);
                $gr_subject = $row['gr_subject'];

                $row2 = get_write($g5['write_prefix'].$row['bo_table'],$row['wr_id']);

                try {
                    unset($row2['wr_password']);     //패스워드 저장 안함( 아예 삭제 )
                } catch (Exception $e) {
                }
                $row2['wr_email'] = '';              //이메일 저장 안함
                if (strstr($row2['wr_option'], 'secret')){           // 비밀글일 경우 내용, 링크, 파일 저장 안함
                    $row2['wr_content'] = $row2['wr_link1'] = $row2['wr_link2'] = '';
                    $row2['file'] = array('count'=>0);
                }
                $list[$i] = get_list($row2, $row, $latest_skin_url, $subject_len);

                $list[$i]['first_file_thumb'] = (isset($row2['wr_file']) && $row2['wr_file']) ? get_board_file_db($row2['bo_table'], $row2['wr_id'], 'bf_file, bf_content', "and bf_type in (1, 2, 3, 18) ", true) : array('bf_file'=>'', 'bf_content'=>'');
                $list[$i]['bo_table'] = $row['bo_table'];

                // 썸네일 추가
                if($options && is_string($options)) {

                    $options_arr = explode(',', $options);
                    $thumb_width = $options_arr[0];
                    $thumb_height = $options_arr[1];
                    $thumb = get_list_thumbnail($row2['bo_table'], $row2['wr_id'], $thumb_width, $thumb_height, false, true);
                    // 이미지 썸네일
                    if($thumb['src']) {
                        $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'">';
                        $list[$i]['img_thumbnail'] = '<a href="'.$list[$i]['href'].'" class="lt_img">'.$img_content.'</a>';
                        // } else {
                        //     $img_content = '<img src="'. G5_IMG_URL.'/no_img.png'.'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'" class="no_img">';
                    }
                }

                if(! isset($list[$i]['icon_file'])) $list[$i]['icon_file'] = '';
            }




            if(G5_USE_CACHE) {

                $caches = array(
                    'list' => $list,
                    'gr_subject' => sql_escape_string($gr_subject),
                );

                g5_set_cache($cache_file_name, $caches, (int) $time_unit * (int) $cache_time);
            }
        } else {
            $list = $cache_list;
            $gr_subject = (is_array($caches) && isset($caches['gr_subject'])) ? $caches['gr_subject'] : '';
        }

        ob_start();
        include $latest_skin_path.'/latest.skin.php';
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
if(!function_exists('wv_latest_group_from_union')){
    function wv_latest_group_from_union($skin_dir='', $bo_tables, $rows=10, $subject_len=40, $cache_time=1, $options='')
    {
        global $g5;

        if (!$skin_dir) $skin_dir = 'basic';

        $time_unit = 3600;  // 1시간으로 고정

        if(is_string($bo_tables)){
            $row = sql_fetch("SELECT GROUP_CONCAT(bo_table SEPARATOR ',') as bo_tables FROM {$g5['board_table']} WHERE gr_id='{$bo_tables}'");
            $bo_tables = explode(',',$row['bo_tables']);
        }

        $cache_title = wv_latest_group_cache_name($bo_tables);


        if(preg_match('#^theme/(.+)$#', $skin_dir, $match)) {
            if (G5_IS_MOBILE) {
                $latest_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                if(!is_dir($latest_skin_path))
                    $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
            } else {
                $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
                $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
            }
            $skin_dir = $match[1];
        } else {
            if(G5_IS_MOBILE) {
                $latest_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
                $latest_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
            } else {
                $latest_skin_path = G5_SKIN_PATH.'/latest/'.$skin_dir;
                $latest_skin_url  = G5_SKIN_URL.'/latest/'.$skin_dir;
            }
        }

        $caches = false;

        if(G5_USE_CACHE) {
            $cache_file_name = "latest-{$cache_title}-{$skin_dir}-{$rows}-{$subject_len}-".g5_cache_secret_key();
            $caches = g5_get_cache($cache_file_name, (int) $time_unit * (int) $cache_time);
            $cache_list = isset($caches['list']) ? $caches['list'] : array();
        }

        if( $caches === false ){

            $list = $tmp_list = array();




            if (is_array($bo_tables)) {
                foreach ($bo_tables as $key => $bo_table) {
                    $board = get_board_db($bo_table, true);
                    $tmp_write_table = $g5['write_prefix'] . $bo_table;
                    $sql = " select * from {$tmp_write_table} where wr_is_comment = '0' order by wr_num limit 0, {$rows} ";
                    $result = sql_query($sql);
                    while($row = sql_fetch_array($result)) {
                        $row['bo_table'] = $bo_table;
                        $row['board'] = $board;
                        $tmp_list[] = $row;
                    }
                }
            }

            foreach ($tmp_list as $key => $record) {
                $cols[$key] = $record['wr_datetime'];
            }

            array_multisort($cols, SORT_DESC, $tmp_list);

            $i=0;

            foreach ($tmp_list as $key => $row) {

                if ($rows < $i) {
                    break;
                }

                try {
                    unset($row['wr_password']);     //패스워드 저장 안함( 아예 삭제 )
                } catch (Exception $e) {
                }
                $row['wr_email'] = '';              //이메일 저장 안함
                if (strstr($row['wr_option'], 'secret')){           // 비밀글일 경우 내용, 링크, 파일 저장 안함
                    $row['wr_content'] = $row['wr_link1'] = $row['wr_link2'] = '';
                    $row['file'] = array('count'=>0);
                }
                $list[$i] = get_list($row, $row['board'], $latest_skin_url, $subject_len);

                $list[$i]['first_file_thumb'] = (isset($row['wr_file']) && $row['wr_file']) ? get_board_file_db($row['bo_table'], $row['wr_id'], 'bf_file, bf_content', "and bf_type in (1, 2, 3, 18) ", true) : array('bf_file'=>'', 'bf_content'=>'');

                // 썸네일 추가
                if($options && is_string($options)) {
                    $options_arr = explode(',', $options);
                    $thumb_width = $options_arr[0];
                    $thumb_height = $options_arr[1];
                    $thumb = get_list_thumbnail($row['bo_table'], $row['wr_id'], $thumb_width, $thumb_height, false, true);
                    // 이미지 썸네일
                    if($thumb['src']) {
                        $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'">';
                        $list[$i]['img_thumbnail'] = '<a href="'.$list[$i]['href'].'" class="lt_img">'.$img_content.'</a>';
                        // } else {
                        //     $img_content = '<img src="'. G5_IMG_URL.'/no_img.png'.'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'" class="no_img">';
                    }
                }

                if(! isset($list[$i]['icon_file'])) $list[$i]['icon_file'] = '';

                $i++;

            }


            if(G5_USE_CACHE) {

                $caches = array(
                    'list' => $list,
                );

                g5_set_cache($cache_file_name, $caches, (int) $time_unit * (int) $cache_time);
            }
        } else {
            $list = $cache_list;
        }

        ob_start();
        include $latest_skin_path.'/latest.skin.php';
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}


/** 데이터 생성 */
/**

$row = array(
'wv_old_id'=>$row['wv_old_id'],
'notice'=>$post_notice,
'ca_name'=>$post_ca_name,
'wr_subject'=>$post_wr_subject,
'wr_content'=>$post_wr_content,
'wr_name'=>$post_wr_name,
'wr_password'=>$post_wr_password,
'wr_email'=>$post_wr_email,
'mb_id'=>$post_mb_id,
'wr_id'=>$row['ip_address'],
'wr_hit'=>$row['view_count'],
'secret'=>'secret',
'wr_1'=>hyphen_hp_number($row['tel']),
'wr_datetime'=>$regdate,
'wr_last'=>$regdate,
'extend_data' => array()
);
$external_attach_files = array();
foreach ($row['files'] as $f){
$external_attach_files['bf_name'][] = basename($f);
$external_attach_files['bf_external_url'][] = $f;
$external_attach_files['bf_datetime'][] = $row['wr_datetime'];
}
unset($row['files']);
$row['wr_file'] = count($external_attach_files['bf_name']);
$row = array_merge($row,$external_attach_files);
$res = wv_write_board($bo_table, $row,$_FILES);
 */
if(!function_exists('wv_write_board')){
    function wv_write_board($bo_table, $post, $files=''){
        global $g5,$config,$is_admin;
        $post    = array_map_deep(G5_ESCAPE_FUNCTION,  $post);
        $wr_id = $post['wr_id'];
        $w = $post['w'];

        if ($wr_id and $w == '') {
            $w = 'u';
        }
        $board = sql_fetch(" select * from {$g5['board_table']} where bo_table = '$bo_table' ");
        if(!$board['bo_table'])return($bo_table.' : bo_table 이 존재하지 않습니다.');
        $write_table = $g5['write_prefix'].$bo_table;

        if($post['wr_content']==''){
            $post['wr_content'] = '&nbsp;';
        }
        if(($w != 'u') and $post['wv_old_id']){
            if(!sql_query(" select wv_old_id from $write_table limit 1 ", false)) {
                sql_query(" ALTER TABLE $write_table
                    ADD `wv_old_id` int NOT NULL DEFAULT '0'", true);
            }
            $is_written = sql_fetch("select wr_id from $write_table where wv_old_id = '{$post['wv_old_id']}'");
            if($is_written['wr_id']){
                return true;
            }
        }

        $ca_name = trim($post['ca_name']);
        if($ca_name){
            if(!$board['bo_use_category']) return($bo_table.' : 해당 게시판이 분류를 사용하지 않습니다.');
            $categories = array_map('trim', explode("|", $board['bo_category_list']));
            if(!in_array($ca_name,$categories)) return( $ca_name.' : 해당 게시판의 분류에 존재하지 않습니다.');
        }


        $wr_subject = '';
        if (isset($post['wr_subject'])) {
            $wr_subject = substr(trim($post['wr_subject']),0,255);
            $wr_subject = preg_replace("#[\\\]+$#", "", $wr_subject);
        }
        if ($wr_subject == '') {
            $msg[] = '<strong>제목</strong>을 입력하세요.';
        }

        $wr_content = '';
        if (isset($post['wr_content'])) {
            $wr_content = substr(trim($post['wr_content']),0,65536);
            $wr_content = preg_replace("#[\\\]+$#", "", $wr_content);
            $wr_content = addslashes($wr_content);
        }
        if ($wr_content == '') {
            $msg[] = '<strong>내용</strong>을 입력하세요.';
        }

        $wr_link1 = '';
        if (isset($post['wr_link1'])) {
            $wr_link1 = substr($post['wr_link1'],0,1000);
            $wr_link1 = trim(strip_tags($wr_link1));
            $wr_link1 = preg_replace("#[\\\]+$#", "", $wr_link1);
        }

        $wr_link2 = '';
        if (isset($post['wr_link2'])) {
            $wr_link2 = substr($post['wr_link2'],0,1000);
            $wr_link2 = trim(strip_tags($wr_link2));
            $wr_link2 = preg_replace("#[\\\]+$#", "", $wr_link2);
        }

        $msg = implode('<br>', $msg);
        if ($msg) {
            return ($msg);
        }

        // 090710
        if (substr_count($wr_content, '&#') > 50) {
            return('내용에 올바르지 않은 코드가 다수 포함되어 있습니다.');
        }

        $upload_max_filesize = ini_get('upload_max_filesize');

        if (empty($post)) {
            return("파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\npost_max_size=".ini_get('post_max_size')." , upload_max_filesize=".$upload_max_filesize."\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.");
        }

        $notice_array = explode(",", $board['bo_notice']);
        $wr_password = isset($post['wr_password']) ? $post['wr_password'] : '';
        $bf_content = isset($post['bf_content']) ? (array) $post['bf_content'] : array();
        $post['html'] = isset($post['html']) ? clean_xss_tags($post['html'], 1, 1) : '';
        $post['secret'] = isset($post['secret']) ? clean_xss_tags($post['secret'], 1, 1) : '';
        $post['mail'] = isset($post['mail']) ? clean_xss_tags($post['mail'], 1, 1) : '';

        $write = array();
        if ($w == 'u' || $w == 'r') {
            $wr = get_write($write_table, $wr_id);
            if (!$wr['wr_id']) {
                return("글이 존재하지 않습니다.\\n글이 삭제되었거나 이동하였을 수 있습니다.");
            }
            $write = get_write($write_table, $wr_id);
        }

        if($post['mb_id']){
            $member = get_member($post['mb_id']);
        }else{
            global $member;
        }

//        $is_admin = is_admin($member['mb_id']);

        if (!$is_admin && !$board['bo_use_secret'] && (stripos($post['html'], 'secret') !== false || stripos($post['secret'], 'secret') !== false || stripos($post['mail'], 'secret') !== false)) {
            return ('비밀글 미사용 게시판 이므로 비밀글로 등록할 수 없습니다.');
        }


        $secret = '';
        if (isset($post['secret']) && $post['secret']) {
            if(preg_match('#secret#', strtolower($post['secret']), $matches))
                $secret = $matches[0];
        }

        if (!$is_admin && $board['bo_use_secret'] == 2) {
            $secret = 'secret';
        }

        $html = 'html1';
        if (isset($post['html']) && $post['html']) {
            if(preg_match('#html(1|2)#', strtolower($post['html']), $matches))
                $html = $matches[0];
        }

        $mail = '';
        if (isset($post['mail']) && $post['mail']) {
            if(preg_match('#mail#', strtolower($post['mail']), $matches))
                $mail = $matches[0];
        }

        $notice = '';
        if (isset($post['notice']) && $post['notice']) {
            $notice = $post['notice'];
        }

        for ($i=1; $i<=10; $i++) {
            $var = "wr_$i";
            $$var = "";
            if (isset($post['wr_'.$i]) && settype($post['wr_'.$i], 'string')) {
                $$var = trim($post['wr_'.$i]);
            }
        }


        run_event('write_update_before', $board, $wr_id, $w, $qstr);

        // 외부이미지 다운로드
        $external_img_in_content = wv_get_src(stripslashes($post['wr_content']));



        foreach ($external_img_in_content as $img_path_in_cont){
            $img_url = $img_path_in_cont;
            if(!wv_is_url($img_url) and $post['old_domain']){
                $img_url = $post['old_domain'].'/'.ltrim($img_url,'/');
            }

            $parse_url = wv_parse_url($img_url);

            if(wv_is_external_url($img_url) and !file_exists(G5_PATH.'/'.$parse_url['path'])){

                $ym = date('ym', G5_SERVER_TIME);
                $data_dir = G5_DATA_PATH.'/'.G5_EDITOR_DIR.'/'.$ym;
                @mkdir($data_dir, G5_DIR_PERMISSION);
                @chmod($data_dir, G5_DIR_PERMISSION);

                $download_result = wv_file_get_contents($img_url,$data_dir);

                if($download_result['result']==false){

                    $file_upload_msg .=  $post['wr_subject'].' - '.$download_result['content'];
                    continue;
                }

                $wr_content = str_replace($img_path_in_cont,str_replace(G5_PATH,'',$download_result['content']),$wr_content); 

            }

        }



        if ($w == '' || $w == 'u') {

            // 외부에서 글을 등록할 수 있는 버그가 존재하므로 공지는 관리자만 등록이 가능해야 함
            if (!$is_admin && $notice) {
                return ('관리자만 공지할 수 있습니다.');
            }

            //회원 자신이 쓴글을 수정할 경우 공지가 풀리는 경우가 있음
            if($w =='u' && !$is_admin && $board['bo_notice'] && in_array($wr['wr_id'], $notice_array)){
                $notice = 1;
            }

            // 김선용 1.00 : 글쓰기 권한과 수정은 별도로 처리되어야 함
            if($w =='u' && $member['mb_id'] && $wr['mb_id'] === $member['mb_id']) {
                ;
            } else if ($member['mb_level'] < $board['bo_write_level']) {
//                return ('글을 쓸 권한이 없습니다.');
            }

        } else if ($w == 'r') {

            if (in_array((int)$wr_id, $notice_array)) {
                return ('공지에는 답변 할 수 없습니다.');
            }

            if ($member['mb_level'] < $board['bo_reply_level']) {
                return ('글을 답변할 권한이 없습니다.');
            }

            // 게시글 배열 참조
            $reply_array = &$wr;

            // 최대 답변은 테이블에 잡아놓은 wr_reply 사이즈만큼만 가능합니다.
            if (strlen($reply_array['wr_reply']) == 10) {
                return ("더 이상 답변하실 수 없습니다.\\n답변은 10단계 까지만 가능합니다.");
            }

            $reply_len = strlen($reply_array['wr_reply']) + 1;
            if ($board['bo_reply_order']) {
                $begin_reply_char = 'A';
                $end_reply_char = 'Z';
                $reply_number = +1;
                $sql = " select MAX(SUBSTRING(wr_reply, $reply_len, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
            } else {
                $begin_reply_char = 'Z';
                $end_reply_char = 'A';
                $reply_number = -1;
                $sql = " select MIN(SUBSTRING(wr_reply, {$reply_len}, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
            }
            if ($reply_array['wr_reply']) $sql .= " and wr_reply like '{$reply_array['wr_reply']}%' ";
            $row = sql_fetch($sql);

            if (!$row['reply']) {
                $reply_char = $begin_reply_char;
            } else if ($row['reply'] == $end_reply_char) { // A~Z은 26 입니다.
                return ("더 이상 답변하실 수 없습니다.\\n답변은 26개 까지만 가능합니다.");
            } else {
                $reply_char = chr(ord($row['reply']) + $reply_number);
            }

            $reply = $reply_array['wr_reply'] . $reply_char;

        } else {
            return('w 값이 제대로 넘어오지 않았습니다.');
        }

        if (!isset($post['wr_subject']) || !trim($post['wr_subject']))
            return ('제목을 입력하여 주십시오.');


        $wr_seo_title = exist_seo_title_recursive('bbs', generate_seo_title($wr_subject), $write_table, $wr_id);


        $options = array($html,$secret,$mail);
        $wr_option = implode(',', array_filter(array_map('trim', $options)));

        $extend_data_sql = '';
        $extend_data_arr = array();
        if(isset($post['extend_data'])){
            if(!is_array($post['extend_data']))return('확장데이터는 배열형태여야 합니다.');
            foreach ($post['extend_data'] as $key => $val){
                $extend_data_arr[] = " {$key} = '$val' ";
            }
        }

        if(isset($post['wv_old_id']) and $post['wv_old_id']){
            $extend_data_arr[] = " wv_old_id = '{$post['wv_old_id']}' ";
        }

        if(count($extend_data_arr)){
            $extend_data_sql = ' , '.implode(' , ',$extend_data_arr);
        }


        if ($w == '' || $w == 'r') {

            if ($member['mb_id']) {
                $mb_id = $member['mb_id'];
                $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
                $wr_password = '';
                $wr_email = addslashes($member['mb_email']);
                $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));
            } else {
                $mb_id = '';
                // 비회원의 경우 이름이 누락되는 경우가 있음
                $wr_name = clean_xss_tags(trim($post['wr_name']));
//                if (!$wr_name)
//                    return ('이름은 필히 입력하셔야 합니다.');
                $wr_password = get_encrypt_string($wr_password);
                $wr_email = get_email_address(trim($post['wr_email']));
                $wr_homepage = clean_xss_tags($wr_homepage);
            }

            if ($w == 'r') {
                // 답변의 원글이 비밀글이라면 비밀번호는 원글과 동일하게 넣는다.
                if ($secret)
                    $wr_password = $wr['wr_password'];

                $wr_id = $wr_id . $reply;
                $wr_num = $write['wr_num'];
                $wr_reply = $reply;
            } else {
                $wr_num = get_next_num($write_table);
                $wr_reply = '';
            }


            $wr_datetime = $post['wr_datetime']?$post['wr_datetime']:G5_TIME_YMDHIS;
            $wr_last = $post['wr_last']?$post['wr_last']:G5_TIME_YMDHIS;
            $wr_ip = $post['wr_ip']?$post['wr_ip']:$_SERVER['REMOTE_ADDR'];


            if (!sql_query(" SELECT wr_seo_title from {$write_table} limit 1 ", false)) {
                sql_query(" ALTER TABLE {$write_table} ADD `wr_seo_title` varchar(255) NOT NULL DEFAULT '' ");
            }

            $sql = " insert into $write_table
                set wr_num = '$wr_num',
                     wr_reply = '$wr_reply',
                     wr_comment = 0,
                     ca_name = '$ca_name',
                     wr_option = '$wr_option',
                     wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_seo_title = '$wr_seo_title',
                     wr_link1 = '$wr_link1',
                     wr_link2 = '$wr_link2',
                     wr_link1_hit = 0,
                     wr_link2_hit = 0,
                     wr_hit = 0,
                     wr_good = 0,
                     wr_nogood = 0,
                     mb_id = '{$member['mb_id']}',
                     wr_password = '$wr_password',
                     wr_name = '$wr_name',
                     wr_email = '$wr_email',
                     wr_homepage = '$wr_homepage',
                     wr_datetime = '".$wr_datetime."',
                     wr_last = '".$wr_last."',
                     wr_ip = '{$wr_ip}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10' {$extend_data_sql} ";

            $result = sql_query($sql,1);

            if(!$result)return false;

            $wr_id = sql_insert_id();

            // 부모 아이디에 UPDATE
            sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

            // 새글 INSERT
            sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

            // 게시글 1 증가
            sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}'");

            // 쓰기 포인트 부여
            if ($w == '') {
                if ($notice) {
                    $bo_notice = $wr_id.($board['bo_notice'] ? ",".$board['bo_notice'] : '');
                    sql_query(" update {$g5['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");
                }

                insert_point($member['mb_id'], $board['bo_write_point'], "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
            } else {
                // 답변은 코멘트 포인트를 부여함
                // 답변 포인트가 많은 경우 코멘트 대신 답변을 하는 경우가 많음
                insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id} 글답변", $bo_table, $wr_id, '쓰기');
            }
        }  else if ($w == 'u') {


            $return_url = get_pretty_url($bo_table, $wr_id);


            if ($is_admin == 'super') // 최고관리자 통과
                ;
            else if ($is_admin == 'group') { // 그룹관리자
                $mb = get_member($write['mb_id']);
                if ($member['mb_id'] != $group['gr_admin']) // 자신이 관리하는 그룹인가?
                    return ('자신이 관리하는 그룹의 게시판이 아니므로 수정할 수 없습니다.');
                else if ($member['mb_level'] < $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                    return ('자신의 권한보다 높은 권한의 회원이 작성한 글은 수정할 수 없습니다.');
            } else if ($is_admin == 'board') { // 게시판관리자이면
                $mb = get_member($write['mb_id']);
                if ($member['mb_id'] != $board['bo_admin']) // 자신이 관리하는 게시판인가?
                    return ('자신이 관리하는 게시판이 아니므로 수정할 수 없습니다.');
                else if ($member['mb_level'] < $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                    return ('자신의 권한보다 높은 권한의 회원이 작성한 글은 수정할 수 없습니다.');
            } else if ($member['mb_id']) {
                if ($member['mb_id'] != $write['mb_id'])
                    return ('자신의 글이 아니므로 수정할 수 없습니다.');
            } else {
                if ($write['mb_id'])
                    return ('로그인 후 수정하세요.');
            }

            if ($member['mb_id']) {
                // 자신의 글이라면
                if ($member['mb_id'] === $wr['mb_id']) {
                    $mb_id = $member['mb_id'];
                    $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
                    $wr_email = addslashes($member['mb_email']);
                    $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));
                } else {
                    $mb_id = $wr['mb_id'];
                    if (isset($post['wr_name']) && $post['wr_name'])
                        $wr_name = clean_xss_tags(trim($post['wr_name']));
                    else
                        $wr_name = addslashes(clean_xss_tags($wr['wr_name']));
                    if (isset($post['wr_email']) && $post['wr_email'])
                        $wr_email = get_email_address(trim($post['wr_email']));
                    else
                        $wr_email = addslashes($wr['wr_email']);
                    if (isset($post['wr_homepage']) && $post['wr_homepage'])
                        $wr_homepage = addslashes(clean_xss_tags($post['wr_homepage']));
                    else
                        $wr_homepage = addslashes(clean_xss_tags($wr['wr_homepage']));
                }
            } else {
                $mb_id = "";
                // 비회원의 경우 이름이 누락되는 경우가 있음
//                if (!trim($wr_name)) return("이름은 필히 입력하셔야 합니다.");
                $wr_name = clean_xss_tags(trim($post['wr_name']));
                $wr_email = get_email_address(trim($post['wr_email']));
            }

            $sql_password = $wr_password ? " , wr_password = '" . get_encrypt_string($wr_password) . "' " : "";

            $sql_ip = '';
            if (!$is_admin)
                $sql_ip = " , wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";
            $set_field_array = array('ca_name','wr_option','wr_subject','wr_content','wr_seo_title','wr_link1','wr_link2','mb_id','wr_name','wr_name','wr_email','wr_homepage',
                'wr_1','wr_2','wr_3','wr_4','wr_5','wr_6','wr_7','wr_8','wr_9','wr_10');

            $set_arr=array();
            foreach ($set_field_array as $field){
                if(isset($post[$field])){
                    $set_arr[] = " {$field} = '".$post[$field]."' ";
                }
            }
            $sql_sql=implode(',', $set_arr);
            $sql = " update {$write_table}
                set {$sql_sql}
                     {$sql_ip}
                     {$sql_password}
                     {$extend_data_sql}
              where wr_id = '{$wr['wr_id']}' ";

            sql_query($sql);

            // 분류가 수정되는 경우 해당되는 코멘트의 분류명도 모두 수정함
            // 코멘트의 분류를 수정하지 않으면 검색이 제대로 되지 않음
            $sql = " update {$write_table} set ca_name = '{$ca_name}' where wr_parent = '{$wr['wr_id']}' ";
            sql_query($sql);



            $bo_notice = board_notice($board['bo_notice'], $wr_id, $notice);
            sql_query(" update {$g5['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");

            // 글을 수정한 경우에는 제목이 달라질수도 있으니 static variable 를 새로고침합니다.
            $write = get_write( $write_table, $wr['wr_id'], false);
        }



        wv_write_board_file($bo_table,$post,$files,$wr_id,$is_admin);




        if ($secret)
            set_session("ss_secret_{$bo_table}_{$wr_num}", TRUE);

        // 메일발송 사용 (수정글은 발송하지 않음)
        if (!($w == 'u' || $w == 'cu') && $config['cf_email_use'] && $board['bo_use_email']) {

            // 관리자의 정보를 얻고
            $super_admin = get_admin('super');
            $group_admin = get_admin('group');
            $board_admin = get_admin('board');

            $wr_subject = get_text(stripslashes($wr_subject));

            $tmp_html = 0;
            if (strstr($html, 'html1'))
                $tmp_html = 1;
            else if (strstr($html, 'html2'))
                $tmp_html = 2;

            $wr_content = conv_content(conv_unescape_nl(stripslashes($wr_content)), $tmp_html);

            $warr = array( ''=>'입력', 'u'=>'수정', 'r'=>'답변', 'c'=>'코멘트', 'cu'=>'코멘트 수정' );
            $str = $warr[$w];

            $subject = '['.$config['cf_title'].'] '.$board['bo_subject'].' 게시판에 '.$str.'글이 올라왔습니다.';

            $link_url = get_pretty_url($bo_table, $wr_id, $qstr);

            include_once(G5_LIB_PATH.'/mailer.lib.php');

            ob_start();
            include (G5_BBS_PATH.'/write_update_mail.php');
            $content = ob_get_contents();
            ob_end_clean();

            $array_email = array();
            // 게시판관리자에게 보내는 메일
            if ($config['cf_email_wr_board_admin']) $array_email[] = $board_admin['mb_email'];
            // 게시판그룹관리자에게 보내는 메일
            if ($config['cf_email_wr_group_admin']) $array_email[] = $group_admin['mb_email'];
            // 최고관리자에게 보내는 메일
            if ($config['cf_email_wr_super_admin']) $array_email[] = $super_admin['mb_email'];

            // 원글게시자에게 보내는 메일
            if ($config['cf_email_wr_write']) {
                if($w == '')
                    $wr['wr_email'] = $wr_email;

                $array_email[] = $wr['wr_email'];
            }

            // 옵션에 메일받기가 체크되어 있고, 게시자의 메일이 있다면
            if (isset($wr['wr_option']) && isset($wr['wr_email'])) {
                if (strstr($wr['wr_option'], 'mail') && $wr['wr_email'])
                    $array_email[] = $wr['wr_email'];
            }

            // 중복된 메일 주소는 제거
            $unique_email = array_unique($array_email);
            $unique_email = run_replace('write_update_mail_list', array_values($unique_email), $board, $wr_id);

            for ($i=0; $i<count($unique_email); $i++) {
                mailer($wr_name, $wr_email, $unique_email[$i], $subject, $content, 1);
            }
        }

        delete_cache_latest($bo_table);

        $redirect_url = run_replace('write_update_move_url', short_url_clean(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr), $board, $wr_id, $w, $qstr, $file_upload_msg);



        run_event('write_update_after', $board, $wr_id, $w, $qstr, $redirect_url);

        if ($file_upload_msg){
            return($file_upload_msg);
        }



        return $wr_id;

    }
}
if(!function_exists('wv_write_board_file')){
    function wv_write_board_file($bo_table,$post,$files,$wr_id,$is_admin){

        global $g5,$config;

        $board = sql_fetch(" select * from {$g5['board_table']} where bo_table = '$bo_table' ");
        if(!$board['bo_table'])alert($bo_table.' : bo_table 이 존재하지 않습니다.');
        $write_table = $g5['write_prefix'].$bo_table;

        $file_count   = 0;
        $upload_count = (isset($files['bf_file']['name']) && is_array($files['bf_file']['name'])) ? count($files['bf_file']['name']) : 0;

        for ($i=0; $i<$upload_count; $i++) {
            if($files['bf_file']['name'][$i] && is_uploaded_file($files['bf_file']['tmp_name'][$i]))
                $file_count++;
        }

        if($w == 'u') {
            $file = get_file($bo_table, $wr_id);
            if($file_count && (int)$file['count'] > $board['bo_upload_count'])
                alert('기존 파일을 삭제하신 후 첨부파일을 '.number_format($board['bo_upload_count']).'개 이하로 업로드 해주십시오.');
        } else {
            if($file_count > $board['bo_upload_count'])
                alert('첨부파일을 '.number_format($board['bo_upload_count']).'개 이하로 업로드 해주십시오.');
        }

        // 디렉토리가 없다면 생성합니다. (퍼미션도 변경하구요.)
        @mkdir(G5_DATA_PATH.'/file/'.$bo_table, G5_DIR_PERMISSION);
        @chmod(G5_DATA_PATH.'/file/'.$bo_table, G5_DIR_PERMISSION);

        $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

        // 가변 파일 업로드
        $file_upload_msg = '';
        $upload = array();
        if(isset($post['bf_external_url']) && is_array($post['bf_external_url'])){
            if(!is_array($files)){
                $files = array();
            }
            foreach ($post['bf_external_url'] as $i=>$val){
                $files['bf_file']['name'][$i] = $post['bf_name'][$i]?$post['bf_name'][$i]:wv_get_file_base_name($post['bf_external_url'][$i]);
                $files['bf_file']['bf_external_url'][$i] = $post['bf_external_url'][$i];
                $files['bf_file']['bf_datetime'][$i] = $post['bf_datetime'][$i];
            }

            foreach ($files['bf_file'] as $key=>&$file_each){

                ksort($file_each);

            }

        }


        if(isset($files['bf_file']['name']) && is_array($files['bf_file']['name'])) {
            foreach ( $files['bf_file']['name'] as $i => $val){




                $upload[$i]['file']     = '';
                $upload[$i]['source']   = '';
                $upload[$i]['filesize'] = 0;
                $upload[$i]['image']    = array();
                $upload[$i]['image'][0] = 0;
                $upload[$i]['image'][1] = 0;
                $upload[$i]['image'][2] = 0;
                $upload[$i]['fileurl'] = '';
                $upload[$i]['thumburl'] = '';
                $upload[$i]['storage'] = '';

                // 삭제에 체크가 되어있다면 파일을 삭제합니다.
                if (isset($post['bf_file_del'][$i]) && $post['bf_file_del'][$i]) {
                    $upload[$i]['del_check'] = true;

                    $row = sql_fetch(" select * from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");

                    $delete_file = run_replace('delete_file_path', G5_DATA_PATH.'/file/'.$bo_table.'/'.str_replace('../', '', $row['bf_file']), $row);
                    if( file_exists($delete_file) ){
                        @unlink($delete_file);
                    }
                    // 썸네일삭제
                    if(preg_match("/\.({$config['cf_image_extension']})$/i", $row['bf_file'])) {
                        delete_board_thumbnail($bo_table, $row['bf_file']);
                    }
                }
                else
                    $upload[$i]['del_check'] = false;


                if(isset($files['bf_file']['bf_external_url'][$i])) {



//                        $download_result = wv_parse_download($files['bf_file']['bf_external_url'][$i],G5_DATA_PATH.'/file/'.$bo_table);
                    $download_result = wv_file_get_contents($files['bf_file']['bf_external_url'][$i],G5_DATA_PATH.'/file/'.$bo_table);

                    if($download_result['result']==false){
                        $file_upload_msg .=  $download_result['content'];
                        continue;
                    }

                    $downloaded_file_path = $download_result['content'];

                    $timg = @getimagesize($downloaded_file_path);

                    // image type
                    if ( preg_match("/\.({$config['cf_image_extension']})$/i", $downloaded_file_path) ||
                        preg_match("/\.({$config['cf_flash_extension']})$/i", $downloaded_file_path) ) {
                        if ($timg['2'] < 1 || $timg['2'] > 18){
                            $file_upload_msg .= $downloaded_file_path.' : 허용하지 않는 확장자';
                            continue;
                        }

                    }


                    $upload[$i]['source']   = $files['bf_file']['name'][$i];
                    $upload[$i]['file']   = wv_get_file_base_name($downloaded_file_path);
                    $upload[$i]['filesize'] = filesize($downloaded_file_path);


                    $upload[$i]['image'] = $timg;
                    continue;

                }

                $tmp_file  = $files['bf_file']['tmp_name'][$i];
                $filesize  = $files['bf_file']['size'][$i];
                $filename  = $files['bf_file']['name'][$i];
                $filename  = get_safe_filename($filename);
                $upload_max_filesize = ini_get('upload_max_filesize');
                // 서버에 설정된 값보다 큰파일을 업로드 한다면
                if ($filename) {
                    if ($files['bf_file']['error'][$i] == 1) {
                        $file_upload_msg .= '\"'.$filename.'\" 파일의 용량이 서버에 설정('.$upload_max_filesize.')된 값보다 크므로 업로드 할 수 없습니다.\\n';
                        continue;
                    }
                    else if ($files['bf_file']['error'][$i] != 0) {
                        $file_upload_msg .= '\"'.$filename.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n';
                        continue;
                    }
                }

                if (is_uploaded_file($tmp_file)) {
                    // 관리자가 아니면서 설정한 업로드 사이즈보다 크다면 건너뜀
                    if (!$is_admin && $filesize > $board['bo_upload_size']) {
                        $file_upload_msg .= '\"'.$filename.'\" 파일의 용량('.number_format($filesize).' 바이트)이 게시판에 설정('.number_format($board['bo_upload_size']).' 바이트)된 값보다 크므로 업로드 하지 않습니다.\\n';
                        continue;
                    }

                    //=================================================================\
                    // 090714
                    // 이미지나 플래시 파일에 악성코드를 심어 업로드 하는 경우를 방지
                    // 에러메세지는 출력하지 않는다.
                    //-----------------------------------------------------------------
                    $timg = @getimagesize($tmp_file);
                    // image type
                    if ( preg_match("/\.({$config['cf_image_extension']})$/i", $filename) ||
                        preg_match("/\.({$config['cf_flash_extension']})$/i", $filename) ) {
                        if ($timg['2'] < 1 || $timg['2'] > 18)
                            continue;
                    }
                    //=================================================================

                    $upload[$i]['image'] = $timg;

                    // 4.00.11 - 글답변에서 파일 업로드시 원글의 파일이 삭제되는 오류를 수정
                    if ($w == 'u') {
                        // 존재하는 파일이 있다면 삭제합니다.
                        $row = sql_fetch(" select * from {$g5['board_file_table']} where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$i' ");

                        if(isset($row['bf_file']) && $row['bf_file']){
                            $delete_file = run_replace('delete_file_path', G5_DATA_PATH.'/file/'.$bo_table.'/'.str_replace('../', '', $row['bf_file']), $row);
                            if( file_exists($delete_file) ){
                                @unlink(G5_DATA_PATH.'/file/'.$bo_table.'/'.$row['bf_file']);
                            }
                            // 이미지파일이면 썸네일삭제
                            if(preg_match("/\.({$config['cf_image_extension']})$/i", $row['bf_file'])) {
                                delete_board_thumbnail($bo_table, $row['bf_file']);
                            }
                        }
                    }

                    // 프로그램 원래 파일명
                    $upload[$i]['source'] = $filename;
                    $upload[$i]['filesize'] = $filesize;

                    // 아래의 문자열이 들어간 파일은 -x 를 붙여서 웹경로를 알더라도 실행을 하지 못하도록 함
                    $filename = preg_replace("/\.(php|pht|phtm|htm|cgi|pl|exe|jsp|asp|inc|phar)/i", "$0-x", $filename);

                    shuffle($chars_array);
                    $shuffle = implode('', $chars_array);

                    // 첨부파일 첨부시 첨부파일명에 공백이 포함되어 있으면 일부 PC에서 보이지 않거나 다운로드 되지 않는 현상이 있습니다. (길상여의 님 090925)
                    $upload[$i]['file'] = md5(sha1($_SERVER['REMOTE_ADDR'])).'_'.substr($shuffle,0,8).'_'.replace_filename($filename);

                    $dest_file = G5_DATA_PATH.'/file/'.$bo_table.'/'.$upload[$i]['file'];

                    // 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
                    $error_code = move_uploaded_file($tmp_file, $dest_file) or die($files['bf_file']['error'][$i]);

                    // 올라간 파일의 퍼미션을 변경합니다.
                    chmod($dest_file, G5_FILE_PERMISSION);

                    $dest_file = run_replace('write_update_upload_file', $dest_file, $board, $wr_id, $w);
                    $upload[$i] = run_replace('write_update_upload_array', $upload[$i], $dest_file, $board, $wr_id, $w);
                }
            }   // end for
        }   // end if


        // 나중에 테이블에 저장하는 이유는 $wr_id 값을 저장해야 하기 때문입니다.
        foreach ($upload as $i=>$val){
            $upload[$i]['source'] = sql_real_escape_string($upload[$i]['source']);
            $bf_content[$i] = isset($bf_content[$i]) ? sql_real_escape_string($bf_content[$i]) : '';
            $bf_width = isset($upload[$i]['image'][0]) ? (int) $upload[$i]['image'][0] : 0;
            $bf_height = isset($upload[$i]['image'][1]) ? (int) $upload[$i]['image'][1] : 0;
            $bf_type = isset($upload[$i]['image'][2]) ? (int) $upload[$i]['image'][2] : 0;

            $row = sql_fetch(" select count(*) as cnt from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");
            if ($row['cnt'])
            {
                // 삭제에 체크가 있거나 파일이 있다면 업데이트를 합니다.
                // 그렇지 않다면 내용만 업데이트 합니다.
                if ($upload[$i]['del_check'] || $upload[$i]['file'])
                {
                    $sql = " update {$g5['board_file_table']}
                        set bf_source = '{$upload[$i]['source']}',
                             bf_file = '{$upload[$i]['file']}',
                             bf_content = '{$bf_content[$i]}',
                             bf_fileurl = '{$upload[$i]['fileurl']}',
                             bf_thumburl = '{$upload[$i]['thumburl']}',
                             bf_storage = '{$upload[$i]['storage']}',
                             bf_filesize = '".(int)$upload[$i]['filesize']."',
                             bf_width = '".$bf_width."',
                             bf_height = '".$bf_height."',
                             bf_type = '".$bf_type."',
                             bf_datetime = '".G5_TIME_YMDHIS."'
                      where bo_table = '{$bo_table}'
                                and wr_id = '{$wr_id}'
                                and bf_no = '{$i}' ";
                    sql_query($sql);
                }
                else
                {
                    $sql = " update {$g5['board_file_table']}
                        set bf_content = '{$bf_content[$i]}'
                        where bo_table = '{$bo_table}'
                                  and wr_id = '{$wr_id}'
                                  and bf_no = '{$i}' ";
                    sql_query($sql);
                }
            }
            else
            {
                $sql = " insert into {$g5['board_file_table']}
                    set bo_table = '{$bo_table}',
                         wr_id = '{$wr_id}',
                         bf_no = '{$i}',
                         bf_source = '{$upload[$i]['source']}',
                         bf_file = '{$upload[$i]['file']}',
                         bf_content = '{$bf_content[$i]}',
                         bf_fileurl = '{$upload[$i]['fileurl']}',
                         bf_thumburl = '{$upload[$i]['thumburl']}',
                         bf_storage = '{$upload[$i]['storage']}',
                         bf_download = 0,
                         bf_filesize = '".(int)$upload[$i]['filesize']."',
                         bf_width = '".$bf_width."',
                         bf_height = '".$bf_height."',
                         bf_type = '".$bf_type."',
                         bf_datetime = '".G5_TIME_YMDHIS."' ";
                sql_query($sql);

                run_event('write_update_file_insert', $bo_table, $wr_id, $upload[$i], $w);
            }
        }

        // 업로드된 파일 내용에서 가장 큰 번호를 얻어 거꾸로 확인해 가면서
        // 파일 정보가 없다면 테이블의 내용을 삭제합니다.
        $row = sql_fetch(" select max(bf_no) as max_bf_no from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ");
        for ($i=(int)$row['max_bf_no']; $i>=0; $i--)
        {
            $row2 = sql_fetch(" select bf_file from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");

            // 정보가 있다면 빠집니다.
            if (isset($row2['bf_file']) && $row2['bf_file']) break;

            // 그렇지 않다면 정보를 삭제합니다.
            sql_query(" delete from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_no = '{$i}' ");
        }

        // 파일의 개수를 게시물에 업데이트 한다.
        $row = sql_fetch(" select count(*) as cnt from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ");
        sql_query(" update {$write_table} set wr_file = '{$row['cnt']}' where wr_id = '{$wr_id}' ");

        return true;
    }
}
if(!function_exists('wv_write_comment')){
    function wv_write_comment($bo_table, $post, $wr_id,  $files = array()){
        global $g5,$config;

        @extract($post);

        $is_guest = true;
        $member = array('mb_id'=>'', 'mb_level'=> 1, 'mb_name'=> '', 'mb_point'=> 0, 'mb_certify'=>'', 'mb_email'=>'', 'mb_open'=>'', 'mb_homepage'=>'', 'mb_tel'=>'', 'mb_hp'=>'', 'mb_zip1'=>'', 'mb_zip2'=>'', 'mb_addr1'=>'', 'mb_addr2'=>'', 'mb_addr3'=>'', 'mb_addr_jibeon'=>'', 'mb_signature'=>'', 'mb_profile'=>'');
        if($post['mb_id']){
            $member = get_member($post['mb_id']);
        }

        $is_admin = is_admin($member['mb_id']);

        if($member['mb_id']){
            $is_guest=false;
        }

        $is_admin = is_admin($member['mb_id']);


        $board = sql_fetch(" select * from {$g5['board_table']} where bo_table = '$bo_table' ");
        if(!$board['bo_table'])alert($bo_table.' : bo_table 이 존재하지 않습니다.');
        $write_table = $g5['write_prefix'].$bo_table;

        if (substr_count($post['wr_content'], "&#") > 50) {
            alert ('내용에 올바르지 않은 코드가 다수 포함되어 있습니다.');
        }


        $w = isset($post['w']) ? clean_xss_tags($post['w']) : 'c';
        $wr_name  = isset($post['wr_name']) ? clean_xss_tags(trim($post['wr_name'])) : '';
        $wr_secret = isset($post['wr_secret']) ? clean_xss_tags($post['wr_secret']) : '';
        $wr_email = $wr_subject = '';
        $comment_id = isset($post['comment_id']) ? clean_xss_tags($post['comment_id']) : '';
        $reply_array = array();

        $wr_1 = isset($post['wr_1']) ? $post['wr_1'] : '';
        $wr_2 = isset($post['wr_2']) ? $post['wr_2'] : '';
        $wr_3 = isset($post['wr_3']) ? $post['wr_3'] : '';
        $wr_4 = isset($post['wr_4']) ? $post['wr_4'] : '';
        $wr_5 = isset($post['wr_5']) ? $post['wr_5'] : '';
        $wr_6 = isset($post['wr_6']) ? $post['wr_6'] : '';
        $wr_7 = isset($post['wr_7']) ? $post['wr_7'] : '';
        $wr_8 = isset($post['wr_8']) ? $post['wr_8'] : '';
        $wr_9 = isset($post['wr_9']) ? $post['wr_9'] : '';
        $wr_10 = isset($post['wr_10']) ? $post['wr_10'] : '';
        $wr_facebook_user = isset($post['wr_facebook_user']) ? clean_xss_tags($post['wr_facebook_user'], 1, 1) : '';
        $wr_twitter_user = isset($post['wr_twitter_user']) ? clean_xss_tags($post['wr_twitter_user'], 1, 1) : '';
        $wr_homepage = isset($post['wr_homepage']) ? clean_xss_tags($post['wr_homepage'], 1, 1) : '';

        if (!empty($post['wr_email']))
            $wr_email = get_email_address(trim($post['wr_email']));



// 비회원의 경우 이름이 누락되는 경우가 있음
        if ($is_guest) {
            if ($wr_name == '')
                alert ('이름은 필히 입력하셔야 합니다.');
            if(!chk_captcha())
                alert ('자동등록방지 숫자가 틀렸습니다.');
        }

        if ($w == "c" || $w == "cu") {
            if ($member['mb_level'] < $board['bo_comment_level'])
                alert ('댓글을 쓸 권한이 없습니다.');
        }
        else
            alert('w 값이 제대로 넘어오지 않았습니다.');




        $wr = get_write($write_table, $wr_id);
        if (empty($wr['wr_id']))
            alert ("글이 존재하지 않습니다.\\n글이 삭제되었거나 이동하였을 수 있습니다.");


// "인터넷옵션 > 보안 > 사용자정의수준 > 스크립팅 > Action 스크립팅 > 사용 안 함" 일 경우의 오류 처리
// 이 옵션을 사용 안 함으로 설정할 경우 어떤 스크립트도 실행 되지 않습니다.
//if (!trim($post["wr_content"])) die ("내용을 입력하여 주십시오.");

        $post_wr_password = '';
        if ($is_member)
        {
            $mb_id = $member['mb_id'];
            // 4.00.13 - 실명 사용일때 댓글에 닉네임으로 입력되던 오류를 수정
            $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
            $wr_password = '';
            $wr_email = addslashes($member['mb_email']);
            $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));
        }
        else
        {
            $mb_id = '';
            $post_wr_password = $wr_password;
            $wr_password = get_encrypt_string($wr_password);
        }

        $extend_data_sql = '';
        $extend_data_arr = array();
        if(isset($post['extend_data'])){
            if(!is_array($post['extend_data']))alert('확장데이터는 배열형태여야 합니다.');
            foreach ($post['extend_data'] as $key => $val){
                $extend_data_arr[] = " {$key} = '$val' ";
            }
        }

        if(isset($post['wv_old_id']) and $post['wv_old_id']){
            $extend_data_arr[] = " wv_old_id = '{$post['wv_old_id']}' ";
        }

        if(count($extend_data_arr)){
            $extend_data_sql = ' , '.implode(' , ',$extend_data_arr);
        }

        if ($w == 'c') // 댓글 입력
        {

            // 댓글쓰기 포인트설정시 회원의 포인트가 음수인 경우 댓글을 쓰지 못하던 버그를 수정 (곱슬최씨님)
            $tmp_point = ($member['mb_point'] > 0) ? $member['mb_point'] : 0;
            if ($tmp_point + $board['bo_comment_point'] < 0 && !$is_admin)
                alert ('보유하신 포인트('.number_format($member['mb_point']).')가 없거나 모자라서 댓글쓰기('.number_format($board['bo_comment_point']).')가 불가합니다.\\n\\n포인트를 적립하신 후 다시 댓글을 써 주십시오.');

            // 댓글 답변
            if ($comment_id)
            {
                $reply_array = get_write($write_table, $comment_id, true);
                if (!$reply_array['wr_id'])
                    alert ('답변할 댓글이 없습니다.\\n\\n답변하는 동안 댓글이 삭제되었을 수 있습니다.');

                if($wr['wr_parent'] != $reply_array['wr_parent'])
                    alert ('댓글을 등록할 수 없습니다.');

                $tmp_comment = $reply_array['wr_comment'];

                if (strlen($reply_array['wr_comment_reply']) == 5)
                    alert ('더 이상 답변하실 수 없습니다.\\n\\n답변은 5단계 까지만 가능합니다.');

                $reply_len = strlen($reply_array['wr_comment_reply']) + 1;
                if ($board['bo_reply_order']) {
                    $begin_reply_char = 'A';
                    $end_reply_char = 'Z';
                    $reply_number = +1;
                    $sql = " select MAX(SUBSTRING(wr_comment_reply, $reply_len, 1)) as reply
                        from $write_table
                        where wr_parent = '$wr_id'
                        and wr_comment = '$tmp_comment'
                        and SUBSTRING(wr_comment_reply, $reply_len, 1) <> '' ";
                }
                else
                {
                    $begin_reply_char = 'Z';
                    $end_reply_char = 'A';
                    $reply_number = -1;
                    $sql = " select MIN(SUBSTRING(wr_comment_reply, $reply_len, 1)) as reply
                        from $write_table
                        where wr_parent = '$wr_id'
                        and wr_comment = '$tmp_comment'
                        and SUBSTRING(wr_comment_reply, $reply_len, 1) <> '' ";
                }
                if ($reply_array['wr_comment_reply'])
                    $sql .= " and wr_comment_reply like '{$reply_array['wr_comment_reply']}%' ";
                $row = sql_fetch($sql);

                if (!$row['reply'])
                    $reply_char = $begin_reply_char;
                else if ($row['reply'] == $end_reply_char) // A~Z은 26 입니다.
                    alert ('더 이상 답변하실 수 없습니다.\\n\\n답변은 26개 까지만 가능합니다.');
                else
                    $reply_char = chr(ord($row['reply']) + $reply_number);

                $tmp_comment_reply = $reply_array['wr_comment_reply'] . $reply_char;
            }
            else
            {
                $sql = " select max(wr_comment) as max_comment from $write_table
                    where wr_parent = '$wr_id' and wr_is_comment = 1 ";
                $row = sql_fetch($sql);
                //$row[max_comment] -= 1;
                $row['max_comment'] += 1;
                $tmp_comment = $row['max_comment'];
                $tmp_comment_reply = '';
            }

            $wr_subject = get_text(stripslashes($wr['wr_subject']));
            $wr_datetime = $post['wr_datetime']?$post['wr_datetime']:G5_TIME_YMDHIS;
            $wr_ip = $post['wr_ip']?$post['wr_ip']:$_SERVER['REMOTE_ADDR'];

            $sql = " insert into $write_table
                set ca_name = '{$wr['ca_name']}',
                     wr_option = '$wr_secret',
                     wr_num = '{$wr['wr_num']}',
                     wr_reply = '',
                     wr_parent = '$wr_id',
                     wr_is_comment = 1,
                     wr_comment = '$tmp_comment',
                     wr_comment_reply = '$tmp_comment_reply',
                     wr_subject = '',
                     wr_content = '$wr_content',
                     mb_id = '$mb_id',
                     wr_password = '$wr_password',
                     wr_name = '$wr_name',
                     wr_email = '$wr_email',
                     wr_homepage = '$wr_homepage',
                     wr_datetime = '".$wr_datetime."',
                     wr_last = '',
                     wr_ip = '{$wr_ip}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10' {$extend_data_sql}";
            sql_query($sql);

            $comment_id = sql_insert_id();

            // 원글에 댓글수 증가 & 마지막 시간 반영
            sql_query(" update $write_table set wr_comment = wr_comment + 1, wr_last = '".G5_TIME_YMDHIS."' where wr_id = '$wr_id' ");

            // 새글 INSERT
            sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '$bo_table', '$comment_id', '$wr_id', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

            // 댓글 1 증가
            sql_query(" update {$g5['board_table']} set bo_count_comment = bo_count_comment + 1 where bo_table = '$bo_table' ");

            // 포인트 부여
            insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id}-{$comment_id} 댓글쓰기", $bo_table, $comment_id, '댓글');

            // 메일발송 사용
            if ($config['cf_email_use'] && $board['bo_use_email'])
            {
                // 관리자의 정보를 얻고
                $super_admin = get_admin('super');
                $group_admin = get_admin('group');
                $board_admin = get_admin('board');

                $wr_content = nl2br(get_text(stripslashes("원글\n{$wr['wr_subject']}\n\n\n댓글\n$wr_content")));

                $warr = array( ''=>'입력', 'u'=>'수정', 'r'=>'답변', 'c'=>'댓글 ', 'cu'=>'댓글 수정' );
                $str = $warr[$w];

                $subject = '['.$config['cf_title'].'] '.$board['bo_subject'].' 게시판에 '.$str.'글이 올라왔습니다.';
                // 4.00.15 - 메일로 보내는 댓글의 바로가기 링크 수정
                $link_url = get_pretty_url($bo_table, $wr_id, $qstr."#c_".$comment_id);

                include_once(G5_LIB_PATH.'/mailer.lib.php');

                ob_start();
                include(G5_BBS_PATH.'/write_update_mail.php');
                $content = ob_get_contents();
                ob_end_clean();

                $array_email = array();
                // 게시판관리자에게 보내는 메일
                if ($config['cf_email_wr_board_admin']) $array_email[] = $board_admin['mb_email'];
                // 게시판그룹관리자에게 보내는 메일
                if ($config['cf_email_wr_group_admin']) $array_email[] = $group_admin['mb_email'];
                // 최고관리자에게 보내는 메일
                if ($config['cf_email_wr_super_admin']) $array_email[] = $super_admin['mb_email'];

                // 원글게시자에게 보내는 메일
                if ($config['cf_email_wr_write']) $array_email[] = $wr['wr_email'];

                // 댓글 쓴 모든이에게 메일 발송이 되어 있다면 (자신에게는 발송하지 않는다)
                if ($config['cf_email_wr_comment_all']) {
                    $sql = " select distinct wr_email from {$write_table}
                        where wr_email not in ( '{$wr['wr_email']}', '{$member['mb_email']}', '' )
                        and wr_parent = '$wr_id' ";
                    $result = sql_query($sql);
                    while ($row=sql_fetch_array($result))
                        $array_email[] = $row['wr_email'];
                }

                // 중복된 메일 주소는 제거
                $unique_email = array_unique($array_email);
                $unique_email = array_values($unique_email);
                for ($i=0; $i<count($unique_email); $i++) {
                    mailer($wr_name, $wr_email, $unique_email[$i], $subject, $content, 1);
                }
            }

            // SNS 등록
            include(G5_BBS_PATH."/write_comment_update.sns.php");
            if($wr_facebook_user || $wr_twitter_user) {
                $sql = " update $write_table
                    set wr_facebook_user = '$wr_facebook_user',
                        wr_twitter_user  = '$wr_twitter_user'
                    where wr_id = '$comment_id' ";
                sql_query($sql);
            }
        }
        else if ($w == 'cu') // 댓글 수정
        {
            $sql = " select mb_id, wr_password, wr_comment, wr_comment_reply from $write_table
                where wr_id = '$comment_id' ";
            $comment = $reply_array = sql_fetch($sql);
            $tmp_comment = $reply_array['wr_comment'];

            $len = strlen($reply_array['wr_comment_reply']);
            if ($len < 0) $len = 0;
            $comment_reply = substr($reply_array['wr_comment_reply'], 0, $len);
            //print_r2($GLOBALS); exit;

            if ($is_admin == 'super') // 최고관리자 통과
                ;
            else if ($is_admin == 'group') { // 그룹관리자
                $mb = get_member($comment['mb_id']);
                if ($member['mb_id'] === $group['gr_admin']) { // 자신이 관리하는 그룹인가?
                    if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                        ;
                    else
                        alert('그룹관리자의 권한보다 높은 회원의 댓글이므로 수정할 수 없습니다.');
                } else
                    alert('자신이 관리하는 그룹의 게시판이 아니므로 댓글을 수정할 수 없습니다.');
            } else if ($is_admin == 'board') { // 게시판관리자이면
                $mb = get_member($comment['mb_id']);
                if ($member['mb_id'] === $board['bo_admin']) { // 자신이 관리하는 게시판인가?
                    if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                        ;
                    else
                        alert('게시판관리자의 권한보다 높은 회원의 댓글이므로 수정할 수 없습니다.');
                } else
                    alert('자신이 관리하는 게시판이 아니므로 댓글을 수정할 수 없습니다.');
            } else if ($member['mb_id']) {
                if ($member['mb_id'] !== $comment['mb_id'])
                    alert('자신의 글이 아니므로 수정할 수 없습니다.');
            } else {
                if( !($comment['mb_id'] === '' && $comment['wr_password'] && check_password($post_wr_password, $comment['wr_password'])) )
                    alert('댓글을 수정할 권한이 없습니다.');
            }

            $sql = " select count(*) as cnt from $write_table
                where wr_comment_reply like '$comment_reply%'
                and wr_id <> '$comment_id'
                and wr_parent = '$wr_id'
                and wr_comment = '$tmp_comment'
                and wr_is_comment = 1 ";
            $row = sql_fetch($sql);
            if ($row['cnt'] && !$is_admin)
                alert('이 댓글와 관련된 답변댓글이 존재하므로 수정 할 수 없습니다.');

            $sql_ip = "";
            if (!$is_admin)
                $sql_ip = " , wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";

            $sql_secret = " , wr_option = '$wr_secret' ";

            $sql = " update $write_table
                set wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10'
                     $sql_ip
                     $sql_secret
                {$extend_data_sql}
              where wr_id = '$comment_id' ";

            sql_query($sql);
        }



        delete_cache_latest($bo_table);

        $redirect_url = short_url_clean(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr['wr_parent'].'&amp;'.$qstr.'&amp;#c_'.$comment_id);

        run_event('comment_update_after', $board, $wr_id, $w, $qstr, $redirect_url, $comment_id, $reply_array);

        return $comment_id;
    }
}
if(!function_exists('wv_write_member')){
    function wv_write_member($data, $password_hash = true ){

        global $config, $g5;
        include_once(G5_LIB_PATH.'/register.lib.php');

        $w              = isset($data['mw']) ? trim($data['mw']) : '';
        $mb_id          = isset($data['mb_id']) ? trim($data['mb_id']) : '';
        $mb_password    = isset($data['mb_password']) ? trim($data['mb_password']) : '';
        $mb_name        = isset($data['mb_name']) ? trim($data['mb_name']) : '';
        $mb_nick        = isset($data['mb_nick']) ? trim($data['mb_nick']) : '';
        $mb_email       = isset($data['mb_email']) ? trim($data['mb_email']) : '';
        $mb_level       = isset($data['mb_level']) ? trim($data['mb_level']) : $config['cf_register_level'];
        $mb_sex         = isset($data['mb_sex'])           ? trim($data['mb_sex'])         : "";
        $mb_birth       = isset($data['mb_birth'])         ? trim($data['mb_birth'])       : "";
        $mb_homepage    = isset($data['mb_homepage'])      ? trim($data['mb_homepage'])    : "";
        $mb_tel         = isset($data['mb_tel'])           ? trim($data['mb_tel'])         : "";
        $mb_hp          = isset($data['mb_hp'])            ? trim($data['mb_hp'])          : "";
        $mb_zip1        = isset($data['mb_zip'])           ? substr(trim($data['mb_zip']), 0, 3) : "";
        $mb_zip2        = isset($data['mb_zip'])           ? substr(trim($data['mb_zip']), 3)    : "";
        $mb_addr1       = isset($data['mb_addr1'])         ? trim($data['mb_addr1'])       : "";
        $mb_addr2       = isset($data['mb_addr2'])         ? trim($data['mb_addr2'])       : "";
        $mb_addr3       = isset($data['mb_addr3'])         ? trim($data['mb_addr3'])       : "";
        $mb_addr_jibeon = isset($data['mb_addr_jibeon'])   ? trim($data['mb_addr_jibeon']) : "";
        $mb_signature   = isset($data['mb_signature'])     ? trim($data['mb_signature'])   : "";
        $mb_profile     = isset($data['mb_profile'])       ? trim($data['mb_profile'])     : "";
        $mb_recommend   = isset($data['mb_recommend'])     ? trim($data['mb_recommend'])   : "";
        $mb_mailling    = isset($data['mb_mailling'])      ? trim($data['mb_mailling'])    : "";
        $mb_sms         = isset($data['mb_sms'])           ? trim($data['mb_sms'])         : "";
        $mb_open        = isset($data['mb_open'])          ? trim($data['mb_open'])        : "0";
        $mb_datetime    = isset($data['mb_datetime'])      ? trim($data['mb_datetime'])    : G5_TIME_YMDHIS;
        $mb_1           = isset($data['mb_1'])             ? trim($data['mb_1'])           : "";
        $mb_2           = isset($data['mb_2'])             ? trim($data['mb_2'])           : "";
        $mb_3           = isset($data['mb_3'])             ? trim($data['mb_3'])           : "";
        $mb_4           = isset($data['mb_4'])             ? trim($data['mb_4'])           : "";
        $mb_5           = isset($data['mb_5'])             ? trim($data['mb_5'])           : "";
        $mb_6           = isset($data['mb_6'])             ? trim($data['mb_6'])           : "";
        $mb_7           = isset($data['mb_7'])             ? trim($data['mb_7'])           : "";
        $mb_8           = isset($data['mb_8'])             ? trim($data['mb_8'])           : "";
        $mb_9           = isset($data['mb_9'])             ? trim($data['mb_9'])           : "";
        $mb_10          = isset($data['mb_10'])            ? trim($data['mb_10'])          : "";

        // XSS 및 유효성 처리
        $mb_name        = clean_xss_tags($mb_name);
        $mb_email       = get_email_address($mb_email);
        $mb_homepage    = clean_xss_tags($mb_homepage);
        $mb_tel         = clean_xss_tags($mb_tel);
        $mb_zip1        = preg_replace('/[^0-9]/', '', $mb_zip1);
        $mb_zip2        = preg_replace('/[^0-9]/', '', $mb_zip2);
        $mb_addr1       = clean_xss_tags($mb_addr1);
        $mb_addr2       = clean_xss_tags($mb_addr2);
        $mb_addr3       = clean_xss_tags($mb_addr3);
        $mb_addr_jibeon = preg_match("/^(N|R)$/", $mb_addr_jibeon) ? $mb_addr_jibeon : '';

        // ✅ 회원정보 수정 모드 체크
        $is_update = ($w === 'u');

        // ✅ 수정 모드일 때 기존 회원 존재 여부 확인
        if ($is_update) {
            if (!$mb_id) {
                return '수정할 회원ID가 필요합니다.';
            }

            $existing_member = get_member($mb_id);
            if (!$existing_member['mb_id']) {
                return '존재하지 않는 회원입니다.';
            }
        } else {
            // 신규 가입 시에만 체크
            if ($msg = empty_mb_id($mb_id))         return $msg;
            if ($msg = valid_mb_id($mb_id))         return $msg;
            if ($msg = count_mb_id($mb_id))         return $msg;
            if ($msg = exist_mb_id($mb_id))         return $msg;
        }

        // 이름, 닉네임 UTF-8 체크 (값이 있을 때만)
        if ($mb_name) {
            $tmp_mb_name = iconv('UTF-8', 'UTF-8//IGNORE', $mb_name);
            if($tmp_mb_name != $mb_name) {
                return('이름을 올바르게 입력해 주십시오.');
            }
        }

        if ($mb_nick) {
            $tmp_mb_nick = iconv('UTF-8', 'UTF-8//IGNORE', $mb_nick);
            if($tmp_mb_nick != $mb_nick) {
                return('닉네임을 올바르게 입력해 주십시오.');
            }
        }

        // 비밀번호 체크
        $is_check_password = run_replace('register_member_password_check', true, $mb_id, $mb_nick, $mb_email, $w);

        if ($is_check_password && !$is_update) {
            if (!$mb_password) {
                return('비밀번호가 넘어오지 않았습니다.');
            }
        }

        // 유효성 검사 (값이 있을 때만)
        if ($mb_name && ($msg = empty_mb_name($mb_name)))       return $msg;
        if ($mb_nick && ($msg = empty_mb_nick($mb_nick)))       return $msg;
        if ($mb_email && ($msg = empty_mb_email($mb_email)))    return $msg;
        if ($mb_id && ($msg = reserve_mb_id($mb_id)))           return $msg;
        if ($mb_nick && ($msg = reserve_mb_nick($mb_nick)))     return $msg;
        if ($mb_nick && ($msg = valid_mb_nick($mb_nick)))       return $msg;
        if ($mb_email && ($msg = valid_mb_email($mb_email)))    return $msg;
        if ($mb_email && ($msg = prohibit_mb_email($mb_email))) return $msg;

        // 휴대폰 유효성 체크
        if (($config['cf_use_hp'] || $config['cf_cert_hp'] || $config['cf_cert_simple']) && $config['cf_req_hp']) {
            if ($mb_hp && ($msg = valid_mb_hp($mb_hp))) return $msg;
        }

        // 추천인 체크
        if ($config['cf_use_recommend'] && $mb_recommend) {
            if (!exist_mb_id($mb_recommend))
                return("추천인이 존재하지 않습니다.");

            if (strtolower($mb_id) == strtolower($mb_recommend)) {
                return ('본인을 추천할 수 없습니다.');
            }
        }

        // 본인확인 체크 (신규 가입시에만)
        if (!$is_update && $config['cf_cert_use'] && $config['cf_cert_req']) {
            $post_cert_no = isset($_POST['cert_no']) ? trim($_POST['cert_no']) : '';
            if($post_cert_no !== get_session('ss_cert_no') || ! get_session('ss_cert_no'))
                return("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
        }

        run_event('register_form_update_valid', $w, $mb_id, $mb_nick, $mb_email);

        // 중복 체크 (수정시에는 자기 자신 제외)
        if ($mb_nick && ($msg = exist_mb_nick($mb_nick, $mb_id))) return $msg;
        if ($mb_email && ($msg = exist_mb_email($mb_email, $mb_id))) return $msg;

        // 비밀번호 암호화
        if ($mb_password && $password_hash) {
            $mb_password = get_encrypt_string($mb_password);
        }

        // ✅ 확장 데이터 처리
        $extend_data_sql = '';
        $extend_data_arr = array();
        if (isset($data['extend_data']) && is_array($data['extend_data'])) {
            foreach ($data['extend_data'] as $key => $val) {
                $extend_data_arr[] = " {$key} = '" . sql_escape_string($val) . "' ";
            }
        }

        if (isset($data['wv_old_id']) && $data['wv_old_id']) {
            $extend_data_arr[] = " wv_old_id = '" . sql_escape_string($data['wv_old_id']) . "' ";
        }

        if (count($extend_data_arr)) {
            $extend_data_sql = ' , ' . implode(' , ', $extend_data_arr);
        }

        // ✅ SQL 생성 - 수정/등록 분기
        if ($is_update) {
            // UPDATE SQL - 넘어온 데이터만 업데이트
            $update_fields = array();

            // 기본 필드들 체크 (값이 있을 때만 추가)
            if (isset($data['mb_password']) && $mb_password) $update_fields[] = "mb_password = '{$mb_password}'";
            if (isset($data['mb_name'])) $update_fields[] = "mb_name = '{$mb_name}'";
            if (isset($data['mb_nick'])) $update_fields[] = "mb_nick = '{$mb_nick}', mb_nick_date = '" . G5_TIME_YMD . "'";
            if (isset($data['mb_email'])) $update_fields[] = "mb_email = '{$mb_email}'";
            if (isset($data['mb_homepage'])) $update_fields[] = "mb_homepage = '{$mb_homepage}'";
            if (isset($data['mb_tel'])) $update_fields[] = "mb_tel = '{$mb_tel}'";
            if (isset($data['mb_hp'])) $update_fields[] = "mb_hp = '{$mb_hp}'";
            if (isset($data['mb_zip'])) {
                $update_fields[] = "mb_zip1 = '{$mb_zip1}', mb_zip2 = '{$mb_zip2}'";
            }
            if (isset($data['mb_addr1'])) $update_fields[] = "mb_addr1 = '{$mb_addr1}'";
            if (isset($data['mb_addr2'])) $update_fields[] = "mb_addr2 = '{$mb_addr2}'";
            if (isset($data['mb_addr3'])) $update_fields[] = "mb_addr3 = '{$mb_addr3}'";
            if (isset($data['mb_addr_jibeon'])) $update_fields[] = "mb_addr_jibeon = '{$mb_addr_jibeon}'";
            if (isset($data['mb_signature'])) $update_fields[] = "mb_signature = '{$mb_signature}'";
            if (isset($data['mb_profile'])) $update_fields[] = "mb_profile = '{$mb_profile}'";
            if (isset($data['mb_level'])) $update_fields[] = "mb_level = '{$mb_level}'";
            if (isset($data['mb_mailling'])) $update_fields[] = "mb_mailling = '{$mb_mailling}'";
            if (isset($data['mb_sms'])) $update_fields[] = "mb_sms = '{$mb_sms}'";
            if (isset($data['mb_open'])) $update_fields[] = "mb_open = '{$mb_open}', mb_open_date = '" . G5_TIME_YMD . "'";
            if (isset($data['mb_sex'])) $update_fields[] = "mb_sex = '{$mb_sex}'";
            if (isset($data['mb_birth'])) $update_fields[] = "mb_birth = '{$mb_birth}'";

            // mb_1 ~ mb_10 필드들
            for ($i = 1; $i <= 10; $i++) {
                if (isset($data["mb_{$i}"])) {
                    $mb_val = ${"mb_{$i}"};
                    $update_fields[] = "mb_{$i} = '{$mb_val}'";
                }
            }

            if (empty($update_fields) && empty($extend_data_arr)) {
                return '수정할 데이터가 없습니다.';
            }

            $sql = "UPDATE {$g5['member_table']} SET " . implode(', ', $update_fields);
            if ($extend_data_sql) {
                $sql .= $extend_data_sql;
            }
            $sql .= " WHERE mb_id = '{$mb_id}'";

        } else {
            // INSERT SQL - 기존 로직
            $sql_certify = '';
            $sql_certify .= " , mb_hp = '{$mb_hp}' ";
            $sql_certify .= " , mb_certify = '' ";
            $sql_certify .= " , mb_adult = 0 ";
            $sql_certify .= " , mb_birth = '{$mb_birth}' ";
            $sql_certify .= " , mb_sex = '{$mb_sex}' ";

            $sql = " INSERT INTO {$g5['member_table']}
                    SET mb_id = '{$mb_id}',
                         mb_password = '{$mb_password}',
                         mb_name = '{$mb_name}',
                         mb_nick = '{$mb_nick}',
                         mb_nick_date = '" . G5_TIME_YMD . "',
                         mb_email = '{$mb_email}',
                         mb_homepage = '{$mb_homepage}',
                         mb_tel = '{$mb_tel}',
                         mb_zip1 = '{$mb_zip1}',
                         mb_zip2 = '{$mb_zip2}',
                         mb_addr1 = '{$mb_addr1}',
                         mb_addr2 = '{$mb_addr2}',
                         mb_addr3 = '{$mb_addr3}',
                         mb_addr_jibeon = '{$mb_addr_jibeon}',
                         mb_signature = '{$mb_signature}',
                         mb_profile = '{$mb_profile}',
                         mb_today_login = '" . G5_TIME_YMDHIS . "',
                         mb_datetime = '{$mb_datetime}', 
                         mb_level = '{$mb_level}', 
                         mb_recommend = '{$mb_recommend}', 
                         mb_mailling = '{$mb_mailling}',
                         mb_sms = '{$mb_sms}',
                         mb_open = '{$mb_open}',
                         mb_open_date = '" . G5_TIME_YMD . "',
                         mb_1 = '{$mb_1}',
                         mb_2 = '{$mb_2}',
                         mb_3 = '{$mb_3}',
                         mb_4 = '{$mb_4}',
                         mb_5 = '{$mb_5}',
                         mb_6 = '{$mb_6}',
                         mb_7 = '{$mb_7}',
                         mb_8 = '{$mb_8}',
                         mb_9 = '{$mb_9}',
                         mb_10 = '{$mb_10}'
                         {$sql_certify} {$extend_data_sql} ";

            // 이메일 인증을 사용하지 않는다면 이메일 인증시간을 바로 넣는다
            if (!$config['cf_use_email_certify'])
                $sql .= " , mb_email_certify = '" . G5_TIME_YMDHIS . "' ";
        }

        $result = sql_query($sql, 1);
        if ($result == false) {
            return $data['mw'] ? '회원정보 수정 실패' : '회원가입 실패';
        }

        return true;
    }
}

/** php low version */
if(!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
if(!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}
if(!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle)
    {
        $needle_len = strlen($needle);
        return ($needle_len === 0 || 0 === substr_compare($haystack, $needle, - $needle_len));
    }
}