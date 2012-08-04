// For discussion and comments, see: http://remysharp.com/2009/01/07/html5-enabling-script/
(function(){if(!/*@cc_on!@*/0)return;var e = "abbr,article,aside,audio,bb,canvas,datagrid,datalist,details,dialog,eventsource,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video".split(',');for(var i=0;i<e.length;i++){document.createElement(e[i])}})()

document.body.className += ' js';

var textNode = function (node, which) {
	  if (!node) return false;
	  var e=node.childNodes, l=e.length, i=0, index=0;
	  for (;i<l; i++) {
		if (e[i].nodeType == 3) {
			//console.log (e[i],++index,which);
			if (++index==which) return e[i];
		}	
	  }
};

// get textNode with index=which ----- ERRRRROR
/*Object.prototype.textNode = function (which) { 
	var e=this.childNodes, l=e.length, i=0, index=0;
	for (;i<l; i++) {
		if (e[i].nodeType == 3) {
			//console.log (e[i],++index,which);
			if (++index==which) return e[i];
		}	
	}
	return false;
}*/
Array.prototype.finds = function (str) {
	for (var i=0, array=[], len=this.length; i<len; i++) {
		if (~this[i].indexOf (str)) {
			array.push (this[i]);
		}	
	}
	return array;
}

$.CMS = {
	debug : function (log) {
		if (console) console.log (log);	
	},
          util : {
                    methodByClass : function (methods, className, node) {
                              var method = node.className.split (' ').finds (className), array = [];
                              if (method) {
                                        for (var i=0, len = method.length; i<len; i++) {
                                                  array.push (methods[method[i].replace (className, '')]);
                                        }
                              }
                              return array;
                    },
                    selectorByClass : function (className, node) {
                              var method = node.className.split (' ').finds (className), array = [];
                              if (method) {
                                        for (var i=0, len = method.length; i<len; i++) {
                                                  array.push (method[i].replace (className, ''));
                                        }
                              }
                              return array;
                    }                     
          },
	html : {
		// node [form, link]
		replace : function (node, html, callback) {
			var classes = node.className.split (' ').finds ('ajax-sel'), i=0, len = classes.length,
                                  method = node.className.split (' ').finds ('ajax-method-');
			
			//console.log (classes);

			for (; i<len; i++) {
                                        var selector  = classes[i].replace ('ajax-sel', ''),
                                            $replaced = $(selector);
				
                                        //console.log (selector);
                                        //console.log (method);
                              
                                        if (method.length) {
                                                  $.CMS.ajax.method[method.replace ('ajax-method-', '')]( $('<div/>').html(html).find (selector), $replaced, node );
                                        }
                                        else if (~node.className.indexOf ('ajax-parent')) { //TODO
					$replaced.parent().html ( $('<div/>').html(html).find (selector).parent().html() );					
                                        }
                                        else if (~node.className.indexOf ('ajax-html')) { //TODO
                                                  $replaced.html ( $('<div/>').html(html).find (selector).html() );			
                                        }
                                        else {
                                                  $replaced.replaceWith ( $('<div/>').html(html).find (selector) );					
                                        }
				
                                        if (callback) {
					callback ($(selector));
                                        }
			}
		},
		
		// cover page
		cover : function () {
			var $cover = $('> .cover', document.body);
			
			if ($cover.length) {
				$cover.remove();
			} else {
				$cover = $('<div class="cover"/>');
				$(document.body).append ($cover);
			}
		},
		
		// notofication status
		status : function (html, o) {
			var $status = $('#container > .info'),
			    close = '<a href="#close" title="zamknij">[OK]</a>';
			
			if ($status.length && o.hide) {
				$status.find ('a.hide').trigger ('click');
			} else if (!o || !o.hide) {
				$status = $('<p class="info '+(o&&o.classes||'')+'"/>');
				$('#container').prepend ($status.html (html + (o.close==false?'':close)));
			}				
		}
	},
	
	window : {
		show : function (self, options) {
			
                              //console.log ($(self).offset().top);
                              
                              var $window = $('<div/>')
                              .attr ({'class':'window loader'})
                              .css ({display:'none', top: (document.documentElement.scrollTop + 70) + 'px'});
                              
                              if (options && options.width) {
                                        var width = Math.max (400, options.width);
                                        $window.css ({'width': width, 'margin-left': -(width/2)});          
                              }
			
			$.CMS.html.cover();
                                             
                              $window[0].options = options;
			
			$window.append ('<h1 draggable="true" title="chwyć i przesuń">' + (self.title || self.innerHTML || 'Window') + '</h1><a href="#close" class="close" title="zamknij">[OK]</a>')
			.appendTo (document.body).fadeIn (600);	
                              
                              // close 
                              $window.bind ('keyup', function (e) {
                                        if (e.keyCode == 27) {
                                                  $(this).remove();
                                                  $.CMS.html.cover();
                                        }
                              });
                              
			/*$window.bind ('mousedown', function (e) {
				//console.log (e.target.tagName);
				//e.originalEvent.what = e.target.tagName;
				if (!e.target.tagName.match(/(H1|A|LABEL|INPUT)/)) return false; 
			});*/
			
			$window.find('h1').bind ('dragstart', function (e) {
				var self = e.originalEvent.target, window = self.parentNode;
				//e.originalEvent.dataTransfer.effectAllowed = 'move';
				//e.dataTransfer.dropEffect = 'move';
				//console.log ('start',this,e.originalEvent.target, e.originalEvent.screenX); 
				e.originalEvent.dataTransfer.setData ('myX', e.originalEvent.screenX - window.offsetLeft);
				e.originalEvent.dataTransfer.setData ('myY', e.originalEvent.screenY - window.offsetTop);
				//e.originalEvent.dataTransfer.setData('x', e.originalEvent.screenX-window.offsetLeft); 
				//e.originalEvent.dataTransfer.setData('y', e.originalEvent.screenY-window.offsetTop);
				
				//e.originalEvent.dataTransfer.setData ('test', 'test');// FF not working with this
				//console.log (e.originalEvent.screenX, window.offsetLeft, e.originalEvent.dataTransfer.myX);
			    //if (e.originalEvent.target instanceof HTMLH1Element) {
			      // use the element's data-value="" attribute as the value to be moving:
			      //e.originalEvent.dataTransfer.setData(internalDNDType, event.target.dataset.value);
				  //console.log ('start', e.originalEvent.target.tagName);
                                        e.originalEvent.dataTransfer.effectAllowed = 'move'; // only allow moves
			    //} else {
			    //  e.originalEvent.preventDefault(); // don't allow selection to be dragged
			    //}				
			});
			$window.find('h1').bind ('drag', function (e) {
				//console.log ('drag', e.originalEvent.target.tagName);
				var self = e.originalEvent.target, window = self.parentNode;
				
				//window.style.left = e.originalEvent.screenX - e.originalEvent.dataTransfer.myX + 'px';
				//window.style.top = e.originalEvent.screenY + e.originalEvent.dataTransfer.getData('y') + 'px';			
			});
			
			$window.find('h1').bind ('dragend', function (e) {
				//console.log ('drag', e.originalEvent.target.tagName);
				var self = e.originalEvent.target, window = self.parentNode;
				
				//console.log (e.originalEvent.dataTransfer.myX, e.originalEvent.screenX);
				window.style.left = e.originalEvent.screenX - e.originalEvent.dataTransfer.getData('myX') + (window.offsetWidth/2)+ 'px';
				window.style.top = e.originalEvent.screenY - e.originalEvent.dataTransfer.getData('myY') + 'px';			
			});			
			
			return $window;		
		},
		set : function ($window, html) {
			$window.append (html).removeClass('loader')
                              .find ('input:not(:hidden)').eq(0).trigger('focus');	
		},
		close : function (self) {
			var $window = $(self).parents('.window');
			
			$window.fadeOut (600, function() { 
				if ($.editor) {
                                                  $.editor.remove ($window, 'textarea.editable');
                                        }
				$(this).remove();
				$.CMS.html.cover();
                                                            
                                        if ($window[0].options && $window[0].options.close) {
                                                  $window[0].options.close ($window);
                                        }
			});			
		}	
	},
	
	ajax : {
		methods : {
                               googlemap : {
                                        before : function (selectors, form) {

                                                  // new data
                                                  var map = $(selectors[0]).get(0).node;
                                                  var pos = map.getCenter();
                                                  var lang = $('html').attr('lang');
                                                  
                                                  $(form).find('input[name="field_' + lang + '__hidden:x_new"]').val (pos.$a);
                                                  $(form).find('input[name="field_' + lang + '__hidden:y_new"]').val (pos.ab);
                                        },
                                        after : function (selectors, form, data) {
                                                  //console.log (selectors, node);
                                                  var $node = $(selectors[0]);
                                                  var map = $(selectors[0]).get(0).node;
                                                  
                                                  var $new = $(selectors[0], data);
                                                  
                                                  var $last = $new.find ('.googlemap > div:last');
                                                  
                                                  console.log (points ($last[0]));
                                                  marker (map, points ($last[0]));
                                                  
                                                  
                                                  //console.log (data);
                                        }
                               },
                               tabs : {
                                         before : function (selectors, form) {

                                                  // new data
                                                  var $article = $(selectors[0]);
                                                  
                                                  //$(form).find('input[name="field__hidden:x_new"]').val (pos.$a);
                                                  //$(form).find('input[name="field__hidden:y_new"]').val (pos.ab);
                                        },
                                        after : function (selectors, form, data) {
                                                  //console.log (selectors, node);
                                                  var $article = $(selectors[0]);
                                                  
                                                  var $new = $(selectors[0], data);
                                                  
                                                  $article.html ($new.html());

                                                  //console.log (data);
                                        }                                       
                               }
                    },
                    
                    error : function (html) {
			if (html.match (/class="info error"/)) {
				return {error: $('p.info', $('<div/>').html(html)).html()};	
			} else if (html.match (/class="info"/)) {
				return {success: $('p.info', $('<div/>').html(html)).html()};	
			}
			return false;	
		},
		parse : function ($window) {
			if ($.editor) { $.editor.load ($window, 'textarea.editable'); }
			
			$('form.ajax-replace', $window).submit (function (e) {
				var form = e.target, $redirect;
				
				// redirect should be full page because of view
				$redirect = $('input[name=redirect]', form);
				$redirect.val ($redirect.val().replace ('view-ajax/',''));
				
				$(form).addClass ('loader');
                
                                        if ($.editor) { $.editor.update(); }
                                        
                                        var selectors = $.CMS.util.selectorByClass ('ajax-sel', form);
                                        
                                        var methods = $.CMS.util.methodByClass ($.CMS.ajax.methods, 'ajax-method-', form);

                                        methods.every (function (element, index, array) {
                                                  element.before (selectors, form);          
                                        });
							
				//console.log (classes);
				$.ajax ({
					url    : form.action,
					data   : $(form).serialize(),
					cache  : false,
					type   : 'POST',
					success: function (html) {
						html = html.replace(/<\!--(.*?)-->|<\/?script.*?>|<link.*?\/>/gim,'');
						
						//$.CMS.debug (html);
						
						$(form).removeClass ('loader');
						
						var info = $.CMS.ajax.error (html);
						
						$.CMS.debug (info);
						
						if (info.error) {
							$.CMS.html.status (info.error, {classes:'error', close:false});
							$window.removeClass ('loader');// TODO automatic ?
							return;
							
						} else if (info.success) {
							$.CMS.html.status (info.success, {close:false});		
						}
						
                                                            if (methods) {
                                                                      methods.every (function (element, index, array) {
                                                                                element.after (selectors, form, html);          
                                                                      });
                                                            } else {
                                                                      $.CMS.html.replace (form, html, function ($replaced) {
                                                                                console.log ($replaced);
                                                                                $.CMS.ajax.parse ($replaced.parent());
                                                                      });
                                                            }
						$.CMS.window.close (form);
					} 
				});
				return false;
			});
			
			if ($('form.ajax-upload', $window).length) {
				var uploader = '<div id="upload-box" href="'+$('input[name=check-exists]', $window).val()+'">Drop your files here from system.</div><ul id="upload-list"/>';
				$('form.ajax-upload', $window).hide().before (uploader);
				
				$('#upload-box', $window).dnduploader ({
					action : $.CMS.ajax.url ($('form.ajax-upload', $window).attr ('action')),
					name   : 'photo',
					params : {
						redirect: $.CMS.ajax.url ($('input[name=redirect]', $window).val()),
						path    : $('input[name=path]', $window).val(),
						token	: $('input[name=token]', $window).val()
						// thumbnail
					},
					onDisplay : function (file) {
						var li = '<li id="'+file.id+'"><strong>'+file.name+'</strong><span title="kilobytes">'+parseInt(file.size/1024)+'K</span><em>pending</em><a href="#" class="action">cancel</a></li>';
						$('#upload-list').append (li);		
					},
					onExists : function (file) {
						$('#'+file.id)
						.addClass ('warning').attr('title','Do you want to replace existing file?').find('em').text ('file exists').end().find('a').text('replace file ?');	
					},				
					onServerLoadStart : function (e, file) {
						$('#'+file.id)
						.removeClass ('warning').removeClass('aborted').addClass ('load').attr('title','uploading...').find('em').text ('1%').end().find('a').text('cancel');	
					},
					onServerProgress : function (e, file) {
						var complete = Math.round(e.loaded / e.total * 100);
						$('#'+file.id+' em').text (complete+'%');	
					},
					onServerReadyStateChange : function (e, file) {
						$('#'+file.id)
						.removeClass('load').attr('title','uploaded').find('em').text ('complete').end().removeAttr('id').find('a').text('remove');	
					},
					onServerError : function (e, file) {
						$('#'+file.id)
						.removeClass('load').addClass('aborted').attr('title', file.xhr.response.replace(/<\/?.*?>/gim,'')).find('em').text ('error').end().find('a').text('upload again');	
					},
					onServerAbort : function (e, file) {
						$('#'+file.id)
						.removeClass('load').addClass('aborted').attr('title','aborted by user').find('em').text ('aborted').end().find('a').text('upload again');	
					},
				});
				
				$('#upload-list').click (function(e) {
					var self = e.target, classes = ' ' + self.className+ ' ';
					if (~classes.indexOf (' action ')) {
						var $box = $(this).prev('#upload-box');
						
						var file = $.fn.dnduploader.find ($box[0].options.files, self.parentNode.id);
						
						if (file.status == 4 || file.status == 0) {
							//console.log ('click-abort');
							$.fn.dnduploader.abort ($box,file);
							
						} else if (file.status == 7) {
							// we should check queue
							$.fn.dnduploader.upload ($box, file);
						} else if (file.status == 5) {
							$.fn.dnduploader.abort ($box,file);		
						} else if (file.status == 1) {
							//console.log ('replace');
							$.fn.dnduploader.upload ($box, file, {overwrite: true});	
						} else {
							$(self.parentNode).remove();
						}
	
						return false;	
					}
				});				
			}
			
			/*$('form.ajax-upload', $window).ajaxFrame ({
				init    : function (form) {
					$(form).addClass ('loader');	
				},
				success : function (html, form) {
					html = html.replace(/<\!--(.*?)-->|<\/?script.*?>|<link.*?\/>/gim,'');
					
					$.CMS.debug (html);
					
					$(form).removeClass ('loader');
					
					var info = $.CMS.ajax.error (html);
					
					$.CMS.debug (info);
					
					if (info.error) {
						$.CMS.html.status (info.error, {classes:'error', close:false});
						$window.removeClass ('loader');
						return;
					} else if (info.success) {
						$.CMS.html.status (info.success, {close:false});		
					}						
					
					$.CMS.html.replace (form, html, function ($replaced) {
						$.CMS.ajax.parse ($replaced);
					});
					$.CMS.window.close (form);
				} 
			});	*/				
		},
		url : function (href) {
			// host+ view + lang + action.name
			//return href.replace(/^(http.?:\/\/.*?\/)(\w{2,3}\/|)(.*?)/, '$1$2view-ajax/$3'); //server
			return href.replace(/^(http.?:\/\/.*?\/)(.*?\/)(\w{2,3}\/|)(.*?)/, '$1$2$3view-ajax/$4');	
		}
	} 
};

