$(function() {
	
        // property
        var $variables = $('a.property');
        
        $variables.hover (
	 function() {
		 $(this).parent().addClass ('hover');
	 },
	 function() {
	       $(this).parent().removeClass ('hover');
	 }
        )
        .parent().addClass ('property-parent');
        
        
        var $moves = $('div.content-top, div.content, aside.sidebar, div.content-bottom');
        
        $moves.dragsort({
	      itemSelector : 'article',
	      dragSelector : 'a.move',
	      dragBetween  : true,
	      dragEnd      : function () {
		    var $parent = $(this).parent(),
			  $articles = $('article', $parent),
			  map = '',
			  url = $('meta[name="host"]').attr('content') + 'module.saveorder';
			   
			  $articles.each (function(i) {
				map += '&' + $parent.attr('data-box') + '#' + ($articles.length-i) + '=' + this.id.replace('art-', '');
			  });
			  
			  $.ajax ({
				url  : url,
				type : 'POST',
				cache: false,
				data : map,
				success : function () {
				        
				}
			  })
	      },
	      placeHolderTemplate: '<article class="placeholder">drop here</article>'
        });
        
		        
        /*$variables.live ('click', function() {
	        var _self = this, $window = $('<div/>').attr({'class':'window loader'}).css({display:'none',top:Math.max(80, this.offsetTop)+'px'});
	        
	        $window.append ('<h1>' + $(this).attr('title') + '</h1><a href="#close" class="close" title="zamknij">[OK]</a>')
	        .appendTo (document.body).fadeIn (600);
	        
	        $.get (this.href, function (html) {
		        $window.append (html).removeClass('loader');
		        
		        if ($.editor) $.editor.load ($window, 'textarea.editable');
	        });
	        return false;
        });*/
        
        //$('.drag').dragout();
        
        // events
        
        $(document.body).click (function(e) {
	      var self = e.target, classes;
	      
	      if (self.tagName == 'A') {
		    classes = ' ' + self.className + ' ';
		    
		    // delete article/data
		    if (~classes.indexOf (' delete ')) {
			  var $header = $(self).parents('header'), $parent = $(self).parent();
			  
			  if ($header.length) {
				$parent = $header.parents('article');
			  }
			  else if ($(self).parent().hasClass ('toolbar')) {
				$parent = $(self).parent().parent();
			  }
			  
			  // add custom class
			  $parent.addClass ('deleted');
			  
			  var $window = $.CMS.window.show (self, {
				close: function ($win) {
				        $parent.removeClass ('deleted');
			  }});
	   
			  $.get ($.CMS.ajax.url (self.href), function (html) {
				$.CMS.window.set ($window, html);
				$.CMS.ajax.parse ($window);
			  });
			  return false;			
		    
		    }
		    // edit/add article/data
		    else if (~classes.indexOf (' edit ') || ~classes.indexOf (' event-help ') || ~classes.indexOf (' password ')
		      || ~classes.indexOf (' add ') || ~classes.indexOf (' property ')) {
			  var $window, $header = $(self).parents('header'), $parent = $(self).parent();
		       
			  if ($header.length) {
				$parent = $header.parents('article');
			  }
			  else if ($(self).parent().hasClass ('toolbar')) {
				$parent = $(self).parent().parent();
			  }
			  
			  // set window width
			  $window = $.CMS.window.show (self, {width: $parent.width() + 50});
        
			  $.get ($.CMS.ajax.url (self.href), function (html) {
				$.CMS.window.set ($window, html);
				$.CMS.ajax.parse ($window);
			  });
			  return false;
		    }
		    //
		    else if (~classes.indexOf (' icon-compact ')) {
			  $(document.body).toggleClass ('compact');
			  return false;  
		    }
        
	      }
     
        });	

});