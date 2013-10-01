// jstorage




// cloning functions for div
jQuery.fn.cloneWithAttribute = function(withDataAndEvents){
	if (jQuery.support.noCloneEvent ){
		return $(this).clone(withDataAndEvents);
	}else{
		$(this).find("*").each(function(){
		$(this).data("name", $(this).attr("name"));
	});
	var clone = $(this).clone(withDataAndEvents);
		
	clone.find("*").each(function(){
	$(this).attr("name", $(this).data("name"));
	});
		
	return clone;
	}
};

// clones given div
jQuery.cloneCopyToDest = function(cloneObjList){
	if(typeof(cloneObjList) == 'object'){
		var numObjs = cloneObjList.length;
		for(var i=0; i< numObjs; i++){
			var cloneInfo = cloneObjList[i];
			if(typeof(cloneInfo)=='object'){
				var destId = cloneInfo.destId;
				var copyId = cloneInfo.copyId;
				// if both elements exist
				if(document.getElementById(copyId) && document.getElementById(destId)){
					// clear existing html in destination
					$('#'+destId).html("");
					// clone show document button in the results table
					$clone = $("#"+copyId).cloneWithAttribute(true);
					// add to the summary popup
					$('#'+destId).html($clone);
				};
			};
		};
	};
};

// returns the position relative to the given div
jQuery.fn.positionAncestor = function(selector){
    var left = 0;
    var top = 0;
    this.each(function(index, element) {
        // check if current element has an ancestor matching a selector
        // and that ancestor is positioned
        var $ancestor = $(this).closest(selector);
        if ($ancestor.length && $ancestor.css("position") !== "static") {
            var $child = $(this);
            var childMarginEdgeLeft = $child.offset().left - parseInt($child.css("marginLeft"), 10);
            var childMarginEdgeTop = $child.offset().top - parseInt($child.css("marginTop"), 10);
            var ancestorPaddingEdgeLeft = $ancestor.offset().left + parseInt($ancestor.css("borderLeftWidth"), 10);
            var ancestorPaddingEdgeTop = $ancestor.offset().top + parseInt($ancestor.css("borderTopWidth"), 10);
            left = childMarginEdgeLeft - ancestorPaddingEdgeLeft;
            top = childMarginEdgeTop - ancestorPaddingEdgeTop;
            // we have found the ancestor and computed the position
            // stop iterating
            return false;
        };
    });
    return {
        left: left,
        top: top
    };
};


// detects if single or double click on same div
jQuery.fn.single_double_click = function(single_click_callback, double_click_callback, timeout, inputObject){
	return this.each(function(){
	    var clicks = 0, self = this;
	    jQuery(this).click({inputObject: inputObject}, function(event){
	      clicks++;
	      if (clicks == 1) {
	        setTimeout(function(){
	          if(clicks == 1) {
	            single_click_callback.call(self, event);
	          } else {
	            double_click_callback.call(self, event);
	          }
	          clicks = 0;
	        }, timeout || 300);
	      }
	    });
	  });
};



(function(){
	 
    var special = jQuery.event.special,
        uid1 = 'D' + (+new Date()),
        uid2 = 'D' + (+new Date() + 1);
 
    special.scrollstart = {
        setup: function() {
 
            var timer,
                handler =  function(evt) {
 
                    var _self = this,
                        _args = arguments;
 
                    if (timer) {
                        clearTimeout(timer);
                    } else {
                        evt.type = 'scrollstart';
                        jQuery.event.handle.apply(_self, _args);
                    }
 
                    timer = setTimeout( function(){
                        timer = null;
                    }, special.scrollstop.latency);
 
                };
 
            jQuery(this).bind('scroll', handler).data(uid1, handler);
 
        },
        teardown: function(){
            jQuery(this).unbind( 'scroll', jQuery(this).data(uid1) );
        }
    };
 
    special.scrollstop = {
        latency: 300,
        setup: function() {
 
            var timer,
                    handler = function(evt) {
 
                    var _self = this,
                        _args = arguments;
 
                    if (timer) {
                        clearTimeout(timer);
                    }
 
                    timer = setTimeout( function(){
 
                        timer = null;
                        evt.type = 'scrollstop';
                        jQuery.event.handle.apply(_self, _args);
 
                    }, special.scrollstop.latency);
 
                };
 
            jQuery(this).bind('scroll', handler).data(uid2, handler);
 
        },
        teardown: function() {
            jQuery(this).unbind( 'scroll', jQuery(this).data(uid2) );
        }
    };
 
})();





// string trim (define if not supported)
if (!String.prototype.trim) {
	String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};
};



