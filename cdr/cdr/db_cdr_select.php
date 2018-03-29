<?PHP
/*
 Copyright (C) 2006 Earl C. Terwilliger
 Email contact: earl@micpc.com

    This file is part of The Asterisk Queue/CDR Log Analyzer WEB Interface.

    These files are free software; you can redistribute them and/or modify
    them under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    These programs are distributed in the hope that they will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

 include("../includes/header.php");
 GLOBAL $NUMOPS, $PREVOP;
 $NUMOPS = 0;
 $PREVOP = "";
 include("../includes/functions.php");
 echo "<center>";

 echo "<H1><a href=index.php><img src=../images/cdr-logo.png border=0></img></a>";
 echo "&nbsp;&nbsp;";
 echo "<b>";
 echo "CDR RECORDS SELECT";
 echo "</b>";
 echo "&nbsp;&nbsp;";
 echo "<a href=../main/index.php><img src=../images/q-logo.png border=0></img></a>";
 echo "</H1>";
?>
<FORM METHOD="post" ACTION="db_cdr_select_query.php">
<TABLE BORDER="0">
<?PHP make_selection("billed","billed"); ?>
<?PHP make_selection("uniqueid","uniqueid"); ?>
<?PHP make_selection("userfield","userfield"); ?>
<?PHP make_selection("accountcode","accountcode"); ?>
<?PHP make_selection("src","src"); ?>
<?PHP make_selection("dst","dst"); ?>
<?PHP make_selection("dcontext","dcontext"); ?>
<?PHP make_selection("clid","clid"); ?>
<?PHP make_selection("channel","channel"); ?>
<?PHP make_selection("dstchannel","dstchannel"); ?>
<?PHP make_selection("lastapp","lastapp"); ?>
<?PHP make_selection("lastdata","lastdata"); ?>
<?PHP make_selection("calldate","calldate"); ?>
<?PHP make_selection("duration","duration"); ?>
<?PHP make_selection("billsec","billsec"); ?>
<?PHP make_selection("disposition","disposition"); ?>
<?PHP make_selection("amaflags","amaflags"); ?>
</TABLE>
<BR>
<INPUT TYPE=submit NAME=SUBMIT VALUE=SUBMIT>
<INPUT TYPE=reset  NAME=RESET  VALUE=RESET>
</FORM>
</center>
</BODY>
</HTML>
