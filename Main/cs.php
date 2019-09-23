<?php

namespace Main;

class cs
{
    use \FC\Traits\Parts;

    public function index($a = 'hello')
    {
        $this->VIEW->assign('text', $a);
        $this->VIEW->display('index');
    }
}
