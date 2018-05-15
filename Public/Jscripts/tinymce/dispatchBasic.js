var oldText;
var configTinyMCE = [
	{
		editor_selector : "basic",
		language: 'cs_CZ',
		relative_urls : true,
		convert_urls: false,
		plugins: [
		    'advlist autolink lists link image charmap print preview anchor preventdelete',
		    'searchreplace visualblocks code fullscreen',
		    'insertdatetime media table template contextmenu paste responsivefilemanager code textcolor colorpicker'
		],
		menubar: 'file edit insert view format tools',
		toolbar: 'insertfile undo redo | bold italic | forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image template',
		image_advtab: true ,
		external_filemanager_path:"/Public/filemanager/",
		filemanager_title:"Filemanager" ,
		external_plugins: { 
			"filemanager" : "/Public/filemanager/plugin.min.js",
			"preventdelete": "/Public/Jscripts/tinymce/preventdelete.js?2",
		},
		templates : [
		         	{"title":"Tabulka", "url":"/Public/Jscripts/tinymce/template/table.html?1"},
		         	{"title":"Šedý podklad", "url":"/Public/Jscripts/tinymce/template/gray_line.html"},
		        	{"title":"2 sloupce (1/2, 1/2)", "url":"/Public/Jscripts/tinymce/template/col-6.html?1"},
		            {"title":"2 sloupce (1/3, 2/3)", "url":"/Public/Jscripts/tinymce/template/col-6-2.html?1"},
		            {"title":"2 sloupce (2/3, 1/3)", "url":"/Public/Jscripts/tinymce/template/col-6-3.html?1"},
		            {"title":"2 sloupce (1/4, 3/4)", "url":"/Public/Jscripts/tinymce/template/col-6-4.html?1"},
		            {"title":"2 sloupce (3/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-6-5.html?1"},
		            {"title":"3 sloupce  (1/3, 1/3, 1/3)", "url":"/Public/Jscripts/tinymce/template/col-4.html?1"},
		            {"title":"3 sloupce  (1/4, 1/4, 1/2)", "url":"/Public/Jscripts/tinymce/template/col-3-2.html?1"},
		            {"title":"3 sloupce  (1/2, 1/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3-3.html?1"},
		            {"title":"3 sloupce  (1/4, 1/2, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3-4.html?1"},
		            {"title":"4 sloupce  (1/4, 1/4, 1/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3.html?1"}
		            ],
		content_css : ['/Public/Css/Bootstrap/css/bootstrap.min.css?6','/Public/Css/Responsive/main.css?6']
	},
	{
		
		editor_selector : "tiny",
		language: 'cs_CZ',
		plugins: [
		    'advlist autolink lists link image charmap print preview anchor',
		    'searchreplace visualblocks code fullscreen',
		    'insertdatetime media table contextmenu paste code textcolor colorpicker'
		],
		menubar: '',
		toolbar: 'bold italic underline | alignleft aligncenter alignright | link unlink | forecolor code removeformat',
		templates : [
		         	{"title":"Tabulka", "url":"/Public/Jscripts/tinymce/template/table.html?1"},
		         	{"title":"Šedý podklad", "url":"/Public/Jscripts/tinymce/template/gray_line.html"},
		        	{"title":"2 sloupce (1/2, 1/2)", "url":"/Public/Jscripts/tinymce/template/col-6.html?1"},
		            {"title":"2 sloupce (1/3, 2/3)", "url":"/Public/Jscripts/tinymce/template/col-6-2.html?1"},
		            {"title":"2 sloupce (2/3, 1/3)", "url":"/Public/Jscripts/tinymce/template/col-6-3.html?1"},
		            {"title":"2 sloupce (1/4, 3/4)", "url":"/Public/Jscripts/tinymce/template/col-6-4.html?1"},
		            {"title":"2 sloupce (3/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-6-5.html?1"},
		            {"title":"3 sloupce  (1/3, 1/3, 1/3)", "url":"/Public/Jscripts/tinymce/template/col-4.html?1"},
		            {"title":"3 sloupce  (1/4, 1/4, 1/2)", "url":"/Public/Jscripts/tinymce/template/col-3-2.html?1"},
		            {"title":"3 sloupce  (1/2, 1/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3-3.html?1"},
		            {"title":"3 sloupce  (1/4, 1/2, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3-4.html?1"},
		            {"title":"4 sloupce  (1/4, 1/4, 1/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3.html?1"}
		            ],
		content_css : ['/Public/Css/Bootstrap/css/bootstrap.min.css?6','/Public/Css/Responsive/main.css?6']
		
	},
	{
		
		editor_selector : "tiny-product",
		language: 'cs_CZ',
		plugins: [
		    'advlist autolink lists link image charmap print preview anchor',
		    'searchreplace visualblocks code fullscreen',
		    'insertdatetime media table contextmenu paste code'
		],
		menubar: '',
		toolbar: 'bullist numlist outdent indent | undo redo | bold italic underline',
		templates : [
		         	{"title":"Tabulka", "url":"/Public/Jscripts/tinymce/template/table.html?1"},
		         	{"title":"Červená čára", "url":"/Public/Jscripts/tinymce/template/red_line.html"},
		         	{"title":"Šedý podklad", "url":"/Public/Jscripts/tinymce/template/gray_line.html"},
		        	{"title":"2 sloupce (1/2, 1/2)", "url":"/Public/Jscripts/tinymce/template/col-6.html?1"},
		            {"title":"2 sloupce (1/3, 2/3)", "url":"/Public/Jscripts/tinymce/template/col-6-2.html?1"},
		            {"title":"2 sloupce (2/3, 1/3)", "url":"/Public/Jscripts/tinymce/template/col-6-3.html?1"},
		            {"title":"2 sloupce (1/4, 3/4)", "url":"/Public/Jscripts/tinymce/template/col-6-4.html?1"},
		            {"title":"2 sloupce (3/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-6-5.html?1"},
		            {"title":"3 sloupce  (1/3, 1/3, 1/3)", "url":"/Public/Jscripts/tinymce/template/col-4.html?1"},
		            {"title":"3 sloupce  (1/4, 1/4, 1/2)", "url":"/Public/Jscripts/tinymce/template/col-3-2.html?1"},
		            {"title":"3 sloupce  (1/2, 1/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3-3.html?1"},
		            {"title":"3 sloupce  (1/4, 1/2, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3-4.html?1"},
		            {"title":"4 sloupce  (1/4, 1/4, 1/4, 1/4)", "url":"/Public/Jscripts/tinymce/template/col-3.html?1"}
		            ],
		content_css : ['/Public/Css/Bootstrap/css/bootstrap.min.css?6','/Public/Css/Responsive/main.css?6']
		
	}
];

function execTinyMCE(settingid,elmID) {

	setTimeout(function(){
		var settings = configTinyMCE[settingid];
		settings.selector = '#'+elmID;
		tinymce.init(settings);
	},500);
	
}
function removeTinyMCEControll(elmID) {
	tinymce.execCommand('mceRemoveEditor', true, elmID);
}


function strip_tags($text){
    return $text.replace(/(<([^>]+)>)/ig,'');
} 
