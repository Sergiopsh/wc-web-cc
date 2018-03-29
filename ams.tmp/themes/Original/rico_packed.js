Rico={};
Rico.Accordion=Class.create();
Rico.Accordion.prototype={initialize:function(_1,_2){
this.container=$(_1);
this.lastExpandedTab=null;
this.accordionTabs=new Array();
this.setOptions(_2);
this._attachBehaviors();
if(!_1){
return;
}
this.container.style.borderBottom="1px solid "+this.options.borderColor;
if(this.options.onLoadShowTab>=this.accordionTabs.length){
this.options.onLoadShowTab=0;
}
for(var i=0;i<this.accordionTabs.length;i++){
if(i!=this.options.onLoadShowTab){
this.accordionTabs[i].collapse();
this.accordionTabs[i].content.style.display="none";
}
}
this.lastExpandedTab=this.accordionTabs[this.options.onLoadShowTab];
if(this.options.panelHeight=="auto"){
var _4=(this.options.onloadShowTab===0)?1:0;
var _5=parseInt(RicoUtil.getElementsComputedStyle(this.accordionTabs[_4].titleBar,"height"));
if(isNaN(_5)){
_5=this.accordionTabs[_4].titleBar.offsetHeight;
}
var _6=this.accordionTabs.length*_5;
var _7=parseInt(RicoUtil.getElementsComputedStyle(this.container.parentNode,"height"));
if(isNaN(_7)){
_7=this.container.parentNode.offsetHeight;
}
this.options.panelHeight=_7-_6-2;
}
this.lastExpandedTab.content.style.height=this.options.panelHeight+"px";
this.lastExpandedTab.showExpanded();
this.lastExpandedTab.titleBar.style.fontWeight=this.options.expandedFontWeight;
},setOptions:function(_8){
this.options={expandedBg:"#63699c",hoverBg:"#63699c",collapsedBg:"#6b79a5",expandedTextColor:"#ffffff",expandedFontWeight:"bold",hoverTextColor:"#ffffff",collapsedTextColor:"#ced7ef",collapsedFontWeight:"normal",hoverTextColor:"#ffffff",borderColor:"#1f669b",panelHeight:200,onHideTab:null,onShowTab:null,onLoadShowTab:0};
Object.extend(this.options,_8||{});
},showTabByIndex:function(_9,_a){
var _b=arguments.length==1?true:_a;
this.showTab(this.accordionTabs[_9],_b);
},showTab:function(_c,_d){
if(this.lastExpandedTab==_c){
return;
}
var _e=arguments.length==1?true:_d;
if(this.options.onHideTab){
this.options.onHideTab(this.lastExpandedTab);
}
this.lastExpandedTab.showCollapsed();
var _f=this;
var _10=this.lastExpandedTab;
this.lastExpandedTab.content.style.height=(this.options.panelHeight-1)+"px";
_c.content.style.display="";
_c.titleBar.style.fontWeight=this.options.expandedFontWeight;
if(_e){
new Rico.Effect.AccordionSize(this.lastExpandedTab.content,_c.content,1,this.options.panelHeight,100,10,{complete:function(){
_f.showTabDone(_10);
}});
this.lastExpandedTab=_c;
}else{
this.lastExpandedTab.content.style.height="1px";
_c.content.style.height=this.options.panelHeight+"px";
this.lastExpandedTab=_c;
this.showTabDone(_10);
}
},showTabDone:function(_11){
_11.content.style.display="none";
this.lastExpandedTab.showExpanded();
if(this.options.onShowTab){
this.options.onShowTab(this.lastExpandedTab);
}
},_attachBehaviors:function(){
var _12=this._getDirectChildrenByTag(this.container,"DIV");
for(var i=0;i<_12.length;i++){
var _14=this._getDirectChildrenByTag(_12[i],"DIV");
if(_14.length!=2){
continue;
}
var _15=_14[0];
var _16=_14[1];
this.accordionTabs.push(new Rico.Accordion.Tab(this,_15,_16));
}
},_getDirectChildrenByTag:function(e,_18){
var _19=new Array();
var _1a=e.childNodes;
for(var i=0;i<_1a.length;i++){
if(_1a[i]&&_1a[i].tagName&&_1a[i].tagName==_18){
_19.push(_1a[i]);
}
}
return _19;
}};
Rico.Accordion.Tab=Class.create();
Rico.Accordion.Tab.prototype={initialize:function(_1c,_1d,_1e){
this.accordion=_1c;
this.titleBar=_1d;
this.content=_1e;
this._attachBehaviors();
},collapse:function(){
this.showCollapsed();
this.content.style.height="1px";
},showCollapsed:function(){
this.expanded=false;
this.titleBar.style.backgroundColor=this.accordion.options.collapsedBg;
this.titleBar.style.color=this.accordion.options.collapsedTextColor;
this.titleBar.style.fontWeight=this.accordion.options.collapsedFontWeight;
this.content.style.overflow="hidden";
},showExpanded:function(){
this.expanded=true;
this.titleBar.style.backgroundColor=this.accordion.options.expandedBg;
this.titleBar.style.color=this.accordion.options.expandedTextColor;
this.content.style.overflow="auto";
},titleBarClicked:function(e){
if(this.accordion.lastExpandedTab==this){
return;
}
this.accordion.showTab(this);
},hover:function(e){
this.titleBar.style.backgroundColor=this.accordion.options.hoverBg;
this.titleBar.style.color=this.accordion.options.hoverTextColor;
if(isIE) this.titleBar.style.cursor='hand';
else this.titleBar.style.cursor='default';
},unhover:function(e){
if(this.expanded){
this.titleBar.style.backgroundColor=this.accordion.options.expandedBg;
this.titleBar.style.color=this.accordion.options.expandedTextColor;
}else{
this.titleBar.style.backgroundColor=this.accordion.options.collapsedBg;
this.titleBar.style.color=this.accordion.options.collapsedTextColor;
}
},_attachBehaviors:function(){
this.content.style.border="1px solid "+this.accordion.options.borderColor;
this.content.style.borderTopWidth="0px";
this.content.style.borderBottomWidth="0px";
this.content.style.margin="0px";
this.titleBar.onclick=this.titleBarClicked.bindAsEventListener(this);
this.titleBar.onmouseover=this.hover.bindAsEventListener(this);
this.titleBar.onmouseout=this.unhover.bindAsEventListener(this);
}};
Rico.Effect={};
Rico.Effect.AccordionSize=Class.create();
Rico.Effect.AccordionSize.prototype={initialize:function(e1,e2,_24,end,_26,_27,_28){
this.e1=$(e1);
this.e2=$(e2);
this.start=_24;
this.end=end;
this.duration=_26;
this.steps=_27;
this.options=arguments[6]||{};
this.accordionSize();
},accordionSize:function(){
if(this.isFinished()){
this.e1.style.height=this.start+"px";
this.e2.style.height=this.end+"px";
if(this.options.complete){
this.options.complete(this);
}
return;
}
if(this.timer){
clearTimeout(this.timer);
}
var _29=Math.round(this.duration/this.steps);
var _2a=this.steps>0?(parseInt(this.e1.offsetHeight)-this.start)/this.steps:0;
this.resizeBy(_2a);
this.duration-=_29;
this.steps--;
this.timer=setTimeout(this.accordionSize.bind(this),_29);
},isFinished:function(){
return this.steps<=0;
},resizeBy:function(_2b){
var _2c=this.e1.offsetHeight;
var _2d=this.e2.offsetHeight;
var _2e=parseInt(_2b);
if(_2b!=0){
this.e1.style.height=(_2c-_2e)+"px";
this.e2.style.height=(_2d+_2e)+"px";
}
}};

