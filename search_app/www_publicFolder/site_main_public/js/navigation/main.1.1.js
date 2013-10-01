// Site navigation






//class definition
function Navigation(infoObj){
	
	// core info
	this.menuId = infoObj.menuId;
	this.tabsId = infoObj.tabsId;
	
	// cookie info
	this.cookie = new Object();
	this.cookie.cookieName = infoObj.cookieName;
	
	// info obj
	this.menuSets = new Object();
	this.menuSets.menuShowing = -1;
	
};


// initialises navigation menu
Navigation.prototype.initialise = function(){
	
	// initialise persistence
	this.initialisePersistence();
	
	// set menu options
	this.setMenuOptions();
	
};


Navigation.prototype.initialisePersistence = function(){
	
	// read settings object
	var cookieName = this.cookie.cookieName;
	this.cookie.info = $.jStorage.get(cookieName);
			
	// create new object if not exist
	if(!(this.cookie.info && typeof(this.cookie.info)=='object')){
		this.cookie.info = new Object();
	};
	if(typeof(this.cookie.info.page)!='object'){
		this.cookie.info.page = new Object();
	};

};


// returns current selected tab num (by the custom order of the tabs)
Navigation.prototype.getSelectedTabNum = function(infoObj){
	
	var tabNum = 0;
	
	if(typeof(infoObj)=='object'){
		// tabs id & route
		var tabsId = infoObj.tabsId;
		var route = infoObj.route;
		
		if(typeof(this.cookie.info.page[route])=='object'){
			if(typeof(this.cookie.info.page[route][tabsId])=='object'){
				tabNum = this.cookie.info.page[route][tabsId].selected;
			};
		};
	};
	// return
	return parseInt(tabNum);
};

// returns current selected tab num by the original order of the tabs
Navigation.prototype.getSelectedTabNumOriginalOrder = function(infoObj){

	// get selected tab num
	var tabNum = this.getSelectedTabNum(infoObj);
	
	if(typeof(infoObj)=='object'){
		// tabs id & route
		var tabsId = infoObj.tabsId;
		var route = infoObj.route;
		
		// current tab order
		if(typeof(this.cookie.info.page[route])=='object'){
			if(typeof(this.cookie.info.page[route][tabsId])=='object'){
				if(typeof(this.cookie.info.page[route][tabsId].tabOrder)=='object'){
					// order
					var tabOrder = this.cookie.info.page[route][tabsId].tabOrder;
					
					if(typeof(tabOrder[tabNum])== 'number'){
						tabNum = tabOrder[tabNum];
					};
				};
			};
		};
	};
	// return
	return tabNum;
	
};


// returns the tab num from the original order in the current order
Navigation.prototype.getCurrentToOriginalTabNum = function(infoObj){
	
	var adjustedTabNum = null;
	
	if(typeof(infoObj)=='object'){
		// info
		var route =infoObj.route;
		var tabNum = infoObj.tabNum;
		var tabsId = infoObj.tabsId;
	
		// default
		adjustedTabNum = parseInt(tabNum); // ensure type
		
		// current tab order
		if(typeof(this.cookie.info.page[route])=='object'){
			if(typeof(this.cookie.info.page[route][tabsId])=='object'){
				if(typeof(this.cookie.info.page[route][tabsId].tabOrder)=='object'){
					// order
					var tabOrder = this.cookie.info.page[route][tabsId].tabOrder;
					
					if(typeof(tabOrder[tabNum])== 'number'){
						adjustedTabNum = tabOrder[tabNum];
					};	
				};
			};
		};
	};
	// return
	return adjustedTabNum;
};


