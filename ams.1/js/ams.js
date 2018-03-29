function ObjectD () {
    ams.deleteObj.push(this);    
}

ams = {
    strReloadConfigFailed  : '',
    strReloadConfigSuccess : '',
    strHide		: '',
    strFieldMustNotBeEmpty : '',
    strHttpTimeout	: '',
    strLoadError	: '',
    fullScr		: 0,
    www_dir		: '',
    deleteObj		: new Array(),
    mRequest		: 0,
    onHttpTimeout	: false,
    global_history 	: new Array(),
    module_history 	: new Array(),
    module_cash 	: new Array,
    changetheme		: 0,
    changelang		: 0,
    modTop		: 0,
    modLeft		: 0,
    modal		: false,
    delObjects		: function() {
	 if (this.deleteObj.length) {
	    var i;
	    for ( i=0; i < this.deleteObj.length; i++) { 
		delete this.deleteObj[i];
	    }
	    this.deleteObj.clear();
	}
    },
    toggleScreen 	: function () {
	if (this.fullScr) {
	    $('_toggle_screen_').style.display="none";
	    $('frame-side-left').style.display="";
	    ams.frame_module.style.cssText=ams.frame_module.savecss;
	    this.fullScr=0;
	} else {
	    
	    $('_toggle_screen_').style.display="";
	    $('frame-side-left').style.display="none";
	    if(!ams.frame_module) { ams.frame_module=$('frame-module'); ams.frame_module.savecss=ams.frame_module.style.cssText; }
	    ams.frame_module.style.cssText="top: 0px; left: 0px; position: absolute;margin-left: 0px; margin-top: 0px; width: 100%;";
	    this.fullScr=1;

	}
    },
    reloadConfig	: function() {
	var url=ams.www_dir + '/modules/System/astreload.php';
	new Ajax.Request(url,{onComplete: function(t) {
			  var tmp=$('reload-div').innerHTML;
			  var bg=$('reload-div').style.backgroundColor; 
			  var str=ams.strReloadConfigFailed; 
			  var color="red";
			  if(t.responseText.indexOf("Success") != -1) {
			    	str=ams.strReloadConfigSuccess; 
				color="#64b743";
			  }
			  $('reload-div').innerHTML=str;
			  $('reload-div').style.backgroundColor=color; 
			    Effect.Pulsate('reload-div',{duration: 2});  
			    setTimeout( function(){
					$('reload-div').style.backgroundColor=bg;
					$('reload-div').innerHTML=tmp;
	    			    },3000);
			}
		      });

	},
	hideReloadLink	: function(){
	   $('reload-div').style.display='none';
	},
	showReloadLink	: function(){
	   $('reload-div').style.display='';
	},
	showMessage	: function (msg,container,hide,img,duration) {
	    var s='';
	    if(!msg) return;
	    if(!duration) duration=3500;
	    if(!container) container='module-note';
	    Element.show(container);
	    if(img) s="<img align='absmiddle' src='images/"+img+"'>&nbsp;";
	    if(hide) {
		s+=msg + "&nbsp;&nbsp;<a title='"+ams.strHide+"'  href='javascript:void(0)'>"+
		"<img onclick=\"$('"+container+"').hide()\" align='absmiddle' src='images/close2.gif'></a>";	
		$(container).innerHTML=s;
		return;
	    }	    
	    $(container).innerHTML=s+msg;
	    setTimeout(function() {
		if($(container)) {
		    //new Effect.BlindUp(container,{duration: 0.2})
		    new Effect.Fade(container,{duration: 0.2})
		}   
	    },duration);
	},
	toolTip	: function(id,event,msg,options) {
	    if(!msg || (!id && !event)) return;
	    if(!options) options={};
	    if(!options.duration) options.duration=3000;
	    var container = options.container ? $(options.container) : document.body;
	    if(id && (typeof id == 'string')) id=document.getElementById(id);
	    var div = document.createElement('div');
	    container.appendChild(div);
	    div.id="__ams_tooltip_div__" + Math.round(Math.random()*1000000000);
	    var css = "display: none; z-index: 1000; overflow: hidden; background-color: #eeeecc; border: 1px solid #34548f; width: 200px;font-size: 11px; font-family: verdana,arial; position: absolute;"
	    if(options.style) css+=options.style;
	    div.style.cssText=css;
	    if(!options.close) div.style.padding='5px';
	    if(options.width) div.style.width=options.width;
	    if(options.color) div.style.color=options.color;
	    if(options.bgcolor) div.style.backgroundColor=options.bgcolor;
	    if(id) {
		var offsetTop = options.offsetTop ? options.offsetTop : id.offsetHeight ? id.offsetHeight : 15;
		var offsetLeft= options.offsetLeft ? options.offsetLeft : 0;
		Position.clone(id,div,{setWidth: false, setHeight: false, offsetTop: offsetTop, offsetLeft: offsetLeft});
	    }else { 	div.style.top=Event.pointerY(event)+'px';	
			div.style.left=Event.pointerX(event)+'px'; 
	    }
	    if(options.close) {
		var w=div.getWidth()-1;
		var s = "<table cellpadding=0 cellspacing=0 style='width: "+w+"px;color: black; background-color: #b0c2e7; margin-bottom: 5px;'><tr><td align='center'>";
		if(options.title) s+=options.title; s+="&nbsp;</td><td align='right' valign='top' width='15'>";
		s+="<a href='javascript:void(0)'><img onclick=\"Element.remove('"+div.id+"');";
		if(isIE) s+="Element.remove('"+div.id+"_iefix');";
		s+="\" align='top' src='images/close3.gif'></a></td></tr></table>";
		msg = s + msg;
	    }
	    if(options.img) {
		var src = (options.img.indexOf('/') !== -1) ? options.img : 'images/'+options.img;
		msg = "<img align='absmiddle' src='"+src+"'>&nbsp;" + msg;
	    }
	    div.innerHTML=msg;
	    Element.show(div);
	    if(isIE) {
	    	    new Insertion.After(div, 
    			'<iframe id="' + div.id + '_iefix" '+
    			'style="display:none;position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);" ' +
    			'src="javascript:false;" frameborder="0" scrolling="no"></iframe>');
		    var iefix=document.getElementById(div.id+'_iefix');
		    Position.clone(div, iefix, {setTop:(!div.style.height)});
		    iefix.style.zIndex = 999;
		    Element.show(iefix);
	    
	    }
	    if(options.close) { ams.hideToolTip(); ams.tooltipDiv = div.id; ams.tooltip_iefix=div.id+'_iefix'; }
	    if(options.hlight || !options.close) {
		setTimeout(function() {
		    if(!options.close) {
			container.removeChild(div);
			if(iefix) container.removeChild(iefix);

		    }
		    if(id) { id.disabled=false; id.focus(); }
		},options.duration);
	    }
	    if(id && options.hlight) {
		var hcolor = options.hcolor ? options.hcolor : '#f6281d';
	    	id.disabled=true;
	    	new Effect.Highlight(id,{startcolor: hcolor});
	    
	    }
	    return div;
	},
	hideToolTip	: function() {
		if($(ams.tooltipDiv)) Element.remove(ams.tooltipDiv);
		if($(ams.tooltip_iefix)) Element.remove(ams.tooltip_iefix);
	   ams.tooltipDiv=ams.tooltip_iefix=null;
	},
	checkInput	: function (id,type,msg,options) {
		if(!id) return;
		if(!options) options = {};
		id=$(id);
		var s=id.value;
		if(type == "integer" && isInteger(s)) return true;
		else if(type == "float" && isFloat(s)) return true;
		else if(type == "empty" && s.length) return true;
		else if(type == "email" && isValidEmail(s)) return true;
		else if(type == "symbols") {
		    if(!options.symbols || !options.symbols.length) return true;
		    for(var i=0; i < options.symbols.length; i++) {
			if(s.indexOf(options.symbols[i]) !== -1) break;
			
		    }
		    if(i == options.symbols.length) return true;
		
		}
		if(!msg) msg=' ';
		if(!options.img) options.img='warning2.gif';
		if(options.hlight == undefined) options.hlight=true;
		//IE bugfix
		if(isIE) id.scrollLeft=id.scrollTop=0;
		this.toolTip(id,0,msg,options);
		return false;

	},
	checkInputs	: function(req,filters) {
		for(var i=0; i < req.length; i++) {
		    if(!this.checkInput(req[i],'empty',ams.strFieldMustNotBeEmpty)) return false;
		}
		for(i=0; i < filters.length; i++) {
		    var type=filters[i]['type'];
		    var options = {};
		    if(type == "symbols") options.symbols = filters[i]['options'];
		    if(!this.checkInput(filters[i]['id'],type,filters[i]['msg'],options)) return false;
		}
		return true;
	},
	hideSelect	: function() {
		var a=$('frame-module-content').getElementsByTagName('select');
		for (var i=0; i < a.length; i++) a[i].style.visibility='hidden';
	},
	showSelect	: function() {
		var a=$('frame-module-content').getElementsByTagName('select');
		for (var i=0; i < a.length; i++) a[i].style.visibility='';	
	},
	setModal	: function() {
		$('__modal-div__').style.height=(2*screen.availHeight)+'px';
		$('__modal-div__').show();
		if(isIE) this.hideSelect();
		document.documentElement.style.overflow='hidden';
		ams.modal = true;
		
	},
	cancelModal	: function() {
		$('__modal-div__').hide();
		$('__modal-box__').hide();
		document.documentElement.style.overflow='';
		if(isIE) this.showSelect();
		ams.modal = false;
	},
	setLoading	: function() {
		$('__loading-div__').show();
		$('frame-module-content').setOpacity(0.4);
		if(isIE) this.hideSelect();
		ams.loading = true;
		
	},
	cancelLoading	: function() {
		$('__loading-div__').hide();
		$('frame-module-content').setOpacity(1.0);
		if(isIE) this.showSelect();
		ams.loading = false;
	}
	
}

