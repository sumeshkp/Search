/**
 * Byblio
 * Search engine
 * Web app test page
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */






// class definition
function WebApp(cookieName){
	// record cookie name
	this.cookieName = cookieName;
};


// initialises class
WebApp.prototype.initialise = function(infoObj){
	
	// initialise persistence
	this.initialisePersistence(infoObj);
	
	// set panels
	this.initialisePanels();
	
	// ajax info
	this.initialiseAjax(infoObj);
	
	// key words
	this.initialiseKeyWords(infoObj);
	
};



// loads up and sets initial persistence settings
WebApp.prototype.initialisePersistence = function(infoObj){
	
	// settings info
	this.settings = new Object();
	
	// html info
	if(typeof(infoObj.html)=='object' && infoObj.html != null){
		this.html = infoObj.html;
	} else {
		this.html = new Object();
	};
	
	// load ui settings
	this.initialiseUiSettings(infoObj);
	
};


// updates local persistence settings
WebApp.prototype.updateLocalPersistence = function(infoObj){
	
	// ui settings
	// set date in settings
	var now = new Date();
	this.settings.uiSettings.date = now.toUTCString();
	
	// read all
	var settings = this.settings.uiSettings;
	
	// cookie name
	var cookieName = this.cookieName;
	
	// write local
	$.jStorage.set(cookieName, settings);
	
};



// key words
WebApp.prototype.initialiseKeyWords = function(infoObj){
	
	this.keyWords = new Object();
	
	if(typeof(infoObj.keyWords)== 'object' && infoObj.keyWords != null){
		this.keyWords = infoObj.keyWords;
	};
	
	
};


// loads up ajax info
WebApp.prototype.initialiseAjax = function(infoObj){
	
	this.ajax = new Object();
	
	if(typeof(infoObj.ajax)== 'object' && infoObj.ajax != null){
		this.ajax = infoObj.ajax;
	};
	if(typeof(this.ajax.search) != 'object' || this.ajax.search == null){
		this.ajax.search = new Object();
	};
	
};



// loads the most recent cookie settings (either given server side or reads client side)
WebApp.prototype.loadCookieSettings = function(infoObj){
	
	//$.jStorage.deleteKey(this.cookieName);
	
	// ui settings from server: actually, there aren't any!
	var serverUiSettings = new Object();
	if(typeof(infoObj.uiSettings)=='object' && infoObj.uiSettings != null){
		serverUiSettings = infoObj.uiSettings;
	};
	
	// date of server settings
	var serverDate = null;
	if(typeof(serverUiSettings.date)=='string'){
		serverDate = new Date(serverUiSettings.date);
	};
	
	// ui settings from client (note, uses jStorage);
	var cookieName = this.cookieName;
	var clientSettings = $.jStorage.get(cookieName);
	var clientUiSettings = new Object();
	if(typeof(clientSettings)=='object' && clientSettings != null){
		clientUiSettings = clientSettings;
	};
	
	// date of client settings
	var clientDate = null;
	if(typeof(clientUiSettings.date)=='string'){
		clientDate = new Date(clientUiSettings.date);
	};
	
	if(serverDate == null){// if  no time info for server settings
		// use client settings
		this.settings.uiSettings = clientUiSettings;
	
	} else {
		if(clientDate == null){// if  no time info for client settings
			// set the date
			clientUiSettings.date = new Date();
			
			// use client settings
			this.settings.uiSettings = clientUiSettings;
		
		} else { // have date info for both client and server
			if(clientDate >=serverDate){
				// use client settings
				this.settings.uiSettings = clientUiSettings;
			} else {
				// use server settings
				this.settings.uiSettings = serverUiSettings;
			};
		};
	};
		
};

// sets initial ui settings
WebApp.prototype.initialiseUiSettings = function(infoObj){
	
	// ui settings all info
	if(typeof(this.settings.uiSettings) != 'object'){
		this.settings.uiSettings = new Object();
	};
	
	
	// load most recent cookie settings
	this.loadCookieSettings(infoObj);
	
	
	// search form
	if(typeof(this.settings.uiSettings.search) != 'object'){
		this.settings.uiSettings.search = new Object();
	};
	
	
};




// initialises the different panels
WebApp.prototype.initialisePanels = function(){
	
	// search form
	this.initialiseSearchForm();
	
	// request
	this.initialiseRequestPanel();
	
	// results
	this.initialiseResultsPanel();
	
};

