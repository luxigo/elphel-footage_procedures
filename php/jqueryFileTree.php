<?php
/**
 * jQuery File Tree PHP Connector
 *
 * Version 1.1.0
 *
 * @author - Cory S.N. LaViska A Beautiful Site (http://abeautifulsite.net/)
 * @author - Dave Rogers - https://github.com/daverogers/jQueryFileTree
 *
 * History:
 *
 * 1.1.0 - adding multiSelect (checkbox) support (08/22/2014)
 * 1.0.2 - fixes undefined 'dir' error - by itsyash (06/09/2014)
 * 1.0.1 - updated to work with foreign characters in directory/file names (12 April 2008)
 * 1.0.0 - released (24 March 2008)
 *
 * Output a list of files for jQuery File Tree
 */

$root="/data";

$_POST['dir'] = urldecode((isset($_POST['dir']) ? $_POST['dir'] : null ));

$DIR=realpath($_POST['dir']);

if (substr($DIR, 0, strlen($root)) !== $root) {
  $DIR=$root;
}

$DIR=$DIR.'/';

// set checkbox if multiSelect set to true
$checkbox = ( isset($_POST['multiSelect']) && $_POST['multiSelect'] == 'true' ) ? "<input type='checkbox' />" : null;

if(file_exists($DIR) ) {
  $files = scandir($DIR);
  if( count($files) > 2 ) { // The 2 accounts for . and ..
    array_shift($files);
    array_shift($files);
	  natcasesort($files);
		echo "<ul class='jqueryFileTree'>";
		// All dirs
    if ($_POST['showFiles']==='true')
    foreach( $files as $file ) {
			if(!is_dir($DIR . $file) ) {
        $ext = preg_replace('/^.*\./', '', $file);
        if ($ext=="xml") {
          echo "<li class='file ext_{$ext}'>{$checkbox}<a href='#' rel='" . htmlentities($DIR . $file) . "'>" . htmlentities($file) . "</a></li>";
        }
			}
    }
		// All files
    if  ($_POST['showDirectories']==='true')
		foreach( $files as $file ) {
			if(is_dir($DIR . $file) ) {
				echo "<li class='directory collapsed'>{$checkbox}<a href='#' rel='" .htmlentities($DIR . $file). "/'>" . htmlentities($file) . "</a></li>";
			}
    }
		echo "</ul>";
	}
}

?>
