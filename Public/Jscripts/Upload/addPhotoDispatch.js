$(document).ready(function() {
      setTimeout(function () {   
    	  var error = "";
    	  
			$("#photo-uploadify").uploadify({
				'swf'       	 : '/Public/Jscripts/Upload/uploadify.swf',
				'uploader'       : '/core/helper/photo-uploadify/swidth/'+sWidth+'/sheight/'+sHeight+'/mwidth/'+mWidth+'/mheight/'+mHeight+'/resized/'+mResizedByLongerSide,
				'queueID'        : 'fileQueue',
				'auto'           : false,
				'multi'          : true,
				'buttonImage'    : '/Public/Jscripts/Upload/upload.png',
				'fileSizeLimit'  : '3MB',
				'width'          :  127,		
				'height'		 : 30,	
        		'debug'          : false,
        		'suppressEvent'  : true,
				'fileObjName'    : 'photo-uploadify',
				'onSelect'       : function(a,b,c){
					
									   var html = " <a class=\"btn btn-success btn-xs\" href=\"javascript:jQuery('#photo-uploadify').uploadify('upload','*')\">Nahrej soubory</a> "+
										   	      " <a class=\"btn btn-danger btn-xs\" href=\"javascript:jQuery('#photo-uploadify').uploadify('cancel','*')\">Zruš všechny soubory</a> "+
										   	      " <a class=\"btn btn-danger btn-xs\" href=\"\" onclick=\"deletePhotos();return false;\"> Smazat označené </a>";
									   $("p.links").html(html);
								 },
				
				'onQueueComplete'  : function(event, uploadObj){
								      if(error)$.prompt('<div class="alert alert-danger">'+error+'</div>');
								       $.ajax({
										   type: "POST",
										   url: "/core/helper/get-add-photos",
										   data: "folder="+mFolder+"&ui="+mUi+"&table="+mTable+"&tableID="+mTableID+"&tableIDvalue="+mTableIDvalue,
										   success: function(html){    
										       $("div.files").html(html);               
								           }
									   });  
									
								 },
				'onUploadSuccess' : function(file, data, response) {
					
				    if(data){
				    	if(error)error += "<br />";
				    	error += data;
				    }
				},
				'formData'      :{'tableType':mTable,'user':mUi,'folder':'/Public/Images/Temp'},
				'queueSizeLimit'  : 100
			});
			},0);
			//nastavime sortovani obrazku
		    if($("div.uploaded-photos").length > 0){
			    $("div.uploaded-photos").sortable({
			    
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
								url: "/core/helper/update-photo-priority",
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