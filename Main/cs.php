<?php
namespace Main;

class cs

{
    use \FC\Traits\Parts;

    public function index()
    {
        $this->VIEW->display('index');
    }
}
