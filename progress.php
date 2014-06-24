<?php
$jobname=$_GET['j'];
$timestamp=$_GET['t'];
$pid_file="/tmp/".$jobname."_".$timestamp.".pid";
$ret_file="/tmp/".$jobname."_".$timestamp.".ret";

if (file_exists($ret_file)) {
    $ret=file_get_contents($ret_file);
    unlink($pid_file);
    send_reply('{"status": "terminated", "exit_code": "'.escapeshellarg($ret).'"}');
}

if (!file_exists($pid_file)) {
     send_reply('{"status": "error", "error": "PID file not found: '.$pid_file.'"}');
}

$job_pid=file_get_contents($pid_file);
$output=array();
exec('ps -o comm= -p'.escapeshellarg($job_pid),$output,$ret);
if ($ret==0){
     $elapsed=time()-explode('_',$timestamp)[0]; 
     send_reply('{"status": "running", "command": "'.$output[0].'", "elapsed": "'.$elapsed.'"}');
} else {
     unlink($pid_file);
     send_reply('{"status": "error", "error": "Job not found: '.$jobname.' '.$timestamp.' '.$job_pid.'"}');
}

function send_reply($reply) {                                                            
    header("Content-Type: application/json");                                                  
    header("Content-Length: ".strlen($reply));                                          
    echo $reply;                                                                        
    exit(0);                                                                            
}                                                                                       

?>
