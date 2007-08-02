function FullScreen(editor, args)
{
  this.editor = editor;
  editor._superclean_on = false;
  cfg = editor.config;

  cfg.registerButton
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
  cfg.addToolbarElement("fullscreen", "popupeditor", 0);
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
};

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
    if (window.innerHeight) // all except Explorer
    {
      x = window.innerWidth;
      y = window.innerHeight;
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

    e.sizeEditor(x + 'px',y + 'px',true,true);
    e._sizing = false;
  }

  function sizeItDown()
  {
    if(e._isFullScreen || e._sizing) return false;
    e._sizing = true;
    e.initSize();
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

    // Restore all ancestor positions
    var ancestor = this._htmlArea;
    while((ancestor = ancestor.parentNode) && ancestor.style)
    {
      ancestor.style.position = ancestor._xinha_fullScreenOldPosition;
      ancestor._xinha_fullScreenOldPosition = null;
    }

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


    // Make all ancestors position = static
    var ancestor = this._htmlArea;
    while((ancestor = ancestor.parentNode) && ancestor.style)
    {
      ancestor._xinha_fullScreenOldPosition = ancestor.style.position;
      ancestor.style.position = 'static';
    }

    // Maximize
    window.scroll(0,0);
    this._htmlArea.style.position = 'absolute';
    this._htmlArea.style.zIndex   = 999;
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
};