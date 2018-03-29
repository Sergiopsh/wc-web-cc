_fileManager = Class.create();
_fileManager.prototype = {
    
    initialize : function(container, options) {
	this.container = $(container);
	this.folder = '';
	this.select = new Array();
	this.selectExt = '';
	this.audioExt = Array('wav','gsm','mp3','ulaw','ul','al','mu','alaw','g729','g722','ogg','pcm','sln','g723','vox','au','wav49');
	this.callToPhoneAudio = Array('wav','gsm','ulaw','ul','al','mu','alaw','g729','g722','ogg','pcm','sln','g723','vox','au','wav49');
	this.playingAudio = Array('mp3','wav','wav49');
	this.lookingFiles = Array('pdf','conf','cfg','ael','txt','php','htm','html','ini','log','js','css','pl','doc','agi','tiff','tif','jpeg','png','gif','jpg');
	this.editingFiles = Array('conf','cfg','ael','txt','php','htm','html','ini','log','js','css','pl','agi');
	this.videoExt = Array('h263','h264');
	this.clip = new Object();
	this.clip.action ='';
	this.clip.buffer = new Array();
	this.right = new Array();
	this.bgColor = '#ffffff';
	this.items = new Object();
	this.hlfiles= new Array();
	this.menu = new Object();
	this.topDirs = new Array();
	this.wwwdir = 'modules/FileManager/';
	this.imgdir = 'modules/FileManager/images/';
	this.imgFolderClosed='folder_closed3.gif';
	this.imgFolderOpened='folder_open3.gif';
	this.msgContainer='filemanager-message';
	this.srvContainer='fm-service';
	this.menu = new Object();
	this.tree = new Object();
	this.dirContent = new Object();
	this.leftId = new Object();
	this.rightId = new Object();
	this.setOptions(options);
	if(!container) return;
    },
    setOptions: function(options) {
	this.options = {
	    fColor	: '#000000',
	    sfColor	: '#ffffff',
	    sfBgColor	: '#34548f'
	}
	Object.extend(this.options, options || {});
    
    },
    isSelected: function(file) {
	if (this.select.indexOf(file) == -1) return false;
	return true;
    },

    isValidDir: function(dir) {
	if(dir == "") return false;
	for (var i=0; i < this.topDirs.length; i++)
	    if(dir.indexOf(this.topDirs[i] + '/') !== -1) break;
	if(i == this.topDirs.length) return false;
	return true;
	
    },
    isTopDir: function(dir) {
	if (this.topDirs.indexOf(dir) == -1) return false;
	return true;
    },
    highliteFiles: function(color) {
	if(!this.hlfiles.length) return;
	if(!color) var color='#91ce92';
	for( var i=0; i < this.hlfiles.length; i++) {
	    var f=this.hlfiles[i];
	    if($(this.rightId[f])) $(this.rightId[f]).style.backgroundColor=color;
	}
	this.hlfiles.clear();    
    },
    unhLite: function(id) {
	if(!$(id)) return;
	$(id).style.backgroundColor=this.bgColor;
	$(id).style.color=this.options.fColor;
    },
    hLite: function(id) {
	if(!$(id)) return;
	$(id).style.backgroundColor=this.options.sfBgColor;
	$(id).style.color=this.options.sfColor;
    },
    moveFiles: function(action) {
	this.clip.buffer.clear();
	this.clip.buffer=this.select.compact();
	this.clip.action=action;
    },
    getSelectExt: function () {
	if(!this.select.length) return '';
	var ext=this.fileExt(this.select[0]);
	if(!ext) return '';
       	for(var i=1; i < this.select.length; i++){
	      if(ext != this.fileExt(this.select[i])) return '';
	}
	return ext;
    },
    isAudio: function (ext) {
	if (this.audioExt.indexOf(ext.toLowerCase()) == -1) return false;	    
	return true;
    
    },
    isPlaying: function (ext) {
	if (this.playingAudio.indexOf(ext.toLowerCase()) == -1) return false;	    
	return true;
    
    },
    isCallToPhone: function (ext) {
	if (this.callToPhoneAudio.indexOf(ext.toLowerCase()) == -1) return false;	    
	return true;
    
    },
    isLooking: function (ext) {
	if (this.lookingFiles.indexOf(ext.toLowerCase()) == -1) return false;	    
	return true;
    
    },
    isEditing: function (ext) {
	if (this.editingFiles.indexOf(ext.toLowerCase()) == -1) return false;	    
	return true;
    
    },
    isVideo: function (ext) {
	if (this.videoExt.indexOf(ext.toLowerCase()) == -1) return false;	    
	return true;
    
    },
    selectAll: function () {
    	this.select=this.right.slice(0);
/*
	var a = $('fm_right').getElementsByTagName('a');

	for(var id,i=0; i < a.length; i++){
	    id=a[i].id;
	    if(id) {
	      	$(id).style.backgroundColor=this.options.sfBgColor;
		$(id).style.color=this.options.sfColor;
	    }
	}
*/	
	var ext = this.fileExt(this.right[0]);
	for (k in this.rightId) {
	    this.hLite(this.rightId[k]);
	    if(ext && (ext != this.fileExt(k))) ext ='';
	}
	
	/*
	for (var i=1; i <= this.right.length; i++) {
	
	    this.hLite(""+i+"");
	
	}
	*/
	this.selectExt = ext;

    },
    unSelectAll: function() {
	for (var i=0 ; i < this.select.length; i++) {
	    this.unhLite(this.rightId[this.select[i]]);
	}
	
	this.select.clear();
    },
    selectFile: function(file,event){
	//if(this.right.indexOf(file) == -1)  return;
	var shift=false;
	var is=this.isSelected(file);
	if(event) shift = (event=='1')? 1 : event.shiftKey;
	if(!shift) {
	    if((this.select.length < 2) || !is) {
		this.unSelectAll(); 
		this.select.push(file);
	    } 
	} 
	this.hLite(this.rightId[file]);
	if(shift){
	    if(is){
		this.select=this.select.without(file);
		this.unhLite(this.rightId[file]);
	    }else this.select.push(file);
	}
	this.selectExt=this.getSelectExt();
	return false;
    },
    inTree: function(dir) {
	if(!dir) return false;
	if($(this.leftId[dir])) return true;
	return false;
    },
    isDir: function(dir) {
	if(!dir) return false;
	if(this.tree[dir]) return true;
	return false;
    },
    getTree: function(dir) {
       var a = new Array();
       a[0]=dir; var i=1;
       while(!this.isTopDir(dir)) {
          dir=this.dirName(dir);
	  a[i++]=dir;
       }
       return a;
    
    },
    isFolderVisible: function(dir) {
    	if(!dir) return false;
	var a=this.getTree(dir); 
	a.shift();
	for (var i=0; i < a.length; i++) {
	    if($(this.leftId[a[i]]+'-div').style.display != 'none') return true;
	}
	return false;	
    },
    isOpen: function(dir) {
	if(!dir) return false;
	if(this.tree[dir]) return this.tree[dir].open;
	return false;
    },
    closeFolder: function(dir) {
	if(this.inTree(dir)) {
	    $(this.leftId[dir]+'-folder').src=this.imgdir+this.imgFolderClosed;
	    this.unhLite(this.leftId[dir]);
	}
    },
    openFolder: function(dir) {
    	if(this.inTree(dir)) {
	    $(this.leftId[dir]+'-folder').src=this.imgdir+this.imgFolderOpened;
	    this.hLite(this.leftId[dir]);
	}
    
    },
    changeFolder: function(from,to) {
	if(from != to) this.closeFolder(from);
    	this.openFolder(to);
    	this.folder = to;
    	this.select.clear();
	this.selectExt='';
    },
    createTree: function(dir) {
	this.tree[dir] = new Object();
	this.tree[dir].open = false;
    
    },
    openTree: function(dir,content) {
	this.tree[dir].open = true;
	var id=this.leftId[dir];
	if(content) Element.update($(id+'-div'),content);
	$(id+'-div').style.display='block';
	if($(id+'-div').innerHTML.length > 10) {
	    if($(id+'-treelink').innerHTML) $(id+'-tree').src=$(id+'-tree').src.replace(/(plus)/,'minus');
	    else $(id+'-treelink').innerHTML="<img id='"+id+"-tree' align='absmiddle' src='"+this.imgdir+"minus.gif'>&nbsp;";	        
	} else { $(id+'-treelink').innerHTML=''; $(id+'-div').style.display='none'; }
    },
    closeTree: function(dir) {
    	var id=this.leftId[dir];
	this.tree[dir].open = false;
	$(id+'-div').style.display='none';
	if($(id+'-div').innerHTML.length > 1) {
	    if($(id+'-treelink').innerHTML) $(id+'-tree').src=$(id+'-tree').src.replace(/(minus)/,'plus');
	    else $(id+'-treelink').innerHTML="<img id='"+id+"-tree' align='absmiddle' src='"+this.imgdir+"plus.gif'>&nbsp;";	        
	}else  $(id+'-treelink').innerHTML='';
    
    },
    getParString: function(par,str) {
	par.compact();
	for (var i=0, s=''; i < par.length; i++) {
	    s+='&'+str+'['+i+']='+encodeURIComponent(par[i]);
	}
	return s;
    },
    fileExt: function(file) {
	return file.substr(file.lastIndexOf('.')+1);
    },
    baseName: function(file) {
	return file.substr(file.lastIndexOf('/')+1);
    },
    dirName: function(file) {
	return file.substr(0,file.lastIndexOf('/'));
    },
    showMessage	: function (msg,hide,img) {
	    var s='';
	    if(!msg) return;
	    Element.show(this.msgContainer);
	    if(img) s="<img align='absmiddle' src='"+this.imgdir+img+"'>&nbsp;";
	    //s+=msg+'<br><br>';
	    s+=msg;
	    $(this.msgContainer).innerHTML=s;
	    this.errorMsg='';
	    if(hide) return;	    
	    var msgContainer = this.msgContainer;
	    setTimeout(function() { Effect.Fade(msgContainer,{duration: 0.2}) },3500);
    },
    hideSrv: function() {
	new Effect.Fade(this.srvContainer,{duration: 0.3});
	//new Effect.BlindUp(this.srvContainer,{duration: 0.2});
	
    },
    postRename: function(type,oldname,newname) {
    	if(type == 'left') {
	    var up = this.dirName(newname);
	    this.tree[up].open = false;
	    this.showTree(up,true);
	    //this.openFolder(up);

	}else {
	    this.hlfiles.push(newname);
	    this.showDir(this.folder);
	}
    
    },
    postDeleteDir: function() {
	this.leftId[this.folder]=this.tree[this.folder]=null;
	this.folder=this.dirName(this.folder);
	this.closeTree(this.folder);
	//alert(this.folder);
	this.showTree(this.folder,true);
	this.showDir(this.folder);
    },
    postDeleteFiles: function() {
        var f,d;
	for (var i=0; i < this.select.length ; i++ ){
	    f=this.select[i];
	    if(this.isDir(f)) {
		this.leftId[f]=this.tree[f]=null; d=true; 
	    }
	
	}
	if(d && this.isOpen(this.folder)) {
	    this.closeTree(this.folder);
	    this.showTree(this.folder,true);
	}
	this.showDir(this.folder);
    },
    postMkDir: function(up,newdir) {
	var updir=this.dirName(newdir);
//	if((this.folder == updir) && this.isOpen(updir)) {
	//if(this.folder == updir) {
	    this.closeTree(updir);
	    this.showTree(updir,true);
	//}
	this.hlfiles.push(newdir);
	//this.showDir(this.folder);
	this.showDir(updir);
	this.hideSrv();
    }
}

    
