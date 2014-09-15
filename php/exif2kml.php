<?php
/*!*******************************************************************************
*! FILE NAME   : exif2kml.php
*! DESCRIPTION : exracts location information into a KML file from images
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

$LEFTFOV   = -180;
$RIGHTFOV  = +180;
$BOTTOMFOV = -90;
$TOPFOV    = +90;
$NEAR      = 10000; //meters?


if (isset($_GET['path'])) $path = $_GET['path'];
else if (isset($argv[1])) $path = $argv[1];

if (isset($_GET['ext']))  $ext = $_GET['ext'];
else if (isset($argv[2])) $ext = $argv[2];

if (isset($_GET['dest'])) $some_dir = $_GET['dest'];
else if (isset($argv[3])) $some_dir = $argv[3];

if (isset($_GET['index'])) $some_index =  $_GET['index'];
else if (isset($argv[4])) $some_index = $argv[4];

if (isset($_GET['visibility'])) $visibility = $_GET['visibility'];
else if (isset($argv[5])) $visibility = $argv[5];

$directory = $path;
$filename ="../results/for_wpe/map.kml";

echo "directory=$directory\n";
echo "filename= $filename\n";

chdir($directory);
$files=glob("*_1.$ext");
  
$numNodes=count($files);
echo "nNodes= $numNodes\n";

$world=array();
$i=0;
for ($j=0;$j<$numNodes;$j++) {
    if (strpos($files[$j],"_1")) {
	//echo strpos($files[$j],"_1")." $i\n";
	$world[$i]['href']=$files[$j];
	$exif_data = exif_read_data($files[$j]);
	$world[$i]['longitude']=(($exif_data['GPSLongitudeRef']=="W")?-1:1)*array2degrees($exif_data['GPSLongitude']);
	$world[$i]['latitude']= (($exif_data['GPSLatitudeRef'] =="S")?-1:1)*array2degrees($exif_data['GPSLatitude']);
	$world[$i]['altitude']= (($exif_data['GPSAltitudeRef'] =="1")?-1:1)*parseAlt($exif_data['GPSAltitude']);

	$world[$i]['DateTimeOriginal']= $exif_data['DateTimeOriginal'];
	$world[$i]['SubSecTimeOriginal']= $exif_data['SubSecTimeOriginal'];

	if (isset($exif_data['GPSImgDirection'])) {
	  $world[$i]['heading']=  parseAlt($exif_data['GPSImgDirection']); // add correction  from magnetic?
	  $world[$i]['tilt']= (($exif_data['GPSDestLatitudeRef'] =="S")?-1:1)*array2degrees($exif_data['GPSDestLatitude'])+90.0;
	  if ($world[$i]['tilt']<0) $world[$i]['tilt']==0;
	  else if ($world[$i]['tilt']>180) $world[$i]['tilt']=180;
	  $world[$i]['roll']=(($exif_data['GPSDestLongitudeRef']=="W")?-1:1)*array2degrees($exif_data['GPSDestLongitude']);
	}
	else{
	  $world[$i]['heading']=0;
	  $world[$i]['tilt']=90;
	  $world[$i]['roll']=0;
	}
	$i++;
    }
}

$numNodes = count($world);

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

    global $LEFTFOV;
    global $RIGHTFOV;
    global $BOTTOMFOV;
    global $TOPFOV;
    global $NEAR;

    global $world, $map, $numNodes;

    global $some_dir;
    global $some_index;
    global $visibility;

    if ($index<0) $indices=range(0,$numNodes-1);
    else $indices=array_merge(array(0=>$index),$map[$index]);

    $kml=<<<HEADER
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
<Document>
HEADER;
    foreach ($indices as $i) {

      $dataTime = convert_to_some_time_format($world[$i]['DateTimeOriginal'],$world[$i]['SubSecTimeOriginal']);
      $href = modify_href($world[$i]['href']);

      $kml.= "<PhotoOverlay>\n";
      $kml.= "\t<name>".($i+$some_index)."</name>\n";
      $kml.= "\t<visibility>$visibility</visibility>\n";
      $kml.= "\t<shape>rectangle</shape>\n";
      $kml.= "\t<TimeStamp>\n";
      $kml.= "\t\t<when>".$dataTime."</when>\n";
      $kml.= "\t</TimeStamp>\n";
      $kml.= "\t<Camera>\n";
      $kml.= "\t\t<longitude>".$world[$i]['longitude']."</longitude>\n";
      $kml.= "\t\t<latitude>".$world[$i]['latitude']."</latitude>\n";
      $kml.= "\t\t<altitude>".$world[$i]['altitude']."</altitude>\n";
      $kml.= "\t\t<heading>".$world[$i]['heading']."</heading>\n";
      $kml.= "\t\t<tilt>".$world[$i]['tilt']."</tilt>\n";
      $kml.= "\t\t<roll>".$world[$i]['roll']."</roll>\n";
      $kml.= "\t</Camera>\n";
      $kml.= "\t<Icon>\n";
      $kml.= "\t\t<href>$some_dir/".$href."</href>\n";
      $kml.= "\t</Icon>\n";
      $kml.= "\t<ExtendedData>\n";
      $kml.= "\t\t<OriginalData>\n";
      $kml.= "\t\t\t<longitude>".$world[$i]['longitude']."</longitude>\n";
      $kml.= "\t\t\t<latitude>".$world[$i]['latitude']."</latitude>\n";
      $kml.= "\t\t\t<altitude>".$world[$i]['altitude']."</altitude>\n";
      $kml.= "\t\t\t<heading>".$world[$i]['heading']."</heading>\n";
      $kml.= "\t\t\t<tilt>".$world[$i]['tilt']."</tilt>\n";
      $kml.= "\t\t\t<roll>".$world[$i]['roll']."</roll>\n";
      $kml.= "\t\t</OriginalData>\n";
      $kml.= "\t</ExtendedData>\n";
      $kml.= "</PhotoOverlay>\n";
    }
    $kml.=<<<TRAILER
</Document>
</kml>
TRAILER;
    return ($kml);   

  }

function convert_to_some_time_format($date,$usec){
    $date[4] = "-";
    $date[7] = "-";
    $date[10] = "T";
    $date .= ".{$usec}Z";
    return $date;
}

function modify_href($name) {
    //$name = substr_replace($name,"",strpos($name,"_1-"),2);
    $name = "result_".substr($name,0,-6).".jpeg";
    return $name;
}


?>