<?php
/*!*******************************************************************************
*! FILE NAME   : copy_all.php
*! DESCRIPTION : copies all the files to a specified destination
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

$folder = $_GET["src"];
$dest_folder = $_GET["dest"];
$imagej = $_GET["imagej"];
$ext = "jp4";

if (substr($dest_folder,-1,1)!="/") $dest_folder = $dest_folder."/";
if (substr($folder,-1,1)!="/") $folder = $folder."/";
if (substr($imagej,-1,1)!="/") $imagej = $imagej."/";

if (!is_dir($dest_folder)){
  $old = umask(0);
  @mkdir($dest_folder);
  umask($old);
}

if (!is_dir($imagej)){
  $old = umask(0);
  @mkdir($imagej);
  umask($old);
}

$subs = scandir($folder);

foreach($subs as $sub){
    if (is_dir($folder.$sub)&&($sub!="trash")) {
	exec("cp {$folder}{$sub}/*.$ext {$dest_folder}");
    }
}

?>
