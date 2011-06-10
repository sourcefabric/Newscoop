sfHover = function() {
	if (!document.getElementsByTagName) return false;
	var sfEls = document.getElementById("nav").getElementsByTagName("li");

	// If you have two menus, remove comment below and change secnav to your class //
	// var sfEls1 = document.getElementById("secnav").getElementsByTagName("li");

	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}

	// If you have two menus, remove comments below //
	//for (var i=0; i<sfEls1.length; i++) {
	//	sfEls1[i].onmouseover=function() {
	//		this.className+=" sfhover1";
	//	}
	//	sfEls1[i].onmouseout=function() {
	//		this.className=this.className.replace(new RegExp(" sfhover1\\b"), "");
	//	}
	//}

}
if (window.attachEvent) window.attachEvent("onload", sfHover);