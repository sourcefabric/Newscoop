// Insert Image plugin for HTMLArea
// Original Author - Udo Schmal
//
// (c) www.Schaffrath-NeueMedien.de  2004
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

//Usage:
//  if(typeof InsertPicture != 'undefined')
//  {  InsertPicture.PicturePath = [webpath to imagefolder];
//     InsertPicture.LocalPicturePath = [local server path to imagefolder];
//  }
//  for Example:
//  if(typeof InsertPicture != 'undefined')
//  {  InsertPicture.PicturePath = _editor_url + "plugins/insertPicture/demo_pictures/";
//     InsertPicture.LocalPicturePath = "d:\\inetpub\\wwwroot\\xinha\\trunk\\plugins\\insertPicture\\demo_pictures";
//  }


function InsertPicture(editor) {
// nothing to do
}

InsertPicture._pluginInfo = {
	name          : "InsertPicture",
	version       : "1.0.1",
	developer     : "Udo Schmal",
	developer_url : "http://www.Schaffrath-NeueMedien.de/",
	sponsor       : "L.N.Schaffrath NeueMedien",
	sponsor_url   : "http://www.schaffrath-neuemedien.de/",	
	c_owner       : "Udo Schmal",
	license       : "htmlArea"
};

HTMLArea.prototype._insertImage = function(image) {
	var editor = this;
	var outparam = null;
	if (typeof image == "undefined") {
		image = this.getParentElement();
		if (image && !/^img$/i.test(image.tagName))
			image = null;
	}
	if (image) outparam = {
		f_url    : HTMLArea.is_ie ? image.src : image.getAttribute("src"),
		f_alt    : image.alt,
		f_border : image.border,
		f_align  : image.align,
		f_vert   : image.vspace,
		f_horiz  : image.hspace,
		f_width  : image.width,
		f_height  : image.height
	};

	var manager = _editor_url + 'plugins/InsertPicture/InsertPicture.php'
	              + '?picturepath=' + InsertPicture.PicturePath;

	Dialog(manager, function(param) {	        
		if (!param) {	// user must have pressed Cancel
			return false;
		}
		if (!image) {
			var sel = editor._getSelection();
			var range = editor._createRange(sel);
			editor._doc.execCommand("insertimage", false, param.f_url);
			if (HTMLArea.is_ie) {
				image = range.parentElement();
				// wonder if this works...
				if (image.tagName.toLowerCase() != "img") {
					image = image.previousSibling;
				}
			} else {
				image = range.startContainer.previousSibling;
			}
		} else {
			image.src = param.f_url;
		}

		for (field in param) {
			var value = param[field];
			switch (field) {
			    case "f_alt"    : image.alt	 = value; break;
			    case "f_border" : image.border = parseInt(value || "0"); break;
			    case "f_align"  : image.align	 = value; break;
			    case "f_vert"   : image.vspace = parseInt(value || "0"); break;
				case "f_horiz"  : image.hspace = parseInt(value || "0"); break;
				case "f_width"  : image.width = parseInt(value || "0"); break;
				case "f_height"  : image.height = parseInt(value || "0"); break;
			}
		}


	}, outparam);
};