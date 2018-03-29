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

$fmHeight=400;
$show_action_time=false;

?>

<div id="fileManager-body" onclick="fm.cMenu.hide();"  
 oncontextmenu="">
<div id="frame-module-header" nowrap>
<?=$strFileManager?>
</div>
<div id="filemanager-message" class="module-warning" style="display: none;">
</div>
<br>
<script>
ams.showReloadLink();

<?
include("js/Sound.js");
include("js/player.js");
?>

//fm=GlobalFileManager;

fm = new _fileManager('fileManager-body',{});

ams.deleteObj.push(fm);
fm.bgColor=$('frame-module-content').style.backgroundColor;

fm.menu['cut'] = Array('<?=$strCut?>','fm.moveFiles(\"cut\")','fm.select.length','cut_off.gif','cut_on.gif');
fm.menu['copy'] = Array('<?=$strCopy?>','fm.moveFiles(\"copy\")','fm.select.length','copy_off.gif','copy_on.gif');
fm.menu['paste'] = Array('<?=$strPaste?>','fm.pasteFiles(\"copy\")','fm.clip.buffer.length','paste_off.gif','paste_on.gif');
fm.menu['delete'] = Array('<?=$strDelete?>','fm.deleteFiles()','fm.select.length','delete_off.gif','delete_on.gif');
fm.menu['mkdir'] = Array('<?=$strMakeDir?>','fm.service(\"mkdir\")','fm.folder','mkdir_off.gif','mkdir_on.gif');
fm.menu['rmdir'] = Array('<?=$strDeleteDir?>','fm.deleteDir()','fm.folder','rmdir_off.gif','rmdir_on.gif');
fm.menu['upload'] = Array('<?=$strUploadFile?>','fm.service(\"upload\")','fm.folder','upload_off.gif','upload_on.gif');
fm.menu['download'] = Array('<?=$strDownload?>','fm.downloadFiles()','fm.select.length','download_off.gif','download_on.gif');
fm.menu['w_audio'] = Array('<?=$strWriteAudioFile?>','fm.service(\"w_audio\")','fm.folder','w_audio_off.gif','w_audio_on.gif');
fm.menu['refresh'] = Array('<?=$strRefresh?>','fm.showDir(fm.folder)','fm.folder','refresh_off.gif','refresh_on.gif','');
fm.menu['refresh'][5]="<input type='text' name='r_pattern' id='r_pattern' size='15' value='*'>";
//fm.menu['convert'] = Array('<?=$strConvert?>','fm.service(\"convert\",fm.select)','fm.select.length','');
fm.menu['play'] = Array('<?=$strPlay?>','fm.play()','fm.select.length','');
fm.menu['rename'] = Array('<?=$strRename?>','fm.rename(\"right\")','fm.select.length==1','');
fm.menu['rename_dir'] = Array('<?=$strRename?>','fm.rename(\"left\")','true','');
fm.menu['call'] = Array('<?=$strCallToPhone?>','fm.call()','fm.select.length','dial.gif','dial.gif');
fm.menu['call_m'] = Array('<?=$strCallToPhone?>','fm.service(\"call\",fm.select)','(fm.select.length == 1) && fm.isCallToPhone(fm.selectExt)','dial.gif','dial.gif');
fm.menu['view'] = Array('<?=$strQuickView?>','fm.view()','(fm.select.length == 1) && fm.isLooking(fm.selectExt)','view_off.gif','view_on.gif');
fm.menu['edit'] = Array('<?=$strEdit?>','fm.edit()','(fm.select.length==1) && fm.isEditing(fm.selectExt)','edit.gif');
fm.menu['selectall'] = Array('<?=$strSelectAll?>','fm.selectAll()','true','');
//fm.menu['hidden'] = Array('','fm.showHidden(this,event)','fm.select.length','m_off.gif','m_on.gif','','onclick');
fm.menu['hidden'] = Array();
fm.menu['hidden'][5]="<img src='modules/FileManager/images/m_off.gif' "+ 
" onmouseover='if(fm.folder) this.src=\"modules/FileManager/images/m_on.gif\"' "+
" onmouseout='this.src=\"modules/FileManager/images/m_off.gif\"' "+
" onclick='if(fm.folder) fm.showHidden(this,event);' >";

