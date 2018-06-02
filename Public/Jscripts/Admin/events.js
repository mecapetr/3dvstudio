//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();

    $('.colorpicker-component').colorpicker({
    	format:"hex"
    });
    
    var siedbarScroll = new IScroll('#sideMenuAccordionScroll',{
        mouseWheel: true,
        scrollbars: true,
        fadeScrollbars:true
    });
});