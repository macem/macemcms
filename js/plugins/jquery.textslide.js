/* 
 * slide text boxes, images, lists ...
 * @plugin textSlide
 * @author {macem | acem@go2.pl} Marcin Sołtysiuk
 * @version {0.6beta}
 * @support {IE6+ , FF , Safari , Opera}
 * 
 * --------------------------------------------
 * version 0.6.5beta
 * - support jQuery 1.3.2 - 1.4.2,
 * - rewrite code for performance, 
 * - fix blinking slideshow stop text, when hover on breadcrumb,
 * - add new param slides - how many slides show on,
 * version 0.7
 * - move some part of code to plugins,
 * - x5 faster initialize plugin,
 * - new design for plugin,
 * TODO
 * - support BACK button
 * - around slide  
 * - resize manual : function  
 * - bounced animation 
 * - key support -> <- 
 * - full screen 
 * 
 * BUGS 
 * - when we have ul -> li    
*/

var timer = {
	array : [],
	start : function () {
		timer.array.push (new Date());	
	},
	stop : function () {
		return (new Date() - timer.array[0]);
	}
};

timer.start();

// all 40-45ms   / 20ms

(function ($) {

$.fn.textSlide = function (o) {
        var args = arguments, option;
        
	if (typeof o !== 'object') {
		return this.each (function () {
			$.fn.textSlide[o] (this, args[1], args[2]);
		});
	}

	option = $.extend ({}, $.fn.textSlide.options, o);
				
	return this.each (function () {
		//var $this = $(this), $slide, i = 0, len;
		
		if (this.options) return; 

		/*if (methods[option]) {
			methods[option].apply (this, Array.prototype.slice.call (arguments, 1));
		} else if (typeof option === 'object' || !option) {
			//console.log (arguments);
			methods.init.apply (this);
		} else {
			alert ('Method ' +  option + ' does not exist on jQuery.tooltip');
		} */
		
		$.fn.textSlide.init ($(this), option);

		// todo speedup 
		/*$this.mouseover (function (event) {
			var $target = $(event.target);
			if ($target.parents('.t-container').length) {	
				   hover ($target[0]).addClass ('current');
			}	
		});
		$this.mouseout (function (event) {
			var $target = $(event.target);
			if ($target.parents('.t-container').length) {	
				   hover ($target[0]).removeClass ('current');
			}	
		});*/		
	});
};

var ts = $.fn.textSlide;

ts.options = { 
	step      : 1,            // how many slides
	axis      : 'horizontal', // [vertical, horizontal]
	slide     : 0,            // start from this slide  
	slides    : 1,            // how many slides visible
	controls  : true,
	mode      : '', // around, both, one
	eventqueue: true,
	fx        : 'slide',      // [slide, show, fade]
	fxoption  : [1000],
	previous  : { 
		href   : '#previous-slide',
		'class': 't-previous',
		title  : 'previous slide',
		text : '<'
	},
	next : { 
		href   : '#next-slide',
		'class': 't-next',
		title  : 'next slide',
		text : '>'
	},
	//onSlideEnd: function ($pane, option) {}, 	
	_nextslide: 0,
	_queue    : [],
	css       : {
		horizontal : ['offsetWidth', 'margin-Left', 'margin-Right'],
		vertical   : ['offsetHeight', 'margin-Top', 'margin-Bottom']
	},
	events : {
		init  : 'click mouseover mouseout', // mouseover mouseout keyup
		click : [],
		mouseover : [],
		mouseout  : []	
	}	
}; 
	
ts.onCreate = [];   // array with function ($node) {}
ts.onSlide = [];    // array with function ($node) {}
ts.onDestroy = [];  // array with function ($node) {}
ts.onSlideEnd = []; // array with function ($pane. options) {} 

ts.coords = function (option) {
	if (option.axis == 'horizontal') {
		return {left: -option._nextpos}; 
	} else if (option.axis == 'vertical') {
		return {top: -option._nextpos};
	}					
};
ts.parentByClass = function (node, classes) {
	var parent=node.parentNode || null;
	while (parent) {
		if (parent.className && parent.className.indexOf (classes) != -1) return parent;
		parent = parent.parentNode || null;
	}
};
ts.getStyle = function (self, styleProp) {
	if (self.currentStyle) {
		var y = self.currentStyle[styleProp];
		if (!y) y = self.currentStyle[styleProp.replace('-','')]; // IE8 bug marginLeft
	} else if (window.getComputedStyle) {
		var y = document.defaultView.getComputedStyle (self, null).getPropertyValue (styleProp);
	}
	return y;
};
ts.space = function (self, style) {
	return self[style[0]] + parseInt (this.getStyle (self, style[1])) + parseInt (this.getStyle (self, style[2])); 
};		
ts.size = function (first, params) {
	var prev=first, size=0;
	while (prev = prev.previousSibling) {
		size += this.space (prev, params);
		//console.log (prev, size);
	};
	return size;			
};				
ts.slide = function ($pane, slides, option) {
	var ts=this, i=0, len=ts.onSlideEnd.length;
	//console.log (option._nextpos);
	$pane.animate (ts.coords (option), option.fxoption[0], function () { 
		for (; i<len; i++) {
			ts.onSlideEnd[i] ($pane, option);
		}
	});
};
ts.slide1 = function ($pane, slides, option) {
	var ts=this, i=0, len=ts.onSlideEnd.length;
	
	/*var coords = ts.coords (option);
	var left = $pane[0].offsetLeft;
	var j=0, frame = 20, wait=5;
	var l=coords.left;
	var step = (left-l)/frame;

	var aa = setInterval (function () {
		
		j++;
		$pane[0].style.left = left + (j*-step) + 'px';
		if (j>=frame) {
			clearInterval (aa);
			for (; i<len; i++) {
				ts.onSlideEnd[i] ($pane, option);
			}
		}
	}, wait);*/
	
	$pane.animate (ts.coords (option), option.fxoption[0], function () { 
		for (; i<len; i++) {
			ts.onSlideEnd[i] ($pane, option);
		}
	});
};
ts.show = function ($pane, slides, option) {
	$pane.css (this.coords (option));
	//this.onSlideEnd ($pane, option);
	for (var i=0, len=this.onSlideEnd.length; i<len; i++) {
		this.onSlideEnd[i] ($pane, option);
	}	
};
ts.fade = function ($pane, slides, option) {
	var ts=this, i=0, len=ts.onSlideEnd.length;
	$pane.fadeOut (option.fxoption[0], function () {
		$pane.css (ts.coords (option)).fadeIn (option.fxoption[0], function () { 
			//ts.onSlideEnd ($pane, option); 
			for (; i<len; i++) {
				ts.onSlideEnd[i] ($pane, option);
			}
		});
	});
};
/*ts.ss = function ($pane, slides, option) {
	var ts=this, i = 0, left = parseInt ($pane[0].style.left.replace ('px',''))||0, interval,
	len = Math.abs (ts.coords (option).left - left), step = -(len/option.fxoption[0]);

	if (left < ts.coords (option).left) step = len/option.fxoption[0];

	interval = setInterval (function () {
		$pane[0].style.left = Math.floor (left + (++i*step)) + 'px';
		if (Math.abs (Math.floor (i*step)) >= len) clearTimeout (interval);
	}, 0);

	ts.onSlideEnd ($pane, option); 
};*/

// display slide 880-
function display ($container) {
	var node=$container[0].parentNode.parentNode, o=node.options, i=0, len=ts.onSlide.length,
	slides={
		current: $container.children()[o.slide],
		next   : $container.children()[o._nextslide]
	};

        o._nextpos = ts.size (slides.next, o.css[o.axis]);
        
	for (; i<len; i++) {
		ts.onSlide[i] (node, slides);
	}
        
	o._fx = true;
			
	ts[o.fx] ($container, slides, o);

	o.slide = o._nextslide;
};

// public
//* show [next, previous, integer] slide  805-
ts.go = function (node, direction) {
	//console.log (timer.stop());
	var o=node.options, elements=o.container.children().length, tmp=(elements-o.slide-o.slides);
	
	if (o._fx) { 
		if (o.eventqueue) o._queue.push ([$.fn.textSlide.go, node, direction]);
		return false; 
	}
		
	if (direction == 'next') {
		o._nextslide = o.slide + (o.step<tmp ? o.step : tmp);
	} else if (direction == 'previous') {
		o._nextslide = o.slide - (o.slide%o.step || o.step);
	} else {
		o._nextslide = (((direction*1)+o.slides)>elements ? (elements-o.slides) : (direction*1)); // parseInt() = *1	
	}

	if (o._nextslide < 0) {
		o._nextslide = elements - o.slides;
	} else if (o._nextslide > (elements-1) || direction == 'next' && (o.slide + o.slides) >= elements) {
		o._nextslide = 0;
	}
	
	display (o.container);
	//console.log (timer.stop());
	return false;
};

// create navigation
ts.navigate = function ($node) {
	var o = $node[0].options;
	$node.prepend ($('<a/>').attr(o.next).text(o.next.text), $('<a/>').attr(o.previous).text(o.previous.text));  // not $(selector, attr) 1.4.2
};

// public
ts.destroy = function (node) {
	var $node=$(node), i=0, len=this.onDestroy.length;

	$node.unbind (node.options.events.init);
	
	for (; i<len; i++) {
		this.onDestroy[i] ($node);
	}	
	
	$(node.options.container.children()).removeClass ('t-slide').appendTo (node);
	
	$('.t-previous, .t-next, .t-pane', node).remove();
	
	node.options.container.remove();
	
	delete node.options;
};

ts.options.events.click.push (function ($target, node) {
	if ($target.hasClass ('t-previous')) {
		$.fn.textSlide.go (node, 'previous');
		$target.blur();
		return false;
	} else if ($target.hasClass ('t-next')) {
		$.fn.textSlide.go (node, 'next');
		$target.blur();
		return false;
	}
	return true;
});

ts.onSlideEnd.push (function ($pane, option) { 
	var queue;
	option._fx = false; 
	if (option.eventqueue && (queue = option._queue.shift())) {
		queue[0] (queue[1], queue[2]); // queue
	}
});

// public 1095	
ts.init = function ($node, option) {
	var $slide, i=0, len=this.onCreate.length,
	
	o = $node[0].options = option;
	
	$node.addClass ('t-slider t-' + o.axis + (o.autosize?' t-autosize':''));

	o.container = $('<'+$node[0].tagName.toLowerCase()+'/>').addClass('t-container').append ($node[0].children);

	if (o.container.children().length && !o.container.children()[0].className.match (/t-pane/)) {
		$('<div/>').addClass('t-pane')
		.append (o.container)
		.appendTo ($node);
	} 

	$(o.container.children()).addClass ('t-slide').css('width', o.container.parent().width());

	$slide = o.container.children()[o.slide];

	if (o.controls) this.navigate ($node);

	if (o.slide !== 0) {
		o._nextslide = options.slide;
		o.slide = 0;
		this.display ($node, o.container); 
	}
	
	for (; i<len; i++) {
		this.onCreate[i] ($node);
	} 

	// TODO live is to expensive / event delegation 4x faster
	$node.bind (o.events.init, function (event) {
		var $return=true, ts=$.fn.textSlide, $target=$(event.target), node=ts.parentByClass ($target[0], 't-slider');
		
		if (node) {
			for (var i=0, len=node.options.events[event.type].length; i<len; i++) {
				//console.log (event.type, node);
				$return = node.options.events[event.type][i]($target, node||$target[0]);
				if ($return==false) { return false; }
			}
		}
	}); 		
};	

})(jQuery);