var contextMenu1=Array('cut','copy','paste','delete','selectall');
var contextMenu2=Array('mkdir','rmdir','upload','w_audio');
var contextMenu3=Array('selectall');
fm.cMenu = new _contextMenu('fileManager-body',{imgdir: 'modules/FileManager/images'});

fm.cMenu.items=fm.menu;

fm.showHidden = function(id,event) {

     if(!this.folder) return false;
     var m=Array('edit','view','selectall','rename');
     var ext,len = this.select.length;
     ext = this.selectExt.toLowerCase();
     var m2=Array('/');
     if(ext && len) {
	var l = m2.length;                           
        if(this.isPlaying(ext) && ((len == 1) || ext == 'mp3')) 
	    m2=m2.concat('play');
        //if(this.isCallToPhone(ext) && (len ==1)) m2=m2.concat('call');
        //if(this.isAudio(ext)) m2=m2.concat('convert');
        if(l == m2.length) m2.shift();
	m=m.concat(m2);
    }
    var mdiv=this.cMenu.create(m,event);
    Position.clone(id,mdiv,{setWidth: false, setHeight: false,offsetTop: 15});
    return false;
}

fm.contextMenuLeft = function(dir,event) {
 var m=contextMenu2;
 if(dir=='') {
    if(!this.folder) return false;
    else dir=this.folder;
 } 
 if(this.inTree(dir) && !this.isTopDir(dir) && this.isFolderVisible(dir)) m=m.concat('rename_dir');
 if(!this.folder) this.folder=dir;
 this.showDir(dir);
 this.cMenu.create(m,event);
 return false;
}

fm.downloadFiles = function () {
 $('fm-service-form').par.value=this.select;
 $('fm-service-form').action="modules/FileManager/downloadfiles.php";
 $('fm-service-form').submit();
}

fm.view = function () {
    var file=this.select[0];
    var ext=this.fileExt(file).toLowerCase();
    var type;
    var menubar='no';
    switch (ext) {
	case 'pdf': type='pdf'; break;
	case 'doc': type='doc'; break;
	case 'jpeg' : case 'jpg': case 'png': 
	case 'tif': case 'tiff': case 'html': type=ext; break;
	case 'gif': if(isIE) type='html'; else type=ext; break;
	default: type='txt'; menubar='yes'; break;
    }

    var wt="top=" + Math.ceil(screen.availHeight * 0.1);
    var wl=", left=" + Math.ceil(screen.availWidth * 0.15);
    var url="lib/loadfile.php?file="+file+"&type="+type;
    var h = Math.ceil(screen.availHeight * 0.75);
    var w = Math.ceil(screen.availWidth * 0.7);
    var id=type+'file'+Math.round(Math.random()*1000000000);
    open(url, id, "height="+h+", width="+w+",status=0,menubar="+menubar+",resizable=1,scrollbars=0," + wt + wl);
}

fm.edit = function () {

    var file=this.select[0];
    var type;
    var wt="top=" + Math.ceil(screen.availHeight * 0.1);
    var wl=", left=" + Math.ceil(screen.availWidth * 0.18);
    var url="modules/FileManager/editfile.php?file="+encodeURIComponent(file);
    //var h = Math.ceil(screen.availHeight * 0.75);
    var h = 600;
    var w = Math.ceil(screen.availWidth * 0.7);
    
    var id=type+'file'+Math.round(Math.random()*1000000000);
    open(url, id, "height="+h+", width="+w+",status=0,menubar=1,resizable=1,scrollbars=0," + wt + wl);
}

fm.play = function() {

    var ext=this.selectExt.toLowerCase();
    switch (ext) {
	case 'wav': 
		    this.player.hide();
		    this.playWav(this.select[0]);
		    break;
	case 'mp3': 
		    $('_play_file_').innerHTML='';
		    $('_play_file_').hide();
		    $('_mp3player_').show();
		    this.player.load(this.select);
		    break;
	default: return;
    }
}

fm.playWav = function(file) {
    $('_play_file_').show();
    var s="<embed src='lib/loadfile.php?file="+encodeURIComponent(file)+"&type=wav"+
    "' autostart='true' loop='false' width='300' height='40'></embed>";
    s+="&nbsp;<a href='javascript:void(0)' title='<?=$strSwitchOff?>' onclick='$(\"_play_file_\").innerHTML=\"\"'>"+
	"<img src='modules/FileManager/images/close2.gif' align='top'></a>";
    $('_play_file_').innerHTML=s;

}

