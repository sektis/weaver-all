<?php
namespace weaver\store_manager\parts;
use weaver\store_manager\StoreSchemaBase;
use weaver\store_manager\StoreSchemaInterface;

class Dayoffs extends StoreSchemaBase implements StoreSchemaInterface{

    public $list_part  = true;

    protected $columns = array(
        'cycle' => " VARCHAR(10) not null",
        'target' => " VARCHAR(10) not null",
        'filter_type' => 'VARCHAR(10) DEFAULT NULL',
        'filter_index' => 'TINYINT DEFAULT NULL',
        'filter_week' => 'VARCHAR(20) DEFAULT NULL',
        'form'=>'',
    );


    public function get_indexes(){
        return array(
            array('filter_type', 'filter_index'),  // 복합 인덱스로 빠른 검색
            array('filter_type'),
            array('filter_index')
        );
    }

    public function column_extend($row){

        $arr = array();

        return $arr;
    }

    public function is_new(&$data, $pkey, $col) {
        $this->calculate_and_set_filter_fields($data);
    }

// ✅ 기존 항목 수정시 필터 필드 재계산
    public function is_update(&$data, $pkey, $col) {
        $this->calculate_and_set_filter_fields($data);
    }

    private function calculate_and_set_filter_fields(&$data) {
        // cycle, target 필드가 있을 때만 계산
        if(!isset($data['cycle'])) {
            return;
        }
        if($data['cycle'] and !$data['target']) {
            alert('요일/날짜를 입력하세요.');
        }

        $filter_data = $this->calculate_filter_fields($data['cycle'], $data['target']);

        // 계산된 필터 필드들을 데이터에 병합
        $data = array_merge($data, $filter_data);
    }

// 📊 실제 필터 필드 계산 로직 (확장된 설계안)
    private function calculate_filter_fields($cycle, $target) {
        $result = array(
            'filter_type' => null,
            'filter_index' => null,
            'filter_week' => null
        );

        // 요일을 숫자로 변환 (일요일=0, PHP date('w') 호환)
        $target_map = array(
            '일요일' => 0, '월요일' => 1, '화요일' => 2, '수요일' => 3,
            '목요일' => 4, '금요일' => 5, '토요일' => 6
        );

        switch($cycle) {
            case '매주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = null; // 모든 주
                break;

            case '홀수격주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = '1,3,5'; // 홀수 주차들
                break;

            case '짝수격주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = '2,4,6'; // 짝수 주차들
                break;

            case '첫째주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = '1';
                break;

            case '둘째주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = '2';
                break;

            case '셋째주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = '3';
                break;

            case '넷째주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = '4';
                break;

            case '다섯째주':
                $result['filter_type'] = 'w';
                $result['filter_index'] = isset($target_map[$target]) ? $target_map[$target] : null;
                $result['filter_week'] = '5';
                break;

            case '매월':
                $result['filter_type'] = 'm';
                // 매월의 경우 $target이 "15일" 형태일 수 있으므로 숫자 추출
                $result['filter_index'] = is_numeric($target) ? (int)$target : intval($target);
                $result['filter_week'] = null; // 모든 월
                break;
        }

        return $result;
    }
}
