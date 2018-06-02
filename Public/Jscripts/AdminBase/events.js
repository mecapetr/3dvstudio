//****************************************** METODY *****************************************************************

//*************************************** J Q U E R Y ****************************************************************
$(document).ready(function() {
	
	$(document).on('show.bs.modal', '.modal', function () {
	    var zIndex = 1040 + (10 * $('.modal:visible').length);
	    $(this).css('z-index', zIndex);
	    setTimeout(function() {
	        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
	    }, 0);
	});
	$(document).on('hidden.bs.modal', '.modal', function () {
	    if($('.modal:visible').length > 0){
		    $('body').addClass('modal-open');		    
	    }
	});
	
	var $myGroup = $('#menu-accordion');
	$myGroup.on('show.bs.collapse','.collapse', function() {
	    $myGroup.find('.collapse.in').collapse('hide');
	});
	
    $("a.link").click(function() {
     	
 	    var href = $(this).attr("href"); 	  
 	    var newWindow = window.open(href, "_blank");
 	    newWindow.focus(); 
 	    return false;
 	
 	});
    
    $("a.add-section").click(function(e){
    	e.preventDefault();
    	
    	$.ajax({
    		type: "POST",
    		url: "/core/helper/get-langs",
    		success: function(data){
    						
    			var data = JSON.parse(data);
    			var flags = "";
    			
    			if(data.length > 1){
    			    flags  = ' <span class="language-mutations">';
    			    for(var i = 0; i < data.length; i++){
    			    	
    			    	var suffClass = data[i].suffix;
    			    	if(i != 0)suffClass = data[i].suffix+"-dis";
    			    	
    			    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
    			    }
    			    flags += '</span> ';
    			}
    			
    			console.log(flags);
    			
    			var sectionHtml = $("div.section-control").eq(0).html();
    	    	var sectionCount = $("input.section-count").val();
    	    	    sectionCount++;
    	    	    $("input.section-count").val(sectionCount);
    	    	    
    	    	var wholeSection  = '<li><div class="card mb-4"><div class="card-header d-flex align-items-center"> Sekce <span class="glyphicons glyphicons-move ml-auto mr-2"></span><span class="glyphicons glyphicons-remove section"></span></div><div class="card-body"><section data="'+sectionCount+'"><div class="form-group section-control">';
    	    	wholeSection += '<div class="row">';
    		    	wholeSection += '<div class="col-sm-4 col-xs-12">';
    			    	wholeSection += '<div class="form-group">';
    			    		for(var i = 0; i < data.length; i++){
    			    		
	    			    		var hid = "";
	    			    		if(i != 0)hid = "hidden-lang";
	    			    		 
	    			    		wholeSection += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
	    			    		wholeSection += '<input placeholder="název sekce" type="text" name="sec-name-'+sectionCount+'-'+data[i].suffix+'" class="form-control">';
	    			    		wholeSection += '</span>';
    			    		}
    			    		wholeSection += flags;
    			    	wholeSection += '</div>';
    		    	wholeSection += '</div>';
    		    	wholeSection += '<div class="col-sm-4 col-xs-12">';
    			    	wholeSection += '<div class="form-group">';
    			    	wholeSection += '<select name="sec-bg-color-'+sectionCount+'" class="form-control">';
    			    		wholeSection += '<option value="0"> bez barvy pozadí </option>';
    			    		wholeSection += '<option value="grey"> šedé pozadí </option>';
    			    		wholeSection += '<option value="white"> bílé pozadí </option>';
    			    		wholeSection += '<option value="black"> černé pozadí </option>';
    			    	wholeSection += '</select>';
    			    	wholeSection += '</div>';
    				wholeSection += '</div>';
    				wholeSection += '<div class="col-sm-4 col-xs-12">';

			    	    wholeSection += ' <label class="font-weight-bold d-block">Na celou šířku webu:</label>';
			    	    wholeSection += ' <div class="form-check form-check-inline"><input class="form-check-input" type="radio" value="1" name="wide-'+sectionCount+'" id="wide-'+sectionCount+'1" /><label class="form-check-label" for="wide-'+sectionCount+'1">Ano</label></div>';
			    	    wholeSection += ' <div class="form-check form-check-inline"><input class="form-check-input" type="radio" value="0" name="wide-'+sectionCount+'" id="wide-'+sectionCount+'2" checked /><label class="form-check-label" for="wide-'+sectionCount+'2">Ne</label></div>';
			    	    

				    wholeSection += '</div>';
    			wholeSection += '</div>';
    	    	wholeSection += '<div class="dropdown">';
    	    		wholeSection += '<button class="btn text-uppercase btn-block btn-primary" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    	    		wholeSection += '--- vyberte element který chcete přidat do sekce ---';
    	    		wholeSection += '</button>';
    	    		wholeSection += '<div class="dropdown-menu" aria-labelledby="dLabel">';	  	
    	    			wholeSection += '<a data-elmtype="e-1" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="Element obsahující pouze velký obrázek a text k němu. Pokud je vloženo více obrázku, stává se z toho slider.">Header/Slider element</a>';
    	    			wholeSection += '<a data-elmtype="e-2" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="nadpis jednotlivé sekce">Section Header element</a>';
    	    			wholeSection += '<a data-elmtype="e-3" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="tvoří sloupce sekce a obsahuje fotku nadpis a text">Link element</a>';
    	    			//wholeSection += '<a data-elmtype="e-4" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="tak samo jako link element ale bez fotky a je propojený s jiným odkazem">Sublink element</a>';
    	    			wholeSection += '<a data-elmtype="e-5" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="vložení textu do sekce">Text element</a>';
    	    			//wholeSection += '<a data-elmtype="e-6" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="vložení tabulky s filtrovaným seznamem">List element</a>';
    	    			wholeSection += '<a data-elmtype="e-7" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="vložení formuláře">Form element</a>';
    	    			wholeSection += '<a data-elmtype="e-8" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="vložení youtube nebo vimeo videí">YouTube,Vimeo element</a>';
    	    			wholeSection += '<a data-elmtype="e-9" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="V tomto elementu se budou zobrazovat články, které přiřadíte tomuto odkazu vsekci Přidat článek">Element zobrazení článků</a>';
    	    			wholeSection += '<a data-elmtype="e-10" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="V tomto elementu se budou zobrazovat vložené fotky nebo soubory">Element fotky a soubory</a>';
    	    			wholeSection += '<a data-elmtype="e-11" class=" dropdown-item d-flex align-items-center" href="" data-toggle="tooltip" data-placement="left" title="Element zobrazí googlemapu a jednolitvé body v ní">Map element</a>';
    	    			wholeSection += '</div>';
    	    			wholeSection += '<script>';
    	    				wholeSection += '$(function () {';
    	    				wholeSection += '$(\'[data-toggle="tooltip"]\').tooltip()';
    	    				wholeSection += '})';
    	    			wholeSection += '</script>';
    	    		wholeSection += '</div>';
    	    	    wholeSection += '</div>';						
    	    	    wholeSection += '<ul class="section-elements sortListBasic list-unstyled">';
    				
    	    	    wholeSection += '</ul>';   
    	    	    wholeSection += '<input type="hidden" name="header-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="section-header-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="link-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="sublink-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="list-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="text-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="form-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="ytv-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="article-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="photo-file-element-count-'+sectionCount+'" value="0" />';
    	    	    wholeSection += '<input type="hidden" name="map-element-count-'+sectionCount+'" value="0" />'
    	    	    wholeSection += '</section><input type="hidden" name="is-section[]" value="'+sectionCount+'" /></div></div></li>';
    	    	
    	    	$("ul.other-sections").append(wholeSection);
    	    	
    	    	setSectionControl();
    	    	removeElement();
    	    	setLangs();
    		}

    	});

    });
            
    $("a.add-food").click(function(){
		
		var rel   = $(this).attr("rel");
		var count = parseInt($("input[name=count-"+rel+"]").val());
		count = count + 1;
		
		var text = '<br /><input class="small" type="text" name="weight-'+rel+'-'+count+'" />g <input type="text" name="food-'+rel+'-'+count+'" /> <input class="small" type="text" name="price-'+rel+'-'+count+'" />Kč';
		$("p.food-"+rel).append(text);
		
		$("input[name=count-"+rel+"]").val(count)
		
		return false;
		
	});
 	
    if(document.getElementById("sortListBasic")){
    
	    $("#sortListBasic").sortable({
	
	    	revert:false,
	        cursor: "move",
	        dropOnEmpty: true,
	        scroll:true,
	        tolerance: 'pointer',
	        helper: 'clone',
	        opacity:0.75,
	        axis:"y",
	        'placeholder':'placeholder'
	
	 	});
	 	 	 	
	 	$("#sortListNested").sortable({
	 	
	
	        revert:false,
	        cursor: "move",
	        dropOnEmpty: true,
	        scroll:false,
	        tolerance: 'pointer',
	        helper: 'clone',
	        items: 'li',
	        'nested':'ul',
	        'placeholder':'placeholder',
	        'maxLevels':3,
	        opacity:0.75,
	        update: function(event, ui) { 
	        
	            var itemID = ui.item.attr("id");
	            var parent = ui.item.parent().parent().attr("id");
	        	var subPortal;
	        	        	
	        	if(parent == ""){
	        	    subPortal = "0";
	        	}else{
	        	    subPortal = "1";
	        	}
	        	
	        	$.ajax({
					type: "POST",
					url: "/admin/helper/updatesubportal",
					data: "itemid="+itemID+"&subportal="+subPortal+"&parrent="+parent,
					success: function(html){
	
					}
	
				});  
	        	
	        }
	        
	
	 	});
	 	
	 	$("#sortListBasic").disableSelection();	 	
	 	$("#sortListNested").disableSelection();
    }
    
    $("cite").click(function() {
 	
 	    var position = $(this).position();
 	    var id = $(this).attr("id");
 	    var left = position.left;
 	    var top  = position.top;
 	    top += 20;
 	    

 	    $("div#citation"+id).css("top",top);
 	    $("div#citation"+id).css("left",left);
 	    
 	    $("div#citation"+id).slideToggle(400);
 	     	
 	});
    

 	$(".check-required-fields").click(function(e){
 		checkRequiredFields(e);
 	});
 	$(".check-product-required-fields").click(function(e){
 		var check = checkRequiredFields(e);
 		if(check){
 			checkLinkOrCategoryFilled(e);
 		}
 	});
 	
 	$(".check-category-required-fields").click(function(e){
 		var check = checkRequiredFields(e);
 		if(check){
 			checkCategoryShortcut(e);
 		}
 	});

 	$(".check-product-status-restrictions").click(function(e){
 		var check = checkRequiredFields(e);
 		if(check){
 			checkMaxFiveStatusesChecked(e);
 		}
 	});
 	$(".check-auction-required-fields").click(function(e){
 		checkAuctionRequiredFields(e);
 	});
 	$(".check-eshop-product-required-fields").click(function(e){
 		checkEshopProductRequiredFields(e);
 	});
 	$("div.citation p.close").click(function() {
 	
 	    id = $(this).parent().attr("id"); 	  
 	    $("div#"+id).slideToggle(400);
 	    
 	    //alert(position.left+" - "+position.top);
 	
 	});
 	
 	quickSearchInit('search-input','search-content'); 
 	quickSearchInit('search-input-ftp','search-content-ftp');
 	//quickSearchInit('searchInput2','searchContent2'); 
 	
 	$("select[name=link-type]").change(function(){
 		var val = $(this).val();
 		if(val == 1){
 			$("div.sub-links").show();
 		}else{
 			$("div.sub-links").hide();
 		}
 	});
 	 	
 	
 	selectLink();
 	removeLink();
 	
 	$("div.main-link span.add-link").click(function(){
 		
 		var count       = $("input.link-count").val();
 		var oldLinkList = $("div.main-link span.parrent-links").html();
 		count++;
 		
 		html='<div class="has-feedback link-'+count+'">';
 			html+='<label> </label>';
			html+=' <span id="selected-parent-link" class="selected-link" rel="'+count+'">';
				html+='<span class="value form-control v-'+count+'">&nbsp;</span>';
					html+='<span class="parrent-links">';
						html += oldLinkList;
					html+='</span>';
				html+='<input type="hidden" name="linkID[]" value="0" id="selected-parent-input-'+count+'" />';
			html+='</span>';
			html+=' <span id="link-'+count+'" class="remove-link"></span>';
		html+='</div>';
		
		$("div.link-List").append(html);
 		
 		$("input.link-count").val(count);
 		
 		selectLink();
 		removeLink();
 		
 	});
 	
 	//vlozi souvisejci prispevek(produkt) do seznamu souvisejicich
 	$('ul.search-content li.related-item').click(function(){
 		var title 		= $(this).find('span.title').text();
 		var id 			= $(this).find('span.id').text();
 		var itemExists 	= false;
 		
 		//zjistime jestli se tam tento prvek jiz nevyskytuje
 		$('div.related-articles span.selected-articles').find('span.item').each(function(){
 			var tmpID = $(this).find('input.related-input').val();
 			if(tmpID == id)
 				itemExists = true
 		});
 		
 		if(!itemExists){
	 		var last = $('div.related-articles span.selected-articles').find('span.item:first');
	
	 		if(last.hasClass('even'))
	 			cls = 'odd';
	 		else
	 			cls = 'even';
	 		
	 		var appendElement = '<span class="item '+cls+'">'+
	 								'<span class="title">'+title+'</span>'+
			    					'<a href="" class="remove-from-related"></a>'+
			    					'<input class="related-input" type="hidden" value="'+id+'" name="related[]" />'+
			    					'<span class="clear-both"></span>'+		    					
			    				'</span>';
	 		
	 		$('div.related-articles span.selected-articles').prepend(appendElement);
	 		
	 		//$('#search-input').val('');
	 		
		 	//odstrani zaznam ze souvisejicich prispevku(produktu)
		 	$('a.remove-from-related').click(function(){
		 		$(this).parent().remove();
		 		return false;
		 	});
		 	
	 	}else $.prompt('Tento záznam zde již existuje!');
 	});	
 	
 	
 	$("div.product-size-wrapper input[name='productTypeID']").click(function(){
 		console.log($(this).is(":checked"));
 		if($(this).is(":checked")){
 			$("div.product-size-wrapper input[name='productTypeID']").not(this).attr("checked",false);
 		}else{
 			$(this).attr("checked",false);
 		}
 	});
 	
 	addVideoURLTitle();
 	initRefreshFTPListButton();

 	$('.show-auction img').click(function(){
 		var row = $(this).closest('li');
 		if(row.find('.other-tels').hasClass("hide")){
 			row.find('.other-tels').removeClass('hide');
 		}else{
 			row.find('.other-tels').addClass('hide');
 		}
 	});
 	

 	/*---------------FILTROVÁNÍ ŘÁDKŮ------------*/
 		//produkty
	 	var options = {
		  valueNames: [ 'title', 'category', 'subcategory']
		};
		var optionsCoverList = {
		  valueNames: [ 'title', 'shortcut', 'supplierTitle']
		};
		var productList = new List('products', options);
		var coverList = new List('covers', optionsCoverList);
		
		$('#category-filter').change(function () {
			var categoryID = $(this).find(":selected").attr("class");
		    var selection = this.value; 
		    if(selection == ""){
		    	productList.filter(function (item) {
			        return true;
			    });
		    }else{
			    // filter items in the list
			    productList.filter(function (item) {
			        return (item.values().category.indexOf(selection) != -1);
			    });
		    }
		    
		    $.ajax({
			      type: "POST",
			      url: "/admin/eshop/kategorie/seznam-podkategorii/id/" + categoryID,
			      success: function(html){

			    	  $('#subcategory-filter').html(html);
			    	  
			      }
			  
	        }); 
		    

		});
		$('#subcategory-filter').change(function () {
			var subCategoryID = $(this).attr("class");
			var categorySelection 	= $('#category-filter').val();
		    var selection 			= this.value; 
		    if(selection == ""){
			    // filter items in the list
			    productList.filter(function (item) {
			        return (item.values().category.indexOf(categorySelection) != -1);
			    });
		    }else{
			    // filter items in the list
			    productList.filter(function (item) {
			        return (item.values().subcategory.indexOf(selection) != -1);
			    });
		    }		    

		});
		$('#supplier-filter').change(function () {
		    var selection = this.value; 
		    if(selection == ""){
		    	coverList.filter(function (item) {
			        return true;
			    });
		    }else{
			    // filter items in the list
		    	coverList.filter(function (item) {
			        return (item.values().supplierTitle.indexOf(selection) != -1);
			    });
		    } 
		});
		$( ".add-products-to-homepage-section" ).click(function( e ) {
			e.preventDefault();
				$('.products-to-homepage-section-panel').hide();
				$('#addToHomepageSectionsForm .loading').removeClass("hide");
				$("input.search").val("");
				$("select#category-filter").val('');
				$("select#subcategory-filter").val('');
				productList.filter();
				productList.search();

			if($( "input.addProductIDs:checked" ).length > 0){
				setTimeout(function(){
			    	$('#addToHomepageSectionsForm').submit();
				},500);
			}else{
				$.prompt('Nevybrali jste žádný produkt!');
				$('.products-to-homepage-section-panel').show();
				$('#addToHomepageSectionsForm .loading').addClass("hide");
			}
		});
		
		 

 	/*--------------- KONEC FILTROVÁNÍ------------*/
 	
 	/*---------------PRIDANI EDITACE KATEGORII S POTAHY PRODUKTU------------*/
 	
	 	$('.add-product-category').click(function(){
	 		openAddEditProductCategory("add");
	 	});

	 	$('.edit-product-category').click(function(e){
	 		e.preventDefault();
	 		var productCategoryID = $(this).closest("li").attr("id");
	 		openAddEditProductCategory("edit",productCategoryID);
	 	});
 	
 	/*---------------KONEC -PRIDANI EDITACE KATEGORII S POTAHY PRODUKTU------------*/

 	/*---------------PRIDANI EDITACE ESHOP PRODUKTŮ------------*/
 	
	 	$('.add-eshop-product').click(function(){
	 		openAddEditEshopProduct("add");
	 	});

	 	$('.edit-eshop-product').click(function(e){
	 		e.preventDefault();
	 		var eshopProductID = $(this).closest("li").attr("id");
	 		openAddEditEshopProduct("edit",eshopProductID);
	 	});
	 	
	 	
 	
 	/*---------------KONEC -PRIDANI EDITACE KATEGORII S POTAHY PRODUKTU------------*/
 	
 	/*---------------OBSLUHA JAZYKOVYCH MUTACI V PRIDAT A UPRAVIT-----------*/
 	
 	setLangs();
 	
 	/*---------------KONEC -OBSLUHA JAZYKOVYCH MUTACI V PRIDAT A UPRAVIT-----------*/

 	/*---------------PRODUKTY DO HOMEPAGE SEKCE------------*/
 	
	 	$('.add-eshop-product-to-homepage-s').click(function(){
	 		$('#addProducts').modal({
				backdrop:'static'
			});
	 	});
 	
 	/*---------------KONEC -PRODUKTY DO HOMEPAGE SEKCE------------*/
 	 	
 	$("button.delButton").click(function(){
 		var l = $("input.delete:checked").length;3
 		if(l > 0){
 			$('#warning').modal();
 			$('#warning').on('shown.bs.modal', function (e) {
 				$(".modal .modal-body").html("Opravdu chcete smazat tyto položky ?");
 				$("button.do-delete").unbind("click");
 				$("button.do-delete").click(function(){
 					$("input.deleteButton").click();
 				});
 			})
 		}
 	});

   
 	$('input[name="check-all-exclude-covers"]').change(function(){
 	
 		if($('input[name="check-all-exclude-covers"]').is(':checked')){
 			$('input[name="coverIDs[]"]').prop('checked',true);
 		}else{
 			$('input[name="coverIDs[]"]').prop('checked',false);
 		}
 		
 	});

 	addHeaderElement();
 	addMapElement();
	getSubLink();
	setSectionControl();
	removeElement();
	sortListBasic();
	sortListPhotos();
	addLinkElement();
	addSublinkElement();
	selectInputType();
	addFormElement();	
	addFormElementValues();
	addListElementRow();
	addListElementCol();
	addYtvElement();
	crSublink();

});