fm.contextMenuRight = function(event) {
 if(!this.folder) return false;
 var m=contextMenu1;
 var ext,len = this.select.length;
 ext = this.selectExt.toLowerCase();
 if(ext && len) {
    m=m.concat('/'); var l = m.length;                           
    if(this.isPlaying(ext) && ((len == 1) || ext == 'mp3')) 
	m=m.concat('play');
    if(this.isCallToPhone(ext) && (len ==1)) m=m.concat('call');
    //if(this.isAudio(ext)) m=m.concat('convert');
    if(l != m.length) { m=m.concat('/'); l = m.length; }
    if(this.isEditing(ext)) m=m.concat('edit'); 
    if(this.isLooking(ext)) m=m.concat('view');
    if(l != m.length) m=m.concat('/'); 
    if(len ==1) m=m.concat('rename');
    m=m.concat('download');                            

 }
 this.cMenu.create(m,event);
 return false;
}

fm.rename = function(type) {

    var f,id;
    if(type=='left') { f=this.folder; id=this.leftId[f];   }
    else { f=this.select[0]; id=this.rightId[f];  }
    if(!$(id)) return;
    var file=this.baseName(f);
    var el = document.createElement('input');
    el.type = 'text';  el.name=el.id='rn'+id; el.value = file;
    el.size = file.length +1; el.cssStyle = "font-size: 11px;";
    el.ondblclick = el.onkeyup = fm.doRename.bindAsEventListener(this,el,type,f);
    el.saveHTML=$(id+'-cell').innerHTML;
    $(id+'-cell').innerHTML="";
    $(id+'-cell').appendChild(el);
    el.style.backgroundColor="#baeea5";
    el.focus();

}

fm.doRename = function(evt,p,type,oldname) {
    if(evt.keyCode && evt.keyCode != 13) return;
    var id=p.id.substr(2)+'-cell';
    var newname=this.dirName(oldname)+'/'+p.value;
    if(oldname == newname) {
        $(id).innerHTML=p.saveHTML;
	if(type == 'left') {
	    if(oldname != fm.folder) fm.unhLite(fm.leftId[oldname]);
	} else {
	    if(!fm.isSelected(oldname)) fm.unhLite(fm.rightId[oldname]);
	}
	return;
    }
    var url='modules/FileManager/rename.php';
    var pb="oldname="+encodeURIComponent(oldname)+"&newname="+encodeURIComponent(newname);
    new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {
	    if(t.responseText != "success") {
		ams.showMessage(t.responseText,'filemanager-message',1,'warning.gif');
		$(id).innerHTML=p.saveHTML;
		if(type == 'left') {
		    if(oldname != fm.folder) fm.unhLite(fm.leftId[oldname]);
		} else {
		    if(!fm.isSelected(oldname)) fm.unhLite(fm.rightId[oldname]);
		}
	    }
	    else fm.postRename(type,oldname,newname);		
	}
    });
}

fm.deleteFiles=function() {
    if(!fm.select.length) return;
    if(!confirm("<?=$strConfirmDelete?> ?")) return;
    var url='modules/FileManager/deletefiles.php';
    var pb="dir="+encodeURIComponent(this.folder);
    if(this.select.length) pb+=this.getParString(this.select,'files')

    new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {

	    if(t.responseText=="success") fm.postDeleteFiles();
	    else ams.showMessage(t.responseText,'filemanager-message',1,'warning.gif');
	    
	}
    });
}

fm.pasteFiles = function() {
    if(!this.folder) return;
    if(!this.clip.buffer.length) return;
    if(!this.clip.action) return;
    var url='modules/FileManager/pastefiles.php';
    var pb="dir="+encodeURIComponent(this.folder)+"&action="+fm.clip.action;
    this.hlfiles.clear();
    pb+=this.getParString(this.clip.buffer,'files');
    fm.errorMsg='';
    new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {
	    
	    if(t.responseText) {
	        var msg=t.responseText.stripScripts();
	        if(msg != 'success') ams.showMessage(msg,'filemanager-message',1,'warning.gif');
	        else {
	    	    t.responseText.evalScripts();
	    	    fm.showDir(fm.folder);
	        }
	    } 
	}
    });
}

