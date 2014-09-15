<?php
$source=$_GET['source'];
$results=$_GET['results'];
$prefs=$_GET['prefs'];

$output=array();
$timestamp=str_replace('.','_',microtime(true));
exec("./bin/imagej_eyesis_correction.sh ".escapeshellarg($prefs)." ".escapeshellarg($source)." ".escapeshellarg($results)." ".$timestamp,$output,$ret);
if ($ret!=0) {
     send_reply('{"error": "'.str_replace('"','\"',$output[0]).'"}');
}
send_reply('{"message": "ImageJ Eyesis correction started", "timestamp": "'.$timestamp.'"}');

function send_reply($reply) {
    header("Content-Type: application/json");
    header("Content-Length: "+strlen($reply));
    echo $reply;
    exit(0);
}
?>
