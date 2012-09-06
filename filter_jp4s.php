<?php
/*!*******************************************************************************
*! FILE NAME   : filter_jp4s.php
*! DESCRIPTION : filters out incomplete panorama sets from the footage directory
*! REVISION    : 1.02
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

//disable the default time limit for php scripts.
set_time_limit(0);

//CONSTANTS

// footage root
$pre_path="/data/footage";

// folder for collecting non-matching results
$dest_path="trash";

//folder
if      (isset($_GET['path'])) $processing_folder = $_GET['path'];
else if (isset($argv[1]))      $processing_folder = $argv[1];
else                           $processing_folder = "test";

if (!is_dir("$pre_path/$processing_folder/$dest_path")) {
  //creating a folder with access rights - 0777
  $old = umask(0);
  @mkdir("$pre_path/$processing_folder/$dest_path");
  umask($old);
}

$filelist = scandir("$pre_path/$processing_folder");

echo "<pre>\n";

foreach ($filelist as $value) {
	//echo $value."\n";
	if ($value!=$dest_path) process_folder($value,"jp4");
}

function process_folder($file,$type) {

	global $pre_path;
	global $processing_folder;
	global $dest_path;

	$tmp_arr = Array();

	$url = "$pre_path/$processing_folder";

	$ext=get_file_extension($file);

	// exclude "." & ".."
	if (substr($file,0,1)!=".") {
		if ($ext=="") {
		    if (is_dir($url."/".$file)) {
			//echo $url."  ".$file."\n";
			if ($type=="") {
			    // do nothing
			}
			else {
			    $list = scandir($url."/".$file);
			    // getting deeper into indexed subfodlers
			    foreach($list as $elem){
				if (get_file_extension($url."/".$file."/".$elem)==$type) {
				      //echo $url."/".$file."/".$elem."\n";
				      // initialize array
				      if (!isset($tmp_arr[substr($elem,0,17)])) $tmp_arr[substr($elem,0,17)] = 0;
				      // 9th image is not part of the panorama
				      if (!strstr($elem,"_9.")) $tmp_arr[substr($elem,0,17)]++;
				}
			    }
			    //do actual copying
			    print_r($tmp_arr);
			    foreach($tmp_arr as $key=>$val){
				if ($val!=8) {
				    for ($i=1;$i<10;$i++){
					if (is_file("$url/$file/{$key}_$i.$type")) rename("$url/$file/{$key}_$i.$type","$url/$dest_path/{$key}_$i.$type");
				    }
				}
			    }
			}
		    }
		}else{
		    //do nothing
		}
	}
}

function get_file_extension($filename) {
	//return substr(strrchr($filename, '.'), 1);
	return pathinfo($filename, PATHINFO_EXTENSION);
}
  
?>