fm.searchFiles=function(){

}

fm.checkValidDir = function(dir) {
    if(!this.isValidDir(dir)) {
	alert("<?=$strNotValidDir?> " + dir);
	return false;
    }
    return true;
}

fm.call = function() {
    if(!fm.dial_exten || !fm.ast_chan || !fm.playToPhone) {
	fm.service('call',fm.select);
	return;
    }
    fm.playToPhone('call',fm.select[0]);
}

fm.service = function(srv,par){

 if(!this.folder) return;
 var url='modules/FileManager/srv'+srv+'.php';
 var pb="dir="+encodeURIComponent(this.folder);
 if(par) pb+=this.getParString(par,'par');
 
 new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {
	    if(t.responseText) {
		if($('fm-service').style.display !== 'none') {
		  Element.hide('fm-service');
		}
	    	Element.update('fm-service-div',t.responseText);
		new Effect.BlindDown('fm-service',{duration: 0.3});
	    }
	}
    });
}

fm.deleteDir=function() {
    if(!this.folder || this.isTopDir(this.folder)) return;
    if(!confirm("<?=$strConfirmDeleteDir?> "+this.folder+" ?")) return;
    var url='modules/FileManager/deletefiles.php';
    var pb="dir="+encodeURIComponent(this.folder)+"&files[0]="+encodeURIComponent(this.folder);
    new Ajax.Request(url,{ postBody: pb,
	onComplete: function(t) {
	    //alert(t.responseText);
	    if(t.responseText=="success") fm.postDeleteDir();
	    else ams.showMessage(t.responseText,'filemanager-message',1,'warning.gif');
	}
    });
}
fm.showFileInfo = function(file,event) {
    var url='modules/FileManager/fileinfo.php';
    var pb="file="+encodeURIComponent(file);
    var id=fm.rightId[file];
    new Ajax.Request(url,{ postBody: pb,
	onComplete: function(t) {
	    if(t.responseText)
	      ams.toolTip(id,0,t.responseText,{close: 1, title: '<?=$strFileInfo?>',container: 'fm_right'});
	}
    });

}
fm.onClick = function(f,event) {
    this.selectFile(f,event);
    this.cMenu.hide();
    if(event) Event.stop(event);
}

fm.onDblClick = function(f) {
    var ext = this.fileExt(f).toLowerCase();
    if(!ext) return;
    if(fm.isLooking(ext)) fm.view(f);
    else if(fm.isPlaying(ext)) fm.play(f);
}

fm.onContextMenu = function(id,f,event) {
      clearTimeout(id.ft_timer); 
      if(!this.isSelected(f)) this.selectFile(f,event); 
      return this.contextMenuRight(event);

}
fm.onMouseOver = function(id,f,event) {
      var e= event.shiftKey ? 1:0;
      if(!this.isSelected(f)) id.style.backgroundColor='#c3c3c3';
      id.stimer=setTimeout(function () { fm.selectFile(f,e)  },700); 
      id.ft_timer=setTimeout(function () { fm.showFileInfo(f,event)  },2500); 
      this.cMenu.hide();
      if(event) Event.stop(event);
}

fm.onMouseOut = function(id,f) {
      if(this.isSelected(f)) id.style.backgroundColor='#34548f';
      else id.style.backgroundColor=fm.bgColor;
      clearTimeout(id.stimer); 
      clearTimeout(id.ft_timer); 
}

fm.showTree=function(dir,refresh){

    if(!this.tree[dir]) this.createTree(dir);
    if(this.tree[dir].open) {
	this.closeTree(dir);
	return;
    }
    if(!refresh && $(this.leftId[dir]+'-div')) {
	if($(this.leftId[dir]+'-div').innerHTML) { this.openTree(dir); return; }
    }
    var url='modules/FileManager/showtree.php';
    var pb="dir="+encodeURIComponent(dir);
    new Ajax.Request(url,{ postBody: pb,
	onComplete: function(t) {
	    //alert(t.responseText);
	    if(t.responseText) fm.openTree(dir,t.responseText);
	}
    });
}

