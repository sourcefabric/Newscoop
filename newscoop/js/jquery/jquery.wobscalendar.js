(function( $ ){
	
	var defaults = {
		'name' : 'Wobs Calendar',
		'defaultView' : 'month',
		'date' : new Date()
    };
  
	var methods = {
		init : function( options ) { 
			// If options exist, lets merge them with our default settings
			if ( options ) { 
				$.extend( defaults, options );
			}
			
			this.each(function(i, _element) {  				
				var element = $(_element);
				var calendar = new WobsCalendar(element, options);
				element.data('wobscalendar', calendar);
				calendar.render();				
			});
		
			return this;
		},
		//moves to previous month/week
		previous : function() {},
		//moves to next month/week
		next : function(){}
	
	};
		
	$.fn.wobscalendar = function( method ) {
	    
	    // Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} 
		else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} 
		else {
		  $.error( 'Method ' +  method + ' does not exist on jQuery.wobscalendar' );
		} 

	};
	
	//element is in jquery form $()
	function WobsCalendar(element, options) {
		
		this.options = options;
		
		var articles = [];
		var _element = element[0];
		
		this.render = render;
	
		if ($.isFunction(options.articles)) {
			this.getArticles = options.articles;
		}
		
		function render() {
			element.append("Cool Plugin: ")
				.append(options.name);
			
			this.getArticles(null, null, function(articles){
				var div = $("<div/>");
				for(var i=0; i < articles.length; i++) {

					div.append("<p>"+articles[i].title+"</p>")
						.append('<img class="thumbnail" src="'+articles[i].image+'">');
					element.append(div);
				}
			});
		}
			
	}
	
	function MonthView(element) {
		var table;
		
		table = $("table");
		thead = $("thead");
		tbody = $("tbody");
		
		table.append(thead)
			.append(tbody);
		
		element.append(table);
	}
	
})( jQuery );


