(function( $ ){
	
	var defaults = {
		'namespace': 'wobs',
		'defaultView' : 'month',
		'date' : new Date(),
		'dayNames': ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
		'monthNames': ['January','February','March','April','May','June','July','August','September','October','November','December'],
		'navigation': true
    };
  
	var methods = {
		init : function( options ) { 
			// If options exist, lets merge them with our default settings
			if ( options ) { 
				options = $.extend( defaults, options );
			}
			
			this.each(function(i, _element) {  				
				var element = $(_element);
				var calendar = new WobsCalendar(element, options);
				element.data('wobscalendar', calendar);
				calendar.render();				
			});
		
			return this;
		}
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
		
		var t = this;
	
		var _get_articles = undefined;
		var _date_cache = [];	
		var _start = undefined;
		var _end = undefined;
		var _date = options.date;
		var _view = options.defaultView;
		var _header = undefined;
		
		this.options = options;
		
		t.getCalendarDate = getCalendarDate;
		t.setCalendarDate = setCalendarDate;
		t.render = render;
		
		if ($.isFunction(options.articles)) {
			_get_articles = options.articles;
			delete options.articles;
		}
	
		function render() {
	
			if (_view === 'month') {
				_header = new Header(t, element, options);
				monthView();
			}
			else if(_view === 'widget') {
				widgetView();
			}
			
			renderArticles();
		}
		
		function getCalendarDate() {
			return _date;
		}
		
		function setCalendarDate(date) {
			_date = date;
			
			//set these to empty date boxes.
			for (var i=0; i<_date_cache.length; i++) {
				_date_cache[i].clear();
			}
			
			if (_view === 'month') {
				setMonthViewDates();
			}
			
			renderArticles();
		}
		
		function renderArticles() {
			
			if (_get_articles !== undefined) {
				if (_view === 'month' && options.navigation === true) {
					_header.disableHeader();
				}
				_get_articles(_start, _end, updateCalendar);
			}	
		}
		
		function updateCalendar(articles) {
			var cached_date, tmpDate, article;
				
			for (var i=0; i<articles.length; i++) {
				article = articles[i];
				
				tmpDate = new Date(article.date.year, article.date.month, article.date.day);
				cached_date = retrieveDateFromCache(tmpDate);
				
				if (article.title !== undefined) {
					cached_date.setTitle(article.title);
				}
				if (article.image !== undefined) {
					cached_date.setThumbnail(article.image);
				}
				if (article.url !== undefined) {
					cached_date.setUrl(article.url);
				}		
			}
			
			if (_view === 'month' && options.navigation === true) {
				_header.enableHeader();
			}
		}
		
		//date is a js Date object.
		function retrieveDateFromCache(date) {
			var one_day, diff;
			
			one_day=1000*60*60*24; //one day in milliseconds.
			diff = (date.getTime() - _start.getTime())/one_day;
			
			return _date_cache[diff];
		}
		
		function widgetView() {
			var table, tr, td, dateBox;
			
			table = $("<table><tbody></tbody></table>");
			tr = $("<tr/>");
			
			for (var j=0; j<7; j++) {
				
				td = $("<td/>");
				td.addClass("wobs-day-"+j);
				
				dateBox = new DayBox(j, td);
				_date_cache.push(dateBox);
				
				tr.append(td);
			}
			
			table.append(tr);
			
			setWidgetViewDates();
			
			element.append(table);
		}
		
		function setWidgetViewDates() {
			var y, m, d;
			
			y = _date.getFullYear();
			m = _date.getMonth();
			d = _date.getDate() - 1;
			
			var tmp_date;
			for (var c=_date_cache.length-1; c>-1; c--) {
				tmp_date = new Date(y, m, d);
				
				if(c == 0) {
					_start = tmp_date;
				}
				else if(c == _date_cache.length-1) {
					_end = tmp_date;
				}
				
				_date_cache[c].setDate(tmp_date);
				d--;
			}
		}
		
		function monthView() {
			var table, thead, tbody, tr, td, th, dateBox;
			
			table = $("<table/>");
			thead = $("<thead/>");
			tbody = $("<tbody/>");
			
			thead.append("<tr/>");
			
			//make the <thead> <tr> <th>s
			for(var i=0; i<7; i++) {
				th = $("<th/>");
				
				th.append(options.dayNames[i]);
				
				thead
					.find("tr")
						.append(th);
			}
			
			//make the <tbody> <tr>s
			for(var i=0; i < 6; i++) {
				
				tr = $("<tr/>");
				tr.addClass("wobs-week-"+i);
				
				if(i === 0) {
					tr.addClass("wobs-week-first");
				}
				else if(i === 5) {
					tr.addClass("wobs-week-last");
				}
				
				for (var j=0; j<7; j++) {
					var dayNum = i*7+j;
					
					td = $("<td/>");
					td.addClass("wobs-day-"+dayNum);
					
					dateBox = new DayBox(dayNum, td);
					_date_cache.push(dateBox);
					
					tr.append(td);
				}
				
				tbody.append(tr);
			}
			
			table.append(thead)
				.append(tbody);
			
			setMonthViewDates();
				
			element.append(table);
		}	
		
		function setMonthViewDates() {
			var y, m, begin, s_dofw;
			
			y = _date.getFullYear();
			m = _date.getMonth();
			
			begin = new Date(y, m, 1);
			s_dofw = begin.getDay();
		
			var d = 1-s_dofw;
			var tmp_date;
			for (var c=0; c<_date_cache.length; c++) {
				tmp_date = new Date(y, m, d);
				
				if(c == 0) {
					_start = tmp_date;
				}
				else if(c == _date_cache.length-1) {
					_end = tmp_date;
				}
				
				_date_cache[c].setDate(tmp_date);
				d++;
			}
		}
	}
	
	function Header(calendar, element, options) {
		
		var t, table, tm, html='';
		
		t = this;
		ns = options.namespace;
		
		t.disableHeader = disableHeader;
		t.enableHeader = enableHeader;
		
		table = $('<table class="'+ns+'-header"/>');
		
		html = '<tbody><tr><th colspan="7"><ul class="wobs-calendar-nav">';
		
		if (options.navigation === true) {
			html = html + '<li class="'+ns+'-button-prev"><a></a></li>';
		}	
		
		html = html + '<li class="'+ns+'-header-title"><h5></h5></li>';
		
		if (options.navigation === true) {
			html = html + '<li class="'+ns+'-button-next"><a></a></li>';
		}
		
		html = html + '</ul></th></tr></tbody>';
		
		table.append(html);
		
		updateHeader(calendar.getCalendarDate());
		
		table.find('.'+ns+'-button-prev').click(function(){
			var date, mm, yyyy, newDate;
			
			if ($(this).hasClass(ns + '-state-disabled')) {
				return;
			}
			
			date = calendar.getCalendarDate();
			
			yyyy = date.getFullYear();
			mm = date.getMonth();
			
			if (mm > 0) {
				mm = mm - 1;
			}
			else {
				mm = 11;
				yyyy = yyyy -1;
			}
			
			newDate = new Date(yyyy, mm);
			
			updateHeader(newDate);
			calendar.setCalendarDate(newDate);			
		});
		
		table.find('.'+ns+'-button-next').click(function(){
			var date, mm, yyyy, newDate;
			
			if ($(this).hasClass(ns + '-state-disabled')) {
				return;
			}
			
			date = calendar.getCalendarDate();
			
			yyyy = date.getFullYear();
			mm = date.getMonth();
			mm = mm + 1;
			
			newDate = new Date(yyyy, mm);
			
			updateHeader(newDate);
			calendar.setCalendarDate(newDate);
		});
		
		element.append(table);
		
		function disableHeader() {
			
			disableButton('prev');
			disableButton('next');
			
			deactivateButton('prev');
			deactivateButton('next');	
		}
		
		function enableHeader() {
			
			activateButton('prev');
			activateButton('next');
			
			enableButton('prev');
			enableButton('next');
		}
		
		function activateButton(buttonName) {
			table.find('.'+ns+'-button-' + buttonName)
				.addClass(ns + '-state-active');
		}	
		
		function deactivateButton(buttonName) {
			table.find('.'+ns+'-button-' + buttonName)
				.removeClass(ns + '-state-active');
		}
			
		function disableButton(buttonName) {
			table.find('.'+ns+'-button-' + buttonName)
				.addClass(ns + '-state-disabled');
		}
			
		function enableButton(buttonName) {
			table.find('.'+ns+'-button-' + buttonName)
				.removeClass(ns + '-state-disabled');
		}
		
		function updateHeader(date) {
			var month;
			
			yyyy = date.getFullYear();
			month = date.getMonth();
			month = options.monthNames[month]; 
			
			table.find('.'+ns+'-header-title h5')
				.empty()
				.append(month+" "+yyyy);
		}
		
		return t;
	}
	
	function DayBox(id, td) {
		
		var _id = id;
		var _date = undefined;
		var _title = undefined;
		var _s_image = undefined;
		var _url = undefined;
		
		var _element = td;
		
		this.setDate = setDate;
		this.setTitle = setTitle;
		this.setThumbnail = setThumbnail;
		this.setUrl = setUrl;
		this.clear = clear;
		
		td.append('<div class="wobs-date-content"><a></a></div>').find("a")
			.append('<div class="wobs-date-label"/>')
			.append('<span class="wobs-date-title"/>');
		
		td.click(function(){
			if (_url !== undefined) {
				window.open(_url);
			}
		});
		
		function setDate(date) {
			_date = date;
			td.find(".wobs-date-label")
				.append(_date.getDate());
		}
		
		function setTitle(title) {
			_title = title;
			td.find(".wobs-date-title")
				.append(_title);
		}
		
		function setThumbnail(picture) {
			_s_image = picture;
			
			td.find("a").append('<img width="164" height="166" src="'+_s_image+'"></img>');
				//.css("background-image", "url("+_s_image+")")
				//.css("background-size", "125px 120px")
				//.css("background-repeat", "no-repeat");
		}
		
		function setUrl(url) {
			_url = url;
		}
		
		function clear() {
			_date = undefined;
			_title = undefined;
			_s_image = undefined;
			_url = undefined;
			
			td.find("img")
				.remove();
			td.find(".wobs-date-title")
				.empty();
			td.find(".wobs-date-label")
				.empty();
		}
	}
	
})( jQuery );


