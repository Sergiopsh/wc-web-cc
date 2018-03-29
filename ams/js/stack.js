/*-------------------------------------------------------------------------
 *  Copyright (c) 2007 Nickolay Shestakov
 *
 *  This software is freely distributable under the terms of an 
 *  GNU General Public License version 2.
 *
 *-------------------------------------------------------------------------*/
_Stack = Class.create();

_Stack.prototype = {
    initialize : function(obj,target,options) {
	if(obj) {
	    this.history = obj;
	    this.index = obj.length;
	    this.current = null;

	}else {
	    this.history = new Array();
	    this.index = 0;
	    this.current = null;
	    this.obj = 0;
	}
	this.target = target;
	this.backFlag=this.forwardFlag=0;
	this.setOptions(options);
	
    },
    setOptions: function(options) {
        this.options = {
	    fImgId	: '',
	    bImgId	: '',
	    fImgOff	: '',
	    fImgOn	: '',
	    bImgOff	: '',
	    bImgOn	: '',
	    depth	: 50
	}
	Object.extend(this.options, options || {});    
	this.images = (this.options.fImgId && this.options.bImgId) ? true : false ;
    
    },
    update : 	function() {
        this.backFlag = (this.index && this.history.length) ? 1:0;
	this.forwardFlag = (this.index==this.history.length) ? 0:1;
	if(!this.images) return;
	$(this.options.fImgId).src = (this.index==this.history.length) ? this.options.fImgOff:this.options.fImgOn;
	$(this.options.bImgId).src = (this.index && this.history.length) ? this.options.bImgOn:this.options.bImgOff;

    },
    store: function(value) {
	if(this.history.length < this.options.depth) {
	    this.index=this.history.push(value);
	}else{
	    this.index=this.options.depth;
	    this.history.push(value);
	    this.history.shift();
	}
	this.backFlag=1; this.forwardFlag=0;
	if(!this.images) return;
	$(this.options.bImgId).src=this.options.bImgOn;
	$(this.options.fImgId).src=this.options.fImgOff;

    },
    setTarget : function(val) {
	for( var i=0; i < this.target.length ; i++) {
	//	try { $(this.target[i]).value=val[i];    } catch(e) {};
		if($(this.target[i]).value == undefined)	
			$(this.target[i]).innerHTML=val[i];
		else 	$(this.target[i]).value=val[i];
	}    
    
    },
    getTarget : function() {
	var a = new Array();
	for( var i=0; i < this.target.length ; i++) {
	      if($(this.target[i]).value == undefined)	
	    		a[i]=$(this.target[i]).innerHTML;
	      else a[i]=$(this.target[i]).value;
	}    
	return a;
    },
    back: function() {
	if(this.index == 0) {
	    if(this.history[0]) this.setTarget(this.history[0]);
	    this.update();
	    return;
	}
	if(this.index==this.history.length) this.current=this.getTarget();
	this.index--;
	this.setTarget(this.history[this.index]);
	this.update();
    },
    forward: function(){
	if(this.index == this.history.length) return;
        if(this.index == (this.history.length-1)){
	    this.setTarget(this.current);
	    this.index++;
	    this.update();
    	    return;
	}
	this.index++;
	this.setTarget(this.history[this.index]);
	this.update();
    },
    clear: function() {
        this.history.clear();
	this.backFlag=this.forwardFlag=0;
	this.index=0; this.current = '';
	if(!this.images) return;
	$(this.options.bImgId).src=this.options.bImgOff;
	$(this.options.fImgId).src=this.options.fImgOff;
    }

};
