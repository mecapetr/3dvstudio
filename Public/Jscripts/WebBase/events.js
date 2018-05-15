//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
    
	checkBrowser();
	$("a.link").click(function() {
     	
 	    var href = $(this).attr("href"); 	  
 	    var newWindow = window.open(href, "_blank");
 	    newWindow.focus(); 
 	    return false;
 	
 	});
	
	$(".lang a").click(function(e){
		e.preventDefault();
		var lang = $(this).attr("id");
		$.ajax({
			type: "POST",
			url: "/core/helper/language",
			data: {lang:lang},
			success: function(response){
				if(response == 1)window.location = "/"+lang;
				
			}

		});
		
	});
	
	$("body").click(function(e){
		if($(e.target).closest(".cover-color").length == 0 && $(e.target).closest(".color-numbers").length == 0 && $(e.target).closest("#fancybox-wrap").length == 0 && $(e.target).closest("#fancybox-overlay").length == 0){
			$(".color-numbers.col-xs-12").remove();
			$(".product-detail .colors .color").removeClass("selected");
		}
	});
	
				
	$("a#inline").fancybox({
		'hideOnContentClick': true
	});
	
	$(".fancy-one").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true,
		'overlayOpacity':   0.7,
		'overlayColor'  :   '#000000',
		'titlePosition' :   'inside'
	});
	
	$(".fancy-gallery").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true,
		'overlayOpacity':   0.7,
		'overlayColor'  :   '#000000',
		'titlePosition' :   'inside',
		'titleFormat'   :   function(title, currentArray, currentIndex, currentOpts) {
			return '<span class="fancybox-title-over-left"><span>Obrázek ' + (currentIndex + 1) + ' z ' + currentArray.length + '</span> - ' + title + '</span><span class="fancybox-title-over-right"><a class="link" href="http://www.1vision.cz"><img src="/Public/Jscripts/Fancybox/logo.png" /></a></span>';
	    }
	});
	
	
	
	$("a.fancy-video").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true,
		'overlayOpacity':   0.7,
		'overlayColor'  :   '#000000',
		'titlePosition' :   'inside',
		'titleFormat'   :   function(title, currentArray, currentIndex, currentOpts) {
			return '<span class="fancybox-title-over-left">' + title + '</span><span class="fancybox-title-over-right"><a class="link" href="http://www.1vision.cz"><img src="/Public/Jscripts/Fancybox/logo.png" /></a></span>';
	    }

	});
	
	setComments();
	//setSlider();
	//setDDslider();
	listElementFilters();
	nextYtvVideos();
	googleMap();
	
});

