/*
 * Asterisk -- An open source telephony toolkit.
 *
 * Javascript routines or accessing manager routines over HTTP.
 *
 * Copyright (C) 1999 - 2006, Digium, Inc.
 *
 * Mark Spencer <markster@digium.com>
 *
 * See http://www.asterisk.org for more information about
 * the Asterisk project. Please do not directly contact
 * any of the maintainers of this project for assistance;
 * the project provides a web site, mailing lists and IRC
 * channels for your use.
 *
 * This program is free software, distributed under the terms of
 * the GNU General Public License Version 2. See the LICENSE file
 * at the top of the source tree.
 *
 * modified by Nickolay Shestakov <ns@ampex.ru>
 */


function Astman() {
	var me = this;
	var channels = new Array;
	var lastselect;
	var selecttarget;
	var selecttargetclass;
	this.f_chan = "";
	this.f_state = "";
	this.f_callerid = "";
	this.e_userfield = "";
	this.e_userfield_name = "";
	this.e_event = "";
	this.ev_monitor = 0;	
	this.ev_format = "format";
	this.setURL = function(url) {
		this.url = url;
	};
	this.setEventCallback = function(callback) {
		this.eventcallback = callback;
	};
	this.setDebug = function(debug) {
		this.debug = debug;
	};
	this.clickChannel = function(ev,id) {

		if(!$(id)) return;
		if (me.selecttarget)
			me.restoreTarget(me.selecttarget);
		me.selecttarget = id;
		$(id).className = "chanlistselected";
		me.chancallback(id);
	};
    this.getChannels = function() {
		return channels;
	
	}
	this.restoreTarget = function(targetname) {
		var other;
		target = $(targetname);
		if (!target) return;
		if (target.previousSibling) {
			other = target.previousSibling.className;
		} 
		if (other) {
			if (other == "chanlisteven") 
				target.className = "chanlistodd";
			else
				target.className = "chanlisteven";
		} else
				target.className = "chanlistodd";
	};
	
	this.channelUpdate = function(msg, channame) {
		
		var fields = new Array("callerid", "calleridnum","calleridname", "context", "extension", "priority", "account", "state", "link", "uniqueid","channelstatedesc","seconds");

		if (!channame || !channame.length)
			channame = msg.headers['channel'];
		if (!channels[channame])
			channels[channame] = new Array();
			
		if (msg.headers.event) {
			var ignore = new Array("Cdr","LogChannel","ChannelReload","Reload");
			var ev = msg.headers.event;
			if (ev == "Hangup") {
				delete channels[channame];
			} else if (ev == "Link") {
				var chan1 = msg.headers.channel1;
				var chan2 = msg.headers.channel2;
				if (chan1 && channels[chan1])
					channels[chan1].link = chan2;
				if (chan2 && channels[chan2])
					channels[chan2].link = chan1;
			} else if (ev == "Unlink") {
				var chan1 = msg.headers.channel1;
				var chan2 = msg.headers.channel2;
				if (chan1 && channels[chan1])
					delete channels[chan1].link;
				if (chan2 && channels[chan2])
					delete channels[chan2].link;
			} else if (ev == "Rename") {
				var oldname = msg.headers.oldname;
				var newname = msg.headers.newname;
				if (oldname && channels[oldname]) {
					channels[newname] = channels[oldname];
					delete channels[oldname];
				}
			} else {
				if(ignore.indexOf(ev) == -1) {
					channels[channame]['channel'] = channame;
					for (x=0;x<fields.length;x++) {
						if (msg.headers[fields[x]])
							channels[channame][fields[x]] = msg.headers[fields[x]];
					}
				} else delete channels[channame];
			}
		} else {
			channels[channame]['channel'] = channame;
			for (x=0;x<fields.length;x++) {
				if (msg.headers[fields[x]]){
					channels[channame][fields[x]] = msg.headers[fields[x]];
				}
			}
		}
		
	};
	this.channelClear = function() {
		channels = new Array;
	}
	this.channelInfo = function(channame) {
		return channels[channame];
	};
	this.channelTable = function(callback) {
		var s, x;
		var cclass, count=0;
		var found = 0;
		var foundactive = 0;
		me.chancallback = callback;
		s = "<table class='chantable' align='center' width='100%'>";
		s = s + "<tr class='labels' id='labels'><td>Channel</td><td>State</td><td>Caller</td><td>Location</td><td>Link</td></tr>";
		count=0;
		for (x in channels) {
			if (channels[x].channel) {
				var chan= new String(channels[x].channel);
				if(this.f_chan.length && (chan.toLowerCase().indexOf(this.f_chan) !== 0)) continue;
				
				var state = (channels[x].state == undefined) ? new String(channels[x].channelstatedesc) : new String(channels[x].state);
							
				if(this.f_state.length && (state.toLowerCase().indexOf(this.f_state) !== 0)) continue;
				var ext=channels[x].extension;
				var link=channels[x].link;
				var callerid = (channels[x].callerid == undefined) ? new String(channels[x].calleridnum) : new String(channels[x].callerid);
				if(this.f_callerid.length && (callerid.toLowerCase().indexOf(this.f_callerid) !== 0)) continue;
				var calleridname=channels[x].calleridname;
			
				if (count % 2)
					cclass = "chanlistodd";
				else
					cclass = "chanlisteven";
				if (me.selecttarget && (me.selecttarget == x)) {
					cclass = "chanlistselected";
					foundactive = 1;
				}
				count++;
				s = s + "<tr class='" + cclass + "' id='" + chan + "' onClick='astmanEngine.clickChannel(event,this.id)'>";
				s = s + "<td>" + chan + "</td>";
				if (state != undefined && state.length) 
					s = s + "<td>" + state + "</td>";
				else
					s = s + "<td><i class='light'>unknown</i></td>";
				if (calleridname && callerid && calleridname != "<unknown>") {
					cid = calleridname.escapeHTML() + " &lt;" + callerid.escapeHTML() + "&gt;";
				} else if (calleridname && (calleridname != "<unknown>")) {
					cid = calleridname.escapeHTML();
				} else if (callerid) {
					cid = callerid.escapeHTML();
				} else {
					cid = "<i class='light'>Unknown</i>";
				}
				s = s + "<td>" + cid + "</td>";
				if (ext) {
					s = s + "<td>" + ext + "@" + channels[x].context + ":" + channels[x].priority + "</td>";
				} else {
					s = s + "<td><i class='light'>None</i></td>";
				}
				if (link) {
					s = s + "<td>" + link + "</td>";
				} else {
					s = s + "<td><i class='light'>None</i></td>";
				}
				s = s + "</tr>\n";
				found++;
			}
		}
		if (!found)
			s += "<tr><td colspan='10'><i class='light'>No active channels</i></td>\n";
		s += "</table>\n";
		if (!foundactive) {
			me.selecttarget = null;
		}
		return s;
	};
	this.formatEvent = function(msg) {
		
		if (!msg.headers.event) return "";
		var ignore = new Array("WaitEventComplete");
		var ev = msg.headers.event;
		if(this.e_event.length && (ev.toLowerCase().indexOf(this.e_event) !== 0)) return "";
		if(ignore.indexOf(ev) != -1) return "";
		var fname = this.e_userfield_name;
		var m = msg.headers;
		if(this.e_userfield.length && ((m[fname] == undefined) || m[fname].toLowerCase().indexOf(this.e_userfield) !== 0)) 	
			return  "";
		var s;
		if(this.ev_format == "raw") {//raw format
			var x,name;
			s="<table width='100%' cellspacing='0' cellpadding='0' class='event-tbl'><tr><td colspan=3><b>&nbsp;Event: "+
			ev+"</b></td></tr>";
			for(x=0;x<msg.names.length;x++) {
				name = msg.names[x];
				if(name == "event") continue;
				s+="<tr><td width='20'>&nbsp;</td><td width='20%'>"+name+"</td><td>:&nbsp;"+msg.headers[name]+"</td></tr>";
			}
			return s+"</table><hr align='left' noshade size=1 style='width: 400px; margin: 0;color: #333333;'>";
		}
		
		var chan="<span class='evchanname'>"+msg.headers.channel+"</span>";
		var evl = ev.toLowerCase();
		s= "<div class='evrow'>";
		
		if(m.application) {
			return s+"&nbsp;&nbsp;&nbsp;&nbsp; -- <i>Executing</i> [<span class='evcontext'>"+m.extension+"@"+m.context+":"+m.priority+"</span>] <b>"+
			m.application+"</b>(\""+chan+"\", \"<span class='evdata'>"+m.appdata+"</span>\")</div>";
		
		}
		s+="&nbsp;&nbsp;- ";
		switch (evl) {
			case "hangup":  var cause = (m['cause-txt'] == undefined) ? m.cause : m['cause-txt'];
							s += "Hangup '"+chan+"', cause - "+cause;	break;
			case "varset":  s += "Variable on channel '"+chan+"' "+m.variable+" = <span class='evdata'>"+m.value+"</span>"; break;
			case "newstate":  var state = (m.state == undefined) ? m.channelstatedesc : m.state;
								s+= chan + " is "+ state;
								break;
			case "newchannel": s+= "New channel '"+chan; break;
			case "channelupdate": s="";break;
			case "newcallerid":  var cid = (m.calleridname.length) ? "<"+m.calleridname+">" : "";
								   cid+=m.calleridnum;
								   s+="New callerid on channel '"+chan+"' is "+cid; break;
			case "newaccountcode": s+="AccountCode on channel '"+chan+"' is "+m.accountcode+", old AccountCode = "+m.oldaccountcode; break;
			case "peerstatus": s+="Peer "+m.peer+" is "+m.peerstatus; break;
			case "dial": var status = (m.dialstatus == undefined) ? "": m.dialstatus;
						 var dest = (m.destination == undefined) ? "": m.destination;
						 s+="Dialing '"+chan;
						 if(dest.length) s+="' --> '<span class=\"evdata\">"+dest+"</span>'"; 
						 if(status.length) s+=", DIALSTATUS = <span class=\"evdata\">"+status+"</span>"; break;
			case "cdr": s="";break;
			case "musiconhold": s+="Music on hold "+m.state+" on channel '"+chan+"'"; break;
			case "join": s+="Channel '"+chan+"' joined to queue '"+m.queue+"', position - "+m.position; break;
			case "leave": s+="Channel '"+chan+"' leaved queue '"+m.queue; break;
			case "link": s+="Channel \"<span class='channame'>"+m.channel1+"</span> linked\" to channel \"<span class='evdata'>"+m.channel2+"</span>\""; break;
			case "meetmejoin": s+="Channel '"+chan+"' joined to conference '"+m.meetme+"', number - "+m.usernum; break;
			case "meetmeleave": s+="Channel '"+chan+"' leaved conference '"+m.meetme; break;
			case "meetmestoptalking": s+="Channel '"+chan+"' stop talking at conference '"+m.meetme; break;
			case "meetmetalking": s+="Channel '"+chan+"' start talking at conference '"+m.meetme; break;
			case "unlink": s+="Channel \"<span class='channame'>"+m.channel1+"</span>\" unlinked from channel \"<span class='evdata'>"+m.channel2+"</span>\""; break;
			case "parkedcallscomplete": s="";break;
			case "bridge": s+="Channel \"<span class='channame'>"+m.channel1+"</span>\"bridged to channel \"<span class='evdata'>"+m.channel2+"</span>\""; break;
			case "rtcpsent": s+= "RTCP sent "+m.sentpackets + "packets to "+m.to+", dlsr = "+m.dlsr; break;
			case "agentcallbacklogin": s+="Agent "+m.agent+" callback login, channel - <span class='evchanname'>"+m.loginchan+"</span>"; break;
			case "agentcallbacklogoff": s+="Agent "+m.agent+" callback logoff, channel - <span class='evchanname'>"+m.loginchan+"</span>, login time - "+m.logintime+", reason - "+m.reason; break;
			case "agentcalled": s+="Channel \"<span class='evchanname'>"+m.channelcalling+"</span>\" calling to agent ["+m.extension+"@"+m.context+":"+m.priority+"] \""+m.agentcalled+"\"";break; 
			case "agentcomplete": s+="Agent "+m.member+", "+m.membername+" completed at queue "+m.queue+", channel - "+chan+", talk time - "+m.talktime+", hold time - "+m.holdtime+", reason - "+m.reason; break;
			case "agentconnect": s+="Agent "+m.member+", "+m.membername+" connected to queue "+m.queue+", channel - "+chan; break;
			case "agentlogin": s+="Agent "+m.agent+" login, channel - "+chan; break;
			case "agentlogoff": s+="Agent "+m.agent+" logoff, login time - "+m.logintime; break;
			case "queuememberadded": s+="Memeber '"+m.location+"' added to queue "+m.queue+", membership - "+m.membership+
									 ", penalty - "+m.penalty+", call taken - "+m.callstaken+", last call - "+m.lastcall+", status - "+m.status; break;
			case "rtpsenderstat": 
			case "rtpreceiverstat": 
			default: s = "";
		}
		if(s.length) return s+"</div>";
		return "";
	};
	this.parseResponse = function(t, callback) {
		
		var msgs = new Array();
		var inmsg = 0;
		var msgnum = 0;
		var x,y;
		
		var s = t.responseText;
		var allheaders = s.split('\r\n');
		
		for (x=0;x<allheaders.length;x++) {
			if (allheaders[x].length) {
				var fields = allheaders[x].split(': ');
				if (!inmsg) {
					msgs[msgnum] = new Object();
					msgs[msgnum].headers = new Array();
					msgs[msgnum].names = new Array();
					y=0;
				}
				msgs[msgnum].headers[fields[0].toLowerCase()] = fields[1];
				msgs[msgnum].names[y++] = fields[0].toLowerCase();
				inmsg=1;
				
			} else {
				if (inmsg) {
					inmsg = 0;
					msgnum++;
				}
			}
		}
		callback(msgs);
	};
	this.managerResponse = function(t) {
		me.parseResponse(t, me.callback);
	};
	this.doEvents = function(msgs) {
		me.eventcallback(msgs);
	};
	this.eventResponse = function(t) {
		me.parseResponse(t, me.doEvents);
	};
	this.sendRequest = function(request, callback) {
		var tmp;
		var opt = {
			method: 'get',
			asynchronous: true,
			onSuccess: this.managerResponse,
			onFailure: function(t) {
				$('statusbar').innerHTML="Error: " + t.status + ": " + t.statusText;
			}
		};
		me.callback = callback;
		opt.parameters = request;
		tmp = new Ajax.Request(this.url, opt);
	};
	
	this.pollEvents = function() {
		var tmp;
		var opt = {
			method: 'get',
			asynchronous: true,
			onSuccess: this.eventResponse,
			onFailure: function(t) {
				$('statusbar').innerHTML="Error: " + t.status + ": " + t.statusText;
			}
		};
		opt.parameters="action=waitevent";
		tmp = new Ajax.Request(this.url, opt);
	};
	
};

astmanEngine = new Astman();
