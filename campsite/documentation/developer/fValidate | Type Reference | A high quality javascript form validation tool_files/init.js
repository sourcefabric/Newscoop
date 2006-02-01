function init( pageId )
{	
	setActiveNavItem();
	if ( pageId == '' )
	{
		return;
	}
	switch( pageId )
	{
		case '_changelog':
			window.changelog = new NavMenu('changelog',0,1,0,'Show changes','Hide changes','','','subMenuHead','subMenu','showhide');
			break;
		case '_beta_signup':
			var coll = document.getElementsByName('groups[]');
			var group = location.search.split( "=" )[1];
			if ( typeof group == 'undefined' || group == '' )
			{
				return;
			}
			for ( var i = 0; i < coll.length; i++ )
			{				
				if ( coll[i].value == group )
				{
					coll[i].checked = true;
					break;
				}
			}
	}
}

function setActiveNavItem()
{
	if ( /fValidate\/?$/.test( top.location.href ) ) return;
	var nav = document.getElementById( 'nav' );
	var linx = nav.getElementsByTagName( 'a' ),
		i = 0,
		link;	
	var page = location.href.match( /peterbailey.net\/fValidate(\/[a-z_]+\/?)/i )[1];
	if ( page != null )
	{
		var pattern = new RegExp( page );
		while( link = linx[i++] )
		{		
			if ( pattern.test( link.href ) )
			{
				link.className = 'current';
				break;
			}
		}
	}
}

function toggleDisp( nodeId, elem )
{
	var node = document.getElementById( nodeId )
	var nodeStyle = node.currentStyle || node.style;
	node.style.display = ( nodeStyle.display == 'none' || nodeStyle.display == '' ) ? 'block' : 'none';
}