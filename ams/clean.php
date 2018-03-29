#!/bin/bash

PATH="/usr/kerberos/sbin:/usr/kerberos/bin:/usr/local/sbin:/usr/sbin:/sbin:/usr/bin:/bin:/root/bin"

echo "insert into queuemetrics.bad_log select * from queuemetrics.queue_log where verb='ABANDON' and data3<6" | mysql
echo "delete from queuemetrics.queue_log where verb='ABANDON'  and data3<6" | mysql