// initialises the search form
WebApp.prototype.initialiseSearchForm = function(){
	
	// ref elements
	this.search_ref();
	
	// select submit functionality & previous state
	this.search_setFormFunctionality();
	

};

// initialises the request panel
WebApp.prototype.initialiseRequestPanel = function(){
	
	// request
	this.request_ref();
	
	// clear post request info
	var inputInfo = {show: false};
	this.request_setPanel(inputInfo);
	
};

// initialises the results panel
WebApp.prototype.initialiseResultsPanel = function(){
	
	// request
	this.results_ref();
	
	// clear post request info
	var inputInfo = {show: false};
	this.results_setPanel(inputInfo);
	
	// make panel resizable
	
};





// sets the facet selection functionality
WebApp.prototype.search_setFormFunctionality = function(){
	
	// all form elements
	var allElements = this.search.form.elements;
	
	// ui settings
	var searchSettings = this.settings.uiSettings.search;
	
	// this
	var WebApp = this;
	
	if(typeof(allElements)== 'object' && allElements != null){
		
		// for each input element
		$.each(allElements, function(optionName, elementInfo){
			
			// get if element
			var $input = elementInfo.$input;
			
			// type
			var optionType = elementInfo.optionType;
			
			// ui info
			if(typeof(searchSettings[optionName])!='object' || searchSettings[optionName] == null){
				searchSettings[optionName] = new Object();
			};
			
			switch(optionType){
			
				case 'checked':
				
					// calc if previously selected
					var selected = false;
					if(typeof(searchSettings[optionName].selected)== 'boolean'){
						// previous state
						selected = searchSettings[optionName].selected;
					} else {
						// no previous info, so get current state
						selected = $input.prop('checked');
						
						// record state
						searchSettings[optionName].selected = selected;
					};
					
					// set state
					$input.prop('checked', selected);
					
					
					// act on click
					var infoObj = new Object();
					infoObj.WebApp = WebApp;
					infoObj.elementInfo = elementInfo;
					
					$input.click({infoObj: infoObj}, function(e){
						
						// ref info
						var infoObj = e.data.infoObj;
						var WebApp = infoObj.WebApp;
						var elementInfo = infoObj.elementInfo;
						
						// selected
						var selected = $(this).prop('checked');
						elementInfo.selected = selected
						
						// act
						WebApp.search_setFormSelectOption(elementInfo);
					});
					
				break;
				
				case 'text':
					
					// previous value
					if(typeof(searchSettings[optionName].text)== 'string'){
						// previous value
						var pValue = searchSettings[optionName].text;
						// set text
						$input.val(pValue);
					};
					
					// act on blur
					var infoObj = new Object();
					infoObj.WebApp = WebApp;
					infoObj.elementInfo = elementInfo;
					
					$input.blur({infoObj: infoObj}, function(e){
						
						// ref info
						var infoObj = e.data.infoObj;
						var WebApp = infoObj.WebApp;
						var elementInfo = infoObj.elementInfo;
						
						// value
						var text = $(this).val();
						elementInfo.text = text
						
						// act
						WebApp.search_setFormTextOption(elementInfo);
					});

				break;
				
			}
		});
	
		
	};
	
	// form submit
	$searchForm = this.search.form.$searchForm;
	
	// act on submit
	var infoObj = new Object();
	infoObj.WebApp = WebApp;
	$searchForm.submit({infoObj:infoObj}, function(e){
		
		// ref info
		var infoObj = e.data.infoObj;
		var WebApp = infoObj.WebApp;
		
		// act
		WebApp.search_submit();
		
		// stop form submitting to php
		return false;
	});
	
};


// acts when search form submitted
WebApp.prototype.search_submit = function(infoObj){

	// set post info
	var postHTML = this.request_setPostInfo();
	var postInfo = new Object();
	postInfo.postHTML = postHTML;
	
	
	// clear post request info
	var inputInfo = new Object();
	inputInfo.show_posted = true;
	inputInfo.show_received = false;
	inputInfo.show_interpretted = false;
	this.request_setPanel(inputInfo);
	
	// set progress indicator
	
	// call
	this.search_submitRequest(postInfo);
	
};


