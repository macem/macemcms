/*!
* jQuery HTML5 Uploader 1.0b
*
* http://www.igloolab.com/jquery-html5-uploader
*/
 (function($) {
    $.fn.dnduploader = function(options) {
        var settings = {
            "name"     : "uploadedFile",
			"params"   : null,
            "parallel" : 3, // 5 files in the same time
            "limit"    : 1048576, // 1mb
            "action"   : "",
            "onDisplay": null,
			"onExists" : null,
            "onServerAbort": null,
            "onServerError": null,
            "onServerLoad": null,
            "onServerLoadStart": null,
            "onServerProgress": null,
            "onServerReadyStateChange": null,
            "files" : []
	        };
        
        if (options) {
            $.extend(settings, options);
        }
		
		/* file.status
		0 - send check
		1 - exists
		2 - ask replace?
		3 - replace
		4 - wait to upload
		5 - uploading
		6 - complete
		7 - error
		*/
        
		return this.each (function(options) {
            var $this = $(this);
            
            this.options = settings;
            this.options.exists = this.getAttribute ('href');
            
            $this.bind ("dragenter dragover", function() {
                return false;
            }).bind ("drop", function(e) {
                var files = e.originalEvent.dataTransfer.files, exist;
                
				for (var i = 0; i < files.length; i++) {

					files[i].id = files[i].name.replace(/[\., ,_,-,\},\{,\],\[,\(,\),\?,\",\',\=,\!]/gim,'');
					exist = $.fn.dnduploader.find (this.options.files, files[i].id);
		
					if (files[i].fileSize >= this.options.limit) {
						alert ('You cannot upload files bigger than '+(this.options.limit/1024)+'MB using this interface. To upload those files, click on blue (S) icon and upload them directly to ShareWeb.');
						return;
					} else if (!exist || exist && exist.status == 6) {
						this.options.files.push (files[i]);
						this.options.onDisplay (files[i]);						
					} else {
						if (exist.status == 4) { 
							alert ('File "'+exist.name+'" is dropped already and awaiting.');
						} else if (exist.status == 7) {
							alert ('File "'+exist.name+'" is dropped already, but cannot be uploaded.');	
						} else if (exist.status == 5) {
							alert ('File "'+exist.name+'" is dropped already, and uploading.');	
						}
					}
                }
				
				// exists
                $.fn.dnduploader.check (this.options.files, undefined, function (file) {
					$.ajax ({
						url     : $this[0].options.exists.replace ('nodeNames', 'nodeName') + escape(file.name),
						dataType: 'json',
						success : function (html) {
							if (file.status == 7) return; // abort
							
							if (html.code == '200') {
								file.status = 4;
								var uploadingFiles = $.fn.dnduploader.check ($this[0].options.files, 5);
								if (uploadingFiles < settings.parallel) $.fn.dnduploader.upload ($this, file);
							} else {
							    file.status = 1; //exists
								if (settings.onExists) settings.onExists (file);
							}
							
							//console.log ('check', file.status);	
						}	
					});
					file.status = 0;
				});
                
                return false;
            });
        });

    };

	$.fn.dnduploader.find = function (array, id) {
		for (var i=0,l=array.length; i<l; i++) {
			if (array[i].id == id) {
				return array[i]
			}	
		}
		return false;				
	};
			        
    $.fn.dnduploader.check = function (array, status, callback) {
		var index = 0;
		for (var i=0,l=array.length; i<l; i++) {
			if (array[i].status === status) {
				if (callback) { callback (array[i]); }
				index++;
			}
		}
		return index;
	};
	
	$.fn.dnduploader.abort = function ($node, file) {
		file.status = 7;
		if (file.xhr) { 
			file.xhr.abort(); 
		} else { // abort
			if ($node[0].options.onServerAbort) {
				$node[0].options.onServerAbort (null, file);
            }			
		}
	},
	
	$.fn.dnduploader.upload = function ($node, file, params) {

		var settings = $node[0].options;
		
		if (console) console.log ('send', file.name);
		
		file.status = 5;
			            
		file.xhr = new XMLHttpRequest();
		
        file.xhr.upload.onabort = function(e) {
            file.status = 7;
			
			if (settings.onServerAbort) {
				settings.onServerAbort(e, file);
            }
			
			file.xhr = null;			
        };
        file.xhr.upload.onerror = function(e) {
            file.status = 7;
		
			if (settings.onServerError) {
				settings.onServerError(e, file);
            }
			
			file.xhr = null;			
        };
        file.xhr.upload.onloadstart = function(e) {
            
			if (settings.onServerLoadStart) {
                settings.onServerLoadStart(e, file);
            }
        };
        file.xhr.upload.onprogress = function(e) {
            if (settings.onServerProgress) {
                settings.onServerProgress(e, file);
            }
        };
        file.xhr.onreadystatechange = function(e) {
            
			if (settings.onServerReadyStateChange && file.xhr && file.xhr.readyState == 4) {

				file.status = 6;
				
				$.fn.dnduploader.check (settings.files, 4, function (filea) {
					var uploadingFiles = $.fn.dnduploader.check (settings.files, 5);
					if (uploadingFiles < settings.parallel) $.fn.dnduploader.upload ($node, filea);
				});	
								
				if (file.xhr.status != 200) {
                    file.status = 7;
					settings.onServerError (e, file);
					file.xhr = null;												
					return false;
				}
			
				settings.onServerReadyStateChange (e, file);

				file.xhr = null;
				file.id = null; //prevent to find the same file
				// remove file from []						
            }
        };
		
		file.xhr.open ('POST', settings.action, true);
		
        file.xhr.setRequestHeader ("X-File-Size", file.fileSize);
        file.xhr.setRequestHeader ("X-File-Name", file.fileName);

        var formData = new FormData();
        formData.append (settings.name, file);
		
		for (param in settings.params) {
			formData.append (param, settings.params[param]); 	
		}
		
		for (param in params) {
			formData.append (param, params[param]); // custom	
		}		
        
		file.xhr.send (formData); 
	
	};

})(jQuery);