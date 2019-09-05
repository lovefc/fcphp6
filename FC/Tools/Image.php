<?php

namespace fcphp\extend;

/**
 * 图片处理类
 */

class Image
{
    // 当前图片
    protected $img;

    public $width;//验证码宽度

    public $height;//验证码高度

    public $nums;//验证码个数

    public $code;//随机数

    public $fonturl;//字体路径

    public $session;//session名称

    protected $status;//检测图片的缩放地址是否已经存在

    // 常用图像types 对应表
    protected $types = array(1 => 'gif', 2 => 'jpg', 3 => 'png', 6 => 'bmp');

    // 是否设置gif
    public $_config = array('do_gif' => 1);

    //设置原图
    public function setimg($img)
    {
        $this->img = $img;
        return $this;
    }

    // 图片信息
    public function getImageInfo($img)
    {
        if (!is_file($img)) {
            $this->error('图片打开失败');
        }
        $info = getimagesize($img);
        if (empty($info)) {
            $this->error('图片打开失败');
        }
        if (isset($this->types[$info[2]])) {
            $info['ext'] = $info['type'] = $this->types[$info[2]];
        } else {
            $info['ext'] = $info['type'] = 'jpg';
        }
        $info['type'] == 'jpg' && $info['type'] = 'jpeg';
        $info['size'] = @filesize($img);
        return $info;
    }

    // thumb(新图地址, 宽, 高, 裁剪, 允许放大, 清淅度)
    public function thumb($filename, $new_w = 160, $new_h = 120, $cut = 0, $big = 0, $pct = 100)
    {
        if ($this->status == 1) {
            if (file_exists($filename)) {
                return true;
            }
        }
        // 获取原图信息
        $info = $this->getImageInfo($this->img);
        if (!empty($info[0])) {
            $old_w = $info[0];
            $old_h = $info[1];
            $type = $info['type'];
            $ext = $info['ext'];
            unset($info);
            $result['type'] = $type;
            $result['width'] = $old_w;
            $result['height'] = $old_h;
            $just_copy = false;
            // 是否处理GIF
            if ($ext == 'gif' && !$this->_config['do_gif']) {
                $just_copy = true;
            }
            // 如果原图比缩略图小 并且不允许放大
            if ($old_w < $new_h && $old_h < $new_w && !$big) {
                $just_copy = true;
            }
            if ($just_copy) {
                // 检查目录
                if (!is_dir(dirname($filename))) {
                    self::create(dirname($filename));
                }
                @copy($this->img, $filename);
                return $result;
            }
            // 裁剪图片
            if ($cut == 0) { // 等比列
                $scale = min($new_w / $old_w, $new_h / $old_h); // 计算缩放比例
                $width = (int)($old_w * $scale); // 缩略图尺寸
                $height = (int)($old_h * $scale);
                $start_w = $start_h = 0;
                $end_w = $old_w;
                $end_h = $old_h;
            } elseif ($cut == 1) { // center center 裁剪
                $scale1 = round($new_w / $new_h, 2);
                $scale2 = round($old_w / $old_h, 2);
                if ($scale1 > $scale2) {
                    $end_h = round($old_w / $scale1, 2);
                    $start_h = ($old_h - $end_h) / 2;
                    $start_w = 0;
                    $end_w = $old_w;
                } else {
                    $end_w = round($old_h * $scale1, 2);
                    $start_w = ($old_w - $end_w) / 2;
                    $start_h = 0;
                    $end_h = $old_h;
                }
                $width = $new_w;
                $height = $new_h;
            } elseif ($cut == 2) { // left top 裁剪
                $scale1 = round($new_w / $new_h, 2);
                $scale2 = round($old_w / $old_h, 2);
                if ($scale1 > $scale2) {
                    $end_h = round($old_w / $scale1, 2);
                    $end_w = $old_w;
                } else {
                    $end_w = round($old_h * $scale1, 2);
                    $end_h = $old_h;
                }
                $start_w = 0;
                $start_h = 0;
                $width = $new_w;
                $height = $new_h;
            }
            // 载入原图
            $createFun = 'ImageCreateFrom' . $type;
            $oldimg = $createFun($this->img);
            // 创建缩略图
            if ($type !== 'gif' && function_exists('imagecreatetruecolor')) {
                $newimg = @imagecreatetruecolor($width, $height);
            } else {
                $newimg = @imagecreate($width, $height);
            }
            // 复制图片
            if (function_exists("ImageCopyResampled")) {
                @ImageCopyResampled($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w, $end_h);
            } else {
                @ImageCopyResized($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w, $end_h);
            }
            // 检查目录
            if (!is_dir(dirname($filename))) {
                self::create(dirname($filename));
            }

            // 对jpeg图形设置隔行扫描
            $type == 'jpeg' && imageinterlace($newimg, 1);
            // 生成图片
            $imageFun = 'image' . $type;
            if ($type == 'jpeg') {
                $did = @$imageFun($newimg, $filename, $pct);
            } else {
                $did = @$imageFun($newimg, $filename);
            }
            if (!$did) {
                return false;
            }
            ImageDestroy($newimg);
            ImageDestroy($oldimg);
            $result['width'] = $width;
            $result['height'] = $height;
            return $result;
        }
        return false;
    }

