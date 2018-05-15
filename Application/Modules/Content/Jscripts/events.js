//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	 	
    
  $("div.list-order select[name=subLinkID]").change(function(){
	 
	    var linkID = $(this).val()
	  console.log("aaaa");
	    getOtherLinks(linkID,"");
	  
  });
    

});

function getOtherLinks(linkID,linkID1){
	
	$.ajax({
		type: "POST",
		url: "/admin/obsah/helper/get-others-link",
		data: "linkID="+linkID+"&linkID1="+linkID1,
		success: function(response){

			$("div.list-order span.others-link").html(response)
			
		}

	});
	
}