function checkCategoryShortcut(e){
	
	e.preventDefault();
	
	var shortcut = $("input[name = shortcut]").val();
	var linkID   = $("input[name = c]").val();
	
	$.ajax({
      type: "POST",
      dataType: "json",
      url: "/admin/eshop/kategorie/kontrola-zkratky/z/"+shortcut+"/l/"+linkID,
      success: function(response){
 
    	  if(response.error){
    		  
    		  $.prompt(response.error);
    	  }else{
    		  $("input[name = enter]").val(1);
    		  $("#send-category-data").submit();
    	  }
    	  
      }
	  
    });

}

function openAddEditProductCategory(operationType,productCategoryID){
	if(typeof productCategoryID == "undefined"){
		productCategoryID = 0;
	}
	
	if(operationType == "add"){
		$('#addCategory').modal({
			backdrop:'static'
		});
	}else if (operationType == "edit"){
		$('#editCategory').modal({
			backdrop:'static'
		});
		
		$('#editCategory .modal-body').html('<div class="text-center"><img src="/Public/Images/animation.gif" /></div>');
        $.ajax({
		      type: "POST",
		      url: "/admin/eshop/produkt/kategorie-a-potahy-edit/id/" + productCategoryID,
		      success: function(html){

		    	  $('#editCategory .modal-body').html(html);
		    	  
		    	  setLangs();
		      }
		  
        }); 
		
	}
}
function openAddEditEshopProduct(operationType,eshopProductID){
	if(typeof eshopProductID == "undefined"){
		eshopProductID = 0;
	}
	
	
	if(operationType == "add"){
		$('#addEshopProduct').modal({
			backdrop:'static'
		});
		setAddEditEshopProductEvents();
		
	}else if (operationType == "edit"){
		$('#editEshopProduct').modal({
			backdrop:'static'
		});
		
		$('#editEshopProduct .modal-body').html('<div class="text-center"><img src="/Public/Images/animation.gif" /></div>');
        $.ajax({
		      type: "POST",
		      url: "/admin/eshop/produkt/eshop-edit/id/" + eshopProductID,
		      success: function(html){

		    	  $('#editEshopProduct .modal-body').html(html);
		    	  
		    	  setLangs();
		  		  setAddEditEshopProductEvents();
		      }
		  
        }); 
		
	}
	function setAddEditEshopProductEvents(){


		$("input[name='predefinedCoversType']").change(function(){
			var predefinedCoversType = $(this).val();
			if(predefinedCoversType == 1){
				$('.predefined-covers-options').removeClass("hide");
				$('.predefined-covers-options .cover-id-display').each(function(){
					$(this).removeClass("hide");
					if($(this).find('select.coverID').val() > 0){
						$(this).closest(".card-body").find('.concrete-cover').removeClass("hide");
					}
				});
				
			}else if(predefinedCoversType == 2){
				$('.predefined-covers-options').removeClass("hide");
				$('.predefined-covers-options .cover-id-display').addClass("hide");
				$('.predefined-covers-options .concrete-cover').addClass("hide");
				
			}else if(predefinedCoversType == 3){
				$('.predefined-covers-options').addClass("hide");
				
			}
		});
		
		$('.choose-concrete-cover-btn').click(function(e){
			var that = this;
			e.preventDefault();
		 	$('#chooseConcreteCover').on('show.bs.modal',$.proxy(chooseConcreteModalShown,that));
	 	    $('#chooseConcreteCover').modal({
				backdrop:'static'
			});
		 	$('#chooseConcreteCover').on('hide.bs.modal',function(){
		 		$('#chooseConcreteCover').unbind();
		 	});
		 	
	 	});
		
		$('select.coverID').change(function(){
			var coverID = $(this).val();
			var that = this;
			
			$(that).closest('.card-body').find('.concrete-cover .img').html("");
			$(that).closest('.card-body').find(".concrete-cover .text").text("Vyberte konkrétní vzor");
			$(that).closest('.card-body').find(".concrete-cover input[type='hidden']").val('');
			$(this).closest('.card-body').find('.concrete-cover').addClass("hide");
	
		    if(coverID == 0){	
				  $(this).closest('.card-body').find('.concrete-cover').addClass("hide");			
		    }else{
				  $(this).closest('.card-body').find('.concrete-cover').removeClass("hide");
		    }
			
		});
	}
	 function chooseConcreteModalShown() {
		 var that = this;
		   	var coverID = $(that).closest(".card-body").find("select.coverID").val();
		   	if(coverID != 0){
			   	$('#chooseConcreteCover .modal-body .content').html('<div class="concrete-cover-loading "><img class="img-responsive center-block" src="/Public/Images/animation.gif" /></div>');
			    
				$.ajax({
				      type: "POST",
				      url: "/admin/eshop/potahy/get-all-photos/id/" + coverID,
				      dataType: "json",
				      success: function(response){
				    	  	if(response.allCoverPhotos.length > 0){
				    	  		
					  			$(that).closest(".card-body").find(".concrete-cover-loading").addClass("hide");
								$('#chooseConcreteCover .modal-body .content').html("");
								for(var i = 0; i < response.allCoverPhotos.length; i++){
									$('#chooseConcreteCover .modal-body .content').append("<div class='col-sm-3'><div class='item'><img class='item-img' src='/Public/Images/Cover/" + response.allCoverPhotos[i].file + "'/><span class='text'>" + response.coverTitle + " " + response.allCoverPhotos[i].number + "</span><input type='hidden' name='photoCoverID' value='"+ response.allCoverPhotos[i].photoID +"' /></div></div>");
								}
					 		    $('#chooseConcreteCover .item').click(function(){
						 			$(that).find(".img").html( $( this ).find("img").clone());
						 			$(that).find(".text").text($( this ).find(".text").text());
						 			$(that).find("input[type='hidden']").val($( this ).find("input[name='photoCoverID']").val());
						 			$('#chooseConcreteCover').modal("hide");
					 		    });
					 		    
							}else{
							   	$('#chooseConcreteCover .modal-body .content').html('<div class="alert alert-warning text-center">Potahu nebyly vloženy žádné fotky!</div>');
							}
				      }
		        });			   
		   	}else{
			   	$('#chooseConcreteCover .modal-body .content').html('<div class="alert alert-danger text-center">Nevzbrali jste název potahu!</div>');
		   	}
		}
}

function crSublink(){
	
	$("input.create-sublink").click(function(){
		
		if($(this).is(':checked')){
			$(this).parent().parent().parent().parent().find("div.url").hide();
		}else{
			$(this).parent().parent().parent().parent().find("div.url").show();
		}
	});
	
}

