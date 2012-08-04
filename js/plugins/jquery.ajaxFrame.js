/* 
 * emulate ajax for post request, create iframe and send there form
 * @method ajaxFrame  
 * @param {String | iframe} template for iframe
 * @param {String | time} for SetInterval to check if iframe body was canged
 * @param {Function | init} 
 * @param {Function | success} 
*/

$.fn.ajaxFrame = function (o) { // TODo move to library
	var options = $.extend ({}, {
		iframe   : '<iframe id="ajaxtmp" name="ajaxtmp" style="display:none"></iframe>',
		time     : 1000,
		init    : null, //function
		success : null  // function
	}, o);	
	
	return this.each (function () {
		$(this).submit (function() {
			var self = this;
			
			// validation support so please don't add validation to form
			//if ($.fn.validForm && $.fn.validForm.check (this, o) === 0) return false;
			
			var $iframe = $(options.iframe);
			$iframe.appendTo (document.body);
			$(this).attr ('target', 'ajaxtmp'); // TODO - what when we send some form in the same time
			
			(options.init ? options.init (this) : null);

            $iframe[0].uploadtime = 0;
            
			var upload = setInterval (function() {
				var data = $('#ajaxtmp')[0].contentWindow.document.body, parsed;
				$('#ajaxtmp')[0].uploadtime += options.time;
				if (data != null && data.innerHTML != '') { 
					parsed = data.innerHTML.replace(/<\!--(.*?)-->|<\/?script.*?>|<link.*?\/>/gim,'');
					clearInterval (upload);
					(options.success ? options.success ('<body class="' + data.className + '">' + parsed + '</body>', self) : null); // STRANGE sometimes on INT <script.*?>.*?<\/script> couse error
					$iframe = null;
					self = null;					
					$('#ajaxtmp').remove();
				} else if ($('#ajaxtmp')[0].uploadtime > 90000) { // timeout
					clearInterval (upload);
					$('#ajaxtmp').remove();
					alert ('Timeout: no response from Server. Please contact with support.');	
				} 

				//(options.check ? options.check (this) : null); 
			}, options.time);
			
			//self.submit();

			//return false;
		});
	});
};