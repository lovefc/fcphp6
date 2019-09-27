<?php

/*
 * 验证码 配置
 * @Author: lovefc 
 * @Date: 2019-09-27 15:19:42
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-27 17:45:34
 */

return [

    'default' => [
        // 验证码宽度
        'width' => 200,
        // 验证码高度
        'height' => 60,
        // 验证码个数
        'nums' => 4,
        // 随机数
        'random' => '1234567890zxcvbnmasdfghjklqwertyuiop',
        // 随机数大小
        'font_size' => 40,
        // 字体路径
        'font_url' => PATH['FC'].'/Http/Font/valicode.otf'
    ],
    'default2' => [
        // 验证码宽度
        'width' => 300,
        // 验证码高度
        'height' => 100,
        // 验证码个数
        'nums' => 4,
        // 随机数
        'random' => '1234567890zxcvbnmasdfghjklqwertyuiop',
        // 随机数大小
        'font_size' => 50,
        // 字体路径
        'font_url' => PATH['FC'].'/Http/Font/valicode.otf'
    ],   
];