// coonverts a number to a formatted string using given info
function formatNumberToString(inNum, formatInfo){
	
	// default
	var returnNumStr = inNum.toString();
	
	// if have formatting info
	if(typeof(formatInfo)== 'object' && formatInfo != null && typeof(inNum) == 'number' && !isNaN(inNum)){
		
		// get/ set formatters
		var currencyCode = "";
		if(typeof(formatInfo.currencyCode) == 'string'){
			currencyCode = formatInfo.currencyCode;
		};
		var currencySymbol = "";
		if(typeof(formatInfo.currencySymbol) == 'string'){
			currencySymbol = formatInfo.currencySymbol;
		};
		var numThousandsSep = ",";
		if(typeof(formatInfo.numThousandsSep) == 'string'){
			numThousandsSep = formatInfo.numThousandsSep;
		};
		var numDecimalSep = ".";
		if(typeof(formatInfo.numDecimalSep) == 'string'){
			numDecimalSep = formatInfo.numDecimalSep;
		};
		var numDecimalPlaces = 0;
		if(typeof(formatInfo.numDecimalPlaces) == 'number'){
			numDecimalPlaces = formatInfo.numDecimalPlaces;
		};
		
		// set decimal places
		var setdpFltNum = inNum.toFixed(numDecimalPlaces);
		
		// convert to string
		var setdpNumStr = setdpFltNum.toString();
		
		// add in punctuation
		setdpNumStr += '';
		var x = setdpNumStr.split('.');
		var x1 = x[0];
		var x2 = x.length > 1 ? numDecimalSep + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + numThousandsSep + '$2');
		};
		
		var returnNumStr = x1 + x2;
		
		
	};
	
	return returnNumStr;
	
};




// tool tips class definition
function ToolTips(){
	
	// connector divs
	this.createConnectors();
	
	// over lap of connector and trigger 
	this.overlapH = 10;
	this.overlapV = -5;

	// time delay
	this.showDelay = 500; // milliseconds
	
	// current showing tip
	this.currentShowingEl = 0;
};


//tool tips connnectors divs
ToolTips.prototype.createConnectors = function(){
	
	if(typeof(this.connectorTopEl)=='undefined'){
		// create tip connector div
		var div = document.createElement("div");
		div.setAttribute('class', 'hapi-tooltip_c_t');
		document.body.appendChild(div);
		var $div = $(div);
		
		// get height from css or set to default
		var height = 20;
		var h = $div.css('height');
		if((h !='auto') && (h !="")){
			height = parseInt(h.substring(0, (h.length-2)));
		}
		// record
		this.connectorTopEl = $div;
		this.connectorTopHeight = height;
		
		// create connector div
		var div = document.createElement("div");
		div.setAttribute('class', 'hapi-tooltip_c_b');
		document.body.appendChild(div);
		var $div = $(div);
		
		// get height from css or set to default
		var height = 20;
		var h = $div.css('height');
		if((h !='auto') && (h !="")){
			height = parseInt(h.substring(0, (h.length-2)));
		}
		// record
		this.connectorBottomEl = $div;
		this.connectorBottomHeight = height;
	}
};

// tool tips initialisation
ToolTips.prototype.initialise = function(infoObj){
	
	// set target div to search for triggers, and delay to show
	var $allTriggers = $('div.hapi-tip_trigger'); // default
	this.currentShowDelay = this.showDelay;
	
	if(typeof(infoObj)=='object'){
		// given target
		var targetDiv = infoObj.contentDiv;
		if(typeof(targetDiv)=='string'){
			$allTriggers = $('#' +targetDiv +' .hapi-tip_trigger');
		}
		// given delay
		if(typeof(showDelay)=='number'){
			this.currentShowDelay = infoObj.showDelay;
		}
	}
	
	// get all tip triggers
	
	var numTriggers = $allTriggers.length;
	
	for(var j=0; j<numTriggers; j++){ 
		
		var trigger = $allTriggers[j];
		var $triggerEl = $(trigger);
		var showDelay = this.currentShowDelay;
		
		// ref the next tooltip as belonging to this
		var $tipEl = $triggerEl.next('.hapi-tooltip');
		
		// hide tip (if not already)
		$tipEl.hide();
		
		// attach mouseenter to trigger
		$triggerEl.mouseenter({UI_toolTips: this, $tipEl: $tipEl, $triggerEl: $triggerEl, showDelay: showDelay}, function(e){
			// data
			var UI_toolTips = e.data.UI_toolTips;
			var $tipEl = e.data.$tipEl;
			var $triggerEl = e.data.$triggerEl;
			var showDelay = e.data.showDelay;
			var infoObj = {triggerEl: $triggerEl, tipEl: $tipEl, showDelay: showDelay};
			// show tip
			UI_toolTips.setShowTipTimer(infoObj);
		});
		
		// attach mouseleave to trigger
		$triggerEl.mouseleave({UI_toolTips: this, $tipEl: $tipEl, $triggerEl: $triggerEl}, function(e){
			// data
			var UI_toolTips = e.data.UI_toolTips;
			var $tipEl = e.data.$tipEl;
			var $triggerEl = e.data.$triggerEl;
			var infoObj = {triggerEl: $triggerEl, tipEl: $tipEl};
			// show tip
			UI_toolTips.hideTip(infoObj);
		});
		
		// attach mousedown to trigger
		$triggerEl.mousedown({UI_toolTips: this, $tipEl: $tipEl, $triggerEl: $triggerEl}, function(e){
			// data
			var UI_toolTips = e.data.UI_toolTips;
			var $tipEl = e.data.$tipEl;
			var $triggerEl = e.data.$triggerEl;
			var infoObj = {triggerEl: $triggerEl, tipEl: $tipEl};
			// show tip
			UI_toolTips.hideTip(infoObj);
		});
	}
	
};


