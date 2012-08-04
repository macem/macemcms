/**
 * attach wysiwyg/m editor for selected textarea
 * @package macem page
*/

$.editor = {
	config : {
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "black",
		language : $('html').attr('lang') || 'pl',
      
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		theme_advanced_buttons1 : 'bold,italic,strikethrough,sub,sup,removeformat,|,bullist,numlist,justifyleft,justifycenter,justifyright,justifyfull,|,blockquote,cite,acronym,hr,link,unlink,image,charmap',
		theme_advanced_buttons2 : 'styleselect,formatselect,|,copy,paste,undo,redo,|,table,delete_row,row_before,row_after,col_after,merge_cells,|,template,code',
		theme_advanced_buttons3 : '', //"cite,ins,del,abbr,acronym,template",
		
		//theme_advanced_styles : 'Green text=top;Section info=section-info;Section more=section-more',
		
		style_formats : [
			{title : 'Nagłówek(kolor)', selector : 'h2,h3,h4,h5,h6', classes : 'top'},
			{title : 'Sekcja(zółta)', selector : 'p,div,img,h2,h3,h4', classes : 'section-info'},
			{title : 'Sekcja(niebieska)', selector : 'p,div,img,h2,h3,h4', classes : 'section-more'},
			{title : 'Text(duży)', selector : 'p,h1,h2,a', classes : 'banner'},
			{title : 'Text(opis)', selector : 'p', classes : 'desc'},
			{title : 'Link(email)', selector : 'a', classes : 'email'},
			{title : 'Link(plik)', selector : 'a', classes : 'file'},
			{title : 'Link(more)', selector : 'a', classes : 'more'},
			{title : 'Link(wykres)', selector : 'a', classes : 'graph'},
			{title : 'Link(web)', selector : 'a', classes : 'web'},
			{title : 'Link(rss)', selector : 'a', classes : 'rss'},
			{title : 'Link(zoom)', selector : 'a', classes : 'zoom'},
			{title : 'Link(link)', selector : 'a', classes : 'link'},
			{title : 'Link(googlemap)', selector : 'a', classes : 'googlemap'}
		],
		
		formats : {
			alignleft : {selector : 'p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
			aligncenter : {selector : 'p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
			alignright : {selector : 'p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
			alignfull : {selector : 'p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'}
		},
		
		content_css : $('meta[name=wysiwyg_css_path]').attr('content'),

		entity_encoding : 'raw',
		
		theme_advanced_blockformats : 'p,div,h2,h3,h4,h5,h6',
		
		width: '100%',
		
		relative_urls     : false,
		keep_styles       : false,
		fix_list_elements : true,
		
		image_browser_url: $('meta[name=wysiwyg_img_path]').attr('content'),
		
		plugins : 'lists,style,table,advimage,advlink,contextmenu,xhtmlxtras,template,inlinepopups,table',
		
		mode : 'exact',
		//editor_selector : selector,
		template_templates : [
                {
                        title : "Information box",
                        src : "div-info.htm",
                        description : "Display information box with background color"
                }
            ]     
	},
	load   : function (container, selector) {
		var arr = [];
		
		$(selector, container).each (function() {
			if (this.id) arr.push (this.id);
			$.editor.config.height = this.rows*18;
		});
		
		 
		$.editor.config.elements = arr.join (',');
		
		if ($.editor.config.elements && window.tinymce) {
			tinyMCE.init ($.editor.config);
		}
		
	},
	update : function () {
		if (window.tinymce) {
			tinyMCE.triggerSave();
       }
	},
	save : function () {
		
	},
	remove : function (container, selector) {

		$(selector, container).each (function() {
			if (this.id && window.tinymce) {
			    tinyMCE.execCommand ('mceFocus', false, this.id);                    
			    tinyMCE.execCommand ('mceRemoveControl', false, this.id);			
			}
		});
	
	}
};

/*$(function(){
	$.editor.load (document.body, 'textarea.editable');
});*/
