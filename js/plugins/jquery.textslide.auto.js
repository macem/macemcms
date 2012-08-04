jQuery.fn.textSlide.options.onAuto     = function() {};
jQuery.fn.textSlide.options.onAutoStop = function() {};
jQuery.fn.textSlide.options.auto       = 'next'; // [next, previous]  autoslide
jQuery.fn.textSlide.options.wait       = 6000;   // ms
jQuery.fn.textSlide.options._mouseover = false;  // when mouseover
jQuery.fn.textSlide.options._timeout   = null;

jQuery.fn.textSlide.options.play = { 
	href      : '#slideshow-play',
	'class'   : 't-play',
	title     : 'start slideshow',
	innerHTML : 'play'	
};
jQuery.fn.textSlide.options.stop = {
	href      : '#slideshow-stop',
	'class'   : 't-play t-stop',
	title     :	 'stop slideshow',
	innerHTML : 'stop'
};
jQuery.fn.textSlide.options.onstop = {
	'class'   : 't-stopped',
	title     :	 'slideshow stopped',
	text : '||'	
};

// public - start auto sliding	
jQuery.fn.textSlide.auto = function (node) {
	var o = node.options;

	if (!o.auto || o._mouseover) return; 
	
	o.onAuto (node);

	o._timeout = setTimeout (function () {
		jQuery.fn.textSlide.go (node, o.auto);
	}, (o.wait + o.fxoption[0])); 
};

jQuery.fn.textSlide.options.events.mouseover.push (function ($target, node) {
	var o = node.options;
	o._mouseover = true;
	if (!o.auto) return;
	//console.log ();
	// TODO test for $('> span.t-stopped', node) and node.childNodes
	
	jQuery('> span.t-stopped', node).css ('display','inline');
	clearTimeout (o._timeout);
	o.onAutoStop (node);	
});
jQuery.fn.textSlide.options.events.mouseout.push (function ($target, node) {
	var o = node.options;
	o._mouseover = false;
	jQuery('> span.t-stopped', node).css ('display','none');
	if (o.auto) jQuery.fn.textSlide.auto (node);	
});

// add
jQuery.fn.textSlide.options.events.click.push (function ($target, node) {
	var o = node.options;
	if (!$target.hasClass ('t-play')) return true;
	if (!o.auto) { // TODo
		o.auto = 'next';
		$target.attr (o.stop).blur();
	} else {
		o.auto = false;
		$target.attr (o.play).blur();
	}  		
	return false;
});

jQuery.fn.textSlide.onCreate.push (function ($node) {
	var o = $node[0].options, fn = jQuery.fn.textSlide;
	
	o.container.parent().before (jQuery('<a/>').attr (o.auto?o.stop:o.play));
	o.container.parent().before (jQuery('<span/>').attr (o.onstop).text(o.onstop.text));
	
	if (o.auto) fn.auto ($node[0]);	
});

jQuery.fn.textSlide.onSlide.push (function (node, slides) {
	if (node.options.auto) jQuery.fn.textSlide.auto (node);	
});

jQuery.fn.textSlide.onDestroy.push (function ($node) {
	jQuery ('.t-play', node).remove();		
});		