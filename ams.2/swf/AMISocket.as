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
 *
 * Compiled with mtasc 1.12 
*/

import flash.external.ExternalInterface;

class AMISocket {
    
    static var app:AMISocket;
    var __xmls:XMLSocket;

    function AMISocket() {
	this.__xmls = new XMLSocket();
	ExternalInterface.addCallback("__connect", this, __connect);
	ExternalInterface.addCallback("__close", this, __close);
	ExternalInterface.addCallback("__send", this, __send);
        this.__xmls.onConnect = this.onConnect;
        this.__xmls.onClose = this.onClose;
	this.__xmls.onData = this.onData;
    }
    function onConnect(success) {
	ExternalInterface.call(_root.__jsObject + ".onConnect",success);
    }
    function onClose() {
	ExternalInterface.call(_root.__jsObject + ".onClose","");
    }
    function onData(data){
    	ExternalInterface.call(_root.__jsObject + ".onData",data);
    }
    function __connect(url:String,port:Number):Boolean {
	return this.__xmls.connect(url,port);
    }
    function __close():Boolean {
	return this.__xmls.close();
    }
    function __send(data:Object):Boolean {
	return this.__xmls.send(data);
    }
    static function main(mc) {
	app = new AMISocket ();
    }
}