    // water(保存地址,水印图片,水印位置,透明度)
    public function water($filename, $water, $pos = 0, $pct = 80)
    {
        // 加载水印图片
        $info = $this->getImageInfo($water);
        if (!empty($info[0])) {
            $water_w = $info[0];
            $water_h = $info[1];
            $type = $info['type'];
            $fun = 'imagecreatefrom' . $type;
            $waterimg = $fun($water);
        } else {
            return false;
        }
        // 加载背景图片
        $info = $this->getImageInfo($this->img);
        if (!empty($info[0])) {
            $old_w = $info[0];
            $old_h = $info[1];
            $type = $info['type'];
            $ext = $info['ext'];
            $fun = 'imagecreatefrom' . $type;
            $oldimg = $fun($this->img);
        } else {
            return false;
        }
        // 是否处理GIF
        if ($ext == 'gif' && !$this->_config['do_gif']) {
            return false;
        }

        // 剪切水印
        $water_w > $old_w && $water_w = $old_w;
        $water_h > $old_h && $water_h = $old_h;

        // 水印位置
        switch ($pos) {
            case 0: //随机
                $posX = rand(0, ($old_w - $water_w));
                $posY = rand(0, ($old_h - $water_h));
                break;
            case 1: //1为顶端居左
                $posX = 0;
                $posY = 0;
                break;
            case 2: //2为顶端居中
                $posX = ($old_w - $water_w) / 2;
                $posY = 0;
                break;
            case 3: //3为顶端居右
                $posX = $old_w - $water_w;
                $posY = 0;
                break;
            case 4: //4为中部居左
                $posX = 0;
                $posY = ($old_h - $water_h) / 2;
                break;
            case 5: //5为中部居中
                $posX = ($old_w - $water_w) / 2;
                $posY = ($old_h - $water_h) / 2;
                break;
            case 6: //6为中部居右
                $posX = $old_w - $water_w;
                $posY = ($old_h - $water_h) / 2;
                break;
            case 7: //7为底端居左
                $posX = 0;
                $posY = $old_h - $water_h;
                break;
            case 8: //8为底端居中
                $posX = ($old_w - $water_w) / 2;
                $posY = $old_h - $water_h;
                break;
            case 9: //9为底端居右
                $posX = $old_w - $water_w;
                $posY = $old_h - $water_h;
                break;
            default: //随机
                $posX = rand(0, ($old_w - $water_w));
                $posY = rand(0, ($old_h - $water_h));
                break;
        }
        // 设定图像的混色模式
        imagealphablending($oldimg, true);
        // 添加水印
        imagecopymerge($oldimg, $waterimg, $posX, $posY, 0, 0, $water_w, $water_h, $pct);

        // 检查目录
        if (!is_dir(dirname($filename))) {
            self::create(dirname($filename));
        }
        $fun = 'image' . $type;
        if ($type == 'jpeg') {
            $did = @$fun($oldimg, $filename, $pct);
        } else {
            $did = @$fun($oldimg, $filename);
        }
        !$did && $this->error('保存失败!检查目录是否存在并且可写?');
        imagedestroy($oldimg);
        imagedestroy($waterimg);
        return $filename;
    }


