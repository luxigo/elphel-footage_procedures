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

get_timestamp_count() {
  local indexlist=()
  local index
  for (( i=0; i<${#JP4LIST[@]}; ++i )) do
    if [[ ${JP4LIST[i]} =~ $TIMESTAMP ]] ; then
      index=${JP4LIST[i]:18}
      indexlist[$index]=1
    fi
  done
  echo ${#indexlist[@]}
}

del_timestamp() {
  local count=0
  for (( i=0; i<${#JP4LIST[@]}; ++i )) do
    if [[ ${JP4LIST[i]} =~ $TIMESTAMP ]] ; then
      unset JP4LIST[i] 
      ((++count))
      [ $count -eq 9 ] && break
    fi
  done
  JP4LIST=(${JP4LIST[@]})
}

wait_jp4() {
  local l
  inotifywait -m -e close_write $JP4DIR 2> $INOTIFY_STDERR | while read l ; do
    local event=($l)
    local filename=$(basename ${event[2]} .jp4)
    JP4LIST+=($filename)
    TIMESTAMP=${filename:0:17}
    local count=$(get_timestamp_count)
    if [ "$count" == "9" ] ; then
      if ! grep -q $TIMESTAMP $OUTFILE ; then
        echo $TIMESTAMP >> $OUTFILE
        echo $TIMESTAMP >&2
      fi
      del_timestamp
    fi
  done
}

wait_jp4 &

tail -f $INOTIFY_STDERR | while read msg ; do
  echo $msg >&2
  [ "$msg" =~ "Watches established" ] && touch $JP4DIR/*.jp4
done

rm $INOTIFY_STDERR

