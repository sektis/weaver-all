<?php
namespace weaver;

class Configs {
    protected $configs = [];

    public function set($plugin, $key, $value='') {

        if(is_array($key)){
            $this->configs[$plugin] = $key;
        }else{
            $this->configs[$plugin][$key] = $value;
        }
    }

    public function add($plugin, $key, $value='') {
        $this->configs[$plugin][$key] = array_merge_recursive((array) $this->configs[$plugin][$key],(array) $value);
    }

    public function get($plugin) {
        return isset($this->configs[$plugin]) ? $this->configs[$plugin] : null;
    }

    public function all() {
        return (object)$this->configs;
    }

    public function __get($name) {
        return $this->get($name);
    }


}
