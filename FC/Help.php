<?php

namespace FC\Tools;

/*
 * 全局辅助函数
 * @Author: lovefc 
 * @Date: 2019-09-17 10:25:58 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-17 13:25:03
 */

class Helper
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
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;
        if (preg_match('/Windows/', $agent) && preg_match('/98/', $agent)) {
            $os = 'Windows 98';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 5.1/', $agent)) {
            $os = 'Windows XP';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 5/', $agent)) {
            $os = 'Windows 2000';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 6.0/', $agent)) {
            $os = 'WindowsVista';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 6.1/', $agent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 6.2/', $agent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 6.3/', $agent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 6.4/', $agent)) {
            $os = 'Windows 10';
        } elseif (preg_match('/Windows/', $agent) && preg_match('/NT 32/', $agent)) {
            $os = 'Windows 32';
        } elseif (preg_match('/linux/', $agent)) {
            $os = 'Linux';
        } elseif (preg_match('/unix/', $agent)) {
            $os = 'Unix';
        } elseif (preg_match('/sun/', $agent) && preg_match('/os/', $agent)) {
            $os = 'SunOS';
        } elseif (preg_match('/ibm/', $agent) && preg_match('/os/', $agent)) {
            $os = 'IBM OS/2';
        } elseif (preg_match('/Mac/', $agent) && preg_match('/PC/', $agent)) {
            $os = 'Macintosh';
        } elseif (preg_match('/PowerPC/', $agent)) {
            $os = 'PowerPC';
        } elseif (preg_match('/AIX/', $agent)) {
            $os = 'AIX';
        } elseif (preg_match('/HPUX/', $agent)) {
            $os = 'HPUX';
        } elseif (preg_match('/NetBSD/', $agent)) {
            $os = 'NetBSD';
        } elseif (preg_match('/BSD/', $agent)) {
            $os = 'BSD';
        } elseif (preg_match('/OSF1/', $agent)) {
            $os = 'OSF1';
        } elseif (preg_match('/IRIX/', $agent)) {
            $os = 'IRIX';
        } elseif (preg_match('/FreeBSD/', $agent)) {
            $os = 'FreeBSD';
        } elseif (preg_match('/teleport/', $agent)) {
            $os = 'teleport';
        } elseif (preg_match('/flashget/', $agent)) {
            $os = 'flashget';
        } elseif (preg_match('/webzip/', $agent)) {
            $os = 'webzip';
        } elseif (preg_match('/offline/', $agent)) {
            $os = 'offline';
        } elseif (strpos($agent, "Android") !== false) {
            $os = 'Android';
        } elseif (strpos($agent, "iPhone") !== false) {
            $os = 'iPhone';
        } elseif (strpos($agent, "iPad") !== false) {
            $os = 'iPad';
        } elseif (strpos($agent, "Nokia") !== false) {
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
