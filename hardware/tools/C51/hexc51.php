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

    * �ֽڸ�ʽ�� ���ֽ�����ʽΪB K M G T P E Z Y �����Ĵ�С
    * @param int $size ��С
    * @param int $dec ��ʾ����
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
    * ȡ�õ���������Ϣ
    * @param $letter
    * @return array
    */

function get_disk_space($letter)

{
    //��ȡ������Ϣ
    $diskct = 0;
    $disk = array();

    if(@disk_total_space($key) != NULL) //Ϊ��ֹӰ������������������
    {
        $diskct = 1;
        $disk["A"] = round((@disk_free_space($key) / (1024 * 1024 * 1024)), 2)."G / ".round((@disk_total_space($key) / (1024 * 1024 * 1024)), 2).'G';
    }

    $diskz = 0; //����������
    $diskk = 0; //����ʣ������
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
    * ȡ�ô���ʹ�����
    * @return var
    */

function get_spec_disk($type = 'system')

{
    $disk = array();

    switch($type)
    {
        case 'system':
            //strrev(array_pop(explode(':',strrev(getenv_info('SystemRoot')))));//ȡ��ϵͳ�̷�
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
            echo '�ж� '.$dir.BR;

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
echo BR.'HEXC51 v1.0 [16/05/08] By Cosine (Changzhou China).'.BR.'with KEIL C51 Tool'.BR.BR.'����ǰ��Ҫ�Ȱ�װC51���빤�ߣ�HEXC51���Զ���������λ�ò������Ŀ¼������.c��.h�ļ�����Intel HEX�ļ�'.BR.BR;
if((@$HEXC51 = trim(file_get_contents('hexc51.ini'))) == '' || !is_dir($HEXC51))
{
    if(!defined('UTF8'))
        echo 'Ѱ��HEXC51����λ��...'.BR;

    if(($HEXC51 = findDir(array('keil', 'hexc51'))) != '')
    {
        if(!defined('UTF8'))
            echo '�ҵ� '.$HEXC51.BR;

        file_put_contents('hexc51.ini', $HEXC51);
    }
    else
    {
        echo 'δ�ҵ����빤�ߣ��޷���������...'.BR;
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
            echo BR . '���� ' . $file . BR;

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
            echo BR . '���� ' . $workDir2.$file . BR;
    }

    foreach(get_files_by_ext($workDir, 'obj') as $file)
    {
        if($flag)
            $str .= ',';

        else
            $flag = 1;

        $str .= '"' . $workDir.$file . '"';

        if(!defined('UTF8'))
            echo BR . '���� ' . $workDir.$file . BR;
    }
    $str .= ' TO '.$workDir.'jarduino.jd';
//echo $str;
    system($HEXC51 . '/BL51 ' . $str);

//file_put_contents('jarduino.bl',$str);
    if(!defined('UTF8'))
        echo BR . '����HEX�ļ�' . BR;

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