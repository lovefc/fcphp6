<?php
namespace fcphp\start;

use fcphp\core\LoaderClass;

/**
 * 处理公共中的加载配置
 * Created by lovefc.
 * Date: 2018/7/12
 * Time: 17:03
 */

class loadStart
{
    use \fcphp\traits\Parents;

    // 初始化操作
    public function init()
    {
        LoaderClass::AddFile($this->P_Config);
    }
    
    // 类扩展设置
    public function ExtendConfig($file = null)
    {
        $namespace = self::P_GetConfigFile($file);
        if ($namespace) {
            LoaderClass::AddFile($namespace);
        }
    }
    
    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }    
}
