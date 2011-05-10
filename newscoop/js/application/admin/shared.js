$(function()
{
	$.registry = 
	{
		_ns 	: "customRegistry"
	,	get		: function( arg )
		{
			return jQuery.data( document.body, this._ns+"."+arg );
		}
	,	set		: function( arg, val )
		{
			jQuery.data( document.body, this._ns+"."+arg, val );
		}	
	,	unset 	: function( arg )
		{
			jQuery.removeData( document.body, this._ns+"."+arg );
		}
	,	setNs 	: function( name )
		{
			console.log( jQuery.data( document.body ) )
		}
	}

});

