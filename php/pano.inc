<?php
$world=array();
$currentNode=-1;
$currentValue="";
$imgOn=false;
$urlPrefix;
function startElement($parser, $name, $attrs) 
{
    global $world,$currentNode,$currentValue,$imgOn;
    switch ($name) {
      case 'PhotoOverlay':
        $currentNode=count($world);
        $world[$currentNode]=array();
        break;
      case 'Icon':
        $imgOn=true;;
        break;

    }
}

function endElement($parser, $name) 
{
    global $world,$currentNode,$currentValue,$imgOn,$urlPrefix;
    switch ($name) {
      case 'longitude':
        $world[$currentNode]['longitude']=$currentValue;
        break;
      case 'latitude':
        $world[$currentNode]['latitude']=$currentValue;
        break;
      case 'altitude':
        $world[$currentNode]['altitude']=$currentValue;
        break;
      case 'heading':
        $world[$currentNode]['heading']=$currentValue;
        break;
      case 'tilt':
        $world[$currentNode]['tilt']=$currentValue;
        break;
      case 'roll':
        $world[$currentNode]['roll']=$currentValue;
        break;
      case 'Icon':
        $imgOn=false;
        break;
      case 'href':
        if ($imgOn)  $world[$currentNode]['href']=$urlPrefix.$currentValue;
        break;
    }
}

function characterData($parser, $data) 
{
    global $currentValue;
    $currentValue=$data;
}


// $base - directory, where there is the  .kml file, root directory for image files
function parseKML($prefix,$name) {
  global $world, $urlPrefix;
  $urlPrefix=$prefix;
  $xml_parser = xml_parser_create();
// use case-folding so we are sure to find the tag in $map_array
  xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
  xml_set_element_handler($xml_parser, "startElement", "endElement");
  xml_set_character_data_handler($xml_parser, "characterData");
  if (!($fp = fopen($name, "r"))) {
    die("could not open XML input");
  }

  while ($data = fread($fp, 4096)) {
    if (!xml_parse($xml_parser, $data, feof($fp))) {
        die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
    }
  }
  xml_parser_free($xml_parser);
  return $world;
}

?>