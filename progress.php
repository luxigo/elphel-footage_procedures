<?php
$jobname=$_GET['j'];
$timestamp=$_GET['t'];

$pid_file="/tmp/".$jobname."_".$timestamp.".pid";
$job_pid=file_get_contents($pid_file);

$output=array();
$ret;
exec('kill -0 '.escapeshellarg($job_pid),$output,$ret);
if ($ret!=0){
 .... zzz..
}
function send_reply(reply) {                                                            
    header("Content-Type: text/json");                                                  
    header("Content-Length: "+strlen($reply));                                          
    echo $reply;                                                                        
    exit(0);                                                                            
}                                                                                       

?>
