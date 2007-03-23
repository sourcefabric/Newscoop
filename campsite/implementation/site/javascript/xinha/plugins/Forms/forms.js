// Form plugin for HTMLArea
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).


function Forms(editor) {
	this.editor = editor;
	var cfg = editor.config;
	var bl = Forms.btnList;
	var self = this;
	// register the toolbar buttons provided by this plugin
  var toolbar = ["linebreak"];
	for (var i = 0; i < bl.length; ++i) {
		var btn = bl[i];
		if (!btn) {
			toolbar.push("separator");
		} else {
			var id = btn[0];
			cfg.registerButton(id, HTMLArea._lc(btn[1]), editor.imgURL("ed_" + btn[0] + ".gif", "Forms"), false,
					   function(editor, id) {
						   // dispatch button press event
						   self.buttonPress(editor, id);
					   });
			toolbar.push(id);
		}
	}
	// add a new line in the toolbar
	cfg.toolbar.push(toolbar);
}

Forms._pluginInfo = {
  name          : "Forms",
	origin        : "version: 1.0, by Nelson Bright, BrightWork, Inc., http://www.brightworkweb.com",
	version       : "2.0",
	developer     : "Udo Schmal",
	developer_url : "",
	sponsor       : "L.N.Schaffrath NeueMedien",
	sponsor_url   : "http://www.schaffrath-neuemedien.de/",
	c_owner       : "Udo Schmal & Schaffrath-NeueMedien",
	license       : "htmlArea"
};

// the list of buttons added by this plugin
Forms.btnList = [
	// form properties button
  null,			// separator
	["form",        "Form"],
	null,			// separator
	// form elements
	["textarea",    "Textarea"],
	["select",      "Selection Field"],
	["checkbox",    "Checkbox"],
	["radio",       "Radio Button"],
	["text",        "Text Field"],
  ["password",    "Password Field"],
  ["file",        "File Field"],
	["button",      "Button"],
  ["submit",      "Submit Button"],
  ["reset",       "Reset Button"], 
	["image",       "Image Button"],
	["hidden",      "Hidden Field"],
  ["label",       "Label"],
  ["fieldset",    "Field Set"]
	];

Forms.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'Forms');
};

Forms.prototype.onGenerate = function() {
  var style_id = "Form-style"
  var style = this.editor._doc.getElementById(style_id);
  if (style == null) {
    style = this.editor._doc.createElement("link");
    style.id = style_id;
    style.rel = 'stylesheet';
    style.href = _editor_url + 'plugins/Forms/forms.css';
    this.editor._doc.getElementsByTagName("HEAD")[0].appendChild(style);
  }
};

