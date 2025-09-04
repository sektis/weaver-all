<?php
namespace weaver;

trait RegionApi
{
    protected $table = 'wv_location_region';

    public function api($params)
    {
        $a    = isset($params['a'])    ? trim($params['a']) : '';
        $d1   = isset($params['d1'])   ? trim($params['d1']) : '';
        $d2   = isset($params['d2'])   ? trim($params['d2']) : '';
        $cd   = isset($params['code']) ? trim($params['code']) : '';
        $q    = isset($params['q'])    ? trim($params['q']) : '';
        $lim  = isset($params['limit'])? (int)$params['limit'] : 50;
        $sort = isset($params['sort']) ? trim($params['sort']) : 'name';
        if ($lim <= 0 || $lim > 500) $lim = 50;
        $sort = ($sort === 'code') ? 'code' : 'name';

        if (!function_exists('sql_query')) {
            return $this->jres(['ok'=>false,'error'=>'그누보드 DB 환경이 아닙니다.'], 500);
        }

        switch ($a) {
            case 'depth1':
                return $this->depth1($sort);
            case 'depth2':
                if ($d1 === '') return $this->bad('파라미터 d1(시/도)이 필요합니다.');
                return $this->depth2($d1, $sort);
            case 'depth3':
                if ($d1 === '' || $d2 === '') return $this->bad('파라미터 d1, d2가 필요합니다.');
                return $this->depth3($d1, $d2, $sort);
            case 'code':
                if ($cd === '') return $this->bad('파라미터 code가 필요합니다.');
                return $this->code($cd);
            case 'search':
                if ($q === '') return $this->bad('파라미터 q가 필요합니다.');
                return $this->search($q, $d1, $d2, $lim, $sort);
            default:
                return $this->jres([
                    'ok'=>true,
                    'usage'=>[
                        'depth1' => '?a=depth1&sort=code',
                        'depth2' => '?a=depth2&d1=서울&sort=code',
                        'depth3' => '?a=depth3&d1=서울&d2=강남구&sort=code',
                        'code'   => '?a=code&code=1168010100',
                        'search' => '?a=search&q=개포&d1=서울&d2=강남구&limit=50&sort=code'
                    ]
                ]);
        }
    }

    protected function depth1($sort='name')
    {
        if ($sort === 'code') {
            $sql = "
                SELECT depth1, MIN(code) AS min_code
                FROM `{$this->table}`
                GROUP BY depth1
                ORDER BY min_code ASC
            ";
            $rs = sql_query($sql);
            $rows = [];

        } else {
            $sql = "SELECT DISTINCT depth1 FROM `{$this->table}` ORDER BY depth1 ASC";
            $rs  = sql_query($sql);
            $rows = [];
        }

        while ($r = sql_fetch_array($rs)) {
            if (mb_strpos($r['depth1'], '출장소') !== false) {
                continue;
            }
            $rows[] = $r['depth1'];
        }
        return $this->jres(['ok'=>true, 'depth1'=>$rows, 'sorted_by'=>'code']);
    }

    protected function depth2($d1, $sort='name')
    {
        $d1s = sql_escape_string($d1);
        if ($sort === 'code') {
            $sql = "
                SELECT depth2, MIN(code) AS code
                FROM `{$this->table}`
                WHERE depth1='$d1s' and depth2<>''
                GROUP BY depth2
                ORDER BY code ASC
            ";
        } else {
            $sql = "
                SELECT depth2, MIN(code) AS code
                FROM `{$this->table}`
                WHERE depth1='$d1s' and depth2<>''
                GROUP BY depth2
                ORDER BY depth2 ASC
            ";
        }
        $rs  = sql_query($sql);
        $rows = [];
        $rows[] = ['code'=>$d1s, 'name'=>'전체'];
        while ($r = sql_fetch_array($rs)) {
            $rows[] = ['code'=>$r['code'], 'name'=>$r['depth2']];
        }
        return $this->jres(['ok'=>true, 'depth1'=>$d1, 'depth2'=>$rows, 'sorted_by'=>$sort]);
    }

    protected function depth3($d1, $d2, $sort='name')
    {
        $d1s = sql_escape_string($d1);
        $d2s = sql_escape_string($d2);

        $order = ($sort === 'code') ? 'code ASC' : 'depth3 ASC';
        $where = "depth1='$d1s' and depth3<>''";
        if($d2s!='전체'){
            $where.=" and depth2='$d2s' ";
        }

        $sql = "
            SELECT code, depth2, depth3
            FROM `{$this->table}`
            WHERE $where
            ORDER BY $order
        ";

        $rs  = sql_query($sql);
        $rows = [];
        while ($r = sql_fetch_array($rs)) {
            $rows[] = ['code'=>$r['code'], 'depth2_name'=>$r['depth2'], 'name'=>$r['depth3']];
        }
        return $this->jres(['ok'=>true, 'depth1'=>$d1, 'depth2'=>$d2, 'items'=>$rows, 'sorted_by'=>$sort]);
    }

    protected function code($code)
    {
        $cds = sql_escape_string($code);
        $sql = "SELECT code, depth1, depth2, depth3 FROM `{$this->table}` WHERE code='$cds' LIMIT 1";
        $row = sql_fetch($sql);
        if (!$row) return $this->bad('not found', 404);
        return $this->jres(['ok'=>true, 'item'=>$row]);
    }

    protected function search($q, $d1='', $d2='', $limit=50, $sort='name')
    {
        $qs = sql_escape_string($q);
        $where = "depth3 LIKE '%$qs%'";
        if ($d1 !== '') $where .= " AND depth1='".sql_escape_string($d1)."'";
        if ($d2 !== '') $where .= " AND depth2='".sql_escape_string($d2)."'";
        $order = ($sort === 'code') ? 'code ASC' : 'depth1 ASC, depth2 ASC, depth3 ASC';

        $sql = "
            SELECT code, depth1, depth2, depth3
            FROM `{$this->table}`
            WHERE $where
            ORDER BY $order
            LIMIT $limit
        ";
        $rs = sql_query($sql);
        $rows = [];
        while ($r = sql_fetch_array($rs)) {
            $rows[] = [
                'code'=>$r['code'],
                'depth1'=>$r['depth1'],
                'depth2'=>$r['depth2'],
                'name'=>$r['depth3']
            ];
        }
        return $this->jres(['ok'=>true, 'q'=>$q, 'count'=>count($rows), 'items'=>$rows, 'sorted_by'=>$sort]);
    }

    protected function jres($data, $status=200)
    {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function bad($msg, $code=400)
    {
        return $this->jres(['ok'=>false, 'error'=>$msg], $code);
    }
}
