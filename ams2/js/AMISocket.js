/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Copyright (C) 2006 Nickolay Shestakov All Rights Reserved. 
*/
function AMISocket(){
    var loaded = false;
    var flash = new Object;
    var jsName = 'AMISocket'+Math.round(Math.random()*1000000000);
    document[jsName]=this.valueOf();
    
    this.connect=function(url,port){
    	if(!loaded) {
	    if(flash.Name) flash=document[flash.Name];
	    try { 
	        flash.SetVariable("__jsObject","document." + jsName); 
	    } catch(e) {return 0;}
	    loaded=true;    
	}
	var res = flash.__connect(url,port);
	if (res) this.connected=true;
	return res;
    };
    this.close=function(){
	flash.__close();
    };
    this.send=function(data){
	return flash.__send(data);
    };
    this.init=function(){
	var swf="swf/AMISocket.swf";
	var name='ami' + Math.round(Math.random()*1000000000);
	var div = document.createElement('div');
	div.id='___amisocket_flash___';
	document.body.appendChild(div);	
	div.style.cssText="display: inline; position: absolute; top: 0px; left: 0px;";
	div.innerHTML+='<object id="'+name+'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '+
	'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ' +
	'width="0" height="0">'+
	'<param name="movie" value="'+swf+'"/>'+
	'<param name="allowScriptAccess" value="always"/> '+
	'<embed swliveconnect="true" name="'+name+'" src="'+swf+'" ' + 
	'width="0" height="0" allowScriptAccess="always" ' +
	'type="application/x-shockwave-flash" '+
	'pluginspage="http://www.macromedia.com/go/getflashplayer"/></object>';
	flash.Name=name;
    };

};
