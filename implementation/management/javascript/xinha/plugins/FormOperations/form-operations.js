
  /*--------------------------------------:noTabs=true:tabSize=2:indentSize=2:--
    --  FormOperations Plugin
    --
    --  $HeadURL: http://gogo@svn.xinha.python-hosting.com/trunk/htmlarea.js $
    --  $LastChangedDate: 2005-05-25 09:30:03 +1200 (Wed, 25 May 2005) $
    --  $LastChangedRevision: 193 $
    --  $LastChangedBy: gogo $
    --------------------------------------------------------------------------*/

HTMLArea.Config.prototype.FormOperations =
{
  // format for fields where multiple values may be selected
  //    'php'          => FieldName[]
  //    'unmodified'   => FieldName
  'multiple_field_format': 'php',
  'allow_edit_form'      : false,
  'default_form_action'  : _editor_url + 'plugins/FormOperations/formmail.php',
  'default_form_html'    : HTMLArea._geturlcontent(_editor_url + 'plugins/FormOperations/default_form.html')
}

FormOperations._pluginInfo =
{
  name     : "FormOperations",
  version  : "1.0",
  developer: "James Sleeman",
  developer_url: "http://www.gogo.co.nz/",
  c_owner      : "Gogo Internet Services",
  license      : "htmlArea",
  sponsor      : "Gogo Internet Services",
  sponsor_url  : "http://www.gogo.co.nz/"
};

function FormOperations(editor)
{
  this.editor = editor;
  this.panel  = false;
  this.html   = false;
  this.ready  = false;
  this.activeElement = null;
  this._preparePanel();


  editor.config.pageStyleSheets.push(_editor_url + 'plugins/FormOperations/iframe.css');

  var toolbar =
  [
    'separator',
    'insert_form',
    'insert_text_field',
    'insert_textarea_field',
    'insert_select_field',
    'insert_cb_field',
    'insert_rb_field',
    'insert_button'
  ];

  this.editor.config.toolbar.push(toolbar);

  function pasteAndSelect(htmlTag)
  {
    var id = HTMLArea.uniq('fo');
    htmlTag = htmlTag.replace(/^<([^ \/>]+)/i, '<$1 id="'+id+'"');
    editor.insertHTML(htmlTag);
    var el = editor._doc.getElementById(id);
    el.setAttribute('id', '');
    editor.selectNodeContents(el);
    editor.updateToolbar();
    return el;
  }

  var buttonsImage = editor.imgURL('buttons.gif', 'FormOperations');

  this.editor.config.btnList.insert_form =
  [ "Insert a form.",
    [buttonsImage, 0, 0],
    false,
    function()
    {
      var form = null;
      if(editor.config.FormOperations.default_form_html)
      {
        form = pasteAndSelect(editor.config.FormOperations.default_form_html);
      }
      else
      {
        form = pasteAndSelect('<form>&nbsp;</form>');
      }

      if(editor.config.FormOperations.default_form_action && !form.action)
      {
        form.action = editor.config.FormOperations.default_form_action;
      }
    }
  ];

  this.editor.config.btnList.insert_text_field =
  [ "Insert a text, password or hidden field.",
    [buttonsImage, 1, 0],
    false,
    function()
    {
      pasteAndSelect('<input type="text" />');
    },
    'form'
  ];

  this.editor.config.btnList.insert_textarea_field =
  [ "Insert a multi-line text field.",
    [buttonsImage, 2, 0],
    false,
    function()
    {
      pasteAndSelect('<textarea> </textarea>');
    },
    'form'
  ];

  this.editor.config.btnList.insert_select_field =
  [ "Insert a select field.",
    [buttonsImage, 3, 0],
    false,
    function()
    {
      pasteAndSelect('<select> <option value="">Please Select...</option> </select>');
    },
    'form'
  ];

  this.editor.config.btnList.insert_cb_field =
  [ "Insert a check box.",
    [buttonsImage, 4, 0],
    false,
    function()
    {
      pasteAndSelect('<input type="checkbox" />');
    },
    'form'
  ];

  this.editor.config.btnList.insert_rb_field =
  [ "Insert a radio button.",
    [buttonsImage, 5, 0],
    false,
    function()
    {
      pasteAndSelect('<input type="radio" />');
    },
    'form'
  ];

  this.editor.config.btnList.insert_button =
  [ "Insert a submit/reset button.",
    [buttonsImage, 6, 0],
    false,
    function()
    {
      pasteAndSelect('<input type="submit" value="Send" />');
    },
    'form'
  ];
}

