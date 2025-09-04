<?php
namespace weaver;

class Makeable extends Plugin{

    protected $execute_once = false;
    protected $make_id;

    public static function getInstance() {

        $class = get_called_class();
        $plugin_name = wv_class_to_plugin_name($class,true);

        if (!isset(parent::$plugins[$plugin_name])) {


            $instance = new Makeable();
            $instance->plugin_init($plugin_name);

            parent::$plugins[$plugin_name] = $instance;

        }

        return parent::$plugins[$plugin_name];
    }

    public function make($id='') {
        $tem_id = $id;
        // 기본값이 숫자일 경우 중복되면 숫자 증가
        if($id===''){
            $max_int_id = max(array_filter(array_keys($this->get_instance()), 'is_int'));
            $id = $max_int_id+1;
        }
        if($this->get_instance($id)){

            $this->error('이미 생성되어있습니다 : '.$id,2);
            return false;
        }


        // 추가 인자 수집
        $args = [$id];
        $numArgs = func_num_args();
        for ($i = 1; $i < $numArgs; $i++) {
            $args[] = func_get_arg($i);
        }

        $class = 'weaver\\'.wv_plugin_name_to_class($this->plugin_name);

        $reflect = new \ReflectionClass($class);
        $instance = $reflect->newInstanceArgs($args);

        if(method_exists($instance,'init_once') and $this->execute_once ==false){
            $instance->init_once();
            $this->execute_once = true;
        }
        $instance->make_id = $id;
        $this->add_instance($id,$instance);


        return $instance;
    }

    public function made($id = ''){

        if($id==''){
            $id=1;
        }
        $instance = $this->get_instance($id);
        if($instance===false){
            $this->error("{$id} instance not found",1);
            return $this;
        }
        return $instance;
    }

}