function sortListBasic(){
	
	  $(".sortListBasic").sortable({
	   
	         revert:false,
	         cursor: "move",
	         dropOnEmpty: true,
	         scroll:true,
	         tolerance: 'pointer',
	         helper: function(e, ui) {  
	        	  ui.children().each(function() {  
	    		    $(this).width($(this).width());  
	    		  });  
	    		  return ui;  
    		 },
	         opacity:0.75,
	         axis:"y",
	         cancel: 'input,.mceEditor,.mce-tinymce,textarea,select,button',
	         'placeholder':'placeholder2',
	         update: function(event, ui) {
	        	 
	        	 var url = "/core/helper/update-link-priority";
	        	 if($(".main-ul").hasClass("category")){
	        		 url = "/core/helper/update-category-priority";
	        	 }else if($(".main-ul").hasClass("article")){
	        		 url = "/core/helper/update-article-priority";
	        	 }else if($(".main-ul").hasClass("supplier")){
	        		 url = "/core/helper/update-supplier-priority";
	        	 }else if($(".main-ul").hasClass("cover")){
	        		 url = "/core/helper/update-cover-priority";
	        	 }else if($(".main-ul").hasClass("productCategory")){
	        		 url = "/core/helper/update-product-category-priority";
	        	 }else if($(".main-ul").hasClass("eshopProduct")){
	        		 url = "/core/helper/update-eshop-product-priority";
	        	 }else if($(".main-ul").hasClass("homepageSections")){
	        		 url = "/core/helper/update-homepage-section-priority";
	        	 }else if($(".main-ul").hasClass("homepageSectionProducts")){
	        		 url = "/core/helper/update-homepage-section-products-priority";
	        	 }else if($(".main-ul").hasClass("socialIcons")){
	        		 url = "/core/helper/update-social-icons-priority";
	        	 }else if($(".main-ul").hasClass("filter")){
	        		 url = "/core/helper/update-filter-priority";
	        	 }else if($(".main-ul").hasClass("product")){
	        		 url = "/core/helper/update-product-priority";
	        	 }
	        	 
	        	 console.log("Start position: " + ui.item.startPos);
	             console.log("New position: " + ui.item.index());
	             
	        	 if($(".main-ul").find("li").length > 0){
	        		 var lis = $(".main-ul").find("li");     
	        	 }else if($(".main-ul").find("tr").length > 0){
	        		 var lis = $(".main-ul").find("tr"); 
	        	 }
	             var count  = lis.size();
	             
	             if(count > 0){
	              
	            	 
	            	  var listID = "";
		              var startPos = ui.item.startPos;
		              var endPos = ui.item.index();
		              
		              if(startPos > endPos){
		            	  var tmp = startPos;
		            	  startPos = endPos;
		            	  endPos = tmp;
		              }
		              var i = 0;
		              
		              if($(".main-ul").hasClass("product")){
		            	  i = startPos;
		            	  count = endPos + 1;
		              }else{
		            	  startPos = 0;
		              }	              
		              
		              for(i;i < count ;i++){
		               
			               id = $(lis).eq(i).attr("id");
			               if(i == startPos){
			                listID += id;
			               }else{
			                listID += "-"+id;
			               }
		              }
		           
			           $.ajax({
					      type: "POST",
					      url: url,
					      data: "listID="+listID+"&startPos=" + startPos + "&endPos=" + endPos,
					      success: function(html){
					            
					      }
					  
					   }); 
	          
	             }
	          
	         },
	         start: function(e, ui){
	        	 ui.item.startPos = ui.item.index();
	        	 $(this).find('textarea:not(.no-MCE)').each(function(){
	                 tinyMCE.execCommand( 'mceRemoveEditor', false, $(this).attr('id') );               
	             });
	             
	             var radio_checked= {};
	 
	             $(this).find('input[type="radio"]', this).each(function(){
	                     if($(this).is(':checked'))
	                     radio_checked[$(this).attr('name')] = $(this).val();
	                     $(document).data('radio_checked', radio_checked);
	             });
	         },
	         stop: function(e,ui) {
	        	 $(this).find('textarea.basic:not(.no-MCE)').each(function(){
	              execTinyMCE(0,$(this).attr('id'))
	             });
	             
	             $(this).find('textarea.tiny:not(.no-MCE)').each(function(){
	              execTinyMCE(1,$(this).attr('id'))
	             });
	             
	             var radio_restore = $(document).data('radio_checked');
	             if(radio_restore){
		             $.each(radio_restore, function(index, value){
		                  $('input[name="'+index+'"][value="'+value+'"]').prop('checked', true);
		             });
	             }
	         }
	 
	   });
	  $(".sortListBasicLinks").sortable({
		   
	         revert:false,
	         cursor: "move",
	         dropOnEmpty: true,
	         scroll:true,
	         tolerance: 'pointer',
	         helper: 'clone',
	         opacity:0.75,
	         axis:"y",
	         cancel: 'input,.mceEditor,.mce-tinymce,textarea,select,button',
	         'placeholder':'placeholder2',
	         update: function(event, ui) {
	        	 
	        	 var url = "/core/helper/update-link-priority";
	        	 
	        	 
	        	 console.log("Start position: " + ui.item.startPos);
	             console.log("New position: " + ui.item.index());
	             
	        	 if($(".main-ul").find("li").length > 0){
	        		 var lis = $(".main-ul").find("li");     
	        	 }else if($(".main-ul").find("tr").length > 0){
	        		 var lis = $(".main-ul").find("tr"); 
	        	 }
	             var count  = lis.size();
	             
	             if(count > 0){
	              
	            	 
	            	  var listID = "";
		              var startPos = ui.item.startPos;
		              var endPos = ui.item.index();
		              
		              if(startPos > endPos){
		            	  var tmp = startPos;
		            	  startPos = endPos;
		            	  endPos = tmp;
		              }
		              var i = 0;
		              
		              if($(".main-ul").hasClass("product")){
		            	  i = startPos;
		            	  count = endPos + 1;
		              }else{
		            	  startPos = 0;
		              }	              
		              
		              for(i;i < count ;i++){
		               
			               id = $(lis).eq(i).attr("id");
			               if(i == startPos){
			                listID += id;
			               }else{
			                listID += "-"+id;
			               }
		              }
		           
			           $.ajax({
					      type: "POST",
					      url: url,
					      data: "listID="+listID+"&startPos=" + startPos + "&endPos=" + endPos,
					      success: function(html){
					            
					      }
					  
					   }); 
	          
	             }
	          
	         },
	         start: function(e, ui){
	        	 ui.item.startPos = ui.item.index();
	        	 $(this).find('textarea:not(.no-MCE)').each(function(){
	                 tinyMCE.execCommand( 'mceRemoveEditor', false, $(this).attr('id') );               
	             });
	             
	             var radio_checked= {};
	 
	             $(this).find('input[type="radio"]', this).each(function(){
	                     if($(this).is(':checked'))
	                     radio_checked[$(this).attr('name')] = $(this).val();
	                     $(document).data('radio_checked', radio_checked);
	             });
	         },
	         stop: function(e,ui) {
	        	 $(this).find('textarea.basic:not(.no-MCE)').each(function(){
	              execTinyMCE(0,$(this).attr('id'))
	             });
	             
	             $(this).find('textarea.tiny:not(.no-MCE)').each(function(){
	              execTinyMCE(1,$(this).attr('id'))
	             });
	             
	             var radio_restore = $(document).data('radio_checked');
	             if(radio_restore){
		             $.each(radio_restore, function(index, value){
		                  $('input[name="'+index+'"][value="'+value+'"]').prop('checked', true);
		             });
	             }
	         }
	 
	   });
	}

function sortListPhotos(){
	  $(".sortListPhotos").sortable({
	   
	         revert:false,
	         cursor: "move",
	         dropOnEmpty: true,
	         scroll:true,
	         tolerance: 'pointer',
	         helper: 'clone',
	         opacity:0.75,
	         cancel: 'input,.mceEditor,.mce-tinymce,textarea,select,button',
	         'placeholder':'placeholder2',
	         update: function(event, ui) { 
	          
	        	 var url = "/core/helper/update-link-priority";
	        	 if($(".main-ul").hasClass("photoPriority")){
	        		 url = "/core/helper/update-photo-priority";
	        	 }       	 
	        	 
	        	 var lis = $(".main-ul").find("div.photo");           
	             var count  = lis.size();
	             
	             if(count > 0){
	              
		              var listID = "";
		              
		              for(var i = 0;i < count ;i++){
		               
			               id = $(lis).eq(i).attr("id");
			               if(i == 0){
			                listID += id;
			               }else{
			                listID += "-"+id;
			               }
		              }
		           
			           $.ajax({
					      type: "POST",
					      url: url,
					      data: "listID="+listID,
					      success: function(html){
					            
					      }
					  
					   }); 
	          
	             }
	          
	         },
	         start: function(e, ui){
	          
	        	 $(this).find('textarea:not(.no-MCE)').each(function(){
	                 tinyMCE.execCommand( 'mceRemoveEditor', false, $(this).attr('id') );               
	             });
	             
	             var radio_checked= {};
	 
	             $(this).find('input[type="radio"]', this).each(function(){
	                     if($(this).is(':checked'))
	                     radio_checked[$(this).attr('name')] = $(this).val();
	                     $(document).data('radio_checked', radio_checked);
	             });
	         },
	         stop: function(e,ui) {
	        	 $(this).find('textarea.basic:not(.no-MCE)').each(function(){
	              execTinyMCE(0,$(this).attr('id'))
	             });
	             
	             $(this).find('textarea.tiny:not(.no-MCE)').each(function(){
	              execTinyMCE(1,$(this).attr('id'))
	             });
	             
	             var radio_restore = $(document).data('radio_checked');
	             if(radio_restore){
		             $.each(radio_restore, function(index, value){
		                  $('input[name="'+index+'"][value="'+value+'"]').prop('checked', true);
		             });
	             }
	         }
	 
	   });
	}
function setSectionControl(){
	
	$("div.section-control .dropdown-menu a").unbind("click");
	$("div.section-control .dropdown-menu a").click(function(e){
    	e.preventDefault();
    	
    	var elm = this;
    	var sectionData = $(elm).closest("section").attr("data");
    	var elementType = $(elm).data("elmtype"); 
    	renderElement(elementType,sectionData,function(html){
    		
    		$(elm).closest("section").find("ul.section-elements").append(html);
    		
    		addHeaderElement();
    		addMapElement();
    		addLinkElement();
    		addSublinkElement();
        	removeElement();
        	setLangs();
        	sortListBasic();
        	sortListPhotos();
        	selectInputType();
        	addFormElement();
        	addListElementRow();
        	addListElementCol();
        	addYtvElement();
        	crSublink();
        	selectLink();
        	
    	});

    });
	
}

function renderElement(elementType,sectionData,callback){
	
	$.ajax({
		type: "POST",
		url: "/core/helper/get-langs",
		success: function(data){
			
			var html;
			var data = JSON.parse(data);
			var flags = "";
			
			if(data.length > 1){
			    flags  = ' <span class="language-mutations">';
			    for(var i = 0; i < data.length; i++){
			    	
			    	var suffClass = data[i].suffix;
			    	if(i != 0)suffClass = data[i].suffix+"-dis";
			    	
			    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
			    }
			    flags += '</span> ';
			}
			
			switch(elementType){
			
				case "e-1":  html = renderHeaderElement(sectionData,data,flags);callback(html);break;
				case "e-2":  html = renderSectionHeaderElement(sectionData,data,flags);callback(html);break;
				case "e-3":  html = renderLinkElement(sectionData,data,flags);callback(html);break;
				case "e-4":  html = renderSublinkElement(sectionData,data,flags);callback(html);break;
				case "e-5":  html = renderTextElement(sectionData,data,flags);callback(html);break;
				case "e-6":  html = renderListElement(sectionData,data,flags);callback(html);break;
				case "e-7":  html = renderFormElement(sectionData,data,flags);callback(html);break;		
				case "e-8":        renderYouTubeVimeoElement(sectionData,function(data){callback(data);});break;
				case "e-9":        renderArticleElement(sectionData,data,flags,function(data){callback(data);});break;
				case "e-10": html = renderPhotoFileElement(sectionData,data,flags);callback(html);break;
				case "e-11": html = renderMapElement(sectionData,data,flags);callback(html);break;
			}
			

		}

	});
}

function renderHeaderElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=header-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
	
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center"><span class="badge badge-secondary">Header/Slider element</span> <span class="glyphicons glyphicons-move ml-auto mr-2"></span> <span class="glyphicons glyphicons-remove"></span></div>';
    html += '<div class="card-body">';
    
    html +='<div class="clearfix row">';
	    html +='<div class="col-sm-8">';
	    	html +='<div class="form-group">';
	    	for(var i = 0; i < data.length; i++){
	    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
	    		html += '<input class="form-control" placeholder="hlavní nadpis" type="text" name="h-e-h1-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
	    		html += '</span>';
	    	}
	    	html += flags;
	    	html +='</div>';
	    	html +='<div>';
	    	for(var i = 0; i < data.length; i++){
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
	    		html += '<input class="form-control" placeholder="podnadpis" type="text" name="h-e-h2-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
	    		html += '</span>';
	    	}
	    	html += flags;
	    	html +='</div>';
	    html +='</div>';
	    html +='<div class="col-sm-4">';
	    	html +='<div class="fileinput fileinput-new" data-provides="fileinput">';
	    		html +='<div class="fileinput-preview img-thumbnail" data-trigger="fileinput" style="width: 122px; height: 82px;float:left;margin-right:5px;"></div>';
	    		html +='<div style="float:left;vertical-align:middle;">';
	    			html +='<div class="my-2"><span class="btn text-uppercase btn-primary btn-file"><span class="fileinput-new">Vyberte obrázek</span><span class="fileinput-exists">Vyměnit</span><input type="file" name="h-e-file-'+sectionData+'-'+secHeaderElement+'[]"></span></div>';
	    			html +='<div><a href="#" class="btn text-uppercase btn-primary fileinput-exists" data-dismiss="fileinput">Odstranit</a></div>';
	    		html +='</div>';
	    	html +='</div>';
    html +='</div>';
    
    html += '</div>';
    html += '</div>';
    
    html += '<div class="card-footer">';
	    html += '<a class="btn text-uppercase btn-primary btn-xs add-header-element" href="#" title="Přidat element">Přidat element</a><input type="hidden" name="h-e-c-'+sectionData+'-'+secHeaderElement+'" value="1" />';
	html += '</div>';

    html += '<input type="hidden" name="is-element-'+sectionData+'[]" value="h-'+secHeaderElement+'" /></li>';
    
	return html;
}

function renderMapElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=map-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
	
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center">';
    	html += '<span class="badge badge-dark-fosfor">Map element</span>';
    	
	    html += '<span class="element-width form-inline ml-auto mr-2">';
		    html += '<label class="font-weight-bold mr-2">Šířka elementu:</label>';
		    html += '<select class="form-control form-control-sm" name="m-e-element-width-' + sectionData + '-' + secHeaderElement +'">';
			    html += '<option value="12">12 dílů (celá šířka)</option>';
			    html += '<option value="11">11 dílů</option>';
			    html += '<option value="10">10 dílů</option>';
			    html += '<option value="9">9 dílů</option>';
			    html += '<option value="8">8 dílů</option>';
			    html += '<option value="7">7 dílů</option>';
			    html += '<option value="6">6 dílů (polovina šířky)</option>';
			    html += '<option value="5">5 dílů</option>';
			    html += '<option value="4">4 díly</option>';
			    html += '<option value="3">3 díly</option>';
			    html += '<option value="2">2 díly</option>';
			    html += '<option value="1">1 díl</option>';
		    html += '</select>';
	    html += '</span>';
    	html += '<span class="glyphicons glyphicons-move mr-2"></span>';
    	html += '<span class="glyphicons glyphicons-remove"></span>';
	html += '</div>';
    html += '<div class="card-body">';
    
    html +='<div class="clearfix row">';
    
	    html +='<div class="col-xs-12 col-sm-4">';
			html +='<div class="form-group">';
			for(var i = 0; i < data.length; i++){
				
				var hid = "";
				if(i != 0)hid = "hidden-lang";
				
				html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input class="form-control" placeholder="nadpis" name="m-e-title-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"></textarea>';
				html += '</span>';
			}
			html += flags;
			html +='</div>';
		html +='</div>';
    	
	    html +='<div class="col-xs-12 col-sm-4">'
			html +='<div class="form-group"><input class="form-control" placeholder="souřadnice zeměpisné šířky" type="text" name="m-e-lat-'+sectionData+'-'+secHeaderElement+'[]" /></div>'
		html +='</div>'
    	html +='<div class="col-xs-12 col-sm-4">'
    		html +='<div class="form-group"><input class="form-control" placeholder="souřadnice zeměpisné dély" type="text" name="m-e-long-'+sectionData+'-'+secHeaderElement+'[]" /></div>'
    	html +='</div>'
	        
    html += '</div>';
    html += '</div>';
    
    html += '<div class="card-footer">';
	    html += '<a class="btn text-uppercase btn-primary btn-xs add-map-element" href="#" title="Přidat element">Přidat element</a><input type="hidden" name="m-e-c-'+sectionData+'-'+secHeaderElement+'" value="1" />';
	html += '</div>';

    html += '<input type="hidden" name="is-element-'+sectionData+'[]" value="m-'+secHeaderElement+'" /></li>';
    
	return html;
}

