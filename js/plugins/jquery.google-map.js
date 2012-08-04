function marker (map, option) {
	var icon = new google.maps.MarkerImage(option.icon,
		new google.maps.Size(32,37),
		new google.maps.Point(0,0),
		new google.maps.Point(16,37)
	);

	var position = new google.maps.LatLng (option.x, option.y);
	
	var marker = new google.maps.Marker({
		id      : option.id,
		addjson : option.addjson,
		position: position,
		map     : map,
		icon    : icon,
		title   : option.title,
		draggable : true
	});

	google.maps.event.addListener (marker, 'click', function() {
		var infowindow = new google.maps.InfoWindow({
			content: '<h2>' +  option.title + '</h2>' + option.desc + '<br/><a href="' + option.edit + '" class="edit">Edit</a><a href="' + option.remove + '" class="delete">Remove</a>'
		});
		
		infowindow.open (map, marker);
	});
	
	google.maps.event.addListener (marker, 'dragend', function() {
		$.ajax ({
			url    : this.addjson,
			cache  : false,
			type   : 'POST',
			data   : '&hidden:x=' + this.getPosition().$a + '&hidden:y=' + this.getPosition().ab,
			success: function (html) {
				;
			}
		});
	});
}

function points (node) {
	return {
		id     : node.getAttribute('data-id'),
		title  : node.getAttribute('data-title'),
		desc   : node.getAttribute('data-description'),
		x      : node.getAttribute('data-x'),
		y      : node.getAttribute('data-y'),
		icon   : node.getAttribute('data-icon'),
		edit   : node.getAttribute('data-edit-url'),
		remove : node.getAttribute('data-remove-url'),
		addjson: node.getAttribute('data-add-json')
	}
}

$(function(){
	var $maps = $('.googlemap');

	if (!window.google) return;
	
	var options = {
		'hybrid' : google.maps.MapTypeId.HYBRID
	};

	$maps.each (function() {
		var _this = this.parentNode;
		
		var myOptions = {
			center    : new google.maps.LatLng (this.getAttribute('data-x'), this.getAttribute('data-y')),
			zoom      : parseInt(this.getAttribute('data-zoom')),
			mapTypeId : options[this.getAttribute('data-type')]
		};		
		
		_this.data = [];

		$(this).children().each (function() {
			_this.data.push (points (this));
		});

		_this.node = new google.maps.Map (this, myOptions);

		google.maps.event.addListener (_this.node, 'maptypeid_changed', function() {
			console.log ('maptypeid_changed', this);
		});		
		google.maps.event.addListener (_this.node, 'dragend', function() {
			console.log ('dragend', this);
		});
		google.maps.event.addListener (_this.node, 'zoom_changed', function() {
			console.log ('zoom_changed', this);
		});		
		
		_this.data.forEach (function (item) {
		          marker (_this.node, item);	
		});
		
		//console.log (_this.data);
	});
});