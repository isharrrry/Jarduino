<?php
//echo "#".$argv[1];
/*
$dir=dirname($argv[1]);
$obj=$dir.'/index.php';
@unlink($obj); //删除旧目录下的文件
rename($argv[1],$obj);
echo "#Move ".$obj;
*/

$dir=dirname($argv[1]);
$filename = explode(".",basename($argv[1]));
//print_r($filename);
//$houzhui = substr(strrchr($filename, '.'), 1);
//$obj = $dir.'/'.basename($filename,".".$houzhui).'.c';
$obj = $dir.'/'.$filename[0].'.c';
//print_r($obj);
@unlink($obj); //删除旧目录下的文件
if (@file_exists($argv[1])) {
	rename($argv[1],$obj);
}
echo "#Move ".$obj;

?>