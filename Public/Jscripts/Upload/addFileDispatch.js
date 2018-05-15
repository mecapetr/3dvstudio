$(document).ready(function() {
      setTimeout(function () { 
    	  var error = "";
			$("#file-uploadify").uploadify({
				'swf'       : '/Public/Jscripts/Upload/uploadify.swf',
				'uploader'         : '/core/helper/file-uploadify',
				'cancelImg'      : '/Public/Jscripts/Upload/cancel.png',
				'queueID'        : 'file-fileQueue',
				'auto'           : false,
				'multi'          : true,
				'buttonImage'    : '/Public/Jscripts/Upload/upload.png',
				'width'          : 127,		
				'height'		 : 30,	
        		'debug'          : false,
        		'suppressEvent'  : true,
				'fileObjName'    : 'file-uploadify',
				'onSelect'       : function(){
									   var html = " <a class=\"btn btn-success btn-xs\" href=\"javascript:jQuery('#file-uploadify').uploadify('upload','*')\">Nahrej soubory</a> "+
										   	      " <a class=\"btn btn-danger btn-xs\" href=\"javascript:jQuery('#file-uploadify').uploadify('cancel','*')\">Zrušit všechny soubory</a> "+
										   	      " <a class=\"btn btn-danger btn-xs\" href=\"\" onclick=\"deleteFiles();return false;\"> Smazat označené </a>";
									   $("p.file-links").html(html);
								 },
				
				'onQueueComplete'  : function(event, uploadObj){
					  if(error)$.prompt('<div class="alert alert-danger">'+error+'</div>');
				       $.ajax({
						   type: "POST",
						   url: "/core/helper/get-add-files",
						   data: "folder="+fFolder+"&ui="+fUi+"&table="+fTable+"&tableID="+fTableID+"&tableIDvalue="+fTableIDvalue,
						   success: function(html){  
						   			   
						       $("div.other-files").html(html);               
				           }
					   });  
									
				 },
				 'onUploadSuccess' : function(file, data, response) {
						
				    if(data){
				    	if(error)error += "<br />";
				    	error += data;
				    }
				},
				'formData' :{'tableType':fTable,'user':fUi,'folder':'/Public/Files/Temp'},
				'queueSizeLimit'  : 100
			});
			},0);
			//nastavime sortovani obrazku
		    if($("div.uploaded-files").length > 0){
			    $("div.uploaded-files").sortable({
			    
			    	 update: function(event, ui) { 
			    	 
			        		var lis = $(this).find('div.photo');      
				            var count  = lis.size();
				            var listID = "";
				            
				            for(var i = 0 ;i < count ;i++){
				            	
				            	id = lis.eq(i).attr("id");
				            	if(i == 0){
				            		listID += id;
				            	}else{
				            		listID += "-"+id;
				            	}
				            }
				        	$.ajax({
								type: "POST",
								url: "/core/helper/update-file-priority",
								data: "listID="+listID,
								success: function(html){	
								}
				
							});  
				        	
				     }
			    });
			 	//$("div.uploaded-photos").disableSelection();
		    }
			
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