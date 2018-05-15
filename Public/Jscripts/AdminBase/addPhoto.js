
function checkCheckbox(element){
    
	var href = $(element).attr("href");
	if($("input[id=del"+href+"]").attr("checked") == true){
		$("input[id=del"+href+"]").attr("checked",false);
	}else{
		$("input[id=del"+href+"]").attr("checked",true);
	}
	
}

function deletePhotos(){
    
	$.prompt("Opravdu chcete smazat označené soubory?",{ 
             buttons: {'Ano': true, 'Ne': false },
             focus: 0,
             submit:function(clicked,m,element){ 
                 
                 if(m){      
		    		var checkedInputs = $("div.files input[type=checkbox]:checked");
		    		var values = new Array();
		    		for(i = 0;i < checkedInputs.length;i++){
		    			values[i] = checkedInputs.eq(i).val();
		    		}
		    		$.ajax({
						type: "POST",
						url: "/core/helper/get-add-photos",
						data: "delete="+values+"&folder="+mFolder+"&table="+mTable+"&ui="+mUi+"&tableID="+mTableID+"&tableIDvalue="+mTableIDvalue,
						success: function(html){  
						    $("div.files").html(html);               
						}
				
					});  
				 }
              
             }
     });
    		

}

function cropPhoto(width,height,id,enter){
   
   	addOverlay(width);
 	
		$.ajax({
			type: "POST",
			url: "/core/helper/photo/id/"+id,
			data: "enter="+enter+"&folder="+mFolder+"&table="+mTable+"&action="+mAction+"&path="+mPath+"&ui="+mUi+"&tableID="+mTableID+"&tableIDvalue="+mTableIDvalue,
			success: function(html){ 			    
				$("div#overlayContent").html(html);                 
			}
		
	});  
	
}

function crop(id){

	var x1     = $("input#x1").val(); 
	var y1     = $("input#y1").val();
	var nWidth  = $("input#newWidth").val();
	var nHeight = $("input#newHeight").val();
 	clwi();
 	
	$.ajax({
			type: "POST",
			url: "/core/helper/crop-photo/file/"+id,
			data: "crop=1&table="+mTable+"&ui="+mUi+"&path="+mPath+"&x1="+x1+"&y1="+y1+"&newWidth="+nWidth+"&newHeight="+nHeight+"&folder="+mFolder+"&tableIDvalue="+mTableIDvalue+"&tableID="+mTableID,
			success: function(html){ 			    
				$("div.files").html(html);  
				              
			}
		
	}); 
	
}