function googleMap(){
	
	if($("div.map-element").size()){
		
		$("div.map-element").each(function(index){
									
			var mID = $(this).attr("data");
			var latlng = new google.maps.LatLng(flags[mID][0][0],flags[mID][0][1]);
			
			var myOptions = {
			    zoom: 11,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map;
			map = new google.maps.Map(document.getElementById("map-"+mID),myOptions);
			
			if(flags[mID]){
			
				
				for(var m in flags[mID]){
					
					var laln = new google.maps.LatLng(flags[mID][m][0],flags[mID][m][1]);
					addFlag(map,laln,flags[mID][m][2],"");
				}
			}
			
		});
		
	}
	
}

function addFlag(map,location,title,contentString){
	var markersArray = new Array();
	var image = '/Public/Images/Layout/Content/flag.png';
		 
	var marker = new google.maps.Marker({
		    position: location,
		    map: map,
		    title:title,
		    animation: google.maps.Animation.DROP,
		    icon: image
	});
	 
	markersArray[0] = marker;
	
	markersArray[0].infowindow = new google.maps.InfoWindow({
    	content: title
	});
	
    google.maps.event.addListener(markersArray[0], 'click', function() {
    	
    	for(var k in markersArray){
    		markersArray[k].infowindow.close();
    	}
    	markersArray[0].infowindow.open(map,marker);
  		
	});
}

function nextYtvVideos(){
	
	$(".ytv-element button.next-videos").click(function(e){
		e.preventDefault();
		
		var thisElm = this;		
		var data = $(thisElm).closest(".ytv-element").find("button.dropdown-info span").attr("data").split("~");

		$.ajax({
			type: "POST",
			url: "/core/helper/get-ytv-videos",
			data: {category:data[0],linkSection:data[1],offset:data[3],priority:data[2]},
			success: function(response){
				
				response = JSON.parse(response);
				
				var newData = data[0]+"~"+data[1]+"~"+data[2]+"~"+(parseInt(data[3]) + parseInt(response[1]));
				
				$(thisElm).closest(".ytv-element").find("div.next-videos").append(response[0]);
				$(thisElm).closest(".ytv-element").find("button.dropdown-info span").attr("data",newData);
				
				if(response[1] < 6){
					$(thisElm).closest(".ytv-element").find("button.next-videos").hide();
				}
			}

		});		
		
	});
	
	$(".ytv-element .dropdown-menu li a").click(function(e){
		e.preventDefault();
		
		var thisElm = this;	
		var text    = $(thisElm).text();
		var data    = $(thisElm).closest(".ytv-element").find("button.dropdown-info span").attr("data").split("~");
		
		$(thisElm).closest(".ytv-element").find("button.dropdown-info span").text(text);

			data[0] = $(thisElm).attr("data");
			data[3] = 0;
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-ytv-videos",
			data: {category:data[0],linkSection:data[1],offset:data[3],priority:data[2]},
			success: function(response){
				
				$(thisElm).closest(".ytv-element").find("div.videos").html('<div class="next-videos"></div><div class="clearfix"></div>');
				
				response = JSON.parse(response);
				
				var newData = data[0]+"~"+data[1]+"~"+data[2]+"~"+(parseInt(data[3]) + parseInt(response[1]));
				
				$(thisElm).closest(".ytv-element").find("div.next-videos").append(response[0]);
				$(thisElm).closest(".ytv-element").find("button.dropdown-info span").attr("data",newData);
				
				if(response[1] < 6){
					$(thisElm).closest(".ytv-element").find("button.next-videos").hide();
				}else{
					$(thisElm).closest(".ytv-element").find("button.next-videos").show();
				}
			}

		});		
		
	});
}

function listElementFilters(){
	
	$(".list-element ul.dropdown-menu a").click(function(e){
		e.preventDefault();
		
		var data = $(this).attr("data");
		
		var elm = $(this).parent().parent().parent().find("button span.val");
		elm.text($(this).text());
		elm.attr("data",data);
		
		var elms = $(this).closest(".list-element").find("button span.val");
		var keywords = [];
		$.each( elms, function( key, value ) {				
			if($(value).attr("data") != 0)keywords.push($(value).attr("data"));
		});
		
		var regex = new RegExp(keywords.join("|"));
		
		var trs = $(this).closest(".list-element").find("table tr:not(.header)");
		trs.show();
		
		$.each( trs, function( key, value ) {
			
			var tdList = [];
			var hide = true;
			$.each( $(value).find("td"), function( key, valueTd ) {				
				
				tdList.push($(valueTd).text());
			});
			
			var result = $.grep(tdList, function(s) { return s.match(regex) })
			
			if(result.length != keywords.length && keywords.length > 0){
				$(this).hide();
			}
		});
		
	});
	
}

function lan(lang){
    
    $.ajax({
		type: "POST",
		url: "/core/helper/language",
		data: "lang="+lang,
		success: function(html){
            window.location = "/";   
			
		}

	});
    
	return false;
}


function addBanner(url,width,height,element){
    html = '<embed wmode="transparent" width="'+width+'" height="'+height+'" quality="high" name="mymovie" id="mymovie" style="" src="'+url+'" type="application/x-shockwave-flash"/>';
    $(element).append(html);
                
}
function isEmpty(value) {
	if ((value==null) || (value=='undefined') || (value.length==0)) {
		return true;
	}else{ return false; }
} 
function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);
}

