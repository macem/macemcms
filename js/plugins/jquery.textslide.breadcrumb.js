jQuery.fn.textSlide.options.breadcrumb = true;
jQuery.fn.textSlide.options.bscroll = false;
jQuery.fn.textSlide.options.bheader = {param:'title'}, //selector:, param:;
jQuery.fn.textSlide.options.bcalculate = 'normal'; // [normal, real]
jQuery.fn.textSlide.options.btitle = 'slide '; //'slide 1'
	
// public - select current breadcrumb
jQuery.fn.textSlide.current = function (node, id) {
	var $breadcrumb = jQuery ('ul:first', node), counter, o = node.options; 
	if (o.bcalculate == 'real') {
		counter = 1+ o._nextslide + '/' + o.container.children().length;
	} else {
		counter = 1+ parseInt (o._nextslide/node.options.step) + '/' + parseInt (o.container.children().length/node.options.step);
	}

	jQuery('li.t-active', $breadcrumb).removeClass ('t-active');
	jQuery('a[slide=' + id + ']', $breadcrumb).parent().addClass ('t-active');
	$breadcrumb.next().text (counter);
};

// public - create breadcrumb TODO - own template		
jQuery.fn.textSlide.breadcrumb = function (node) {
	var o = node.options, html = '<ul class="t-breadcrumb' + (o.bscroll?' t-breadcrumb-scroll':'') + '">', i = 0, len, title, counter=0, content;
	
	for (len=o.container.children().length; i<len; i+=o.step) {
		title = (o.container.children()[i].alt||o.container.children()[i].title||(o.btitle+(i+1))); // alt from img or title from div
		
		if (o.bheader.param) content = o.container.children()[i][o.bheader.param];
		if (o.bheader.selector) content = $(o.bheader.selector, o.container.children()[i]).text();
		if (!content) content = (counter+1); 
		counter = (o.bcalculate == 'real' ? i : parseInt (i/o.step)+1); 
		html += '<li><a href="#slide" title="' + title + '" slide="' + i + '">' + content + '</a></li>';	
	}
	o._slides = i*o.step;
	
	html += '</ul><strong class="t-breadcrumb-count" title="slide counter"/>'
	
	if (o.bscroll == true) html += '<a href="#left" class="t-breadcrumb-left">left</a><a href="#right" class="t-breadcrumb-right">right</a>';
	
	jQuery (node).prepend (html);	
};

// add
jQuery.fn.textSlide.onSlide.push (function (node, slides) {
	var o = node.options, tmp = Math.ceil (o._nextslide/o.step);
	if (!o.breadcrumb) return;
	
	if (o.auto && !o._mouseover) 
	jQuery ('ul:first a:eq(' + tmp + ')', node).fadeTo (o.wait+o.fxoption[0], 0, function () {
		this.removeAttribute ('style');
	});
	
	jQuery.fn.textSlide.current (node, tmp*o.step); // check *o.step
});

jQuery.fn.textSlide.options.events.mouseover.push (function ($target, node) {
	if (node.options.auto && node.options.breadcrumb) 
		jQuery ('ul:first a:eq(' + Math.ceil (node.options.slide/node.options.step) + ')', node).stop().removeAttr ('style');
});

/*jQuery.fn.textSlide.events.mouseenter.push (function ($target, node) {
	console.log ($target);
	if (!$target.hasClass ('t-slide')) return false; 
	var index = Math.floor($target.prevAll().length/node.options.step)*node.options.step;
	$('ul.t-breadcrumb a[slide=' + index + ']', node).addClass ('current'); 
	return false;
});
jQuery.fn.textSlide.event.mouseleave.push (function ($target, node) {
	if (!$target.hasClass ('t-slide')) return false; 
	var index = Math.floor($target.prevAll().length/node.options.step)*node.options.step;
	$('ul.t-breadcrumb a[slide=' + index + ']', node).removeClass ('current');
	return false;
});*/
jQuery.fn.textSlide.options.events.click.push (function ($target, node) {
	if ($target.parent().parent().hasClass ('t-breadcrumb')) { 
		jQuery.fn.textSlide.go (node, $target.attr ('slide'));
		$target.blur();
		return false;
	} else if ($target.hasClass ('t-breadcrumb-right')) {
		var $li = jQuery ('ul.t-breadcrumb li:not(.t-breadcrumb-collapse):not(.t-active):first', node);
		$li.addClass ('t-breadcrumb-collapse');
		//console.log (jQuery.fn.textSlide.size ($ss.find('li.active').get(0), node.options.css['horizontal']));	
	} else if ($target.hasClass ('t-breadcrumb-left')) {
		var $li = jQuery ('ul.t-breadcrumb li.t-breadcrumb-collapse:last', node);
		$li.removeClass ('t-breadcrumb-collapse');
		//console.log (jQuery.fn.textSlide.size ($ss.find('li.active').get(0), node.options.css['horizontal']));	
	} 
});

jQuery.fn.textSlide.onCreate.push (function ($node) {
	var o = $node[0].options, ts = jQuery.fn.textSlide;
	if (!o.breadcrumb) return;
	
	ts.breadcrumb ($node[0]);
	ts.current ($node[0], o.slide); 
		
	/*function hover (self) {
		var node = fn.parentClass (self, 't-slider');
		return $('ul.t-breadcrumb a[slide=' + (Math.floor($(self).prevAll().length/o.step)*o.step) + ']', node);
	}	
		
	$('div.t-container > *', $node).live ('mouseenter', function () {
		hover (this).addClass ('current');
	}).live ('mouseleave', function () {
		hover (this).removeClass ('current')
	});*/	
});

jQuery.fn.textSlide.onDestroy.push (function ($node) {
	jQuery ('ul.t-breadcrumb, strong.t-breadcrumb-count', $node).remove();	
});		