Forms.prototype.buttonPress = function(editor,button_id, node) {
  function optionValues(text,value) {
		this.text = text;
		this.value = value;
	}
  var outparam = new Object();
  var type = button_id;
  var sel = editor._getSelection();
  var range = editor._createRange(sel);
  if (button_id=="form") { //Form
  	// see if selection is inside an existing 'form' tag 
	  var pe = editor.getParentElement();
	  var frm = null;
	  while (pe && (pe.nodeType == 1) && (pe.tagName.toLowerCase() != 'body')) {
		  if(pe.tagName.toLowerCase() == "form") {
			  frm = pe;
			  break;
		  } else 
        pe = pe.parentNode;
	  }
	  if (frm) { 
      outparam.f_name = frm.name;
      outparam.f_action = frm.action;
      outparam.f_method = frm.method;
      outparam.f_enctype = frm.enctype;
      outparam.f_target = frm.target;
    } else {;
      outparam.f_name = "";
  	  outparam.f_action = "";
	    outparam.f_method = "";
      outparam.f_enctype = "";
      outparam.f_target = "";
    }
  	editor._popupDialog("plugin://Forms/form", function(param) {
  		if (param) {
	  		if(frm) {
			    frm.name	 = param["f_name"];
          setAttr(frm, "action", param["f_action"]);
          setAttr(frm, "method", param["f_method"]);
          setAttr(frm, "enctype",param["f_enctype"]);
          setAttr(frm, "target", param["f_target"]);
		    } else {
          frm = '<form name="' + param["f_name"] + '"';
          if (param["f_action"] != "") frm += ' action="' + param["f_action"] + '"';
				  if (param["f_method"] != "") frm += ' method="' + param["f_method"] + '"';
          if (param["f_enctype"] != "") frm += ' enctype="' + param["f_enctype"] + '"';
          if (param["f_target"] != "") frm += ' target="' + param["f_target"] + '"';
          frm += '>';
			    editor.surroundHTML(frm, '&nbsp;</form>');
        }
      }
	  }, outparam);
    
  } else { // form element (checkbox, radio, text, password, textarea, select, button, submit, reset, image, hidden)
	  var tagName = "";
	  // see if selection is an form element
	  if (typeof node == "undefined") {
		  node = editor.getParentElement();
		  var tag = node.tagName.toLowerCase()
      if (node && (tag == "legend")) {
        node = node.parentElement;
        tag = node.tagName.toLowerCase();
      }
		  if (node && !(tag == "textarea" || tag == "select" || tag == "input" || tag == "label" || tag == "fieldset"))
			  node = null;
	  }

	  if(node) {
		  type = node.tagName.toLowerCase();
      outparam.f_name = node.name;
      tagName = node.tagName;
      if (type == "input") {
        outparam.f_type = node.type;
        type = node.type;
      }
      switch (type) {
        case "textarea":
    		  outparam.f_cols = node.cols;
				  outparam.f_rows = node.rows;
				  outparam.f_text = node.innerHTML;
          outparam.f_wrap = node.getAttribute("wrap");
          outparam.f_readOnly = node.getAttribute("readOnly");
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          outparam.f_accesskey = node.getAttribute("accesskey");
			    break;
        case "select":
			    outparam.f_size = parseInt(node.size);
				  outparam.f_multiple = node.getAttribute("multiple");
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          var a_options = new Array(); 
			    for (var i=0; i<=node.options.length-1; i++) {
            a_options[i] = new optionValues(node.options[i].text, node.options[i].value);
			    }
			    outparam.f_options = a_options;
				  break;
			  case "text":
			  case "password":
				  outparam.f_value = node.value;
					outparam.f_size = node.size;
					outparam.f_maxLength = node.maxLength;
          outparam.f_readOnly = node.getAttribute("readOnly");
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          outparam.f_accesskey = node.getAttribute("accesskey");
					break;
				case "hidden":
          outparam.f_value = node.value;
          break;
				case "submit":
				case "reset":
				  outparam.f_value = node.value;
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          outparam.f_accesskey = node.getAttribute("accesskey");
				  break;
				case "checkbox":
				case "radio": 
				  outparam.f_value = node.value;
		  		outparam.f_checked = node.checked;
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          outparam.f_accesskey = node.getAttribute("accesskey");
			   	break;
				case "button": 
				  outparam.f_value = node.value;
					outparam.f_onclick = node.getAttribute("onclick");
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          outparam.f_accesskey = node.getAttribute("accesskey");
				  break;
				case "image":
				  outparam.f_value = node.value;
					outparam.f_src = node.src;
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          outparam.f_accesskey = node.getAttribute("accesskey");
				  break;
        case "file":
          outparam.f_disabled = node.getAttribute("disabled");
          outparam.f_tabindex = node.getAttribute("tabindex");
          outparam.f_accesskey = node.getAttribute("accesskey");
				  break;
        case "label":
          outparam.f_text = node.innerHTML;
          outparam.f_for = node.getAttribute("for");
          outparam.f_accesskey = node.getAttribute("accesskey");
          break;
        case "fieldset":
          if(node.firstChild.tagName.toLowerCase()=="legend")
            outparam.f_text = node.firstChild.innerHTML;
          else
            outparam.f_text = "";
          break;
      }    
		} else {
      outparam.f_name = "";
      switch (button_id) {
        case "textarea":
        case "select":
        case "label":
        case "fieldset":
          tagName = button_id;
          break;
        default:
          tagName = "input";
          outparam.f_type = button_id;
          break;
      }
      outparam.f_options = "";
      outparam.f_cols = "20";
		  outparam.f_rows = "4";
		  outparam.f_multiple = "false";
     	outparam.f_value = "";
		  outparam.f_size = "";
		  outparam.f_maxLength = "";
		  outparam.f_checked = "";
		  outparam.f_src = "";
		  outparam.f_onclick = "";
      outparam.f_wrap = "";
      outparam.f_readOnly = "false";
      outparam.f_disabled = "false";
      outparam.f_tabindex = "";
      outparam.f_accesskey = "";
      outparam.f_for = "";
      outparam.f_text = "";
      outparam.f_legend = "";
	  }
  	editor._popupDialog("plugin://Forms/" + tagName + ".html", function(param) {
	  	if (param) {
        if(param["f_cols"])
          if (isNaN(parseInt(param["f_cols"],10)) || parseInt(param["f_cols"],10) <= 0)
            param["f_cols"] = "";
        if(param["f_rows"])
          if(isNaN(parseInt(param["f_rows"],10)) || parseInt(param["f_rows"],10) <= 0)
            param["f_rows"] = "";
        if(param["f_size"])
          if(isNaN(parseInt(param["f_size"],10)) || parseInt(param["f_size"],10) <= 0)
            param["f_size"] = "";
        if(param["f_maxlength"])
          if(isNaN(parseInt(param["f_maxLength"],10)) || parseInt(param["f_maxLength"],10) <= 0)
            param["f_maxLength"] = "";
		  	if(node) {
          //prepare existing Element
          for (field in param) {
            alert(field.substring(2,20) + '=' + param[field]);
					  if ((field=="f_text") || (field=="f_options") || (field=="f_onclick") || (field=="f_checked"))continue;
            if (param[field] != "")
              node.setAttribute(field.substring(2,20), param[field]);
            else
              node.removeAttribute(field.substring(2,20));
				  }
			    if (type == "textarea") {
            node.innerHTML = param["f_text"];
			    } else if(type == "select") {
				    node.options.length = 0;
				    var optionsList =  param["f_options"];
				    for (i=0; i<= optionsList.length-1; i++) {
					    node.options[i] = new Option(optionsList[i].text, optionsList[i].value)
				    }
			    } else if(type == "label") {
				    node.innerHTML = param["f_text"];
          } else if(type == "fieldset") {
            if(outparam.f_text != "") {
				      if(node.firstChild.tagName.toLowerCase()=="legend")
                node.firstChild.innerHTML = param["f_text"];
            } else {}// not implemented jet
          } else if((type == "checkbox") || (type == "radio")) { //input
              if(param["f_checked"]!="")
						    node.checked = true;
              else
                node.checked = false;
          } else {
            if(param["f_onclick"]){
				      node.onclick = "";
					    if(param["f_onclick"]!="") 
						    node.onclick = param["f_onclick"];
            }
			    }
        } else {
          //create Element
          var text = "";
          for (field in param) {
					  if (!param[field]) continue;
            if ((param[field]=="") || (field=="f_text")|| (field=="f_options"))continue;
            text += " " + field.substring(2,20) + '="' + param[field] + '"';
				  }

			    if(type == "textarea") {
				    text = '<textarea' + text + '>' + param["f_text"] + '</textarea>';
			    } else if(type == "select") {
				    text = '<select' + text + '>';
				    var optionsList =  param["f_options"];
				    for (i=0; i<= optionsList.length-1; i++) {
					    text += '<option value="'+optionsList[i].value+'">'+optionsList[i].text+'</option>';
				    }
				    text += '</select>';
          } else if(type == "label") {
            text = '<label' + text + '>' + param["f_text"] + '</label>';
          } else if(type == "fieldset") {
            text = '<fieldset' + text + '>';
            if (param["f_legend"] != "") text += '<legend>' + param["f_text"] + '</legend>';
				    text += '</fieldset>';
			    } else {
				    text = '<input type="'+type+'"' + text + '>';
			    }
	        editor.insertHTML(text);
        }
      }
	  }, outparam);
  }  
};