// returns the tab num in the current tab order of a given original tab num
Navigation.prototype.getOriginalToCurrentTabNum = function(infoObj){
	
	var adjustedTabNum = null;
	
	if(typeof(infoObj)=='object'){
		// info
		var route =infoObj.route;
		var tabsId = infoObj.tabsId;
		var oTabNum = infoObj.tabNum;
		
		// default
		adjustedTabNum = parseInt(oTabNum); // ensure type
		
		if(typeof(this.cookie.info.page[route])=='object'){
			if(typeof(this.cookie.info.page[route][tabsId])=='object'){	
				if(typeof(this.cookie.info.page[route][tabsId].tabOrder)=='object'){
		
					// get tab order
					var tabOrder = this.cookie.info.page[route][tabsId].tabOrder;
					
					if(typeof(tabOrder)=='object'){
						var numTabs = tabOrder.length;
						
						for(var i=0; i<numTabs; i++){
							var orderedTabNum = tabOrder[i];
							if(oTabNum == orderedTabNum){
								// set 
								adjustedTabNum = i;
								// exit loop
								break;
							};
						};
					};
				};
			};
		};
	};
	
	// return
	return adjustedTabNum;
};



// records current selected tab num
Navigation.prototype.updateSelectedTabNum = function(infoObj){
	
	if(typeof(infoObj)=='object'){
		// info
		var route =infoObj.route;
		var tabsId = infoObj.tabsId;
		var tabNum = infoObj.tabNum;

		// set object if not exist
		if(typeof(this.cookie.info.page[route])!='object'){
			this.cookie.info.page[route] = new Object();
		};
		if(typeof(this.cookie.info.page[route][tabsId])!='object'){
			this.cookie.info.page[route][tabsId] = new Object();
		};
	
	// record tab num
	this.cookie.info.page[route][tabsId].selected = tabNum;
	
	// rocord local
	$.jStorage.set(this.cookie.cookieName, this.cookie.info);
	
	};
};


// records current tab order and selected tab num
Navigation.prototype.updateCurrentTabOrder = function(infoObj){
	
	if(typeof(infoObj)=='object'){
		// info
		var route =infoObj.route;
		var tabsId = infoObj.tabsId;
		var tabOrder = infoObj.tabOrder;
		var idList = infoObj.idList;
		
		
		if(typeof(tabOrder)=='object'){
			
			// set object if not exist
			if(typeof(this.cookie.info.page[route])!='object'){
				this.cookie.info.page[route] = new Object();
			};
			if(typeof(this.cookie.info.page[route][tabsId])!='object'){
				this.cookie.info.page[route][tabsId] = new Object();
			};
			
			// the currently selected tab num in the previous tab order
			var selectedTabNum =  this.getSelectedTabNum(infoObj);
			var pTabNum = selectedTabNum;
			
			if(typeof(this.cookie.info.page[route][tabsId].tabOrder)=='object'){
				var pTabOrder = this.cookie.info.page[route][tabsId].tabOrder;
				if(typeof(pTabOrder[selectedTabNum])== 'number'){
					pTabNum = pTabOrder[selectedTabNum];
				};
			};
			
			// record new order
			this.cookie.info.page[route][tabsId].tabOrder = tabOrder;
			this.cookie.info.page[route][tabsId].tabOrderIdList = idList;
			
			
			// calc the selected tab num in the new sort order
			var adjustedTabNum = pTabNum;
			if(typeof(tabOrder)=='object'){
				var numTabs = tabOrder.length;
				
				for(var i=0; i<numTabs; i++){
					var orderedTabNum = tabOrder[i];
					if(pTabNum == orderedTabNum){
						// set 
						adjustedTabNum = i;
						// exit loop
						break;
					};
				};
			};
			// set the selected tab num
			this.cookie.info.page[route][tabsId].selected = adjustedTabNum;
			
			// write local
			$.jStorage.set(this.cookie.cookieName, this.cookie.info);
			
		};
	};
};


