<?php
include "_common.php";
if($wv_location_action=='set'){
    wv()->location->set($wv_location_name,$wv_location_data,true);
}

if($wv_location_action=='get'){
    echo json_encode(wv()->location->get($wv_location_name));
}
if($wv_location_action=='favorite_title'){
    $arr = wv()->location->get('favorite');
    if($arr){
        $other_count = count($arr)-1;
        echo wv()->location->display_text($arr[0]).($other_count>0?"외{$other_count}":"");
    }else{

        echo wv()->location->display_text(wv()->location->get('current'));
    }

}
if($wv_location_action=='region'){
    echo wv_widget('location/region/depth');

}
if($wv_location_action=='api'){
    echo wv()->location->api($_POST);
}
