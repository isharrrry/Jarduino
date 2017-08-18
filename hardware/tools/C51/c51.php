<?php

if(!defined('BR'))
    define('BR', "\r\n");

error_reporting(E_ALL);

define('FILE_APPEND', 1);
if(!function_exists("file_put_contents"))
{
    function file_put_contents($n, $d, $flag = false)
    {
        $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
        $f = @fopen($n, $mode);

        if($f === false)
        {
            return 0;
        }

        else
        {
            if(is_array($d))
                $d = implode($d);

            $bytes_written = fwrite($f, $d);
            fclose($f);
            return $bytes_written;
        }
    }
}

$HEXC51 = '';

if((@$HEXC51 = trim(file_get_contents('hexc51.ini'))) == '' || !is_dir($HEXC51))
{
    if(!defined('UTF8'))
        echo '寻找HEXC51工具位置...'.BR;

    if(($HEXC51 = findDir(array('keil', 'hexc51'))) != '')
    {
        if(!defined('UTF8'))
            echo '找到 '.$HEXC51.BR;

        file_put_contents('hexc51.ini', $HEXC51);
    }
    else
    {
        echo '未找到编译工具，无法继续编译...'.BR;
        exit(0);
    }
}

if (@isset($argv[1]))
    $file=$argv[1];

$workDir='./';
if (@isset($argv[2]))
{
    $workDir=substr($argv[2],2);
    file_put_contents('jarduino.ini', $workDir.'/');
}

if(!defined('UTF8'))
    echo BR . '编译 ' . $file . BR;

system($HEXC51 . '/C51 ' . $file .' BROWSE INCDIR('.$workDir.')');

?>