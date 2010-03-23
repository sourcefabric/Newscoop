/*
	Author: Joe Nicora
	www.seemecreate.com
	-------------------
	Attribution-Share Alike 3.0 United States
	You are free:
		* to Share � to copy, distribute, display, and perform the work
		* to Remix � to make derivative works
	Under the following conditions:
		* Attribution. You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).
		* Share Alike. If you alter, transform, or build upon this work, you may distribute the resulting work only under the same, similar or a compatible license.
		* For any reuse or distribution, you must make clear to others the license terms of this work. The best way to do this is with a link to this web page.
		* Any of the above conditions can be waived if you get permission from the copyright holder.
		* Apart from the remix rights granted under this license, nothing in this license impairs or restricts the author's moral rights.
		
	http://creativecommons.org/licenses/by-sa/3.0/us/
*/
/*
	lastmodified: 2/29/2008
	version: 1.7.1 STABLE
*/
/* Set up lib namespace, used to store convenience objects
 */
var lib = {};
lib.d = YAHOO.util.Dom;
lib.e = YAHOO.util.Event;
lib.ce = YAHOO.util.CustomEvent;
lib.a = YAHOO.util.Anim;
/* Set up photoViewer namespace
 */
YAHOO.namespace("YAHOO.photoViewer");
/* photoViewer_base class
 * Base class responsible for core viewer functionality
 */