ams.callInProgress = function (transport) {
    switch (transport.readyState) {
	case 1: case 2: case 3:
	    return true; break;
	    //case 4 & 0
	default: return false; break;
    }
}
ams.printStatusMessage = function (m,bgColor,effect) {
    if(!ams.bgStatus) ams.bgStatus=$('status-message').style.backgroundColor;
    $('status-message').innerHTML=m; 
    $('status-message').style.backgroundColor = bgColor ? bgColor : ams.bgStatus;
    $('status-message').show();
    if(!effect) {
	new Effect.Pulsate('status-message',{duration: 2});  
	setTimeout("$('status-message').hide()",5000);
    }
}

ams.globalHandler = {
    onCreate: function (request) {
	$('status-message').hide();
	request.loadingInProgress=setTimeout(
	        function() { ams.setLoading(); },1000);
	request.timeoutId=setTimeout(
		function() {
		        if(ams.callInProgress(request.transport)){
			  ams.onHttpTimeout=1;
			  request.transport.abort();	
			}
		},ams.httpTimeout);	

    },
    onComplete: function (request,t) {
	clearTimeout(request.timeoutId);
	clearTimeout(request.loadingInProgress);
	if(ams.loading) ams.cancelLoading(); 
	if(ams.onHttpTimeout) ams.printStatusMessage(ams.strHttpTimeout,'red');
	else {
	    if(!t.responseText) ams.printStatusMessage(ams.strLoadError,'red');
	    else $('status-message').hide();
	}
	ams.onHttpTimeout=0;
    }
}

