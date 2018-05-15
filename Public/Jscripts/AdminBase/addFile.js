
function checkCheckbox(element){
    
	var href = $(element).attr("href");
	if($("input[id=del"+href+"]").attr("checked") == true){
		$("input[id=del"+href+"]").attr("checked",false);
	}else{
		$("input[id=del"+href+"]").attr("checked",true);
	}
	
}

function deleteFiles(){
    
	$.prompt("Opravdu chcete smazat označené soubory?",{ 
             buttons: {'Ano': true, 'Ne': false },
             focus: 0,
             submit:function(clicked,m,element,trueFalse){ 
                
                 if(m){ 
                	 
		    		var checkedInputs = $("div.other-files input[type=checkbox]:checked");
		    		var values = new Array();
		    		for(i = 0;i < checkedInputs.length;i++){
		    			values[i] = checkedInputs.eq(i).val();
		    		}
		    		$.ajax({
						type: "POST",
						url: "/core/helper/get-add-files",
						data: "delete="+values+"&folder="+fFolder+"&table="+fTable+"&ui="+fUi+"&tableID="+fTableID+"&tableIDvalue="+fTableIDvalue,
						success: function(html){  
						    $("div.other-files").html(html);               
						}
				
					});  
				 }
              
             }
     });
    		

}

function setFTPFileSelect(thisContext){
 	var fileName = $(thisContext).text();
 	$('div.search-file div.search-content-list ul').hide();
 	$('#search-input').val('');
 	$.ajax({
		type: "POST",
		url: "/admin/obsah/helper/get-next-ftp-file",
		data: "fileName="+fileName+'&tableType='+fTable+'&user='+fUi,
		success: function(response){

            if(response == 1){
            	$.ajax({
				   type: "POST",
				   url: "/core/helper/get-add-files",
				   data: "folder="+fFolder+"&ui="+fUi+"&table="+fTable+"&tableID="+fTableID+"&tableIDvalue="+fTableIDvalue,
				   success: function(html){  
				   			   
				       $("div.other-files").html(html);               
		           }
			   });  
            }
            	
		}

	});
	return false;
}
