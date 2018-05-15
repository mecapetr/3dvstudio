//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	 	
	$("input#all-cat-user").click(function(){
        
        var dis = $("input#all-cat-user").attr("checked");
        
        if(dis){
            $("input.user-cat-check").attr("checked",true);
        }else{
            $("input.user-cat-check").attr("checked",false);
        }
        
    });
    

});