// sets tip for display
ToolTips.prototype.setTipForDisplay = function(infoObj){
	
	// set tip position
	this.setTipPosition(infoObj);
	
	
};

// set tip position
ToolTips.prototype.setTipPosition = function(infoObj){
	
	// info
	var $tipEl = infoObj.tipEl;
	var $triggerEl = infoObj.triggerEl;
	
	if(typeof($tipEl)=='object' && typeof($triggerEl)=='object'){
		if($tipEl.length>0 && $triggerEl.length>0){
			// trigger position and dimensions
			var triggerPos = $triggerEl.offset();
			var triggerHeight = $triggerEl.outerHeight();
			var triggerLeft = triggerPos.left;
			var triggerTop = triggerPos.top;
			var triggerBottom = triggerTop + triggerHeight;
			
			// tip dimensions
			var tipWidth = $tipEl.outerWidth(true);
			var tipHeight = $tipEl.outerHeight(true) + this.connectorBottomHeight;
			
			// intended tip position
			var tipRight = triggerLeft + tipWidth - this.overlapH;
			var tipBottom = triggerBottom + tipHeight - this.overlapV;
			
			// document boundaries
			var docRight = this.docRight;
			var docBottom = this.docBottom;
			
			// H offset to display on screen
			var offSetH = Math.min(0, (docRight - tipRight - 5));
			
			// set h position of tip and connector
			var tipLeftPos = parseInt(triggerLeft + offSetH - this.overlapH);
			var connectorLeft = tipLeftPos -offSetH + 5;
			
			// set default v position of tip and connector
			var connectorTop = parseInt(triggerBottom - this.overlapV);
			var tipTopPos = parseInt(connectorTop + this.connectorTopHeight);
			
			// if past bottom
			if(tipBottom > docBottom){
				// new tip top
				connectorTop = parseInt(triggerTop + this.overlapV - this.connectorTopHeight);
				tipTopPos = parseInt(connectorTop - tipHeight + this.connectorBottomHeight); 
				
				// set connector
				this.tipConnector = this.connectorTopEl;
				// hide unused connector
				this.connectorBottomEl.hide();
			} else {
				// set connector
				this.tipConnector = this.connectorBottomEl;
				// hide unused connector
				this.connectorTopEl.hide();
			}
			
			// set positions
			$tipEl.offset({top: tipTopPos, left: tipLeftPos});
			this.tipConnector.offset({top: connectorTop, left: connectorLeft});
		}
	}
	
};

// show tool tip
ToolTips.prototype.showTip = function(infoObj){
	
	// record document dimensions before opening tip
	this.docRight = $(document).width();
	this.docBottom = $(document).height();
	
	// info
	var $tipEl = infoObj.tipEl;

	// show tip
	$tipEl.show();
	// show connector
	this.connectorTopEl.show();
	this.connectorBottomEl.show();
	// set tip format and position
	this.setTipForDisplay(infoObj);
	// record
	this.currentShowingEl = $tipEl;
};

// sets up timer to show tool tip
ToolTips.prototype.setShowTipTimer = function(infoObj){
	
	 
	// hide current showing tip
	if(typeof(this.currentShowingEl)=='object'){
		var hideObj = {tipEl: currentShowingEl};
		this.hideTip(hideObj);
	}
	
	// clear current timeout
	clearTimeout(this.timeout);
	
	var $tipEl = infoObj.tipEl;
	var showDelay = infoObj.showDelay;
	
	// set timer
	if(typeof($tipEl)=='object'){
		if($tipEl.length>0){
			var UI_toolTips = this;
			this.timeout = setTimeout(function(){
				UI_toolTips.showTip(infoObj);
			}, showDelay);
		
		}
	}
};


