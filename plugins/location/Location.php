<?php
namespace weaver;

include_once dirname(__FILE__).'/RegionApi.php';

class Location extends Plugin {


    use RegionApi;
    public $locations = array();
    private $favorite_max_count = 3;
    private $ajax_url = '';
    protected $table = 'wv_location_region';

    protected $kakao_js_apikey = '';
    protected $kakao_rest_key = '';
    protected $kakao_rest_header = '';
    protected $kakao_rest_type = array(
        'address'=>'search',
        'coord2regioncode'=>'geo',
        'coord2address'=>'geo',
        'transcoord'=>'geo',
        'keyword'=>'search',
        'category'=>'search',
    );


    public function __construct() {

        global $config,$g5;

        if(!$config['cf_kakao_js_apikey'] or !$config['cf_kakao_rest_key']){
            $this->error('카카오 js api key를 등록하세요.',2);
        }

        $this->kakao_rest_key = 'f57928291f014a0f3cc91f843b31d423';
        $this->kakao_js_apikey = 'f1d7df6827c9cbc8a39c19ac0baac410';
        $this->kakao_rest_header = array(
            "Authorization: KakaoAK {$this->kakao_rest_key}"
        );

        if(!wv_is_ajax()){
            add_javascript('<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey='.$this->kakao_js_apikey.'&libraries=services"></script>');
        }

        $this->ajax_url = wv_path_replace_url(dirname(__FILE__)).'/ajax.php';
        $this->make_json();



        add_event('wv_hook_before_header_wrapper',array($this,'wv_hook_before_header_wrapper'));


    }

    public function wv_hook_before_header_wrapper(){

        wv()->location->init_script();
        wv()->location->current(true);
    }

    public function set($name,$info=array(),$save_cookie=false){

        $this->locations[$name] = $info;


        foreach ($info as $key=>$val){
            if(!$val['lat']){
                $arr = $this->address_search("{$val['region_1depth_name']} {$val['region_2depth_name']} {$val['region_3depth_name']}");
                $info[$key]['lat']=$arr['lat'];
                $info[$key]['lng']=$arr['lng'];


            }
        }

        if($save_cookie){
            set_cookie("wv_location_{$name}",wv_base64_encode_serialize($info),60 * 60 * 24 * 365);
        }

    }

    public function get($name){

        $arr = $this->locations[$name];
        if(array_filter($arr)){
            return $arr;
        }

        $arr = wv_base64_decode_unserialize(get_cookie(("wv_location_{$name}")));
//
        if(array_filter($arr)){
//            dd(wv_base64_decode_unserialize(get_cookie(("wv_location_favorite"))));
            $this->locations[$name] = $arr;
            return $arr;
        }
        return false;
    }

    public function ajax_url(){
        return $this->ajax_url;
    }

    public function display_text($arr){

        return wv_trans_sido($arr['region_1depth_name']).'/'.$arr['region_3depth_name'];
    }

    public function get_favorite_max_count(){
        return $this->favorite_max_count;
    }


    public function init_script(){
          include dirname(__FILE__).'/init_script.php';    }

    public function current($force=false){
        if(!$this->get('current') or $force){
              include dirname(__FILE__).'/init_current.php';
        }
    }


    public function make_json(){
        wv_add_symlink($this->plugin_theme_path.'/page',wv()->page->plugin_theme_path.'/location');
    }


    public function address_search($address,$size=10,$page=1){



        $query_array = array(
            'query'=>$address,
            'size'=>$size,
            'page'=>$page
        );



        return $this->kakao_restapi('keyword',$query_array);
    }

    public function coords_to_region($lat,$lng){



        $query_array = array(
            'x'=>$lng,
            'y'=>$lat,
        );
        return $this->kakao_restapi('coord2regioncode',$query_array);



    }

    private function kakao_restapi($api,$query_array=array()){
        $result = array();
        if(!key_exists($api,$this->kakao_rest_type)){
            alert('api type error');
        }
        $query_string = http_build_query($query_array, '', '&');
        $url = "https://dapi.kakao.com/v2/local/{$this->kakao_rest_type[$api]}/{$api}.json?{$query_string}";


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->kakao_rest_header);



        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if($data['errorType']   ){
            $err_msg = "{$data['errorType']} : {$data['message']}";
            if(WV_DEBUG){
                alert($err_msg);
            }
            $result['error'] = $err_msg;
        }

        if (count($data['documents'])) {
            $result['list'] = wv_extract_keys($data['documents'], array('address_name','road_address_name','x','y','region_1depth_name','region_2depth_name','region_3depth_name','place_name'));
            $result['list'] = wv_array_rename_keys($result['list'], array("x" => "lng",
                "y" => "lat"));

            $result['total_count'] = $data['meta']['pageable_count'];
            $result['is_end'] = $data['meta']['is_end'];
        }
        return $result;
    }


    public function display($skin,$data=''){

        return $this->make_skin($skin,$data);
    }
/*
$aaa = wv()->location->address_search('영도구 동삼동');
$bbb = $aaa['list'][0];
$ccc = wv()->location->coords_to_region($bbb['y'],$bbb['x']);
$ddd = $ccc['list'][0];
$eee = $bbb+$ddd;
echo '<pre>';
print_r($bbb);
echo '</pre>';
echo '<pre>';
print_r($ddd);
echo '</pre>';
dd($eee);
 */

}
Location::getInstance();