    /**
     * 创建一个文件或者目录
     * @param $dir 目录名或者文件名
     * @param $file 如果是文件，则设为true
     * @param $mode 文件的权限
     * @return false|true
     */
    public static function create($dir, $file = false, $mode = 0777)
    {
        $path = str_replace("\\", "/", $dir);
        if ($file) {
            if (is_file($path)) {
                return true;
            }
            $temp_arr = explode('/', $path);
            array_pop($temp_arr);
            $file = $path;
            $path = implode('/', $temp_arr);
        }
        if (!is_dir($path)) {
            @mkdir($path, $mode, true);
        } else {
            @chmod($path, $mode);
        }
        if ($file) {
            $fh = @fopen($file, 'a');
            if ($fh) {
                fclose($fh);
                return true;
            }
        }
        if (is_dir($path)) {
            return true;
        }
        return false;
    }


    /**
     * 新创建一个图片
     * $string 文字，支持中文
     * $w  宽度
     * $h  高度
     * $size
     */

    public function creimg($string, $w = 500, $h = 500, $size = '50', $font = '')
    {
        $len = 1;
        $start = 0;
        $strlen = $count = mb_strlen($string, 'utf-8');
        while ($strlen) {
            $array[] = mb_substr($string, $start, $len, "utf8");
            $string = mb_substr($string, $len, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
        $im = imagecreatetruecolor($w, $h); //创建画布

        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, $w, $h, $white); //输出一个使用白色填充的矩形作为背景

        $y = floor($h / 2) + floor($h / $size);
        $fontsize = rand(25, 30);
        $counts = $count;
        for ($i = 0; $i < $counts; $i++) {
            $char = $array[$i];
            $x = floor($w / $counts) * $i + 2;
            $jiaodu = rand(-30, 30);
            $color = ImageColorAllocate($im, rand(0, 50), rand(50, 100), rand(100, 140));
            imagettftext($im, $size, $jiaodu, $x, $y, $color, $font, $char);
        }

        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
        exit;
    }


    //输出图片
    public function Imageout($sessionname = 'YZM')
    {
        $this->session = $this->sessionCode();
        $_SESSION[$sessionname] = strtolower($this->session);
        $im = $this->createimagesource();
        $this->setbackgroundcolor($im);
        $this->setCode($im);
        $this->setdistrubecode($im);
        Header("Content-type: image/png");
        Imagepng($im);
        ImageDestroy($im);
        exit;
    }

    //创建个画布
    public function createimagesource()
    {
        return imagecreate($this->width, $this->height);
    }

    //设置背景颜色
    public function setbackgroundcolor($im)
    {
        $bgcolor = ImageColorAllocate($im, rand(200, 255), rand(200, 255), rand(200, 255));
        imagefill($im, 0, 0, $bgcolor);
    }

    //加入随机数
    public function setdistrubecode($im)
    {
        $count_h = $this->height;
        $cou = floor($count_h * 1);
        for ($i = 0; $i < $cou; $i++) {
            $x = rand(0, $this->width);
            $y = rand(0, $this->height);
            $jiaodu = rand(0, 360);//设置角度
            $fontsize = rand(8, 12);//设置字体大小
            $fonturl = $this->fonturl;//使用的字体
            $originalcode = 'zxcvbnmasdfghjklqwertyuiop1234567890';//随机字符串
            $countdistrub = strlen($originalcode);
            $dscode = $originalcode[rand(0, $countdistrub - 1)];
            $color = ImageColorAllocate($im, rand(40, 140), rand(40, 140), rand(40, 140));
            imagettftext($im, $fontsize, $jiaodu, $x, $y, $color, $fonturl, $dscode);
        }
    }

    //生成图片
    public function setCode($im)
    {
        $width = $this->width;
        $height = $this->height;
        $string = $this->session;
        $len = 1;
        $start = 0;
        $strlen = $count = mb_strlen($string, 'utf-8');
        while ($strlen) {
            $array[] = mb_substr($string, $start, $len, "utf8");
            $string = mb_substr($string, $len, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
        $y = floor($height / 2) + floor($height / 4);
        $fontsize = rand(20, 30);
        $fonturl = $this->fonturl;
        $counts = $count;
        for ($i = 0; $i < $counts; $i++) {
            $char = $array[$i];
            $x = floor($width / $counts) * $i + 2;
            $jiaodu = rand(-30, 30);
            $color = ImageColorAllocate($im, rand(0, 50), rand(50, 100), rand(100, 140));
            imagettftext($im, $fontsize, $jiaodu, $x, $y, $color, $fonturl, $char);
        }
    }

    //生成session
    public function sessionCode()
    {
        $len = 1;
        $start = 0;
        $string = $this->code;
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

    //获取验证码
    public function getVerCode($name = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : '';
    }

    //打印错误
    public function error($msg)
    {
        die($msg);
    }
}