function renderArticleElement(sectionData,data,flags,callback){
	
	var elmCount         = $("input[name=article-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
		
		
	$.ajax({
		type: "POST",
		url: "/core/helper/get-all-links",
		success: function(response){
			
			var html = '<li><div class="card mb-4">';
		    html += '<div class="card-header d-flex align-items-center"><span class="badge badge-dark-green">Element zobrazení článků</span> <span class="glyphicons glyphicons-move ml-auto mr-2"></span> <span class="glyphicons glyphicons-remove"></span></div>';
		    html += '<div class="card-body">';
		    
			    html +='<div class="form-inline form-group">';
					html +='<div class="radio" style="padding-top:5px;">';
				
						html += '<input value="1" checked type="radio" name="a-e-type-'+sectionData+'-'+secHeaderElement+'"/> Zobrazit seznam všech článků včetně stránkování';
				
					html +='</div> ';
					//html +='<div class="form-group">';
				
						html += '<input style="width:41px;" class="form-control" value="20" type="hidden" name="a-e-pageCount-'+sectionData+'-'+secHeaderElement+'"/>';
				
					//html +='</div> ';
			
				html +='</div>';

				html +='<div class="form-group">';
			    	html +='<div class="form-inline">';
				    	html +='<div class="radio mr-2">';
			
				    		html += '<input value="2"  type="radio" name="a-e-type-'+sectionData+'-'+secHeaderElement+'"/> Zobrazit ';
			
				    	html +='</div> ';
				    	html +='<div class="form-group mr-2">';
			
				    		html += '<input style="width:41px;" class="form-control mr-2" value="4" type="text" name="a-e-newCount-'+sectionData+'-'+secHeaderElement+'"/> nejnovější články z odkazu';
			
				    	html +='</div> ';
				    	
				    	html +='<div class="form-group">';
				    		html +='<span id="selected-parent-link" class="selected-link" rel="'+sectionData+'-'+secHeaderElement+'">';
				    			html +='<span class="value form-control v-'+sectionData+'-'+secHeaderElement+'" style="width:320px;"></span>';
				    				html +='<span class="parrent-links">';
									html += response;
								html +='</span>';
								html +='<input type="hidden" name="a-e-lID-'+sectionData+'-'+secHeaderElement+'" value="0" id="selected-parent-input-'+sectionData+'-'+secHeaderElement+'" />';
							html +='</span>';
						html +='</div>';
				    	
				    html +='</div>';
			    html +='</div>';
			    
			    html +='<div class="form-group">';
		    	
			    	for(var i = 0; i < data.length; i++){
			    		var hid = "";
			    		if(i != 0)hid = "hidden-lang";
			    		
			    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
			    		html += '<input class="form-control" placeholder="Odkaz na seznam všech článků" type="text" name="a-e-url-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'"/>';
			    		html += '</span>';
			    	}
			    	html += flags;
			
				html +='</div>';
		    
		    html += '</div>';
		    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="a-'+secHeaderElement+'" /></li>';
		    		    
		    if(callback)callback(html);
		}

	});	
	
	
}

function renderPhotoFileElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=photo-file-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
	
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center">';
    	html += '<span class="badge badge-dark-yellow">Element fotky a soubory</span>';
    	html += '<span class="glyphicons glyphicons-move ml-auto mr-2"></span>';
    	html += '<span class="glyphicons glyphicons-remove"></span>';
    html += '</div>';
    html += '<div class="card-body">';
    
	    html +='<div class="form-group">';
	    
		    html +='<div class="fileinput fileinput-new" data-provides="fileinput">';
		    	html +='<div class="input-group">';
		    		html +='<div class="form-control uneditable-input d-flex align-items-center">';
		    			html +='<i class="glyphicons glyphicons-file fileinput-exists mr-2"></i> <span class="fileinput-filename" style="overflow:visible;"></span>';
		    		html +='</div>';
		    		html +='<div class="input-group-append">';
		    			html +='<div class="btn text-uppercase btn-primary btn-file">';
		    				html +='<span class="fileinput-new" >Vyberte</span>';
		    				html +='<span class="fileinput-exists">Změnit</span>';
		    				html +='<input type="file" multiple="multiple" name="ph-file-'+sectionData+'-'+secHeaderElement+'[]" />';
		    			html +='</div>';
		    			html +='<button type="button" class="btn text-uppercase btn-danger fileinput-exists" data-dismiss="fileinput" title="remove">';
		    				html +='Odstranit';
		    			html +='</button> ';   
		    		html +='</div>';
		    	html +='</div>';
		    html +='</div>';
	
		html +='</div>';

    html += '</div>';
    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="pf-'+secHeaderElement+'" /></li>';
    
	return html;
}

function renderSectionHeaderElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=section-header-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
	
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center">';
    	html += '<span class="badge badge-warning">Section header element</span>';
	    html += '<span class="element-width form-inline ml-auto mr-2">';
		    html += '<label class="font-weight-bold mr-2">Šířka elementu:</label>';
		    html += '<select class="form-control form-control-sm" name="s-h-e-element-width-' + sectionData + '-' + secHeaderElement +'">';
			    html += '<option value="12">12 dílů (celá šířka)</option>';
			    html += '<option value="11">11 dílů</option>';
			    html += '<option value="10">10 dílů</option>';
			    html += '<option value="9">9 dílů</option>';
			    html += '<option value="8">8 dílů</option>';
			    html += '<option value="7">7 dílů</option>';
			    html += '<option value="6">6 dílů (polovina šířky)</option>';
			    html += '<option value="5">5 dílů</option>';
			    html += '<option value="4">4 díly</option>';
			    html += '<option value="3">3 díly</option>';
			    html += '<option value="2">2 díly</option>';
			    html += '<option value="1">1 díl</option>';
		    html += '</select>';
		html += '</span>';
    	html += '<span class="glyphicons glyphicons-move mr-2"></span>';
    	html += '<span class="glyphicons glyphicons-remove section-header"></span>';
	html += '</div>';
    html += '<div class="card-body">';
    
    html +='<div class="form-group">';
    html +='<div class="row">';
    html +='<div class="col-xs-12 col-sm-6">';
    for(var i = 0; i < data.length; i++){
    	var hid = "";
		if(i != 0)hid = "hidden-lang";
		
		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
    	html += '<input class="form-control" placeholder="nadpis" type="text" name="s-h-e-h1-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'"/>';
    	html += '</span>';
    }
    html += flags;
    html +='</div>';
    html +='<div class="col-xs-12 col-sm-6">';
    	html +='<select class="form-control" name="s-h-e-h-type-'+sectionData+'-'+secHeaderElement+'">';
    	html +='<option value="h1">Nadpis H1</option>';
    	html +='<option value="h2">Nadpis H2</option>';
    	html +='</select>';
    html +='</div>';
    html +='<div class="clearfix"></div>';
    html +='</div>';
    html +='</div>';
    
    html +='<div class="form-group">';
    for(var i = 0; i < data.length; i++){
    	var hid = "";
		if(i != 0)hid = "hidden-lang";
		
		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
    	html += '<input class="form-control" placeholder="popisek pod nadpisem" type="text" name="s-h-e-h2-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'"/>';
    	html += '</span>';
    }
    html += flags;
    html +='</div>';
    
    html +='<div class="radio">';

	    html += '<div class="form-check form-check-inline">';
	    	html += '<input checked="checked" class="form-check-input" type="radio" name="s-h-e-align-'+sectionData+'-'+secHeaderElement+'" id="s-h-e-align-'+sectionData+'-'+secHeaderElement+'1" value="center">';
	    	html += '<label class="form-check-label" for="s-h-e-align-'+sectionData+'-'+secHeaderElement+'1">vycentrovat</label>';
	    html += '</div>';
	    html += '<div class="form-check form-check-inline">';
			html += '<input class="form-check-input" type="radio" name="s-h-e-align-'+sectionData+'-'+secHeaderElement+'" id="s-h-e-align-'+sectionData+'-'+secHeaderElement+'2" value="left">';
			html += '<label class="form-check-label" for="s-h-e-align-'+sectionData+'-'+secHeaderElement+'2">zarovnat doleva</label>';
		html += '</div>';
	    html += '<div class="form-check form-check-inline">';
			html += '<input class="form-check-input" type="radio" name="s-h-e-align-'+sectionData+'-'+secHeaderElement+'" id="s-h-e-align-'+sectionData+'-'+secHeaderElement+'3" value="right">';
			html += '<label class="form-check-label" for="s-h-e-align-'+sectionData+'-'+secHeaderElement+'3">zarovnat doprava</label>';
		html += '</div>';
  
    html +='</div>';
    
    html += '</div>';
    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="sh-'+secHeaderElement+'" /></li>';
    
	return html;
}
function renderLinkElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=link-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
	
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center">';
    	html += '<span class="badge badge-success">Link element</span>';
	    html += '<span class="element-width form-inline ml-auto mr-2">';
		    html += '<label class="font-weight-bold mr-2">Šířka elementu:</label>';
		    html += '<select class="form-control form-control-sm" name="l-e-element-width-' + sectionData + '-' + secHeaderElement +'">';
			    html += '<option value="12">12 dílů (celá šířka)</option>';
			    html += '<option value="11">11 dílů</option>';
			    html += '<option value="10">10 dílů</option>';
			    html += '<option value="9">9 dílů</option>';
			    html += '<option value="8">8 dílů</option>';
			    html += '<option value="7">7 dílů</option>';
			    html += '<option value="6">6 dílů (polovina šířky)</option>';
			    html += '<option value="5">5 dílů</option>';
			    html += '<option value="4">4 díly</option>';
			    html += '<option value="3">3 díly</option>';
			    html += '<option value="2">2 díly</option>';
			    html += '<option value="1">1 díl</option>';
		    html += '</select>';
		html += '</span>';
    	html += '<span class="glyphicons glyphicons-move mr-2"></span>';
    	html += '<span class="glyphicons glyphicons-remove link-element"></span>';
		html += '</div>';
    html += '<div class="card-body">';
   		
	html +='<div class="form-group">';

		html += '<label class="label-concrete">Počet sloupců v řádku</label>';
		html += '<select class="form-control" name="l-e-colCount-'+sectionData+'-'+secHeaderElement+'">';
		html += '<option value="1">1</option>';
		html += '<option value="2">2</option>';
		html += '<option value="3">3</option>';
		html += '<option value="4">4</option>';
		html += '<option value="6">6</option>';
		html += '</select>';

	html +='</div> ';
	
	html +='<hr /> ';

	html +='<div class="link-elm-cont row"> ';
	    html +='<div class="col-sm-4 link-element-item">';
			html +='<div class="fileinput fileinput-new" data-provides="fileinput">';
				html +='<div class="fileinput-preview img-thumbnail" data-trigger="fileinput" style="width: 122px; height: 82px;float:left;margin-right:5px;"></div>';
				html +='<div style="float:left;vertical-align:middle;">';
					html +='<div class="my-2"><span class="btn text-uppercase btn-primary btn-file"><span class="fileinput-new">Vyberte obrázek</span><span class="fileinput-exists">Vyměnit</span><input type="file" name="l-e-file-'+sectionData+'-'+secHeaderElement+'[]"></span></div>';
					html +='<div><a href="#" class="btn text-uppercase btn-danger fileinput-exists" data-dismiss="fileinput">Odstranit</a></div>';
				html +='</div>';
			html +='</div>';
			
			html +='<div class="form-group" >';
				html +='<div class="checkbox" >';
					html += '<label><input class="create-sublink" value="1" type="checkbox" name="l-e-isSublink-'+sectionData+'-'+secHeaderElement+'-1" value="1"/> Automaticky vytvořit pododkaz</label>';
				html +='</div>';
			html +='</div>';
	
			html +='<div class="form-group url">';
		    	for(var i = 0; i < data.length; i++){
		    		
		    		var hid = "";
		    		if(i != 0)hid = "hidden-lang";
		    		
		    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
		    		html += '<input class="form-control" placeholder="url" type="text" name="l-e-url-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
		    		html += '</span>';
		    	}
		    	html += flags;
	    	html +='</div>';
		    html +='<div class="form-group">';
		    	for(var i = 0; i < data.length; i++){
		    		
		    		var hid = "";
		    		if(i != 0)hid = "hidden-lang";
		    		
		    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
		    		html += '<input class="form-control" placeholder="h2 nadpis" type="text" name="l-e-h2-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
		    		html += '</span>';
		    	}
		    	html += flags;
		    html +='</div>';
		    html +='<div class="form-group">';
		    	for(var i = 0; i < data.length; i++){
		    		var hid = "";
		    		if(i != 0)hid = "hidden-lang";
		    		
		    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
		    		html +='<textarea id="l-e-'+sectionData+'-'+i+'-'+secHeaderElement+'" class="form-control tiny" placeholder="text" type="text" name="l-e-text-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"></textarea>';
		    		html += '</span>';
		    		html += '<script>execTinyMCE(1,\'l-e-'+sectionData+'-'+i+'-'+secHeaderElement+'\');</script>';
		    	}
		    	html += flags;
		    html +='</div>';
		    html +='<input value="" type="hidden" name="l-e-linkID-'+sectionData+'-'+secHeaderElement+'[]" />';
	    html +='</div>';
    html +='</div>';
    
    html += '</div><div class="card-footer"><a class="btn text-uppercase btn-primary btn-xs add-link-element" href="#" title="Přidat element">Přidat element</a><input type="hidden" name="l-e-c-'+sectionData+'-'+secHeaderElement+'" value="1" /></div>';
    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="l-'+secHeaderElement+'" /></li>';
        
	return html;
}

function renderSublinkElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=sublink-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
	
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center"><span class="badge badge-danger">Sublink element</span> <span class="glyphicons glyphicons-move ml-auto mr-2"></span> <span class="glyphicons glyphicons-remove sublink-element"></span></div>';
    html += '<div class="card-body">';
    
    html +='<div class="form-group">';

		html += '<label class="label-concrete">Počet sloupců v řádku</label>';
		html += '<select class="form-control" name="sl-e-colCount-'+sectionData+'-'+secHeaderElement+'">';
		html += '<option value="1">1</option>';
		html += '<option value="2">2</option>';
		html += '<option value="3">3</option>';
		html += '<option value="4">4</option>';
		html += '<option value="6">6</option>';
		html += '</select>';
	
	html +='</div> ';
    
	html +='<hr /> ';
    html +='<div class="col-sm-4 sublink-element-item">';
		
	    html +='<div class="form-group">';
	    	for(var i = 0; i < data.length; i++){
	    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
	    		html += '<input class="form-control" placeholder="h2 nadpis" type="text" name="sl-e-h2-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
	    		html += '</span>';
	    	}
	    	html += flags;
	    html +='</div>';
	    html +='<div class="form-group">';
	    	for(var i = 0; i < data.length; i++){
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
	    		html +='<textarea id="sl-e-'+sectionData+'-'+i+'-'+secHeaderElement+'" class="form-control tiny" placeholder="text" type="text" name="sl-e-text-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"></textarea>';
	    		html += '</span>';
	    		html += '<script>execTinyMCE(1,\'sl-e-'+sectionData+'-'+i+'-'+secHeaderElement+'\');</script>';
	    	}
	    	html += flags;
	    html +='</div>';
	    html +='<input value="" type="hidden" name="sl-e-linkID-'+sectionData+'-'+secHeaderElement+'[]" />';
    html +='</div>';
    	    
    html += '</div><div class="card-footer"><a class="btn text-uppercase btn-primary btn-xs add-sublink-element" href="#" title="Přidat element">Přidat element</a></div>';
    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="sl-'+secHeaderElement+'" /></li>';
        
	return html;
}

function renderTextElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=text-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
		    
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center">';
    	html += '<span class="badge badge-info">Text element</span>';
	    html += '<span class="element-width form-inline ml-auto mr-2">';
		    html += '<label class="font-weight-bold mr-2">Šířka elementu:</label>';
		    html += '<select class="form-control form-control-sm" name="t-e-element-width-' + sectionData + '-' + secHeaderElement +'">';
			    html += '<option value="12">12 dílů (celá šířka)</option>';
			    html += '<option value="11">11 dílů</option>';
			    html += '<option value="10">10 dílů</option>';
			    html += '<option value="9">9 dílů</option>';
			    html += '<option value="8">8 dílů</option>';
			    html += '<option value="7">7 dílů</option>';
			    html += '<option value="6">6 dílů (polovina šířky)</option>';
			    html += '<option value="5">5 dílů</option>';
			    html += '<option value="4">4 díly</option>';
			    html += '<option value="3">3 díly</option>';
			    html += '<option value="2">2 díly</option>';
			    html += '<option value="1">1 díl</option>';
		    html += '</select>';
		html += '</span>';
    	html += '<span class="glyphicons glyphicons-move mr-2"></span>';
    	html += '<span class="glyphicons glyphicons-remove text-element"></span>';
	html += '</div>';
    html += '<div class="card-body">';
    
    html += '<div>';
    html += flags;
    for(var i = 0; i < data.length; i++){
    	var hid = "";
		if(i != 0)hid = "hidden-lang";
		
		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
    	html += '<textarea id="t-e-'+sectionData+'-'+i+'-'+secHeaderElement+'" class="form-control basic" placeholder="text" type="text" name="t-e-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'"><div class="container"><div class="text-element"><p>text</p></div></div></textarea>';
    	html += '</span>';
    	html += '<script>execTinyMCE(0,\'t-e-'+sectionData+'-'+i+'-'+secHeaderElement+'\');</script>';
    }
    html += '</div>';
    html += '</div>';
    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="t-'+secHeaderElement+'" /></li>';
        
	return html;
}
function renderFormElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=form-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
		    
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center">';
    	html += '<span class="badge badge-primary">Form element</span>';
	    html += '<span class="element-width form-inline ml-auto mr-2">';
		    html += '<label class="font-weight-bold mr-2">Šířka elementu:</label>';
		    html += '<select class="form-control form-control-sm" name="f-e-element-width-' + sectionData + '-' + secHeaderElement +'">';
			    html += '<option value="12">12 dílů (celá šířka)</option>';
			    html += '<option value="11">11 dílů</option>';
			    html += '<option value="10">10 dílů</option>';
			    html += '<option value="9">9 dílů</option>';
			    html += '<option value="8">8 dílů</option>';
			    html += '<option value="7">7 dílů</option>';
			    html += '<option value="6">6 dílů (polovina šířky)</option>';
			    html += '<option value="5">5 dílů</option>';
			    html += '<option value="4">4 díly</option>';
			    html += '<option value="3">3 díly</option>';
			    html += '<option value="2">2 díly</option>';
			    html += '<option value="1">1 díl</option>';
		    html += '</select>';
		html += '</span>';
    	html += '<span class="glyphicons glyphicons-move mr-2"></span>';
    	html += '<span class="glyphicons glyphicons-remove form-element"></span>';
	html += '</div>';
    html += '<div class="card-body">';
    
	    html += '<div>';
	    	html += '<input class="form-control" placeholder="email" type="text" name="f-e-email-'+sectionData+'-'+secHeaderElement+'"/>';
	    html += '</div>';
	    html += '<hr/>';
	    html += '<div class="inputs">'
		    html += '<div class="form-inline align-items-start">';	    		
			    html += '<div class="form-group d-block mr-2">';
				    for(var i = 0; i < data.length; i++){
				    	var hid = "";
						if(i != 0)hid = "hidden-lang";
						
						html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
						html += '<input class="form-control" placeholder="název položky" type="text" name="f-e-name-'+sectionData+'-'+secHeaderElement+'-0-'+data[i].suffix+'"/>';
						html += '</span>';
				    }
				    html += flags;
			    html += '</div> ';
			    html += '<div class="form-group">';
				    html += '<select class="form-control input-type" name="f-e-type-'+sectionData+'-'+secHeaderElement+'-0">';
				    	html += '<option value="0"> --- typ položky --- </option>';
				    	html += '<option value="text-'+sectionData+'-'+secHeaderElement+'-0">text</option>';
				    	html += '<option value="checkbox-'+sectionData+'-'+secHeaderElement+'-0">checkbox</option>';
				    	html += '<option value="radio-'+sectionData+'-'+secHeaderElement+'-0">radio</option>';
				    	html += '<option value="dropdown-'+sectionData+'-'+secHeaderElement+'-0">dropdown</option>';
				    	html += '<option value="textarea-'+sectionData+'-'+secHeaderElement+'-0">textarea</option>';
				    html += '</select>';
				html += '</div> ';
				html += '<div class="inline-html">';
					
			    html += '</div> ';
		    html += '</div>';
			html += '<div class="inputs-elements">';
			
			html += '</div> ';			
	    html += '</div>';
    html += '</div><input type="hidden" value="0" name="form-element-values-count-'+sectionData+'-'+secHeaderElement+'" /><div class="card-footer"><a class="btn text-uppercase btn-primary btn-xs add-form-element" href="#" title="Přidat položku">Přidat položku</a></div>';
    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="f-'+secHeaderElement+'" /></li>';
        
	return html;
}

function renderListElement(sectionData,data,flags){
	
	var elmCount         = $("input[name=list-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
		    
	var html = '<li><div class="card mb-4">';
    html += '<div class="card-header d-flex align-items-center">';
    	html += '<span class="badge badge-purple">List element</span>';
	    html += '<span class="element-width form-inline ml-auto mr-2">';
		    html += '<label class="font-weight-bold mr-2">Šířka elementu:</label>';
		    html += '<select class="form-control form-control-sm" name="element-width-' + sectionData + '-' + secHeaderElement +'">';
			    html += '<option value="12">12 dílů (celá šířka)</option>';
			    html += '<option value="11">11 dílů</option>';
			    html += '<option value="10">10 dílů</option>';
			    html += '<option value="9">9 dílů</option>';
			    html += '<option value="8">8 dílů</option>';
			    html += '<option value="7">7 dílů</option>';
			    html += '<option value="6">6 dílů (polovina šířky)</option>';
			    html += '<option value="5">5 dílů</option>';
			    html += '<option value="4">4 díly</option>';
			    html += '<option value="3">3 díly</option>';
			    html += '<option value="2">2 díly</option>';
			    html += '<option value="1">1 díl</option>';
		    html += '</select>';
		html += '</span>';
    	html += '<span class="glyphicons glyphicons-move mr-2"></span>';
    	html += '<span class="glyphicons glyphicons-remove form-element"></span>';
	html += '</div>';
    html += '<div class="card-body">';
    
	    html += '<table class="table table-striped">';
	    	html += '<thead>';
	    		html += '<tr><th>';  			
	    			html += '<div>';
					    for(var i = 0; i < data.length; i++){
					    	var hid = "";
							if(i != 0)hid = "hidden-lang";
							
							html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
							html += '<input class="form-control" placeholder="nadpis sloupce" type="text" name="li-e-name-'+sectionData+'-'+secHeaderElement+'-0-'+data[i].suffix+'"/>';
							html += '</span>';
					    }
					    html += flags;
				    html += '</div>'; 
				    html += '<div class="radio">';
				    	html += '<div class="radio-label"><strong>Aktivní:</strong></div>';
				    	html += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-active-'+sectionData+'-'+secHeaderElement+'-0" id="showArticles" value="1" checked="checked"> Ano </label>';
				    	html += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-active-'+sectionData+'-'+secHeaderElement+'-0" id="showArticles" value="0"> Ne </label>';
				    html += '</div>';
				    	
				    html += '<div class="radio">';
				    	html += '<div class="radio-label"><strong>Filtrovaný:</strong></div>';
				    	html += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-filtered-'+sectionData+'-'+secHeaderElement+'-0" id="showArticles" value="1" checked="checked"> Ano </label>';
				    	html += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-filtered-'+sectionData+'-'+secHeaderElement+'-0" id="showArticles" value="0"> Ne </label>';
				    html += '</div>';
				    	
				html += '</th></tr>';
	    	html += '</thead>';
	    	html += '<tbody>';
	    		html += '<tr><td>';
	    			html += '<div class="">';
					    for(var i = 0; i < data.length; i++){
					    	var hid = "";
							if(i != 0)hid = "hidden-lang";
							
							html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
							html += '<input class="form-control" placeholder="hodnota" type="text" name="li-e-row-'+sectionData+'-'+secHeaderElement+'-0-'+data[i].suffix+'[]"/>';
							html += '</span>';
					    }
					    html += flags;
					    html += '<input type="hidden" name="li-e-linkID-'+sectionData+'-'+secHeaderElement+'-0[]" />';
				    html += '</div> ';
				html += '</td></tr>';
	        html += '</tbody>';
	    html += '</table>';
    	
    html += '</div><input type="hidden" value="0" name="list-element-col-count-'+sectionData+'-'+secHeaderElement+'" /><div class="card-footer"><a class="btn text-uppercase btn-primary btn-xs add-list-element-row" href="#" title="Přidat řádek">Přidat řádek</a> <a class="btn text-uppercase btn-primary btn-xs add-list-element-col" href="#" title="Přidat sloupec">Přidat sloupec</a></div>';
    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="li-'+secHeaderElement+'" /></li>';
        
	return html;
}

function renderYouTubeVimeoElement(sectionData,callback){
	
	
	var elmCount         = $("input[name=ytv-element-count-"+sectionData+"]");
	var secHeaderElement = elmCount.val();
		secHeaderElement++;
		elmCount.val(secHeaderElement);
		
	$.ajax({
		type: "POST",
		url: "/core/helper/get-categories",
		success: function(data){
			data = JSON.parse(data);
			var html = "";
						
			html += '<li><div class="card mb-4">';
		    html += '<div class="card-header d-flex align-items-center">';
		    html += '<span class="badge badge-brown">YoutTube,Vimeo element</span>';
			    html += '<span class="element-width form-inline ml-auto mr-2">';
				    html += '<label class="font-weight-bold mr-2">Šířka elementu:</label>';
				    html += '<select class="form-control form-control-sm" name="ytv-element-width-' + sectionData + '-' + secHeaderElement +'">';
					    html += '<option value="12">12 dílů (celá šířka)</option>';
					    html += '<option value="11">11 dílů</option>';
					    html += '<option value="10">10 dílů</option>';
					    html += '<option value="9">9 dílů</option>';
					    html += '<option value="8">8 dílů</option>';
					    html += '<option value="7">7 dílů</option>';
					    html += '<option value="6">6 dílů (polovina šířky)</option>';
					    html += '<option value="5">5 dílů</option>';
					    html += '<option value="4">4 díly</option>';
					    html += '<option value="3">3 díly</option>';
					    html += '<option value="2">2 díly</option>';
					    html += '<option value="1">1 díl</option>';
				    html += '</select>';
				html += '</span>';
		    	html += '<span class="glyphicons glyphicons-move mr-2"></span>';
		    	html += '<span class="glyphicons glyphicons-remove ytv-element"></span>';
			html += '</div>';
		    html += '<div class="card-body">';
		    	html += '<div class="col-xs-6">';	
					html +='<div class="form-group">';	    	
				    	html += '<input placeholder="URL videa (youtube, vimeo)" class="form-control" type="text" name="ytv-element-'+sectionData+'-'+secHeaderElement+'[]" />';	    		
				    html +='</div>';
				html += '</div>';
				
				if(data.length > 0){
					html += '<div class="col-xs-6">';	
						html +='<div class="form-group">';	    	
					    	html += '<select class="form-control" name="ytv-c-element-'+sectionData+'-'+secHeaderElement+'[]">';
					    		html += '<option value="0"> -- vyberte kategorii -- </option>';
					    		
					    		for(var i = 0;i < data.length;i++){
					    			html += '<option value="'+data[i].categoryID+'"> '+data[i].title+' </option>';
					    		}
					    		
					    	html += '</select>';
					    html +='</div>';
					html += '</div>';
				}
		    	    
		    html += '</div><div class="card-footer"><a class="btn text-uppercase btn-primary btn-xs add-ytv-element" href="#" title="Přidat video">Přidat video</a></div>';
		    html += '</div><input type="hidden" name="is-element-'+sectionData+'[]" value="ytv-'+secHeaderElement+'" /></li>';

		    if(callback)callback(html);
		}

	});

}

function addFormElement(){
	
	$("a.add-form-element").unbind("click");
	$("a.add-form-element").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
							
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}
				
				var sectionData      = thisElm.closest("section").attr("data");				
				var secHeaderElement = $("input[name=form-element-count-"+sectionData+"]").val();
				
				var formElmValueElm  = $("input[name=form-element-values-count-"+sectionData+"-"+secHeaderElement+"]");
				var formElmValue = formElmValueElm.val();
					formElmValue++;
					formElmValueElm.val(formElmValue);
				
				html  = '<hr/><div class="inputs">'
				    html += '<div class="form-inline align-items-start">';	    		
					    html += '<div class="form-group d-block mr-2">';
						    for(var i = 0; i < data.length; i++){
						    	var hid = "";
								if(i != 0)hid = "hidden-lang";
								
								html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
								html += '<input class="form-control" placeholder="název položky" type="text" name="f-e-name-'+sectionData+'-'+secHeaderElement+'-'+formElmValue+'-'+data[i].suffix+'"/>';
								html += '</span>';
						    }
						    html += flags;
					    html += '</div> ';
					    html += '<div class="form-group">';
						    html += '<select class="form-control input-type" name="f-e-type-'+sectionData+'-'+secHeaderElement+'-'+formElmValue+'">';
						    	html += '<option value="0"> --- typ položky --- </option>';
						    	html += '<option value="text-'+sectionData+'-'+secHeaderElement+'-'+formElmValue+'">text</option>';
						    	html += '<option value="checkbox-'+sectionData+'-'+secHeaderElement+'-'+formElmValue+'">checkbox</option>';
						    	html += '<option value="radio-'+sectionData+'-'+secHeaderElement+'-'+formElmValue+'">radio</option>';
						    	html += '<option value="dropdown-'+sectionData+'-'+secHeaderElement+'-'+formElmValue+'">dropdown</option>';
						    	html += '<option value="textarea-'+sectionData+'-'+secHeaderElement+'-'+formElmValue+'">textarea</option>';
						    html += '</select>';
						html += '</div> ';
				    html += '</div>';
					html += '<div class="inputs-elements">';
					
					html += '</div> ';			
			    html += '</div>';
				
			    thisElm.closest("div.card").find("div.card-body").append(html);
				
				setLangs();
				selectInputType();
				removeElement();

			}

		});
		
		
	});
	
}