// set the tabs in the current order
// if no order found, leaves as sequential (assumes starts at tab 0)
Navigation.prototype.orderTabs = function(infoObj){
	
	if(typeof(infoObj)=='object'){
		// tabs id & route
		var tabsId = infoObj.tabsId;
		var route = infoObj.route;
		
		// default
		var tabOrder = 0;
		var tabOrderIdList = 0;
		
		if(typeof(this.cookie.info.page[route])=='object'){
			if(typeof(this.cookie.info.page[route][tabsId])=='object'){ // have info for this route and tab set
				// current order
				if(typeof(this.cookie.info.page[route][tabsId].tabOrder)=='object'){
					tabOrder = this.cookie.info.page[route][tabsId].tabOrder;
				};
				// list of ids in current order
				if(typeof(this.cookie.info.page[route][tabsId].tabOrderIdList)=='object'){
					tabOrderIdList = this.cookie.info.page[route][tabsId].tabOrderIdList;
				};
			};	
		};
		
		if(typeof(tabOrder)=='object' && (typeof(tabOrderIdList)=='object')){
			// first tab
			var firstTabId = tabOrderIdList[0];
			var $firsTab = $('#'+firstTabId);
			
			// ref the parent
			var $parentDiv = $firsTab.parent();
			// ref the children
			var $originalTabs = $parentDiv.children();
			
			// copy all the tab divs (with functionality) in the new order
			var originalTabsObj = new Object(); 
			for(var i=0; i<$originalTabs.length; i++){
				// ref tab
				var tab = $originalTabs[i];
				// clone show document button in the results table
				$clone = $(tab).cloneWithAttribute(true);
				// record
				originalTabsObj[i] = $clone;
			};
			
			// clear the original tabs
			$parentDiv.html("");
			
			// add the cloned tabs in the new order
			for(var i=0; i<tabOrder.length; i++){
				var tabNum = tabOrder[i];
				var $tabDiv = originalTabsObj[tabNum];
				
				// add
				$parentDiv.append($tabDiv);
			};
			
		};
	};
};