fm.showDir=function(dir) {

    var url='modules/FileManager/showdir.php';
    var pb="dir="+encodeURIComponent(dir);
    if($F('r_pattern')) pb+="&pattern="+encodeURIComponent($F('r_pattern'));
    new Ajax.Request(url,{ postBody: pb,
	onComplete: function(t) {
	    if(t.responseText) {
		Element.update('fm_right',t.responseText);
		fm.changeFolder(fm.folder,dir);
		setTimeout("fm.highliteFiles()",100);
		$('fm-right-sub').innerHTML='<small>&nbsp;&nbsp;<?=$strSelectionHelp?></small>';
	    }
	}
    });

}


</script>


<table border=0 cellpadding=0 cellspacing=0 width="95%">
<tr>
<td align="center" id='top-menu-left2' width="30%" oncontextmenu="return false;">
</td>
<td>&nbsp;</td>
<td align="left" id='top-menu-right' oncontextmenu="return false;">
</td>


</tr>
</table>
<script>
  fm.topMenu1 = new _Toolbar('top-menu-left2',{className: 'menu-panel', imgdir: 'modules/FileManager/images'});
  fm.topMenu2 = new _Toolbar('top-menu-right',{className: 'menu-panel',imgdir: 'modules/FileManager/images'});
  fm.topMenu1.items=fm.topMenu2.items=fm.menu;
  fm.topMenu1.create(Array('/','mkdir','rmdir','upload','/','w_audio'));
  fm.topMenu2.create(Array('/','refresh','/','cut','copy','paste','delete','/','download','call_m','/','hidden'));
</script>


<div id="fm-service" style="display: none;margin-top: 3px;">
 <table border=0 width="95%" class="input-data-tbl" cellspacing=0 >
 <tr><td >

 <div id="fm-service-div" style="font-family: verdana,sans; font-size: 11px; 
 font-style: italic; color: #34548f;">
 </div>
 <td width="15" align="right" valign="top" style="padding: 0px;"> 
 <a href="javascript:fm.hideSrv()" title="<?=$strSwitchOff?>">
 <img src="modules/FileManager/images/close2.gif" align="top">
 </a>
 </td>
 </tr>
 </table>

</div>

<table border=0 width="95%" id="table_fm_left">
<tr>
<td width="30%" valign="top" id="td_fm_left">

<div id="fm_left" oncontextmenu="return fm.contextMenuLeft('',event);"  
 style="width: 100%; border: 1px solid #dddddd; height: <?=$fmHeight?>px; 
 overflow: auto; ">


<?
$i=0;
foreach($filemanager_dirs as $d=>$dir) {

    $dir_name = $d ? $d : $dir;
    $id=uniqid(true);
?>
    <script>
      fm.topDirs[<?=$i?>]='<?=addslashes($dir)?>';
      fm.leftId['<?=addslashes($dir)?>']='<?=$id?>';
    </script>
    <table border=0 width="90%" cellspacing=0 cellpadding=0 style="font-family: arial,verdana, sans; font-size: 12px;">
    <tr height="20">
    <td width="16" align="center" valign="center" style="padding-bottom: 1px;">
    <a id="<?=$id?>-treelink" href="javascript:fm.showTree('<?=$dir?>');" oncontextmenu="Event.stop(event); return false;">
    <img id="<?=$id?>-tree" align="absmiddle" src="modules/FileManager/images/plus.gif">
    </a>
    </td><td align="center" valign="center" width="16">
    <a href="javascript:fm.showDir('<?=$dir?>')" oncontextmenu="return fm.contextMenuLeft('<?=$dir?>',event);" >
    <img id="<?=$id?>-folder" align="absmiddle" src="modules/FileManager/images/folder_closed3.gif">
    </a>
    </td><td align="left">
    <a id="<?=$id?>" href="javascript:fm.showDir('<?=ah($dir)?>')" style="color: black;" oncontextmenu="return fm.contextMenuLeft('<?=$dir?>',event);">
    &nbsp;<?=hc($dir_name)?>
    </a>
    </td></tr>
    </table>
    <div id="<?=$id?>-div" style="display: none; margin-left: 16px; margin-top: 0px; "></div>
<?$i++;
}
?>
</div>


</td>

<td style="width: 1px;"></td>
<td valign="top">

<div id="fm_right"  oncontextmenu="return fm.contextMenuRight(event);" onclick="fm.unSelectAll();"
 style="width: 100%; border: 1px solid #dddddd; height: <?=$fmHeight?>px; 
 overflow: auto;">