// show tool tip
ToolTips.prototype.hideTip = function(infoObj){
	
	// clear current timeout
	clearTimeout(this.timeout);
	
	// clear ref
	this.currentShowingEl = 0;
	
	var $tipEl = infoObj.tipEl;
	if(typeof($tipEl)=='object'){
		if($tipEl.length>0){
			// hide tip
			$tipEl.hide();
			// hide connectors
			this.connectorTopEl.hide();
			this.connectorBottomEl.hide();		
		}
	}
	
};





// tell me why class definition
function TellMeWhy(){
	
	
	
};

// persistence
TellMeWhy.prototype.initialisePersistence = function(){
	
	// cookie name
	var cookieName = 'hamel_tmw_' +CURRENT_ROUTE;
	
	// cookie
	this.cookie = new Object();
	this.cookie.cookieName = cookieName;
	
	// read settings object (note, uses jStorage);
	this.cookie.info = $.jStorage.get(cookieName);
			
	// create new object if not exist
	if(!this.cookie.info){
		this.cookie.info = new Object();
	};
	
	if(typeof(this.cookie.info.divList) !='object' || this.cookie.info.divList == null){
		this.cookie.info.divList = new Object();
	};
	
	
};

// tell me why initialisation
TellMeWhy.prototype.initialise = function(){
	
	// initialise persistence
	this.initialisePersistence();
	
	// set functionality and previous state
	this.initialiseFunctionality();
	
};


// functionality
TellMeWhy.prototype.initialiseFunctionality = function(){
	
	// list of all divs
	this.divList = new Object();
	
	// set target div to search for triggers
	var $allTriggers = $('div.hapi-tellmewhy_trigger'); // default
	
	// num triggers
	var numTriggers = $allTriggers.length;
	
	for(var j=0; j<numTriggers; j++){ 
		
		// ref trigger
		var trigger = $allTriggers[j];
		var $triggerEl = $(trigger);
		
		// ref trigger info open
		$triggerInfoOpened = $triggerEl.find('.hapi-tellmewhy-infoOpened');
		
		// ref trigger info closed
		$triggerInfoClosed = $triggerEl.find('.hapi-tellmewhy-infoClosed');
		
		// ref the next info div as belonging to this
		var $infoEl = $triggerEl.next('.hapi-tellmewhy-info');
		
		// previous state
		var open = false;
		if(typeof(this.cookie.info.divList[j])=='object' && this.cookie.info.divList[j] != null){
			if(this.cookie.info.divList[j].open){
				open = true;
			};
		} else {
			this.cookie.info.divList[j] = new Object();
		};
		
		// set previous state
		if(open){
			$triggerInfoClosed.hide();
			$triggerInfoOpened.show();
			$infoEl.show();
		} else {
			$triggerInfoOpened.hide();
			$infoEl.hide();
			$triggerInfoClosed.show();
		};
		
		// attach click to closed trigger
		$triggerInfoClosed.click({UI_tellMeWhy: this, triggerInfoOpenedJQ: $triggerInfoOpened, infoElJQ: $infoEl, infoNum: j}, function(e){
			// data
			var UI_tellMeWhy = e.data.UI_tellMeWhy;
			var $triggerInfoOpened = e.data.triggerInfoOpenedJQ;
			var $infoEl = e.data.infoElJQ;
			var infoNum = e.data.infoNum;
			
			// hide closed trigger
			$(this).hide();
			
			// show opened trigger
			$triggerInfoOpened.show();
			
			// show info
			$infoEl.show('blind', 300);

			// persistence
			UI_tellMeWhy.cookie.info.divList[infoNum].open = true;
			$.jStorage_Hamel.set(UI_tellMeWhy.cookie.cookieName, UI_tellMeWhy.cookie.info);
			
		});
		
		// attach click to opened trigger
		$triggerInfoOpened.click({UI_tellMeWhy: this, triggerInfoClosedJQ: $triggerInfoClosed, infoElJQ: $infoEl, infoNum: j}, function(e){
			// data
			var UI_tellMeWhy = e.data.UI_tellMeWhy;
			var $triggerInfoClosed = e.data.triggerInfoClosedJQ;
			var $infoEl = e.data.infoElJQ;
			var infoNum = e.data.infoNum;
			
			// hide opened trigger
			$(this).hide();
			
			// show closed trigger
			$triggerInfoClosed.show();
			
			// hide info
			$infoEl.hide();
			
			// persistence
			UI_tellMeWhy.cookie.info.divList[infoNum].open = false;
			$.jStorage_Hamel.set(UI_tellMeWhy.cookie.cookieName, UI_tellMeWhy.cookie.info);
		});
		
		
	};
	
};


