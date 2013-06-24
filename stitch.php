<?php
/*!*******************************************************************************
*! FILE NAME   : stitch.php
*! DESCRIPTION : launches enblend for imagej processed images
*! REVISION    : 1.00
*! AUTHOR      : Oleg Dzhimiev <oleg@elphel.com>
*! Copyright (C) 2012 Elphel, Inc
*! -----------------------------------------------------------------------------**
*!  This program is free software: you can redistribute it and/or modify
*!  it under the terms of the GNU General Public License as published by
*!  the Free Software Foundation, either version 3 of the License, or
*!  (at your option) any later version.
*!
*!  This program is distributed in the hope that it will be useful,
*!  but WITHOUT ANY WARRANTY; without even the implied warranty of
*!  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*!  GNU General Public License for more details.
*!
*!  The four essential freedoms with GNU GPL software:
*!  * the freedom to run the program for any purpose
*!  * the freedom to study how the program works and change it to make it do what you wish
*!  * the freedom to redistribute copies so you can help your neighbor
*!  * the freedom to distribute copies of your modified versions to others
*!
*!  You should have received a copy of the GNU General Public License
*!  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*! -----------------------------------------------------------------------------**
*/

if      (isset($_GET['src'])) $src = $_GET['src'];
else if (isset($argv[1]))     $src = $argv[1];
else    {print_script_contents();}

if (isset($_GET['dest'])) $dest = $_GET['dest'];
else if (isset($argv[2])) $dest = $argv[2];
else {print_script_contents();}

if (isset($_GET['bp']))   $black_point = $_GET['bp'];
else if (isset($argv[3])) $black_point = $argv[3];
else                      $black_point = 0;

if (isset($_GET['wp']))   $white_point = $_GET['wp'];
else if (isset($argv[4])) $white_point = $argv[4];
else                      $white_point = 25;

if (isset($_GET['q']))    $quality = $_GET['q'];
else if (isset($argv[5])) $quality = $argv[5];
else                      $quality = 92;

if (!is_dir($dest)) {
  //creating a folder with access rights - 0777
  $old = umask(0);
  @mkdir($dest);
  umask($old);
}

$files = scandir($src);

$timestamps = Array();
$suffs = Array();

//print_r($files);

$f = Array();

foreach($files as $file){
      if (get_file_extension($file)=="tiff") update($file);
}

//print_r($timestamps);

foreach($timestamps as $index=>$tss) {
    $list1 = "";
    $list2 = "";
    $list3 = "";
    
    $f=update_fs($suffs[$index]);
    
    for ($i=0;$i<9;$i++)   if (is_file("$src/{$tss}-".$f[$i])) $list1 .= " $src/{$tss}-".$f[$i];
    for ($i=9;$i<18;$i++)  if (is_file("$src/{$tss}-".$f[$i])) $list2 .= " $src/{$tss}-".$f[$i];
    for ($i=18;$i<27;$i++) if (is_file("$src/{$tss}-".$f[$i])) $list3 .= " $src/{$tss}-".$f[$i];
    
    //echo "dam {$suffs[$index]}\n";
    
    //exec("enblend -l 10 --no-optimize --fine-mask -a -v -w -o $dest/result_{$tss}_1.tif $list");
    exec("enblend -w -o $dest/result_{$tss}_top.tif $list1");
    exec("enblend -w -o $dest/result_{$tss}_mid.tif $list2");
    exec("enblend -w -o $dest/result_{$tss}_bot.tif $list3");
    
    exec("enblend --wrap='vertical' -o $dest/result_{$tss}.tif $dest/result_{$tss}_top.tif $dest/result_{$tss}_mid.tif $dest/result_{$tss}_bot.tif");
    unlink("$dest/result_{$tss}_top.tif");
    unlink("$dest/result_{$tss}_mid.tif");
    unlink("$dest/result_{$tss}_bot.tif");
    //exec("convert $dest/result_{$tss}.tif -level 0%,10%,1 $dest/result_{$tss}-0-10-1.jpeg");
    //exec("convert $dest/result_{$tss}.tif -level 0%,14%,1 $dest/result_{$tss}-0-14-1.jpeg");
    //exec("convert $dest/result_{$tss}.tif -level 0%,18%,1 $dest/result_{$tss}-0-18-1.jpeg");
    exec("convert $dest/result_{$tss}.tif -level {$black_point}%,{$white_point}%,1 -quality $quality $dest/result_{$tss}-0-25-1.jpeg");
    //exec("convert $dest/result_{$tss}.tif -level 0%,50%,1 $dest/result_{$tss}-0-50-1.jpeg");
}



function update($file){
    global $timestamps;
    global $suffs;
    $found = false;
    $ts = substr($file,0,17);
    
    $mid = "RGB24";
    if (preg_match("/INT16/",$file)!=0) $mid = "INT16";

    $suf = "DECONV";//default
    if (preg_match("/DECONV/",$file)!=0)   $suf = "DECONV-{$mid}_EQR";
    if (preg_match("/DEMOSAIC/",$file)!=0) $suf = "DEMOSAIC-{$mid}_EQR";
    if (preg_match("/LOWRES/",$file)!=0)   $suf = "LOWRES-{$mid}_EQR";
    
    foreach($timestamps as $index=>$elem) {
	if ($ts==$elem) {
	    if ($suffs[$index]==$suf) {
	      $found = true;
	    }
	}
    }
    if (!$found) {
	$timestamps[count($timestamps)] = $ts;
	$suffs[count($suffs)] = $suf;
    }
}

function get_file_extension($filename) {
	return pathinfo($filename, PATHINFO_EXTENSION);
}

function update_fs($suff){
    $f = Array();
    
    $f[0] = "12-$suff-RIGHT.tiff";
    $f[1] = "13-$suff.tiff";
    $f[2] = "14-$suff.tiff";
    $f[3] = "15-$suff.tiff";
    $f[4] = "08-$suff.tiff";
    $f[5] = "09-$suff.tiff";
    $f[6] = "10-$suff.tiff";
    $f[7] = "11-$suff.tiff";
    $f[8] = "12-$suff-LEFT.tiff";

    $f[9] = "04-$suff-RIGHT.tiff";
    $f[10] = "05-$suff.tiff";
    $f[11] = "06-$suff.tiff";
    $f[12] = "07-$suff.tiff";
    $f[13] = "00-$suff.tiff";
    $f[14] = "01-$suff.tiff";
    $f[15] = "02-$suff.tiff";
    $f[16] = "03-$suff.tiff";
    $f[17] = "04-$suff-LEFT.tiff";

    $f[18] = "20-$suff-RIGHT.tiff";
    $f[19] = "21-$suff.tiff";
    $f[20] = "22-$suff.tiff";
    $f[21] = "23-$suff.tiff";
    $f[22] = "16-$suff.tiff";
    $f[23] = "17-$suff.tiff";
    $f[24] = "18-$suff.tiff";
    $f[25] = "19-$suff.tiff";
    $f[26] = "20-$suff-LEFT.tiff";
    
    return $f;
}

function print_script_contents(){
  $fp = fopen($_SERVER['SCRIPT_FILENAME'], 'rb');
  fseek($fp, 0, SEEK_END);  /// file pointer at the end of the file (to find the file size)
  $fsize = ftell($fp);      /// get file size
  fseek($fp, 0, SEEK_SET);  /// rewind to the start of the file
  /// send the headers
  header("Content-Type: application/x-php");
  header("Content-Length: ".$fsize."\n");
  fpassthru($fp);           /// send the script (this file) itself
  fclose($fp);
  die("0");
}
