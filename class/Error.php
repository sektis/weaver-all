<?php
namespace weaver;

class Error{

    protected $errors = array();

    public function set($plugin, $msg, $level='') {
        if (!is_string($msg)) {
            if (is_object($msg)) $msg = '('.get_class($msg).')';
            else $msg = print_r($msg, true);
        }
        $error_msg = "{$plugin} : {$msg}";

        if($level==2){
            die($error_msg);
        }
        $this->errors[$plugin][$level][] = $msg;
    }

    public function get($plugin) {
        return isset($this->errors[$plugin]) ? (object)$this->errors[$plugin] : (object)[];
    }

    public function all() {
        return (object)$this->errors;
    }

    public function __get($name) {
        return $this->get($name);
    }

}
