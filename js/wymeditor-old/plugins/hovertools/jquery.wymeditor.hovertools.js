/*
 * WYMeditor : what you see is What You Mean web-based editor
 * Copyright (c) 2005 - 2009 Jean-Francois Hovinne, http://www.wymeditor.org/
 * Dual licensed under the MIT (MIT-license.txt)
 * and GPL (GPL-license.txt) licenses.
 *
 * For further information visit:
 *        http://www.wymeditor.org/
 *
 * File Name:
 *        jquery.wymeditor.hovertools.js
 *        hovertools plugin for WYMeditor
 *
 * File Authors:
 *        Jean-Francois Hovinne (jf.hovinne a-t wymeditor dotorg)
 */

//Extend WYMeditor
WYMeditor.editor.prototype.hovertools = function() {
  
  var wym = this;
  
  //bind events on buttons
  jQuery(this._box).find(this._options.toolSelector).hover(
    function() {
      wym.status(jQuery(this).html());
    },
    function() {
      wym.status('&nbsp;');
    }
  );

  // select only
  jQuery(this._box).find(this._options.classSelector).click (function() {
      var aClasses = eval(wym._options.classesItems);
      var sName = jQuery(this).attr(WYMeditor.NAME);
      var oClass = WYMeditor.Helper.findByName(aClasses, sName);
      
      if(oClass.expr == "pre") {
	  jQuery(wym._doc).find(oClass.expr + '.' + oClass.name).removeAttr('class').addClass(oClass.name);
      }	
   });
   
   // clear css
   jQuery(this._box).find(this._options.classSelector).click (function() {   
	if (wym._selected) clear (wym._selected);	       
  }); 
  
  //classes: add/remove a style attr to matching elems
  //while mouseover/mouseout
  jQuery(this._box).find(this._options.classSelector).hover(
    function() {
      var aClasses = eval(wym._options.classesItems);
      var sName = jQuery(this).attr(WYMeditor.NAME);
      var oClass = WYMeditor.Helper.findByName(aClasses, sName);

      if(oClass){
        jqexpr = oClass.expr;
        //don't use jQuery.find() on the iframe body
        //because of MSIE + jQuery + expando issue (#JQ1143)
        if(!jQuery.browser.msie)
          jQuery(wym._doc).find(jqexpr).css('background-color','#cfc');
      }
    },
    function() {
      //don't use jQuery.find() on the iframe body
      //because of MSIE + jQuery + expando issue (#JQ1143)
      if(!jQuery.browser.msie)
        jQuery(wym._doc).find('*').removeAttr('style');
    }
  );

function clear (self) {
      var aClasses = self.className.split(' ');
      
      jQuery(wym._box).find (wym._options.classSelector).css ('background-color', '#fff');
      jQuery(wym._box).find (wym._options.containerSelector).css ('background-color', '#fff');
      
      for (var i=0, len = aClasses.length; i < len; i++) {
	      
	      // TODO jQuery(wym._box).find (wym._options.toolSelector).each(function() {
	      	//if (this.getAttribute('name').toLowerCase() == classname) $(self).addClass ('selected'); 
	      //});	      
	      jQuery(wym._box).find (wym._options.classSelector).each(function() {
			if (this.getAttribute('name') == aClasses[i]) {
				$(this).css ('background-color', '#eee');
			}
			wym._selected = self;
	      });
	      jQuery(wym._box).find (wym._options.containerSelector).each(function() {
			if (this.getAttribute('name').toLowerCase() == self.tagName.toLowerCase()) {
				$(this).css ('background-color', '#eee');
			}
			wym._selected = self;
	      });
      }
}

  /*wym._doc.body.onclick = function(e) {
      console.log(e);
      var self = e.target;
      
      if (this.nodeName == "P") {
           
      }
      
      //if (this.nodeType != 1) return;

      //clear (self);

    };*/  

};