FormOperations.prototype.onGenerate = function()
{
  // Gecko does not register click events on select lists inside the iframe
  // so the only way of detecting that is to do an event on mouse move.
  if( HTMLArea.is_gecko)
  {
    var editor = this.editor;
    var doc    = this.editor._doc;
    HTMLArea._addEvents
    (doc, ["mousemove"],
     function (event) {
       return editor._editorEvent(event);
     });
  }
}

FormOperations.prototype._preparePanel = function ()
{
  var fo = this;
  if(this.html == false)
  {

    HTMLArea._getback(_editor_url + 'plugins/FormOperations/panel.html',
      function(txt)
      {
        fo.html = txt;
        fo._preparePanel();
      }
    );
    return false;
  }

  if(typeof HTMLArea.Dialog == 'undefined')
  {
    HTMLArea._loadback
      (_editor_url + 'inline-dialog.js', function() { fo._preparePanel(); } );
      return false;
  }

  if(typeof HTMLArea.PanelDialog == 'undefined')
  {
    HTMLArea._loadback
      (_editor_url + 'panel-dialog.js', function() { fo._preparePanel(); } );
      return false;
  }



  this.panel = new HTMLArea.PanelDialog(this.editor,'bottom',this.html,'FormOperations');
  this.panel.hide();
  this.ready = true;
}

FormOperations.prototype.onUpdateToolbar = function()
{
  if(!this.ready) return true;
  var activeElement = this.editor._activeElement(this.editor._getSelection());
  if(activeElement != null)
  {
    if(activeElement == this.activeElement) return true;

    var tag = activeElement.tagName.toLowerCase();
    this.panel.show();

    this.hideAll();
    if(tag === 'form')
    {
      if(this.editor.config.FormOperations.allow_edit_form)
      {
        this.showForm(activeElement);
      }
      else
      {
        this.panel.hide();
        this.activeElement = null;
        this.panel.hide();
        return true;
      }
    }
    else
    {

      if(this.editor.config.FormOperations.allow_edit_form && typeof activeElement.form != 'undefined' && activeElement.form)
      {
        this.showForm(activeElement.form);
      }

      switch(tag)
      {
        case 'form':
        {
          this.showForm(activeElement);
        }
        break;

        case 'input':
        {
          switch(activeElement.getAttribute('type').toLowerCase())
          {
            case 'text'    :
            case 'password':
            case 'hidden'  :
            {
              this.showText(activeElement);
            }
            break;

            case 'radio'   :
            case 'checkbox':
            {
              this.showCbRd(activeElement);
            }
            break;

            case 'submit'  :
            case 'reset'   :
            case 'button'  :
            {
              this.showButton(activeElement);
            }
            break;
          }
        }
        break;

        case 'textarea':
        {
          this.showTextarea(activeElement);
        }
        break;

        case 'select':
        {
          this.showSelect(activeElement);
        }
        break;

        default:
        {
          this.activeElement = null;
          this.panel.hide();
          return true;
        }
      }
    }
    //this.editor.scrollToElement(activeElement);
    this.activeElement = activeElement;
    return true;
  }
  else
  {
    this.activeElement = null;
    this.panel.hide();
    return true;
  }
}


FormOperations.prototype.hideAll = function()
{
  this.panel.getElementById('fs_form').style.display = 'none';
  this.panel.getElementById('fs_text').style.display = 'none';
  this.panel.getElementById('fs_textarea').style.display = 'none';
  this.panel.getElementById('fs_select').style.display = 'none';
  this.panel.getElementById('fs_cbrd').style.display = 'none';
  this.panel.getElementById('fs_button').style.display = 'none';
}

FormOperations.prototype.showForm = function(form)
{
  this.panel.getElementById('fs_form').style.display = '';
  var vals =
  {
    'action' : form.action,
    'method' : form.method.toUpperCase()
  }
  this.panel.setValues(vals);
  var f = form;
  this.panel.getElementById('action').onkeyup = function () { f.action = this.value; }
  this.panel.getElementById('method').onchange   = function () { f.method = this.options[this.selectedIndex].value; }
}

