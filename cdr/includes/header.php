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

 session_start();
 if (!isset($_SESSION['Level'])) header("Location: ../");
 echo "<HTML>\n";
 echo " <HEAD>\n";
 echo "  <META HTTP-EQUIV=\"Pragma\"  CONTENT=\"no-cache\">\n";
 echo "  <META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">\n";
 echo "  <META NAME=\"Copyright\"     CONTENT=\"Copyright (C) 2006 Earl Terwilliger earl@micpc.com All Rights reserved\">\n";
 echo "  <META NAME=\"Description\"   CONTENT=\"PHP script '";
 echo $_SERVER['PHP_SELF'];
 echo "' Copyright (C) 2006 by Earl C. Terwilliger earl@micpc.com\">\n";
 echo "  <TITLE>Asterisk Queue Log Analyzer</TITLE>\n";
 echo "  <LINK REL=\"SHORTCUT ICON\" HREF=\"http://$_SERVER[SERVER_NAME]/favicon.ico\">\n";
?>
<link rel="stylesheet" type="text/css" href="../includes/style.css">
</HEAD>
<BODY>