function setComments(){
	var element;
	$('#content #sub-content article.actuality').each(function(){
	
		element = $(this).find('div.comment-bar a.add-comment');
		
		if(element.length > 0){

			element.comments({
				sendControllerPath :"/adminBase/helper/add-actuality-comment",
				loadControllerPath :"/adminBase/helper/get-actuality-comment",
	    		plusOfset: -29,
	    		appendToBody:false,
	    		width:700
			});
			
		}
	
	});
	
	$('#content #sub-content article.reference').each(function(){
	
		element = $(this).find('div.comment-bar a.add-comment');
		
		if(element.length > 0){

			element.comments({
				sendControllerPath :"/adminBase/helper/add-reference-comment",
				loadControllerPath :"/adminBase/helper/get-reference-comment",
	    		plusOfset: -29,
	    		appendToBody:false,
	    		tableID:"referenceID",
	    		width:700
			});
			
		}
	
	});
	
	$('#content #sub-content article.blog,#content #sub-content article.blog_detail').each(function(){
	
		element = $(this).find('div.comment-bar a.add-comment');
		
		if(element.length > 0){

			element.comments({
				sendControllerPath :"/adminBase/helper/add-blog-comment",
				loadControllerPath :"/adminBase/helper/get-blog-comment",
	    		plusOfset: -29,
	    		appendToBody:false,
	    		tableID:"blogID",
	    		width:700
			});
			
		}
  });
}	


function checkBrowser(){
	 
	 if(BrowserDetect.browser == "Explorer" && BrowserDetect.version < 9){
		 $('body').prepend('<div id="old-explorer">'+
					'<div class="content">'+
					'<p>'+
						'Používáte zastaralý prohlížeč, který už se běžně nepoužívá.'+
						' Pro správné zobrazení si prosím stahněte nový <a href="http://windows.microsoft.com/cs-cz/internet-explorer/download-ie" title="Internet Explorer"><strong>Internet&nbsp;Explorer</strong></a>'+
						' nebo si stáhněte jiný prohlížeč. Například <a href="http://www.google.com/intl/cs/chrome/browser/" title="Google Chrome"><strong>Google&nbsp;Chrome</strong></a> nebo <a href="http://www.mozilla.org/cs/firefox/new/" title="Mozilla Firefox"><strong>Mozilla&nbsp;Firefox</strong></a>'+
					'</p>'+
					'<div class="close">Zavřít</div>'+
				'</div>'+
			'</div>');
		 $('#old-explorer div.close').click(function(){
			 $('#old-explorer').fadeOut(200,function(){
				 $(this).remove();
			 });
		 });
		 $('#old-explorer').fadeIn(1000);
	 }
}
var BrowserDetect = {
    init: function () {
        this.browser = this.searchString(this.dataBrowser) || "Other";
        this.version = this.searchVersion(navigator.userAgent) || this.searchVersion(navigator.appVersion) || "Unknown";
    },
    searchString: function (data) {
        for (var i = 0; i < data.length; i++) {
            var dataString = data[i].string;
            this.versionSearchString = data[i].subString;

            if (dataString.indexOf(data[i].subString) !== -1) {
                return data[i].identity;
            }
        }
    },
    searchVersion: function (dataString) {
        var index = dataString.indexOf(this.versionSearchString);
        if (index === -1) {
            return;
        }

        var rv = dataString.indexOf("rv:");
        if (this.versionSearchString === "Trident" && rv !== -1) {
            return parseFloat(dataString.substring(rv + 3));
        } else {
            return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
        }
    },

    dataBrowser: [
        {string: navigator.userAgent, subString: "Chrome", identity: "Chrome"},
        {string: navigator.userAgent, subString: "MSIE", identity: "Explorer"},
        {string: navigator.userAgent, subString: "Trident", identity: "Explorer"},
        {string: navigator.userAgent, subString: "Firefox", identity: "Firefox"},
        {string: navigator.userAgent, subString: "Safari", identity: "Safari"},
        {string: navigator.userAgent, subString: "Opera", identity: "Opera"}
    ]

};
BrowserDetect.init();