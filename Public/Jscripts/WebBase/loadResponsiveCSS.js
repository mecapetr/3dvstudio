//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	var CssArr = new Array('0','640','960','1200');
    $(window).resize(function(){
    	setCss(CssArr);
    });
	setCss(CssArr);  	
});
function setCss(CssArr){
	var css = '0';
   	if($('body').width() <= 480){    	
   		removeCss(CssArr);	
   		addCss(css);
   	}else if($('body').width() > 480 && $('body').width() <= 768){
   		css = '480';
   		removeCss(CssArr);
   		addCss(css);
   	}else if($('body').width() > 768 && $('body').width() <= 1024){
   		css = '768';
   		removeCss(CssArr);
   		addCss(css);
   	}else if($('body').width() > 1024 && $('body').width() <= 1280){
   		css = '1024';
   		removeCss(CssArr);
   		addCss(css);
   	}
}
function addCss(file){
	$("<link>")
	  .appendTo($('head'))
	  .attr({type : 'text/css', rel : 'stylesheet', id : 'css-'+file})
	  .attr('href', '/Public/Css/Responsive/javascript/'+file+'.css?');
}
function removeCss(cssFilesArr){
	for(var i = 0; i < cssFilesArr.length; i++){
		if($('#css-'+cssFilesArr[i]).length > 0)
			$('#css-'+cssFilesArr[i]).remove();
	}
}