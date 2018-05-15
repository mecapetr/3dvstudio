tinymce.PluginManager.add('preventdelete', function(ed, link) {

    var lastContainer = [];
    //List of bootstrap elements not to delete
    var bootstrapElements = ["col-xs-12"];

    ed.on('keydown', function(evt) {

        var node            = ed.selection.getNode();
        var range           = ed.selection.getRng();
        var startOffset     = range.startOffset;
        var currentWrapper  = range.endContainer.parentElement.parentElement.className;
                
          // if delete Keys pressed
          if (evt.keyCode == 8 || evt.keyCode == 46){
        	 
        	  currentWrapper = currentWrapper.split(" ");
        	          	  
        	  for(w in currentWrapper){
        		  
	             if ((startOffset == "0" || startOffset == "1") && bootstrapElements.indexOf(currentWrapper[w]) > -1 ){
	            	
	            	if(range.endContainer.parentElement.innerHTML.replace(/<(?:.|\n)*?>/gm, '').length == 1){
	            		range.endContainer.parentElement.innerHTML = "&nbsp;";
	            		evt.preventDefault();
	 	                evt.stopPropagation();
	 	                return false;
	            	}
	            	 
	               
	             }
        	  }


          }

        lastContainer = currentWrapper;


    });



});