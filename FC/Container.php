<?php

namespace FC;

/* 
 * 容器类实现
 * @Author: lovefc 
 * @Date: 2019-09-06 08:54:09 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-05 23:22:21
 */

class Container
{
    private static $s = array();

    /**
     * 创建值
     *
     * @param [type] $k
     * @param [type] $c
     * @return void
     */
    public static function set($k, $c)
    {
        self::$s[$k] = $c;
    }

    /**
     * 获取值
     *
     * @param [type] $k
     * @return void
     */ 
    public static function get($k)
    {
        $obj =  self::build(self::$s[$k]);
        return $obj;
    }

    /**
     * 自动绑定（Autowiring）自动解析（Automatic Resolution）
     *
     * @param string $className
     * @return object
     */
    public static function build($className)
    {
        // 如果是匿名函数（Anonymous functions），也叫闭包函数（closures）
        if ($className instanceof Closure) {
            // 执行闭包函数，并将结果返回
            return $className($this);
        }

        /*通过反射获取类的内部结构，实例化类*/
        $reflector = new \ReflectionClass($className);
        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Can't instantiate this.");
        }
        /** @var ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();
        // 若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return new $className;
        }
        // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();
        // 递归解析构造函数的参数
        $dependencies = self::getDependencies($parameters);
        // 创建一个类的新实例，给出的参数将传递到类的构造函数。
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * 获取构造方法里面的默认值
     * 
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public static function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                // 是变量,有默认值则设置默认值
                $dependencies[] = self::resolveNonClass($parameter);
            } else {
                // 是一个类，递归解析
                $dependencies[] = self::build($dependency->name);
            }
        }
        return $dependencies;
    }

    /**
     * 检测并获取默认值
     * 
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws Exception
     */
    public static function resolveNonClass($parameter)
    {
        // 有默认值则返回默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
    }
}
