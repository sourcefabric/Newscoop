
HorizontalRule._pluginInfo = {
	name          : "HorizontalRule",
	version       : "1.0",
	developer     : "Nelson Bright",
	developer_url : "http://www.brightworkweb.com/",
	c_owner       : "Nelson Bright",
	sponsor       : "BrightWork, Inc.",
	sponsor_url   : "http://www.brightworkweb.com/",
	license       : "htmlArea"
};

function HorizontalRule(editor) {
    this.editor = editor;

    var cfg = editor.config;
	var toolbar = cfg.toolbar;
	var self = this;
        
	cfg.registerButton({
		id       : "edithorizontalrule",
		tooltip  : this._lc("Insert/edit horizontal rule"),
	//	image    : editor.imgURL("ed_hr.gif", "HorizontalRule"),
		image    : [_editor_url + "images/ed_buttons_main.gif",6,0],
		textMode : false,
		action   : function(editor) {
						self.buttonPress(editor);
				   }
	});

	cfg.addToolbarElement("edithorizontalrule","inserthorizontalrule",0);
}

HorizontalRule.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'HorizontalRule');
};

HorizontalRule.prototype.buttonPress = function(editor) {
	this.editor = editor;
	this._editHorizontalRule();
};

HorizontalRule.prototype._editHorizontalRule = function(rule) {
	editor = this.editor;
	var sel = editor._getSelection();
	var range = editor._createRange(sel);
  var outparam = null;
  if (typeof rule == "undefined") {
    rule = editor.getParentElement();
    if (rule && !/^hr$/i.test(rule.tagName))
      rule = null;
  }
  if (rule) outparam = {
    f_size        : rule.size,
    f_width       : /%/.test(rule.width)? rule.width.substring(0,rule.width.length-1):rule.width,
    f_widthUnit   : /%/.test(rule.width)?"%":"px",
    f_color       : rule.color,
    f_noshade     : rule.noShade,
    f_align       : rule.align
  };

	editor._popupDialog("plugin://HorizontalRule/edit_horizontal_rule.html", function(param) {
		if (!param) {	// user pressed Cancel
			return false;
		}
		var hr = rule;
		if (!hr) {
		  var hrule = "<hr";
			for (var field in param) {
				var value = param[field];
				if(value == "") continue;
				switch (field) { 
				case "f_width" :
				if(param["f_widthUnit"]=="%")hrule += " width='" + value+"%'";
					else hrule += " width='" + value +"'"; break;
				case "f_size" :
				hrule += " size='" + value +"'"; break;
				case "f_align" :
				hrule += " align='" + value +"'"; break;
				case "f_color" :
				hrule += " color='" + value +"'"; break;
				case "f_noshade" :
				hrule += (value)? " noshade":""; break;
				}
			}
			hrule += ">";
			editor.insertHTML(hrule);
		} else {
			for (var field in param) {
			  var value = param[field];
			  switch (field) {
				  case "f_size"    : hr.size  = value; break;
				  case "f_width"   : hr.width   = (param["f_widthUnit"]=="%")?value+"%":value; break;
				  case "f_align"   : hr.align   = value; break;
				  case "f_color"   : hr.color   = value; break;
				  case "f_noshade" : hr.noShade = value; break;
			  }
			}
		}
	}, outparam);
};
	