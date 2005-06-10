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
};

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
	["button",      "Button"],
  ["submit",      "Submit Button"],
  ["reset",       "Reset Button"], 
	["image",       "Image Button"],
	["hidden",      "Hidden Field"]
	];

Forms.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'Forms');
}

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
}

Forms.prototype.buttonPress = function(editor,button_id, node) {
  function optionValues(text,value) {
		this.text = text;
		this.value = value;
	}
  function setAttr(el, attr, value) {
    if (value != "")
      el.setAttribute(attr, value);
    else
      el.removeAttribute(attr);
  }
  var outparam = new Object();
  var type = button_id;
  if (button_id=="form") { //Form
  	var sel = editor._getSelection();
	  var range = editor._createRange(sel);
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
	  var sel = editor._getSelection();
	  var range = editor._createRange(sel);
	  //see if selection is an form element
	  if (typeof node == "undefined") {
		  node = editor.getParentElement();
		  var tag = node.tagName.toLowerCase()
		  if (node && !(tag == "textarea" || tag == "select" || tag == "input"))
			  node = null;
	  }

	  if(node) {
		  type = node.tagName.toLowerCase();
      outparam.f_name = node.name;
      outparam.f_tagName = node.tagName;      
      if (type == "input") {
        outparam.f_type = node.type;
        type = node.type;
      }
      switch (type) {
        case "textarea":
    		  outparam.f_cols = node.cols;
				  outparam.f_rows = node.rows;
				  outparam.f_value = node.innerHTML;
			    break;
        case "select":
			    outparam.f_size = parseInt(node.size);
				  outparam.f_multiple = node.multiple;
          var a_options = new Array(); 
			    for (var i=0; i<=node.options.length-1; i++) {
            a_options[i] = new optionValues(node.options[i].text, node.options[i].value);
			    };
			    outparam.f_options = a_options;
				  break;
			  case "text":
			  case "password":
				  outparam.f_value = node.value;
					outparam.f_size = node.size;
					outparam.f_maxlength = node.maxLength;
					break;
				case "hidden":
				case "submit":
				case "reset":
				  outparam.f_value = node.value;
				  break;
				case "checkbox":
				case "radio": 
				  outparam.f_value = node.value;
		  		outparam.f_checked = node.checked
			   	break;
				case "button": 
				  outparam.f_value = node.value;
					outparam.f_onclick = node.getAttribute("onclick");
				  break;
				case "image":
				  outparam.f_value = node.value;
					outparam.f_src = node.src;
				  break;
      }    
		} else {
      outparam.f_name = "";
      outparam.f_tagName = "";
      switch (button_id) {
        case "textarea":
        case "select":
          outparam.f_tagName = button_id
          break;
        default:
          outparam.f_tagName = "input";
          outparam.f_type = button_id;
          break;
      }
      outparam.f_options = "";
      outparam.f_cols = "20";
		  outparam.f_rows = "4";
		  outparam.f_multiple = "false";
     	outparam.f_value = "";
		  outparam.f_size = "";
		  outparam.f_maxlength = "";
		  outparam.f_checked = "";
		  outparam.f_src = "";
		  outparam.f_onclick = "";
	  };
  	editor._popupDialog("plugin://Forms/" + outparam.f_tagName + ".html", function(param) {
	  	if (param) {
		  	if(node) {
			    node.name = param["f_name"];
			    if (type == "textarea") {
				    if (isNaN(parseInt(param["f_cols"],10)) || parseInt(param["f_cols"],10) <= 0)
					    node.removeAttribute("cols");
					  else 
              node.setAttribute("cols", param["f_cols"]);
				    if(isNaN(parseInt(param["f_rows"],10)) || parseInt(param["f_rows"],10) <= 0)
					    node.removeAttribute("rows");
				  	else 
              node.setAttribute("rows", param["f_rows"]);
				    node.value = param["f_value"]; //for ta in editor
				    node.innerHTML = param["f_value"]; //for ta on web page
			    } else if(type == "select") {
				    node.requiredfield = param["f_requiredfield"];
				    if(isNaN(parseInt(param["f_size"],10)) || parseInt(param["f_size"],10) <= 0)
					    node.removeAttribute("size");
					  else 
              node.size = param["f_size"];
				    node.multiple = param["f_multiple"];
				    node.options.length = 0;
				    var optionsList =  param["f_options"];
				    for (i=0; i<= optionsList.length-1; i++) {
					    node.options[i] = new Option(optionsList[i].text, optionsList[i].value)
				    }
			    } else {  //type == "input"
				    for (field in param) {
					    switch (field) {
						    case "f_type": node.type = param["f_type"]; break;
						    case "f_value": node.setAttribute("value", param["f_value"]); break;
						    case "f_size": 
							    if(isNaN(parseInt(param["f_size"],10)) || parseInt(param["f_size"],10) <= 0)
								    node.removeAttribute("size");
							    else 
                    node.setAttribute("size", param["f_size"]); break;
						    case "f_maxlength":
							    if(isNaN(parseInt(param["f_maxlength"],10)) || parseInt(param["f_maxlength"],10) <= 0) 
								    node.removeAttribute("maxLength");
							    else 
                    node.setAttribute("maxLength", param["f_maxlength"]); break;
						    case "f_checked":
							    if(param["f_checked"]==true) 
                    node.setAttribute("checked",true);
								  else {
                    node.setAttribute("checked",false);  
								  node.removeAttribute("checked");} break;
						    case "f_src": node.setAttribute("src", param["f_src"]); break;
						    case "f_onclick":  
                  node.removeAttribute("onclick"); 
                  node.onclick = "";
							    if(param["f_onclick"]!="") {
								    node.setAttribute("onclick",param["f_onclick"]);
								    node.onclick = param["f_onclick"];
							    } break;
					    }
				    }
			    }
        } else {
			    if(type == "textarea") {
				    text = '<textarea name="' + param["f_name"] + '" ' +
				                    ' cols="' + param["f_cols"] + '"' +
  				                  ' rows="' + param["f_rows"] +'">' +
	  			          param["f_value"] +
		  		          '</textarea>';
			    } else if(type == "select") {
				    text = '<select name="'+param["f_name"]+'"';
				    if(param["f_size"]) text += ' size="'+parseInt(param["f_size"],10)+'"';
				    if(param["f_multiple"]) text += ' multiple';
				    text += '>';
				    var optionsList =  param["f_options"];
				    for (i=0; i<= optionsList.length-1; i++) {
					    text += '<option value="'+optionsList[i].value+'">'+optionsList[i].text+'</option>';
				    }
				    text += '</select>';
			    } else {
				    text = '<input type="'+type+'"' +
				           ' name="'+param["f_name"]+'"';
				    for (field in param) {
					    var value = param[field];
					    if (!value) continue;
					    switch (field) {
						    case "f_value": text += ' value="'+param["f_value"]+'"'; break;
						    case "f_size": text += ' size="'+parseInt(param["f_size"],10)+'"'; break;
						    case "f_maxlength": text += ' maxlength="'+parseInt(param["f_maxlength"],10)+'"'; break;
						    case "f_checked": text += ' checked'; break;
						    case "f_src": text += ' src="'+param["f_src"]+'"'; break;
						    case "f_onclick": text += ' onClick="'+param["f_onclick"]+'"'; break;
					    }
				    }
				    text += '>';
			    }
	        editor.insertHTML(text);
        }
      }
	  }, outparam);
  }  
};