// displays the results of the search query
WebApp.prototype.search_processResponse = function(infoObj){
	
	if(typeof(infoObj)=='object' && infoObj != null){
		
		// post info
		var postInfo = infoObj.postInfo;
		if(typeof(postInfo)=='object' && postInfo != null){
			var postHTML = postInfo.postHTML;
			
			// if have html of the post info
			if(typeof(postHTML)=='string'){
				
				// set html
				var $info = this.request.posted.$info;
				$info.html(postHTML);
				
			};
		};
		
		
		// response success & message
		var success = infoObj.success;
		var message = infoObj.message;
		
		if(!success){
			// message
			var requestFailedStr = this.keyWords.requestFailed;
			//requestFailed
			var messageStr = "<div class='error'><div>" +requestFailedStr +"</div><div>" +message +"</div>";
			
			// received info div - use for failure message if request failed
			var $info = this.request.received.$info;
			$info.html(messageStr);
		
		} else {
			// received  info
			var reveivedHTML = message.received;
			var $info = this.request.received.$info;
			
			if(typeof(reveivedHTML)=='string'){
				$info.html(reveivedHTML);
			} else {
				var messageStr = "<div class='error'>Received response not text</div>"
			};
			
			// search interpretation  info
			var searchHTML = message.search;
			var $info = this.request.interpretted.$info
			
			if(typeof(searchHTML)=='string'){
				$info.html(searchHTML);
			} else {
				var messageStr = "<div class='error'>Search response not text</div>"
			};
			
			// search results info
			var resultsHTML = message.results;
			var $info = this.results.$info;
			
			if(typeof(resultsHTML)=='string'){
				$info.html(resultsHTML);
			} else {
				var messageStr = "<div class='error'>Results response not text</div>"
			};
		};
		
		
	
		// show
		var inputInfo = new Object();
		inputInfo.show_posted = true;
		inputInfo.show_received = true;
		inputInfo.show_interpretted = true;
		this.request_setPanel(inputInfo);
		
		
	};
	
};


// gets the search form variables
WebApp.prototype.search_getFormValues = function(infoObj){
	
	// return info
	var returnInfo = new Object();
	
	// key words
	var trueStr = this.keyWords.trueStr;
	var falseStr = this.keyWords.falseStr;
	
	// search form elements
	var allElements = this.search.form.elements;
	
	$.each(allElements, function(optionName, elementInfo){
		
		// re input
		var $input = elementInfo.$input;
		
		// name
		var varName = elementInfo.optionName;
		
		// type
		var optionType = elementInfo.optionType; 
			
		var value = 'unknown';
		if($input.length>0){
			// value
			value = $input.val();
			
			switch(optionType){
				case 'checked':
					// get current state
					selected = $input.prop('checked');
					
					if(selected){
						value = trueStr;
					} else {
						value = falseStr;
					};
					
					// record
					returnInfo[varName] = value;
					
				break;
				
				case 'text':
				case 'hidden':
					// record
					returnInfo[varName] = value;
					
				break;
				
				default:
					// nothing
				break;
				
			};
			
			
		};
		
	});
	
	// return
	return returnInfo;
	
};


// makes ajax call to server
WebApp.prototype.search_submitRequest = function(postInfo){

	// get post variables
	var searchVars = this.search_getFormValues();
	
	// ajax info
	var ajaxUrl = this.ajax.search.url;
	var requestType = this.ajax.search.requestType;
	
	var ajaxInfoObj = new Object();
	if(typeof(searchVars)=='object' && searchVars != null){
		ajaxInfoObj = searchVars;
	}
	// record type
	ajaxInfoObj.requestType = requestType;
	
	
	// create ajax call to server
	this.ajax.search.ajax = $.ajax({
		 type: "post",
		 url: ajaxUrl,
		 data: ajaxInfoObj,
		 dataType: "json",
		 cache: false,
		 WebApp: this,
		 postInfo: postInfo,
	 });

	// set up response action
	this.ajax.search.ajax.done(function(returnObj){
		
		var WebApp = this.WebApp;
		var postInfo = this.postInfo;
		
		if(typeof(returnObj)=='object' && returnObj != null){
			
			// update info
			returnObj.postInfo = postInfo;
			WebApp.search_processResponse(returnObj);
		
		} else {
			
			// update info
			var infoObj = new Object();
			infoObj.success = false;
			infoObj.postInfo = postInfo;
			WebApp.search_processResponse(infoObj);
		}
		
	});
	 
	 // error action
	this.ajax.search.ajax.fail(function(jqXHR, textStatus, errorThrown){
		
		var WebApp = this.WebApp;
		var postInfo = this.postInfo;
		
		// update info
		var infoObj = new Object();
		infoObj.success = false;
		infoObj.message = 'Ajax request failed';
		infoObj.postInfo = postInfo;
		WebApp.search_processResponse(infoObj);
		
	 });
	
	
	
	
};