YAHOO.photoViewer.base = function(){
	// dom
	var container = null; // thumb container
	var viewerDom = null;
	var maskDom = null; // overlay (viewer) mask
	var showcaseImage = null;
	var headerDom = null;
	var titleDom = null;
	var closeDom = null;
	var bodyDom = null;
	var descDom = null;
	var footerDom = null;
	var prevDom = null;
	var nextDom = null;
	var flickrDom = null;
	var controlsDom = null;
	var playDom = null;
	var stopDom = null;
	var displayDom = null;
	var thumbContDom = null;
	// yui objects
	var viewer = null;
	var controls = null;
	// collections
	var thumbs = null; // collection of thumbs in thumb container
	// storage
	var that = this;
	var properties = null;
	var events = {}; // storage for custom events
	var currentThumb = null;
	var preloadtimer = null;
	var preloadimgs = [];
	var hasLoaded = {};
	var defaultText = {next:"next", prev:"prev", close:"close"};
	var defaultControls = {play: "play",pause: "pause",stop: "stop",display: "({0} of {1})"}; // not implemented
	var shuffleArr = [];
	var slideShowTimerTimeout = null; // 
	var controlThumbs = [];
	var lastControlThumb = null;
	var registeredThumbs = 0;

	// public
	this.init = function(id){
		properties = YAHOO.photoViewer.config.viewers[id].properties;
		// check for templates
		if (properties.template){
			createTemplate();
		}
		//set up property defaults
		properties.state = 0; // 0 = closed, 1 = open
		properties.xy = (properties.position ==  "relative") ? [0,0] : properties.xy;
		properties.thumbEvent = properties.thumbEvent || "click";
		properties.slideShow = properties.slideShow || false;
		if (properties.slideShow){
			properties.slideShow.duration = properties.slideShow.duration ||  3000;
			properties.slideShow.loopMode = properties.slideShow.loopMode ||  "loop";
			properties.slideShow.startAt = properties.slideShow.startAt ||  "first";
			properties.slideShow.playMode = properties.slideShow.playMode ||  "ordered";
			properties.slideShow.applyControls = lib.d.get(properties.slideShow.applyControls) ||  false;
			properties.slideShow.controlsText = properties.slideShow.controlsText || null;
			// boolean checks
			properties.slideShow.loop = (properties.slideShow.loop ==  undefined) ? true : properties.slideShow.loop;
			properties.slideShow.autoStart = (properties.slideShow.autoStart ==  undefined) ? true : properties.slideShow.autoStart;
			properties.slideShow.state = 0; // 0 = stop, 1 = pause, 2 = play
		}
		if (properties.flickrRss){
			if (!properties.flickrRss.id){
				alert("You cannot use a Flickr feed without an id");
				return;
			}
			properties.loadFrom = "flickr";
			properties.flickrRss.thumbSize = properties.flickrRss.thumbSize ||  "thumb";
			properties.flickrRss.maxDescriptionLen = properties.flickrRss.maxDescriptionLen ||  255;
		}
		if (properties.flickrApi) {
			if (!properties.flickrApi.apikey || !properties.flickrApi.method) {
				alert("You need both a Flickr API key and an API method to use this feature.");
				return;
			}
			properties.loadFrom = "flickr";
		}
		// check loadfrom
		switch(properties.loadFrom){
			case "xml":
				setEvents();
				if (properties.slideShow){
					if (properties.slideShow.autoStart){ 
						this.loadXML();
						events.xmlload.unsubscribe(this.play);
						events.xmlload.subscribe(this.play);
					}	
				}
				break;
			case "flickr":
				setEvents();
				if (properties.slideShow){
					if (properties.slideShow.autoStart){ 
						this.loadFlickr();
						events.flickrload.unsubscribe(this.play);
						events.flickrload.subscribe(this.play);
					}	
				}
				break;
			case "html":
				config();
				if (properties.slideShow){
					if (properties.slideShow.autoStart) this.play();
				}
				break;
		};
		if (properties.slideShow){
			if (!properties.slideShow.loop) events.lastphoto.subscribe(this.stop); // no looping
		}
		return this;
	}; // load init
	this.loadXML = function(url){
		var sUrl = url ? url : properties.url;
		var postData = "";
		var callback = {
		  success: readPhotos,
		  failure: fail,
		  that: this
		};
		YAHOO.photoViewer.loading.on();
		var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback, postData);
	}; // end loadXML
	this.loadFlickr = function(override){
		var scriptSrc = "";
		var p = override || properties; // override config for Flickr API calls
		var jsoncallback = "YAHOO.photoViewer.controller.viewers." + p.id + ".registerFlickrApi";
		if (p.flickrRss) {
			scriptSrc = (!p.flickrRss.set) ? "http://api.flickr.com/services/feeds/photos_public.gne?id=" + p.flickrRss.id + "&lang=en-us&format=json&jsoncallback=YAHOO.photoViewer.controller.viewers." + p.id + ".registerFlickrRss" : "http://api.flickr.com/services/feeds/photoset.gne?set="+p.flickrRss.id+"&nsid=" + p.flickrRss.id + "&lang=en-us&format=json&jsoncallback=YAHOO.photoViewer.controller.viewers." + p.id + ".registerFlickrRss";
		}
		if (p.flickrApi) { 
			if (!override) override = {};
			if (override.flickrApi){
				jsoncallback = override.flickrApi.jsoncallback || jsoncallback;
			}
			scriptSrc = "http://www.flickr.com/services/rest/?method="+p.flickrApi.method+"&format=json&api_key="+p.flickrApi.apikey+"&jsoncallback=" + jsoncallback; 
			var paramStr = "";
			for (var a in p.flickrApi.params){
				paramStr += "&" + a + "=" + encodeURIComponent(p.flickrApi.params[a]);
			}
			scriptSrc += paramStr;
		}
		flickrDom = document.createElement("script");
		flickrDom.setAttribute("type", "text/javascript");
		flickrDom.setAttribute("src", scriptSrc);
		document.getElementsByTagName("head")[0].appendChild(flickrDom);
	}; // end loadFlickr
	this.registerFlickrRss = function(rsp){
		if(properties.flickrRss){
			var photo = {};
			var thumbReplace = (properties.flickrRss.thumbSize == "square") ? "_s" : "_t";
			var photoNodes = rsp.items;
			for (var i = 0; i < photoNodes.length; i++){
				photo.thumbsource = photoNodes[i].media.m.replace("_m", thumbReplace);
				photo.fullsource = photoNodes[i].media.m.replace("_m", "");
				photo.title = photoNodes[i].title;
				photo.description = photoNodes[i].description;
				// parse text
				/* Thanks to Tim Stone <www.timsworlds.com> for this snippet */
				photo.description = photo.description.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&amp;/g, "&").replace(/&quot;/g, '"');
				if (YAHOO.env.ua.ie){ // wow, ie has some problems
					var tempDesc = document.createElement("div");
					tempDesc.innerHTML = photo.description;
					photo.description = tempDesc.innerText;
					tempDesc = null;
				}
				var maxLen = properties.flickrRss.maxDescriptionLen != null ? properties.flickrRss.maxDescriptionLen : 600;
				photo.description = photo.description.replace(/<p>.*?<\/p> /, ''); // lop off the "so-and-so posted a photo:" paragraph
				if (photo.description.length >= maxLen) { photo.description = mid(photo.description, 0, maxLen).replace(/\s*[^\s]*$/, '') + " ..."; }
				//
				createPhoto(photo);
			}
			config();
			events.flickrload.fire();
			YAHOO.photoViewer.loading.off();
		}
	}; // end registerFlickrRss
	this.registerFlickrApi = function(rsp){
		// find me 
		if (rsp.stat != "fail"){ // successful API return
			var c = {
				properties: properties,
				createPhoto: createPhoto,
				rsp: rsp
			};
			var func = this;
			var els = properties.flickrApi.method.split(".");
			while (els.length) {
				func = func[els.shift()];
			}
			properties = func.call(this, c);
			config();
			events.flickrload.fire();
		}
		else{ // report API error
			alert("Flickr API error code: " + rsp.code + "\n" + rsp.message);
		}
		YAHOO.photoViewer.loading.off();
	}; // end registerFlickrApi
	this.prev = function(){		
		function onNext(){
			var currentIndex = getThumbIndexFromId(currentThumb);
			var prevousIndex = (currentIndex > 0) ? (currentIndex - 1) : (thumbs.length - 1);
			currentThumb = thumbs[prevousIndex].getElementsByTagName("img")[0];
			loadViewer(thumbs[prevousIndex]);
		};
		beforeNext(onNext);
	}; // end prev
	this.next = function(){
		function onNext(){
			var currentIndex = getThumbIndexFromId(currentThumb);
			var nextIndex = (currentIndex < (thumbs.length - 1)) ? (currentIndex + 1) : 0;
			currentThumb = thumbs[nextIndex].getElementsByTagName("img")[0];
			loadViewer(thumbs[nextIndex]);
		};
		beforeNext(onNext);
	}; // end next
	this.close = function(){
		function onNext(){
			viewer.hide();
			lib.d.setStyle(maskDom, "visibility", "hidden");
			if (properties.slideShow){ 
				that.stop();
				lib.d.setStyle(controlsDom, "display", "none");
			}
			events.closeviewer.fire(that);
			properties.state = 0;
		};
		beforeNext(onNext);
	}; // end close
	this.open = function(index){
		function onNext(index){
			var thumb = currentThumb ? currentThumb.parentNode : thumbs[0];
			var index = Number(index);
			if (index > -1 && index < thumbs.length) {
				thumb = thumbs[index];
			}
			currentThumb = thumb.getElementsByTagName("img")[0];
			events.openviewer.fire(that);
			loadViewer(thumb);
		};
		beforeNext(onNext, index);
	}; // end open
	this.on = function(evt, callback){
		events[evt].subscribe(callback);
	}; // end on
	this.un = function(evt, callback){
		events[evt].unsubscribe(callback);
	}; // end on
	this.getProperty = function(p){
		if (properties[p] === undefined){ return null; }
		return properties[p]
	}; // end getProperty
	this.setProperty = function(p, v){
		if (properties[p] === undefined){ return null; }
		properties[p] = v;
		return properties[p]
	}; // end getProperty
	this.preload = function(){		
		var func = "YAHOO.photoViewer.controller.getViewer('"+properties.id+"').preload()";
		if (preloadtimer) {
			clearTimeout(preloadtimer); 
		}		
		for (var a = 0; a < thumbs.length; a++){
			var href = thumbs[a].getAttribute("href");
			var fullsource = thumbs[a].getAttribute("fullsource");
			preloadimgs.push(new Image());
			if (href != "javascript")
				preloadimgs[a].src = href;
			else
				preloadimgs[a].src = fullsource;
		}
		if (preloadimgs.length < thumbs.length)
			preloadtimer = setTimeout(func, 1000);
	}; // end preload
	/* slide show methods */
	this.play = function(index){
		var next = 0;
		if (properties.slideShow.playMode == "shuffle") { next = shuffle(); }
		if (properties.slideShow.playMode == "random") { next = randRange(0, thumbs.length); }
		if (properties.slideShow.state == 1) { next = (getThumbIndexFromId(currentThumb) + 1); } // if paused pick off where we left off
		if (arguments.length) { next = index + 1; }
		events.viewerload.unsubscribe(startTimer);
		events.viewerload.subscribe(startTimer);
		that.open(next);
		properties.slideShow.state = 2;
		events.play.fire(that);
	}; // end play
	this.stop = function(){
		events.viewerload.unsubscribe(startTimer);
		clearTimeout(slideShowTimerTimeout);
		slideShowTimerTimeout = null;
		resetShuffle();
		properties.slideShow.state = 0;
		events.stop.fire(that);
	}; // end stop
	this.pause = function(){
		events.viewerload.unsubscribe(startTimer);
		clearTimeout(slideShowTimerTimeout);
		slideShowTimerTimeout = null;
		properties.slideShow.state = 1;
		events.pause.fire(that);
	}; // end pause
	this.destroyViewer = function(){
		if (viewer){
			// purge showcase
			lib.e.purgeElement(showcaseImage, true, "load");
			viewer.destroy();
			// clear custom events
			for (var a in events){
				var us = events[a].unsubscribeAll();
				//alert(us);
			}
			// clear thumb events
			for (var a = 0; a < thumbs.length; a++){
				lib.e.removeListener(thumbs[a], properties.thumbEvent, thumbClick);
				thumbs[a].setAttribute("href", thumbs[a].fullsource);
			}
			// clear slidesow
			events.viewerload.unsubscribe(startTimer);
			clearTimeout(slideShowTimerTimeout);
			slideShowTimerTimeout = null;
			// clear mask
			if (maskDom){ if (maskDom.parentNode) maskDom.parentNode.removeChild(maskDom); }
			// clear loading
			YAHOO.photoViewer.loading.destroy();
			// clear controller
			YAHOO.photoViewer.controller.removeViewer(properties.id);
			// clear this
			//that = null;
			//delete this;
		} 
	}; // end destroyViewer
	// private
	/* -----------------------------*/
	/* -----------------------------*/
	/* slide show functions */
	function startTimer(){
		var func = "YAHOO.photoViewer.controller.getViewer('"+properties.id+"').getNextSlide()";
		if (slideShowTimerTimeout) clearTimeout(slideShowTimerTimeout);
		slideShowTimerTimeout = setTimeout(func, properties.slideShow.duration);
	}; // end start timer
	function shuffle(){
		if (shuffleArr.length == 0){
			resetShuffle();
		}
		var seed = randRange(0, (shuffleArr.length - 1));
		var next = shuffleArr[seed];
		shuffleArr.splice(seed, 1);
		if (shuffleArr.length == 0){ // double check arraylength
			events.lastphoto.fire();
			// stop here if slide show is stopped
			if (properties.slideShow.state < 2) { return; }
		}
		return next;
	}; // end shuffle
	function resetShuffle(){
		for (var a = 0; a < thumbs.length; a++){
			shuffleArr[a] = a;
		}
	}; // end resetShuffle
	function randRange(intFrom, intTo, intSeed){
		intFrom = Math.floor( intFrom );
		intTo = Math.floor( intTo );
		return(
			Math.floor(intFrom + ((intTo - intFrom + 1) * Math.random( (intSeed != null) ? intSeed : 0)))
		);
	}; // end randomize
	this.getNextSlide = function(){
		var next = 0;
		if (properties.slideShow.playMode == "ordered"){
			var currentIndex = getThumbIndexFromId(currentThumb);
			var nextIndex = (currentIndex < (thumbs.length - 1)) ? (currentIndex + 1) : 0;
			this.open(nextIndex);
		}
		if (properties.slideShow.playMode == "shuffle"){
			this.open(shuffle());
		}
		if (properties.slideShow.playMode == "random"){
			this.open(randRange(0, thumbs.length));
		}
	}; // end getNextSlide
	function getThumbIndexFromId(el){
		//return Number(lib.d.getAncestorByTagName(el, "a").getAttribute("id").split("_")[1]);
		return Number(lib.d.getAncestorByTagName(el, "a").index);
	}; // end getThumbIndexFromId
	function getThumbIndex(el){
		for (var a = 0; a < thumbs.length; a++){
			if (el === thumbs[a])
				return a;
		}
	}; // end getThumbIndex
	function loadViewer(e){
		if (!viewer){
			createViewer();
			if (properties.slideShow) { if (properties.slideShow.controlsText) createControls(); }
			if (YAHOO.env.ua.ie == 0 || YAHOO.env.ua.ie == 7) { lib.e.on(showcaseImage, "load", viewerLoaded); }
			else { that.checkLoadForIe(); }
		}
		var thumbImage = lib.e.getTarget(e) ? lib.d.get(properties.id + "-thumb_" + lib.e.getTarget(e).parentNode.index).getElementsByTagName("img")[0] : e.getElementsByTagName("img")[0];
		var thumbAnchor = thumbImage.parentNode;
		var titleText = thumbAnchor.getAttribute("title").length ? thumbAnchor.getAttribute("title") : "";
		var descText = thumbImage.getAttribute("alt");
		var photoCount = getThumbIndexFromId(thumbImage) + 1;
		if (properties.slideShow){ // prep controls
			lib.d.setStyle(controlsDom, "display", "block");
		}
		if (photoCount == (thumbs.length)){ // fire before last photo
			var flag = false;
			if (properties.slideShow) {if (properties.slideShow.playMode == "ordered") { flag = true; }}
			else { flag = true; }
			if (flag) events.beforelastphoto.fire();
		}
		insertStr(titleDom, titleText); // set header
		insertStr(descDom.firstChild, descText); // set description
		// handle events
		if (!YAHOO.env.ua.ie != 6){ // everything but ie 6
			events.viewerupdated.unsubscribe(attachShowcaseEvts);
			events.viewerupdated.subscribe(attachShowcaseEvts);
			events.viewerupdated.fire();
		}
		else{
			that.checkLoadForIe();
		}
		/* set items hidden */
		lib.d.setStyle(bodyDom, "opacity", "0");
		lib.d.setStyle(titleDom, "opacity", "0");
		lib.d.setStyle(imageContDom, "opacity", "0");
		// handle events
		showcaseImage.src = thumbAnchor.fullsource; // load image!
		//if (!hasLoaded[thumbAnchor.fullsource]){ // check to see if this has loaded yet
		/* for now throw loading screen up every time */
		YAHOO.photoViewer.loading.on({applyTo:viewerDom});
		//}
		// adjust z indicies
		lib.d.setStyle(viewerDom, "z-index", YAHOO.photoViewer.zIndex++);
		lib.d.setStyle(maskDom, "z-index", YAHOO.photoViewer.zIndex - 1);
	}; // loadViewer
	function attachShowcaseEvts(){ // work around for ie bug
		lib.e.purgeElement(showcaseImage, true, "load");
		lib.e.on(showcaseImage, "load", viewerLoaded);
	}; 	// end attachShowcaseEvts
	
	/* bug for ie aaaaarrrrrggggghhhhhhhh!!!!!!!!!!!!! */
	var checkLoadTimeout = null;
	this.checkLoadForIe = function(){
		var func = "YAHOO.photoViewer.controller.getViewer('"+properties.id+"').checkLoadForIe()";
		if (checkLoadTimeout) {
			clearTimeout(checkLoadTimeout); 
		}
		if (!showcaseImage.complete){
			checkLoadTimeout = setTimeout(func, 1000);
		}
		else{
			viewerLoaded();
		}
	}; // end checkLoad
	/* bug for ie aaaaarrrrrggggghhhhhhhh!!!!!!!!!!!!! */
	
	function getClassName(el){
		if (!typeof el == "object") { return null; }
		if (YAHOO.env.ua.ie)
			return el.getAttribute("className");
		else
			return el.getAttribute("class");
	}; // end getClassName
	function readPhotos(data){
		var photo = {};
		if(data.responseXML !== undefined){
			var photoNodes = data.responseXML.getElementsByTagName("photo");
			for (var i = 0; i < photoNodes.length; i++){
				photo.thumbsource = photoNodes[i].getElementsByTagName("thumbsource")[0].firstChild.nodeValue;
				photo.fullsource = photoNodes[i].getElementsByTagName("fullsource")[0].firstChild.nodeValue;
				if (photoNodes[i].getElementsByTagName("title")[0].firstChild){
					photo.title = photoNodes[i].getElementsByTagName("title")[0].firstChild.nodeValue;
				}
				else{
					photo.title = "";
				}
				if (photoNodes[i].getElementsByTagName("description")[0].firstChild){
					photo.description = photoNodes[i].getElementsByTagName("description")[0].firstChild.nodeValue;
				}
				else{
					photo.description = "";
				}
				createPhoto(photo);
			}
			config();
			events.xmlload.fire();
			YAHOO.photoViewer.loading.off();
		}
	}; // end readPhotos
	function fail(data){
		alert("XML file failed to load");
		YAHOO.photoViewer.loading.off();
	}; // end fail
	function createPhoto(photo){
		var anchor = document.createElement("a");
		var img = document.createElement("img");
		container = lib.d.get(properties.id);

		anchor.setAttribute("href", photo.fullsource);
		anchor.setAttribute("fullsource", photo.fullsource);
		anchor.setAttribute("title", photo.title);
		lib.d.addClass(anchor, "photoViewer");
		//lib.d.setStyle(anchor, "display", "none"); // make the devs set thumbs to hidden via css
		
		img.setAttribute("src", photo.thumbsource);
		img.setAttribute("alt", photo.description);
		
		anchor.appendChild(img);
		container.appendChild(anchor);
	}; // end createPhoto
	function config(){
		container = lib.d.get(properties.id);
		thumbs = lib.d.getElementsByClassName("photoViewer", "a", container);
		if (YAHOO.env.ua.ie < 7) {
			that.preload(); // help ie6 by trying to preload full size images
		}
		// set events
		setEvents();
	}; // end config
	function setEvents(){
		if (thumbs){ // check if this viewer has  been configed
			// set thumb events, loop over collection
			for (var a = registeredThumbs; a < thumbs.length; a++){
				//thumbs[a].setAttribute("fullsource", thumbs[a].href);
				if (thumbs[a].getAttribute("href") != "javascript:;"){
					thumbs[a].fullsource = thumbs[a].href;
					thumbs[a].setAttribute("href", "javascript:;");
				}
				lib.e.removeListener(thumbs[a], properties.thumbEvent, thumbClick, that, true);
				lib.e.on(thumbs[a], properties.thumbEvent, thumbClick, that, true);
				thumbs[a].setAttribute("id", properties.id + "-thumb_" + a);
				thumbs[a].index = registeredThumbs;
				registeredThumbs++;
			}
			// set window events
			lib.e.on(window, "resize", adjustPosition);
			lib.e.on(window, "scroll", adjustPosition);
			lib.e.on(window, "resize", adjustControls);
			lib.e.on(window, "scroll", adjustControls);
		}
		// set custom events
		if (!events.opeviewer) events.openviewer = new lib.ce("openviewer", this, true, 1);
		if (!events.closeviewer) events.closeviewer = new lib.ce("closeviewer", this, true, 1);
		if (!events.xmlload) events.xmlload = new lib.ce("xmlload", this, true, 1); // think about re-naming as this is confusing, this pertains to the image load
		if (!events.viewerload) events.viewerload = new lib.ce("viewerload", this, true, 1);
		if (!events.viewerupdated) events.viewerupdated = new lib.ce("viewerupdated", this, true, 1); // used to check if new viewer image has loaded
		if (!events.beforelastphoto) events.beforelastphoto = new lib.ce("beforelastphoto", this, true, 1); 
		if (!events.lastphoto) events.lastphoto = new lib.ce("lastphoto", this, true, 1);
		if (!events.play) events.play = new lib.ce("play", this, true, 1);
		if (!events.stop) events.stop = new lib.ce("stop", this, true, 1);
		if (!events.pause) events.pause = new lib.ce("pause", this, true, 1);
		if (!events.flickrload) events.flickrload = new lib.ce("flickrload", this, true, 1);
	}; // end setEvents
	function thumbClick(e, scope){
		currentThumb = lib.d.get(properties.id + "-thumb_" + lib.e.getTarget(e).parentNode.index).getElementsByTagName("img")[0];
		events.openviewer.fire(that);
		loadViewer(e);
		lib.e.preventDefault(e); // stop default
	}; //  end thumbClick
	function viewerLoaded(){
		if (showcaseImage.src == "" || !showcaseImage.src){
			alert("Load error");
			return;
		}
		// show viewer, if already open
		if (viewerDom){
			viewer.show();
		}
		lib.d.setStyle(imageContDom, "opacity", "1");
		// apply modal screen
		lib.d.setStyle(maskDom, "visibility", "visible");
		// record loaded
		hasLoaded[showcaseImage.src] = true;
		var widthTo = (lib.d.getRegion(showcaseImage).right - lib.d.getRegion(showcaseImage).left) + 
			parseInt(lib.d.getStyle(imageContDom, "padding-left"), 10) +
			parseInt(lib.d.getStyle(imageContDom, "padding-right"), 10)
		;
		// ensure height by setting width
		lib.d.setStyle(descDom, "width", widthTo + "px");
		/* set total height for all components */
		var heightTo = 
			lib.d.getRegion(showcaseImage).bottom - lib.d.getRegion(showcaseImage).top +
			parseInt(lib.d.getStyle(imageContDom, "padding-top"), 10) +
			parseInt(lib.d.getStyle(imageContDom, "padding-bottom"), 10) +
			lib.d.getRegion(descDom).bottom - lib.d.getRegion(descDom).top +
			lib.d.getRegion(headerDom).bottom - lib.d.getRegion(headerDom).top + 
			lib.d.getRegion(footerDom).bottom - lib.d.getRegion(footerDom).top
		;
		var topTo = (lib.d.getViewportHeight()/2) - (heightTo/2) - 20 + lib.d.getDocumentScrollTop();
		var leftTo = (lib.d.getViewportWidth()/2) - (widthTo/2) - 20 + lib.d.getDocumentScrollLeft();
		var attr = {width:{to:widthTo},height:{to:heightTo},top:{to:topTo},left:{to:leftTo}};
		if (!properties.fixedcenter){ // dont update XY
			var x = eval(properties.xy)[0];
			var y = eval(properties.xy)[1];
			attr = {width:{to:widthTo},height:{to:heightTo}};
			lib.d.setStyle(viewerDom, "top", y + "px");
			lib.d.setStyle(viewerDom, "left", x + "px");
		}
		var resize = new lib.a(viewerDom, attr, properties.grow, properties.easing);
		resize.animate();
		resize.onComplete.unsubscribe(fadeViewer);
		resize.onComplete.subscribe(fadeViewer);
		// size mask
		adjustPosition();
		// clear objects
		resize = null;
		// fire event
		events.viewerload.fire();
		properties.state = 1;
	}; // end viewerLoaded
	function fadeViewer(){
		var fade = null;
		var domEls = [bodyDom,titleDom];
		
		YAHOO.photoViewer.loading.off();
		for (var a = 0; a < domEls.length; a++){
			fade = new lib.a(domEls[a], {opacity:{to:1}}, properties.fade, properties.easing);
			fade.animate();
		}
		fade.onComplete.unsubscribe(finishLoad);
		fade.onComplete.subscribe(finishLoad);
		// clear objects
		fade = null;
	}; // end fade viewer
	function finishLoad(){
		if ((getThumbIndexFromId(currentThumb) + 1) == (thumbs.length)){ // fire last photo
			var flag = false;
			if (properties.slideShow) {if (properties.slideShow.playMode == "ordered") { flag = true; }}
			else { flag = true; }
			if (flag) events.lastphoto.fire();
		}
		// show viewer, if already open
		if (!isVisible(viewerDom)){
			viewer.show();
		}
	}; // end finishLoad
	function createControls(){
		var controlsZ = 99999999999999999999;
		var defaultPlayText = properties.slideShow.controlsText.pause;
		var playClass = "photoViewer-pause";
		// build controls
		controlsDom = document.createElement("div");
		controlsDom.setAttribute("id", properties.id + "-controls");
		lib.d.addClass(controlsDom, "photoViewer-controls");
		// build play btn
		playDom = document.createElement("a"); // is both play/pause
		playDom.setAttribute("id", properties.id + "-play");
		lib.d.addClass(playDom, playClass);
		playDom.setAttribute("href", "javascript:;");
		lib.e.on(playDom, "click", btnPlay);
		insertStr(playDom, defaultPlayText);
		// build stop btn
		stopDom = document.createElement("a");
		stopDom.setAttribute("id", properties.id + "-stop");
		lib.d.addClass(stopDom, "photoViewer-stop");
		stopDom.setAttribute("href", "javascript:;");
		lib.e.on(stopDom, "click", btnStop);
		insertStr(stopDom, properties.slideShow.controlsText.stop);
		// build display
		displayDom = document.createElement("span");
		displayDom.setAttribute("id", properties.id + "-display");
		lib.d.addClass(displayDom, "photoViewer-display");
		// build thumb container outer
		thumbContOuterDom = document.createElement("div");
		thumbContOuterDom.setAttribute("id", properties.id + "-thumbContOuter");
		lib.d.addClass(thumbContOuterDom, "photoViewer-thumbContOuter");
		// build pan left
		panLeftDom = document.createElement("a");
		panLeftDom.setAttribute("id", properties.id + "-panLeft");
		lib.d.addClass(panLeftDom, "photoViewer-panLeft");
		panLeftDom.setAttribute("href", "javascript:;");
		lib.e.on(panLeftDom, "click", panLeft);
		insertStr(panLeftDom, " ");
		// build pan right
		panRightDom = document.createElement("a");
		panRightDom.setAttribute("id", properties.id + "-panRight");
		lib.d.addClass(panRightDom, "photoViewer-panRight");
		panRightDom.setAttribute("href", "javascript:;");
		lib.e.on(panRightDom, "click", panRight);
		insertStr(panRightDom, " ");
		// build thumb container
		thumbContDom = document.createElement("div");
		thumbContDom.setAttribute("id", properties.id + "-thumbCont");
		lib.d.addClass(thumbContDom, "photoViewer-thumbCont");
		insertStr(stopDom, properties.slideShow.controlsText.stop);
		// add to the dom
		thumbContOuterDom.appendChild(thumbContDom);
		controlsDom.appendChild(panLeftDom);
		controlsDom.appendChild(thumbContOuterDom);
		controlsDom.appendChild(panRightDom);
		controlsDom.appendChild(playDom);
		controlsDom.appendChild(stopDom);
		controlsDom.appendChild(displayDom);
		if (properties.slideShow.applyControls){
			lib.d.get(properties.slideShow.applyControls).appendChild(controlsDom);
			lib.d.addClass(controlsDom, "photoViewer-controls-relative");
		}
		else{
			document.body.appendChild(controlsDom);
			lib.d.addClass(controlsDom, "photoViewer-controls-absolute");
			lib.d.setStyle(controlsDom, "z-index", controlsZ);
			// position
			var marginLeft = ((lib.d.getRegion(controlsDom).right - lib.d.getRegion(controlsDom).left) / 2) * -1;
			lib.d.setStyle(controlsDom, "margin-left", marginLeft + "px");
		}
		initControlThumbs();
		// subscribe to the viewerload event for display updates
		events.viewerload.unsubscribe(displayUpdate);
		events.viewerload.subscribe(displayUpdate);
		events.lastphoto.unsubscribe(playBtnDisplay);
		events.lastphoto.subscribe(playBtnDisplay);
		// adjust position
		adjustControls();
	}; // end createControls
	function panLeft(){
		pan(1);
	}; // end panLeft
	function panRight(){
		pan(-1);
	}; // end panRight
	function pan(op){
		var index = currentThumb.parentNode.index;
		var currentControlThumb = lib.d.get("controlsThumb_" + index); // the anchor
		var layInfo = getThumbLay(currentControlThumb);
		var offset = parseInt(lib.d.getStyle(thumbContDom, "margin-left"), 10) + ((layInfo.thumbw * 2) * op);
		thumbContDom.offsetlay = offset;
		var pan = new lib.a(thumbContDom, {marginLeft:{to:offset}}, 0.5, YAHOO.util.Easing.backOut);
		pan.animate();
	}; // end pan
	function initControlThumbs(){
		var controlAch, controlThb;
		for (var a = controlThumbs.length; a < thumbs.length; a++){
			controlThumbs.push({
				dom: document.createElement("a").appendChild(document.createElement("img")),
				index: a
			});
			controlAch = controlThumbs[a].dom.parentNode;
			controlThb = controlThumbs[a].dom;
			controlAch.setAttribute("href", "javascript:;");
			controlAch.setAttribute("id", "controlsThumb_" + a);
			controlAch.index = a;
			lib.e.on(controlAch, "click", thumbClick, thumbs[a], true);
			lib.d.addClass(controlAch, "controlsThumb");
			controlThb.setAttribute("src", thumbs[a].getElementsByTagName("img")[0].src);
			thumbContDom.appendChild(controlAch);
		}
		events.xmlload.unsubscribe(initControlThumbs);
		events.xmlload.subscribe(initControlThumbs);
		events.flickrload.unsubscribe(initControlThumbs);
		events.flickrload.subscribe(initControlThumbs);
	}; // end initControlThumbs
	function displayUpdate(){
		var displayText = properties.slideShow.controlsText.display;
		var newText = "";
		var index = currentThumb.parentNode.index;
		var currentControlThumb = lib.d.get("controlsThumb_" + index); // the anchor
		var layInfo = getThumbLay(currentControlThumb);
		
		if (lastControlThumb){ lib.d.removeClass(lastControlThumb, "active"); }
		// need catch all for removing active class
		lib.d.addClass(currentControlThumb, "active");
		
		if (layInfo.thumbLay != 1){ // off screen right or left
			var layOffset = (thumbContDom.offsetlay || 0);
			var opposite = Number(String(layOffset).replace("-",""));
			var offset = (((layInfo.thumbx - layInfo.thumbw*2) - layInfo.contRange.l) + opposite) *-1;
			thumbContDom.offsetlay = offset;
			if (index == 0){ offset = 0; }
			var pan = new lib.a(thumbContDom, {marginLeft:{to:offset}}, 1, properties.easing);
			pan.animate();
		}
		newText = displayText.replace("{0}", Number(getThumbIndexFromId(currentThumb) + 1));
		newText = newText.replace("{1}", thumbs.length);
		displayDom.innerHTML = newText;
		playBtnDisplay();
		adjustControls();
		// set last
		lastControlThumb = currentControlThumb;
	}; // end displayUpdate
	function getThumbLay(currentControlThumb){		
		var contRange = {
			l:lib.d.getX(thumbContOuterDom),
			r:lib.d.getX(thumbContOuterDom) + (lib.d.getRegion(thumbContOuterDom).right - lib.d.getRegion(thumbContOuterDom).left),
			w:(lib.d.getRegion(thumbContOuterDom).right - lib.d.getRegion(thumbContOuterDom).left)
		};
		var thumbw = (lib.d.getRegion(currentControlThumb).right - lib.d.getRegion(currentControlThumb).left);
		var thumbx = lib.d.getX(currentControlThumb) + thumbw; // right side
		var thumbLay = 0; // 0 = outside left, 1 = in, 2 = outside right
		var layInfo = {thumbx:thumbx, thumbw:thumbw, contRange:contRange};
		
		if (thumbx < contRange.l){ thumbLay = 0; }
		if (thumbx >= contRange.r){ thumbLay = 2; }
		if ((thumbx - thumbw) > contRange.l && thumbx <= contRange.r){ thumbLay = 1; }
		layInfo.thumbLay = thumbLay;
		return layInfo;
	}; // end getThumbLay
	function adjustControls(){
		if (YAHOO.env.ua.ie < 7){  
			if (controlsDom){
				var top = (lib.d.getViewportHeight() - controlsDom.offsetHeight) + (lib.d.getDocumentScrollTop() - 10);
				lib.d.setY(controlsDom, top);
			}
		}
		else{
			var bottom = 10 - lib.d.getDocumentScrollTop();
			lib.d.setStyle(controlsDom, "bottom", bottom + "px");
		}
		var width = lib.d.getRegion(controlsDom).right - lib.d.getRegion(controlsDom).left;
		lib.d.setStyle(controlsDom, "margin-left", (width/2 * -1) + "px");
		// limit max width of controls
		//lib.d.setStyle(controlsDom, "max-width", lib.d.getViewportWidth() + "px");
	}; // end adjustControls
	function btnPlay(){
		if (lib.d.hasClass(playDom, "photoViewer-play")){
			that.play();
			insertStr(playDom, properties.slideShow.controlsText.pause);
			lib.d.replaceClass(playDom, "photoViewer-play", "photoViewer-pause");
		}
		else{
			that.pause();
			insertStr(playDom, properties.slideShow.controlsText.play);
			lib.d.replaceClass(playDom, "photoViewer-pause", "photoViewer-play");
		}
	}; // end btnPlay
	function playBtnDisplay(){
		if (properties.slideShow.state != 2){
			insertStr(playDom, properties.slideShow.controlsText.play);
			lib.d.replaceClass(playDom, "photoViewer-pause", "photoViewer-play");
		}
		else{
			insertStr(playDom, properties.slideShow.controlsText.pause);
			lib.d.replaceClass(playDom, "photoViewer-play", "photoViewer-pause");
		}
	}; // end playBtnDisplay
	function btnStop(){
		that.stop();
		insertStr(playDom, properties.slideShow.controlsText.play);
		lib.d.replaceClass(playDom, "photoViewer-pause", "photoViewer-play");
	}; // end btnStop
	function createViewer(){
		var buttonText = properties.buttonText ? properties.buttonText : defaultText;
		showcaseImage = document.createElement("img");
		lib.d.addClass(showcaseImage, "photoViewer-showcaseImage");
		lib.d.addClass(showcaseImage, "photoViewer-showcase");
		if (!viewerDom){
			// build viewer
			viewerDom = document.createElement("div");
			viewerDom.setAttribute("id", properties.id + "-viewer");
			lib.d.addClass(viewerDom, "photoViewer-viewer");
			// build mask
			maskDom = document.createElement("div");
			maskDom.setAttribute("id", properties.id + "-mask");
			lib.d.addClass(maskDom, "photoViewer-mask");
			lib.d.setStyle(maskDom, "z-index", YAHOO.photoViewer.zIndex - 1);
			lib.e.on(maskDom, "click", that.close);
			// build header
			headerDom = document.createElement("div");
			headerDom.setAttribute("id", properties.id + "-header");
			lib.d.addClass(headerDom, "photoViewer-header");
			// build title
			titleDom = document.createElement("h1");
			titleDom.setAttribute("id", properties.id + "-title");
			lib.d.addClass(titleDom, "photoViewer-title");
			// build close button
			closeDom = document.createElement("a");
			var closeText = buttonText.close ? buttonText.close : defaultText.close;
			insertStr(closeDom, closeText);
			closeDom.setAttribute("id", properties.id + "-close");
			closeDom.setAttribute("href", "javascript:;");
			lib.d.addClass(closeDom, "photoViewer-close");
			lib.e.on(closeDom, "click", that.close);
			// build body
			bodyDom = document.createElement("div");
			bodyDom.setAttribute("id", properties.id + "-body");
			lib.d.addClass(maskDom, "photoViewer-body");
			// build image container
			imageContDom = document.createElement("div");
			imageContDom.setAttribute("id", properties.id + "-imageCont");
			lib.d.addClass(imageContDom, "photoViewer-imageCont");
			// build despription
			descDom = document.createElement("div");
			descDom.setAttribute("id", properties.id + "-desc");
			lib.d.addClass(descDom, "photoViewer-desc");
			// build footer
			footerDom = document.createElement("div");
			footerDom.setAttribute("id", properties.id + "-footer");
			lib.d.addClass(footerDom, "photoViewer-footer");
			// build buttons
			// prev
			prevDom = document.createElement("a");
			var prevText = buttonText.prev ? buttonText.prev : defaultText.prev;
			insertStr(prevDom, prevText);
			prevDom.setAttribute("id", properties.id + "-prev");
			prevDom.setAttribute("href", "javascript:;");
			lib.d.addClass(prevDom, "photoViewer-prev");
			lib.e.on(prevDom, "click", that.prev);
			// next
			nextDom = document.createElement("a");
			var nextText = buttonText.next ? buttonText.next : defaultText.next;
			insertStr(nextDom, nextText);
			nextDom.setAttribute("id", properties.id + "-next");
			nextDom.setAttribute("href", "javascript:;");
			lib.d.addClass(nextDom, "photoViewer-next");
			lib.e.on(nextDom, "click", that.next);
			// append nodes
			headerDom.appendChild(titleDom);
			headerDom.appendChild(closeDom);
			bodyDom.appendChild(imageContDom);
			imageContDom.appendChild(showcaseImage);
			bodyDom.appendChild(descDom);
			descDom.appendChild(document.createElement("p")); // add paragraph
			footerDom.appendChild(prevDom);
			footerDom.appendChild(nextDom);
			if (properties.modal) { document.body.appendChild(maskDom); } // if modal switched on
			if (properties.position == "absolute" || !properties.container){
				document.body.appendChild(viewerDom);
			}
			else{
				lib.d.get(properties.container).appendChild(viewerDom);
			}
			// set up config
			// drag and drop
			if (properties.dragable){
				var dragObj = new YAHOO.photoViewer.DDOnTop(viewerDom.getAttribute("id"));
				dragObj.setHandleElId(titleDom.getAttribute("id"));
				lib.d.setStyle(titleDom, "cursor", "move");
				lib.e.on(viewerDom, "click", function(){
					lib.d.setStyle(this, "z-index", YAHOO.photoViewer.zIndex++);
				});
			}
		}
		var posXY = properties.xy ? properties.xy : null;
		var type, attr;
		if (properties.position == "absolute"){
			type = YAHOO.widget.Overlay;
			attr = { 
				xy:eval(posXY),
				fixedcenter:properties.fixedcenter,
				constraintoviewport:true,
				visible:false,  
				zIndex:YAHOO.photoViewer.zIndex, 
				effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:properties.fade} 
			};
		}
		else{
			type = YAHOO.widget.Module;
			attr = {visible:true};
			// set up positioning
			lib.d.setStyle(viewerDom, "position", "relative");
		}
		viewer =  new type(properties.id + "-viewer", attr); 
		viewer.setHeader(headerDom);
		viewer.setBody(bodyDom);
		viewer.setFooter(footerDom);
		viewer.render();
	}; // end createViewer
	function adjustPosition(){
		// mask adjustment
		var width = lib.d.getViewportWidth();
		var height = lib.d.getViewportHeight();
		lib.d.setStyle(maskDom, "width", width + "px");
		lib.d.setStyle(maskDom, "height", height + "px");
		lib.d.setStyle(maskDom, "top", lib.d.getDocumentScrollTop() + "px");
		lib.d.setStyle(maskDom, "left", lib.d.getDocumentScrollLeft() + "px");
	}; // end adjustPosition
	function isVisible(el){
		return viewer.cfg.config.visible.value;
	}; // end isVisible
	function insertStr(el, str, append){
		var append = append ? append : false;
		if (append) el.innerHTML += str;
		else el.innerHTML = str;
	}; // end insertStr
	function testForThumb(el){
		return lib.d.hasClass(el, "photoViewer");
	}; // end testForThumb
	function mid(str, start, len){
	    if (start < 0 || len < 0) return "";
	    var iEnd, iLen = String(str).length;
	    if (start + len > iLen)
	          iEnd = iLen;
	    else
	          iEnd = start + len;
	    return String(str).substring(start,iEnd);
	}; //end mid
	function createTemplate(){
		var templateProperties = YAHOO.photoViewer.config.viewers[properties.template].properties;
		var templateId = properties.id;
		delete properties.template;
		
		for (var a in templateProperties){
			if (!properties[a]){
				properties[a] = templateProperties[a]
			}
		}
		properties.id = templateId;
	}; // end createTemplate
	function beforeNext(onNext, index){
		if (!index) { index = null; }
		if (bodyDom && titleDom && imageContDom) {
			var domEls = [bodyDom, titleDom, imageContDom];
			for (var a = 0; a < domEls.length; a++) {
				var fade = new lib.a(domEls[a], {
					opacity: {
						to: 0
					}
				}, properties.fade, properties.easing);
				fade.animate();
			}
			fade.onComplete.unsubscribe((function(){onNext(index)}));
			fade.onComplete.subscribe((function(){onNext(index)}));
		}
		else { onNext(index); }
	}; // end beforeNext
};
/* Class extension
 */