// initialises page tabs
Navigation.prototype.initialiseTabs = function(infoObj){
	
	// default
	var selected = 0;

	if(typeof(infoObj)=='object'){
		
		// core tabs id and route
		var tabsId = infoObj.tabsId;
		var route = infoObj.route;
		 
		// read info
		var tabsAndContentContainerId = infoObj.tabsAndContentContainerId;
		var cookieName = infoObj.cookieName;
		var sortableContainmentId = infoObj.sortableContainmentId;
		var returnByOriginalOrder = infoObj.returnByOriginalOrder;
		
		// disabled tab info
		var disabledInfo = infoObj.disabledInfo;
		
		// functions called on tab events
		var tabActionFunctions = infoObj.tabActionFunctions;
		
		// list of tabs to disable
		var disableList = new Array();
		
		// set the tab order
		this.orderTabs(infoObj);
		
		
		// set disabled list from given info
		if(typeof(disabledInfo)=='object'){
			var numTabs = disabledInfo.length;
			for(var i=0; i<numTabs; i++){ // for each tab
				var tabInfo = disabledInfo[i];
				if(typeof(tabInfo)=='object'){
					// core info
					var id = tabInfo.id;
					var tabNum = tabInfo.tabNum;
					var disableIfNotFound = tabInfo.disableIfNotFound;
					
					// look for div
					$targetDiv = $('#'+id);
					
					var haveTarget = false;
					if($targetDiv.length>0){
						haveTarget = true;
					};
					
					// set flag
					var addTab = false;
					if(!haveTarget){
						if(disableIfNotFound){
							addTab = true; // don't have div and request to disable if not found
						};
					} else {
						if(!disableIfNotFound){
							addTab = true; // have div and request to disable if found
						};
					};
					
					if(addTab){
						// convert the original tab num to the current tab order
						var infoObj = new Object();
						infoObj.route = route;
						infoObj.tabsId = tabsId;
						infoObj.tabNum = tabNum;
						var orderedTabNum = this.getOriginalToCurrentTabNum(infoObj);
						
						disableList.push(orderedTabNum); // record
					}
				};
			};
		};
		
		
		

		// get last selected tab num
		var infoObj = new Object();
		infoObj.route = route;
		infoObj.tabsId = tabsId;
		var tabNum = this.getSelectedTabNum(infoObj);
		
		var updateObj = new Object();
		updateObj.route = route;
		updateObj.tabsId = tabsId;
		
		// ref this
		Navigation = this;
		
		// set tabs
		$tabs = $('#' +tabsAndContentContainerId).tabs({
				disabled: disableList, 
				active: tabNum,
				heightStyle: "content",
				hide: false,
				show: false,
				select: function(event, ui){
					
					// ref update object
					var infoObj = updateObj;
					// record current tab num
					infoObj.tabNum = ui.index;
					
					// update
					Navigation.updateSelectedTabNum(infoObj);
					
					// optional functions
					if(typeof(tabActionFunctions)=='object'){
						if(typeof(tabActionFunctions.select)=='function'){
							tabActionFunctions.select.call(self, event);
						};
					};
					
					// if to go to a url (and not to a tab on same page)
				    var $tab = $(ui.tab);
				    var linkType = $tab.attr('linktype');
				    if(linkType == 'url'){
				    	// target URL
				    	var targetUrl = $tab.attr('linkurl');
				    	// set location
				    	location.href = targetUrl;
				    	
				    	// cancel out of tabs routine
				    	return false;
				    };
				    
				},
				beforeLoad: function(event, ui){
					// if to go to a url (and not to a tab on same page)
				    var $tab = $(ui.tab);
				    var linkType = $tab.attr('linktype');
				    if(linkType == 'url'){
				    	// target URL
				    	var targetUrl = $tab.attr('href');
				    	// set location
				    	location.href = targetUrl;
				    	
				    	// cancel out of tabs routine
				    	return false;
				    };
					
				},
				show: function(event, ui){
					// optional functions
					if(typeof(tabActionFunctions)=='object'){
						if(typeof(tabActionFunctions.show)=='function'){
							tabActionFunctions.show.call(self, event);
						};
					};
				},
				create: function(event, ui){
					// remove hide class for tab container
					$('#' +tabsAndContentContainerId).removeClass('hapi-hide');
				},
			});
		
		
		
		// sortable
		var sortable = infoObj.sortable;
		
		if(sortable){
		// make the tabs sortable
			$tabs.find('#' +tabsId +' .ui-tabs-nav').sortable({
				expression: /(.+)[-](.+)/,
		        axis: "x",
		        containment: '#'+sortableContainmentId,
		        update: function(e, ui){
		        	// serialise new tab order
		        	var pos = $('#' +tabsId +' .ui-tabs-nav').sortable('serialize',{
		        		attribute: 'id',
		        		key: 'tab',
		        	});
		        	// split into array of ints
		        	var posStr1 = pos.replace(/&/g, '');
		        	var posStr2 = posStr1.replace('tab=', '');
		        	var tabOrderList = $.map(posStr2.split('tab='), function(value){
		        		return parseInt(value, 10);
		        		});
		        	
		        	// get array of ids of tabs in new order
		        	var idList = $('#' +tabsId +' .ui-tabs-nav').sortable('toArray');
		        	
		        	// update the global nav
		        	var infoObj = updateObj;
		        	infoObj.idList = idList;
		        	infoObj.tabOrder = tabOrderList;
					Navigation.updateCurrentTabOrder(infoObj);
		        }
		    });
		};
		
		// read currently selected tab
		selected = $tabs.tabs('option', 'selected');
		
		if(jQuery.inArray(selected,disableList) > -1) { // if tabs persistence cookie has set a disabled tab
			// set first tab as selected
			$tabs.tabs('select', 0);
			// disable the selected
			$tabs.tabs('disable', selected);
			// set the return val
			selected = 0;
		};
		
		// return tab num
		if(returnByOriginalOrder){
			// translate the curren tab num to the original order
			var infoObj = new Object();
			infoObj.route = route;
			infoObj.tabsId = tabsId;
			selected = this.getSelectedTabNumOriginalOrder(infoObj);
		};
		
	};
	
	return selected;

};


