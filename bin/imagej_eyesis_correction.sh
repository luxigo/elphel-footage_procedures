#!/bin/sh

FIJI=ImageJ-linux64
MEM=8192m

if [ $# -ne 4 ] ; then
  echo "Invalid number of parameters"
  exit 1
fi

prefs="$1"
source="$2"
results="$3"
timestamp="$4"

PREFS="/tmp/Eyesis_Correction_$timestamp.xml"
LOGFILE="/tmp/Eyesis_Correction_$timestamp.log"
PIDFILE="/tmp/Eyesis_Correction_$timestamp.pid"

if ! corrxml.sh "$prefs" "$source" "$PREFS" ; then
  echo corrxml.sh failed
  exit 1
fi

/usr/bin/time $FIJI --headless --allow-multiple --mem $MEM --run Eyesis_Correction "$PREFS" > "$LOGFILE" 2>&1 &
FIJI_PID=$!

echo $FIJI_PID > "$PIDFILE"

sleep 5

if ! killall -0 $FIJI_PID ; then
  echo "Error: see $LOGFILE for details"
  exit 1
fi

exit 0



