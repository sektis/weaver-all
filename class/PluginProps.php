<?php
namespace weaver;

class PluginProps{
    protected $props = array('plugin_name'=>'');

    public function set($plugin, $key, $value='') {

        if(is_array($key)){
            $this->props[$plugin] = $key;
        }else{
            $this->props[$plugin][$key] = $value;
        }
    }

    public function add($plugin, $key, $value='') {
        $this->props[$plugin][$key] = array_merge_recursive((array) $this->props[$plugin][$key],(array) $value);
    }

    public function get($plugin) {
        return isset($this->props[$plugin]) ? (object)$this->props[$plugin] : null;
    }

    public function all() {
        return (object)$this->props;
    }

    public function __get($name) {
        return $this->get($name);
    }




}
