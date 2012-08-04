/* 
 * Portal methods for synergy portal
 * @author Marcin Sołtysiuk
 * @namespace $  
*/

$.Tree = { 

	/* initialize portal 
	 * @method init
	*/
	init : function () {
	
		$(document.body).click (function (e){
			var node = e.target;  
			switch (node.tagName) {
				case 'A':
				
				// edit mode open property editor in new window TODO
				if (~node.className.indexOf ('collapsed')) {
					$(node).parent().removeClass('collapsed').find ('ul:first').remove();
				} else if (~node.className.indexOf ('collapse')) {
					$.Tree.json ($(node).next().attr('href'), function (data) {
						$(node).parent().addClass ('collapsed').append ($.Tree.render (data));	
					});  
					return false; 
				}
				break;
			}
		});	
		
		$.Tree.json (changeMode (location.href, 'json'), function (data) {
			$.Tree.get (data, function (html) {
				$('#leftpane').html ($.Tree.render (html));	
			});
			
			$('#middlepane').html ($.Tree.render (data));
		});
			
	},
	json : function (url, success) {
		$.send ({
			url : url,
			dataType : 'json',
			success : success
		});
	},
	
	render : function (data) {
		var item, $li, $ul = $('<ul/>');
	
		for (node in data.nodes) {
			item = data.nodes[node];
			$li = $('<li id="'+node+'"><a href="'+item.link+'" class="title">'+item.title+'('+item.mime+')</a></li>');
			if (item.type == "100") $li.prepend ('<a href="#" class="collapse">+</a>');
			if (item.level) $li.append ($.Tree.render (item.level));
			$ul.append ($li);
		}
		
		return $ul;
	},
	
	get : function (data, callback) {
		if (data.parentUrl != '') {
			$.Tree.json (data.parentUrl, function (html) {
				//console.log (html.nodes, data.parentId);
				html.nodes[data.id].level = data;
				if (html.parentUrl == '') {
					callback (html);
					return false;
				}
				$.Tree.get (html, callback);
			});
		}
	}
	
};

$(function(){ 
	$.Tree.init();
});	