FormOperations.prototype.showText = function (input)
{
  this.panel.getElementById('fs_text').style.display = '';

  var vals =
  {
    'text_name'  : this.deformatName(input, input.name),
    'text_value' : input.value,
    'text_type'  : input.type.toLowerCase(),
    'text_width' : input.style.width ? parseFloat(input.style.width.replace(/[^0-9.]/, '')) : '',
    'text_width_units': input.style.width ? input.style.width.replace(/[0-9.]/, '').toLowerCase() : 'ex',
    'text_maxlength'  : input.maxlength   ? input.maxlength : ''
  }
  this.panel.setValues(vals);

  var i = input;
  var fo = this;

  this.panel.getElementById('text_name').onkeyup   = function () { i.name = fo.formatName(i, this.value); }
  this.panel.getElementById('text_value').onkeyup  = function () { i.value = this.value; }
  this.panel.getElementById('text_type').onchange   = function ()
    {
      if(!HTMLArea.is_ie)
      {
        i.type = this.options[this.selectedIndex].value;
      }
      else
      {
        // IE does not permit modifications of the type of a form field once it is set
        // We therefor have to destroy and recreate it.  I swear, if I ever
        // meet any of the Internet Explorer development team I'm gonna
        // kick them in the nuts!
        var tmpContainer = fo.editor._doc.createElement('div');
        if(!/type=/.test(i.outerHTML))
        {
          tmpContainer.innerHTML = i.outerHTML.replace(/<INPUT/i, '<input type="'+ this.options[this.selectedIndex].value + '"');
        }
        else
        {
          tmpContainer.innerHTML = i.outerHTML.replace(/type="?[a-z]+"?/i, 'type="' + this.options[this.selectedIndex].value + '"');
        }
        var newElement = HTMLArea.removeFromParent(tmpContainer.childNodes[0]);
        i.parentNode.insertBefore(newElement, i);
        HTMLArea.removeFromParent(i);
        input = i = newElement;
      }
    }

  var w  = this.panel.getElementById('text_width');
  var wu = this.panel.getElementById('text_width_units');

  this.panel.getElementById('text_width').onkeyup     =
  this.panel.getElementById('text_width_units').onchange =
    function ()
    {
      if(!w.value || isNaN(parseFloat(w.value)))
      {
        i.style.width = '';
      }
      i.style.width = parseFloat(w.value) + wu.options[wu.selectedIndex].value;
    }

  this.panel.getElementById('text_maxlength').onkeyup = function () { i.maxlength = this.value; }
}

FormOperations.prototype.showCbRd = function (input)
{
  this.panel.getElementById('fs_cbrd').style.display = '';
  var vals =
  {
    'cbrd_name'    : this.deformatName(input, input.name),
    'cbrd_value'   : input.value,
    'cbrd_checked' : input.checked ? 1 : 0,
    'cbrd_type'    : input.type.toLowerCase()
  }
  this.panel.setValues(vals);

  var i = input;
  var fo = this;
  this.panel.getElementById('cbrd_name').onkeyup   = function () { i.name = fo.formatName(i, this.value); }
  this.panel.getElementById('cbrd_value').onkeyup  = function () { i.value = this.value; }
  this.panel.getElementById('cbrd_type').onchange   = function ()
    {
      if(!HTMLArea.is_ie)
      {
        i.type = this.options[this.selectedIndex].value;
      }
      else
      {
        // IE does not permit modifications of the type of a form field once it is set
        // We therefor have to destroy and recreate it.  I swear, if I ever
        // meet any of the Internet Explorer development team I'm gonna
        // kick them in the nuts!
        var tmpContainer = fo.editor._doc.createElement('div');
        if(!/type=/.test(i.outerHTML))
        {
          tmpContainer.innerHTML = i.outerHTML.replace(/<INPUT/i, '<input type="'+ this.options[this.selectedIndex].value + '"');
        }
        else
        {
          tmpContainer.innerHTML = i.outerHTML.replace(/type="?[a-z]+"?/i, 'type="' + this.options[this.selectedIndex].value + '"');
        }
        var newElement = HTMLArea.removeFromParent(tmpContainer.childNodes[0]);
        i.parentNode.insertBefore(newElement, i);
        HTMLArea.removeFromParent(i);
        input = i = newElement;
      }
    }
  this.panel.getElementById('cbrd_checked').onclick   = function () { i.checked = this.checked; }
}

