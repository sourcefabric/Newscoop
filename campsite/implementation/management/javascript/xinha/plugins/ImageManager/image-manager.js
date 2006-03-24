/**
 * The ImageManager plugin javascript.
 * @author $Author$
 * @version $Id$
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
	developer     : "Xiang Wei Zhuo, Paul Baranowski",
	developer_url : "http://www.zhuo.org/htmlarea/",
	license       : "htmlArea"
};


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
	if (image) {
		outparam = {
			f_url    : HTMLArea.is_ie ? image.src : image.getAttribute("src"),
			f_image_template_id : image.id,
			f_alt    : image.alt,
			f_border : image.border,
			f_align  : image.align,
			f_vert   : image.vspace,
			f_horiz  : image.hspace,
			f_width  : image.width,
			f_height  : image.height,
			f_caption : HTMLArea.is_ie ? image.sub : image.getAttribute("sub").replace(/\&quot;/g, '"')
		};
	}
	var manager = _editor_url + 'plugins/ImageManager/manager.php?article_id='+_campsite_article_id;

	Dialog(manager, function(param) {
		//alert("Inside IM_InsertImage");
		if (!param) {	// user must have pressed Cancel
			return false;
		}
		var img = image;
		if (!img) {
			// Image was added.
			var imageTag = '<img src="'+param.f_url+'"';
			if (param.f_alt) {
				imageTag += ' alt="'+param.f_alt.replace(/\"/g, "&quot;")+'"';
			}
			if (param.f_align) {
				imageTag += ' align="'+param.f_align+'"';
			}
			if (param.f_caption) {
				imageTag += ' sub="'+param.f_caption.replace(/\"/g, "&quot")+'"';
			}
			if (param.f_image_template_id) {
				imageTag += ' id="'+param.f_image_template_id+'"';
			}
			imageTag += ' />';
			//alert(imageTag);
			editor.insertHTML(imageTag);
		} else {
			// Image was modified.
			img.src = param.f_url;
			for (field in param) {
				var value = param[field];
				//alert(field+' : '+value);
				switch (field) {
					case "f_image_template_id" : img.id = parseInt(value || "0"); break;
				    case "f_alt"    : img.alt	 = value.replace(/\"/g, "&quot;"); break;
				    case "f_caption": img.setAttribute("sub", value.replace(/\"/g, "&quot;")); break;
				    case "f_border" : img.border = parseInt(value || "0"); break;
				    case "f_align"  : img.align	 = value; break;
				    case "f_vert"   : img.vspace = parseInt(value || "0"); break;
				    case "f_horiz"  : img.hspace = parseInt(value || "0"); break;
					case "f_width"  : img.width = parseInt(value || "0"); break;
					case "f_height" : img.height = parseInt(value || "0"); break;
				}
			}
		}
	}
	, outparam);
};


