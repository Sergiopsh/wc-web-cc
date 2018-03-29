#!/bin/bash
loadq.py </var/log/asterisk/queue_log
tail -f -n 0 /var/log/asterisk/queue_log | loadq.py &
