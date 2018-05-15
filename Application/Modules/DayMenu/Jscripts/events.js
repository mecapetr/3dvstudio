//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	 	
	

});

function showMenu(weekNumber,year,month){
	if(weekNumber == 1 && month == 12)
		year ++;
	$("div#menu div.loading")
        .ajaxStart(function(){
            $(this).show();
            
        })
        .ajaxComplete(function(){					            
            $(this).hide();
            
	 });
	$.ajax({
		type: "POST",
		url: "/admin/denni-menu/helper/get-food",
		data: "weekNumber="+weekNumber+"&year="+year,
		success: function(response){
			$("div#menu div.content-day-menu").html(response);
		}

    });
}

function initAddFood(){

    $("a.add-food").click(function(){
		var rel   = $(this).attr("rel");
		var count = parseInt($("input[name=count-"+rel+"]").val());
		count = count + 1;
		
		var text = '<br /><input class="small" type="text" name="weight-'+rel+'-'+count+'" />g <input type="text" name="food-'+rel+'-'+count+'" /> <input class="small" type="text" name="price-'+rel+'-'+count+'" />Kč';
		$("p.food-"+rel).append(text);
		
		$("input[name=count-"+rel+"]").val(count)
		
		return false;
		
	});
}