/**
 * The ImageManager plugin javascript.
 * @author $Author: paul $
 * @version $Id: image-manager.js,v 1.1 2005/05/03 15:28:07 paul Exp $
 * @package ImageManager
 */

/**
 * To Enable the plug-in add the following line before HTMLArea is initialised.
 *
 * HTMLArea.loadPlugin("ImageManager");
 *
 * Then configure the config.inc.php file, that is all.
 * For up-to-date documentation, please visit http://www.zhuo.org/htmlarea/
 */

/**
 * It is pretty simple, this file over rides the HTMLArea.prototype._insertImage
 * function with our own, only difference is the popupDialog url
 * point that to the php script.
 */

function ImageManager(editor)
{

};

ImageManager._pluginInfo = {
	name          : "ImageManager",
	version       : "1.0",
	developer     : "Xiang Wei Zhuo",
	developer_url : "http://www.zhuo.org/htmlarea/",
	license       : "htmlArea"
};

// default Xinha layout. plugins are beneath the Xinha directory.
// Note the trailing &. Makes forming our URL's easier. 
//
// To change the backend, just set this config variable in the calling page.
// The images_url config option is used to strip out the directory info when
// images are selected from the document.

HTMLArea.Config.prototype.ImageManager =
{
  'backend' : _editor_url + 'plugins/ImageManager/backend.php?__plugin=ImageManager&',
  'images_url' : _editor_url + 'plugins/ImageManager/demo_images'
}

// Over ride the _insertImage function in htmlarea.js.
// Open up the ImageManger script instead.

HTMLArea.prototype._insertImage = function(image) {

	var editor = this;	// for nested functions
	var outparam = null;
	if (typeof image == "undefined") {
		image = this.getParentElement();
		if (image && !/^img$/i.test(image.tagName))
			image = null;
	}

	// the selection will have the absolute url to the image. 
	// coerce it to be relative to the images directory.
	//
	// FIXME: we have the correct URL, but how to get it to select?
	// FIXME: need to do the same for MSIE.

	if ( image )
		{
		if ( HTMLArea.is_ie )
			{
			var image_src = image.src;
			}
		else
			{
			// gecko

			var image_src = image.getAttribute("src");

			// strip off any http://blah prefix

			var images_url = editor.config.ImageManager.images_url.replace( /https?:\/\/[^\/]*/, "" );

			// alert( "images_url is '" + images_url + "'" );

			var image_regex = new RegExp( images_url );

			// alert(" regex is '" + image_regex.source + "'" );

			image_src = image_src.replace( image_regex, "" );

			// alert( "new source is " + image_src );
			}
	
		outparam = 
			{
			f_url    : HTMLArea.is_ie ? image.src : image_src,
			f_alt    : image.alt,
			f_border : image.border,
			f_align  : image.align,
			f_vert   : image.vspace,
			f_horiz  : image.hspace,
			f_width  : image.width,
			f_height  : image.height
			};

      // TODO - somehow highlight and focus the currently selected image.

		} // end of if we selected an image before raising the dialog.

	// the "manager" var is legacy code. Should probably reference the
	// actual config variable in each place .. for now this is good enough.

	// alert( "backend is '" + editor.config.ImageManager.backend + "'" );

	var manager = editor.config.ImageManager.backend + '__function=manager';

	Dialog(manager, function(param) {
		if (!param) {	// user must have pressed Cancel
			return false;
		}
		var img = image;
		if (!img) {
			var sel = editor._getSelection();
			var range = editor._createRange(sel);			
			editor._doc.execCommand("insertimage", false, param.f_url);
			if (HTMLArea.is_ie) {
				img = range.parentElement();
				// wonder if this works...
				if (img.tagName.toLowerCase() != "img") {
					img = img.previousSibling;
				}
			} else {
				img = range.startContainer.previousSibling;
			}
		} else {			
			img.src = param.f_url;
		}
		
		for (field in param) {
			var value = param[field];
			switch (field) {
			    case "f_alt"    : img.alt	 = value; break;
			    case "f_border" : img.border = parseInt(value || "0"); break;
			    case "f_align"  : img.align	 = value; break;
			    case "f_vert"   : img.vspace = parseInt(value || "0"); break;
			    case "f_horiz"  : img.hspace = parseInt(value || "0"); break;
				case "f_width"  : img.width = parseInt(value || "0"); break;
				case "f_height"  : img.height = parseInt(value || "0"); break;
			}
		}
		
		
	}, outparam);
};