// help class definition
function Help(divId){
	
	// over lap of connector and trigger 
	this.overlapH = 10;
	this.overlapV = -5;

	// time delay
	this.showDelay = 500; // milliseconds
	
	// record main div
	this.id = divId;
	
	// initialse
	this.initialise();
};

// help initalise all help divs
function Help_initialiseAll(){
	
	// object holding all help class instances
	Help_All = new Object();
	
	// ids of all main help divs
	var $helpDivs = $('.hapi-help');
	
	$.each($helpDivs, function(index, $helpDiv){
		
		// id
		var divId = $helpDiv.id; 
		
		// create a new instance
		Help_All[divId] = new Help(divId);
	
	});	
	
};

// refs all divis within the main div
Help.prototype.refDivs = function(){
	
	// ref div
	var divId = this.id;
	
	// reference buttons
	this.buttons = new Object();
	this.buttons.showDiv = $('#' +divId +' .hapi-help_button_show');
	this.buttons.pinDiv = $('#' +divId +' .hapi-help_pin');
	this.buttons.noPinDiv = $('#' +divId +' .hapi-help_nopin');
	this.buttons.closeMessageDiv = $('#' +divId +' .hapi-help_button_close');
	this.buttons.pinMessageDiv = $('#' +divId +' .hapi-help_button_pin');
	
	// message divs
	this.message = new Object();
	this.message.messageDiv = $('#' +divId +' .hapi-help_message');
};


Help.prototype.initialise = function(divId){
	
	// ref the buttons and onther divs
	this.refDivs(divId);
	
	// initialise persistance
	this.initialisePersistence();
	
	// set functionality
	this.initialiseFunctionality();
	
	// set previous state
	this.setPreviousState();
	
};

//persistence
Help.prototype.initialisePersistence = function(){
	
	// div
	var id = this.id;
	
	// cookie name
	var cookieName = 'hamel_help_' +id +'_'  +CURRENT_ROUTE;
	
	// cookie
	this.cookie = new Object();
	this.cookie.cookieName = cookieName;
	
	// read settings object (note, uses jStorage);
	this.cookie.info = $.jStorage_Hamel.get(cookieName);
			
	// create new object if not exist
	if(!this.cookie.info){
		this.cookie.info = new Object();
	};
	
	if(!this.cookie.info.message){
		this.cookie.info.message = new Object();
	};
	
	
};



// set up functionality
Help.prototype.initialiseFunctionality = function(){
	
	// pin
	this.initialisePinned();
	
	// show div
	this.initialiseShow();
	
	// message
	this.initialiseMessage();
	
	// drag and drop
	this.initialiseDnD();
	
	// pin and close button
	this.initialisePinCloseButton();
	
	// section open/ close
	this.initiailiseSectionOpenClose();
};


// section open/ close functionality
Help.prototype.initiailiseSectionOpenClose = function(){
	
	// info
	if(typeof(this.cookie.info.message.sections)!= 'object' || this.cookie.info.message.sections == null){
		this.cookie.info.message.sections = new Object();
	}
	
	var $message = this.message.messageDiv;
	
	// all open/close sections
	var $allSections = $message.find('.hapi-help_sectionOpenClose');
	
	// if have any sections
	if($allSections.length>0){
		
		// ref this
		var Help = this;
		
		$.each($allSections, function(index, sectionRef){
			
			// ref section
			var $section = $(sectionRef);
			
			// ref button
			var $openCloseBtn = $section.find('.hapi-help_sectionOpenCloseBtn');
			
			// ref section to tie with this button
			var $mySection = $section.find('.hapi-help_sectionOpenCloseSection');
			
			// if have btn and section
			if($openCloseBtn.length > 0 && $mySection.length > 0){
				
				// sectionn status
				var status = false;
				if(typeof(Help.cookie.info.message.sections[index])=='boolean'){
					status = Help.cookie.info.message.sections[index];
				};
				
				// set button
				$openCloseBtn.click({Help: Help, status: status}, function(e){
					
					// this
					var Help = e.data.Help;
					
					// new status
					var status = !e.data.status;
					
					if(status){
						$mySection.show();
					} else {
						$mySection.hide();
					};
					
					// record new status
					e.data.status = status;
					
					// persistence
					Help.cookie.info.message.sections[index] = status;
					$.jStorage_Hamel.set(Help.cookie.cookieName, Help.cookie.info);
					
				});
				
				// set initial state
				if(status){
					$mySection.show();
				} else {
					$mySection.hide();
				};
				
			};
			
			// record
			Help.cookie.info.message.sections[index] = status;
			
		});
		
		
	};
	
	
	// persistence
	this.cookie.info.message.pinned = false;
	$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
};