// sets post request info
WebApp.prototype.request_setPanel = function(infoObj){
	
	// info panels
	var $panel_posted = this.request.posted.$panel;
	var $panel_received = this.request.received.$panel;
	var $panel_interpretted = this.request.interpretted.$panel;
	
	// show / clear elements
	var show_posted = false;
	if(typeof(infoObj)=='object' && infoObj != null){
		show_posted = infoObj.show_posted;
	};
	var show_received = false;
	if(typeof(infoObj)=='object' && infoObj != null){
		show_received = infoObj.show_received;
	};
	var show_interpretted = false;
	if(typeof(infoObj)=='object' && infoObj != null){
		show_interpretted = infoObj.show_interpretted;
	};
	

	// show hide panels
	if(show_posted){
		// show panel
		$panel_posted.show();
	} else {
		// hide panel
		$panel_posted.hide();
	};
	// show hide panels
	if(show_received){
		$panel_received.show();
	} else {
		$panel_received.hide();
	};
	// show hide panels
	if(show_interpretted){
		$panel_interpretted.show();
	} else {
		$panel_interpretted.hide();
	};
	
};




// sets the posted info in the request panel
WebApp.prototype.request_setPostInfo = function(infoObj){
	
	// key words
	var trueStr = this.keyWords.trueStr;
	var falseStr = this.keyWords.falseStr;
	var emptyStr = this.keyWords.emptyString;
	
	// html
	var allInfoHTML = "";
	
	// search form elements
	var allElements = this.search.form.elements;
	
	$.each(allElements, function(optionName, elementInfo){
		
		// re input
		var $input = elementInfo.$input;
		
		// name
		var varName = elementInfo.optionName;
		
		// type
		var optionType = elementInfo.optionType; 
			
		var value = 'unknown';
		if($input.length>0){
			// value
			value = $input.val();
			
			switch(optionType){
				case 'checked':
					// get current state
					selected = $input.prop('checked');
					
					if(selected){
						value = trueStr;
					} else {
						value = falseStr;
					};
					
					// row info
					$row1Str = "<div><span class='name'>" +varName +":</span> <span class='value'>" +value +"</span></div>";
					
				break;
				
				case 'text':
				case 'hidden':
					if(value == ""){
						value = "<span class='error'>" +emptyStr +"</span>";
					};
					
					// row info
					$row1Str = "<div><span class='name'>" +varName +":</span> <span class='value'>" +value +"</span></div>";
					
				break;
				
				default:
					$row1Str = "";
				break;
				
			};
			
			
		};
		

		// add
		allInfoHTML += $row1Str;
		
	});
	
	// set
	var $info = this.request.posted.$info;
	$info.html(allInfoHTML);
	
	// return
	return allInfoHTML;
};





// sets results info
WebApp.prototype.results_setPanel = function(infoObj){
	
	// panel
	$panel = this.results.$panel;
	
	// show / clear
	var show = false;
	if(typeof(infoObj)=='object' && infoObj != null){
		show = infoObj.show;
	};
	
	if(show){
		
		
		
		
	} else {
		
	}
	
};



// acts on user select search option 
WebApp.prototype.search_setFormSelectOption = function(infoObj){
	
	if(typeof(infoObj)=='object' && infoObj != null){
		
		// option name
		var optionName = infoObj.optionName;
		
		// selected
		var selected = infoObj.selected;
		
		// update ui settings
		var searchSettings = this.settings.uiSettings.search[optionName];
		
		// record
		searchSettings.selected = selected;
		
		// update local
		this.updateLocalPersistence();
	};
	
};

// acts on user sets form text  option 
WebApp.prototype.search_setFormTextOption = function(infoObj){
	
	if(typeof(infoObj)=='object' && infoObj != null){
		
		// option name
		var optionName = infoObj.optionName;
		
		// text
		var text = infoObj.text; 
		
		// update ui settings
		var searchSettings = this.settings.uiSettings.search[optionName];
		
		// record
		searchSettings.text = text;
		
		// update local
		this.updateLocalPersistence();
	};
	
};


