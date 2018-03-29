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

 $function_title      = "Calls Dropped";
 $function_title_long = "Calls Dropped [Incompatability]";
 $function_key_th     = "Date";
 $function_val_th     = $function_title;
 $sql = "SELECT * FROM queuelog where action='SYSCOMPAT' order by timestamp";

// ABANDON(position|origposition|waittime)
// AGENTDUMP
// AGENTLOGIN(channel)
// AGENTCALLBACKLOGIN(exten@context)
// AGENTLOGOFF(channel|logintime)
// AGENTCALLBACKLOGOFF(exten@context|logintime|reason)
// COMPLETEAGENT(holdtime|calltime|origposition)
// COMPLETECALLER(holdtime|calltime|origposition)
// CONFIGRELOAD
// CONNECT(holdtime)
// ENTERQUEUE(url|callerid)
// EXITWITHKEY(key|position)
// EXITWITHTIMEOUT(position)
// QUEUESTART
// SYSCOMPAT
// TRANSFER(extension,context)

 include("../includes/gen_chart.php");
?>
