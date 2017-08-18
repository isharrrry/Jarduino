<?php

if(!defined('BR'))
    define('BR', '');
    //define('BR', "\r\n");

error_reporting(E_ALL);
function get_files_by_ext($path, $ext)
{
    $files = array();

    if(is_dir($path))
    {
        $handle = opendir($path);

        while($file = readdir($handle))
        {
            if($file[0] == '.')
            {
                continue;
            }

            if(is_file($path . $file) && preg_match('/\.' . $ext . '$/i', $file))
            {
                $files[] = $file;
            }
        }

        closedir($handle);
        sort($files);
    }

    return $files;
}

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

/**

    * 字节格式化 把字节数格式为B K M G T P E Z Y 描述的大小
    * @param int $size 大小
    * @param int $dec 显示类型
    * @return int
    */

function byte_format($size, $dec = 2)

{
    $a = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
    $pos = 0;

    while($size >= 1024)
    {
        $size /= 1024;
        $pos++;
    }

    return round($size, $dec)." ".$a[$pos];
}



/**
    * 取得单个磁盘信息
    * @param $letter
    * @return array
    */

function get_disk_space($letter)

{
    //获取磁盘信息
    $diskct = 0;
    $disk = array();

    if(@disk_total_space($key) != NULL) //为防止影响服务器，不检查软驱
    {
        $diskct = 1;
        $disk["A"] = round((@disk_free_space($key) / (1024 * 1024 * 1024)), 2)."G / ".round((@disk_total_space($key) / (1024 * 1024 * 1024)), 2).'G';
    }

    $diskz = 0; //磁盘总容量
    $diskk = 0; //磁盘剩余容量
    $is_disk = $letter.':';

    if(@disk_total_space($is_disk) != NULL)
    {
        $diskct++;
        $disk[$letter][0] = byte_format(@disk_free_space($is_disk));
        $disk[$letter][1] = byte_format(@disk_total_space($is_disk));
        $disk[$letter][2] = round(((@disk_free_space($is_disk) / (1024 * 1024 * 1024)) / (@disk_total_space($is_disk) / (1024 * 1024 * 1024))) * 100, 2).'%';
        $diskk += byte_format(@disk_free_space($is_disk));
        $diskz += byte_format(@disk_total_space($is_disk));
    }

    return $disk;
}



/**
    * 取得磁盘使用情况
    * @return var
    */

function get_spec_disk($type = 'system')

{
    $disk = array();

    switch($type)
    {
        case 'system':
            //strrev(array_pop(explode(':',strrev(getenv_info('SystemRoot')))));//取得系统盘符
        $disk = get_disk_space(strrev(array_pop(explode(':', strrev(getenv('SystemRoot'))))));
        break;

        case 'all':
        foreach(range('b', 'z') as $letter)
        {
            $disk = array_merge($disk, get_disk_space($letter));
        }
        break;

        default:
        $disk = get_disk_space($type);
        break;
    }

    return $disk;
}

function findDir($firstDir, $file = '/C51/BIN/C51.exe')
{
    $dir = '';
    //print_r(get_spec_disk('all'));
    foreach(get_spec_disk('all') as $key => $val)
    {
        foreach($firstDir as $dirVal)
        {
            $dir = dirname($key.':/'.$dirVal.$file);
            echo '判断 '.$dir.BR;

            if(file_exists($dir))
            {
                return $dir;
            }
        }
    }
    return '';
}


// if(@isset($argv[2]))
//     define('UTF8', 1);

$HEXC51 = '';
echo BR.'HEXC51 v1.0 [16/05/08] By Cosine (Changzhou China).'.BR.'with KEIL C51 Tool'.BR.BR.'运行前需要先安装C51编译工具，HEXC51将自动搜索工具位置并编译该目录下所有.c、.h文件生成Intel HEX文件'.BR.BR;
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

/*
if (@isset($argv[1]))
    define('HEXC51', $argv[1]);
else
    define('HEXC51', 'C:\\KEIL\\C51\\BIN\\');
*/

    $workDir='./';
    if (@isset($argv[1]))
        $workDir=$argv[1];

/*    foreach(get_files_by_ext($workDir, 'c') as $file)
    {
        if(!defined('UTF8'))
            echo BR . '编译 ' . $file . BR;

        system($HEXC51 . '/C51 ' . $workDir.$file .' BROWSE INCDIR(D:\Arduino\hardware\C51\C51\cores\jarduino)');
    }*/
    $str = '';
    $flag = 0;
    $workDir2=trim(file_get_contents('jarduino.ini'));
    //echo $workDir2;
    foreach(get_files_by_ext($workDir2, 'obj') as $file)
    {
        if($flag)
            $str .= ',';

        else
            $flag = 1;

        $str .= '"' . $workDir2.$file . '"';

        if(!defined('UTF8'))
            echo BR . '链接 ' . $workDir2.$file . BR;
    }

    foreach(get_files_by_ext($workDir, 'obj') as $file)
    {
        if($flag)
            $str .= ',';

        else
            $flag = 1;

        $str .= '"' . $workDir.$file . '"';

        if(!defined('UTF8'))
            echo BR . '链接 ' . $workDir.$file . BR;
    }
    $str .= ' TO '.$workDir.'jarduino.jd';
//echo $str;
    system($HEXC51 . '/BL51 ' . $str);

//file_put_contents('jarduino.bl',$str);
    if(!defined('UTF8'))
        echo BR . '生成HEX文件' . BR;

    system($HEXC51 . '/OH51 '.$workDir.'jarduino.jd');
    foreach(get_files_by_ext($workDir, 'lst') as $file)
    {
        unlink($workDir.$file);
    }
    foreach(get_files_by_ext($workDir, 'obj') as $file)
    {
        unlink($workDir.$file);
    }
    @unlink($workDir.'jarduino.jd');
    @unlink($workDir.'jarduino.m51');
    exit(0);
    ?>