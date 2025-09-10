<?php
namespace weaver;

include_once dirname(__FILE__).'/RegionApi.php';

class Location extends Plugin {


    use RegionApi;
    public $locations = array();
    private $favorite_max_count = 3;
    private $ajax_url = '';
    protected $table = 'wv_location_region';


    public function __construct() {

        global $config,$g5;

        if(!$config['cf_kakao_js_apikey'] or !$config['cf_kakao_rest_key']){
            $this->error('카카오 js api key를 등록하세요.',2);
        }
        if(!wv_is_ajax()){
            add_javascript('<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=f1d7df6827c9cbc8a39c19ac0baac410&libraries=services"></script>');
            add_javascript('<script src="'.wv_path_replace_url(dirname(__FILE__)).'/js/func.js"></script>');
        }

        $this->ajax_url = wv_path_replace_url(dirname(__FILE__)).'/ajax.php';
        $this->plugin_init();
        $this->make_json();

        add_event('wv_hook_before_header_wrapper',array($this,'wv_hook_before_header_wrapper'));


    }

    public function wv_hook_before_header_wrapper(){
        echo wv()->location->current(true);
    }

    public function set($name,$info=array(),$save_cookie=false){

        $this->locations[$name] = $info;

        foreach ($info as $key=>$val){
            if(!$val['address']['lat']){
                $arr = $this->address("{$val['address']['region_1depth_name']} {$val['address']['region_2depth_name']} {$val['address']['region_3depth_name']}");
                $info[$key]['lat']=$arr['lat'];
                $info[$key]['lng']=$arr['lng'];
                $info[$key]['address']['address_name']=$arr['address']['address_name'];

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
        return wv_trans_sido($arr['address']['region_1depth_name']).'/'.$arr['address']['region_3depth_name'];
    }

    public function get_favorite_max_count(){
        return $this->favorite_max_count;
    }


    public function current($force=false){
        if(!$this->get('current') and $force){

            include dirname(__FILE__).'/init_current.php';

        }
    }


    public function make_json(){
        wv_add_symlink($this->plugin_theme_path.'/page',wv()->page->plugin_theme_path.'/location');
    }


    public function address($address){
        global $config;



        $url = "https://dapi.kakao.com/v2/local/search/address.json?query=" . urlencode($address);
        $headers = [
            "Authorization: KakaoAK {$config['cf_kakao_rest_key']}"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);


        if (!empty($data['documents'][0])) {
            $arr = array();
            $arr['lng'] = $data['documents'][0][x];
            $arr['lat'] = $data['documents'][0][y];
            $arr['region_1depth_name'] = $data['documents'][0]['address']['region_1depth_name'];
            $arr['region_2depth_name'] = $data['documents'][0]['address']['region_2depth_name'];
            $arr['region_3depth_name'] = $data['documents'][0]['address']['region_3depth_name'];
            $arr['address_name'] = $data['documents'][0]['address']['address_name'];
            $arr['address_road_name'] = $data['documents'][0]['road_address']['address_name'];

        } else {
            return null;
        }

        return $arr;
    }

    public function coords($lat,$lng){
        global $config;


        $address="x={$lng}&y={$lat}";
        $url = "https://dapi.kakao.com/v2/local/geo/coord2address.json?" . ($address);

        $headers = [
            "Authorization: KakaoAK {$config['cf_kakao_rest_key']}"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!empty($data['documents'][0])) {
            $arr = array();
            $arr['lng'] = $lng;
            $arr['lat'] = $lat;
            $arr['region_1depth_name'] = $data['documents'][0]['address']['region_1depth_name'];
            $arr['region_2depth_name'] = $data['documents'][0]['address']['region_2depth_name'];
            $arr['region_3depth_name'] = $data['documents'][0]['address']['region_3depth_name'];
            $arr['address_name'] = $data['documents'][0]['address']['address_name'];
            $arr['address_road_name'] = $data['documents'][0]['road_address']['address_name'];

        } else {
            return null;
        }

        return $arr;
    }


    public function display($skin,$data=''){

        return $this->make_skin($skin,$data);
    }


}
Location::getInstance();