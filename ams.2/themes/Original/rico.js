/**
  *
  *  Copyright 2005 Sabre Airline Solutions
  *
  *  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
  *  file except in compliance with the License. You may obtain a copy of the License at
  *
  *         http://www.apache.org/licenses/LICENSE-2.0
  *
  *  Unless required by applicable law or agreed to in writing, software distributed under the
  *  License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
  *  either express or implied. See the License for the specific language governing permissions
  *  and limitations under the License.
  **/


Rico = {};

Rico.Accordion = Class.create();
Rico.Accordion.prototype = {

   initialize: function(container, options) {
      this.container            = $(container);
      this.lastExpandedTab      = null;
      this.accordionTabs        = new Array();
      this.setOptions(options);
      this._attachBehaviors();
      if(!container) return;

      this.container.style.borderBottom = '1px solid ' + this.options.borderColor;
      // validate onloadShowTab
       if (this.options.onLoadShowTab >= this.accordionTabs.length)
        this.options.onLoadShowTab = 0;

      // set the initial visual state...
      for ( var i=0 ; i < this.accordionTabs.length ; i++ )
      {
        if (i != this.options.onLoadShowTab){
         this.accordionTabs[i].collapse();
         this.accordionTabs[i].content.style.display = 'none';
        }
      }
      this.lastExpandedTab = this.accordionTabs[this.options.onLoadShowTab];
      if (this.options.panelHeight == 'auto'){
          var tabToCheck = (this.options.onloadShowTab === 0)? 1 : 0;
          var titleBarSize = parseInt(RicoUtil.getElementsComputedStyle(this.accordionTabs[tabToCheck].titleBar, 'height'));
          if (isNaN(titleBarSize))
            titleBarSize = this.accordionTabs[tabToCheck].titleBar.offsetHeight;
          
          var totalTitleBarSize = this.accordionTabs.length * titleBarSize;
          var parentHeight = parseInt(RicoUtil.getElementsComputedStyle(this.container.parentNode, 'height'));
          if (isNaN(parentHeight))
            parentHeight = this.container.parentNode.offsetHeight;
          
          this.options.panelHeight = parentHeight - totalTitleBarSize-2;
      }
      
      this.lastExpandedTab.content.style.height = this.options.panelHeight + "px";
      this.lastExpandedTab.showExpanded();
      this.lastExpandedTab.titleBar.style.fontWeight = this.options.expandedFontWeight;

   },

   setOptions: function(options) {
      this.options = {
         expandedBg          : '#63699c',
         hoverBg             : '#63699c',
         collapsedBg         : '#6b79a5',
         expandedTextColor   : '#ffffff',
         expandedFontWeight  : 'bold',
         hoverTextColor      : '#ffffff',
         collapsedTextColor  : '#ced7ef',
         collapsedFontWeight : 'normal',
         hoverTextColor      : '#ffffff',
         borderColor         : '#1f669b',
         panelHeight         : 200,
         onHideTab           : null,
         onShowTab           : null,
         onLoadShowTab       : 0
      }
      Object.extend(this.options, options || {});
   },

   showTabByIndex: function( anIndex, animate ) {
      var doAnimate = arguments.length == 1 ? true : animate;
      this.showTab( this.accordionTabs[anIndex], doAnimate );
   },

   showTab: function( accordionTab, animate ) {
     if ( this.lastExpandedTab == accordionTab )
        return;

      var doAnimate = arguments.length == 1 ? true : animate;

      if ( this.options.onHideTab )
         this.options.onHideTab(this.lastExpandedTab);

      this.lastExpandedTab.showCollapsed(); 
      var accordion = this;
      var lastExpandedTab = this.lastExpandedTab;

      this.lastExpandedTab.content.style.height = (this.options.panelHeight - 1) + 'px';
      accordionTab.content.style.display = '';

      accordionTab.titleBar.style.fontWeight = this.options.expandedFontWeight;

      if ( doAnimate ) {
         new Rico.Effect.AccordionSize( this.lastExpandedTab.content,
                                   accordionTab.content,
                                   1,
                                   this.options.panelHeight,
                                   100, 10,
                                   { complete: function() {accordion.showTabDone(lastExpandedTab)} } );
         this.lastExpandedTab = accordionTab;
      }
      else {
         this.lastExpandedTab.content.style.height = "1px";
         accordionTab.content.style.height = this.options.panelHeight + "px";
         this.lastExpandedTab = accordionTab;
         this.showTabDone(lastExpandedTab);
      }
   },

   showTabDone: function(collapsedTab) {
      collapsedTab.content.style.display = 'none';
      this.lastExpandedTab.showExpanded();
      if ( this.options.onShowTab )
         this.options.onShowTab(this.lastExpandedTab);
   },

   _attachBehaviors: function() {
      var panels = this._getDirectChildrenByTag(this.container, 'DIV');
      for ( var i = 0 ; i < panels.length ; i++ ) {

         var tabChildren = this._getDirectChildrenByTag(panels[i],'DIV');
         if ( tabChildren.length != 2 )
            continue; // unexpected

         var tabTitleBar   = tabChildren[0];
         var tabContentBox = tabChildren[1];
         this.accordionTabs.push( new Rico.Accordion.Tab(this,tabTitleBar,tabContentBox) );
      }
   },

   _getDirectChildrenByTag: function(e, tagName) {
      var kids = new Array();
      var allKids = e.childNodes;
      for( var i = 0 ; i < allKids.length ; i++ )
         if ( allKids[i] && allKids[i].tagName && allKids[i].tagName == tagName )
            kids.push(allKids[i]);
      return kids;
   }

};