function addHeaderElement(){
	
	$("a.add-header-element").unbind("click");
	$("a.add-header-element").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
							
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}
				
				var sectionData      = thisElm.closest("section").attr("data");
				
				var secHeaderElement = $("input[name=header-element-count-"+sectionData+"]").val();
				
				var lC = $('input[name=h-e-c-'+sectionData+'-'+secHeaderElement+']').val();
				lC++;
				$('input[name=h-e-c-'+sectionData+'-'+secHeaderElement+']').val(lC);
				
				var html ='<div class="clearfix row">';
			    html +='<div class="col-sm-8">';
			    	html +='<div class="form-group">';
			    	for(var i = 0; i < data.length; i++){
			    		
			    		var hid = "";
			    		if(i != 0)hid = "hidden-lang";
			    		
			    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
			    		html += '<input class="form-control" placeholder="hlavní nadpis" type="text" name="h-e-h1-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
			    		html += '</span>';
			    	}
			    	html += flags;
			    	html +='</div>';
			    	html +='<div>';
			    	for(var i = 0; i < data.length; i++){
			    		var hid = "";
			    		if(i != 0)hid = "hidden-lang";
			    		
			    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
			    		html += '<input class="form-control" placeholder="podnadpis" type="text" name="h-e-h2-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
			    		html += '</span>';
			    	}
			    	html += flags;
			    	html +='</div>';
			    html +='</div>';
			    html +='<div class="col-sm-4">';
			    	html +='<div class="fileinput fileinput-new" data-provides="fileinput">';
			    		html +='<div class="fileinput-preview img-thumbnail" data-trigger="fileinput" style="width: 122px; height: 82px;float:left;margin-right:5px;"></div>';
			    		html +='<div style="float:left;vertical-align:middle;">';
			    			html +='<div class="my-2"><span class="btn text-uppercase btn-primary btn-file"><span class="fileinput-new">Vyberte obrázek</span><span class="fileinput-exists">Vyměnit</span><input type="file" name="h-e-file-'+sectionData+'-'+secHeaderElement+'[]"></span></div>';
			    			html +='<div><a href="#" class="btn text-uppercase btn-primary fileinput-exists" data-dismiss="fileinput">Odstranit</a></div>';
			    		html +='</div>';
			    	html +='</div>';
		        html +='</div>';
		        
			    thisElm.closest("div.card").find("div.card-body").append(html);
				
				setLangs();				

			}

		});
		
		
	});
	
}

function addMapElement(){
	
	$("a.add-map-element").unbind("click");
	$("a.add-map-element").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
							
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}
				
				var sectionData      = thisElm.closest("section").attr("data");
				
				var secHeaderElement = $("input[name=map-element-count-"+sectionData+"]").val();

				var html ='<div class="clearfix row">';
					
				    html +='<div class="col-xs-12 col-sm-4">';
			    	    html +='<div class="form-group">';
				    	for(var i = 0; i < data.length; i++){
				    		
				    		var hid = "";
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<input class="form-control" placeholder="nadpis" name="m-e-title-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
				    		html += '</span>';
				    	}
				    	html += flags;
				    	html +='</div>';
				    html +='</div>';
				
					html +='<div class="col-xs-12 col-sm-4">';
						html +='<div class="form-group"><input class="form-control" placeholder="souřadnice zeměpisné šířky" type="text" name="m-e-lat-'+sectionData+'-'+secHeaderElement+'[]" /></div>'
					html +='</div>';
					html +='<div class="col-xs-12 col-sm-4">';
						html +='<div class="form-group"><input class="form-control" placeholder="souřadnice zeměpisné dély" type="text" name="m-e-long-'+sectionData+'-'+secHeaderElement+'[]" /></div>'
			        html +='</div>';
				   
				html +='</div>';				
			    thisElm.closest("div.card").find("div.card-body").append(html);				
				setLangs();				

			}

		});
				
	});
	
}

function addLinkElement(){
		
	$("a.add-link-element").unbind("click");
	$("a.add-link-element").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
							
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}
				
				var sectionData      = thisElm.closest("section").attr("data");
				
				var secHeaderElement = $("input[name=link-element-count-"+sectionData+"]").val();
				
				var lC = $('input[name=l-e-c-'+sectionData+'-'+secHeaderElement+']').val();
				lC++;
				$('input[name=l-e-c-'+sectionData+'-'+secHeaderElement+']').val(lC);
				
				var html ='<div class="col-sm-4 link-element-item">';
					html +='<div class="fileinput fileinput-new" data-provides="fileinput">';
						html +='<div class="fileinput-preview img-thumbnail" data-trigger="fileinput" style="width: 122px; height: 82px;float:left;margin-right:5px;"></div>';
						html +='<div style="float:left;vertical-align:middle;">';
							html +='<div class="my-2"><span class="btn text-uppercase btn-primary btn-file"><span class="fileinput-new">Vyberte obrázek</span><span class="fileinput-exists">Vyměnit</span><input type="file" name="l-e-file-'+sectionData+'-'+secHeaderElement+'[]"></span></div>';
							html +='<div><a href="#" class="btn text-uppercase btn-danger fileinput-exists" data-dismiss="fileinput">Odstranit</a></div>';
						html +='</div>';
					html +='</div>';

					html +='<div class="form-group" >';
						html +='<div class="checkbox" >';
							html += '<label><input class="create-sublink" type="checkbox" name="l-e-isSublink-'+sectionData+'-'+secHeaderElement+'-'+lC+'" value="1" /> Automaticky vytvořit pododkaz</label>';
						html +='</div>';
					html +='</div>';
					
					html +='<div class="form-group url">';
				    	for(var i = 0; i < data.length; i++){
				    		
				    		var hid = "";
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<input class="form-control url" placeholder="url" type="text" name="l-e-url-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
				    		html += '</span>';
				    	}
				    	html += flags;
			    	html +='</div>';
				    html +='<div class="form-group">';
				    	for(var i = 0; i < data.length; i++){
				    		
				    		var hid = "";
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<input class="form-control" placeholder="h2 nadpis" type="text" name="l-e-h2-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
				    		html += '</span>';
				    	}
				    	html += flags;
				    html +='</div>';
				    html +='<div class="form-group">';
				    	for(var i = 0; i < data.length; i++){
				    		var hid = "";
				    		var randomNum = Math.floor((Math.random() * 1000 * (i+1)) + 1);
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<textarea id="l-e-'+sectionData+'-'+i+'-'+secHeaderElement+'-'+randomNum+'" class="form-control tiny" placeholder="text" type="text" name="l-e-text-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"></textarea>';
				    		html += '</span>';
				    		html += '<script>execTinyMCE(1,\'l-e-'+sectionData+'-'+i+'-'+secHeaderElement+'-'+randomNum+'\');</script>';
				    	}
				    	html += flags;
				    html +='</div>';
			    html +='</div>';
				
			    thisElm.closest("div.card").find("div.link-elm-cont").append(html);
				
				setLangs();
				crSublink();
				

			}

		});
		
		
	});
	
}

function addSublinkElement(){
	
	$("a.add-sublink-element").unbind("click");
	$("a.add-sublink-element").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
							
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}
				
				var sectionData      = thisElm.closest("section").attr("data");				
				var secHeaderElement = $("input[name=sublink-element-count-"+sectionData+"]").val();
								
				var html ='<div class="col-sm-4 sublink-element-item">';
					
				    html +='<div class="form-group">';
				    	for(var i = 0; i < data.length; i++){
				    		
				    		var hid = "";
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<input class="form-control" placeholder="h2 nadpis" type="text" name="sl-e-h2-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"/>';
				    		html += '</span>';
				    	}
				    	html += flags;
				    html +='</div>';
				    html +='<div class="form-group">';
				    	for(var i = 0; i < data.length; i++){
				    		var hid = "";
				    		var randomNum = Math.floor((Math.random() * 1000 * (i+1)) + 1);
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<textarea id="sl-e-'+sectionData+'-'+i+'-'+secHeaderElement+'-'+randomNum+'" class="form-control tiny" placeholder="text" type="text" name="sl-e-text-'+sectionData+'-'+secHeaderElement+'-'+data[i].suffix+'[]"></textarea>';
				    		html += '</span>';
				    		html += '<script>execTinyMCE(1,\'sl-e-'+sectionData+'-'+i+'-'+secHeaderElement+'-'+randomNum+'\');</script>';
				    	}
				    	html += flags;
				    html +='</div>';
				    html +='<input value="" type="hidden" name="sl-e-linkID-'+sectionData+'-'+secHeaderElement+'[]" />';
			    html +='</div>';
				
			    thisElm.closest("div.card").find("div.card-body").append(html);
				
				setLangs();

			}

		});

	});
	
}

function addYtvElement(){
	
	$("a.add-ytv-element").unbind("click");
	$("a.add-ytv-element").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		var html = "";
		var sectionData      = thisElm.closest("section").attr("data");				
		var secHeaderElement = $("input[name=ytv-element-count-"+sectionData+"]").val();

		$.ajax({
			type: "POST",
			url: "/core/helper/get-categories",
			success: function(data){
				data = JSON.parse(data);
				var html = "";
							
				
			    	html += '<div class="col-xs-6">';	
						html +='<div class="form-group">';	    	
					    	html += '<input placeholder="URL videa (youtube, vimeo)" class="form-control" type="text" name="ytv-element-'+sectionData+'-'+secHeaderElement+'[]" />';	    		
					    html +='</div>';
					html += '</div>';
					console.log(data);
					if(data.length > 0){
						html += '<div class="col-xs-6">';	
							html +='<div class="form-group">';	    	
						    	html += '<select class="form-control" name="ytv-c-element-'+sectionData+'-'+secHeaderElement+'[]">';
						    		html += '<option value="0"> -- vyberte kategorii -- </option>';
						    		
						    		for(var i = 0;i < data.length;i++){
						    			html += '<option value="'+data[i].categoryID+'"> '+data[i].title+' </option>';
						    		}
						    		
						    	html += '</select>';
						    html +='</div>';
						html += '</div>';
					}   
			    
			    thisElm.closest("div.card").find("div.card-body").append(html);
			}

		});
	    
	    

	});
	
}


function addListElementRow(){
	
	$("a.add-list-element-row").unbind("click");
	$("a.add-list-element-row").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
							
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}

				var sectionData         = thisElm.closest("section").attr("data");				
				var secHeaderElement    = $("input[name=list-element-count-"+sectionData+"]").val();
				var listElementColCount = $("input[name=list-element-col-count-"+sectionData+"-"+secHeaderElement+"]").val();
				
				var html ='<tr>';
					for(j = 0;j <= listElementColCount; j++){
					    html +='<td><div>';
					    	for(var i = 0; i < data.length; i++){
					    		
					    		var hid = "";
					    		if(i != 0)hid = "hidden-lang";
					    		
					    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
					    		html += '<input class="form-control" placeholder="hodnota" type="text" name="li-e-row-'+sectionData+'-'+secHeaderElement+'-'+j+'-'+data[i].suffix+'[]"/>';
					    		html += '</span>';
					    	}
					    	html += flags;
					    	if(j == 0) html += '<input type="hidden" name="li-e-linkID-'+sectionData+'-'+secHeaderElement+'-0[]" />';
					    html +='</div></td>';
					}
			    html +='</tr>';
				
			    thisElm.closest("div.card").find("table tbody").append(html);
				
				setLangs();

			}

		});

	});
	
}

function addListElementCol(){
	
	$("a.add-list-element-col").unbind("click");
	$("a.add-list-element-col").click(function(e){
		e.preventDefault();
		
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
							
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}
				
				var sectionData         = thisElm.closest("section").attr("data");				
				var secHeaderElement    = $("input[name=list-element-count-"+sectionData+"]").val();
				var listElementColCountElm = $("input[name=list-element-col-count-"+sectionData+"-"+secHeaderElement+"]");
				
					listElementColCount = listElementColCountElm.val();
					listElementColCount++;
					
					listElementColCountElm.val(listElementColCount);
					
				var html ='<td>';
					
					    html +='<div>';
					    	
					    	for(var i = 0; i < data.length; i++){
					    		
					    		var hid = "";
					    		if(i != 0)hid = "hidden-lang";
					    		
					    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
					    		html += '<input class="form-control" placeholder="hodnota" type="text" name="li-e-row-'+sectionData+'-'+secHeaderElement+'-'+listElementColCount+'-'+data[i].suffix+'[]"/>';
					    		html += '</span>';
					    	}
					    	html += flags;
					    html +='</div>';
					
			    html +='</td>';
			    
			    thisElm.closest("div.card").find("table tbody tr").append(html);
			    
			    var head = '<th>'
			    	head +='<div>';
		    	
				    	for(var i = 0; i < data.length; i++){
				    		
				    		var hid = "";
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		head += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		head += '<input class="form-control" placeholder="nadpis sloupce" type="text" name="li-e-name-'+sectionData+'-'+secHeaderElement+'-'+listElementColCount+'-'+data[i].suffix+'"/>';
				    		head += '</span>';
				    	}
				    	head += flags;
				    	head +='</div>';
				    	
				    	head += '<div class="radio">';
				    		head += '<div class="radio-label"><strong>Aktivní:</strong></div>';
				    		head += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-active-'+sectionData+'-'+secHeaderElement+'-'+listElementColCount+'" id="showArticles" value="1" checked="checked"> Ano </label>';
				    		head += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-active-'+sectionData+'-'+secHeaderElement+'-'+listElementColCount+'" id="showArticles" value="0"> Ne </label>';
				    	head += '</div>';
					    	
				    	head += '<div class="radio">';
				    		head += '<div class="radio-label"><strong>Filtrovaný:</strong></div>';
				    		head += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-filtered-'+sectionData+'-'+secHeaderElement+'-'+listElementColCount+'" id="showArticles" value="1" checked="checked"> Ano </label>';
				    		head += '<label class="radio-inline"> <input class="checkbox" type="radio" name="li-e-filtered-'+sectionData+'-'+secHeaderElement+'-'+listElementColCount+'" id="showArticles" value="0"> Ne </label>';
				    	head += '</div>';
					
				    head +='</th>';
			    	
				
			    thisElm.closest("div.card").find("table thead tr").append(head);
				
				setLangs();

			}

		});

	});
	
}

