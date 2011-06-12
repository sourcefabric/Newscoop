// MicroAJAX: http://www.blackmac.de/index.php?/archives/31-Smallest-JavaScript-AJAX-library-ever!.html
function microAjax(url,cF){
this.bF=function(caller,object){
return function(){
return caller.apply(object,new Array(object));
}}
this.sC=function(object) {
if (this.r.readyState==4) {
this.cF(this.r.responseText);
}}
this.gR=function(){
if (window.ActiveXObject)
return new ActiveXObject('Microsoft.XMLHTTP');
else if (window.XMLHttpRequest)
return new XMLHttpRequest();
else
return false;
}
if (arguments[2]) this.pb=arguments[2];
else this.pb="";
this.cF=cF;
this.url=url;
this.r=this.gR();
if(this.r){
this.r.onreadystatechange=this.bF(this.sC,this);
if(this.pb!=""){
this.r.open("POST",url,true);
this.r.setRequestHeader('Content-type','application/x-www-form-urlencoded');
this.r.setRequestHeader('Connection','close');
}else{
this.r.open("GET",url,true);
}
this.r.send(this.pb);
}}
