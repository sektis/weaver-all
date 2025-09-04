<?php
namespace weaver;

class Info{

    private $info = [];

    public function __construct() {

        $path_info = pathinfo($_SERVER['SCRIPT_NAME']);
        $path_info = str_replace('\\', '/',$path_info);
        $this->info['path'] = ltrim($path_info['dirname'], '/');
        $this->info['dir'] = strtok($this->info['path'],'/'.WV_SUB_DIR);
        $this->info['file'] = $path_info['filename'];
        $this->info['type'] = $this->get_location_type();
    }

    private function get_location_type(){
        global $wv_page_id, $bo_table;

        if(defined('G5_IS_ADMIN')){
            return 'admin';
        }
        if ($this->info['dir'] == 'bbs' and in_array($this->info['file'], array('board', 'write'))) {
            return 'board';
        }

        if ($this->info['dir'] == 'plugin') {
            return 'plugin';
        }

        if ($this->info['dir'] == '' and (!defined('G5_COMMUNITY_USE') or (defined('G5_COMMUNITY_USE') and  G5_COMMUNITY_USE!==false)) and $this->info['file'] == 'index') {
            return 'index';
        }

        if (($this->info['dir'] == 'shop' or (defined('G5_COMMUNITY_USE') and G5_COMMUNITY_USE!==false)) and $this->info['file'] == 'index') {
            return 'shop_index';
        }

        if ($this->info['dir'] == 'shop') {
            return 'shop';
        }

        if ($this->info['dir'] == 'bbs' and !$bo_table) {
            return 'bbs';
        }

        return 'other';
    }

    public function location_type_update($type){
        $this->info['type'] = $type;
    }

    public function __get($name) {

        $get_name = strtolower($name);
        return $this->info[$get_name];
    }

}