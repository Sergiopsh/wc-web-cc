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

function align($s1, $s2)
{
  $l1 = strlen($s1);
  $l2 = strlen($s2);
  if ($l1 >= $l2) return;
  $len = $l2 - $l1;
  while($len > 0) { echo "&nbsp; "; $len--; }
}

function mk_query($q,$var,$var1,$op,$andor)
{
  GLOBAL $NUMOPS,$PREVOP;
  if ($var != "") {
    if (($NUMOPS > 0) && ($PREVOP == ""))  $q = $q."AND ";
    if ($op == "") $op = "LIKE";
    $q = $q.$var1." ".$op." \'".$var."\' ";
    if ($andor != "") $q = $q.$andor." ";
    $PREVOP = $andor;
    $NUMOPS += 1;
  }
  return $q;
}

function make_selection($name,$var)
{
?>
      <TR>
        <TD><?PHP echo $name; ?></TD>
        <TD>&nbsp;&nbsp;<SELECT SIZE="1" NAME="op_<?PHP echo $var; ?>">
            <OPTION></OPTION>
            <OPTION> = </OPTION>
            <OPTION> <= </OPTION>
            <OPTION> >= </OPTION>
            <OPTION> LIKE </OPTION>
            <OPTION> NOT LIKE </OPTION>
            <OPTION> != </OPTION>
            <OPTION> &lt  </OPTION>
            <OPTION> &gt  </OPTION>
        <TD>
        <TD>&nbsp;&nbsp;<INPUT SIZE="30" NAME="<?PHP echo $var; ?>" value=""></TD>
        <TD>&nbsp;&nbsp;<SELECT SIZE="1" NAME="andor_<?PHP echo $var; ?>">
            <OPTION></OPTION>
            <OPTION> AND </OPTION>
            <OPTION> OR </OPTION>
        <TD>
      </TR>
<?PHP
}
?>
