//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	 	
    $(".generate-type input").click(function(){
    
    	var val = $(this).val();
    	if(val == 2){
    		$(".generate-button").show();
    	}else{
    		$(".generate-button").hide();
    	}
    	
    });

});





