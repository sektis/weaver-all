<?php
namespace weaver;

class Widget extends Makeable {

    public function __construct(){

    }

    public function init_once(){

    }

    public function display_widget($skin,$data=''){

        return $this->make_skin($skin,$data);
    }

}
Widget::getInstance();