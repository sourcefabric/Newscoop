$(function()
{
	String.prototype.capitalize = function()
	{
		return this.replace( /(^|\s)([a-z])/g , function( m, p1, p2 ){ return p1+p2.toUpperCase(); } );
	};
	/**
	 * @todo should have this inside the view helper
	 */
	jQuery.registry = 
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

