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

?>
 <br>
 <form name=details method=post action=../includes/listdetails.php>
  <INPUT TYPE=hidden NAME=query VALUE="<?php echo $sql; ?>">
  <INPUT TYPE=hidden NAME=title VALUE="<?php echo $function_title_long; ?>">
  <INPUT TYPE=hidden NAME=start VALUE="<?php echo $start_date; ?>">
  <INPUT TYPE=hidden NAME=end   VALUE="<?php echo $end_date; ?>">
  <input type=submit name=submit value="DETAILS">
 </form>
