<?php
@ini_set('memory_limit', '1024M');
@set_time_limit(0);
include_once '_common.php';

if (!sql_query(" DESC wv_location_region ", false)) {
    sql_query(
        " CREATE TABLE IF NOT EXISTS `wv_location_region` (
                  `code`   CHAR(10)     NOT NULL,      -- 법정동코드(10)
                  `depth1` VARCHAR(10)  NOT NULL,      -- 시/도 약칭 (서울, 경기, ...)
                  `depth2` VARCHAR(60)  NOT NULL,      -- 시/군/구명
                  `depth3` VARCHAR(100) NOT NULL,      -- 읍/면/동 + 리 (공백 결합)
                  PRIMARY KEY (`code`),
                  KEY `idx_d1_d2` (`depth1`, `depth2`),
                  KEY `idx_d3` (`depth3`)
                ) ",
        false
    );
}

/* ===== 헤더 매핑 ===== */
function map_header_cols($headerAssoc){
    $cands = array(
        'code'         => array('법정동코드','법정동 코드'),
        'sido'         => array('시도명','*시도명'),
        'sigungu'      => array('시군구명','시·군·구명','시군구 명'),
        'eupmyeondong' => array('읍면동명','읍/면/동명','읍면동리','읍면동'),
        'ri'           => array('리명','동리명','동/리명','동리 명','법정리명','법정동리명','법정동리 명','법정리','법정동리'),
    );
    $idx = array('code'=>null,'sido'=>null,'sigungu'=>null,'eupmyeondong'=>null,'ri'=>null);
    foreach($idx as $k=>$v){
        foreach($cands[$k] as $cand){
            foreach($headerAssoc as $col=>$title){
                $t = trim((string)$title);
                if ($t === $cand) { $idx[$k] = $col; break 2; }
            }
        }
        if (!$idx[$k]){
            foreach($cands[$k] as $cand){
                foreach($headerAssoc as $col=>$title){
                    $t = trim((string)$title);
                    if ($cand!=='' && mb_strpos($t,$cand)!==false){ $idx[$k] = $col; break 2; }
                }
            }
        }
    }
    return $idx;
}

/* ===== 파일 로딩: XLSX/XLS/CSV 지원 ===== */
function load_rows_from_uploaded($path, $ext){
    $ext = strtolower($ext);

    // CSV
    if ($ext === 'csv') {
        $rows = array(); $i=0;
        if (($fp = fopen($path, 'r')) !== false) {
            while (($cols = fgetcsv($fp, 0, ",")) !== false) {
                $assoc = array(); $col='A';
                foreach($cols as $v){ $assoc[$col++] = $v; }
                $rows[++$i] = $assoc;
            }
            fclose($fp);
        }
        return $rows;
    }

    // PHPExcel (그누보드 lib)
    if (defined('G5_LIB_PATH') && file_exists(G5_LIB_PATH.'/PHPExcel/IOFactory.php')) {
        require_once G5_LIB_PATH.'/PHPExcel/IOFactory.php';
    } elseif (defined('G5_LIB_PATH') && file_exists(G5_LIB_PATH.'/PHPExcel.php')) {
        require_once G5_LIB_PATH.'/PHPExcel.php';
    } else {
        throw new RuntimeException('PHPExcel 라이브러리를 찾을 수 없습니다. (G5_LIB_PATH 확인)');
    }
    if (!class_exists('PHPExcel_IOFactory')) {
        throw new RuntimeException('PHPExcel_IOFactory 클래스를 로드할 수 없습니다.');
    }

    if ($ext === 'xlsx') {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('.xlsx를 읽으려면 ZipArchive 확장이 필요합니다. .xls 또는 .csv로 업로드 해주세요.');
        }
        $reader = PHPExcel_IOFactory::createReader('Excel2007'); // xlsx
    } else {
        $reader = PHPExcel_IOFactory::createReader('Excel5');    // xls
    }

    $reader->setReadDataOnly(true);
    $obj = $reader->load($path);
    $sheet = $obj->getSheet(0);
    return $sheet->toArray(null, true, true, true);
}