// sets menu options
Navigation.prototype.setMenuOptions = function(){
	
	// ref ids
	var menuId = this.menuId;
	var tabsId = this.tabsId;
	// ref this
	var Navigation = this;
	
	
	// set listener to close menu on mouse down elsewhere in document
	$(document).mousedown({Navigation:this}, function(e){
		// info
		var Navigation = e.data.Navigation;
		var menuShowing = Navigation.menuSets.menuShowing;
	
		if(menuShowing != -1){
			// hide the showing menu
			var uInfoObj = new Object();
			uInfoObj.show = false;
			uInfoObj.callType = 'outside';
			Navigation.showMenuOptions(uInfoObj);
		};
	});
	
	
	// select all menu items
	var $allMenuItems = $('#' +menuId +" .menuItemAndOptions");
	
	// menuset num
	var menuSetNum = 0;
	
	// for each menu item set
	$.each($allMenuItems, function(setIndex){ // (1)
		
		// set num
		menuSetNum++;
		
		// info obj
		Navigation.menuSets[menuSetNum] = new Object();
		Navigation.menuSets[menuSetNum].withinOptions = false;
		Navigation.menuSets[menuSetNum].editing = false;
		
		// ref set
		var $menuSet = $(this);
		
		Navigation.menuSets[menuSetNum].$menu = $menuSet;
		
		// select all menu items
		var $allMenuItems = $menuSet.find('.menuItem');
		
		// for each item in this set
		$.each($allMenuItems, function(itemIndex){ //(2)
			// get class
			var classStr = $(this).attr('class');
			
			// if this item is active
			if(classStr.indexOf('inactive') !== -1){ // if active
				
				// route
				var route = $(this).attr('route');
				// tab num
				var oTabNum = parseInt($(this).attr('tabnum'));
				
				// if this menu item has a tab num associated with it
				if(typeof(oTabNum) != 'undefined'){ // if has tab num
					
					// info obj
					var infoObj = new Object();
					infoObj.Navigation = Navigation;
					infoObj.tabsId = tabsId;
					infoObj.oTabNum = oTabNum;
					infoObj.route = route;
					
					// on mouse up
					$(this).mouseup({infoObj: infoObj}, function(e){
						// ref nav menu
						var infoObj = e.data.infoObj;
						var Navigation = infoObj.Navigation;
						var tabsId = infoObj.tabsId;
						var oTabNum = infoObj.oTabNum;
						var route = infoObj.route;
						

						// info obj
						var updateInfo = new Object();
						updateInfo.tabsId = tabsId;
						updateInfo.tabNum = oTabNum;
						updateInfo.route = route;
						// convert original tab num to current order
						var tabNum = Navigation.getOriginalToCurrentTabNum(updateInfo);
						
						// update menu
						updateInfo.tabNum = tabNum;
						Navigation.updateSelectedTabNum(updateInfo);
						
					});
					
					
				}; // end if has tab num	
			}; // end if active
			
		}); //(2)
		
		
		// options in this menu set
		var $allMenuOptions = $menuSet.find('.options');
		
		
		
		if($allMenuOptions.length>0){ // (2) if have options
			
			// info obj
			var infoObj = new Object();
			infoObj.Navigation = Navigation;
			infoObj.menuSetNum = menuSetNum;
			
			// add listenet to each option set
			$.each($allMenuOptions, function(index, optionSet){
				
				var $optionSet = $(optionSet);
				
				// mouse enter set of potions
				$optionSet.mouseenter({infoObj: infoObj}, function(e){
					// info
					var infoObj = e.data.infoObj;
					var Navigation = infoObj.Navigation;
					var menuSetNum = infoObj.menuSetNum;
					
					// set flag
					Navigation.menuSets[menuSetNum].withinOptions = true;
				});
				
				
				// mouse leave set of potions
				$optionSet.mouseleave({infoObj: infoObj}, function(e){
					// info
					var infoObj = e.data.infoObj;
					var Navigation = infoObj.Navigation;
					var menuSetNum = infoObj.menuSetNum;
					
					// set flag
					Navigation.menuSets[menuSetNum].withinOptions = false;
				});
				
				
			});
		};
		
		
		if($allMenuOptions.length>0){ // (2) if have options
			
			// ref option set
			Navigation.menuSets[menuSetNum].$options = $allMenuOptions;
			
			// does menu option set have any text input fields
			var haveTextInputs = false;
			var $allTextInputs = $allMenuOptions.find("[type='text'], [type='textarea'], [type='email'], [type='password']");
			if($allTextInputs.length > 0){
				haveTextInputs = true;
			};
			
			
			
			// info obj
			var infoObj = new Object();
			infoObj.Navigation = Navigation;
			infoObj.menuSetNum = menuSetNum;
			

			
			// open menu options on enter menu set
			$menuSet.mouseenter({infoObj: infoObj}, function(e){
				// info
				var infoObj = e.data.infoObj;
				var Navigation = infoObj.Navigation;
				var menuSetNum = infoObj.menuSetNum;
				
				// show
				var uInfoObj = new Object();
				uInfoObj.show = true;
				uInfoObj.callType = 'menu';
				uInfoObj.menuSetNum = menuSetNum;
				Navigation.showMenuOptions(uInfoObj);
			
			});
			
			// catch if user is entering text in this (will be open) menu option set
			if(haveTextInputs){
				
				// all text-based inputs
				$.each($allTextInputs, function(index, input){
					
					// this
					var $input = $(input);
										
					// when user is entering in this input
					$input.mouseup({infoObj: infoObj}, function(e){
						var infoObj = e.data.infoObj;
						var Navigation = infoObj.Navigation;
						var menuSetNum = infoObj.menuSetNum;
						
						// set flag
						Navigation.menuSets[menuSetNum].editing = true;
					});
					
				});
				
				
			};
			
			
			// add roll out to main menu
			$menuSet.mouseleave({infoObj: infoObj}, function(e){
				// info
				var infoObj = e.data.infoObj;
				var menuSetNum = infoObj.menuSetNum;
				
				// hide the options
				var uInfoObj = new Object();
				uInfoObj.show = false;
				uInfoObj.callType = 'options';
				uInfoObj.menuSetNum = menuSetNum;
				Navigation.showMenuOptions(uInfoObj);
				
			});
			
			
			// options showing on intial output?
			var showing = $allMenuOptions.is(":visible");
			
			if(showing){
				// set position
				var posInfoObj = new Object();
				posInfoObj.show = true;
				uInfoObj.callType = 'options';
				posInfoObj.menuSetNum = menuSetNum;
				Navigation.showMenuOptions(posInfoObj);
			};
			
			
		} else { // do not have options
			

			// info obj
			var infoObj = new Object();
			infoObj.Navigation = Navigation;
			infoObj.menuSetNum = menuSetNum;
			
			// close any open options of onther menu sets on entering this menu set
			$menuSet.mouseenter({infoObj: infoObj}, function(e){
				// info
				var infoObj = e.data.infoObj;
				var Navigation = infoObj.Navigation;
				var menuSetNum = infoObj.menuSetNum;
				
				// show
				var uInfoObj = new Object();
				uInfoObj.show = false;
				uInfoObj.callType = 'menu';
				uInfoObj.menuSetNum = menuSetNum;
				Navigation.showMenuOptions(uInfoObj);
			
			});
			
		}; // (2)
		
		
	}); // (1)
	
};



