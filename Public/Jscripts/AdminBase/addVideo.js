
function videoCheckCheckbox(element){
    
	var href = $(element).attr("href");
	if($("input[id=del"+href+"]").attr("checked") == true){
		$("input[id=del"+href+"]").attr("checked",false);
	}else{
		$("input[id=del"+href+"]").attr("checked",true);
	}
	
}

function deleteVideos(){
    
    $.prompt("Opravdu chcete smazat označené soubory?",{ 
             buttons: {'Ano': true, 'Ne': false },
             focus: 0,
             submit:function(clicked,m,element){ 
                 
                 if(clicked){ 	
			    		var checkedInputs = $("div.video-files input[type=checkbox]:checked");
			    		var values = new Array();
			    		for(i = 0;i < checkedInputs.length;i++){
			    			values[i] = checkedInputs.eq(i).val();
			    		}
			    		$.ajax({
							type: "POST",
							url: "/core/helper/get-add-videos",
							data: "delete="+values+"&folder="+vFolder+"&table="+vTable+"&ui="+vUi+"&tableID="+vTableID+"&tableIDvalue="+vTableIDvalue,
							success: function(html){  
							    $("div.video-files").html(html);               
							}
					
						});  
			
    			 }
              
             }
     });

}
