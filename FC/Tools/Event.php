<?php

namespace fcphp\extend;

/**
 * 网上找的代码，我来简单的说明一下原理，超级简单 -- by lovefc
 */

class Event
{
    //记录事件
    protected static $listens = array();

    /*
     * 绑定一个事件
     * $event 事件名称
     * $callback 执行方法
     * $once 是否执行一次？
     */
    public static function listen($event, $callback, $once = false)
    {
        if (!is_callable($callback)) {
            return false;
        }
        self::$listens[$event][] = array('callback' => $callback, 'once' => $once);
        return true;
    }

    /*
     * 就是把上面的方法封装了一下,默认第三个参数为true
     */
    public static function one($event, $callback)
    {
        return self::listen($event, $callback, true);
    }

    /*
     * 移除事件
     * 就是从self::$listens数组中删除而已
     */
    public static function remove($event, $index = null)
    {
        if (is_null($index)) {
            unset(self::$listens[$event]);
        } else {
            unset(self::$listens[$event][$index]);
        }
    }

    /*
     * 触发事件
     */
    public static function trigger()
    {
        //判断有没有参数
        if (!func_num_args()) {
            return;
        }
        //获取参数
        $args = func_get_args();
        //取得第一个参数，那是事件的名称
        $event = array_shift($args);
        //检测该事件有没有被注册
        if (!isset(self::$listens[$event])) {
            return false;
        }
        //没有？非常好,开始执行这些事件
        foreach ((array)self::$listens[$event] as $index => $listen) {
            //取得要执行的函数或者匿名方法这里其实应该加个判断
            $callback = $listen['callback'];
            //判断是不是只执行一次，其实就是如果检测到once为true，就删除掉这个事件，这样下次就不会执行了，简直无脑，可以判断状态不执行啊
            $listen['once'] && self::remove($event, $index);
            //执行函数，不要解析太多，后面跟的是方法和参数名，这里可以任意发挥！
            call_user_func_array($callback, $args);
        }
    }
}
