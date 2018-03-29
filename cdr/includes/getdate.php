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

 $current_year      = date("Y");
 $current_month     = date("m");
 $current_day       = date("d");
 $current_date      = $current_year . "-" . $current_month . "-" . $current_day;
 $current_timestamp = time();
?>

  <form name=dateinput method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="3" cellspacing="0" border="0">
    <tr>
      <th>Start Date:</th>
      <td>
        <select name="start_month">
          <option value="01" <?php if ($current_month=="01") echo "selected"; ?>>January</option>
          <option value="02" <?php if ($current_month=="02") echo "selected"; ?>>February</option>
          <option value="03" <?php if ($current_month=="03") echo "selected"; ?>>March</option>
          <option value="04" <?php if ($current_month=="04") echo "selected"; ?>>April</option>
          <option value="05" <?php if ($current_month=="05") echo "selected"; ?>>May</option>
          <option value="06" <?php if ($current_month=="06") echo "selected"; ?>>June</option>
          <option value="07" <?php if ($current_month=="07") echo "selected"; ?>>July</option>
          <option value="08" <?php if ($current_month=="08") echo "selected"; ?>>August</option>
          <option value="09" <?php if ($current_month=="09") echo "selected"; ?>>September</option>
          <option value="10" <?php if ($current_month=="10") echo "selected"; ?>>October</option>
          <option value="11" <?php if ($current_month=="11") echo "selected"; ?>>November</option>
          <option value="12" <?php if ($current_month=="12") echo "selected"; ?>>December</option>
        </select>
      </td> <td>
        <select name="start_day">
          <option value="01" selected>01</option>
          <option value="02">02</option>
          <option value="03">03</option>
          <option value="04">04</option>
          <option value="05">05</option>
          <option value="06">06</option>
          <option value="07">07</option>
          <option value="08">08</option>
          <option value="09">09</option>
          <option value="10">10</option>
          <option value="11">11</option>
          <option value="12">12</option>
          <option value="13">13</option>
          <option value="14">14</option>
          <option value="15">15</option>
          <option value="16">16</option>
          <option value="17">17</option>
          <option value="18">18</option>
          <option value="19">19</option>
          <option value="20">20</option>
          <option value="21">21</option>
          <option value="22">22</option> 
          <option value="23">23</option>
          <option value="24">24</option>
          <option value="25">25</option>
          <option value="26">26</option>
          <option value="27">27</option>
          <option value="28">28</option>
          <option value="29">29</option>
          <option value="30">30</option>
          <option value="31">31</option>
        </select>
      </td> <td>
        <select name="start_year">
          <option value="2006" <?php if ($current_year=="2006") echo "selected"; ?>>2006</option>
          <option value="2007" <?php if ($current_year=="2007") echo "selected"; ?>>2007</option>
          <option value="2008" <?php if ($current_year=="2008") echo "selected"; ?>>2008</option>
          <option value="2009" <?php if ($current_year=="2009") echo "selected"; ?>>2009</option>
          <option value="2010" <?php if ($current_year=="2010") echo "selected"; ?>>2010</option>
        </select>
      </td>
       <td>
         <A HREF="#" onClick="cal.select(document.forms['dateinput'].date1,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1">Calendar Select</A>
         <INPUT TYPE="text" NAME="date1" VALUE="" SIZE=10>
       </td>
    </tr>
    <tr>
      <th>End Date:</th>
      <td>
        <select name="end_month">
          <option value="01" <?php if ($current_month=="01") echo "selected"; ?>>January</option>
          <option value="02" <?php if ($current_month=="02") echo "selected"; ?>>February</option>
          <option value="03" <?php if ($current_month=="03") echo "selected"; ?>>March</option>
          <option value="04" <?php if ($current_month=="04") echo "selected"; ?>>April</option>
          <option value="05" <?php if ($current_month=="05") echo "selected"; ?>>May</option>
          <option value="06" <?php if ($current_month=="06") echo "selected"; ?>>June</option>
          <option value="07" <?php if ($current_month=="07") echo "selected"; ?>>July</option>
          <option value="08" <?php if ($current_month=="08") echo "selected"; ?>>August</option>
          <option value="09" <?php if ($current_month=="09") echo "selected"; ?>>September</option>
          <option value="10" <?php if ($current_month=="10") echo "selected"; ?>>October</option>
          <option value="11" <?php if ($current_month=="11") echo "selected"; ?>>November</option>
          <option value="12" <?php if ($current_month=="12") echo "selected"; ?>>December</option>
        </select>
      </td> <td>
        <select name="end_day">
          <option value="01" <?php if ($current_day=="01") echo "selected"; ?>>01</option>
          <option value="02" <?php if ($current_day=="02") echo "selected"; ?>>02</option>
          <option value="03" <?php if ($current_day=="03") echo "selected"; ?>>03</option>
          <option value="04" <?php if ($current_day=="04") echo "selected"; ?>>04</option>
          <option value="05" <?php if ($current_day=="05") echo "selected"; ?>>05</option>
          <option value="06" <?php if ($current_day=="06") echo "selected"; ?>>06</option>
          <option value="07" <?php if ($current_day=="07") echo "selected"; ?>>07</option>
          <option value="08" <?php if ($current_day=="08") echo "selected"; ?>>08</option>
          <option value="09" <?php if ($current_day=="09") echo "selected"; ?>>09</option>
          <option value="10" <?php if ($current_day=="10") echo "selected"; ?>>10</option>
          <option value="11" <?php if ($current_day=="11") echo "selected"; ?>>11</option>
          <option value="12" <?php if ($current_day=="12") echo "selected"; ?>>12</option>
          <option value="13" <?php if ($current_day=="13") echo "selected"; ?>>13</option>
          <option value="14" <?php if ($current_day=="14") echo "selected"; ?>>14</option>
          <option value="15" <?php if ($current_day=="15") echo "selected"; ?>>15</option>
          <option value="16" <?php if ($current_day=="16") echo "selected"; ?>>16</option>
          <option value="17" <?php if ($current_day=="17") echo "selected"; ?>>17</option>
          <option value="18" <?php if ($current_day=="18") echo "selected"; ?>>18</option>
          <option value="19" <?php if ($current_day=="19") echo "selected"; ?>>19</option>
          <option value="20" <?php if ($current_day=="20") echo "selected"; ?>>20</option>
          <option value="21" <?php if ($current_day=="21") echo "selected"; ?>>21</option>
          <option value="22" <?php if ($current_day=="22") echo "selected"; ?>>22</option> 
          <option value="23" <?php if ($current_day=="23") echo "selected"; ?>>23</option>
          <option value="24" <?php if ($current_day=="24") echo "selected"; ?>>24</option>
          <option value="25" <?php if ($current_day=="25") echo "selected"; ?>>25</option>
          <option value="26" <?php if ($current_day=="26") echo "selected"; ?>>26</option>
          <option value="27" <?php if ($current_day=="27") echo "selected"; ?>>27</option>
          <option value="28" <?php if ($current_day=="28") echo "selected"; ?>>28</option>
          <option value="29" <?php if ($current_day=="29") echo "selected"; ?>>29</option>
          <option value="30" <?php if ($current_day=="30") echo "selected"; ?>>30</option>
          <option value="31" <?php if ($current_day=="31") echo "selected"; ?>>31</option>
        </select>
      </td> <td>
        <select name="end_year">
          <option value="2006" <?php if ($current_year=="2006") echo "selected"; ?>>2006</option>
          <option value="2007" <?php if ($current_year=="2007") echo "selected"; ?>>2007</option>
          <option value="2008" <?php if ($current_year=="2008") echo "selected"; ?>>2008</option>
          <option value="2009" <?php if ($current_year=="2009") echo "selected"; ?>>2009</option>
          <option value="2010" <?php if ($current_year=="2010") echo "selected"; ?>>2010</option>
        </select>
      </td>
       <td>
         <A HREF="#" onClick="cal.select(document.forms['dateinput'].date2,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2">Calendar Select</A>
         <INPUT TYPE="text" NAME="date2" VALUE="" SIZE=10>
       </td>
    </tr>
    </tr>
    <tr valign="top">
      <th colspan="6" align="right" valign="middle">
        <input type="submit" name="submit" value="SUBMIT">
      </th>
    </tr>
  </table>
  </form>
