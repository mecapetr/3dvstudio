//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	 	
    
  //vsechny kategorie u emailu
    $("input#all-cat-email").click(function(){
                
        var dis = $("input#all-cat-email").attr("checked");
        
        if(dis){
            $("input.email-cat-check").attr("checked",true);
        }else{
            $("input.email-cat-check").attr("checked",false);
        }
        
    });
        
    selectAllEmailsInit();
    $('#send-categories input').click(function(){    
    	getNewsletterEmails();
    });
    $('#all-cat-email').click(function(){    
    	getNewsletterEmails();
    });
    

});

function selectAllEmailsInit(){
	
	$("input#all-email").click(function(){
        
        var dis = $("input#all-email").attr("checked");
        
        if(dis){
            $("input.email").attr("checked",true);
        }else{
            $("input.email").attr("checked",false);
        }
        
    });
	
}

function getNewsletterEmails(){

    $("div.loading").ajaxStart(function(){
	 	$('ul#filtered-emails').hide();
       $(this).show();
    })
    .ajaxComplete(function(){
	 	$('ul#filtered-emails').show();
       $(this).hide();
    });

	
	var categoriesSelected = "";

	var length = $('#send-categories input:checked').length;
	
	$('#send-categories input:checked').each(function(index) {
		if(length != index + 1)	categoriesSelected += $(this).val()+",";
		else					categoriesSelected += $(this).val();	   
	});
	 
	$.ajax({
		type: "POST",
		url: "/admin/newsletter/helper/get-emails",
		data: "categoriesSelected="+categoriesSelected,
		success: function(html){
			$('#filtered-emails').html(html);
			selectAllEmailsInit();
		}
				
	});

}