function selectInputType(){
	
	$("select.input-type").change(function(){
		
		var thisElm = $(this);
		var val = thisElm.val();
		    val = val.split("-");
		
		    $.ajax({
				type: "POST",
				url: "/core/helper/get-langs",
				success: function(data){
					
					var data = JSON.parse(data);
					var flags = "";
					
					if(data.length > 1){
					    flags  = ' <span class="language-mutations">';
					    for(var i = 0; i < data.length; i++){
					    	
					    	var suffClass = data[i].suffix;
					    	if(i != 0)suffClass = data[i].suffix+"-dis";
					    	
					    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
					    }
					    flags += '</span> ';
					}
								
					var html = "";
					
					switch(val[0]){
						case "text":     html = getSelectInputTypeText(val[1]+"-"+val[2]+"-"+val[3],flags,data);break;
						case "checkbox": html = getSelectInputTypeCheckbox(val[1]+"-"+val[2]+"-"+val[3],flags,data);break;
						case "radio":    html = getSelectInputTypeRadio(val[1]+"-"+val[2]+"-"+val[3],flags,data);break;
						case "textarea": html = getSelectInputTypeTextarea(val[1]+"-"+val[2]+"-"+val[3],flags,data);break;
						case "dropdown": html = getSelectInputTypeDropdown(val[1]+"-"+val[2]+"-"+val[3],flags,data);break;
					}

					thisElm.closest("div.inputs").find("div.inputs-elements").html(html);
					
					addFormElementValues();					
					setLangs();

				}

			});

	});
	
}

function addFormElementValues(){

	$("button.add-form-element-values").unbind("click");
	$("button.add-form-element-values").click(function(){
		var param = $(this).attr("data");
		var thisElm = $(this);
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-langs",
			success: function(data){
				
				var data = JSON.parse(data);
				var flags = "";
				
				if(data.length > 1){
				    flags  = ' <span class="language-mutations">';
				    for(var i = 0; i < data.length; i++){
				    	
				    	var suffClass = data[i].suffix;
				    	if(i != 0)suffClass = data[i].suffix+"-dis";
				    	
				    	flags += ' <a class="'+suffClass+'" title="'+data[i].title+'"></a> ';
				    }
				    flags += '</span> ';
				}
							
				var html = '<div class="form-inline align-items-start">';
					html += '<div class="form-group d-block mr-2">';
						for(var i = 0; i < data.length; i++){    		
				    		var hid = "";
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<input class="form-control" placeholder="název hodnoty" type="text" name="f-e-valueName-'+param+'-'+data[i].suffix+'[]" />';
				    		html += '</span>';
						}
						html += flags;
					html += '</div> ';
					
					html += '<div class="form-group d-block mr-2">';
						for(var i = 0; i < data.length; i++){    		
				    		var hid = "";
				    		if(i != 0)hid = "hidden-lang";
				    		
				    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				    		html += '<input class="form-control" placeholder="hodnota" type="text" name="f-e-value-'+param+'-'+data[i].suffix+'[]" />';
				    		html += '</span>';
						}
						html += flags;
					html += '</div> ';
				html += '</div> ';
				
				thisElm.closest(".inputs").find(".inputs-elements .add-form-element-values").before(html);
				
				setLangs();

			}

		});
		
	});
	
}

