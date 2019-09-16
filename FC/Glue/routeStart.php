
<?php
namespace fcphp\Glue;
use FC\Route;

/*
 * 路由中间件
 * @Author: lovefc 
 * @Date: 2019-09-16 15:05:57 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-16 15:13:09
 */

class Route extends Route
{
    // 继承配置
    use \FC\Traits\Parents;
    
    // 初始设置
    public function init()
    {
        self::$routeval = $this->P_Config;
        self::$rule = self::P_Receive('rule.php');
    }
    
    // 错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }    
}
