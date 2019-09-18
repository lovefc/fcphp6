<?php

namespace FC;

/*
 * 全局辅助函数
 * @Author: lovefc 
 * @Date: 2019-09-17 10:25:58 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-18 16:21:55
 */

class Help
{

    /**
     * 获取客户端ip
     *
     * @return void
     */
    public static function GetIP()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"]) && strcasecmp($_SERVER["HTTP_CLIENT_IP"], "unknown")) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], "unknown")) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                if (isset($_SERVER["REMOTE_ADDR"]) && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown")) {
                    $ip = $_SERVER["REMOTE_ADDR"];
                } else {
                    if (
                        isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp(
                            $_SERVER['REMOTE_ADDR'],
                            "unknown"
                        )
                    ) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    } else {
                        $ip = "unknown";
                    }
                }
            }
        }
        return $ip;
    }


    /**
     * 获取客户端类型(简单检测)
     *
     * @return void
     */
    public static function GetOS()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $os = false;
        if (strpos($agent, 'Windows')) {
            $os = 'Windows';
        } elseif (strpos($agent, "Linux")) {
            $os = 'Linux';
        } elseif (strpos($agent, "Android")) {
            $os = 'Android';
        } elseif (strpos($agent, "iPhone")) {
            $os = 'iPhone';
        } elseif (strpos($agent, "iPad")) {
            $os = 'iPad';
        } elseif (strpos($agent, "Nokia")) {
            $rel = 'Nokia';
        } else {
            $os = 'Unknown';
        }
        return $os;
    }

    /**
     * 获取精确时间戳
     *
     * @return float
     */
    public static function GetMilliSecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 加解密字符串
     * 
     * @param string $string：需要加密解密的字符串；
     * @param D|E $operation：判断是加密还是解密，E表示加密，D表示解密；
     * @param string $key：密匙。
     * @return string
     */
    public static function Encrypt($string, $operation = 'E', $key = 'ytbsjhskbsabqanl123456')
    {
        $key = md5($key);
        $key1 = substr($key, 0, 15);
        $key2 = substr($key, 15);
        if ($operation == 'E') {
            $str = base64_encode($string);
            $str = $key1 . $str . $key2;
            return base64_encode($str);
        }
        if ($operation == 'D') {
            return base64_decode(str_replace(array(
                $key1,
                $key2
            ), '', base64_decode($string)));
        }
    }

    /**
     * json 操作函数 
     * 
     * @param string $str 要操作的字符串(数组)
     * @param D|E $operation 判断生成还是解析，D为解析。E为生成
     * @param bool $st 解析返回的形式，true为数组，false为对象
     * @return void
     */
    public static function Json($str, $operation = 'E', $st = true)
    {
        $re = false;
        if ($operation === 'D') {
            $re = json_decode($str, $st);
        }
        if ($operation == 'E') {
            $re = json_encode($str);
        }
        return $re;
    }

    /**
     * xml 操作函数
     * 
     * @param array|string $str 要操作的字符串(数组)
     * @param E|D $operation 判断生成还是解析，D为解析。E为生成
     * @param bool $st 解析返回的形式，true为数组，false为对象
     * @return void
     */
    public static function Xml($str, $operation = 'E', $st = true)
    {
        if ($operation == 'E') {
            $xml = "<xml>";
            foreach ($arr as $key => $val) {
                if (is_array($val)) {
                    $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
                } else {
                    $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
                }
            }
            $xml .= "</xml>";
            return $xml;
        }
        if ($operation === 'D') {
            if ($st == true) {
                return (array) simplexml_load_string($str);
            } else {
                return (object) simplexml_load_string($str);
            }
        }
    }
}