Rico.Accordion.Tab = Class.create();

Rico.Accordion.Tab.prototype = {

   initialize: function(accordion, titleBar, content) {
      this.accordion = accordion;
      this.titleBar  = titleBar;
      this.content   = content;
      this._attachBehaviors();
   },

   collapse: function() {
      this.showCollapsed();
      this.content.style.height = "1px";
   },

   showCollapsed: function() {
      this.expanded = false;
      this.titleBar.style.backgroundColor = this.accordion.options.collapsedBg;
      this.titleBar.style.color           = this.accordion.options.collapsedTextColor;
      this.titleBar.style.fontWeight      = this.accordion.options.collapsedFontWeight;
      this.content.style.overflow = "hidden";

   },

   showExpanded: function() {
      this.expanded = true;
      this.titleBar.style.backgroundColor = this.accordion.options.expandedBg;
      this.titleBar.style.color           = this.accordion.options.expandedTextColor;
      this.content.style.overflow         = "auto";

   },

   titleBarClicked: function(e) {
      if ( this.accordion.lastExpandedTab == this )
         return;
      this.accordion.showTab(this);
   },

   hover: function(e) {
		this.titleBar.style.backgroundColor = this.accordion.options.hoverBg;
		this.titleBar.style.color           = this.accordion.options.hoverTextColor;
		if(isIE) this.titleBar.style.cursor = "hand";
		else this.titleBar.style.cursor = "default";
   },

   unhover: function(e) {
      if ( this.expanded ) {
         this.titleBar.style.backgroundColor = this.accordion.options.expandedBg;
         this.titleBar.style.color           = this.accordion.options.expandedTextColor;
      }
      else {
         this.titleBar.style.backgroundColor = this.accordion.options.collapsedBg;
         this.titleBar.style.color           = this.accordion.options.collapsedTextColor;
      }
   },

   _attachBehaviors: function() {
      this.content.style.border = "1px solid " + this.accordion.options.borderColor;
      this.content.style.borderTopWidth    = "0px";
      this.content.style.borderBottomWidth = "0px";
      this.content.style.margin            = "0px";

      this.titleBar.onclick     = this.titleBarClicked.bindAsEventListener(this);
      this.titleBar.onmouseover = this.hover.bindAsEventListener(this);
      this.titleBar.onmouseout  = this.unhover.bindAsEventListener(this);
   }

};


