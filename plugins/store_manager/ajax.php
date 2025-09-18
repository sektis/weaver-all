<?php
include_once '_common.php';

if($action == 'get_stores_by_bounds'){

    // 입력값 검증
    if (!$sw_lat || !$sw_lng || !$ne_lat || !$ne_lng) {
        alert('잘못된 좌표 정보입니다.');
    }

    if ($sw_lat >= $ne_lat || $sw_lng >= $ne_lng) {
        alert('좌표 범위가 올바르지 않습니다.');
    }

    // Store Manager에서 매장 목록 조회
    $manager = wv()->store_manager->made('sub01_01');

    // WHERE 조건 구성 (확장테이블 s의 물리키 사용)

    // get_list 옵션
    $options = array(
        'where' =>    array(
            " location_lat  <>'' ",
            " location_lng <>'' "
        ),
        'where_location' => array('and' =>
            array(
                'lat' => "BETWEEN {$sw_lat} AND {$ne_lat} ",

                'lng' => "BETWEEN {$sw_lng} AND {$ne_lng} "
            ),
        ),
        'select_store'=>array('list_each'=>'','service'=>array('cont_pdt_type'=>1)),
        'order_by' => 'w.wr_datetime DESC',
        'rows' => 1000,  // 최대 1000개까지,
        'with_list_part'=>true
    );
    if($curr_coords){
        $distance_options = wv_make_current_location_distance_options($curr_coords);
        $options = array_merge($options, $distance_options);
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
            if ($lat < $sw_lat || $lat > $ne_lat || $lng < $sw_lng || $lng > $ne_lng) {
                continue;
            }

            $stores[] = array(
                'wr_id' => intval($item['wr_id']),
                'name' => isset($item['store']['name']) ? $item['store']['name'] : $item['wr_subject'],
                'lat' => $lat,
                'lng' => $lng,
                'category' => isset($item['store']['category']) ? $item['store']['category'] : '',
                'category_text' => isset($item['store']['category_text']) ? $item['store']['category_text'] : '',
                'category_icon' => isset($item['store']['category_icon']) ? $item['store']['category_icon'] : '',
                'main_image' => isset($item['store']['main_image']) ? $item['store']['main_image'] : '',
                'tel' => isset($item['store']['tel']) ? $item['store']['tel'] : '',
                'address' => isset($item['location']['address_name']) ? $item['location']['address_name'] : '',
                'notice' => isset($item['store']['notice']) ? $item['store']['notice'] : '',
                'store_info' => '<div>dasdasda</div>',
            );
        }
    }

    // 성공 응답
    wv_json_exit(array(
        'result' => true,
        'content' => array(
            'count' => $result['total_count'],
            'lists' => $result['list'],
            'category_icon_wrap'=>wv()->store_manager->made('sub01_01')->plugin_url.'/img/category_list/small/category_icon_wrap.png',
            'category_icon_wrap_on'=>wv()->store_manager->made('sub01_01')->plugin_url.'/img/category_list/small/category_icon_wrap_on.png',

            'timestamp' => time()
        )
    ));
}

if($action=='render_part'){
    if(!$type){
        $type='form';
    }
    $store = wv()->store_manager->made($made)->get($wr_id);

    ob_start();
    echo $store->{$part}->render_part(explode(',',$fields),'form',$_POST);
    $render_content = ob_get_clean();
    $skin_data = array(
        'store'=>$store,
        'render_content' => $render_content,
        'made'=>$made
    );
    echo wv_widget('store_manager_form', $skin_data);
    exit;
}
if($action=='update'){

    $manager = wv()->store_manager->made($made);
    $wr_id = $manager->set($_POST);
    $store = $manager->get($wr_id);
    if($fields){
        ob_start();
        echo $store->{$part}->render_part(explode(',',$fields),'form',$_POST);
        $render_content = ob_get_clean();
        $skin_data = array(
            'store'=>$store,
            'render_content' => $render_content,
            'made'=>$made
        );
        echo wv_widget('store_manager_form', $skin_data);
        exit;
    }
    wv_json_exit(array('wr_id'=>$wr_id));
}
if($action=='delete'){
    $wr_id = wv()->store_manager->made($made)->delete(array('wr_id'=>$wr_id));
    wv_json_exit(array('wr_id'=>$wr_id));
}

// 잘못된 action
alert('잘못된 요청입니다.');