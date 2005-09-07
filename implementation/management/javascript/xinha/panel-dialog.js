
HTMLArea.PanelDialog = function(editor, side, html, localizer)
{
  this.id    = { };
  this.r_id  = { }; // reverse lookup id
  this.editor   = editor;
  this.document = document;
  this.rootElem = editor.addPanel(side);

  var dialog = this;
  if(typeof localizer == 'function')
  {
    this._lc = localizer;
  }
  else if(localizer)
  {
    this._lc = function(string)
    {
      return HTMLArea._lc(string,localizer);
    }
  }
  else
  {
    this._lc = function(string)
    {
      return string;
    }
  }

  html = html.replace(/\[([a-z0-9_]+)\]/ig,
                      function(fullString, id)
                      {
                        if(typeof dialog.id[id] == 'undefined')
                        {
                          dialog.id[id] = HTMLArea.uniq('Dialog');
                          dialog.r_id[dialog.id[id]] = id;
                        }
                        return dialog.id[id];
                      }
             ).replace(/<l10n>(.*?)<\/l10n>/ig,
                       function(fullString,translate)
                       {
                         return dialog._lc(translate) ;
                       }
             ).replace(/="_\((.*?)\)"/g,
                       function(fullString, translate)
                       {
                         return '="' + dialog._lc(translate) + '"';
                       }
             );

  this.rootElem.innerHTML = html;
}

HTMLArea.PanelDialog.prototype.show = function(values)
{
  this.editor.showPanel(this.rootElem);
}

HTMLArea.PanelDialog.prototype.hide = function()
{
  this.editor.hidePanel(this.rootElem);
  return this.getValues();
}

HTMLArea.PanelDialog.prototype.onresize   = HTMLArea.Dialog.prototype.onresize;

HTMLArea.PanelDialog.prototype.toggle     = HTMLArea.Dialog.prototype.toggle;

HTMLArea.PanelDialog.prototype.setValues  = HTMLArea.Dialog.prototype.setValues;

HTMLArea.PanelDialog.prototype.getValues  = HTMLArea.Dialog.prototype.getValues;

HTMLArea.PanelDialog.prototype.getElementById    = HTMLArea.Dialog.prototype.getElementById;

HTMLArea.PanelDialog.prototype.getElementsByName = HTMLArea.Dialog.prototype.getElementsByName;