// show/ hide button functionality
Help.prototype.initialiseShow = function(){
	
	var $showDiv = this.buttons.showDiv;
	
	if($showDiv.length>0){
		
		// mouse enter trigger
		$showDiv.mouseenter({Help: this}, function(e){
			var Help = e.data.Help;
			var pinned = Help.message.pinned;
			var showing = Help.message.showing;
			
			// if not pinned open and not showing
			if(!pinned && !showing){
				Help.showMessage();
			};
		});
	
		// mouse leave trigger
		$showDiv.mouseleave({Help: this}, function(e){
			var Help = e.data.Help;
			var pinned = Help.message.pinned;
			
			// if not pinned open
			if(!pinned){

				Help.message.closeTimeout = setTimeout(function() {
					var infoObj = new Object();
					infoObj.closeIfMouseOver = false;
					Help.hideMessage(infoObj);
				}, 200);// 0.2 seconds
				
			};
		});
		
		
		// single click and double-click functionality
		var Help = this;
		$showDiv.single_double_click(
			function(e){ // single click
				// ref input
			    var Help = e.data.inputObject; 
			    var pinned = Help.message.pinned;
				
				// if not pinned open
				if(!pinned){
					// set flag
					var showing = Help.message.showing;
					
					if(!showing){ // show message if not showing
						Help.showMessage();
					};
					
					// pin message
					Help.pinMessage();
					
				} else { // pinned
					var dragged = Help.message.dragged;
					
					if(!dragged){
						// hide message immediately
						var infoObj = new Object();
						infoObj.closeIfMouseOver = true;
						Help.hideMessage(infoObj);
						
						// unpin
						Help.unpinMessage();
					};
					
				};
			}, 
			function (e){ // double click
				var Help = e.data.inputObject;
				var $messageDiv = Help.message.messageDiv;
				
				// get original position
				var position = Help.message.position.original;
				
				// set new position
				$messageDiv.offset(position);
				
				// record current position
				Help.message.position.current = position;
				
				// persistence
				Help.cookie.info.message.position.current = position;
				$.jStorage_Hamel.set(Help.cookie.cookieName, Help.cookie.info);
			},
			300, // timeout
			Help // input
		);
	};
	

};


// pin/ unpin functionality
Help.prototype.initialisePinned = function(){
	
	// flags
	var pinned = false;
	
	// read from persistence
	if(typeof(this.cookie.info.message.pinned) =='boolean'){
		pinned = this.cookie.info.message.pinned;
	} else { // not recorded
		// record local
		this.cookie.info.message.pinned = pinned;
		$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
	};
	
	// set
	this.message.pinned = pinned;
	
	
};

// drag and drop functionality
Help.prototype.initialiseDnD = function(){
	
	// defaults
	this.message.dragging = false;
	
	if(typeof(this.message.position)!='object'){
		this.message.position = new Object();
	};
	
	// div
	var $messageDiv = this.message.messageDiv;
	
	if($messageDiv.length>0){
		
		// current position from persistence
		var currentPosition = null;
		if(typeof(this.cookie.info.message.position) =='object'){
			if(typeof(this.cookie.info.message.position.current) =='object'){
				currentPosition = this.cookie.info.message.position.current;
			} else {
				// set
				this.cookie.info.message.position.current = currentPosition;
				// record
				$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
			};
		} else {
			// set
			this.cookie.info.message.position = new Object();
			// record
			$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
		};
		
		// local
		this.message.position.current = currentPosition;
		
		
		
		var Help = this;
		// make draggable
		$messageDiv.draggable({
			containment: "document",
			start: function(event, ui){ // action on start of drag
				// set flags
				Help.message.dragging = true;
				
				// pin message
				Help.pinMessage();
			},
			
			stop: function(event, ui){ // action of end of drag
				// set flag
				Help.message.dragging = false;
				
				// record position
				$messageDiv = Help.message.messageDiv;
				var position = $messageDiv.offset();
				
				// local
				Help.message.position.current = position;
				
				// persistence
				Help.cookie.info.message.position.current = position;
				$.jStorage_Hamel.set(Help.cookie.cookieName, Help.cookie.info);
				
			},
		});
		
	}
	
	
	
};