$(function() {

	// top link
	$(document).scroll (function() {
		var $top = $('a.top');
		if (!$top.length && document.documentElement.scrollTop > document.documentElement.offsetHeight) {
			$('<a href="#header" class="top" title="idź na początek">wróć</a>').appendTo ('#container');
		} else if (document.documentElement.scrollTop <= document.documentElement.offsetHeight) {
			$top.remove();
		}
	});	
	
	$(window).keydown (function(e) {
		//console.log (e.keyCode,e.shiftKey);
		if (e.shiftKey && e.keyCode === 191) { // '?' key show CMS toolbar
			$('html').toggleClass ('edit');	
		}	
	});	
	
	// animation
	//var data = $('div.section-about h2:first').text().split(',');
	//$('div.section-about h2').airport (data);

	// preview full photo
	if ($.fn.lightBox) $('#gallery div a.open').lightBox({
		imageLoading : '/js/plugins/images/lightbox-ico-loading.gif',
		imageBtnClose: '/js/plugins/images/lightbox-btn-close.gif',
		imageBtnPrev : '/js/plugins/images/lightbox-btn-prev.gif',
		imageBtnNext : '/js/plugins/images/lightbox-btn-next.gif'
	});
	
	// animation
	
	var $header = $('#header'), $h2 = $header.find ('h2.anim'), slogan = textNode ($('.slogan', $header)[0], 1),
	    images  = ['header0.jpg', 'header1.jpg', 'header2.jpg'],	
	    slogans = (slogan ? slogan.data.split (',') : null),	
	    url     = '/ekopol-new/skin/ekopol/img/' + images[0]; //$header.css ('background-image').replace(/url\(['"](.*?)['"]\)/,'$1');

	if (slogans && $h2.length) {
		
		$h2.css ('visibility', 'hidden');
		slogan.data = '';
		$header[0].index = 1;
		
		setInterval (function() {
			var index = $header[0].index,
			    href  = url.replace (images[0], images[index]);
			
			$h2.css ('background-image', 'url("' + href + '")' );
			$header[0].index++;
			
			setTimeout (function() {
				slogan.data = '';
				$h2.hide(0).css('visibility', 'visible').fadeIn (1000, function () {
					$h2.css ('visibility', 'hidden');
					$header.css ('background-image', 'url("' + href + '")' );
					slogan.data = slogans[index];
				});
				if (index+2 > images.length) { $header[0].index = 0; }	
			}, 3500);
			
		}, 8500);		
	}

    $('#login').focus();
	
	
     // add photo
	/*$('a.add.button').live ('click', function () {
		var _self = this, $window = $('<div/>').attr({id:'window','class':'loader'}).css({display:'none',top:(this.offsetTop-150)+'px'});
		$window.append ('<h1>' + $(this).text() + '</h1><a href="#close" class="close" title="zamknij">[X]</a>')
		.appendTo (document.body).fadeIn(600);
		$.get (this.href + '&ajax', function (html) {
			var parsed = html.replace(/<script.*?>.*?<\/script>|<link.*?\/>/gim,'');
            $window.append (parsed).removeClass('loader');
		});
		return false;	
	});
	
	// delete photo
	$('a.delete').live ('click', function () {
		var _self = this, $window = $('<div/>').attr({id:'window','class':'loader'}).css({display:'none',top:(this.offsetTop-150)+'px'});
		$window.append ('<h1>' + $(this).text() + '</h1><a href="#close" class="close" title="zamknij">[X]</a>')
		.appendTo (document.body).fadeIn(600);
		$.get (this.href, function (html) {
			var parsed = html.replace(/<script.*?>.*?<\/script>|<link.*?\/>/gim,'');
            $window.append (parsed).removeClass('loader');
		});
		return false;	
	});*/
	
	
	$('a.googlemap').each (function() {
		var param = this.href.split('&');
		
		var iframe = '<iframe class="googlemap" frameborder="0" src="' + (this.href + '&amp;output=embed&amp;z=13') + '"></iframe>';
		
		$(this).replaceWith (iframe);
	});
	
	var $info = $('p.info');
	if ($info.length) {
		setTimeout (function () {
			$.CMS.html.status ($info);
		}, 6000);	
	}

	// tabs
	function tabEvent(node) {
		var $parent = $(node).parents ('ul:first'),
		    $current = $('.tab-show a', $parent);

		$current.parent().removeClass('tab-show');
		$($current.attr('href')).removeClass ('tab-show');
		
		$(node).parent().addClass ('tab-show');
		$(node.getAttribute('href')).addClass ('tab-show');

		location.hash = node.getAttribute('href') + '!'; // for back button, ! prevent scrolling
		
		
	}

	/*$('a.hide').live ('click', function () {
		$(this).parent().fadeOut (600, function() { $(this).remove(); });
		return false;
	});*/	
		
	$(document.body).click (function(e) {
		var self = e.target, classes;
		
		if (self.tagName == 'A') {
			classes = ' ' + self.className + ' ';
			
                        //
			if (~classes.indexOf (' close ') || ~classes.indexOf (' cancel ')) {
				$.CMS.window.close (self);
				return false;	
			}
 			// ajax
			else if (~classes.indexOf (' ajax-replace ')) {
				$.ajax ({
					url    : $.CMS.ajax.url (self.href),
					cache  : false,
					type : 'GET',
					success: function (html) {
						console.log ('ok');
                                                            $.CMS.html.replace (self, html, function ($replaced) {
							console.log ($replaced);
							$.CMS.ajax.parse ($replaced.parent());
						});                        
					} 
				});
				return false;
			}                       
                        //                              
                        /*else if (~classes.indexOf (' hide ')) {
				$(self).parent().fadeOut (600, function() { $(this).remove(); });
				return false;	
			}*/
                        //                        
                              else if (~self.parentNode.parentNode.className.indexOf ('tab-control')) {
				tabEvent (self);
				return false;	
                              }
                              else if (~classes.indexOf (' control-switch ')) {
      
                                    var text = self.getAttribute('title').split('|'),
                                        $pane = $(self.getAttribute('href'));
                                        
                                    if ($pane.hasClass('hide')) {
                                              $pane.removeClass ('hide');
                                              $(self).addClass('active').text (text[1]);
                                    } else {
                                              $pane.addClass ('hide');
                                              $(self).removeClass('active').text (text[0]);
                                    }
                                    return false;
                              }
		}
                else if (self.tagName == 'input') {
                        //			
                        if (self.getAttribute ('type') == 'submit') {
				var $window = $(self).parents('.window');
				$window.addClass ('loader');
				if ($.editor) { $.editor.remove ($window, 'textarea.editable'); }
                                
                                alert ('submit');
                                
				return false;				
			}
		}	
	
	});
	
	/*$('a.close, a.cancel').live ('click', function () {
		$.CMS.window.close (this);
		return false;
	});
	
	$('div.window input:submit').live ('click', function () {
		var $window = $(this).parents('.window');
		$window.addClass ('loader');
		if ($.editor) $.editor.remove ($window, 'textarea.editable');
	});*/
	
	// parse 
	$.CMS.ajax.parse (document.body);
        
        if ($.fn.textSlide) {
          $('.slider').textSlide({fx: 'slide',fxoption  : [500]});
        }

	// support for history in old way
	if (location.hash) {
		var hash = location.hash.replace('!',''), $el = $(hash);
		//console.log (hash,$el);
		// support tabs
		if ($el.hasClass ('tab-pane')) {
			//console.log ($('ul.tab-control').find ('a[href='+hash+']'));
			$('ul.tab-control').find ('a[href='+hash+']').trigger ('click');	
		}	
	}	
	
});