//updates local persistence settings
WebApp.prototype.updateLocalPersistence = function(infoObj){
	
	// ui settings
	// set date in settings
	var now = new Date();
	this.settings.uiSettings.date = now.toUTCString();
	
	// read all
	var settings = this.settings.uiSettings;
	
	// cookie name
	var cookieName = this.cookieName;
	
	// write local
	$.jStorage.set(cookieName, settings);
	
};


//references the post request panel
WebApp.prototype.request_ref = function(){
	
	// info
	this.request = new Object();
	
	// html info, signup form
	var searchInfo = new Object();
	if(typeof(this.html.searchForm) =='object' && this.html.searchForm != null){
		searchInfo = this.html.searchForm;
	};
	
	// id list
	var idList = new Object();
	if(typeof(searchInfo.idList) =='object' && searchInfo.idList != null){
		idList = searchInfo.idList;
	};
	

	// class list
	var classList = new Object();
	if(typeof(searchInfo.classList) =='object' && searchInfo.classList != null){
		classList = searchInfo.classList;
	};
	
	// request panel (all divs plus form)
	var panelId = "";
	if(typeof(idList.requestPanel)=='string'){
		panelId = idList.requestPanel;
	};
	
	var $panel = $('#' +panelId);
	// record
	this.request.$panel = $panel;
	
	if($panel.length>0){
		
		// class of info
		var infoC = "-";
		if(typeof(classList.info)=='string'){
			infoC = classList.info;
		};
		
		// core sections:
		
		// 1: posted
		this.request.posted = new Object();
		
		// class of div containing posted info
		var postedC = "-";
		if(typeof(classList.posted)=='string'){
			postedC = classList.posted;
		};
		// heading and info div
		var $panel_all = $panel.find('.' +postedC);
		this.request.posted.$panel = $panel_all;
		
		// info div
		if($panel_all.length>0){
			var $info = $panel_all.find('.' +infoC);
			this.request.posted.$info = $info;
		};
		
		
		// 2: received
		this.request.received = new Object();
		
		// class of div containing posted info
		var receivedC = "-";
		if(typeof(classList.received)=='string'){
			receivedC = classList.received;
		};
		// heading and info div
		var $panel_all = $panel.find('.' +receivedC);
		this.request.received.$panel = $panel_all;
		
		// info div
		if($panel_all.length>0){
			var $info = $panel_all.find('.' +infoC);
			this.request.received.$info = $info;
		};
		
		
		// 3: interpretted
		this.request.interpretted = new Object();
		
		// class of div containing posted info
		var interprettedC = "-";
		if(typeof(classList.interpretted)=='string'){
			interprettedC = classList.interpretted;
		};
		// heading and info div
		var $panel_all = $panel.find('.' +interprettedC);
		this.request.interpretted.$panel = $panel_all;
		
		// info div
		if($panel_all.length>0){
			var $info = $panel_all.find('.' +infoC);
			this.request.interpretted.$info = $info;
		};
		
	};
};


//references the post request panel
WebApp.prototype.results_ref = function(){
	
	// info
	this.results = new Object();
	
	// html info, signup form
	var searchInfo = new Object();
	if(typeof(this.html.searchForm) =='object' && this.html.searchForm != null){
		searchInfo = this.html.searchForm;
	};
	
	// id list
	var idList = new Object();
	if(typeof(searchInfo.idList) =='object' && searchInfo.idList != null){
		idList = searchInfo.idList;
	};
	
	// class list
	var classList = new Object();
	if(typeof(searchInfo.classList) =='object' && searchInfo.classList != null){
		classList = searchInfo.classList;
	};
	
	// class of info
	var infoC = "-";
	if(typeof(classList.info)=='string'){
		infoC = classList.info;
	};
	
	// results panel (all divs plus form)
	var panelId = "";
	if(typeof(idList.resultsPanel)=='string'){
		panelId = idList.resultsPanel;
	};
	
	var $panel = $('#' +panelId);
	// record
	this.results.$panel = $panel;
	
	// info div
	if($panel.length>0){
		var $info = $panel.find('.' +infoC);
		this.results.$info = $info;
	};
	
};