function getSelectInputTypeText(parameters,flags,data){
	var html = "";
	
	html =  '<div class="form-inline align-items-start">';
		html += '<div class="form-group d-block">';
		for(var i = 0; i < data.length; i++){    		
    		var hid = "";
    		if(i != 0)hid = "hidden-lang";
    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
	    		html += '<input type="hidden" name="f-e-valueName-'+parameters+'-'+data[i].suffix+'[]" value="" />';
				html += '<input class="form-control" placeholder="hodnota" type="text" name="f-e-value-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags; 
		html += '</div>';
	html += '</div>';
	
	return html;	
}
function getSelectInputTypeCheckbox(parameters,flags,data){
	var html = "";
	
	html =  '<div class="form-inline align-items-start">';
		html +=  '<div class="form-group d-block mr-2">';
			for(var i = 0; i < data.length; i++){    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input class="form-control" placeholder="název hodnoty" type="text" name="f-e-valueName-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags; 
		html += '</div> ';
		
		html += '<div class="form-group d-block mr-2">';
			for(var i = 0; i < data.length; i++){    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input class="form-control" placeholder="hodnota" type="text" name="f-e-value-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags;
		html += '</div> ';
	
	html += '</div>';
    html +=  '<div class="form-group d-block">';
		html += '<button class="btn text-uppercase btn-primary btn-xs add-form-element-values" data="'+parameters+'" type="button">Přidat hodnoty</button>';
	html += '</div>';
	
	return html;	
}
function getSelectInputTypeRadio(parameters,flags,data){
	var html = "";
	
	html =  '<div class="form-inline align-items-start">';
		html += '<div class="form-group d-block mr-2">';
			for(var i = 0; i < data.length; i++){    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input class="form-control" placeholder="název hodnoty" type="text" name="f-e-valueName-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags; 
		html += '</div> ';
		
		html += '<div class="form-group d-block mr-2">';
			for(var i = 0; i < data.length; i++){    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input class="form-control" placeholder="hodnota" type="text" name="f-e-value-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags;
		html += '</div> ';
	
	html += '</div>';
    html +=  '<div class="form-group d-block">';
		html += '<button class="btn text-uppercase btn-primary btn-xs add-form-element-values" data="'+parameters+'" type="button">Přidat hodnoty</button>';
	html += '</div>';
	
	return html;	
}
function getSelectInputTypeDropdown(parameters,flags,data){
	var html = "";
	
	html =  '<div class="form-inline align-items-start">';
		html +=  '<div class="form-group d-block mr-2">';
			for(var i = 0; i < data.length; i++){    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input class="form-control" placeholder="název hodnoty" type="text" name="f-e-valueName-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags; 
		html += '</div> ';
		
		html += '<div class="form-group d-block mr-2">';
			for(var i = 0; i < data.length; i++){    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input class="form-control" placeholder="hodnota" type="text" name="f-e-value-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags;
		html += '</div> ';
	
	html += '</div>';
    html +=  '<div class="form-group d-block">';
		html += '<button class="btn text-uppercase btn-primary btn-xs add-form-element-values" data="'+parameters+'" type="button">Přidat hodnoty</button>';
	html += '</div>';
	
	return html;	
}
function getSelectInputTypeTextarea(parameters,flags,data){
	var html = "";
	
	html =  '<div class="form-inline align-items-start">';
		html +=  '<div class="form-group d-block">';
			for(var i = 0; i < data.length; i++){    		
	    		var hid = "";
	    		if(i != 0)hid = "hidden-lang";
	    		
	    		html += '<span class="language-input '+hid+' '+data[i].suffix+'" >';
				html += '<input type="hidden" name="f-e-valueName-'+parameters+'-'+data[i].suffix+'[]" value="" />';
				html += '<input class="form-control" placeholder="hodnota" type="text" name="f-e-value-'+parameters+'-'+data[i].suffix+'[]" />';
				html += '</span>';
			}
			html += flags;
		html += '</div>';
	html += '</div>';
	
	return html;	
}

function removeElement(){
	
	$(".card-header span.glyphicons-remove").click(function(){
		
		var thisEml = $(this);
		var data    = thisEml.attr("data");
		var url     = "/admin/obsah/helper/delete-header-element";
		var text    = "Opravdu chcete smazat tento element ?";
		
		if(thisEml.hasClass("section")){	
			url  = "/admin/obsah/helper/delete-section";
			text = "Opravdu chcete smazat tuto sekci ?";			
		}
		if(thisEml.hasClass("section-header")){	
			url  = "/admin/obsah/helper/delete-section-header-element";			
		}
		if(thisEml.hasClass("text-element")){	
			url  = "/admin/obsah/helper/delete-text-element";			
		}
		if(thisEml.hasClass("link-element")){	
			url  = "/admin/obsah/helper/delete-link-element";			
		}
		if(thisEml.hasClass("sublink-element")){	
			url  = "/admin/obsah/helper/delete-sublink-element";			
		}
		if(thisEml.hasClass("form-element")){	
			url  = "/admin/obsah/helper/delete-form-element";			
		}
		if(thisEml.hasClass("list-element")){	
			url  = "/admin/obsah/helper/delete-list-element";			
		}
		if(thisEml.hasClass("ytv-element")){	
			url  = "/admin/obsah/helper/delete-ytv-element";			
		}
		if(thisEml.hasClass("article-element")){	
			url  = "/admin/obsah/helper/delete-article-element";			
		}
		if(thisEml.hasClass("photo-file-element")){	
			url  = "/admin/obsah/helper/delete-photo-file-element";			
		}	
		if(thisEml.hasClass("map-element")){	
			url  = "/admin/obsah/helper/delete-map-element";			
		}
							
		$('#warning').modal();
		$('#warning').on('shown.bs.modal', function (e) {
			$(".modal .modal-body").html(text);
			$("button.do-delete").unbind("click");
			$("button.do-delete").click(function(){
								
				if(data){
					$.ajax({
						type: "POST",
						url: url,
						data:{elmID:data},
						success: function(data){console.log(data);}
					});
				}
				thisEml.closest("li").remove();
				$(".modal .modal-body").html("");
				$('#warning').modal('hide')
			});
		});
		
	});
	
	$(".inputs span.glyphicons-remove").click(function(){
		
		var thisEml = $(this);
		var data    = thisEml.attr("data");
		var url     = "/admin/obsah/helper/delete-form-element-value";
		var text    = "Opravdu chcete smazat tuto položku ?";
						
		$('#warning').modal();
		$('#warning').on('shown.bs.modal', function (e) {
			$(".modal .modal-body").html(text);
			$("button.do-delete").unbind("click");
			$("button.do-delete").click(function(){
								
				if(data){
					$.ajax({
						type: "POST",
						url: url,
						data:{elmID:data},
						success: function(data){console.log(data);}
					});
				}
				thisEml.closest("li").remove();
				$(".modal .modal-body").html("");
				$('#warning').modal('hide')
			});
		});
		
	});
	
}

function setLangs(){
	
	$('span.language-mutations a').click(function(){

 		var closestP = $(this).closest('div');
 		
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
	
}

function removeLink(){
	
	$("span.remove-link").click(function(){
 		
 		var id = $(this).attr("id");
 		id = id.split("-");
 		id = id[1];
 		 		
 		$("div.link-"+id).remove();
 		
 	});
	
}

function selectLink(){
	
	$("#selected-parent-link span.parrent-links span.text").click(function(){
 		var title = $(this).text();
 		var linkID = $(this).next().text();
 		
 		var c = $(this).closest("#selected-parent-link").attr("rel");

 		$("#selected-parent-link span.v-"+c).text(title);
 		$("#selected-parent-input-"+c).val(linkID);
 		
 	});
	
}

function initRefreshFTPListButton(){
	$('span.refresh-button-ftp-list').click(function(){
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-ftp-list",
			data: "FTPfolder="+FTPfolder,
			success: function(response){
	            $("ul.ftp-list").html(response);	
	            $('span.refresh-button-ftp-list span.refresh-notify').fadeIn(); 
	            setTimeout(function() {
	            	$('span.refresh-button-ftp-list span.refresh-notify').fadeOut(); 
				}, 3000);           
 				quickSearchInit('search-input','search-content'); 
			}

		});
		
	});
}
function getSubLink(backData,val){
    
	$("select[name=link]").change(function(){
	   
		var val = $(this).val();
		
		$.ajax({
			type: "POST",
			url: "/core/helper/get-sub-link",
			data: "link="+val+"&backData="+backData,
			success: function(html){
	            $("div.sub-links").html(html); 
	            $("div.sub-links").show();

			}

		});
		
	});

}

function getSubLinkEdit(backData,val){
   
		$.ajax({
			type: "POST",
			url: "/core/helper/get-sub-link",
			data: "link="+val+"&backData="+backData,
			success: function(html){
	            $("div.sub-links").html(html); 
	            $("div.sub-links").show();
                
			}

		});

}

function quickSearchInit(searchInput,searchContent){

	if(document.getElementById(searchInput)){
 	
 		$('input#'+searchInput).quicksearch('ul.'+searchContent+' li');
		
		$('input#'+searchInput).focus(function () {
			$('ul.'+searchContent).show();
		 	$('ul.'+searchContent+' li').show();
		});
		
 		$("body").click(function(e){
		  //you can then check what has been clicked
		   var target = $(e.target);
  
		   if(!target.is("input#"+searchInput) && !target.is('ul.'+searchContent) && !target.is('ul.'+searchContent+' li')){
			   if($('ul.'+searchContent).is(':visible')){
		 			$('ul.'+searchContent).hide();
		 			$('ul.'+searchContent+' li').hide();
		 			$('input#'+searchInput).val('');
		 	   }
	 	   }
		});
		 		
 	}
}

function saveSortedList(controler,sortlist){
    
    var rowNumber = $('#'+sortlist+' li').size();
    var sort = new Array();
    
    for(i = 0;i < rowNumber;i++){
    
        sort[i] = $('#'+sortlist+' li').eq(i).attr("id");
         
    }

    $.ajax({
		type: "POST",
		url: "/core/helper/savesortedlist",
		data: "sort="+sort+"&control="+controler,
		success: function(html){
		    
		    $('div.sortMessage').css("display","inline-block");
		    $('div.sortMessage').html("Pořadí úspěšně uloženo");
            
			}

	});    

}

function getVideos(url){

    $("div.animationVideo")
        .ajaxStart(function(){
            $(this).show();
        })
        .ajaxComplete(function(){
            $(this).hide();
        });
        
    $.ajax({
	    type: "POST",
		url: "/core/helper/get-videos",
		data: "url="+url,
		success: function(html){   

             location.reload(true); 

        }

		});     
	
	}


function lan(lang){
    
    $.ajax({
		type: "POST",
		url: "/core/helper/language",
		data: "lang="+lang,
		success: function(html){
            location.reload(true);   

		}

	});
    

}

function addPhoto(){
    
    id = $('.currentContent p.photos input:last').attr('id');
    id++;
    photo = "<label for=\"text\"> &nbsp; </label><input id=\""+id+"\" type=\"file\" name=\"photo"+id+"\" size=\"47\">";
    $('.currentContent p.photos').append(photo);
    $('.currentContent p input[name=hidden]').val(id);
    
    if(id == 5){
    
        $('#pridat_foto').css("display","none");   
    
    }

}

function addVideoURLTitle(){
    
	$("a.add-videoURL").click(function(e){
		e.preventDefault();
	    id = $('input[name=hidvideo]').val();
	    id++;
	    var subtitle = '<div class="form-group">';	    
			subtitle += '<input placeholder="URL videa (youtube, vimeo)" class="form-control" type="text" name="videoURL-'+id+'" id="videoURL-'+id+'" />';
	    subtitle += ' 	</div>';
	    $('input[name=hidvideo]').val(id);
	    $('div.video-URLs').append(subtitle);
	    
	    return false;
	});

}

function getFotogallery(folder){

    $("div.loading")
        .ajaxStart(function(){
            $(this).show();
        })
        .ajaxComplete(function(){
            $(this).hide();
        });
    

    itemek = $('div.currentContent select').val();
    if(itemek == 0){
        itemek = folder;
    }
    $.ajax({
		type: "POST",
		url: "/admin/helper/fotogallery",
		data: "item="+itemek,
		success: function(html){
              
             $("div.contentItem").html(html);   

			}

	});

}


function getForm(item,parrentlevel,captcha,name,title,text,email){
    
    html =  '<center><div class="reaction"><p class="reaction"><label for="name">Jméno:</label><input id="name" type="text" name="nameReaction" value="'+name+'" />';
    html +=    '<label class="email" for="email">Email:</label><input id="email" type="text" name="emailReaction" value="'+email+'" />';
	html += '</p>';
	            
	html += '<p class="reaction"><label for="title"></label>Re: '+title+'<input id="title" type="hidden" name="titleReaction" value="Re:'+title+'" /></p>';
	html += '<p class="reaction"><label for="name">Text:</label><textarea id="text" name="textReaction">'+text+'</textarea></p>';
	html += '<p class="reaction"><input id="hidden" type="hidden" name="hiddenReaction" value="'+parrentlevel+'" /></p>';
	html += '<p><label></label><img src="/Public/Captcha/'+captcha+'" /><input type="text" name="hiddenCaptcha" /></p>';
	html += '<p class="reaction button"><input id="button" type="submit" name="addReaction" value="Přidat" /></p></div></center>';
	
    $("div.answer"+item).html(html);
    
    if($("div.answer"+item).css("display")=="block"){
        $("div.answer"+item).slideUp("slow");
    }else{
        $("div.answer"+item).slideDown("slow");
    }


}



    function startUpload(){
    
        $('p.upload_process').css("visibility",'visible');
        return true;

    }
    
    function stopUpload(success){
   
      var result = '';
      if (success == 1){
  
	      $('p#result').html('<span class="msg">Soubor úspěšně nahrán<\/span>');
	      $();
  
      }else {
      
          $('p#result').html('<span class="emsg">Došlo k chybě ři nahrávání!<\/span>');
          
      }
      $('p.upload_process').css("visibility",'hidden');
  
      return true;
  
      }
      
     function showAddedMp3(title){
         
         $('div.addedMp3').css('display','block');
         $('div.addedMp3').html(title);
         
         $('input[name = mp3Title]').val("");
         $('input[name = mp3]').val("");
     
     }
     
     function getCustValue(element)
	 {
	    $(element).find("span").remove();
	    title = $(element).text();
	    
	    var id = $(element).attr("id");
	    id = id.split("-");
	 
	    $("input[name='user']").val(id[1]);
	    
	    /*
	    html  = '<li id="'+id+'">';
	    html += '<div class="listTitleItem cross ">'+title+'</div>';				
		html += '<div class="listItem">';
		html += '    <a onclick="deleteCustValue('+id+');return false;" title="Smaž" href="/">';
		html += '        <img alt="Smaž" src="/Public/Images/Admin/delete.gif" />';
		html += '    </a>';
		html += '</div>';
		html += '<br id="endFloat" />';
	    html += '</li>';
	    
	    oldHtml = $('div.currentContent ul#sortList').html();*/
	    $('div.list-order input#search-input').val(title);
	    //$('div.currentContent ul#sortList').append(oldHtml);
	        
	    $('div.search-content-list ul').hide();
	    $('div.cus_list').hide();
	    
	    
	    	    
	    
	 }

	function hideCust()
	{
	   $('div.cus_list ul li').hide();
	   $('div.cus_list').hide();
	}
	
	
	function deleteCustValue(id)
	 {

	    $('div.currentContent ul#sortList li#'+id).remove();

	 }		
	function ToggleLogin(){
	
	    $("#portal-select-login-wrap").toggle();
	    return false;
	
	}
	
	
	
function generatePassword(len){

    var charactors = 'abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var password = '';

    while(password.length < len){
  
        password += charactors.charAt(Math.floor(Math.random()*charactors.length));
    
    }
    
    $('input[id=password]').val(password);
    
}

function page(page,content,url){

        var comodity    = $("input[name=comodity]:checked");
        var allComodity = "";
        var client      = $("select option:selected").val();
        var public      = $("input[name=type]:checked");
        var allPublic   = "";
        
        if(comodity.length > 0){
	        allComodity = "(";
	        
	        for(i = 1;i <= comodity.length; i++){
	            if(i == comodity.length){
	                allComodity += "'"+comodity.eq(i-1).val()+"'";
	            }else{
	            	allComodity += "'"+comodity.eq(i-1).val()+"',";
	            }
	        }
	        
	        allComodity += ")";
        	 
        }
        
        if(public.length > 0){
	        
	        for(i = 1;i <= public.length; i++){
	            if(i == public.length){
	                allPublic += public.eq(i-1).val();
	            }else{
	            	allPublic += public.eq(i-1).val()+",";
	            }
	        }
        	 
        }
 
	    $.ajax({
		type: "POST",
		url: "/helper/"+content,
		data: "page="+page+"&allcomodity="+allComodity+"&client="+client+"&allpublic="+allPublic+"&url="+url,
		success: function(html){    
	         
             $("div."+content).html(html); 
             window.scrollTo(0,0); 

        }

		});     
	
	}
	
		 
	 
function contactFormToggle(){

    $("div.contactForm").slideToggle("slow",
	    
		    function () {

		            cl = $("h3.contactForm").attr("class");
                    
                                
		            if(cl == "nadpis contactForm plus"){
			        	$("h3.contactForm").removeClass("plus");
				    	$("h3.contactForm").addClass("minus");
				    }else{
				    
				        $("h3.contactForm").removeClass("minus");
				    	$("h3.contactForm").addClass("plus");
				    
				    }
				    
	});

}
function addOverlay(width){

    var scrollHeight = parseInt($(window).scrollTop());
    var newWidth     = parseInt(width) + 2;

    var documentWidth  = parseInt($(document).width());
    
    var marginLeft     = ((documentWidth - newWidth)/2);
    var marginTop      = 10;
    
    var htmlOverlay  = '<div id="overlay"> </div>';
	htmlOverlay += '<div id="overlayContent" style="top:'+scrollHeight+'px;width:'+newWidth+'px;margin-left:'+marginLeft+'px;margin-top:'+marginTop+'px;">';
	htmlOverlay += '</div>'; 
	$("body").append(htmlOverlay);
}

function putOverlayLoading(){

	var windowHeight = $(window).height();
	var loadingHeight= 40;
	var top			 = (windowHeight / 2) - (loadingHeight/2);
	$('body').append('<div style="display:none;width: 100%;height: 100%;top: 0px;left: 0px;position: fixed; z-index:999;background: url(\'/Public/Images/overlay_bg.png\');" id="overlay-loading">'+
						'<div style="text-align:center; top:'+top+'px;left:0px; width: 100%;height: '+loadingHeight+'px;position: fixed;" class="loading">'+
							'<span style="width:200px;height:'+loadingHeight+'px;text-align: center;background-color: white;padding: 11px 19px 5px 19px;border: 2px solid grey;display:inline-block;">'+
								'<span style="text-align:center;color: grey;">�ekejte pros�m...</span>'+
								'<img src="/Public/Images/loadingAnimation.gif" />'+
							'</span>'+
						'</div>'+
					'</div>');
					
	$('div#overlay-loading').fadeIn();
}
function hideOverlayLoading(){
	$('div#overlay-loading').fadeOut(300, function() { $(this).remove(); });
}
function clwi(){
		
		$("div#overlay").fadeOut(600, function() { $(this).remove(); });
        $("div#overlayContent").fadeOut(600, function() { $(this).remove(); });
        
};
function createXmlDOMObject(xmlString)
{
	var xmlDoc
	if (navigator.appName == "Microsoft Internet Explorer") {
					     
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async = false;
		if(!xmlDoc.loadXML(xmlString))xmlDoc = xmlString;
		
	} else {
		xmlDoc = xmlString;
	}
     return xmlDoc;
}

function isEmpty(value) {
	if ((value==null) || (value=='undefined') || (value.length==0)) {
		return true;
	}else{ return false; }
} 
function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);
}
function checkMaxFiveStatusesChecked(e){
	var numberOfcheckedStatuses = $(e.target).closest("form").find("input.productStatusID:checked" ).length;
	if(numberOfcheckedStatuses > 4){
		e.preventDefault();
		$.prompt('Můžete označit maximálně 4 statusy!');
		return false;
	}
	return true;
}
function checkRequiredFields(e){
	var inputPassed 		= true;
	var selectPassed 		= true;
	var textPassed 			= true;
	var allCheckboxesPassed = true;
	
	$(e.target).closest("form").find("input.required:not(:checkbox)" ).each(function( index ) {
	  if(isEmpty($(this).val())){
		  inputPassed = false;
		  $(this).addClass("has-error");
		  return false;
	  }
	});
	$(e.target).closest("form").find( "select.required" ).each(function( index ) {
		  if(isEmpty($(this).val())){
			  selectPassed = false;
			  $(this).addClass("has-error");
			  return false;
		  }
	});
	$(e.target).closest("form").find( "textarea.required" ).each(function( index ) {
		  if(isEmpty($(this).val())){
			  textPassed = false;
			  $(this).addClass("has-error");
			  return false;
		  }
	});
	$(e.target).closest("form").find( ".required-checkboxes" ).each(function( index ) {
		var checkboxesPassed = false;
		$(this).find("input").each(function( index ) {
			if($(this).is(':checked')){
				checkboxesPassed = true;
			}
		});

		if(!checkboxesPassed){
			  $(this).addClass("has-error");
			  allCheckboxesPassed = false;
			  return false;
		}

	});

	if(!inputPassed || !selectPassed || !textPassed || !allCheckboxesPassed){
		e.preventDefault();
		$.prompt('Nevyplnili jste povinné údaje označené hvězdičkou!');
		return false;
	}
	return true;
}
function checkLinkOrCategoryFilled(e){

	var linkInputPassed 		= false;
	$( "input[name='linkID[]']" ).each(function( index ) {
		if($(this).val() != 0){
			  linkInputPassed = true;
			  return false;
		}
	});
	if(!linkInputPassed){
		e.preventDefault();
		$.prompt('Nevyplnili jste povinné údaje označené hvězdičkou!');		
		$(".selected-link span.value" ).addClass("has-error");
	}
}
function checkAuctionRequiredFields(e){

	var auctionFieldsPassed 		= false;
	var date = $( "input[name='dateAuction']" ).val();
	var time = $( "input[name='timeAuction']" ).val();
	
	var regex = /([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]/;

	if(regex.test(time) || (isEmpty(date) && isEmpty(time))){	
		if(isEmpty(date) && !isEmpty(time)){
			e.preventDefault();
			$.prompt('Nevyplnili jste datum aukce!');		
			$("input[name='dateAuction']" ).addClass("has-error");
		}else if(!isEmpty(date) && isEmpty(time)){
			e.preventDefault();
			$.prompt('Nevyplnili jste čas aukce!');		
			$("input[name='timeAuction']" ).addClass("has-error");
		}
	}else{
		e.preventDefault();
		$.prompt('Zadali jste špatný formát času!<br/>Správný formát je <strong>HH:mm:ss</strong>');		
		$("input[name='timeAuction']" ).addClass("has-error");
	}
}
function checkEshopProductRequiredFields(e){
	
	var photo 					= $(e.target).closest("form").find( "input[name='photo[]']" ).val();
	var hiddenPhoto 			= $(e.target).closest("form").find( "input[name='hiddenPhoto[]']" ).val();
	var title 					= $(e.target).closest("form").find( "input[name='title-cz']" ).val();
	var productCategoryID 		= $(e.target).closest("form").find( "select[name='productCategoryID']" ).val();
	var showFirstCover 			= $(e.target).closest("form").find( "input[name='showFirstCover']:checked" ).val();
	var showSecondCover 		= $(e.target).closest("form").find( "input[name='showSecondCover']:checked" ).val();
	var cover1TitleID 			= $(e.target).closest("form").find( "select[name='cover1TitleID']" ).val();
	var cover2TitleID 			= $(e.target).closest("form").find( "select[name='cover2TitleID']" ).val();
	var cover1ID 				= $(e.target).closest("form").find( "select[name='cover1ID']" ).val();
	var cover2ID 				= $(e.target).closest("form").find( "select[name='cover2ID']" ).val();
	var cover1photoID 			= $(e.target).closest("form").find( "input[name='cover1photoID']" ).val();
	var cover2photoID 			= $(e.target).closest("form").find( "input[name='cover2photoID']" ).val();	
	var predefinedCoversType	= $(e.target).closest("form").find( "input[name='predefinedCoversType']:checked" ).val();

	console.log(predefinedCoversType);
	if(isEmpty(photo) && isEmpty(hiddenPhoto)){
		e.preventDefault();
		$.prompt('Nevyplnili fotku!');		
		$(e.target).closest("form").find("input[name='photo[]']" ).addClass("has-error");
	}else if(isEmpty(title)){
		e.preventDefault();
		$.prompt('Nevyplnili jste název produktu!');		
		$(e.target).closest("form").find("input[name='title-cz']" ).addClass("has-error");
	}else if(isEmpty(productCategoryID)){
		e.preventDefault();
		$.prompt('Nevyplnili jste kategorii produktu!');	
		$(e.target).closest("form").find("select[name='productCategoryID']" ).addClass("has-error");	
	}else if( predefinedCoversType == 1 && (!isEmpty(showFirstCover) && (isEmpty(cover1ID) || isEmpty(cover1photoID)))){
		e.preventDefault();
		$.prompt('Nevyplnili jste povinné položky prvního vzoru');		
		$(e.target).closest("form").find(".first-panel" ).addClass("has-panel-error");	
	}else if( predefinedCoversType == 1 && (isEmpty(showFirstCover) && (!isEmpty(cover1ID) || !isEmpty(cover1photoID)))){
		e.preventDefault();
		$.prompt('Pro uložení prvního vzoru jej musíte označit jako "zobrazovat"');	
		$(e.target).closest("form").find(".first-panel" ).addClass("has-panel-error");		
	}else if( predefinedCoversType == 1 && (!isEmpty(showSecondCover) && (isEmpty(cover2ID) || isEmpty(cover2photoID)))){
		e.preventDefault();
		$.prompt('Nevyplnili jste povinné položky druhého vzoru');		
		$(e.target).closest("form").find(".second-panel" ).addClass("has-panel-error");	
	}else if( predefinedCoversType == 1 && (isEmpty(showSecondCover) && (!isEmpty(cover2ID) || !isEmpty(cover2photoID)))){
		e.preventDefault();
		$.prompt('Pro uložení druhého vzoru jej musíte označit jako "zobrazovat"');	
		$(e.target).closest("form").find(".second-panel" ).addClass("has-panel-error");		
	}
}

