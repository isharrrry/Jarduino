<?php
$dir1=substr($argv[1],2);
$dir2=substr($argv[2],2);
//echo $dir1.$dir2;

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

foreach(get_files_by_ext($dir1,'c') as $file)
{
		echo   'Copy:' .$dir1.'/'. $file." to $argv[3]\r\n" ;
		copy($dir1.'/'.$file,$argv[3].$file);
}
foreach(get_files_by_ext($dir2,'c') as $file)
{
		echo   'Copy:' .$dir2.'/'. $file." to $argv[3]\r\n" ;
		copy($dir2.'/'.$file,$argv[3].$file);
}
?>