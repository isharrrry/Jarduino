<?php
//自定义一个函数dirSize()，统计传入参数的目录大小
function dirSize($directory){
  $dir_size = 0; //用来累加各个文件大小
 
  if($dir_handle = @opendir($directory)){      //打开目录，并判断是否能成功打开
    while($filename = readdir($dir_handle)){     //循环遍历目录下的所有文件
        if($filename != "."&& $filename != ".."){     //一定要排除两个特殊的目录
            $subFile = $directory."/".$filename;     //将目录下的子文件和当前目录相连
            if(is_dir($subFile))     //如果为目录
            $dir_size += dirSize($subFile);     //递归地调用自身函数，求子目录的大小
            if(is_file($subFile))     //如果是文件
            $dir_size += filesize($subFile);     //求出文件的大小并累加
        }
    }
    closedir($dir_handle);      //关闭文件资源
    return $dir_size;     //返回计算后的目录大小
  }
}
 
$dir_size = dirSize($argv[1]);    //调用该函数计算目录大小
//echo round($dir_size/pow(1024,1),2)."KB";    //字节数转换为“KB”单位并输出
/*avr-size.exe -A C:\Users\Harry\AppData\Local\Temp\arduino_build_221354/Blink.ino.elf
C:\Users\Harry\AppData\Local\Temp\arduino_build_221354/Blink.ino.elf  :
section                    size      addr
.data                         0   8388864
.text                       928         0
.bss                          9   8388864
.comment                     17         0
.note.gnu.avr.deviceinfo     64         0
.debug_aranges               40         0
.debug_info                3214         0
.debug_abbrev              1977         0
.debug_line                 525         0
.debug_frame                180         0
.debug_str                 1157         0
.debug_loc                 1096         0
.debug_ranges                24         0
Total                      9231*/
echo 'section                    size      addr
.data                         0   8388864
.text                       '.$dir_size.'         0';

?>