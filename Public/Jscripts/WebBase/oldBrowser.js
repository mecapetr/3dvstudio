//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	setCloseOldBrowserEvents();  	
});

function setCloseOldBrowserEvents(){	
	var cookieVal = $.cookie("closedOldBrowser");
	console.log(cookieVal);
	if(isEmpty(cookieVal)){
		$('div#old-explorer').fadeIn();
	}
	$('div#old-explorer div.content div.close').click(function(){
		$('div#old-explorer').fadeOut();
		$.cookie("closedOldBrowser",1);
	});
}