//-------------------- ricoEffects.js

Rico.Effect = {};

Rico.Effect.AccordionSize = Class.create();

Rico.Effect.AccordionSize.prototype = {

   initialize: function(e1, e2, start, end, duration, steps, options) {
      this.e1       = $(e1);
      this.e2       = $(e2);
      this.start    = start;
      this.end      = end;
      this.duration = duration;
      this.steps    = steps;
      this.options  = arguments[6] || {};

      this.accordionSize();
   },

   accordionSize: function() {

      if (this.isFinished()) {
         // just in case there are round errors or such...
         this.e1.style.height = this.start + "px";
         this.e2.style.height = this.end + "px";

         if(this.options.complete)
            this.options.complete(this);
         return;
      }

      if (this.timer)
         clearTimeout(this.timer);

      var stepDuration = Math.round(this.duration/this.steps) ;

      var diff = this.steps > 0 ? (parseInt(this.e1.offsetHeight) - this.start)/this.steps : 0;
      this.resizeBy(diff);

      this.duration -= stepDuration;
      this.steps--;

      this.timer = setTimeout(this.accordionSize.bind(this), stepDuration);
   },

   isFinished: function() {
      return this.steps <= 0;
   },

   resizeBy: function(diff) {
      var h1Height = this.e1.offsetHeight;
      var h2Height = this.e2.offsetHeight;
      var intDiff = parseInt(diff);
      if ( diff != 0 ) {
         this.e1.style.height = (h1Height - intDiff) + "px";
         this.e2.style.height = (h2Height + intDiff) + "px";
      }
   }

};
//---------------------------------//
Rico.__onShowTab = function (tab) {
    var s = new String(tab.titleBar.id);
    s=s.substr(15);
    menu_module=s;
    if(_panelTab[s]) {
	if(_panelTab[s].actionH) {
	    eval(_panelTab[s].actionH);
	    _panelTab[s].actionH='';
	}else eval(_panelTab[s].action);
	if(_lastTabH) {
	    $(_lastTabH).style.display='none';
	    _lastTabH=null;
	}			    
    }else if(_panelTabH[s]) eval(_panelTabH[s].action);
}

runJScriptMenu = function(form,m,cm,a,p,cash) {
	if(_panelTab[m]) {

	    if(_panelTab[m].id != _lastTab){
		 _panelTab[m].actionH="loadModuleContent('"+form+"','"+cm+"','"+a+"','"+p+"',"+cash+")";
		 _accordionMenu.showTabByIndex(_panelTab[m].indexTab);
	    }
	    _lastTab=_panelTab[m].id;
	    return;
	}
	if(_panelTabH[m]) {
		if(_lastTabH) $(_lastTabH).style.display='none';
		$(_panelTabH[m].id).style.display='inline';
		_panelTabH[m].action="loadModuleContent('"+form+"','"+cm+"','"+a+"','"+p+"',"+cash+")";
		_accordionMenu.showTabByIndex(_panelTabH[m].indexTab);
		_lastTabH=_panelTabH[m].id;
		_lastTab=null;
		
	}
	else _lastTabH=null;
}

_panelTab = new Array;
_panelTabH = new Array;
_lastTabH=null;
_lastTab=null;

Rico.__setTab = function(name,index,action) {
    _panelTab[name] = new Object;
    _panelTab[name].indexTab=index;
    _panelTab[name].id='_panelTab'+name;
    _panelTab[name].action=action;
    _panelTab[name].actionH='';
}
Rico.__setTabH = function(name,index) {
    _panelTabH[name] = new Object;
    _panelTabH[name].indexTab=index;
    _panelTabH[name].id='_panelTab'+name;
    _panelTabH[name].action='';
}