YAHOO.photoViewer.DDOnTop = function(id, sGroup, config) {
    YAHOO.photoViewer.DDOnTop.superclass.constructor.apply(this, arguments);
};
YAHOO.extend(YAHOO.photoViewer.DDOnTop, YAHOO.util.DD, {
    startDrag: function(x, y) {
        var style = this.getEl().style;
        style.position = "absolute";
		style.zIndex = YAHOO.photoViewer.zIndex++;
    },
    endDrag: function(x, y){
    	//var pos = "[" + lib.e.getPageX(x) + "," + lib.e.getPageY(x) + "]";
    	var pos = "[" + lib.d.getRegion(this.getEl()).left + "," + lib.d.getRegion(this.getEl()).top + "]"; 
		var id = this.getEl().id.split("-");
		id.splice(id.length - 1, 1);
		id = id.join("-");
		YAHOO.photoViewer.controller.getViewer(id).setProperty("xy", pos);
    }
});

YAHOO.photoViewer.base.prototype.flickr = {
	/* Prototyping base class with methods that reflect the Flickr API <http://www.flickr.com/services/api/>
	 * The API method for this function is "flickr.photo.search"
	 * Config object passed in: {properties:properties, createPhoto:createPhoto, rsp:rsp}
	 * 		properties Object: This is the properties object set up in the photoViewer config and used to store, set and get data. You are required to return this object.
	 * 		createPhoto Function: This is the function you call to create a thumbnail. You are required to build the photo object and pass it in as the only argument.
	 *		rsp Object: JSON Object returned from the Flickr API call
	 */
	photos: {			
		search: function(config){
			/* Flickr API Params
			 * 		http://www.flickr.com/services/api/flickr.photos.search.html
			 */		
			return YAHOO.photoViewer.flickCommon.photosPattern(config.properties, config.createPhoto, config.rsp);
		} // end search
	}, // end photos
	people:{
		getPublicPhotos: function(config){
			/* Flickr API Params
			 * 		http://www.flickr.com/services/api/flickr.people.getPublicPhotos.html
			 */	
			return YAHOO.photoViewer.flickCommon.photosPattern(config.properties, config.createPhoto, config.rsp);
		} // end getPublicPhotos
	}, // end people
	interestingness:{
		getList: function(config){
			/* Flickr API Params
			 * 		http://www.flickr.com/services/api/flickr.interestingness.getList.html
			 */	
			return YAHOO.photoViewer.flickCommon.photosPattern(config.properties, config.createPhoto, config.rsp);
		} // end getList
	}, // end interestingness
	groups:{
		getPhotos: function(config){
			/* Flickr API Params
			 * 		http://www.flickr.com/services/api/flickr.groups.pools.getPhotos.html
			 */	
			return YAHOO.photoViewer.flickCommon.photosPattern(config.properties, config.createPhoto, config.rsp);
		} // end getPhotos
	} // end groups
};
YAHOO.photoViewer.flickCommon = {
	/* Patterns, re-usable components for Flickr API */
	photosPattern: function(properties, createPhoto, rsp){
		/* Common pattern used to create thumbs from a common JSON structure
		 * rsp.photo.photo [array]
		 */
		var photo = {};
		var thumbReplace = (properties.flickrApi.thumbSize == "square") ? "_s" : "_t";
		var photoNodes = rsp.photos.photo;
		var n, s;
		properties.flickrApi.response = rsp; // store response
		for (var i = 0; i < photoNodes.length; i++) {
			n = photoNodes[i];
			s = "http://farm" + n.farm + ".static.flickr.com/" + n.server + "/" + n.id + "_" + n.secret + thumbReplace + ".jpg";
			photo.thumbsource = s;
			photo.fullsource = s.replace(thumbReplace, "");
			photo.title = n.title;
			photo.description = "";
			createPhoto(photo);
		}
		return properties;
	} // end photosPattern
};
/* Singleton Object
 */

