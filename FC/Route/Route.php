<?php
namespace swoole;

/**
 * 通用路由处理类
 * 作者:lovefc
 * 创建时间：2017/1/3 0:27
 * *
 */

class Route extends Execs
{
    public static $cutting = '/';
    public static $route, $mode, $rvar;
    public static $counters = false, $handleStatus = false;
    public static $counter = 0, $jsq = 0;
    public static $routeval = array();
    public static $parameters = array(), $rule = array(), $returnback = array();
    public static $reback = true;
    public static $query, $query2;

    /*
     * 路由配置
     * @param $value 访问名称 规则
     * @param $array 过滤数组
     */
    public static function set($name, $value, $array = null)
    {
        self::$routeval[$name] = $value;
        if (!is_null($array) && is_array($array)) {
            self::$rule[$name] = $array;
        }
    }

    //判断是否是cgi模式
    public static function isCli()
    {
        if (PHP_SAPI === 'cli') {
            if (self::IsSwooleHttp() === true) {
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }

    //判断swoole
    public static function IsSwooleHttp()
    {
        if (isset($_SERVER['SERVER_SOFTWARE']) && $_SERVER['SERVER_SOFTWARE'] === 'swoole-http-server') {
            return true;
        }
        return false;
    }

    //获取query
    public static function get_query()
    {
        if (self::isCli() === true) {
            $url = isset($_SERVER['argv'][1]) ? strtr($_SERVER['argv'][1], '#', '&') : null;
            if ($url) {
                return ltrim($url, '/');
            }
        }
        $url = '';
        $path_info = empty($_SERVER['PATH_INFO']) ? '' : $_SERVER['PATH_INFO'];
        $orig_path_info = empty($_SERVER['ORIG_PATH_INFO']) ? '' : $_SERVER['ORIG_PATH_INFO'];
        $url = $path_info ? $path_info : $orig_path_info;
        if (!$url) {
            $url = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        }
        if(self::$route != '#^([\w\W]*)#'){
            $url = self::str_replace_limit('&', '?', $url, 1);
        }
        return ltrim($url, '/');
    }

    // 指定字符串替换次数并且过滤字符串
    public static function str_replace_limit($search, $replace, $subject, $limit = 1)
    {
        if (is_array($search)) {
            foreach ($search as $k => $v) {
                $search[$k] = '`' . preg_quote($search[$k], '`') . '`';
            }
        } else {
            $search = '`' . preg_quote($search, '`') . '`';
        }
        return preg_replace($search, $replace, $subject, $limit);
    }

    //获取变量的值
    public static function get_var($Input)
    {
        $keys = explode('__', $Input);
        if (!is_array($_GET)) {
            $_GET = array();
        }
        if (count($keys) == 1) {
            self::$mode = 'GET';
            return array(
                'var' => $_GET,
                'key' => $keys[0],
            );
        }
        $var = $keys[0];
        $var2 = strtoupper($var);
        array_shift($keys);
        switch ($var2) {
            case 'GET':
                $Main = $_GET;
                self::$mode = 'GET';
                break;
            case 'POST':
                $Main = $_POST;
                self::$mode = 'POST';
                break;
            case 'REQUEST':
                $Main = $_REQUEST;
                self::$mode = 'REQUEST';
                break;
            case 'SERVER':
                $Main = $_SERVER;
                self::$mode = 'SERVER';
                break;
            case 'COOKIE':
                $Main = $_COOKIE;
                self::$mode = 'COOKIE';
                break;
            case 'SESSION':
                $Main = $_SESSION;
                self::$mode = 'SESSION';
                break;
            case 'FILES':
                $Main = $_FILES;
                self::$mode = 'FILES';
                break;
        }
        if (!isset($Main)) {
            self::$mode = 'GET';
            return array(
                'var' => $_GET,
                'key' => $keys,
            );
        }
        $keys[0] = isset($keys[0]) ? $keys[0] : '0';
        return array(
            'var' => $Main,
            'key' => $keys[0],
        );
    }

    //解析参数
    public static function analy_var($value, $keys)
    {
        if (self::$jsq == 0) {
            $_GET = array();
            $re = array();
        }
        parse_str(ltrim($value, '?'), $s);
        foreach ($s as $key => $values) {
            if ($values) {
                $re[$key] = $values;
            } else {
                $re[self::$jsq] = $value;
                self::$jsq++;
            }
        }
        if (isset($re) && is_array($re)) {
            $_GET = (array) $_GET + $re;
        }
    }

    //转义字符串
    protected static function _quote($val)
    {
        return preg_quote($val, '/');
    }

    //按照键名高低进行排序后重置键名
    public static function _arrValues($arr)
    {
        ksort($arr);
        return array_values($arr);
    }

    //设置键值获取
    protected static function _counters($_vatr = null)
    {
        $_vatr = empty($_vatr) ? self::$routeval[self::$route] : $_vatr;
        if (is_array($_vatr)) {
            isset($_vatr[2]) && self::$counters = (array) $_vatr[2];
            if (!empty(self::$counters) && count(self::$counters) >= 1) {
                self::$counters = array_map(function ($v) {
                    $v = (int) $v;
                    if ($v > 0) {
                        $v--;
                    }
                    return $v;
                }, self::$counters);

                $arr = array();
                foreach (self::$counters as $key => $value) {
                    $value = (int) $value;
                    $arr[$value] = isset($_GET[$key]) ? $_GET[$key] : null;
                }
                $_GET = self::_arrValues($arr);
            }
        }
    }

    //检测是否为伪静态模式
    public static function isRewrite($url)
    {
        foreach (self::$routeval as $key => $value) {
            $shift_str = isset($key[0]) ? $key[0] : null;
            if (preg_match('#\W#', $shift_str) === 0) {
                continue;
            }
            if (strrchr($key, $shift_str) == $shift_str) {
                try {
                    if (preg_match($key, $url, $strs)) {
                        self::$route = $key;
                        array_shift($strs);
                        array_walk($strs, array(
                            'self',
                            'analy_var',
                        ));
                        $get = $_GET;
                        self::_counters();
                        $_GET = $_GET + $get;
                        return true;
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Regular Expression Error');
                }
            }
        }
        return false;
    }

    //解析query
    public static function queryHandle()
    {
        if (self::$handleStatus == true) {
            return false;
        }
        $url = self::get_query();
        if (self::isRewrite($url) !== true) {
            $m = explode(self::$cutting, $url);
		    $end = end($m);
            $purl = parse_url($end);
		    $key = key($m);
		    $key2 = $key + 1;
            if (isset($purl['path']) && $purl['path'] != $end) {
                $m[$key] = isset($purl['path']) ? $purl['path'] : $m[$key];
            }
            if (isset($purl['query'])) {
                $m[$key2] = $purl['query'];
            }
            if (count($m) >= 1) {
                self::$route = $m[0];
                if (isset(self::$routeval[self::$route])) {
                    $_vatr = self::$routeval[self::$route];
                } else {
                    $a1 = $a2 = array();
                    self::$route && parse_str(self::$route, $a1);
                    $m && parse_str($url, $a2);
                    $_GET = array_merge($a1, $a2);
                }
                if (isset($_vatr) && !parent::isFunc($_vatr)) {
                    if (!is_array($_vatr)) {
                        self::$query = isset($m[1]) ? $m[1] : null;
                        $var = trim($_vatr);
                        $len = strlen($var) - 1;
                        if ($var{$len} == '\\') {
                            self::$query2 = isset($m[2]) ? $m[2] : null;
                            unset($m[2]);
                        }
                        unset($m[1]);
                        unset($m[0]);
                        array_walk($m, array(
                            'self',
                            'analy_var',
                        ));
                    } else {
                        unset($m[0]);
                        array_walk($m, array(
                            'self',
                            'analy_var',
                        ));
                        self::_counters($_vatr);
                    }
                } else {
                    self::$rvar = $m[0];
                    unset($m[0]);
                    array_walk($m, array(
                        'self',
                        'analy_var',
                    ));
                }
            }
        }
        self::$counters && self::$counters = self::_arrValues(self::$counters);
        self::$handleStatus == true;
    }

    //执行代码
    public static function run()
    {
        self::queryHandle();
        $pz = self::$routeval;
        if (array_key_exists(self::$route, $pz)) {
            if ($reback = self::funcHandle($pz[self::$route])) {
                self::reback($reback);
            }
        } else {
            if (array_key_exists('default', $pz)) {
                self::$route = '#^([\w\W]*)#';
                if(strpos(self::$rvar, '=')){
                    parse_str(self::$rvar,$GET);
                    $_GET = $GET + $_GET;
                }else{
                    array_unshift($_GET, self::$rvar);
                }
                array_filter($_GET);
                if ($reback = self::funcHandle($pz['default'])) {
                    self::reback($reback);
                }
            }
        }
    }

    //返回值处理
    public static function reback($reback)
    {
        if (self::$reback == true) {
            if (is_array($reback)) {
                if (self::isCli() === true) {
                    print_r($reback);
                } else {
                    echo '<pre>' . htmlspecialchars(print_r($reback, true)) . '</pre>';
                }
            } else {
                if (is_object($reback)) {
                    throw new \Exception('method: does not exist');
                } else {
                    echo $reback;
                }
            }
        } else {
            self::$returnback[self::$route] = $reback;
        }
    }

    //判断解析
    public static function funcHandle($func)
    {
        if (is_array($func)) {
            return parent::method($func);
        } else {
            if (!empty(self::$query2)) {
                $func = $func . self::$query;

                $func = array(
                    $func,
                    self::$query2,
                );
                return parent::method($func);
            }
            if (!empty(self::$query)) {
                $func = array(
                    $func,
                    self::$query,
                );
                return parent::method($func);
            }
            return parent::func($func);
        }
    }

    //获取方法参数
    public static function getMethodVar($Parameters)
    {
        $vars = array();
        if (is_array($Parameters) && count($Parameters) > 0) {
            $Parameters = array_values($Parameters);
            foreach ($Parameters as $js => $value) {
                if (is_object($value)) {
                    $getname = $value->getname();
                    $re = self::get_var($getname);
                    $var = $re['var'];
                    $key = $re['key'];
                    if (!is_array($key) && !isset($var[$key])) {
                        $getvar = '';
                        if (is_array(self::$counters)) {
                            if (in_array($js, self::$counters)) {
                                $getvar = isset($var[self::$counter]) ? self::varHandle($getname, $var[self::$counter]) : null;
                                if (self::$mode == 'GET') {
                                    self::$counter++;
                                }
                            }
                        } else {
                            $getvar = isset($var[self::$counter]) ? self::varHandle($getname, $var[self::$counter]) : null;
                            if (self::$mode == 'GET') {
                                self::$counter++;
                            }
                        }
                        $defaultvar = ($value->isDefaultValueAvailable()) ? $value->getDefaultValue() : null;
                        $vars[] = $getvar != null ? $getvar : $defaultvar;
                    } else {
                        $vars[] = self::varHandle($getname, $var[$key]);
                    }
                } else {
                    $vars[] = $value;
                }
            }
        }
        return $vars;
    }

    //判断参数
    public static function varHandle($name, $str)
    {
        $preg = isset(self::$rule[self::$route][$name]) ? self::$rule[self::$route][$name] : false;
        return parent::regularHandle($preg, $str);
    }
}
