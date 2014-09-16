#!/bin/bash
export JP4DIR=$1
export OUTFILE=$2
export JP4LIST=()
export MYPID=$BASHPID
export INOTIFY_STDERR=$(mktemp)

trap "killtree -9 $MYPID" EXIT SIGINT SIGKILL SIGHUP

usage() {
  echo "usage: $(basename $0) <jp4_directory> <output_list>"
  exit $1
}

[ $# -ne 2 -o "$1" == "-h" ] && usage

wait_jp4() {
  local l
  inotifywait -m -e close_write $JP4DIR 2> $INOTIFY_STDERR | doit | while read timestamp ; do
    if ! grep -q $TIMESTAMP $OUTFILE ; then
      echo $TIMESTAMP >> $OUTFILE
    fi
  done
}

doit() {

  node << EOF 

var jp4list={};

readline=require('readline');

var rl=readline.createInterface({

  input: process.stdin,
  output: process.stdout

}).on('line',function(line){

  var filename=line.split('.')[0];
  var timestamp=filename.substr(0,17);
  var camera=filename.substr(18);

  if (!jp4list[timestamp]) {
    jp4list[timestamp]=[];
  }

  var got=jp4list[timestamp];
  got[camera]=1;

  if (got[1] && got[2] && got[3] && got[4] && got[5] && got[6] && got[7] && got[8] && got[9]) {
    console.log(timestamp);
    delete jp4list[timestamp];
  }

}).on('close',function(){
  process.exit(0);
});

EOF
}

wait_jp4 &

tail -f $INOTIFY_STDERR | while read msg ; do
  echo $msg >&2
  [ "$msg" =~ "Watches established" ] && touch $JP4DIR/*.jp4
done

rm $INOTIFY_STDERR

