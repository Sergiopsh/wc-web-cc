var reconnectTimer=null;
var reconnectCount=0;
var amiSocket= new AMISocket();
var amiConnected=0;
amiSocket.onConnect=function(val) {
    if(val) {
	$('status-connection').innerHTML="<font color=green>"+strAstConnected+"</font>";
	if(reconnectTimer) clearTimeout(reconnectTimer);
	reconnectCount=0;amiConnected=1;
    }
    else {
	reconnectCount++; amiConnected=0;
    	$('status-connection').innerHTML="<font color=red>"+strAstNotConnected+"</font>";    
	if(!reconnectTimer) {
	    t_interval=2000 * reconnectCount;
	    if(t_interval >= 20000) {t_interval=20000; reconnectCount=10;}
	    reconnectTimer=setTimeout(connectServer,t_interval);
	}
    }
}
amiSocket.onData = function(t) {
    //alert(typeof(SystemParseData));
    if(typeof(SystemParseData)=="function") SystemParseData(t);
}
amiSocket.onClose = function() {
    $('status-connection').innerHTML=strConnectionClosed;
    reconnectTimer=setTimeout(connectServer,2000);
}

connectServer=function() {
    amiSocket.connect(ami_ip,ami_port);
    reconnectTimer=null;
}
initConnection=function() {
    //amiSocket.init("xml-socket-div");
    amiSocket.init();
    $('status-connection').innerHTML=strInitConnection;
    setTimeout(connectServer,1000);
}


function AstMan() {
    var crlf="\r\n";

    this.sendCommand=function(command) {
	var s="";
	s =crlf+'Action: Command'+ name + crlf;
	s +='Command: '+command + crlf+crlf;
	//alert(s);
	amiSocket.send(s);
    }
    this.sendAction=function(name,key,variable) {
	var s="";
	s =crlf+'Action: '+ name + crlf;
	if(key) {
    	    for(var i=0; i<key.length;i++) {
		s+=key[i][0]+': '+key[i][1]+crlf;
    	    }
	}
	if(variable) {
    	    for(var i=0; i<variable.length;i++) {
		s+=variable[i][0]+': '+variable[i][1]+crlf;
    	    }
	}
	s+='ActionID: '+Math.round + crlf+crlf;
	//alert(s);
	amiSocket.send(s);
    
    }
}
astMan = new AstMan();