FormOperations.prototype.showButton = function (input)
{
  this.panel.getElementById('fs_button').style.display = '';
  var vals =
  {
    'button_name'    : this.deformatName(input, input.name),
    'button_value'   : input.value,
    'button_type'    : input.type.toLowerCase()
  }
  this.panel.setValues(vals);

  var i = input;
  var fo = this;
  this.panel.getElementById('button_name').onkeyup   = function () { i.name = fo.formatName(i, this.value); }
  this.panel.getElementById('button_value').onkeyup  = function () { i.value = this.value; }
  this.panel.getElementById('button_type').onchange   = function ()
    {
      if(!HTMLArea.is_ie)
      {
        i.type = this.options[this.selectedIndex].value;
      }
      else
      {
        // IE does not permit modifications of the type of a form field once it is set
        // We therefor have to destroy and recreate it.  I swear, if I ever
        // meet any of the Internet Explorer development team I'm gonna
        // kick them in the nuts!
        var tmpContainer = fo.editor._doc.createElement('div');
        if(!/type=/.test(i.outerHTML))
        {
          tmpContainer.innerHTML = i.outerHTML.replace(/<INPUT/i, '<input type="'+ this.options[this.selectedIndex].value + '"');
        }
        else
        {
          tmpContainer.innerHTML = i.outerHTML.replace(/type="?[a-z]+"?/i, 'type="' + this.options[this.selectedIndex].value + '"');
        }
        var newElement = HTMLArea.removeFromParent(tmpContainer.childNodes[0]);
        i.parentNode.insertBefore(newElement, i);
        HTMLArea.removeFromParent(i);
        input = i = newElement;
      }
    }
}

FormOperations.prototype.showTextarea = function (input)
{
  this.panel.getElementById('fs_textarea').style.display = '';
  var vals =
  {
    'textarea_name'  : this.deformatName(input, input.name),
    'textarea_value' : input.value,
    'textarea_width' : input.style.width ? parseFloat(input.style.width.replace(/[^0-9.]/, '')) : '',
    'textarea_width_units' : input.style.width ? input.style.width.replace(/[0-9.]/, '').toLowerCase() : 'ex',
    'textarea_height'      : input.style.height ? parseFloat(input.style.height.replace(/[^0-9.]/, '')) : '',
    'textarea_height_units': input.style.height ? input.style.height.replace(/[0-9.]/, '').toLowerCase() : 'ex'
  }

  this.panel.setValues(vals);

  var i = input;
  var fo = this;
  this.panel.getElementById('textarea_name').onkeyup   = function () { i.name = fo.formatName(i, this.value); }
  this.panel.getElementById('textarea_value').onkeyup  = function () { i.value = i.innerHTML = this.value; }

  var w  = this.panel.getElementById('textarea_width');
  var wu = this.panel.getElementById('textarea_width_units');

  this.panel.getElementById('textarea_width').onkeyup     =
  this.panel.getElementById('textarea_width_units').onchange =
    function ()
    {
      if(!w.value || isNaN(parseFloat(w.value)))
      {
        i.style.width = '';
      }
      i.style.width = parseFloat(w.value) + wu.options[wu.selectedIndex].value;
    }

  var h  = this.panel.getElementById('textarea_height');
  var hu = this.panel.getElementById('textarea_height_units');

  this.panel.getElementById('textarea_height').onkeyup     =
  this.panel.getElementById('textarea_height_units').onchange =
    function ()
    {
      if(!h.value || isNaN(parseFloat(h.value)))
      {
        i.style.height = '';
      }
      i.style.height = parseFloat(h.value) + hu.options[hu.selectedIndex].value;
    }

}

