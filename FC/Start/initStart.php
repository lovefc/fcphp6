<?php
namespace fcphp\start;
use fcphp\core\Execs;

class initStart extends Execs
{
    use \fcphp\traits\Parents;
    
    // 运行
    public function run()
    {
        count($this->P_Config)>1?ksort($this->P_Config):'';
        $config = $this->P_Config;
        if (is_array($config) && count($config)>=1) {
            foreach ($config as $value) {
                if (is_array($value)) {
                    self::method($value);
                } else {
                    self::func($value);
                }
            }
        }
    }
    
    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }    
}