/* ===== DB 적재 (TRUNCATE 후 전체 INSERT; 배치 처리) ===== */
function truncate_and_insert_regions($rows){
    global $g5;
    if (!function_exists('sql_query')) throw new RuntimeException('그누보드 DB 함수(sql_query)를 사용할 수 없습니다.');
    if (!$rows || !isset($rows[1])) return 0;

    $idx = map_header_cols($rows[1]);

    // 전체 비우기
    sql_query("TRUNCATE TABLE `wv_location_region`", false);

    // 중복 code 제거 / 값 준비
    $seen = array();
    $values = array();
    $inserted = 0;

    // 트랜잭션(옵션)
    sql_query("START TRANSACTION", false);

    $flush = function() use (&$values, &$inserted){
        if (!$values) return;
        $sql = "INSERT INTO `wv_location_region` (`code`,`depth1`,`depth2`,`depth3`) VALUES ".implode(',', $values);
        $ok  = sql_query($sql, false);
        if ($ok) $inserted += substr_count($sql, '),(') + 1;
        $values = array();
    };

    $BATCH = 800; // 한 번에 800행씩 INSERT (환경에 맞게 조절)

    for ($i=2, $n=count($rows); $i<=$n; $i++) {
        if (!isset($rows[$i])) continue;
        $r = $rows[$i];

        $code = trim((string)($idx['code']         ? $r[$idx['code']]         : ''));
        $sido = trim((string)($idx['sido']         ? $r[$idx['sido']]         : ''));
        $sgg  = trim((string)($idx['sigungu']      ? $r[$idx['sigungu']]      : ''));
        $emd  = trim((string)($idx['eupmyeondong'] ? $r[$idx['eupmyeondong']] : ''));
        $ri   = trim((string)($idx['ri']           ? $r[$idx['ri']]           : ''));

        foreach (array('code','sido','sgg','emd','ri') as $k) {
            if ($$k==='NaN'||$$k==='nan'||$$k==='None') $$k='';
        }
        if ($code==='') continue;

        if (isset($seen[$code])) continue; // 같은 코드 중복 제거
        $seen[$code] = true;

        $depth1 = wv_trans_sido($sido);
        $depth2 = $sgg;
        $depth3 = ($ri!=='' && $ri!==$emd) ? trim($emd.' '.$ri) : $emd;

        // escape
        $code   = sql_escape_string($code);
        $depth1 = sql_escape_string($depth1);
        $depth2 = sql_escape_string($depth2);
        $depth3 = sql_escape_string($depth3);

        $values[] = "('$code','$depth1','$depth2','$depth3')";
        if (count($values) >= $BATCH) $flush();
    }
    $flush();
    sql_query("COMMIT", false);

    return $inserted;
}

/* ===== 업로드 처리 ===== */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['file'])) {
    $f = $_FILES['file'];
    if (!is_uploaded_file($f['tmp_name'])) {
        echo '<div class="alert alert-danger">업로드 실패: 임시 파일이 없습니다. (용량/중첩 form/권한 확인)</div>';
    } else {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, array('xlsx','xls','csv'))) {
            echo '<div class="alert alert-danger">xlsx/xls/csv만 업로드 가능합니다.</div>';
        } else {
            try {
                $rows = load_rows_from_uploaded($f['tmp_name'], $ext);
                $cnt  = truncate_and_insert_regions($rows);
                echo '<div class="alert alert-success">완료! 총 '.$cnt.'행을 wv_location_region에 새로 적재했습니다.</div>';
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">에러: '.htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8').'</div>';
            }
        }
    }
}
?>

<!-- ===== 본문: 업로드 폼 ===== -->
<div    >
    <div  >
        <h5 class="card-title">법정동 엑셀 → DB 적재 (wv_location_region)</h5>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="form-label">엑셀/CSV 파일 선택 (.xlsx / .xls / .csv)</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">업로드 & 전체 리셋</button>
            <div class="form-text">
                * 매 업로드 시 테이블을 비우고(TRUNCATE) 전체를 다시 인서트합니다.<br>
                * 서버에 ZipArchive가 없으면 .xlsx는 불가 → .xls 또는 .csv를 사용하세요.
            </div>
        </form>
    </div>
</div>