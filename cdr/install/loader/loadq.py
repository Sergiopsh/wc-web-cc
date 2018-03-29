#!/bin/env python
#
# Copyright (C) 2006 Earl Terwilliger  earl@micpc.com
#
import sys, MySQLdb, string, fileinput
try:
  conn = MySQLdb.connect(host = "localhost",
                        user = "asteriskuser",
                        passwd = "asterisk",
                        db = "asterisk")
except MySQLdb.Error, e:
  print "Error %d: %s" % (e.args[0], e.args[1])
  sys.exit (1)

try:
  cursor = conn.cursor ()
except MySQLdb.Error, e:
  print "Error %d: %s" % (e.args[0], e.args[1])
  sys.exit (1)

try:
  cursor.execute("select max(timestamp) from queuelog")
except MySQLdb.Error, e:
  print "Error %d: %s" % (e.args[0], e.args[1])
  sys.exit (1)

numrows = int(cursor.rowcount)
maxtime = None
if (numrows == 1):
 row = cursor.fetchone()
 maxtime = row[0]
if maxtime == None:
 maxtime = 0

rows = 0
#in_file = open(sys.argv[1],"r")
#for in_line in in_file.readlines():
for in_line in fileinput.input():
 in_line = string.strip(in_line[:-1])
 if (in_line[-1] == "|"):
   in_line = in_line[0:-1]
 s = string.split(in_line,"|")
 if (int(maxtime) >= int(s[0])):
   continue
 length = len(s)
 if (length < 6):
   s.append('')
 if (length < 7):
   s.append('')
 if (length < 8):
   s.append('')
 try:
   cursor.execute("INSERT INTO queuelog (timestamp,callid,qname,agent,action,info1,info2,info3) VALUES  (%s,%s,%s,%s,%s,%s,%s,%s)", (s[0],s[1],s[2],s[3],s[4],s[5],s[6],s[7]))

 except MySQLdb.Error, e:
   print "Error %d: %s" % (e.args[0], e.args[1])
   sys.exit (1)
 rows += 1

#in_file.close()
conn.close ()
print "%d rows were inserted" % rows
sys.exit (0)
