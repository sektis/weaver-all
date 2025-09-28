<?php
include_once '_common.php';

if($action == 'get_store_list'){




    // Store Manager에서 매장 목록 조회
    $manager = wv()->store_manager->made('sub01_01');

    // WHERE 조건 구성 (확장테이블 s의 물리키 사용)

    // get_list 옵션
    $options = array(
        'select'=>array(),
        'where' =>    array(
            " location_lat  <>'' ",
            " location_lng <>'' "
        ),
        'select_store'=>array('list_each'=>array('contractitem_wr_id'=>$contractitem_wr_id),'service'),
        'order_by' => 'w.wr_datetime DESC',
        'rows' => $rows?$rows:30,
        'with_list_part'=>false
    );

    if($category_wr_id and $category_wr_id!='all'){
        if($category_wr_id=='other'){
            $options['where_store'] = array('category_wr_id' => ">11");
        }else{
            $options['where_store'] = array('category_wr_id' => "={$category_wr_id}");
        }

    }

    if($contractitem_wr_id ){
        $options['where_contract'] = array(
            'and'=>array(
                array('contractitem_wr_id' => "={$contractitem_wr_id}"),
                array('status' => "=1"),
            )
        );

    }


    $need_bound_check=false;
    if ($sw_lat and $sw_lng and $ne_lat and $ne_lng) {
        if ($sw_lat >= $ne_lat || $sw_lng >= $ne_lng) {
            alert('좌표 범위가 올바르지 않습니다.');
        }
        $need_bound_check=true;


        $options['where_location'] = array('and' =>
            array(
                'lat' => "BETWEEN {$sw_lat} AND {$ne_lat} ",

                'lng' => "BETWEEN {$sw_lng} AND {$ne_lng} "
            ),
        );

    }
    $options = wv_make_distance_options($center['lat'],$center['lng'],$options, $limit_km);

    if($order and $order!='near'){
        $options['order_by'] = $order;

    }

    if($q){
        $q_arr = array(
          'or'=>array(
              'where_store'=>array(
                  array('name'=>" like '%{$q}%'"),
              ),
              'where_menu'=>array(
                  array('name'=>" like '%{$q}%'")
              )
          )
        );
      $options['where'] = array_merge_recursive($options['where'],$q_arr);
    }


    $result = $manager->get_list($options);




    // 응답 데이터 정리
    $stores = array();

    if (isset($result['list']) && is_array($result['list'])) {
        foreach ($result['list'] as $item) {
            // location 파트에서 좌표 정보 추출 (일반 파트 - Array 접근)
            $lat = null;
            $lng = null;

            if (isset($item['location']) && is_array($item['location'])) {
                $lat = floatval($item['location']['lat']);
                $lng = floatval($item['location']['lng']);
            }

            // 좌표가 없는 매장은 제외
            if (!$lat || !$lng) continue;

            // 범위 재검증 (혹시나 하는 이중체크)
            if ($need_bound_check and ($lat < $sw_lat || $lat > $ne_lat || $lng < $sw_lng || $lng > $ne_lng)) {
                continue;
            }

            $stores[] = array(
                'id' => intval($item['wr_id']),
                'name' => isset($item['store']['name']) ? $item['store']['name'] : $item['wr_subject'],
                'lat' => $lat,
                'lng' => $lng,
                'marker' => array(
                    'image'=>$item['store']['category_item']['icon']['path'],
                ),
                'list_each'=>$item['store']['list_each']

            );
        }
    }

    $return = array(
        'result' => true,
        'content' => array(
            'count' => $result['total_count'],
            'lists' => $stores,
            'marker_wrap'=>wv()->store_manager->made('sub01_01')->plugin_url.'/img/category_icon_wrap.png',
            'marker_wrap_on'=>wv()->store_manager->made('sub01_01')->plugin_url.'/img//category_icon_wrap_on.png',
            'timestamp' => time()
        )
    );

    if($widget){
        echo wv_widget($widget, array_merge($return,$_REQUEST));
        exit;
    }
    // 성공 응답
    wv_json_exit($return);
}

if($action=='form'){
    if(!$made or !$part or !$field){
        alert('필수파라메터 누락');
    }
    echo wv()->store_manager->made($made)->get($wr_id)->{$part}->render_part($field,'form',$_REQUEST);
    exit;
}

if($action=='view'){
    if(!$made or !$part or !$field){
        alert('필수파라메터 누락');
    }
    echo wv()->store_manager->made($made)->get($wr_id)->{$part}->render_part($field,'view',$_REQUEST);
    exit;
}

if($action=='update'){
    if(!$made){
        alert('필수파라메터 누락');
    }
    $manager = wv()->store_manager->made($made)->set($_POST);
    wv_json_exit(array('reload'=>true,'msg'=>'완료'));
    exit;
}

if($action=='delete'){
    $wr_id = wv()->store_manager->made($made)->delete(array('wr_id'=>$wr_id));
    exit;
}

if($action=='get_current_store'){
    $arr = wv()->store_manager->made('sub01_01')->get_current_store();
    echo $arr['name'];
    exit;
}

if($action=='set_current_store'){
    $arr = wv()->store_manager->made('sub01_01')->set_current_store($wr_id);
    exit;
}

if($action=='init_current_store'){
    $arr = wv()->store_manager->made('sub01_01')->init_current_store();
    exit;
}

if($action=='widget'){
    echo wv_widget($widget, $_REQUEST['data']?$_REQUEST['data']:$_REQUEST);
    exit;
}
// 잘못된 action
alert('잘못된 요청입니다.');