// references the search panel and form 
WebApp.prototype.search_ref = function(){
	
	// info
	this.search = new Object();
	this.search.form = new Object();
	
	// list of elements
	this.search.form.elements = new Object();

	
	// html info, signup form
	var searchInfo = new Object();
	if(typeof(this.html.searchForm) =='object' && this.html.searchForm != null){
		searchInfo = this.html.searchForm;
	};
	
	// id list
	var idList = new Object();
	if(typeof(searchInfo.idList) =='object' && searchInfo.idList != null){
		idList = searchInfo.idList;
	};
	
	// class list
	var classList = new Object();
	if(typeof(searchInfo.classList) =='object' && searchInfo.classList != null){
		classList = searchInfo.classList;
	};
	
	// element name list
	var elementList = new Object();
	if(typeof(searchInfo.elementList) =='object' && searchInfo.elementList != null){
		elementList = searchInfo.elementList;
	};
	
	//data list
	var dataList = new Object();
	if(typeof(searchInfo.dataList) =='object' && searchInfo.dataList != null){
		dataList = searchInfo.dataList;
	};
	
	// option data
	var data_optionType = "-";
	if(typeof(dataList.optionType)=='string'){
		data_optionType = dataList.optionType;
	};
	
	
	// search panel (all divs plus form)
	var panelId = "";
	if(typeof(idList.searchPanel)=='string'){
		panelId = idList.searchPanel;
	};
	
	var $panel = $('#' +panelId);
	// record
	this.search.panel = $panel;
	
	
	// form name
	var formName = searchInfo.formName;
	
	// form
	var $searchForm = $panel.find('form[name=' +formName +']');
	this.search.form.$searchForm = $searchForm;
	
	// ref this
	var WebApp = this;
	
	// if have form
	if($searchForm.length>0){
		
		$.each(elementList, function(elementIndex, elementInfo){
			
			if(typeof(elementInfo)== 'object' && elementInfo != null){
				
				// name of input
				var inputName = elementInfo.name;
				
				// type
				var elType = elementInfo.type;
				
				// option name
				var optionName = elementIndex;
				
				
				// ref element
				var $input = $searchForm.find('input[name=' +inputName +']');
				
				// if more than one element for this input, this is a group set
				if($input.length>1){
					
					// for each sub input
					$.each($input, function(inputIndex, inputEl){
						
						// input
						var $inputEl = $(inputEl);
						
						// value
						var inputVal = $inputEl.val();
						
						// set option name
						var optionName = elementIndex +"-" + inputVal;
						
						// data
						var dataInfo = $inputEl.data();
						
						var optionType = ""; // default
						if(typeof(dataInfo)=='object' && dataInfo != null){
							optionType = dataInfo[data_optionType];
						};
						
						
						// info obj
						WebApp.search.form.elements[optionName] = new Object();
						
						// record
						WebApp.search.form.elements[optionName].optionName = optionName;
						WebApp.search.form.elements[optionName].name = inputName;
						WebApp.search.form.elements[optionName].type = elType;
						WebApp.search.form.elements[optionName].$input = $inputEl;
						WebApp.search.form.elements[optionName].optionType = optionType;
					});
					
					
				} else {

					// data
					var dataInfo = $input.data();
					
					var optionType = ""; // default
					if(typeof(dataInfo)=='object' && dataInfo != null){
						optionType = dataInfo[data_optionType];
					};
					
					// info obj
					WebApp.search.form.elements[optionName] = new Object();
					
					// record
					WebApp.search.form.elements[optionName].optionName = optionName;
					WebApp.search.form.elements[optionName].name = inputName;
					WebApp.search.form.elements[optionName].type = elType;
					WebApp.search.form.elements[optionName].$input = $input;
					WebApp.search.form.elements[optionName].optionType = optionType;
				};
				
			};	
		});
		
		
	};
	
	
};





$(document).ready(function(){
		
	// read initialisation info
	var infoObj = ByblioSearchInfo;
	
	if(typeof(infoObj)!= 'object' || infoObj == null){
		infoObj = new Object();
	};
	
	// cookie name
	var cookieName = 'test-searchWebApp';
	if(typeof(infoObj.cookieName)=='string'){
		cookieName = infoObj.cookieName;
	}
	
	// create class instance
	WebApp = new WebApp(cookieName);
	
	// initialise
	WebApp.initialise(infoObj);
	
});













