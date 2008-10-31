// Marquee plugin for HTMLArea
// Implementation by Udo Schmal based on HTMLArea 3.0
// Original Author - Udo Schmal, Schaffrath-NeueMedien
//
// (c) Udo Schmal.2004
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function InsertMarquee(editor) {
	this.editor = editor;

	var cfg = editor.config;
	var self = this;

	// register the toolbar buttons provided by this plugin
	cfg.registerButton({
	id       : "insertmarquee",
	tooltip  : this._lc("Insert scrolling marquee"),
	image    : editor.imgURL("ed_marquee.gif", "InsertMarquee"),
	textMode : false,
	action   : function(editor) {
			self.buttonPress(editor);
		}
	})
	cfg.addToolbarElement("insertmarquee", "inserthorizontalrule", -1);
}

InsertMarquee._pluginInfo = {
	name          : "InsertMarquee",
	version       : "1.0",
	developer     : "Udo Schmal",
	developer_url : "http://www.schaffrath-NeueMedien.de/",
	c_owner       : "Udo Schmal & Schaffrath NeueMedien",
	license       : "htmlArea"
};

InsertMarquee.prototype._lc = function(string) {
    return HTMLArea._lc(string, "InsertMarquee");
};

InsertMarquee.prototype.buttonPress = function(editor, node) {
  function setAttr(el, attr, value) {
    if (value != "")
      el.setAttribute(attr, value);
    else
      el.removeAttribute(attr);
  }
  var outparam = new Object();
	if (typeof node == "undefined") {
	  node = editor.getParentElement();
	}
  if ( node.tagName.toLowerCase() == "marquee") {
    outparam.f_name         = node.name;
		outparam.f_behavior     = node.behavior;
		outparam.f_direction    = node.direction;
		outparam.f_text         = node.innerHTML;
		outparam.f_width	      = node.width;
		outparam.f_height	      = node.height;
		outparam.f_bgcolor      = node.bgColor;
		outparam.f_scrollamount = node.scrollAmount;
		outparam.f_scrolldelay  = node.scrollDelay;
	} else {
	outparam = {
    f_name    : '',
		f_behavior	: '',
		f_direction	: '',
		f_text		: '',
		f_width		: '',
		f_height	: '',
		f_bgcolor	: '',
		f_scrollamount	: '',
		f_scrolldelay	: ''
		};
	}
	editor._popupDialog( "plugin://InsertMarquee/insert_marquee", function( param )
	{
		if ( !param )
		{ //user must have pressed Cancel
			return false;
		} else if ( node.tagName.toLowerCase() == "marquee") {
        setAttr(node, "name", param["f_name"]);
        setAttr(node, "id", param["f_name"]);
				setAttr(node, "behavior",	param["f_behavior"]);
				setAttr(node, "direction", param["f_direction"]);
				setAttr(node, "width", param["f_width"]);
				setAttr(node, "height", param["f_height"]);
				setAttr(node, "bgColor", param["f_bgcolor"]);
				setAttr(node, "scrollAmount", param["f_scrollamount"]);
				setAttr(node, "scrollDelay", param["f_scrolldelay"]);
        node.innerHTML = param["f_text"];
		} else {
			var text = '<marquee name="' + param["f_name"] + '" ' +
          'id="' + param["f_name"] + '" ' + 
          'behavior="' + param["f_behavior"] + '" ' +
					'direction="' + param["f_direction"] + '" ' +
					'width="' + param["f_width"] + '" ' +
					'height="' + param["f_height"] + '" ' +
					'bgcolor="' + param["f_bgcolor"] + '" ' +
					'scrollamount="' + param["f_scrollamount"] + '" ' +
					'scrolldelay="' + param["f_scrolldelay"] + '">\n';
          alert(text);
			text = text + param["f_text"];
			text = text + '</marquee>';
			editor.insertHTML( text );
		}
	}, outparam);
};