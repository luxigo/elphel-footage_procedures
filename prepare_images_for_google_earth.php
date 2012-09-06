<?php
/*!*******************************************************************************
*! FILE NAME   : prepare_images_for_google_earth.php
*! DESCRIPTION : takes tiff and converts it to a scaled jpeg
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

if (!isset($_GET['path'])) {
    printf("No such folder");
    exit(-1);
}

// manage a slash in the path string
$path=cut_path_ending_slash($_GET['path']);

if (!is_dir("$path/for_wpe")) {
    $old = umask(0);
    @mkdir("$path/for_wpe");
    umask($old);
}

foreach (scandir($path) as $value) {
	process_images($path,$value);
}

function process_images($path,$file) {
    global $w,$h;
    //resize 
    $ext=get_file_extension($file);

    if ($ext=="tif") {
	$basename = basename($file,".tif");
	$file = $basename."-0-25-1.jpeg";
	exec("convert $path/$file -resize 8192x4096 $path/for_wpe/$basename.jpeg");
	//exec("convert $path/$file -resize 2000x1000 -background Black -extent 2000x1000 $path/$basename.jpeg");
    }
}


function cut_path_ending_slash($path) {
  if (substr($path,-1,1)=="/") $path=substr($path,0,-1);
  return $path;
}

function get_file_extension($filename) {
	return pathinfo($filename, PATHINFO_EXTENSION);
}

function get_file_basename($filename) {
	return substr($filename,0,strpos($filename,"."));
}

?>