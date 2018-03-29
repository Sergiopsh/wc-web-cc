<?php
/*
 * Asterisk Management System - An open source toolkit for Asterisk PBX.
 * See http://www.asterisk.org for more information about
 * the Asterisk project.
 *
 * Copyright (C) 2006 - 2007, West-Web Limited.
 *
 * Nickolay Shestakov <ns@ampex.ru>
 *
 * This program is free software, distributed under the terms of
 * the GNU General Public License Version 2. See the LICENSE file
 * at the top of the source tree.
 */

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
$show_action_time=false;

moduleHeader($strConfigFiles);

?>
<script src=""js/toolbar.js"></script>
<div id="eConf-history" class="module-warning">
</div>

<div id="eConf-menu" style="width: 95%;">
</div>

<script>

ams.showReloadLink();

if(!GlobalConfigFiles.history) {
    GlobalConfigFiles.history=new Array();
} 
cfHistory=GlobalConfigFiles.history;

eConf = new ObjectD();
eConf.top = new Array();
eConf.showHistory=function() {
    var s="<table border=0 cellpadding=0 cellspacing=0><tr>";
    var f,fname; 
    for (var i=0; i<(cfHistory.length); i++) {
	fname=cfHistory[i];
	f=fname.split('/');
	s+="<td><a style='text-decoration: underline;' href='javascript:eConf.load(\""+fname+"\");'>"+f.last()+"</a></td><td width='8'>&nbsp;</td>";
    
    }
    s+="</tr></table><br>";
    $('eConf-history').innerHTML=s;
}


if(cfHistory.length) eConf.showHistory();
eConf.find = function(){
    Try.these(
	function() {
	    var str;
	    if(eConf.trange == null) eConf.trange=eConf.tArea.createTextRange();
	    str=eConf.trange.findText($('ftext').value);
	    if(str) {
		eConf.trange.scrollIntoView();
		eConf.trange.select();
		eConf.trange.collapse(false);
	    } 
	    else eConf.trange=null;
	},
	function() {
	    eConf.win.find($('ftext').value,false,false,false,false,false,true);

	}
    );
}

eConf.print = function(){
    if(!eConf.win) return;
    $('ce-form').target="framePrintFile";
    $('ce-form').action="lib/printfile.php";
    $('ce-form').content.value=eConf.tArea.value;
    $('ce-form').file.value=eConf.conffile;
    $('ce-form').submit();
}

eConf.save = function() {
    var url="lib/savefile.php";
    var pb="file="+encodeURIComponent(eConf.file)+"&content="+encodeURIComponent(eConf.tArea.value);
    new Ajax.Request(url,
	    {postBody: pb,
	     onComplete: function(t){
		    ams.showMessage(t.responseText,'module-warning');
		}
	    });	
}

eConf.load = function(file){
    var f;
    $('ce-form').target="ce-frame";
    $('ce-form').action="modules/ConfigFiles/loadfile.php";
    $('ce-form').file.value=file;
    $('ce-form').submit();

    this.trange=null;
    this.file=file;
    $('ce-frame').style.display="inline";
    if((this.top.indexOf(file) == -1) && (cfHistory.indexOf(file) == -1 )) {
        if(cfHistory.push(file) > 8) cfHistory.shift();
	this.showHistory(file);
    }
    f=file.split('/');
    $('frame-module-header').innerHTML="<?=$strConfigFiles?>: " + f.last();
}

var cfmenu = new Array('/','save','print','/','undo','find','/');
eConf.menuitems = new Array();

eConf.menuitems['save'] = Array('<?=$strSave?>','eConf.save()','eConf.win','save_off.gif','save_on.gif');
eConf.menuitems['print'] = Array('<?=$strPrint?>','eConf.print()','eConf.win','print_off.gif','print_on.gif');
eConf.menuitems['find'] = Array('<?=$strFind?>','eConf.find()','eConf.win && $("ftext").value.length','find_off.gif','find_on.gif');
eConf.menuitems['find'][5]='<input type="text" size="25" name="ftext" id="ftext" value="<?=$ftext?>">';

 <?$i=0;
 foreach($configfiles_dirs as $cd) {
  $s="<select id='confdirs[".$i."]' onchange='eConf.load(this.value);'>";
  $s.="<option selected value='".$cd[0]."'>";?>
    eConf.top.push("<?=$cd[0]?>");
  <?
  $name = ($cd[1]=="") ? $cd[0] : $cd[1];
    $s.=$name."</option>";
     foreach ($cd[2] as $pattern) {
      foreach(@glob($cd[0].'/'.$pattern) as $conffile) {
	if(!is_dir($conffile)) {
	    $s.="<option value='$conffile'>".basename($conffile)."</option>";
        }
      }
     }
     $s.="</select>&nbsp;<a href='javascript:void(0)'>";
     $s.='<img style=\"margin-bottom: 2px;\" align=\"absmiddle\" onclick=\"eConf.load($(\'confdirs['.$i.']\').value);\"  src=\"images/run.gif\" title=\"'.$strReadFile.'\"></a>';

 ?>
   eConf.menuitems['<?=$name?>']=Array(); cfmenu.push('<?=$name?>');
   eConf.menuitems['<?=$name?>'][5]="<?=$s?>";
<?}?>
cfmenu.push('/');
eConf.menu = new _Toolbar('eConf-menu',{className: 'menu-panel',imgdir: 'modules/ConfigFiles/images'});
eConf.menu.items=eConf.menuitems;
eConf.menu.create(cfmenu);
eConf.trange=null;
</script>

<form name="ce-form" id="ce-form" method="post">
<input type="hidden" name="file" id="file" value="">
<input type="hidden" name="content" id="content" value="">
</form>
<div style="width: 100%;">
<iframe name="ce-frame" id="ce-frame" frameborder="0" scrolling="no" 
style="border: 0; display: none; width: 100%; height: 500px;">
</iframe> 
</div>
<iframe name="framePrintFile" id="framePrintFile" frameborder="0" scrolling="no" height="0" width="0">
</iframe> 
</div>
