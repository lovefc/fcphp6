<?php

namespace FC\Http;

/*
 * 验证码类库
 * @Author: lovefc 
 * @Date: 2019-09-27 14:35:05 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-27 17:44:47
 */


class ValiCode
{

    // 验证码宽度
    public $width = 200;

    // 验证码高度
    public $height = 60;

    // 验证码个数
    public $nums = 4;

    // 随机数
    public $random = '1234567890zxcvbnmasdfghjklqwertyuiop';

    // 随机数大小
    public $font_size = 38;

    // 字体路径
    public $font_url = '';

    // 验证码
    public $code;

    /**
     * 获取验证码
     *
     * @return void
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 生成验证码
     *
     * @param integer $w 宽度
     * @param integer $h 高度
     * @param integer $nums 数量
     * @param string $random 随机字符串
     * @return void
     */
    public function doImg()
    {
        $this->code = strtolower($this->setVerNumber());
        $im = $this->createImageSource();
        $this->setBackGroundColor($im);
        $this->setCode($im, $this->code);
        $this->setRandomCode($im);
        header("Content-type: image/png");
        Imagepng($im);
        ImageDestroy($im);
        exit;
    }

    /**
     * 创建个画布
     *
     * @return void
     */
    private function createImageSource()
    {
        return imagecreate($this->width, $this->height);
    }

    /**
     * 设置背景颜色
     *
     * @param [type] $im
     * @return void
     */
    private function setBackGroundColor($im)
    {
        $bgcolor = ImageColorAllocate($im, rand(200, 255), rand(200, 255), rand(200, 255));
        imagefill($im, 0, 0, $bgcolor);
    }

    /**
     * 加入随机数
     *
     * @param [type] $im
     * @return string
     */
    private function setRandomCode($im)
    {
        $count_h = $this->height;
        $cou = floor($count_h * 1);
        for ($i = 0; $i < $cou; $i++) {
            $x = rand(0, $this->width);
            $y = rand(0, $this->height);
            $jiaodu = rand(0, 360); //设置角度
            $fonturl = $this->font_url; //使用的字体
            // 检测中文
            if (preg_match("/[\x7f-\xff]/", $this->random)) {
                $fontsize = $this->font_size / 4;
                $dscode = $this->getChar(1);
            } else {
                $size = $this->font_size / 3.5;
                $fontsize = rand($size, $size + 3);
                $originalcode = 'zxcvbnmasdfghjklqwertyuiop1234567890'; //随机字符串
                $countdistrub = mb_strlen($originalcode);
                $dscode = $originalcode[rand(0, $countdistrub - 1)];
            }
            $color = ImageColorAllocate($im, rand(40, 140), rand(40, 140), rand(40, 140));
            imagettftext($im, $fontsize, $jiaodu, $x, $y, $color, $fonturl, $dscode);
        }
    }

    /**
     * 随机中文字符串
     *
     * @param [type] $num 返回数量
     * @return string
     */
    private function getChar($num)
    {
        $b = '';
        for ($i = 0; $i < $num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0, 0xD0)) . chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }

    /**
     * 画布上生成验证码
     *
     * @param [type] $im
     * @param [type] $string
     * @return void
     */
    private function setCode($im, $string)
    {
        $width = $this->width;
        $height = $this->height;
        $len = 1;
        $start = 0;
        $strlen = $count = mb_strlen($string, 'utf-8');
        while ($strlen) {
            $array[] = mb_substr($string, $start, $len, "utf8");
            $string = mb_substr($string, $len, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
        $y = floor($height / 2) + floor($height / 4);
        $fontsize = rand($this->font_size - 2, $this->font_size);
        $fonturl = $this->font_url;
        $counts = $count;
        for ($i = 0; $i < $counts; $i++) {
            $char = $array[$i];
            $x = floor($width / $counts) * $i + ($width / 15);
            $jiaodu = rand(-30, 30);
            $color = ImageColorAllocate($im, rand(0, 50), rand(50, 100), rand(100, 140));
            imagettftext($im, $fontsize, $jiaodu, $x, $y, $color, $fonturl, $char);
        }
    }

    /**
     * 生成随机码,(支持中文)
     *
     * @return void
     */
    private function setVerNumber()
    {
        $len = 1;
        $start = 0;
        $string = $this->random;
        $strlen = $num = mb_strlen($string, 'utf-8');
        while ($strlen) {
            $array[] = mb_substr($string, $start, $len, "utf-8");
            $string = mb_substr($string, $len, $strlen, "utf-8");
            $strlen = mb_strlen($string, 'utf-8');
        }
        $originalcode = $array;
        $_dscode = "";
        $counts = $this->nums;
        for ($j = 0; $j < $counts; $j++) {
            $rand = rand(0, $num - 1);
            $dscode = $originalcode[$rand];
            $_dscode .= $dscode;
        }
        return $_dscode;
    }
}
