<?php

namespace Server;

/*
 * 安全模块
 * 主要针对xss跨站攻击、sql注入等敏感字符串进行过滤
 * @Author: hkshadow
 * @Date: 2019-09-30 09:23:21 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-30 09:34:21
 */

class safeMode
{
    
    /**
     * 执行过滤
     * @param 1 linux/2 http/3 Db/ $group
     * @param 保存路径以及文件名/文件名/null $projectName
     */
    public function xss($group = 1, $projectName = NULL)
    {
        //正则条件
        $referer = empty($_SERVER['HTTP_REFERER']) ? array() : array($_SERVER['HTTP_REFERER']);
        $getfilter = "'|<[^>]*?>|^\\+\/v(8|9)|\\b(and|or)\\b.+?(>|<|=|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        $postfilter = "^\\+\/v(8|9)|\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|<\\s*img\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        $cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

        //遍历过滤
        if (is_array($_GET) && count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                $this->stopAttack($key, $value, $getfilter, $group, $projectName);
            }
        }
        //遍历过滤
        if (is_array($_POST) && count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->stopAttack($key, $value, $postfilter, $group, $projectName);
            }
        }
        //遍历过滤
        if (is_array($_COOKIE) && count($_COOKIE) > 0) {
            foreach ($_COOKIE as $key => $value) {
                $this->stopAttack($key, $value, $cookiefilter, $group, $projectName);
            }
        }
        //遍历过滤

        foreach ($referer as $key => $value) {
            $this->stopAttack($key, $value, $getfilter, $group, $projectName);
        }
    }

    /**
     * 匹配敏感字符串，并处理
     * @param 参数key $strFiltKey
     * @param 参数value $strFiltValue
     * @param 正则条件 $arrFiltReq
     * @param 项目名 $joinName
     * @param 1 linux/2 http/3 Db/ $group
     * @param 项目名/文件名/null $projectName
     */
    public function stopAttack($strFiltKey, $strFiltValue, $arrFiltReq, $group = 1, $projectName = NULL)
    {

        $strFiltValue = $this->arr_foreach($strFiltValue);
        //匹配参数值是否合法
        if (preg_match("/" . $arrFiltReq . "/is", $strFiltValue) == 1) {
            //记录ip
            $ip = "操作IP: " . $_SERVER["REMOTE_ADDR"];
            //记录操作时间
            $time = " 操作时间: " . strftime("%Y-%m-%d %H:%M:%S");
            //记录详细页面带参数
            $thePage = " 操作页面: " . $this->request_uri();
            //记录提交方式
            $type = " 提交方式: " . $_SERVER["REQUEST_METHOD"];
            //记录提交参数
            $key = " 提交参数: " . $strFiltKey;
            //记录参数
            $value = " 提交数据: " . htmlspecialchars($strFiltValue);
            //写入日志
            $strWord = $ip . $time . $thePage . $type . $key . $value;
            //保存为linux类型
            if ($group == 1) {
                $this->log_result_common($strWord, $projectName);
            }
            //保存为可web浏览
            if ($group == 2) {
                $strWord .= "<br>";
                $this->slog($strWord, $projectName);
            }
            //保存至数据库
            if ($group == 3) {
                $this->sDb($strWord);
            }
            //过滤参数
            $_REQUEST[$strFiltKey] = '';
            //这里不作退出处理
            //exit;
        }
    }

    /**
     * 获取当前url带具体参数
     * @return string
     */
    public function request_uri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            if (isset($_SERVER['argv'])) {
                $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['argv'][0];
            } else {
                $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
            }
        }
        return $uri;
    }


    /**
     * 日志记录(linux模式)
     * @param 保存内容 $strWord
     * @param 保存文件名$strPathName
     */
    public function log_result_common($strWord, $strPathName = NULL)
    {
        if ($strPathName == NULL) {
            $strPath = "/var/tmp/";
            $strDay = date('Y-m-d');
            $strPathName = $strPath . "xss_log_" . $strDay . '.log';
        }

        $fp = fopen($strPathName, "a");
        flock($fp, LOCK_EX);
        fwrite($fp, $strWord . " date " . date('Y-m-d H:i:s', time()) . PHP_EOL);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * 写入日志(支持http查看)
     * @param 日志内容 $strWord
     * @param web页面文件名 $fileName
     */
    public function slog($strWord, $fileName = NULL)
    {
        if ($fileName == NULL) {
            $strDay = date('Y-m-d');
            $toppath = $_SERVER["DOCUMENT_ROOT"] . "/xss/xss_log_" .$strDay. ".htm";
        } else {
            $toppath = $fileName;
        }
        $Ts = fopen($toppath, "a+");
        fputs($Ts, $strWord . "\r\n");
        fclose($Ts);
    }

    /**
     * 写入日志(数据库)
     * @param 日志内容 $strWord
     */
    public function sDb($strWord)
    {
        //....
    }

    /**
     * 递归数组
     * @param array $arr
     * @return unknown|string
     */
    public function arr_foreach($arr)
    {
        static $str = '';
        if (!is_array($arr)) {
            return $arr;
        }
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $this->arr_foreach($val);
            } else {
                $str[] = $val;
            }
        }
        return implode($str);
    }
}

$b = new safeMode();

$log = __DIR__ . '/log.html';

$b->xss(2, $log);

echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5);

print_r($_GET);
