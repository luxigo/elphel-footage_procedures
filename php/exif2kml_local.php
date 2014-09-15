<?php
/*!*******************************************************************************
*! FILE NAME   : exif2kml_local.php
*! DESCRIPTION : exracts location information into a KML file from images for Panorama Previewer
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

require "pano.inc";

if (isset($_GET['path'])) $path = $_GET['path'];
else if (isset($argv[1])) $path = $argv[1];
else                      $path = "test";

if (isset($_GET['ext']))  $ext = $_GET['ext'];
else if (isset($argv[2])) $ext = $argv[2];
else                      $ext = "jp4";

$filename="map_points.kml";

echo "<pre>";
echo "directory=$path\n";
echo "filename= $filename\n";

chdir("/data/footage/$path");

$list = scandir(".");
//ascending sort
asort($list);

$files = array();

foreach ($list as $value) {
    //echo $value."\n";
    if ($value!="trash") $files = array_merge($files,process_folder($value,"jp4"));
}
  
$numNodes=count($files);
echo "nNodes= $numNodes\n";

$world=array();
for ($i=0;$i<$numNodes;$i++) {
    $world[$i]['href']="http://127.0.0.1/footage/$path/".$files[$i];
    $exif_data = exif_read_data($files[$i]);
    $world[$i]['longitude']=(($exif_data['GPSLongitudeRef']=="W")?-1:1)*array2degrees($exif_data['GPSLongitude']);
    $world[$i]['latitude']= (($exif_data['GPSLatitudeRef'] =="S")?-1:1)*array2degrees($exif_data['GPSLatitude']);
    $world[$i]['altitude']= (($exif_data['GPSAltitudeRef'] =="1")?-1:1)*parseAlt($exif_data['GPSAltitude']);
    if (isset($exif_data['GPSImgDirection'])) {
	$world[$i]['heading']=  parseAlt($exif_data['GPSImgDirection']); // add correction  from magnetic?
	$world[$i]['tilt']= (($exif_data['GPSDestLatitudeRef'] =="S")?-1:1)*array2degrees($exif_data['GPSDestLatitude'])+90.0;
	if ($world[$i]['tilt']<0) $world[$i]['tilt']==0;
	else if ($world[$i]['tilt']>180) $world[$i]['tilt']=180;
	$world[$i]['roll']=(($exif_data['GPSDestLongitudeRef']=="W")?-1:1)*array2degrees($exif_data['GPSDestLongitude']);
    }else{
      $world[$i]['heading']=0;
      $world[$i]['tilt']=90;
      $world[$i]['roll']=0;
    }
}

// $file=fopen ($filename,'w');
// fwrite($file,generateKML());
// fclose($file);

// works by a couple microseconds slower
file_put_contents($filename,generateKML());

chmod($filename,0666);


function array2degrees($dms) {
    $round=1000000;
    $d=explode('/',$dms[0]);
    $m=explode('/',$dms[1]);
    $s=explode('/',$dms[2]);
    $rslt= $d[0]/$d[1]+($m[0]/$m[1])/60.0+($s[0]/$s[1])/3600.0;
    return round($round*$rslt)/$round;
} 

function parseAlt($alt) {
    $round=1000000;
    $a=explode('/',$alt);
    $rslt=  $a[0]/$a[1];
    return round($round*$rslt)/$round;
} 

function generateKML($index=-1) {
    global $world, $map, $numNodes;
    if ($index<0) $indices=range(0,$numNodes-1);
    else $indices=array_merge(array(0=>$index),$map[$index]);
    $kml=<<<HEADER
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
<Document>
HEADER;
    foreach ($indices as $i) {
      $kml.= "<PhotoOverlay>\n";
      $kml.= "  <Camera>\n";
      $kml.=sprintf("   <longitude>%s</longitude>\n",$world[$i]['longitude']);
      $kml.=sprintf("   <latitude>%s</latitude>\n",$world[$i]['latitude']);
      $kml.=sprintf("   <altitude>%s</altitude>\n",$world[$i]['altitude']);
      $kml.=sprintf("   <heading>%s</heading>\n",$world[$i]['heading']);
      $kml.=sprintf("   <tilt>%s</tilt>\n",$world[$i]['tilt']);
      $kml.=sprintf("   <roll>%s</roll>\n",$world[$i]['roll']);
      $kml.= "  </Camera>\n";
      $kml.= "  <Icon>\n";
      $kml.=sprintf("   <href>%s</href>\n",$world[$i]['href']);
      $kml.= "  </Icon>\n";
      $kml.= "</PhotoOverlay>\n";
    }
    $kml.=<<<TRAILER
</Document>
</kml>
TRAILER;
    return ($kml);   
}

function process_folder($file,$type) {

	$arr = array();
	$ext=get_file_extension($file);

	if (substr($file,0,1)!=".") {
		if ($ext=="") {
		    if (is_dir($file)) {
			$arr = glob("$file/*_1.jp4");
		    }
		}
	}

	return $arr;

}

function get_file_extension($filename) {
	//return substr(strrchr($filename, '.'), 1);
	return pathinfo($filename, PATHINFO_EXTENSION);
}

?>