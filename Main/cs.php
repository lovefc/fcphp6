<?php

namespace Main;

class cs
{
    use \FC\Traits\Parts;

    public function index($a = 'hello')
    {
        $this->SESSION->set('aaa',222);
        
        echo $this->SESSION->get('aaa');
        /*
        $this->VIEW->assign('text', $a);
        $this->VIEW->display('index');
        */
    }

    public function index2()
    {
        echo $this->SESSION->get('aaa');

    }

}
