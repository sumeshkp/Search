/**
 * Byblio
 * Search engine
 * CMS test page
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */






// class definition
function CMS(cookieName){
	// record cookie name
	this.cookieName = cookieName;
};


// initialises class
CMS.prototype.initialise = function(infoObj){
	
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
CMS.prototype.initialisePersistence = function(infoObj){
	
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
CMS.prototype.updateLocalPersistence = function(infoObj){
	
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
CMS.prototype.initialiseKeyWords = function(infoObj){
	
	this.keyWords = new Object();
	
	if(typeof(infoObj.keyWords)== 'object' && infoObj.keyWords != null){
		this.keyWords = infoObj.keyWords;
	};
	
	
};


// loads up ajax info
CMS.prototype.initialiseAjax = function(infoObj){
	
	this.ajax = new Object();
	
	if(typeof(infoObj.ajax)== 'object' && infoObj.ajax != null){
		this.ajax = infoObj.ajax;
	};
	if(typeof(this.ajax.add) != 'object' || this.ajax.add == null){
		this.ajax.add = new Object();
	};
	
};



// loads the most recent cookie settings (either given server side or reads client side)
CMS.prototype.loadCookieSettings = function(infoObj){
	
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
CMS.prototype.initialiseUiSettings = function(infoObj){
	
	// ui settings all info
	if(typeof(this.settings.uiSettings) != 'object'){
		this.settings.uiSettings = new Object();
	};
	
	
	// load most recent cookie settings
	this.loadCookieSettings(infoObj);
	
	
	// add form
	if(typeof(this.settings.uiSettings.add) != 'object'){
		this.settings.uiSettings.add = new Object();
	};
	
	
};




// initialises the different panels
CMS.prototype.initialisePanels = function(){
	
	// add form
	this.initialiseMainForm();
	
	// request
	this.initialiseRequestPanel();
	
	// results
	this.initialiseResultsPanel();
	
};

// initialises the add form
CMS.prototype.initialiseMainForm = function(){
	
	// ref elements
	this.add_ref();
	
	// select submit functionality & previous state
	this.add_setFormFunctionality();
	

};

// initialises the request panel
CMS.prototype.initialiseRequestPanel = function(){
	
	// request
	this.request_ref();
	
	// clear post request info
	var inputInfo = {show: false};
	this.request_setPanel(inputInfo);
	
};

// initialises the results panel
CMS.prototype.initialiseResultsPanel = function(){
	
	// request
	this.results_ref();
	
	// clear post request info
	var inputInfo = {show: false};
	this.results_setPanel(inputInfo);
	
	// make panel resizable
	
};





// sets the facet selection functionality
CMS.prototype.add_setFormFunctionality = function(){
	
	// all form elements
	var allElements = this.add.form.elements;
	
	// ui settings
	var addSettings = this.settings.uiSettings.add;
	
	// this
	var CMS = this;
	
	if(typeof(allElements)== 'object' && allElements != null){
		
		// for each input element
		$.each(allElements, function(optionName, elementInfo){
			
			// get if element
			var $input = elementInfo.$input;
			
			// type
			var optionType = elementInfo.optionType;
			
			// ui info
			if(typeof(addSettings[optionName])!='object' || addSettings[optionName] == null){
				addSettings[optionName] = new Object();
			};
			
			switch(optionType){
			
				case 'checked':
				
					// calc if previously selected
					var selected = false;
					if(typeof(addSettings[optionName].selected)== 'boolean'){
						// previous state
						selected = addSettings[optionName].selected;
					} else {
						// no previous info, so get current state
						selected = $input.prop('checked');
						
						// record state
						addSettings[optionName].selected = selected;
					};
					
					// set state
					$input.prop('checked', selected);
					
					
					// act on click
					var infoObj = new Object();
					infoObj.CMS = CMS;
					infoObj.elementInfo = elementInfo;
					
					$input.click({infoObj: infoObj}, function(e){
						
						// ref info
						var infoObj = e.data.infoObj;
						var CMS = infoObj.CMS;
						var elementInfo = infoObj.elementInfo;
						
						// selected
						var selected = $(this).prop('checked');
						elementInfo.selected = selected
						
						// act
						CMS.add_setFormSelectOption(elementInfo);
					});
					
				break;
				
				case 'text':
					
					// previous value
					if(typeof(addSettings[optionName].text)== 'string'){
						// previous value
						var pValue = addSettings[optionName].text;
						// set text
						$input.val(pValue);
					};
					
					// act on blur
					var infoObj = new Object();
					infoObj.CMS = CMS;
					infoObj.elementInfo = elementInfo;
					
					$input.blur({infoObj: infoObj}, function(e){
						
						// ref info
						var infoObj = e.data.infoObj;
						var CMS = infoObj.CMS;
						var elementInfo = infoObj.elementInfo;
						
						// value
						var text = $(this).val();
						elementInfo.text = text
						
						// act
						CMS.add_setFormTextOption(elementInfo);
					});

				break;
				
			}
		});
	
		
	};
	
	// form submit
	$mainForm = this.add.form.$mainForm;
	
	// act on submit
	var infoObj = new Object();
	infoObj.CMS = CMS;
	$mainForm.submit({infoObj:infoObj}, function(e){
		
		// ref info
		var infoObj = e.data.infoObj;
		var CMS = infoObj.CMS;
		
		// act
		CMS.add_submit();
		
		// stop form submitting to php
		return false;
	});
	
};


// acts when add form submitted
CMS.prototype.add_submit = function(infoObj){

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
	this.add_submitRequest(postInfo);
	
};


// displays the results of the add query
CMS.prototype.add_processResponse = function(infoObj){
	
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
			
			// add interpretation  info
			var addHTML = message.add;
			var $info = this.request.interpretted.$info
			
			if(typeof(addHTML)=='string'){
				$info.html(addHTML);
			} else {
				var messageStr = "<div class='error'>Search response not text</div>"
			};
			
			// add results info
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


// gets the add form variables
CMS.prototype.add_getFormValues = function(infoObj){
	
	// return info
	var returnInfo = new Object();
	
	// key words
	var trueStr = this.keyWords.trueStr;
	var falseStr = this.keyWords.falseStr;
	
	// add form elements
	var allElements = this.add.form.elements;
	
	$.each(allElements, function(optionName, elementInfo){
		
		// re input
		var $input = elementInfo.$input;
		
		// name
		var varName = elementInfo.variableName;
		
		// type
		var type = elementInfo.type; 
			
		var value = 'unknown';
		if($input.length>0){
			// value
			value = $input.val();
			
			switch(type){
				case 'radio':
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
				case 'textarea':
				case 'select-one':
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
CMS.prototype.add_submitRequest = function(postInfo){

	// get post variables
	var addVars = this.add_getFormValues();
	
	// ajax info
	var ajaxUrl = this.ajax.add.url;
	var requestType = this.ajax.add.requestType;
	
	var ajaxInfoObj = new Object();
	if(typeof(addVars)=='object' && addVars != null){
		ajaxInfoObj = addVars;
	}
	// record type
	ajaxInfoObj.requestType = requestType;
	
	
	// create ajax call to server
	this.ajax.add.ajax = $.ajax({
		 type: "post",
		 url: ajaxUrl,
		 data: ajaxInfoObj,
		 dataType: "json",
		 cache: false,
		 CMS: this,
		 postInfo: postInfo,
	 });

	// set up response action
	this.ajax.add.ajax.done(function(returnObj){
		
		var CMS = this.CMS;
		var postInfo = this.postInfo;
		
		if(typeof(returnObj)=='object' && returnObj != null){
			
			// update info
			returnObj.postInfo = postInfo;
			CMS.add_processResponse(returnObj);
		
		} else {
			
			// update info
			var infoObj = new Object();
			infoObj.success = false;
			infoObj.postInfo = postInfo;
			CMS.add_processResponse(infoObj);
		}
		
	});
	 
	 // error action
	this.ajax.add.ajax.fail(function(jqXHR, textStatus, errorThrown){
		
		var CMS = this.CMS;
		var postInfo = this.postInfo;
		
		// update info
		var infoObj = new Object();
		infoObj.success = false;
		infoObj.message = 'Ajax request failed';
		infoObj.postInfo = postInfo;
		CMS.add_processResponse(infoObj);
		
	 });
	
	
	
	
};


// sets post request info
CMS.prototype.request_setPanel = function(infoObj){
	
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
CMS.prototype.request_setPostInfo = function(infoObj){
	
	// key words
	var trueStr = this.keyWords.trueStr;
	var falseStr = this.keyWords.falseStr;
	var emptyStr = this.keyWords.emptyString;
	
	// html
	var allInfoHTML = "";
	
	// add form elements
	var allElements = this.add.form.elements;
	
	$.each(allElements, function(optionName, elementInfo){
		
		// re input
		var $input = elementInfo.$input;
		
		// name
		var varName = elementInfo.variableName;
		
		// type
		var elType = elementInfo.type; 
			
		var value = 'unknown';
		if($input.length>0){
			// value
			value = $input.val();
			
			switch(elType){
				case 'select-one':
					// get current state
					value = $input.val();
					
					// row info
					$row1Str = "<div><span class='name'>" +varName +":</span> <span class='value'>" +value +"</span></div>";
					
				break;
				
				case 'text':
					if(value == ""){
						value = "<span class='error'>" +emptyStr +"</span>";
					};
					
					// row info
					$row1Str = "<div><span class='name'>" +varName +":</span> <span class='value'>" +value +"</span></div>";
					
				break;
				case 'textarea':
					if(value == ""){
						var valueStr = "<span class='error'>" +emptyStr +"</span>";
					} else {
						// take first 10 words
						var manNumWords = 10;
						var valueStr = "";
						var split = value.split(" ");
						var maxNum = Math.min(manNumWords +1, split.length);
						for(var i=0; i< maxNum; i++){
							var word = split[i];
							valueStr += word +" ";
						};
						if(split.length > manNumWords){
							valueStr += '...';	
						};
					};
					
					// row info
					$row1Str = "<div><span class='name'>" +varName +":</span> <span class='value'>" +valueStr +"</span></div>";
					
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
CMS.prototype.results_setPanel = function(infoObj){
	
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



// acts on user select add option 
CMS.prototype.add_setFormSelectOption = function(infoObj){
	
	if(typeof(infoObj)=='object' && infoObj != null){
		
		// option name
		var optionName = infoObj.optionName;
		
		// selected
		var selected = infoObj.selected;
		
		// update ui settings
		var addSettings = this.settings.uiSettings.add[optionName];
		
		// record
		addSettings.selected = selected;
		
		// update local
		this.updateLocalPersistence();
	};
	
};

// acts on user sets form text  option 
CMS.prototype.add_setFormTextOption = function(infoObj){
	
	if(typeof(infoObj)=='object' && infoObj != null){
		
		// option name
		var optionName = infoObj.optionName;
		
		// text
		var text = infoObj.text; 
		
		// update ui settings
		var addSettings = this.settings.uiSettings.add[optionName];
		
		// record
		addSettings.text = text;
		
		// update local
		this.updateLocalPersistence();
	};
	
};


//updates local persistence settings
CMS.prototype.updateLocalPersistence = function(infoObj){
	
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
CMS.prototype.request_ref = function(){
	
	// info
	this.request = new Object();
	
	// html info, signup form
	var addInfo = new Object();
	if(typeof(this.html.addForm) =='object' && this.html.addForm != null){
		addInfo = this.html.addForm;
	};
	
	// id list
	var idList = new Object();
	if(typeof(addInfo.idList) =='object' && addInfo.idList != null){
		idList = addInfo.idList;
	};
	

	// class list
	var classList = new Object();
	if(typeof(addInfo.classList) =='object' && addInfo.classList != null){
		classList = addInfo.classList;
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
CMS.prototype.results_ref = function(){
	
	// info
	this.results = new Object();
	
	// html info, signup form
	var addInfo = new Object();
	if(typeof(this.html.addForm) =='object' && this.html.addForm != null){
		addInfo = this.html.addForm;
	};
	
	// id list
	var idList = new Object();
	if(typeof(addInfo.idList) =='object' && addInfo.idList != null){
		idList = addInfo.idList;
	};
	
	// class list
	var classList = new Object();
	if(typeof(addInfo.classList) =='object' && addInfo.classList != null){
		classList = addInfo.classList;
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



// references the add panel and form 
CMS.prototype.add_ref = function(){
	
	// info
	this.add = new Object();
	this.add.form = new Object();
	
	// list of elements
	this.add.form.elements = new Object();

	
	// html info, signup form
	var addInfo = new Object();
	if(typeof(this.html.addForm) =='object' && this.html.addForm != null){
		addInfo = this.html.addForm;
	};
	
	// id list
	var idList = new Object();
	if(typeof(addInfo.idList) =='object' && addInfo.idList != null){
		idList = addInfo.idList;
	};
	
	// class list
	var classList = new Object();
	if(typeof(addInfo.classList) =='object' && addInfo.classList != null){
		classList = addInfo.classList;
	};
	
	//data list
	var dataList = new Object();
	if(typeof(addInfo.dataList) =='object' && addInfo.dataList != null){
		dataList = addInfo.dataList;
	};
	
	// option data
	var data_optionType = "-";
	if(typeof(dataList.optionType)=='string'){
		data_optionType = dataList.optionType;
	};
	// variable name data
	var data_variableName = "-";
	if(typeof(dataList.variableName)=='string'){
		data_variableName = dataList.variableName;
	};
	
	
	// add panel (all divs plus form)
	var panelId = "";
	if(typeof(idList.addPanel)=='string'){
		panelId = idList.addPanel;
	};
	
	var $panel = $('#' +panelId);
	// record
	this.add.panel = $panel;
	
	
	// form name
	var formName = addInfo.formName;
	
	// form
	var $mainForm = $panel.find('form[name=' +formName +']');
	this.add.form.$mainForm = $mainForm;
	
	// all input in the form
	var $allInputs = $mainForm.find(":input");
	
	// ref this
	var CMS = this;
	
	// if have form
	if($mainForm.length>0){
		
		$.each($allInputs, function(index, inputDiv){
				
			// input
			var $input = $(inputDiv);
			
			// name of input
			var inputName = $input.prop('name');
			
			// type
			var elType = $input.prop('type');
			
			// option name
			var optionName = inputName;
			
			// if more than one element for this input, this is a group set
			if($input.length>1){
				
				// for each sub input
				$.each($input, function(inputIndex, inputEl){
					
					// input
					var $inputEl = $(inputEl);
					
					// value
					var inputVal = $inputEl.val();
					
					// set option name
					var optionName = inputName +"-" + inputVal;
					
					// data
					var dataInfo = $inputEl.data();
					
					var optionType = ""; // default
					var variableName = ""; // default
					if(typeof(dataInfo)=='object' && dataInfo != null){
						optionType = dataInfo[data_optionType];
						variableName = dataInfo[data_variableName];
					};
					
					
					// info obj
					CMS.add.form.elements[optionName] = new Object();
					
					// record
					CMS.add.form.elements[optionName].optionName = optionName;
					CMS.add.form.elements[optionName].name = inputName;
					CMS.add.form.elements[optionName].type = elType;
					CMS.add.form.elements[optionName].$input = $inputEl;
					CMS.add.form.elements[optionName].optionType = optionType;
					CMS.add.form.elements[optionName].variableName = variableName;
				});
				
				
			} else {

				// data
				var dataInfo = $input.data();
				
				var optionType = ""; // default
				var variableName = ""; // default
				if(typeof(dataInfo)=='object' && dataInfo != null){
					optionType = dataInfo[data_optionType];
					variableName = dataInfo[data_variableName];
				};
				
				// info obj
				CMS.add.form.elements[optionName] = new Object();
				
				// record
				CMS.add.form.elements[optionName].optionName = optionName;
				CMS.add.form.elements[optionName].name = inputName;
				CMS.add.form.elements[optionName].type = elType;
				CMS.add.form.elements[optionName].$input = $input;
				CMS.add.form.elements[optionName].optionType = optionType;
				CMS.add.form.elements[optionName].variableName = variableName;
			};
					
		});
		
		
	};
	
	
};





$(document).ready(function(){
		
	// read initialisation info
	var infoObj = ByblioContentAddInfo;
	
	if(typeof(infoObj)!= 'object' || infoObj == null){
		infoObj = new Object();
	};
	
	// cookie name
	var cookieName = 'test-addCMS';
	if(typeof(infoObj.cookieName)=='string'){
		cookieName = infoObj.cookieName;
	}
	
	// create class instance
	CMS = new CMS(cookieName);
	
	// initialise
	CMS.initialise(infoObj);
	
});













