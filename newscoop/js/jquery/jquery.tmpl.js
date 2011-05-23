/**
 * originally designed by Laura Doktorova in 2011, undet the name of doT.js - https://github.com/olado/doT
 */
(function()
{
	var dooTz =
	{
		version : '0.1'
	};

	if( typeof module !== 'undefined' && module.exports )
		module.exports = dooTz;
	else
		this.dooTz = dooTz;

	dooTz.templateSettings =
	{
		encode 		: /\{\{!([\s\S]+?)\}\}/g,
		defines 	: /\{\{#([\s\S]+?)\}\}/g,
		evaluate 	: /\{\%([\s\S]+?)\%\}/g,
		interpolate : /\{\{([\s\S]+?)\}\}/g,
		ends 		: /(endfor|endif|enblock)/g,
		varname 	: 'it',
		strip 		: true
	};

	dooTz.template = function(tmpl, c, defs)
	{
		c = c || dooTz.templateSettings;
		var str = ("with("
				+ c.varname
				+ "){var out='"
				+ //
				((c.strip) ? tmpl.replace( /\s*<!\[CDATA\[\s*|\s*\]\]>\s*|[\r\n\t]|(\/\*[\s\S]*?\*\/)/g, '' )
						: tmpl)
						.replace(c.defines, function(match, code)
						{
							return eval(code.replace(/[\r\t\n]/g, ' '));
						})
						.replace(/\\/g, '\\\\')
						.replace(/'/g, "\\'")
						.replace(
								c.interpolate,
								function(match, code)
								{
									return "';out+="
											+ code.replace(/\\'/g, "'")
													.replace(/\\\\/g, "\\")
													.replace(/[\r\t\n]/g, ' ')
											+ ";out+='";
								})
						.replace(
								c.encode,
								function(match, code)
								{
									return "';out+=("
											+ code.replace(/\\'/g, "'")
													.replace(/\\\\/g, "\\")
													.replace(/[\r\t\n]/g, ' ')
											+ ").toString().replace(/&(?!\\w+;)/g, '&#38;').split('<').join('&#60;').split('>').join('&#62;').split('"
											+ '"'
											+ "').join('&#34;').split("
											+ '"'
											+ "'"
											+ '"'
											+ ").join('&#39;').split('/').join('&#x2F;');out+='";
								})
						.replace(
								c.evaluate,
								function(match, code)
								{
									if (c.ends.test(code))
										return "';"
												+ code.replace(c.ends, '}')
														.replace(/\\'/g, "'")
														.replace(/\\\\/g, "\\")
														.replace(/[\r\t\n]/g,
																' ') + "out+='";
									return "';"
											+ code.replace(/\\'/g, "'")
													.replace(/\\\\/g, "\\")
													.replace(/[\r\t\n]/g, ' ')
											+ "{out+='";
								}) + "';return out;}").replace(/\n/g, '\\n')
				.replace(/\t/g, '\\t').replace(/\r/g, '\\r').split("out+='';")
				.join('').split('var out="";out+=').join('var out=');
		try
		{
			return new Function( c.varname, str );
		} 
		catch (e)
		{
			if (typeof console !== 'undefined')
				console.error( "Could not create a template function: " + str );
			throw e;
		}
	};
}());

(function($)
{
	$.fn.extend
	({
		template : function(name)
		{
			return $.template(this[0]);
		},
		tmpl : function(data, options, parent)
		{
			return $.tmpl( $(this[0]), data, options, parent );
		}
	});
	$.extend
	({
		tmpl : function(tmpl, data, options, parentItem)
		{
			return ( dooTz.template( tmpl.html() ) )(data);
		},
		template : function(tmpl)
		{
			return dooTz.template( $(tmpl).html() );
		}
	});
})(jQuery);