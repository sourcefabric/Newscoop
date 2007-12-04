// Character Map plugin for HTMLArea
// Original Author - Bernhard Pfeifer novocaine@gmx.net
HTMLArea.loadStyle( 'CharacterMap.css', 'CharacterMap' );

function CharacterMap( editor )
{
  this.editor = editor;
  var cfg = editor.config;
  var self = this;
  cfg.registerButton(
    {
      id       : 'insertcharacter',
      tooltip  : HTMLArea._lc( 'Insert special character', 'CharacterMap' ),
      image    : editor.imgURL( 'ed_charmap.gif', 'CharacterMap' ),
      textMode : false,
      action   : function( editor ) { self.buttonPress( editor ); }
    }
  );
  cfg.addToolbarElement('insertcharacter', 'createlink', -2);

  if ( cfg.CharacterMap.mode == 'panel' )
  {
    editor._CharacterMap = editor.addPanel( 'right' );
    HTMLArea._addClass( editor._CharacterMap, 'CharacterMap' );

    editor.notifyOn( 'modechange',
      function( e, args )
      {
        if ( args.mode == 'text' ) editor.hidePanel( editor._CharacterMap );
      }
    );

    var entites =
    [
      '&Yuml;', '&scaron;', '&#064;', '&quot;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;',
      '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&macr;', '&deg;', '&plusmn;', '&sup2;',
      '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;',
      '&frac12;', '&frac34;', '&iquest;', '&times;', '&Oslash;', '&divide;', '&oslash;', '&fnof;', '&circ;',
      '&tilde;', '&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;',
      '&dagger;', '&Dagger;', '&bull;', '&hellip;', '&permil;', '&lsaquo;', '&rsaquo;', '&euro;', '&trade;',
      '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;',
      '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;',
      '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&reg;', '&times;', '&Ugrave;', '&Uacute;',
      '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;',
      '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;',
      '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;',
      '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;',
      '&yuml;', '&OElig;', '&oelig;', '&Scaron;'
    ];

    for ( var i=0; i<entites.length; i++ )
      this.addEntity( entites[i], i );

    editor.hidePanel( editor._CharacterMap );
  }
}

// configuration mode : panel or popup
HTMLArea.Config.prototype.CharacterMap =
{
  'mode': 'popup' // configuration mode : panel or popup
};

CharacterMap._pluginInfo =
{
  name          : "CharacterMap",
  version       : "2.0",
  developer     : "Laurent Vilday",
  developer_url : "http://www.mokhet.com/",
  c_owner       : "Xinha community",
  sponsor       : "",
  sponsor_url   : "",
  license       : "Creative Commons Attribution-ShareAlike License"
};

CharacterMap._isActive = false;

CharacterMap.prototype.buttonPress = function( editor )
{
  var cfg = editor.config;
  if ( cfg.CharacterMap.mode == 'panel' )
  {
    if ( this._isActive )
    {
      this._isActive = false;
      editor.hidePanel( editor._CharacterMap );
    }
    else
    {
      this._isActive = true;
      editor.showPanel( editor._CharacterMap );
    }
  }
  else
  {
    editor._popupDialog( "plugin://CharacterMap/select_character", function( entity )
    {
      if ( !entity ) return false;
      if ( HTMLArea.is_ie ) editor.focusEditor();
      editor.insertHTML( entity );
    }, null);
  }
};

CharacterMap.prototype.addEntity = function ( entite, pos )
{
  var editor = this.editor;
  var self = this;
  var a = document.createElement( 'a' );
  HTMLArea._addClass( a, 'entity' );
  a.innerHTML = entite;
  a.href = 'javascript:void(0)';
  HTMLArea._addClass(a, (pos%2)? 'light':'dark');
  a.onclick = function()
  {
    if (HTMLArea.is_ie) editor.focusEditor();
    editor.insertHTML( entite );
    self._isActive = false;
    editor.hidePanel( editor._CharacterMap );
    return false;
  };
  editor._CharacterMap.appendChild( a );
};
