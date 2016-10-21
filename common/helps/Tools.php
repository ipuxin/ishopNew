<?php
//把报错信息以json形式返回,服务器端判断登录时用户名为空时报的错误
namespace app\common\helps;
/**
 * 调试程序
 * @param $val  要显示的数据
 * @param string $name 数据名称
 * @param bool $exit 是否中断程序
 * @param bool $dump 是否选择var_dump打印
 */
class Tools
{

    static function debug($val, $name = '', $exit = false, $dump = false)
    {
        //自动获取调试函数名$func
        if ($dump) {
            $func = 'var_dump';
        } else {
            $func = (is_array($val) || is_object($val)) ? 'print_r' : 'printf';
        }
        //输出到html
        header('Content-type:text/html; charset=utf-8');
        echo '<pre>debug output: ' . $name . ' : <hr/>';
        $func($val);
        echo '</pre>';
        if ($exit) exit;
    }
}