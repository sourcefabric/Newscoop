(function( $ ){
	
	var defaults = {
		'namespace': 'wobs',
		'defaultView' : 'month',
		'today' : new Date(),
		'date' : new Date(),
		'dayNames': ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
		'firstDay': 0,
		'monthNames': ['January','February','March','April','May','June','July','August','September','October','November','December'],
		'navigation': true,
		'showDayNames': true,
		'earliestMonth': undefined,
		'latestMonth': undefined
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
		var _today = options.today;
		var _date = options.date;
		var _view = options.defaultView;
		var _header = undefined;
		
		this.options = options;
		
		t.getCalendarDate = getCalendarDate;
		t.setCalendarDate = setCalendarDate;
		t.getTodaysDate = getTodaysDate;
		t.render = render;
		
		if ($.isFunction(options.articles)) {
			_get_articles = options.articles;
			delete options.articles;
		}
	
		function render() {
	
			if (_view === 'month') {
				monthView();
				_header = new Header(t, element, options);
			}
			else if(_view === 'widget') {
				widgetView();
			}
			
			renderArticles();
		}
		
		function getTodaysDate() {
			return _today;
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
			diff = Math.round((date.getTime() - _start.getTime())/one_day);
			
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
			var table, thead, tbody, tr, td, th, dateBox, dayIndex;
			
			table = $("<table/>");
			tbody = $("<tbody/>");
			
			//show the names of the days of the week on the calendar
			if(options.showDayNames) {
				thead = $("<thead/>");
				thead.append("<tr/>");
				
				//make the <thead> <tr> <th>s
				for(var i=0; i<7; i++) {
					th = $("<th/>");
					
					dayIndex = (options.firstDay + i) % 7;
					th.append(options.dayNames[dayIndex]);
					
					thead.find("tr").append(th);
				}
				
				table.append(thead)
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
					
					dateBox = new DayBox(t, td, dayNum);
					_date_cache.push(dateBox);
					
					tr.append(td);
				}
				
				tbody.append(tr);
			}
			
			table.append(tbody);
			
			setMonthViewDates();
				
			element.append(table);
		}	
		
		function setMonthViewDates() {
			var y, m, begin, s_dofw, d, tmp_date;
			
			y = _date.getFullYear();
			m = _date.getMonth();
			
			begin = new Date(y, m, 1);
			s_dofw = begin.getDay();
			
			//need this first day option to start week on either sunday/monday etc (leftmost day)
			d = 1 - s_dofw + options.firstDay;
			if (s_dofw < options.firstDay) {
				d = d - 7;
			}

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
		
		var t, ul, html='';
		
		t = this;
		ns = options.namespace;
		
		t.disableHeader = disableHeader;
		t.enableHeader = enableHeader;
		
		ul = $('<ul class="'+ns+'-calendar-nav"/>');
		
		if (options.navigation === true) {
			html = html + '<li class="'+ns+'-button-next"><a></a></li>';
			html = html + '<li class="'+ns+'-button-prev"><a></a></li>';
		}	
		
		html = html + '<li class="'+ns+'-calendar-month"><p></p></li>';
		
		
		ul.append(html);
		
		updateHeader(calendar.getCalendarDate());
		enableHeader();
		
		ul.find('.'+ns+'-button-prev').click(function(){
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
		
		ul.find('.'+ns+'-button-next').click(function(){
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
		
		element.append(ul);
		
		function disableHeader() {
			
			disableButton('prev');
			disableButton('next');		
		}
		
		function enableHeader() {
			var date;
			
			date = calendar.getCalendarDate();
			
			//have reached the earliest month we should show.
			if ((options.earliestMonth !== undefined) && (date.getTime() === options.earliestMonth.getTime())) {
				deactivateButton('prev');
			}
			else {
				activateButton('prev');
				enableButton('prev');
			}
			
			//have reached the latest month we should show.
			if ((options.latestMonth !== undefined) && (date.getTime() === options.latestMonth.getTime())) {
				deactivateButton('next');
			}
			else {
				activateButton('next');
				enableButton('next');
			}
					
		}
		
		function activateButton(buttonName) {
			ul.find('.'+ns+'-button-' + buttonName)
				.removeClass('disabled');
		}	
		
		function deactivateButton(buttonName) {
			ul.find('.'+ns+'-button-' + buttonName)
				.addClass('disabled');
		}
			
		function disableButton(buttonName) {
			ul.find('.'+ns+'-button-' + buttonName)
				.addClass(ns + '-state-disabled');
		}
			
		function enableButton(buttonName) {
			ul.find('.'+ns+'-button-' + buttonName)
				.removeClass(ns + '-state-disabled')
				.removeClass("disabled");
		}
		
		function updateHeader(date) {
			var month;
			
			yyyy = date.getFullYear();
			month = date.getMonth();
			month = options.monthNames[month]; 
			
			ul.find('.'+ns+'-calendar-month p')
				.empty()
				.append(month+" "+yyyy);
		}
		
		return t;
	}
	
	function DayBox(calendar, td, box_id) {
		
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
		
		td.append('<div class="wobs-date-content"><a></a></div>')
			.find("a")
			.append('<div class="wobs-date-container"/>')
				.find(".wobs-date-container")
				.append('<div class="wobs-date-label"/>');
		
		function setDate(date) {
			var cm, dm, today;
	
			td.find(".wobs-date-label")
				.append(date.getDate());
			
			cm = calendar.getCalendarDate();
			cm = cm.getMonth();
			dm = date.getMonth();
			
			if (cm === dm) {
				td.addClass("wobs-curr-month");
			}
			else {
				td.addClass("wobs-other-month");
			}
			
			today = calendar.getTodaysDate();
			//date is today
			if (today.getTime() == date.getTime()) {
				td.find(".wobs-date-label").addClass("wobs-today");
			}
			
			_date = date;
		}
		
		function setTitle(title) {
			_title = title;
			
			$(td).qtip({
			    id: 'wobs-tooltip-'+box_id,
			    content: {
			        text: title
			    },
				position: {
					my: 'bottom center',
					at: 'top center'
				},
				style: {
			      classes: 'ui-tooltip-light ui-tooltip-shadow ui-tooltip-rounded'
			   }
			});
		}
		
		function setThumbnail(picture) {
			_s_image = picture;
			
			td.find("a").append('<img src="'+_s_image+'"></img>');
		}
		
		function setUrl(url) {
			_url = url;
			td.find("a").attr("href", url);
		}
		
		function clear() {
			_date = undefined;
			_title = undefined;
			_s_image = undefined;
			_url = undefined;
			
			td.qtip('destroy');
			
			td.removeClass("wobs-other-month");
			td.removeClass("wobs-curr-month");
			td.removeAttr("title");
			
			td.find("a").removeAttr("href");
			
			td.find("img")
				.remove();
	
			td.find(".wobs-date-label")
				.removeClass("wobs-today")
				.empty();
		}
	}
	
})( jQuery );