// shows and sets position of menu options on show
Navigation.prototype.showMenuOptions = function(infoObj){
	
	if(typeof(infoObj)=='object'){
		
		// core info
		var showMenu = infoObj.show;
		var callType = infoObj.callType;
		var menuSetNum = infoObj.menuSetNum;
		
		// options and set
		var $options = new Object();
		var $menu = new Object();
		if(typeof(this.menuSets[menuSetNum])=='object' && this.menuSets[menuSetNum] != null){
			$options = this.menuSets[menuSetNum].$options;
			$menu = this.menuSets[menuSetNum].$menu;
		};
		
		if(showMenu){
			// if another menu options set is showing
			var c_showing = this.menuSets.menuShowing;
			if(c_showing != -1){
				if(c_showing != menuSetNum){
					
					// current
					var $c_options = this.menuSets[c_showing].$options;
					
					// hide the options
					$c_options.hide();
				
					// set flags
					this.menuSets[c_showing].editing = false;
					this.menuSets[c_showing].showing = false;
					this.menuSets.showing = -1;
				};
			};

			// doc edges
			var docRight = $(window).width() - 20;
			
			// show the options
			$options.show();
			
			// record 
			this.menuSelected = menuSetNum;
			
			// position
			var leftPos = $options.position().left;
			var rightPos = $options.outerWidth(true) + leftPos;
			var menuLeft = $menu.position().left;
			var menuRight = $menu.outerWidth(true) + menuLeft;
			
			
			if(rightPos > docRight){
				var offSet = Math.max(0, (rightPos - docRight));
				var newLeftPos = leftPos - offSet;
				$options.css('left', newLeftPos +'px');
			};
			
			// set flags
			this.menuSets[menuSetNum].showing = true;
			this.menuSets.menuShowing = menuSetNum;
			
		} else { // call to hide
		
			switch(callType){

				case 'outside': // called when clicking outside of the options / menu
					
					// if another menu options set is showing
					var c_showing = this.menuSets.menuShowing;
					if(c_showing != -1){
						if(c_showing != menuSetNum){
							
							var withinOptions = this.menuSets[c_showing].withinOptions;
							
							if(!withinOptions){
								// current
								var $c_options = this.menuSets[c_showing].$options;
								
								// hide the options
								$c_options.hide();
							
								// set flags
								this.menuSets[c_showing].editing = false;
								this.menuSets[c_showing].showing = false;
								this.menuSets.showing = -1;
							};
						};
					};
				break;
				
				case 'menu': // called by another menu
					// if another menu options set is showing
					var c_showing = this.menuSets.menuShowing;
					if(c_showing != -1){
						if(c_showing != menuSetNum){
							
							// current
							var $c_options = this.menuSets[c_showing].$options;
							
							// hide the options
							$c_options.hide();
						
							// set flags
							this.menuSets[c_showing].editing = false;
							this.menuSets[c_showing].showing = false;
							this.menuSets.showing = -1;
						};
					};
				
				break;
				
				case 'options': // called by the options of this menu
					// editing
					var editing = this.menuSets[menuSetNum].editing;
					
					// if not editing within this set of options
					if(!editing){
						// close options
						$options.hide();
						
						// set flags
						this.menuSets[menuSetNum].editing = false;
						this.menuSets[menuSetNum].showing = false;
						this.menuSets.menuShowing = -1;
					};
					
				break;	
				
			};
			
		};
	
	};
	
}


$(document).ready(function(){
	
	// set menu options
	var infoObj = new Object();
	
	infoObj.menuId = 'menu_site_menu'; // id of main menu
	infoObj.tabsId ='pageMenuTabs'; // id of tabs set by  menu
	infoObj.cookieName = 'byblioNav'; // cookie info
	
	
	// create class instance & record core info
	SiteNavigation =  new Navigation(infoObj);
	
	// initialise
	SiteNavigation.initialise();
	
});













