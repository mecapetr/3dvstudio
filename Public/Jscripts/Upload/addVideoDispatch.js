$(document).ready(function() {
      setTimeout(function () {      
			$("#video-uploadify").uploadify({
				'swf'       : '/Public/Jscripts/Upload/uploadify.swf',
				'uploader'         : '/core/helper/video-uploadify',
				'cancelImg'      : '/Public/Jscripts/Upload/cancel.png',
				'queueID'        : 'video-file-queue',
				'auto'           : false,
				'multi'          : true,
				'buttonImage'    : '/Public/Jscripts/Upload/upload.png',
				'width'          : 127,		
				'height'		 : 30,
        		'debug'          : false,
        		'suppressEvent'  : true,	
				'fileObjName'    : 'video-filedata',
				'onSelect'       : function(){
									   var html = " <a href=\"javascript:jQuery('#video-uploadify').uploadify('upload','*')\">Nahrej soubory</a> "+
										   	      " <a href=\"javascript:jQuery('#video-uploadify').uploadify('cancel','*')\">Zrušit všechny soubory</a> "+
										   	      " <a href=\"\" onclick=\"deleteVideos();return false;\"> Smazat označené </a>";
									   $("p.video-links").html(html);
								 },
				
								
				'onQueueComplete'  : function(event, uploadObj){
								      
									       $.ajax({
											   type: "POST",
											   url: "/core/helper/get-add-videos",
											   data: "folder="+vFolder+"&ui="+vUi+"&table="+vTable+"&tableID="+vTableID+"&tableIDvalue="+vTableIDvalue,
											   success: function(html){  
											   			   
											       $("div.video-files").html(html);               
									           }
										   });  
									   
									
								 },
				'formData'      :{'tableType':vTable,'user':vUi,'folder':'/Public/Videos/Temp'}
			});
			},0);
		    /*---------------OBSLUHA JAZYKOVYCH MUTACI V PRIDAT A UPRAVIT-----------*/
		 	$('span.language-mutations a').click(function(){
		
		 		var closestP = $(this).closest('p');
		 		
		 		closestP.find('span.language-mutations a').each(function(){
		 			var classToDisable;
		 			classToDisable = $(this).attr('class');
		 			$(this).removeClass(classToDisable);
		 			classToDisable = classToDisable.replace('-dis',''); 	
		 			$(this).addClass(classToDisable+'-dis');
		 		});
		 		
		 		var cls = $(this).attr('class');
		 		var enabledClass = cls.replace('-dis',''); 	
		 		
		 		//odebereme classu
		 		$(this).removeClass(cls);
		 		//a vlozime classu bez -dis
		 		$(this).addClass(enabledClass);
		 		
		 		//zobrazime spravny input prislusny k vlajce
		 		closestP.find('span.language-input').each(function(){
		 			if($(this).hasClass(enabledClass))
		 				$(this).show();
		 			else
		 				$(this).hide();
		 		});
		 		
		 		return false;
		 	});
		 	/*---------------KONEC -OBSLUHA JAZYKOVYCH MUTACI V PRIDAT A UPRAVIT-----------*/
		});