// message functionality
Help.prototype.initialiseMessage = function(){
	
	// flags, read from persistence
	var showing = false;
	if(typeof(this.cookie.info.message.showing) =='boolean'){
		showing = this.cookie.info.message.showing;
	} else { // not recorded
		// record local
		this.cookie.info.message.showing = showing;
		$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
	};
	this.message.showing = showing;
	
	// mouse over
	this.message.mouseover = false;
	
	// div
	var $messageDiv = this.message.messageDiv;
	
	if($messageDiv.length>0){
		
		// mouse leave message
		$messageDiv.mouseleave({Help: this}, function(e){
			var Help = e.data.Help;
			var pinned = Help.message.pinned;
			
			// if not pinned open
			if(!pinned){
				// hide immediately
				var infoObj = new Object();
				infoObj.closeIfMouseOver = true;
				Help.hideMessage(infoObj);
			};
			
		});
		
		// mouse enter message
		$messageDiv.mouseenter({Help: this}, function(e){
			var Help = e.data.Help;
			
			// set flag
			Help.message.mouseover = true;
			
		});
		
	};
	
};


// close button functionality
Help.prototype.initialisePinCloseButton = function(){
	
	// close button - mouse up
	var $closeMessageDiv = this.buttons.closeMessageDiv;
	$closeMessageDiv.click({Help: this}, function(e){
		var Help = e.data.Help;
		
		var pinned = Help.message.pinned;
		
		// pinned open
		if(pinned){
			// hide message immediately
			var infoObj = new Object();
			infoObj.closeIfMouseOver = true;
			Help.hideMessage(infoObj);
			
			// unpin
			Help.unpinMessage();
	
		};
		
	});
	
	// pin button - mouse up
	var $pinMessageDiv = this.buttons.pinMessageDiv;
	$pinMessageDiv.click({Help: this}, function(e){
		var Help = e.data.Help;
		
		var pinned = Help.message.pinned;
		
		// not pinned open
		if(!pinned){
			// pin
			Help.pinMessage();
			
		};
		
	});
	
};


// sets previous state
Help.prototype.setPreviousState = function(){
	
	// pinned
	var pinned = this.message.pinned;
	
	if(pinned){
		// set pinned
		this.pinMessage();
		
		// show message
		this.showMessage();
	}
};



// pins the message open
Help.prototype.pinMessage = function(){
	
	// set flag
	this.message.pinned = true;
	
	// hide the pin button, show the close button
	var $closeMessageDiv = this.buttons.closeMessageDiv;
	$closeMessageDiv.show();
	var $pinMessageDiv = this.buttons.pinMessageDiv;
	$pinMessageDiv.hide();

	// set pin button
	var $pinDiv = this.buttons.pinDiv;
	var $noPinDiv = this.buttons.noPinDiv;
	$noPinDiv.hide();
	$pinDiv.show();
	
	// persistence
	this.cookie.info.message.pinned = true;
	$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
};

// uppins the message
Help.prototype.unpinMessage = function(){
	
	// set flag
	this.message.pinned = false;
	
	// set main button
	var $pinDiv = this.buttons.pinDiv;
	var $noPinDiv = this.buttons.noPinDiv;
	$pinDiv.hide();
	$noPinDiv.show();
	
	// persistence
	this.cookie.info.message.pinned = false;
	$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
	
};

//hides message
Help.prototype.hideMessage = function(infoObj){
	
	if(typeof(infoObj)=='object'){
		
		// flag
		var closeIfMouseOver = infoObj.closeIfMouseOver;
		
		// calc if to close
		var hideMessage = true;
		if(!closeIfMouseOver){
			if(this.message.mouseover){
				hideMessage = false;
			};
		};
		
		if(hideMessage){
			// clear any close timer
			clearTimeout(this.message.closeTimeout);
			
			// hide the message
			var $messageDiv = this.message.messageDiv;
			$messageDiv.hide();
			
			// set flags
			this.message.showing = false;
			this.message.mouseover = false;
			this.message.pinned = false;
			
			// persistence
			this.cookie.info.message.pinned = false;
			$.jStorage_Hamel.set(this.cookie.cookieName, this.cookie.info);
		};	
	};
};


