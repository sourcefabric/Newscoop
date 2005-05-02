function FullScreen(editor, args)
{
  this.editor = editor;
  editor._superclean_on = false;
  cfg = editor.config;

  editor.config.registerButton
  ( 'fullscreen',
    this._lc("Maximize/Minimize Editor"),
    [_editor_url + cfg.imgURL + 'ed_buttons_main.gif',8,0], true,
      function(e, objname, obj)
      {
        e._fullScreen();
        if(e._isFullScreen)
        {
          obj.swapImage([_editor_url + cfg.imgURL + 'ed_buttons_main.gif',9,0]);
        }
        else
        {
          obj.swapImage([_editor_url + cfg.imgURL + 'ed_buttons_main.gif',8,0]);
        }
      }
  );

  // See if we can find 'popupeditor' and replace it with fullscreen
  var t = editor.config.toolbar;
  var done = false;
  for(var i = 0; i < t.length && !done; i++)
  {
    for(var x = 0; x < t[i].length && !done; x++)
    {
      if(t[i][x] == 'popupeditor')
      {
        t[i][x] = 'fullscreen';
        done = true;
      }
    }
  }

  if(!done)
  {
    t[0].push('fullscreen');
  }
}

FullScreen._pluginInfo =
{
  name     : "FullScreen",
  version  : "1.0",
  developer: "James Sleeman",
  developer_url: "http://www.gogo.co.nz/",
  c_owner      : "Gogo Internet Services",
  license      : "htmlArea",
  sponsor      : "Gogo Internet Services",
  sponsor_url  : "http://www.gogo.co.nz/"
};

FullScreen.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'FullScreen');
}

/** fullScreen makes an editor take up the full window space (and resizes when the browser is resized)
 *  the principle is the same as the "popupwindow" functionality in the original htmlArea, except
 *  this one doesn't popup a window (it just uses to positioning hackery) so it's much more reliable
 *  and much faster to switch between
 */

HTMLArea.prototype._fullScreen = function()
{
  var e = this;
  function sizeItUp()
  {
    if(!e._isFullScreen || e._sizing) return false;
    e._sizing = true;
    // Width & Height of window
    var x,y;
    if (self.innerHeight) // all except Explorer
    {
      x = self.innerWidth;
      y = self.innerHeight;
    }
    else if (document.documentElement && document.documentElement.clientHeight)
      // Explorer 6 Strict Mode
    {
      x = document.documentElement.clientWidth;
      y = document.documentElement.clientHeight;
    }
    else if (document.body) // other Explorers
    {
      x = document.body.clientWidth;
      y = document.body.clientHeight;
    }

    if(!e._revertFullScreen) e._revertFullScreen = e.getInnerSize();

    width = x;
    height = y - e._toolbar.offsetHeight - (e._statusBar ? e._statusBar.offsetHeight : 0);
    e.setInnerSize(width,height);

    // IE in standards mode needs us to set the width of the tool & status bar,
    // I have NO idea why
    if(HTMLArea.is_ie && document.documentElement && document.documentElement.clientHeight)
    {
      e._toolbar.style.width = (width - 12) + 'px';
      if(e._statusBar)
      {
        e._statusBar.style.width = (width - 12) + 'px';
      }
    }

    e._sizing = false;
  }

  function sizeItDown()
  {
    if(e._isFullScreen || e._sizing) return false;
    e._sizing = true;
    e.setInnerSize(e._revertFullScreen.width, e._revertFullScreen.height);
    if(HTMLArea.is_ie && document.documentElement && document.documentElement.clientHeight)
    {
      e._toolbar.style.width = '';
      if(e._statusBar)
      {
        e._statusBar.style.width = '';
      }
    }
    e._revertFullScreen = null;
    e._sizing = false;
  }

  /** It's not possible to reliably get scroll events, particularly when we are hiding the scrollbars
   *   so we just reset the scroll ever so often while in fullscreen mode
   */
  function resetScroll()
  {
    if(e._isFullScreen)
    {
      window.scroll(0,0);
      window.setTimeout(resetScroll,150);
    }
  }

  if(typeof this._isFullScreen == 'undefined')
  {
    this._isFullScreen = false;
    if(e.target != e._iframe)
    {
      HTMLArea._addEvent(window, 'resize', sizeItUp);
    }
  }

  // Gecko has a bug where if you change position/display on a
  // designMode iframe that designMode dies.
  if(HTMLArea.is_gecko)
  {
    this.deactivateEditor();
  }

  if(this._isFullScreen)
  {
    // Unmaximize
    this._htmlArea.style.position = '';
    try
    {
      if(HTMLArea.is_ie)
      {
        var bod = document.getElementsByTagName('html');
      }
      else
      {
        var bod = document.getElementsByTagName('body');
      }
      bod[0].style.overflow='';
    }
    catch(e)
    {
      // Nutthin
    }
    this._isFullScreen = false;
    sizeItDown();

    window.scroll(this._unScroll.x, this._unScroll.y);
  }
  else
  {

    // Get the current Scroll Positions
    this._unScroll =
    {
     x:(window.pageXOffset)?(window.pageXOffset):(document.documentElement)?document.documentElement.scrollLeft:document.body.scrollLeft,
     y:(window.pageYOffset)?(window.pageYOffset):(document.documentElement)?document.documentElement.scrollTop:document.body.scrollTop
    };


    // Maximize
    window.scroll
    this._htmlArea.style.position = 'absolute';
    this._htmlArea.style.zIndex   = 9999;
    this._htmlArea.style.left     = 0;
    this._htmlArea.style.top      = 0;
    this._isFullScreen = true;
    resetScroll();

    try
    {
      if(HTMLArea.is_ie)
      {
        var bod = document.getElementsByTagName('html');
      }
      else
      {
        var bod = document.getElementsByTagName('body');
      }
      bod[0].style.overflow='hidden';
    }
    catch(e)
    {
      // Nutthin
    }

    sizeItUp();
  }

  if(HTMLArea.is_gecko)
  {
    this.activateEditor();
  }
  this.focusEditor();
}