/* loading class
 * Used to control the loading mask
 */
YAHOO.photoViewer.loading = function(){
	var loadingScreen = null;
	var applyTo = null;
	var public = {
		on: function(config){
			/* config.applyTo = element to render loading over. */
			if (!config){ config = {}; }
			applyTo = config.applyTo ? lib.d.get(config.applyTo) : document.body;
			createLoadingScreen();
		},
		off: function(){
			lib.d.setStyle(loadingScreen, "display", "none");
		},
		destroy: function(){
			if (loadingScreen){
				loadingScreen.parentNode.removeChild(loadingScreen);
				loadingScreen = null;
			}
		}
	};
	// private
	function createLoadingScreen(){
		if (!loadingScreen){
			loadingScreen = document.createElement("div");
			loadingScreen.setAttribute("id", "photoViewer-loading");
			lib.d.addClass(loadingScreen, "photoViewer-loading");
			lib.d.setStyle(loadingScreen, "position", "absolute");
			lib.d.setStyle(loadingScreen, "display", "none");
			document.body.appendChild(loadingScreen);
		}
		position();
		lib.d.setStyle(loadingScreen, "display", "block");
		lib.d.setStyle(loadingScreen, "z-index", YAHOO.photoViewer.zIndex + 10);
	}; // end createLoadingScreen
	function position(){
		lib.d.setStyle(loadingScreen, "top", lib.d.getRegion(applyTo).top + "px");
		lib.d.setStyle(loadingScreen, "left", lib.d.getRegion(applyTo).left + "px");
		lib.d.setStyle(loadingScreen, "width", (lib.d.getRegion(applyTo).right - lib.d.getRegion(applyTo).left) + "px");
		lib.d.setStyle(loadingScreen, "height", (lib.d.getRegion(applyTo).bottom - lib.d.getRegion(applyTo).top) + "px");
		if (applyTo.tagName.toLowerCase() == "body"){
			if ((lib.d.getRegion(applyTo).bottom - lib.d.getRegion(applyTo).top) < lib.d.getViewportHeight()){
				lib.d.setStyle(loadingScreen, "height", lib.d.getViewportHeight() + "px");
			}
		}
	}; // end position
	// 
	return public;
}();
/* controller class
 * Used to controll all viewer instances
 */
YAHOO.photoViewer.controller = function(){
	var viewers = {};
	var public = {
		init: function(){
			for (var a in YAHOO.photoViewer.config.viewers){
				viewers[a] = new YAHOO.photoViewer.base();
				viewers[a].init(YAHOO.photoViewer.config.viewers[a].properties.id);
			}
		},
		getViewer: function(id){
			if (viewers[id]) return viewers[id];
		},
		getViewers: function(){
			return viewers;
		},
		removeViewer: function(id){
			delete viewers[id];
		},
		viewers:viewers
	};
	// private
	//
	return public;
}();
/* photoViewer level public properties
 */
YAHOO.photoViewer.zIndex = 1000;
/* The controller in init'd automatically and will in turn init all viewers set up in the config. If you do not set up any viewers
 * you can config them after run time and manually re-init the controller
 */
lib.e.onDOMReady(YAHOO.photoViewer.controller.init, YAHOO.photoViewer.controller, YAHOO.photoViewer.controller);