loadModuleMenu=function(m,a) {
  var tmp;
  menu_custom=0;
  var url=ams.www_dir+'/menu.php';
  var pb ="menu_module=" + m + "&action=" +a;
  new Ajax.Request(url,  { postBody: pb, 
	onComplete: function(t) {  if(t.responseText) 	$('ams-menu').update(t.responseText); }
	});

}

loadModuleContent=function(form,cm,a,p,cash) {

  var url=ams.www_dir+'/module.php';
  var f='';
  if(form) f='&' + Form.serialize(form);
  var pb ="&current_module=" + cm + "&action=" + a + f;
  if(p) pb+='&' + p;

  ams.hideReloadLink();
  new  Ajax.Request(url, { postBody: pb, 
		  onLoading: function(request) {
		    if(ams.mRequest) ams.mRequest.transport.abort();
		    ams.mRequest=request;	  
		  },
	    	  onComplete:  function(t) {
			    ams.mRequest=0;
			    if(t.responseText) {
			    	ams.delObjects();
				$('_action_time_').innerHTML='';
			        Element.update('ams-module',t.responseText);

			    }
			}
		});

}

loadModule=function(form,mm,cm,a,par,cash) {

  var p='';
  if(!cm) cm=current_module;
  if(!mm) mm=menu_module;
  if(!a) a=action;
  if(form) {
	if (form==1) form='module_form';
  } else form='';
  current_module=cm; action=a;
  if(par) p=par.toQueryString();
  if(!cash) cash=0;
  //alert(mm+' '+cm+' '+a +' '+ menu_module);
  if(mm == menu_module) { loadModuleContent(form,cm,a,p,cash);  return;   }
  //if((typeof(runJScriptMenu) == "function") && !ams.changetheme && !ams.changelang) {
  if(typeof(runJScriptMenu) == "function") {
	runJScriptMenu(form,mm,cm,a,p,cash);
	return;
  }
  loadModuleMenu(mm,a);
  menu_module=mm;
  loadModuleContent(form,cm,a,p,cash);
  //ams.changelang=ams.changetheme=0;
}


