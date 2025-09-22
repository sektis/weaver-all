<?php
namespace weaver;

include_once dirname(__FILE__).'/Info.php';
include_once dirname(__FILE__).'/Configs.php';
include_once dirname(__FILE__).'/Error.php';
include_once dirname(__FILE__).'/Plugin.php';
include_once dirname(__FILE__).'/PluginProps.php';
include_once dirname(__FILE__).'/Makeable.php';

class Weaver {

    private static $core;
    private static $info;
    private static $error;
    private static  $configs = array();
    protected static $plugins;
    protected static $plugins_props;


//    protected $temp_plugin_name;

    public static function getInstance() {

        if (!self::$core) {
            self::$core = new static();
            self::$info = new Info();
            self::$error = new Error();
            self::$configs = new Configs();
            self::$plugins_props = new PluginProps();


        }

        return self::$core;
    }

    public function load($plugin){

        foreach ((array) $plugin as $p){
            $p = trim($p);
            if($p=='')continue;

            if(!isset($this->plugins->$p)){;
                if(!wv_plugin_exists($p)){
                    $this->error($p.' plugin not found',2);
                    return false;
                }

                include_once WV_PLUGINS_PATH.'/'.$p.'/plugin.php';
            }
        }

        return self::$plugins[$plugin];
    }

    public function __get($name) {

        $get_name = strtolower($name);

        if($get_name=='plugins'){return self::$plugins;}
        else if($get_name=='info'){return self::$info;}
        else if($get_name=='configs'){return self::$configs;}
        else if($get_name=='error'){return self::$error;}
        else if(wv_plugin_exists($name)){return $this->load($name);}

        return null;
    }

    public function __call($name,$value){

        if($this->temp_plugin_name){
            $plugin_name = $this->temp_plugin_name;
        }else{
            $class = get_called_class();
            $plugin_name = wv_class_to_plugin_name($class,true);
        }

        self::$error->set($plugin_name,$value[0],$value[1]);
    }



}
Weaver::getInstance();