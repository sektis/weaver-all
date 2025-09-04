<?php
namespace weaver;

class Theme extends Plugin {

    protected function __construct() {
        add_event('write_update_before',array($this,'counsel_write_update_before'),0,4);
        add_event('write_update_after',array($this,'counsel_write_update_after'),0,5);
        add_replace('wv_hook_board_list_i',array($this,'counsel_list_name_mask'),0,2);

    }


    public function counsel_write_update_before($board, $wr_id, $w, $qstr){
        if(strpos($board['bo_table'], 'counsel')!==false){
            global $is_guest;
            $is_guest=false;
        }
    }


    public function counsel_list_name_mask($row, $i){
        global $bo_table,$is_admin;
        if(strpos($bo_table, 'counsel')!==false and !$is_admin){

            $row['name'] = wv_mask_name($row['wr_name']);
            return $row;
        }
    }

    public function counsel_write_update_after($board, $wr_id, $w, $qstr, $redirect_url){

        global $write_table;

        if($_POST['wr_name']){
            $post_wr_name = clean_xss_tags(trim($_POST['wr_name']));
            $set_array[] = " wr_name = '{$post_wr_name}' ";
        }
        if($_POST['wr_email']){
            $post_wr_email = get_email_address(trim($_POST['wr_email']));
            $set_array[] = " wr_email = '{$post_wr_email}' ";
        }
        if($_POST['wr_password']){
            $post_wr_password = get_encrypt_string(trim($_POST['wr_password']));
            $set_array[] = " wr_password = '{$post_wr_password}' ";
        }

        if(count($set_array)){
            $set_sql = implode(',',$set_array);
            sql_query("update {$write_table} set {$set_sql} where wr_id='{$wr_id}'",1);
        }
    }


}
Theme::getInstance();