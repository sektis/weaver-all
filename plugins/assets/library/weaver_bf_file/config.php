<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

add_event('write_update_before', 'wv_assets_plugin_write_update_before',1,4);
add_event('write_update_file_insert', 'wv_assets_plugin_write_update_file_insert',1,3);


function wv_assets_plugin_write_update_before($board, $wr_id, $w, $qstr){
    global $g5;
    $temp_array = array();
    if(wv_is_ajax() and is_array($_POST['bf_file_del'])){
        foreach ($_POST['bf_file_del'] as $key=>$val){
            if(!$_FILES['bf_file']['name'][$key]){
                $_FILES['bf_file']['name'][$key]='.';
            }
        }

    }
    $bf_file_names = $_FILES['bf_file']['name'];
    if(!is_array($bf_file_names) or !count($bf_file_names))return;
    krsort($bf_file_names);
    $bf_last_key = key($bf_file_names);



    for($i=0;$i<$bf_last_key;$i++){
        if(isset($bf_file_names[$i]))continue;
        $_FILES['bf_file']['name'][$i]='';
    }

    foreach ($_FILES['bf_file'] as $name=>$arr){
        foreach ($arr as $key=>$val){
            $temp_array[$key][$name]=$val;
        }
    }

    $temp_db_array = array();
    $sql = "select * from {$g5['board_file_table']} where bo_table = '{$board['bo_table']}' and wr_id = '{$wr_id}' ";
    $result = sql_query($sql);

    while ($row = sql_fetch_array($result)){
        $temp_db_array[$row['bf_no']] = $row;
    }


    $bf_fields = sql_field_names($g5['board_file_table']);

    $delete_keys = [];

    foreach ($temp_array as $key=>$arr){


        if(isset($_POST['wv_multiple_order'][$key]) and $key!=$_POST['wv_multiple_order'][$key]){

            foreach ($temp_array[$key] as $name=>$val){

                $_FILES['bf_file'][$name][$_POST['wv_multiple_order'][$key]]=$val;
            }

            if($_POST["bf_file_del"][$key]==1){
                unset($_POST["bf_file_del"][$key]);
                $delete_keys[] = $_POST['wv_multiple_order'][$key];
            }

            $set_arr = array();
            foreach ($bf_fields as $field){

                if($field=='bf_no'){
                   continue;
                }
                if($field=='bo_table'){
                    $set_arr[] = " bo_table = '{$board['bo_table']}' ";

                }else if($field=='wr_id'){
                    $set_arr[] = " wr_id = '{$wr_id}' ";

                }else{
                    $set_arr[] = " {$field} = '{$temp_db_array[$key][$field]}' ";
                }

            }
            $set_sql = implode(',',$set_arr);


            if($temp_db_array[$_POST['wv_multiple_order'][$key]]){
//                echo "update {$g5['board_file_table']} set {$set_sql} where bo_table='{$board['bo_table']}' and  wr_id='{$wr_id}' and bf_no='{$_POST['wv_multiple_order'][$key]}'<br>";
                sql_query("update {$g5['board_file_table']} set {$set_sql} where bo_table='{$board['bo_table']}' and  wr_id='{$wr_id}' and bf_no='{$_POST['wv_multiple_order'][$key]}'");
            }else{
                $set_arr[] = " bf_no = '{$_POST['wv_multiple_order'][$key]}' ";
                $set_sql = implode(',',$set_arr);
//                echo "insert into {$g5['board_file_table']} set {$set_sql}<br>";
                sql_query("insert into {$g5['board_file_table']} set {$set_sql}");
            }

        }
    }


    foreach ($delete_keys as $k){
        $_POST["bf_file_del"][$k] = 1;
    }
}



function wv_assets_plugin_write_update_file_insert($bo_table, $wr_id, $upload){

    global $g5,$i;
    if($upload['file'])return;

    sql_query(" delete from {$g5['board_file_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and bf_file = '' ");




}

add_event('tail_sub', 'wv_assets_plugin_file_preview_tail_sub',1,0);

function wv_assets_plugin_file_preview_tail_sub(){

    global $wr_id,$w,$board;
    if($wr_id and $w=='u'){
        include_once dirname(__FILE__).'/inc_write.php';
    }

}