</div>

</td>
</tr>
<td></td><td></td>
<td align="right" id="fm-right-sub" class="module-note">
</td>
</table>
<br>

<center>
<div id="_play_file_" align="center" valign="top" style="display: none; width: 400px; vertical-align: top;">
</div>
<div align="left" id="_mp3player_" style="display: none; background-color: #92b8d8; font-size: 10px; color: black; width: 230px;  
   font-family: verdana, sans; border: 1px solid #555555;">
    <table border=0 cellspacing=0 cellpadding=0 width="100%" style="font-style: italic;">
      <tr><td align="left" id="_mp3player_display_" >&nbsp;</td>
      <td align="left" id="_mp3player_time_" width="15%" nowrap>&nbsp;</td>
      <td valign="top" align="right" width="10%">
      <a href="javascript:fm.player.hide()" title="<?=$strSwitchOff?>">
      <img align="right" src="modules/FileManager/images/close2.gif"></a>
      </td></tr>
    </table>
    <div nowrap style="width: 100%; overflow: hidden; " align="left" >
      <div id="_mp3player_info_" nowrap style="overflow: hidden; color: #7f0000; font-style: italic;">
      &nbsp;
      </div>
    </div>
    <div>&nbsp;</div>
    <table border=0 cellspacing=0 cellpadding=1 width="100%">
      <tr><td id="_mp3player_progressbar_" style="width: 1%; height: 5px; 
       background-color: #842e51;">
      </td><td bgcolor="#92b8d8"></td>
      </tr>
    </table>
    <table border=0 width="100%" cellspacing=0 cellpadding=0 >
      <tr>
      <td><img align="absmiddle" onmouseover="fm.player.onMouseOver(this,'play')" onmouseout="fm.player.onMouseOut(this,'play')" id="_mp3player_play_" onclick="fm.player.play()"  src="modules/FileManager/images/play_on.gif">
      </td><td><img align="absmiddle" onmouseover="fm.player.onMouseOver(this,'pause')" onmouseout="fm.player.onMouseOut(this,'pause')" id="_mp3player_pause_" onclick="fm.player.pause()" src="modules/FileManager/images/pause_off.gif">
      </td><td><img align="absmiddle" onmouseover="fm.player.onMouseOver(this,'stop')" onmouseout="fm.player.onMouseOut(this,'stop')" id="_mp3player_stop_"  onclick="fm.player.stop()" src="modules/FileManager/images/stop_off.gif">
      </td><td><img align="absmiddle" onmouseover="fm.player.onMouseOver(this,'prev')" onmouseout="fm.player.onMouseOut(this,'prev')" id="_mp3player_prev_" onclick="fm.player.prevTrack()" src="modules/FileManager/images/prev_on.gif">
      </td><td><img align="absmiddle" onmouseover="fm.player.onMouseOver(this,'next')" onmouseout="fm.player.onMouseOut(this,'next')" id="_mp3player_next_" onclick="fm.player.nextTrack()"  src="modules/FileManager/images/next_on.gif">
      </td><td id="_mp3player_msg_" align="center" width="80%"
      style="color: white; background-color: #34548f; font-style: italic;">
      </td><td title="<?=$strPlayList?>" width="10%" align="center" onclick="fm.player.onClickPlayList(event)" style="background-color: #34548f;">
      <img id="_mp3player_pl_button_"  align="absmiddle" src="modules/FileManager/images/pld_w.gif">      
      </td></tr>
    </table>
    <div align="left" id="_mp3player_pl_" style="width: 100%; 
      display: none; border-top: 1px solid #555555; 
      scrollbar-base-color: #92b8d8f; scrollbar-highlight-color: #92b8d8;
      scrollbar-darkshadow-color: #92b8d8;">
    </div>    
</div>
<script>
    fm.player = new Player({container: 'fileManager-body'});
</script>

</center>
<form name="fm-service-form" id="fm-service-form" method="post" target="fm-service-frame">
<input type="hidden" name="par" id="par" value="">
</form>
<iframe name="fm-service-frame" id="fm-service-frame" width="0" height="0" scrolling="yes" frameborder=0
 style="display: none;">
</iframe>

<br>
</div>
