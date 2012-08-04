/*(function($) {

$.fn.extend({
  dragout: function() {
    var files = this;
    if (files.length > 0) {
      $(files).each(function() {
        var url = (this.dataset && this.dataset.downloadurl) ||
                   this.getAttribute("data-downloadurl");
        if (this.addEventListener) {
          this.addEventListener("dragstart", function(e) {
            if (e.dataTransfer && e.dataTransfer.constructor == Clipboard &&
                e.dataTransfer.setData('DownloadURL', 'http://www.box.net')) {
              e.dataTransfer.setData("DownloadURL", url);
            }
          },false);
        }
      });
    }
  }
});

})(jQuery);*/

var files = document.querySelectorAll('.drag');
for (var i = 0, file; file = files[i]; ++i) {
  file.addEventListener('dragstart', function(e) {
         var url = (this.dataset && this.dataset.downloadurl) ||
                   this.getAttribute("data-downloadurl");     
    
    //e.dataTransfer.setData('DownloadURL', url);

    $.ajax({
      async: false,
      complete: function(data) {
        e.dataTransfer.setData("DownloadURL", data.responseText);
      },
      error: function(xhr) {
        if (xhr.status == 404) {
          xhr.abort();
        }
      },
      type: 'GET',
      timeout: 3000,
      url: url
    });   
  }, false);
}