// ListType Plugin for Xinha
// Toolbar Implementation by Mihai Bazon, http://dynarch.com/mishoo/
HTMLArea.loadStyle( 'ListType.css', 'ListType' );

function ListType( editor )
{
  this.editor = editor;
  var cfg = editor.config;
  var self = this;

  if ( cfg.ListType.mode == 'toolbar' )
  {
  var options = {};
    options[HTMLArea._lc( "Decimal numbers", "ListType" )] = "decimal";
    options[HTMLArea._lc( "Lower roman numbers", "ListType" )] = "lower-roman";
    options[HTMLArea._lc( "Upper roman numbers", "ListType" )] = "upper-roman";
    options[HTMLArea._lc( "Lower latin letters", "ListType" )] = "lower-alpha";
    options[HTMLArea._lc( "Upper latin letters", "ListType" )] = "upper-alpha";
    if (!HTMLArea.is_ie)
      // IE doesn't support this property; even worse, it complains
      // with a gross error message when we tried to select it,
      // therefore let's hide it from the damn "browser".
      options[HTMLArea._lc( "Lower greek letters", "ListType" )] = "lower-greek";
    var obj =
    {
      id            : "listtype",
      tooltip       : HTMLArea._lc( "Choose list style type (for ordered lists)", "ListType" ),
      options       : options,
      action        : function( editor ) { self.onSelect( editor, this ); },
      refresh       : function( editor ) { self.updateValue( editor, this ); },
      context       : "ol"
    };
    cfg.registerDropdown( obj );
    cfg.addToolbarElement( "listtype", ["insertorderedlist","orderedlist"], 1 );
  }
  else
  {
    editor._ListType = editor.addPanel( 'right' );
    HTMLArea.freeLater( editor, '_ListType' );
    HTMLArea.addClass( editor._ListType, 'ListType' );
    // hurm, ok it's pretty to use the background color for the whole panel,
    // but should not it be set by default when creating the panel ?
    HTMLArea.addClass( editor._ListType.parentNode, 'dialog' );

    editor.notifyOn( 'modechange',
      function(e,args)
      {
        if ( args.mode == 'text' ) editor.hidePanel( editor._ListType );
      }
    );

    var elts_ul = ['disc', 'circle', 'square', 'none'];
    var elts_ol = ['decimal', 'lower-alpha', 'upper-alpha', 'lower-roman', 'upper-roman', 'none'];
    var divglobal = document.createElement( 'div' );
    divglobal.style.height = '90px';
    var div = document.createElement( 'div' );
    div.id = 'LTdivUL';
    div.style.display = 'none';
    for ( var i=0; i<elts_ul.length; i++ )
    {
      div.appendChild( this.createImage( elts_ul[i] ) );
    }
    divglobal.appendChild( div );
    var div = document.createElement( 'div' );
    div.id = 'LTdivOL';
    div.style.display = 'none';
    for ( var i=0; i<elts_ol.length; i++ )
    {
      div.appendChild( this.createImage( elts_ol[i] ) );
    }
    divglobal.appendChild( div );

    editor._ListType.appendChild( divglobal );

    editor.hidePanel( editor._ListType );
  }
}

HTMLArea.Config.prototype.ListType =
{
  'mode': 'toolbar' // configuration mode : toolbar or panel
};

ListType._pluginInfo =
{
  name          : "ListType",
  version       : "2.1",
  developer     : "Laurent Vilday",
  developer_url : "http://www.mokhet.com/",
  c_owner       : "Xinha community",
  sponsor       : "",
  sponsor_url   : "",
  license       : "Creative Commons Attribution-ShareAlike License"
};

ListType.prototype.onSelect = function( editor, combo )
{
  var tbobj = editor._toolbarObjects[ combo.id ].element;
  var parent = editor.getParentElement();
  while (!/^ol$/i.test( parent.tagName ))
    parent = parent.parentNode;
  parent.style.listStyleType = tbobj.value;
};

ListType.prototype.updateValue = function( editor, combo )
{
  var tbobj = editor._toolbarObjects[ combo.id ].element;
  var parent = editor.getParentElement();
  while ( parent && !/^ol$/i.test( parent.tagName ) )
    parent = parent.parentNode;
  if (!parent)
  {
    tbobj.selectedIndex = 0;
    return;
  }
  var type = parent.style.listStyleType;
  if (!type)
  {
    tbobj.selectedIndex = 0;
  }
  else
  {
    for ( var i = tbobj.firstChild; i; i = i.nextSibling )
    {
      i.selected = (type.indexOf(i.value) != -1);
    }
  }
};

ListType.prototype.onUpdateToolbar = function()
{
  if ( this.editor.config.ListType.mode == 'toolbar' ) return ;
  var parent = this.editor.getParentElement();
  while ( parent && !/^[o|u]l$/i.test( parent.tagName ) )
    parent = parent.parentNode;
  if (parent && /^[o|u]l$/i.test( parent.tagName ) )
  {
    this.showPanel( parent );
  }
  else if (this.editor._ListType.style.display != 'none')
  {
    this.editor.hidePanel( this.editor._ListType );
  }
};

ListType.prototype.createImage = function( listStyleType )
{
  var self = this;
  var editor = this.editor;
  var a = document.createElement( 'a' );
  a.href = 'javascript:void(0)';
  HTMLArea._addClass( a, listStyleType );
  HTMLArea._addEvent( a, "click", function ()
    {
      var parent = editor._ListType.currentListTypeParent;
      parent.style.listStyleType = listStyleType;
      self.showActive( parent );
      return false;
    }
  );
  return a;
};

ListType.prototype.showActive = function( parent )
{
  var activeDiv = document.getElementById( ( parent.tagName.toLowerCase() == 'ul' )? 'LTdivUL':'LTdivOL' );
  document.getElementById( 'LTdivUL' ).style.display = 'none';
  document.getElementById( 'LTdivOL' ).style.display = 'none';
  activeDiv.style.display = 'block';
  var defaultType = parent.style.listStyleType;
  if ( '' == defaultType ) defaultType = ( parent.tagName.toLowerCase() == 'ul' )? 'disc':'decimal';
  for ( var i=0; i<activeDiv.childNodes.length; i++ )
  {
    var elt = activeDiv.childNodes[i];
    if ( HTMLArea._hasClass( elt, defaultType ) )
    {
      HTMLArea._addClass( elt, 'active' );
    }
    else
    {
      HTMLArea._removeClass( elt, 'active' );
    }
  }
};

ListType.prototype.showPanel = function( parent )
{
  this.editor._ListType.currentListTypeParent = parent;
  this.showActive(parent);
  this.editor.showPanel( this.editor._ListType );
};