FormOperations.prototype.showSelect = function (input)
{
  this.panel.getElementById('fs_select').style.display = '';
  var vals =
  {
    'select_name'  : this.deformatName(input, input.name),
    'select_multiple' : input.multiple ? 1 : 0,
    'select_width' : input.style.width ? parseFloat(input.style.width.replace(/[^0-9.]/, '')) : '',
    'select_width_units' : input.style.width ? input.style.width.replace(/[0-9.]/, '').toLowerCase() : 'ex',
      'select_height'      : input.style.height ? parseFloat(input.style.height.replace(/[^0-9.]/, '')) : (input.size && input.size > 0 ? input.size : 1),
    'select_height_units': input.style.height ? input.style.height.replace(/[0-9.]/, '').toLowerCase() : 'items'
  }

  this.panel.setValues(vals);

  var i = input;
  var fo = this;
  this.panel.getElementById('select_name').onkeyup   = function () { i.name = fo.formatName(i, this.value); }
  this.panel.getElementById('select_multiple').onclick   = function () { i.multiple = this.checked; }

  var w  = this.panel.getElementById('select_width');
  var wu = this.panel.getElementById('select_width_units');

  this.panel.getElementById('select_width').onkeyup     =
  this.panel.getElementById('select_width_units').onchange =
    function ()
    {
      if(!w.value || isNaN(parseFloat(w.value)))
      {
        i.style.width = '';
      }
      i.style.width = parseFloat(w.value) + wu.options[wu.selectedIndex].value;
    }

  var h  = this.panel.getElementById('select_height');
  var hu = this.panel.getElementById('select_height_units');

  this.panel.getElementById('select_height').onkeyup     =
  this.panel.getElementById('select_height_units').onchange =
    function ()
    {
      if(!h.value || isNaN(parseFloat(h.value)))
      {
        i.style.height = '';
        return;
      }

      if(hu.selectedIndex == 0)
      {
        i.style.height = '';
        i.size = parseInt(h.value);
      }
      else
      {
        i.style.height = parseFloat(h.value) + hu.options[hu.selectedIndex].value;
      }
    }


  var fo_sel = this.panel.getElementById('select_options');
  this.arrayToOpts(this.optsToArray(input.options), fo_sel.options);

  this.panel.getElementById('add_option').onclick =
    function()
    {
      var txt = prompt("Enter the name for new option.");
      if(txt == null) return;
      var newOpt = new Option(txt);
      var opts   = fo.optsToArray(fo_sel.options);
      if(fo_sel.selectedIndex >= 0)
      {
        opts.splice(fo_sel.selectedIndex, 0, newOpt);
      }
      else
      {
        opts.push(newOpt);
      }
      fo.arrayToOpts(opts, input.options);
      fo.arrayToOpts(opts, fo_sel.options);
    }

  this.panel.getElementById('del_option').onclick =
    function()
    {
      var opts    = fo.optsToArray(fo_sel.options);
      var newOpts = [ ];
      for(var i = 0; i < opts.length; i++)
      {
        if(opts[i].selected) continue;
        newOpts.push(opts[i]);
      }
      fo.arrayToOpts(newOpts, input.options);
      fo.arrayToOpts(newOpts, fo_sel.options);
    }

  this.panel.getElementById('up_option').onclick =
    function()
    {
      if(!(fo_sel.selectedIndex > 0)) return;
      var opts    = fo.optsToArray(fo_sel.options);
      var move    = opts.splice(fo_sel.selectedIndex, 1).pop();
      opts.splice(fo_sel.selectedIndex - 1, 0, move);
      fo.arrayToOpts(opts, input.options);
      fo.arrayToOpts(opts, fo_sel.options);
    }

  this.panel.getElementById('down_option').onclick =
    function()
    {
      if(fo_sel.selectedIndex == fo_sel.options.length - 1) return;
      var opts    = fo.optsToArray(fo_sel.options);
      var move    = opts.splice(fo_sel.selectedIndex, 1).pop();
      opts.splice(fo_sel.selectedIndex+1, 0, move);
      fo.arrayToOpts(opts, input.options);
      fo.arrayToOpts(opts, fo_sel.options);
    }

  this.panel.getElementById('select_options').onchange =
    function()
    {
      fo.arrayToOpts(fo.optsToArray(fo_sel.options), input.options);
    }
}

FormOperations.prototype.optsToArray = function(o)
{
  var a = [ ];
  for(var i = 0; i < o.length; i++)
  {
    a.push(
      {
        'text'            : o[i].text,
        'value'           : o[i].value,
        'defaultSelected' : o[i].defaultSelected,
        'selected'        : o[i].selected
      }
    );
  }
  return a;
}

FormOperations.prototype.arrayToOpts = function(a, o)
{
  for(var i = o.length -1; i >= 0; i--)
  {
    o[i] = null;
  }

  for(var i = 0; i < a.length; i++)
  {
    o[i] = new Option(a[i].text, a[i].value, a[i].defaultSelected, a[i].selected);
  }
}

FormOperations.prototype.formatName = function(input, name)
{

  // Multiple name
  var mname = name;
  switch(this.editor.config.FormOperations.multiple_field_format)
  {
    case 'php':
    {
      mname += '[]';
    }
    break;

    case 'unmodified':
    {
      // Leave as is.
    }
    break;

    default:
    {
      throw("Unknown multiple field format " + this.editor.config.FormOperations.multiple_field_format);
    }
  }

  if
  (
       (input.tagName.toLowerCase() == 'select' && input.multiple)
    || (input.tagName.toLowerCase() == 'input'  && input.type.toLowerCase() == 'checkbox')
  )
  {
    name = mname;
  }

  return name;
}

FormOperations.prototype.deformatName = function(input, name)
{
  if(this.editor.config.FormOperations.multiple_field_format == 'php')
  {
    name = name.replace(/\[\]$/, '');
  }

  return name;
}