// shows message
Help.prototype.showMessage = function(){
	
	// clear any close timers
	clearTimeout(this.message.closeTimeout);
	
	// set the info div and buttons
	var pinned = this.message.pinned;
	
	$closeMessageDiv = this.buttons.closeMessageDiv;
	$pinMessageDiv = this.buttons.pinMessageDiv;
	
	if(!pinned){
		
		// hide the 'close message' button
		$closeMessageDiv.hide();
		// show the 'click to pin' button
		$pinMessageDiv.show();
	} else {
		
		// hide the 'click to pin' button
		$pinMessageDiv.hide();
		// show the 'close message' button
		$closeMessageDiv.show();
	};
	
	// show the message
	var $messageDiv = this.message.messageDiv;
	$messageDiv.show();
	
	// get position
	var position = $messageDiv.offset();
	
	// record original position
	var originalPosition = this.message.position.original;
	if(!originalPosition || typeof(originalPosition)!='object'){
		// get position
		var position = $messageDiv.offset();
		
		if(position && typeof(position)=='object'){
			// record
			this.message.position.original = position;
		};
	};
	
	// set position if have a current one
	var currentPosition = this.message.position.current;
	if(currentPosition || typeof(originalPosition)=='object'){
		// position
		$messageDiv.offset(currentPosition);
	} else {
		// get position
		var position = $messageDiv.offset();
		
		if(position && typeof(position)=='object'){
			// record
			this.message.position.current = position;
		};
	};
	
	// set flag
	this.message.showing = true;
};


//initialise doms on document ready
$(window).load(function(){
	// tool tips class
	UI_toolTips = new ToolTips();
	// initialise
	UI_toolTips.initialise();
	
	// help instances
	Help_initialiseAll();
	
	// tell me why class
	UI_tellMeWhy = new TellMeWhy();
	// initialise
	UI_tellMeWhy.initialise();
	
});




// svg extension

 
// Testing the existence of the global SVGElement object to safely extend it.
if(SVGElement && SVGElement.prototype) {
	
	/**
	 * This method allow to easily add a CSS class to any SVG element
	 * 
	 * The classList parameter is a string of white space separated CSS class name.
	 * 
	 * Conveniently, this method return the object itself in order to easily chain
	 * method call.
	 *
	 * @param classList string
	 */
    SVGElement.prototype.addClass = function addClass(classList) {
        "use strict";

        // Because the className property can be animated through SVG, we have to reach
        // the baseVal property of the className SVGAnimatedString object.
        var currentClass = this.className.baseVal;

        // Note that all browsers which currently support SVG also support Array.forEach()
        classList.split(' ').forEach(function (newClass) {
            var tester = new RegExp('\\b' + newClass + '\\b', 'g');

            if (-1 === currentClass.search(tester)) {
                currentClass += ' ' + newClass;
            }
        });

        // The SVG className property is a readonly property so 
        // we must use the regular DOM API to write our new classes.
        this.setAttribute('class', currentClass);

        return this;
    };

	/**
	 * This method allow to easily remove a CSS class to any SVG element
	 * 
	 * The classList parameter is a string of white space separated CSS class name.
	 * 
	 * Conveniently, this method return the object itself in order to easily chain
	 * method call.
	 *
	 * @param classList string
	 */
  
    SVGElement.prototype.removeClass = function removeClass(classList) {
        "use strict";

        // Because the className property can be animated through SVG, we have to reach
        // the baseVal property of the className SVGAnimatedString object.
        var currentClass = this.className.baseVal;

        // Note that all browsers which currently support SVG also support Array.forEach()
        classList.split(' ').forEach(function (newClass) {
            var tester = new RegExp(' *\\b' + newClass + '\\b *', 'g');

            currentClass = currentClass.replace(tester, ' ');
        });

        // The SVG className property is a readonly property so 
        // we must use the regular DOM API to write our new classes.
        // Note that all browsers which currently support SVG also support String.trim()
        this.setAttribute('class', currentClass.trim());

        return this;
    };

	/**
	 * This method allow to easily toggle a CSS class to any SVG element
	 * 
	 * The classList parameter is a string of white space separated CSS class name.
	 * 
	 * Conveniently, this method return the object itself in order to easily chain
	 * method call.
	 *
	 * @param classList string
	 */
	SVGElement.prototype.toggleClass = function toggleClass(classList) {
	    "use strict";
	
	    // Because the className property can be animated through SVG, we have to reach
	    // the baseVal property of the className SVGAnimatedString object.
	    var currentClass = this.className.baseVal;
	
	    // Note that all browsers which currently support SVG also support Array.forEach()
	    classList.split(' ').forEach(function (newClass) {
	        var tester = new RegExp(' *\\b' + newClass + '\\b *', 'g');
	
	        if (-1 === currentClass.search(tester)) {
	            currentClass += ' ' + newClass;
	        } else {
	            currentClass = currentClass.replace(tester, ' ');
	        }
	    });
	
	    // The SVG className property is a readonly property so 
	    // we must use the regular DOM API to write our new classes.
	    // Note that all browsers which currently support SVG also support String.trim()
	    this.setAttribute('class', currentClass.trim());
	
	    return this;
	};
	
	
};



