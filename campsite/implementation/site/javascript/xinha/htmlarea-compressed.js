HTMLArea.version={"Release":"Trunk","Head":"$HeadURL: http://svn.xinha.python-hosting.com/trunk/htmlarea.js $".replace(/^[^:]*: (.*) \$$/,"$1"),"Date":"$LastChangedDate: 2006-05-08 21:29:49 +1200 (Mon, 08 May 2006) $".replace(/^[^:]*: ([0-9-]*) ([0-9:]*) ([+0-9]*) \((.*)\) \$/,"$4 $2 $3"),"Revision":"$LastChangedRevision: 513 $".replace(/^[^:]*: (.*) \$$/,"$1"),"RevisionBy":"$LastChangedBy: gogo $".replace(/^[^:]*: (.*) \$$/,"$1")};
if(typeof _editor_url=="string"){
_editor_url=_editor_url.replace(/\x2f*$/,"/");
}else{
alert("WARNING: _editor_url is not set!  You should set this variable to the editor files path; it should preferably be an absolute path, like in '/htmlarea/', but it can be relative if you prefer.  Further we will try to load the editor files correctly but we'll probably fail.");
_editor_url="";
}
if(typeof _editor_lang=="string"){
_editor_lang=_editor_lang.toLowerCase();
}else{
_editor_lang="en";
}
if(typeof _editor_skin!=="string"){
_editor_skin="";
}
var __htmlareas=[];
HTMLArea.agt=navigator.userAgent.toLowerCase();
HTMLArea.is_ie=((HTMLArea.agt.indexOf("msie")!=-1)&&(HTMLArea.agt.indexOf("opera")==-1));
HTMLArea.is_opera=(HTMLArea.agt.indexOf("opera")!=-1);
HTMLArea.is_mac=(HTMLArea.agt.indexOf("mac")!=-1);
HTMLArea.is_mac_ie=(HTMLArea.is_ie&&HTMLArea.is_mac);
HTMLArea.is_win_ie=(HTMLArea.is_ie&&!HTMLArea.is_mac);
HTMLArea.is_gecko=(navigator.product=="Gecko");
function HTMLArea(_1,_2){
if(!_1){
throw ("Tried to create HTMLArea without textarea specified.");
}
if(HTMLArea.checkSupportedBrowser()){
if(typeof _2=="undefined"){
this.config=new HTMLArea.Config();
}else{
this.config=_2;
}
this._htmlArea=null;
if(typeof _1!="object"){
_1=HTMLArea.getElementById("textarea",_1);
}
this._textArea=_1;
this._initial_ta_size={w:_1.style.width?_1.style.width:(_1.offsetWidth?(_1.offsetWidth+"px"):(_1.cols+"em")),h:_1.style.height?_1.style.height:(_1.offsetHeight?(_1.offsetHeight+"px"):(_1.rows+"em"))};
if(this.config.showLoading){
var _3=document.createElement("div");
_3.id="loading_"+_1.name;
_3.className="loading";
try{
_3.style.width=_1.offsetWidth+"px";
}
catch(ex){
_3.style.width=this._initial_ta_size.w;
}
_3.style.left=HTMLArea.findPosX(_1)+"px";
_3.style.top=(HTMLArea.findPosY(_1)+parseInt(this._initial_ta_size.h,10)/2)+"px";
var _4=document.createElement("div");
_4.className="loading_main";
_4.id="loading_main_"+_1.name;
_4.appendChild(document.createTextNode(HTMLArea._lc("Loading in progress. Please wait !")));
var _5=document.createElement("div");
_5.className="loading_sub";
_5.id="loading_sub_"+_1.name;
_5.appendChild(document.createTextNode(HTMLArea._lc("Constructing main object")));
_3.appendChild(_4);
_3.appendChild(_5);
document.body.appendChild(_3);
this.setLoadingMessage("Constructing object");
}
this._editMode="wysiwyg";
this.plugins={};
this._timerToolbar=null;
this._timerUndo=null;
this._undoQueue=[this.config.undoSteps];
this._undoPos=-1;
this._customUndo=true;
this._mdoc=document;
this.doctype="";
this.__htmlarea_id_num=__htmlareas.length;
__htmlareas[this.__htmlarea_id_num]=this;
this._notifyListeners={};
var _6={right:{on:true,container:document.createElement("td"),panels:[]},left:{on:true,container:document.createElement("td"),panels:[]},top:{on:true,container:document.createElement("td"),panels:[]},bottom:{on:true,container:document.createElement("td"),panels:[]}};
for(var i in _6){
if(!_6[i].container){
continue;
}
_6[i].div=_6[i].container;
_6[i].container.className="panels "+i;
HTMLArea.freeLater(_6[i],"container");
HTMLArea.freeLater(_6[i],"div");
}
this._panels=_6;
HTMLArea.freeLater(this,"_textArea");
}
}
HTMLArea.onload=function(){
};
HTMLArea.init=function(){
HTMLArea.onload();
};
HTMLArea.RE_tagName=/(<\/|<)\s*([^ \t\n>]+)/ig;
HTMLArea.RE_doctype=/(<!doctype((.|\n)*?)>)\n?/i;
HTMLArea.RE_head=/<head>((.|\n)*?)<\/head>/i;
HTMLArea.RE_body=/<body[^>]*>((.|\n|\r|\t)*?)<\/body>/i;
HTMLArea.RE_Specials=/([\/\^$*+?.()|{}[\]])/g;
HTMLArea.RE_email=/[_a-zA-Z\d\-\.]{3,}@[_a-zA-Z\d\-]{2,}(\.[_a-zA-Z\d\-]{2,})+/i;
HTMLArea.RE_url=/(https?:\/\/)?(([a-z0-9_]+:[a-z0-9_]+@)?[a-z0-9_-]{2,}(\.[a-z0-9_-]{2,}){2,}(:[0-9]+)?(\/\S+)*)/i;
HTMLArea.Config=function(){
var _8=this;
this.version=HTMLArea.version.Revision;
this.width="auto";
this.height="auto";
this.sizeIncludesBars=true;
this.sizeIncludesPanels=true;
this.panel_dimensions={left:"200px",right:"200px",top:"100px",bottom:"100px"};
this.statusBar=true;
this.htmlareaPaste=false;
this.mozParaHandler="best";
this.undoSteps=20;
this.undoTimeout=500;
this.changeJustifyWithDirection=false;
this.fullPage=false;
this.pageStyle="";
this.pageStyleSheets=[];
this.baseHref=null;
this.stripBaseHref=true;
this.stripSelfNamedAnchors=true;
this.only7BitPrintablesInURLs=true;
this.sevenBitClean=false;
this.specialReplacements={};
this.killWordOnPaste=true;
this.makeLinkShowsTarget=true;
this.charSet=HTMLArea.is_gecko?document.characterSet:document.charset;
this.imgURL="images/";
this.popupURL="popups/";
this.htmlRemoveTags=null;
this.flowToolbars=true;
this.showLoading=false;
this.toolbar=[["popupeditor"],["separator","formatblock","fontname","fontsize","bold","italic","underline","strikethrough"],["separator","forecolor","hilitecolor","textindicator"],["separator","subscript","superscript"],["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],["separator","insertorderedlist","insertunorderedlist","outdent","indent"],["separator","inserthorizontalrule","createlink","insertimage","inserttable"],["linebreak","separator","undo","redo","selectall","print"],(HTMLArea.is_gecko?[]:["cut","copy","paste","overwrite","saveas"]),["separator","killword","clearfonts","removeformat","toggleborders","splitblock","lefttoright","righttoleft"],["separator","htmlmode","showhelp","about"]];
this.fontname={"&mdash; font &mdash;":"","Arial":"arial,helvetica,sans-serif","Courier New":"courier new,courier,monospace","Georgia":"georgia,times new roman,times,serif","Tahoma":"tahoma,arial,helvetica,sans-serif","Times New Roman":"times new roman,times,serif","Verdana":"verdana,arial,helvetica,sans-serif","impact":"impact","WingDings":"wingdings"};
this.fontsize={"&mdash; size &mdash;":"","1 (8 pt)":"1","2 (10 pt)":"2","3 (12 pt)":"3","4 (14 pt)":"4","5 (18 pt)":"5","6 (24 pt)":"6","7 (36 pt)":"7"};
this.formatblock={"&mdash; format &mdash;":"","Heading 1":"h1","Heading 2":"h2","Heading 3":"h3","Heading 4":"h4","Heading 5":"h5","Heading 6":"h6","Normal":"p","Address":"address","Formatted":"pre"};
this.customSelects={};
function cut_copy_paste(e,cmd,obj){
e.execCommand(cmd);
}
this.debug=true;
this.URIs={"blank":"popups/blank.html","link":"link.html","insert_image":"insert_image.html","insert_table":"insert_table.html","select_color":"select_color.html","about":"about.html","help":"editor_help.html"};
this.btnList={bold:["Bold",HTMLArea._lc({key:"button_bold",string:["ed_buttons_main.gif",3,2]},"HTMLArea"),false,function(e){
e.execCommand("bold");
}],italic:["Italic",HTMLArea._lc({key:"button_italic",string:["ed_buttons_main.gif",2,2]},"HTMLArea"),false,function(e){
e.execCommand("italic");
}],underline:["Underline",HTMLArea._lc({key:"button_underline",string:["ed_buttons_main.gif",2,0]},"HTMLArea"),false,function(e){
e.execCommand("underline");
}],strikethrough:["Strikethrough",HTMLArea._lc({key:"button_strikethrough",string:["ed_buttons_main.gif",3,0]},"HTMLArea"),false,function(e){
e.execCommand("strikethrough");
}],subscript:["Subscript",HTMLArea._lc({key:"button_subscript",string:["ed_buttons_main.gif",3,1]},"HTMLArea"),false,function(e){
e.execCommand("subscript");
}],superscript:["Superscript",HTMLArea._lc({key:"button_superscript",string:["ed_buttons_main.gif",2,1]},"HTMLArea"),false,function(e){
e.execCommand("superscript");
}],justifyleft:["Justify Left",["ed_buttons_main.gif",0,0],false,function(e){
e.execCommand("justifyleft");
}],justifycenter:["Justify Center",["ed_buttons_main.gif",1,1],false,function(e){
e.execCommand("justifycenter");
}],justifyright:["Justify Right",["ed_buttons_main.gif",1,0],false,function(e){
e.execCommand("justifyright");
}],justifyfull:["Justify Full",["ed_buttons_main.gif",0,1],false,function(e){
e.execCommand("justifyfull");
}],orderedlist:["Ordered List",["ed_buttons_main.gif",0,3],false,function(e){
e.execCommand("insertorderedlist");
}],unorderedlist:["Bulleted List",["ed_buttons_main.gif",1,3],false,function(e){
e.execCommand("insertunorderedlist");
}],insertorderedlist:["Ordered List",["ed_buttons_main.gif",0,3],false,function(e){
e.execCommand("insertorderedlist");
}],insertunorderedlist:["Bulleted List",["ed_buttons_main.gif",1,3],false,function(e){
e.execCommand("insertunorderedlist");
}],outdent:["Decrease Indent",["ed_buttons_main.gif",1,2],false,function(e){
e.execCommand("outdent");
}],indent:["Increase Indent",["ed_buttons_main.gif",0,2],false,function(e){
e.execCommand("indent");
}],forecolor:["Font Color",["ed_buttons_main.gif",3,3],false,function(e){
e.execCommand("forecolor");
}],hilitecolor:["Background Color",["ed_buttons_main.gif",2,3],false,function(e){
e.execCommand("hilitecolor");
}],undo:["Undoes your last action",["ed_buttons_main.gif",4,2],false,function(e){
e.execCommand("undo");
}],redo:["Redoes your last action",["ed_buttons_main.gif",5,2],false,function(e){
e.execCommand("redo");
}],cut:["Cut selection",["ed_buttons_main.gif",5,0],false,cut_copy_paste],copy:["Copy selection",["ed_buttons_main.gif",4,0],false,cut_copy_paste],paste:["Paste from clipboard",["ed_buttons_main.gif",4,1],false,cut_copy_paste],selectall:["Select all","ed_selectall.gif",false,function(e){
e.execCommand("selectall");
}],inserthorizontalrule:["Horizontal Rule",["ed_buttons_main.gif",6,0],false,function(e){
e.execCommand("inserthorizontalrule");
}],createlink:["Insert Web Link",["ed_buttons_main.gif",6,1],false,function(e){
e._createLink();
}],insertimage:["Insert/Modify Image",["ed_buttons_main.gif",6,3],false,function(e){
e.execCommand("insertimage");
}],inserttable:["Insert Table",["ed_buttons_main.gif",6,2],false,function(e){
e.execCommand("inserttable");
}],htmlmode:["Toggle HTML Source",["ed_buttons_main.gif",7,0],true,function(e){
e.execCommand("htmlmode");
}],toggleborders:["Toggle Borders",["ed_buttons_main.gif",7,2],false,function(e){
e._toggleBorders();
}],print:["Print document",["ed_buttons_main.gif",8,1],false,function(e){
if(HTMLArea.is_gecko){
e._iframe.contentWindow.print();
}else{
e.focusEditor();
print();
}
}],saveas:["Save as","ed_saveas.gif",false,function(e){
e.execCommand("saveas",false,"noname.htm");
}],about:["About this editor",["ed_buttons_main.gif",8,2],true,function(e){
e.execCommand("about");
}],showhelp:["Help using editor",["ed_buttons_main.gif",9,2],true,function(e){
e.execCommand("showhelp");
}],splitblock:["Split Block","ed_splitblock.gif",false,function(e){
e._splitBlock();
}],lefttoright:["Direction left to right",["ed_buttons_main.gif",0,4],false,function(e){
e.execCommand("lefttoright");
}],righttoleft:["Direction right to left",["ed_buttons_main.gif",1,4],false,function(e){
e.execCommand("righttoleft");
}],overwrite:["Insert/Overwrite","ed_overwrite.gif",false,function(e){
e.execCommand("overwrite");
}],wordclean:["MS Word Cleaner",["ed_buttons_main.gif",5,3],false,function(e){
e._wordClean();
}],clearfonts:["Clear Inline Font Specifications",["ed_buttons_main.gif",5,4],true,function(e){
e._clearFonts();
}],removeformat:["Remove formatting",["ed_buttons_main.gif",4,4],false,function(e){
e.execCommand("removeformat");
}],killword:["Clear MSOffice tags",["ed_buttons_main.gif",4,3],false,function(e){
e.execCommand("killword");
}]};
for(var i in this.btnList){
var btn=this.btnList[i];
if(typeof btn!="object"){
continue;
}
if(typeof btn[1]!="string"){
btn[1][0]=_editor_url+this.imgURL+btn[1][0];
}else{
btn[1]=_editor_url+this.imgURL+btn[1];
}
btn[0]=HTMLArea._lc(btn[0]);
}
};
HTMLArea.Config.prototype.registerButton=function(id,_14,_15,_16,_17,_18){
var _19;
if(typeof id=="string"){
_19=id;
}else{
if(typeof id=="object"){
_19=id.id;
}else{
alert("ERROR [HTMLArea.Config::registerButton]:\ninvalid arguments");
return false;
}
}
switch(typeof id){
case "string":
this.btnList[id]=[_14,_15,_16,_17,_18];
break;
case "object":
this.btnList[id.id]=[id.tooltip,id.image,id.textMode,id.action,id.context];
break;
}
};
HTMLArea.prototype.registerPanel=function(_20,_21){
if(!_20){
_20="right";
}
this.setLoadingMessage("Register panel "+_20);
var _22=this.addPanel(_20);
if(_21){
_21.drawPanelIn(_22);
}
};
HTMLArea.Config.prototype.registerDropdown=function(_23){
this.customSelects[_23.id]=_23;
};
HTMLArea.Config.prototype.hideSomeButtons=function(_24){
var _25=this.toolbar;
for(var i=_25.length;--i>=0;){
var _26=_25[i];
for(var j=_26.length;--j>=0;){
if(_24.indexOf(" "+_26[j]+" ")>=0){
var len=1;
if(/separator|space/.test(_26[j+1])){
len=2;
}
_26.splice(j,len);
}
}
}
};
HTMLArea.Config.prototype.addToolbarElement=function(id,_29,_30){
var _31=this.toolbar;
var a,i,j,o,sid;
var _33=false;
var _34=false;
var _35=0;
var _36=0;
var _37=0;
var _38=false;
var _39=false;
if((id&&typeof id=="object")&&(id.constructor==Array)){
_33=true;
}
if((_29&&typeof _29=="object")&&(_29.constructor==Array)){
_34=true;
_35=_29.length;
}
if(_33){
for(i=0;i<id.length;++i){
if((id[i]!="separator")&&(id[i].indexOf("T[")!==0)){
sid=id[i];
}
}
}else{
sid=id;
}
for(i=0;!_38&&!_39&&i<_31.length;++i){
a=_31[i];
for(j=0;!_39&&j<a.length;++j){
if(a[i]==sid){
_38=true;
break;
}
if(_34){
for(o=0;o<_35;++o){
if(a[j]==_29[o]){
if(o===0){
_39=true;
j--;
break;
}else{
_37=i;
_36=j;
_35=o;
}
}
}
}else{
if(a[j]==_29){
_39=true;
break;
}
}
}
}
if(!_38){
if(!_39&&_34){
if(_29.length!=_35){
j=_36;
a=_31[_37];
_39=true;
}
}
if(_39){
if(_30===0){
if(_33){
a[j]=id[id.length-1];
for(i=id.length-1;--i>=0;){
a.splice(j,0,id[i]);
}
}else{
a[j]=id;
}
}else{
if(_30<0){
j=j+_30+1;
}else{
if(_30>0){
j=j+_30;
}
}
if(_33){
for(i=id.length;--i>=0;){
a.splice(j,0,id[i]);
}
}else{
a.splice(j,0,id);
}
}
}else{
_31[0].splice(0,0,"separator");
if(_33){
for(i=id.length;--i>=0;){
_31[0].splice(0,0,id[i]);
}
}else{
_31[0].splice(0,0,id);
}
}
}
};
HTMLArea.Config.prototype.removeToolbarElement=HTMLArea.Config.prototype.hideSomeButtons;
HTMLArea.replaceAll=function(_40){
var tas=document.getElementsByTagName("textarea");
for(var i=tas.length;i>0;(new HTMLArea(tas[--i],_40)).generate()){
}
};
HTMLArea.replace=function(id,_42){
var ta=HTMLArea.getElementById("textarea",id);
return ta?(new HTMLArea(ta,_42)).generate():null;
};
HTMLArea.prototype._createToolbar=function(){
this.setLoadingMessage("Create Toolbar");
var _44=this;
var _45=document.createElement("div");
this._toolBar=this._toolbar=_45;
_45.className="toolbar";
_45.unselectable="1";
HTMLArea.freeLater(this,"_toolBar");
HTMLArea.freeLater(this,"_toolbar");
var _46=null;
var _47={};
this._toolbarObjects=_47;
this._createToolbar1(_44,_45,_47);
this._htmlArea.appendChild(_45);
return _45;
};
HTMLArea.prototype._setConfig=function(_48){
this.config=_48;
};
HTMLArea.prototype._addToolbar=function(){
this._createToolbar1(this,this._toolbar,this._toolbarObjects);
};
HTMLArea._createToolbarBreakingElement=function(){
var brk=document.createElement("div");
brk.style.height="1px";
brk.style.width="1px";
brk.style.lineHeight="1px";
brk.style.fontSize="1px";
brk.style.clear="both";
return brk;
};
HTMLArea.prototype._createToolbar1=function(_50,_51,_52){
var _53;
if(_50.config.flowToolbars){
_51.appendChild(HTMLArea._createToolbarBreakingElement());
}
function newLine(){
if(typeof _53!="undefined"&&_53.childNodes.length===0){
return;
}
var _54=document.createElement("table");
_54.border="0px";
_54.cellSpacing="0px";
_54.cellPadding="0px";
if(_50.config.flowToolbars){
if(HTMLArea.is_ie){
_54.style.styleFloat="left";
}else{
_54.style.cssFloat="left";
}
}
_51.appendChild(_54);
var _55=document.createElement("tbody");
_54.appendChild(_55);
_53=document.createElement("tr");
_55.appendChild(_53);
_54.className="toolbarRow";
}
newLine();
function setButtonStatus(id,_56){
var _57=this[id];
var el=this.element;
if(_57!=_56){
switch(id){
case "enabled":
if(_56){
HTMLArea._removeClass(el,"buttonDisabled");
el.disabled=false;
}else{
HTMLArea._addClass(el,"buttonDisabled");
el.disabled=true;
}
break;
case "active":
if(_56){
HTMLArea._addClass(el,"buttonPressed");
}else{
HTMLArea._removeClass(el,"buttonPressed");
}
break;
}
this[id]=_56;
}
}
function createSelect(txt){
var _60=null;
var el=null;
var cmd=null;
var _61=_50.config.customSelects;
var _62=null;
var _63="";
switch(txt){
case "fontsize":
case "fontname":
case "formatblock":
_60=_50.config[txt];
cmd=txt;
break;
default:
cmd=txt;
var _64=_61[cmd];
if(typeof _64!="undefined"){
_60=_64.options;
_62=_64.context;
if(typeof _64.tooltip!="undefined"){
_63=_64.tooltip;
}
}else{
alert("ERROR [createSelect]:\nCan't find the requested dropdown definition");
}
break;
}
if(_60){
el=document.createElement("select");
el.title=_63;
var obj={name:txt,element:el,enabled:true,text:false,cmd:cmd,state:setButtonStatus,context:_62};
HTMLArea.freeLater(obj);
_52[txt]=obj;
for(var i in _60){
if(typeof (_60[i])!="string"){
continue;
}
var op=document.createElement("option");
op.innerHTML=HTMLArea._lc(i);
op.value=_60[i];
el.appendChild(op);
}
HTMLArea._addEvent(el,"change",function(){
_50._comboSelected(el,txt);
});
}
return el;
}
function createButton(txt){
var el,btn,obj=null;
switch(txt){
case "separator":
if(_50.config.flowToolbars){
newLine();
}
el=document.createElement("div");
el.className="separator";
break;
case "space":
el=document.createElement("div");
el.className="space";
break;
case "linebreak":
newLine();
return false;
case "textindicator":
el=document.createElement("div");
el.appendChild(document.createTextNode("A"));
el.className="indicator";
el.title=HTMLArea._lc("Current style");
obj={name:txt,element:el,enabled:true,active:false,text:false,cmd:"textindicator",state:setButtonStatus};
HTMLArea.freeLater(obj);
_52[txt]=obj;
break;
default:
btn=_50.config.btnList[txt];
}
if(!el&&btn){
el=document.createElement("a");
el.style.display="block";
el.href="javascript:void(0)";
el.style.textDecoration="none";
el.title=btn[0];
el.className="button";
el.style.direction="ltr";
obj={name:txt,element:el,enabled:true,active:false,text:btn[2],cmd:btn[3],state:setButtonStatus,context:btn[4]||null};
HTMLArea.freeLater(obj);
_52[txt]=obj;
el.ondrag=function(){
return false;
};
HTMLArea._addEvent(el,"mouseout",function(ev){
if(obj.enabled){
HTMLArea._removeClass(el,"buttonActive");
if(obj.active){
HTMLArea._addClass(el,"buttonPressed");
}
}
});
HTMLArea._addEvent(el,"mousedown",function(ev){
if(obj.enabled){
HTMLArea._addClass(el,"buttonActive");
HTMLArea._removeClass(el,"buttonPressed");
HTMLArea._stopEvent(HTMLArea.is_ie?window.event:ev);
}
});
HTMLArea._addEvent(el,"click",function(ev){
if(obj.enabled){
HTMLArea._removeClass(el,"buttonActive");
if(HTMLArea.is_gecko){
_50.activateEditor();
}
obj.cmd(_50,obj.name,obj);
HTMLArea._stopEvent(HTMLArea.is_ie?window.event:ev);
}
});
var _67=HTMLArea.makeBtnImg(btn[1]);
var img=_67.firstChild;
el.appendChild(_67);
obj.imgel=img;
obj.swapImage=function(_69){
if(typeof _69!="string"){
img.src=_69[0];
img.style.position="relative";
img.style.top=_69[2]?("-"+(18*(_69[2]+1))+"px"):"-18px";
img.style.left=_69[1]?("-"+(18*(_69[1]+1))+"px"):"-18px";
}else{
obj.imgel.src=_69;
img.style.top="0px";
img.style.left="0px";
}
};
}else{
if(!el){
el=createSelect(txt);
}
}
return el;
}
var _70=true;
for(var i=0;i<this.config.toolbar.length;++i){
if(!_70){
}else{
_70=false;
}
if(this.config.toolbar[i]===null){
this.config.toolbar[i]=["separator"];
}
var _71=this.config.toolbar[i];
for(var j=0;j<_71.length;++j){
var _72=_71[j];
var _73;
if(/^([IT])\[(.*?)\]/.test(_72)){
var _74=RegExp.$1=="I";
var _75=RegExp.$2;
if(_74){
_75=HTMLArea._lc(_75);
}
_73=document.createElement("td");
_53.appendChild(_73);
_73.className="label";
_73.innerHTML=_75;
}else{
if(typeof _72!="function"){
var _76=createButton(_72);
if(_76){
_73=document.createElement("td");
_73.className="toolbarElement";
_53.appendChild(_73);
_73.appendChild(_76);
}else{
if(_76===null){
alert("FIXME: Unknown toolbar item: "+_72);
}
}
}
}
}
}
if(_50.config.flowToolbars){
_51.appendChild(HTMLArea._createToolbarBreakingElement());
}
return _51;
};
var use_clone_img=false;
HTMLArea.makeBtnImg=function(_77,doc){
if(!doc){
doc=document;
}
if(!doc._htmlareaImgCache){
doc._htmlareaImgCache={};
HTMLArea.freeLater(doc._htmlareaImgCache);
}
var _79=null;
if(HTMLArea.is_ie&&((!doc.compatMode)||(doc.compatMode&&doc.compatMode=="BackCompat"))){
_79=doc.createElement("span");
}else{
_79=doc.createElement("div");
_79.style.position="relative";
}
_79.style.overflow="hidden";
_79.style.width="18px";
_79.style.height="18px";
_79.className="buttonImageContainer";
var img=null;
if(typeof _77=="string"){
if(doc._htmlareaImgCache[_77]){
img=doc._htmlareaImgCache[_77].cloneNode();
}else{
img=doc.createElement("img");
img.src=_77;
img.style.width="18px";
img.style.height="18px";
if(use_clone_img){
doc._htmlareaImgCache[_77]=img.cloneNode();
}
}
}else{
if(doc._htmlareaImgCache[_77[0]]){
img=doc._htmlareaImgCache[_77[0]].cloneNode();
}else{
img=doc.createElement("img");
img.src=_77[0];
img.style.position="relative";
if(use_clone_img){
doc._htmlareaImgCache[_77[0]]=img.cloneNode();
}
}
img.style.top=_77[2]?("-"+(18*(_77[2]+1))+"px"):"-18px";
img.style.left=_77[1]?("-"+(18*(_77[1]+1))+"px"):"-18px";
}
_79.appendChild(img);
return _79;
};
HTMLArea.prototype._createStatusBar=function(){
this.setLoadingMessage("Create StatusBar");
var _80=document.createElement("div");
_80.className="statusBar";
this._statusBar=_80;
HTMLArea.freeLater(this,"_statusBar");
var div=document.createElement("span");
div.className="statusBarTree";
div.innerHTML=HTMLArea._lc("Path")+": ";
this._statusBarTree=div;
HTMLArea.freeLater(this,"_statusBarTree");
this._statusBar.appendChild(div);
div=document.createElement("span");
div.innerHTML=HTMLArea._lc("You are in TEXT MODE.  Use the [<>] button to switch back to WYSIWYG.");
div.style.display="none";
this._statusBarTextMode=div;
HTMLArea.freeLater(this,"_statusBarTextMode");
this._statusBar.appendChild(div);
if(!this.config.statusBar){
_80.style.display="none";
}
return _80;
};
HTMLArea.prototype.generate=function(){
var i;
var _82=this;
this.setLoadingMessage("Generate Xinha object");
if(typeof Dialog=="undefined"){
HTMLArea._loadback(_editor_url+"dialog.js",function(){
_82.generate();
});
return false;
}
if(typeof HTMLArea.Dialog=="undefined"){
HTMLArea._loadback(_editor_url+"inline-dialog.js",function(){
_82.generate();
});
return false;
}
if(typeof PopupWin=="undefined"){
HTMLArea._loadback(_editor_url+"popupwin.js",function(){
_82.generate();
});
return false;
}
if(_editor_skin!==""){
var _83=false;
var _84=document.getElementsByTagName("head")[0];
var _85=document.getElementsByTagName("link");
for(i=0;i<_85.length;i++){
if((_85[i].rel=="stylesheet")&&(_85[i].href==_editor_url+"skins/"+_editor_skin+"/skin.css")){
_83=true;
}
}
if(!_83){
var _86=document.createElement("link");
_86.type="text/css";
_86.href=_editor_url+"skins/"+_editor_skin+"/skin.css";
_86.rel="stylesheet";
_84.appendChild(_86);
}
}
var _87=_82.config.toolbar;
for(i=_87.length;--i>=0;){
for(var j=_87[i].length;--j>=0;){
if(_87[i][j]=="popupeditor"){
if(typeof FullScreen=="undefined"){
HTMLArea.loadPlugin("FullScreen",function(){
_82.generate();
});
return false;
}
_82.registerPlugin("FullScreen");
}
}
}
if(HTMLArea.is_gecko&&_82.config.mozParaHandler=="best"){
if(typeof EnterParagraphs=="undefined"){
HTMLArea.loadPlugin("EnterParagraphs",function(){
_82.generate();
});
return false;
}
_82.registerPlugin("EnterParagraphs");
}
this._framework={"table":document.createElement("table"),"tbody":document.createElement("tbody"),"tb_row":document.createElement("tr"),"tb_cell":document.createElement("td"),"tp_row":document.createElement("tr"),"tp_cell":this._panels.top.container,"ler_row":document.createElement("tr"),"lp_cell":this._panels.left.container,"ed_cell":document.createElement("td"),"rp_cell":this._panels.right.container,"bp_row":document.createElement("tr"),"bp_cell":this._panels.bottom.container,"sb_row":document.createElement("tr"),"sb_cell":document.createElement("td")};
HTMLArea.freeLater(this._framework);
var fw=this._framework;
fw.table.border="0";
fw.table.cellPadding="0";
fw.table.cellSpacing="0";
fw.tb_row.style.verticalAlign="top";
fw.tp_row.style.verticalAlign="top";
fw.ler_row.style.verticalAlign="top";
fw.bp_row.style.verticalAlign="top";
fw.sb_row.style.verticalAlign="top";
fw.ed_cell.style.position="relative";
fw.tb_row.appendChild(fw.tb_cell);
fw.tb_cell.colSpan=3;
fw.tp_row.appendChild(fw.tp_cell);
fw.tp_cell.colSpan=3;
fw.ler_row.appendChild(fw.lp_cell);
fw.ler_row.appendChild(fw.ed_cell);
fw.ler_row.appendChild(fw.rp_cell);
fw.bp_row.appendChild(fw.bp_cell);
fw.bp_cell.colSpan=3;
fw.sb_row.appendChild(fw.sb_cell);
fw.sb_cell.colSpan=3;
fw.tbody.appendChild(fw.tb_row);
fw.tbody.appendChild(fw.tp_row);
fw.tbody.appendChild(fw.ler_row);
fw.tbody.appendChild(fw.bp_row);
fw.tbody.appendChild(fw.sb_row);
fw.table.appendChild(fw.tbody);
var _89=this._framework.table;
this._htmlArea=_89;
HTMLArea.freeLater(this,"_htmlArea");
_89.className="htmlarea";
this._framework.tb_cell.appendChild(this._createToolbar());
var _90=document.createElement("iframe");
_90.src=_editor_url+_82.config.URIs.blank;
this._framework.ed_cell.appendChild(_90);
this._iframe=_90;
this._iframe.className="xinha_iframe";
HTMLArea.freeLater(this,"_iframe");
var _91=this._createStatusBar();
this._framework.sb_cell.appendChild(_91);
var _92=this._textArea;
_92.parentNode.insertBefore(_89,_92);
_92.className="xinha_textarea";
HTMLArea.removeFromParent(_92);
this._framework.ed_cell.appendChild(_92);
if(_92.form){
HTMLArea.prependDom0Event(this._textArea.form,"submit",function(){
_82._textArea.value=_82.outwardHtml(_82.getHTML());
return true;
});
var _93=_92.value;
HTMLArea.prependDom0Event(this._textArea.form,"reset",function(){
_82.setHTML(_82.inwardHtml(_93));
_82.updateToolbar();
return true;
});
}
HTMLArea.prependDom0Event(window,"unload",function(){
_92.value=_82.outwardHtml(_82.getHTML());
return true;
});
_92.style.display="none";
_82.initSize();
_82._iframeLoadDone=false;
HTMLArea._addEvent(this._iframe,"load",function(e){
if(!_82._iframeLoadDone){
_82._iframeLoadDone=true;
_82.initIframe();
}
return true;
});
};
HTMLArea.prototype.initSize=function(){
this.setLoadingMessage("Init editor size");
var _94=this;
var _95=null;
var _96=null;
switch(this.config.width){
case "auto":
_95=this._initial_ta_size.w;
break;
case "toolbar":
_95=this._toolBar.offsetWidth+"px";
break;
default:
_95=/[^0-9]/.test(this.config.width)?this.config.width:this.config.width+"px";
break;
}
switch(this.config.height){
case "auto":
_96=this._initial_ta_size.h;
break;
default:
_96=/[^0-9]/.test(this.config.height)?this.config.height:this.config.height+"px";
break;
}
this.sizeEditor(_95,_96,this.config.sizeIncludesBars,this.config.sizeIncludesPanels);
HTMLArea.addDom0Event(window,"resize",function(e){
_94.sizeEditor();
});
this.notifyOn("panel_change",function(){
_94.sizeEditor();
});
};
HTMLArea.prototype.sizeEditor=function(_97,_98,_99,_100){
this._iframe.style.height="100%";
this._textArea.style.height="100%";
this._iframe.style.width="";
this._textArea.style.width="";
if(_99!==null){
this._htmlArea.sizeIncludesToolbars=_99;
}
if(_100!==null){
this._htmlArea.sizeIncludesPanels=_100;
}
if(_97){
this._htmlArea.style.width=_97;
if(!this._htmlArea.sizeIncludesPanels){
var _101=this._panels.right;
if(_101.on&&_101.panels.length&&HTMLArea.hasDisplayedChildren(_101.div)){
this._htmlArea.style.width=this._htmlArea.offsetWidth+parseInt(this.config.panel_dimensions.right,10);
}
var _102=this._panels.left;
if(_102.on&&_102.panels.length&&HTMLArea.hasDisplayedChildren(_102.div)){
this._htmlArea.style.width=this._htmlArea.offsetWidth+parseInt(this.config.panel_dimensions.left,10);
}
}
}
if(_98){
this._htmlArea.style.height=_98;
if(!this._htmlArea.sizeIncludesToolbars){
this._htmlArea.style.height=(this._htmlArea.offsetHeight+this._toolbar.offsetHeight+this._statusBar.offsetHeight)+"px";
}
if(!this._htmlArea.sizeIncludesPanels){
var _103=this._panels.top;
if(_103.on&&_103.panels.length&&HTMLArea.hasDisplayedChildren(_103.div)){
this._htmlArea.style.height=(this._htmlArea.offsetHeight+parseInt(this.config.panel_dimensions.top,10))+"px";
}
var _104=this._panels.bottom;
if(_104.on&&_104.panels.length&&HTMLArea.hasDisplayedChildren(_104.div)){
this._htmlArea.style.height=(this._htmlArea.offsetHeight+parseInt(this.config.panel_dimensions.bottom,10))+"px";
}
}
}
_97=this._htmlArea.offsetWidth;
_98=this._htmlArea.offsetHeight;
var _105=this._panels;
var _106=this;
var _107=1;
function panel_is_alive(pan){
if(_105[pan].on&&_105[pan].panels.length&&HTMLArea.hasDisplayedChildren(_105[pan].container)){
_105[pan].container.style.display="";
return true;
}else{
_105[pan].container.style.display="none";
return false;
}
}
if(panel_is_alive("left")){
_107+=1;
}
if(panel_is_alive("right")){
_107+=1;
}
this._framework.tb_cell.colSpan=_107;
this._framework.tp_cell.colSpan=_107;
this._framework.bp_cell.colSpan=_107;
this._framework.sb_cell.colSpan=_107;
if(!this._framework.tp_row.childNodes.length){
HTMLArea.removeFromParent(this._framework.tp_row);
}else{
if(!HTMLArea.hasParentNode(this._framework.tp_row)){
this._framework.tbody.insertBefore(this._framework.tp_row,this._framework.ler_row);
}
}
if(!this._framework.bp_row.childNodes.length){
HTMLArea.removeFromParent(this._framework.bp_row);
}else{
if(!HTMLArea.hasParentNode(this._framework.bp_row)){
this._framework.tbody.insertBefore(this._framework.bp_row,this._framework.ler_row.nextSibling);
}
}
if(!this.config.statusBar){
HTMLArea.removeFromParent(this._framework.sb_row);
}else{
if(!HTMLArea.hasParentNode(this._framework.sb_row)){
this._framework.table.appendChild(this._framework.sb_row);
}
}
this._framework.lp_cell.style.width=this.config.panel_dimensions.left;
this._framework.rp_cell.style.width=this.config.panel_dimensions.right;
this._framework.tp_cell.style.height=this.config.panel_dimensions.top;
this._framework.bp_cell.style.height=this.config.panel_dimensions.bottom;
this._framework.tb_cell.style.height=this._toolBar.offsetHeight+"px";
this._framework.sb_cell.style.height=this._statusBar.offsetHeight+"px";
var _109=_98-this._toolBar.offsetHeight-this._statusBar.offsetHeight;
if(panel_is_alive("top")){
_109-=parseInt(this.config.panel_dimensions.top,10);
}
if(panel_is_alive("bottom")){
_109-=parseInt(this.config.panel_dimensions.bottom,10);
}
this._iframe.style.height=_109+"px";
this._framework.rp_cell.style.height=_109+"px";
this._framework.lp_cell.style.height=_109+"px";
for(var i=0;i<this._panels.left.panels.length;i++){
this._panels.left.panels[i].style.height=this._iframe.style.height;
}
for(var i=0;i<this._panels.right.panels.length;i++){
this._panels.right.panels[i].style.height=this._iframe.style.height;
}
var _110=_97;
if(panel_is_alive("left")){
_110-=parseInt(this.config.panel_dimensions.left,10);
}
if(panel_is_alive("right")){
_110-=parseInt(this.config.panel_dimensions.right,10);
}
this._iframe.style.width=_110+"px";
this._textArea.style.height=this._iframe.style.height;
this._textArea.style.width=this._iframe.style.width;
this.notifyOf("resize",{width:this._htmlArea.offsetWidth,height:this._htmlArea.offsetHeight});
};
HTMLArea.prototype.addPanel=function(side){
var div=document.createElement("div");
div.side=side;
if(side=="left"||side=="right"){
div.style.width=this.config.panel_dimensions[side];
if(this._iframe){
div.style.height=this._iframe.style.height;
}
}
HTMLArea.addClasses(div,"panel");
this._panels[side].panels.push(div);
this._panels[side].div.appendChild(div);
this.notifyOf("panel_change",{"action":"add","panel":div});
return div;
};
HTMLArea.prototype.removePanel=function(_112){
this._panels[_112.side].div.removeChild(_112);
var _113=[];
for(var i=0;i<this._panels[_112.side].panels.length;i++){
if(this._panels[_112.side].panels[i]!=_112){
_113.push(this._panels[_112.side].panels[i]);
}
}
this._panels[_112.side].panels=_113;
this.notifyOf("panel_change",{"action":"remove","panel":_112});
};
HTMLArea.prototype.hidePanel=function(_114){
if(_114&&_114.style.display!="none"){
_114.style.display="none";
this.notifyOf("panel_change",{"action":"hide","panel":_114});
}
};
HTMLArea.prototype.showPanel=function(_115){
if(_115&&_115.style.display=="none"){
_115.style.display="";
this.notifyOf("panel_change",{"action":"show","panel":_115});
}
};
HTMLArea.prototype.hidePanels=function(_116){
if(typeof _116=="undefined"){
_116=["left","right","top","bottom"];
}
var _117=[];
for(var i=0;i<_116.length;i++){
if(this._panels[_116[i]].on){
_117.push(_116[i]);
this._panels[_116[i]].on=false;
}
}
this.notifyOf("panel_change",{"action":"multi_hide","sides":_116});
};
HTMLArea.prototype.showPanels=function(_118){
if(typeof _118=="undefined"){
_118=["left","right","top","bottom"];
}
var _119=[];
for(var i=0;i<_118.length;i++){
if(!this._panels[_118[i]].on){
_119.push(_118[i]);
this._panels[_118[i]].on=true;
}
}
this.notifyOf("panel_change",{"action":"multi_show","sides":_118});
};
HTMLArea.objectProperties=function(obj){
var _120=[];
for(var x in obj){
_120[_120.length]=x;
}
return _120;
};
HTMLArea.prototype.editorIsActivated=function(){
try{
return HTMLArea.is_gecko?this._doc.designMode=="on":this._doc.body.contentEditable;
}
catch(ex){
return false;
}
};
HTMLArea._someEditorHasBeenActivated=false;
HTMLArea._currentlyActiveEditor=false;
HTMLArea.prototype.activateEditor=function(){
if(HTMLArea._currentlyActiveEditor){
if(HTMLArea._currentlyActiveEditor==this){
return true;
}
HTMLArea._currentlyActiveEditor.deactivateEditor();
}
if(HTMLArea.is_gecko&&this._doc.designMode!="on"){
try{
if(this._iframe.style.display=="none"){
this._iframe.style.display="";
this._doc.designMode="on";
this._iframe.style.display="none";
}else{
this._doc.designMode="on";
}
}
catch(ex){
}
}else{
if(!HTMLArea.is_gecko&&this._doc.body.contentEditable!==true){
this._doc.body.contentEditable=true;
}
}
HTMLArea._someEditorHasBeenActivated=true;
HTMLArea._currentlyActiveEditor=this;
var _122=this;
this.enableToolbar();
};
HTMLArea.prototype.deactivateEditor=function(){
this.disableToolbar();
if(HTMLArea.is_gecko&&this._doc.designMode!="off"){
try{
this._doc.designMode="off";
}
catch(ex){
}
}else{
if(!HTMLArea.is_gecko&&this._doc.body.contentEditable!==false){
this._doc.body.contentEditable=false;
}
}
if(HTMLArea._currentlyActiveEditor!=this){
return;
}
HTMLArea._currentlyActiveEditor=false;
};
HTMLArea.prototype.initIframe=function(){
this.setLoadingMessage("Init IFrame");
this.disableToolbar();
var doc=null;
var _123=this;
try{
if(_123._iframe.contentDocument){
this._doc=_123._iframe.contentDocument;
}else{
this._doc=_123._iframe.contentWindow.document;
}
doc=this._doc;
if(!doc){
if(HTMLArea.is_gecko){
setTimeout(function(){
_123.initIframe();
},50);
return false;
}else{
alert("ERROR: IFRAME can't be initialized.");
}
}
}
catch(ex){
setTimeout(function(){
_123.initIframe();
},50);
}
HTMLArea.freeLater(this,"_doc");
doc.open();
var html="";
if(!_123.config.fullPage){
html="<html>\n";
html+="<head>\n";
html+="<meta http-equiv=\"Content-Type\" content=\"text/html; charset="+_123.config.charSet+"\">\n";
if(typeof _123.config.baseHref!="undefined"&&_123.config.baseHref!==null){
html+="<base href=\""+_123.config.baseHref+"\"/>\n";
}
html+="<style title=\"table borders\">";
html+=".htmtableborders, .htmtableborders td, .htmtableborders th {border : 1px dashed lightgrey ! important;} \n";
html+="</style>\n";
html+="<style type=\"text/css\">";
html+="html, body { border: 0px;  background-color: #ffffff; } \n";
html+="span.macro, span.macro ul, span.macro div, span.macro p {background : #CCCCCC;}\n";
html+="</style>\n";
if(_123.config.pageStyle){
html+="<style type=\"text/css\">\n"+_123.config.pageStyle+"\n</style>";
}
if(typeof _123.config.pageStyleSheets!=="undefined"){
for(var i=0;i<_123.config.pageStyleSheets.length;i++){
if(_123.config.pageStyleSheets[i].length>0){
html+="<link rel=\"stylesheet\" type=\"text/css\" href=\""+_123.config.pageStyleSheets[i]+"\">";
}
}
}
html+="</head>\n";
html+="<body>\n";
html+=_123.inwardHtml(_123._textArea.value);
html+="</body>\n";
html+="</html>";
}else{
html=_123.inwardHtml(_123._textArea.value);
if(html.match(HTMLArea.RE_doctype)){
_123.setDoctype(RegExp.$1);
html=html.replace(HTMLArea.RE_doctype,"");
}
}
doc.write(html);
doc.close();
this.setEditorEvents();
};
HTMLArea.prototype.whenDocReady=function(_125){
var _126=this;
if(!this._doc.body){
setTimeout(function(){
_126.whenDocReady(_125);
},50);
}else{
_125();
}
};
HTMLArea.prototype.setMode=function(mode){
var html;
if(typeof mode=="undefined"){
mode=this._editMode=="textmode"?"wysiwyg":"textmode";
}
switch(mode){
case "textmode":
html=this.outwardHtml(this.getHTML());
this.setHTML(html);
this.deactivateEditor();
this._iframe.style.display="none";
this._textArea.style.display="";
if(this.config.statusBar){
this._statusBarTree.style.display="none";
this._statusBarTextMode.style.display="";
}
this.notifyOf("modechange",{"mode":"text"});
break;
case "wysiwyg":
html=this.inwardHtml(this.getHTML());
this.deactivateEditor();
this.setHTML(html);
this._iframe.style.display="";
this._textArea.style.display="none";
this.activateEditor();
if(this.config.statusBar){
this._statusBarTree.style.display="";
this._statusBarTextMode.style.display="none";
}
this.notifyOf("modechange",{"mode":"wysiwyg"});
break;
default:
alert("Mode <"+mode+"> not defined!");
return false;
}
this._editMode=mode;
for(var i in this.plugins){
var _128=this.plugins[i].instance;
if(_128&&typeof _128.onMode=="function"){
_128.onMode(mode);
}
}
};
HTMLArea.prototype.setFullHTML=function(html){
var _129=RegExp.multiline;
RegExp.multiline=true;
if(html.match(HTMLArea.RE_doctype)){
this.setDoctype(RegExp.$1);
html=html.replace(HTMLArea.RE_doctype,"");
}
RegExp.multiline=_129;
if(!HTMLArea.is_ie){
if(html.match(HTMLArea.RE_head)){
this._doc.getElementsByTagName("head")[0].innerHTML=RegExp.$1;
}
if(html.match(HTMLArea.RE_body)){
this._doc.getElementsByTagName("body")[0].innerHTML=RegExp.$1;
}
}else{
var reac=this.editorIsActivated();
if(reac){
this.deactivateEditor();
}
var _131=/<html>((.|\n)*?)<\/html>/i;
html=html.replace(_131,"$1");
this._doc.open();
this._doc.write(html);
this._doc.close();
if(reac){
this.activateEditor();
}
this.setEditorEvents();
return true;
}
};
HTMLArea.prototype.setEditorEvents=function(){
var _132=this;
var doc=this._doc;
_132.whenDocReady(function(){
HTMLArea._addEvents(doc,["mousedown"],function(){
_132.activateEditor();
return true;
});
HTMLArea._addEvents(doc,["keydown","keypress","mousedown","mouseup","drag"],function(_133){
return _132._editorEvent(HTMLArea.is_ie?_132._iframe.contentWindow.event:_133);
});
for(var i in _132.plugins){
var _134=_132.plugins[i].instance;
HTMLArea.refreshPlugin(_134);
}
if(typeof _132._onGenerate=="function"){
_132._onGenerate();
}
_132.removeLoadingMessage();
});
};
HTMLArea.prototype.registerPlugin=function(){
var _135=arguments[0];
if(_135===null||typeof _135=="undefined"||(typeof _135=="string"&&eval("typeof "+_135)=="undefined")){
return false;
}
var args=[];
for(var i=1;i<arguments.length;++i){
args.push(arguments[i]);
}
return this.registerPlugin2(_135,args);
};
HTMLArea.prototype.registerPlugin2=function(_137,args){
if(typeof _137=="string"){
_137=eval(_137);
}
if(typeof _137=="undefined"){
return false;
}
var obj=new _137(this,args);
if(obj){
var _138={};
var info=_137._pluginInfo;
for(var i in info){
_138[i]=info[i];
}
_138.instance=obj;
_138.args=args;
this.plugins[_137._pluginInfo.name]=_138;
return obj;
}else{
alert("Can't register plugin "+_137.toString()+".");
}
};
HTMLArea.getPluginDir=function(_140){
return _editor_url+"plugins/"+_140;
};
HTMLArea.loadPlugin=function(_141,_142){
if(eval("typeof "+_141)!="undefined"){
if(_142){
_142(_141);
}
return true;
}
var dir=this.getPluginDir(_141);
var _144=_141.replace(/([a-z])([A-Z])([a-z])/g,function(str,l1,l2,l3){
return l1+"-"+l2.toLowerCase()+l3;
}).toLowerCase()+".js";
var _149=dir+"/"+_144;
if(_142){
HTMLArea._loadback(_149,function(){
_142(_141);
});
}else{
document.write("<script type=\"text/javascript\" src=\""+_149+"\"></script>");
}
return false;
};
HTMLArea._pluginLoadStatus={};
HTMLArea.loadPlugins=function(_150,_151){
var _152=true;
var _153=HTMLArea.cloneObject(_150);
while(_153.length){
var p=_153.pop();
if(typeof HTMLArea._pluginLoadStatus[p]=="undefined"){
HTMLArea._pluginLoadStatus[p]="loading";
HTMLArea.loadPlugin(p,function(_155){
if(eval("typeof "+_155)!="undefined"){
HTMLArea._pluginLoadStatus[_155]="ready";
}else{
HTMLArea._pluginLoadStatus[_155]="failed";
}
});
_152=false;
}else{
switch(HTMLArea._pluginLoadStatus[p]){
case "failed":
case "ready":
break;
default:
_152=false;
break;
}
}
}
if(_152){
return true;
}
if(_151){
setTimeout(function(){
if(HTMLArea.loadPlugins(_150,_151)){
_151();
}
},150);
}
return _152;
};
HTMLArea.refreshPlugin=function(_156){
if(_156&&typeof _156.onGenerate=="function"){
_156.onGenerate();
}
if(_156&&typeof _156.onGenerateOnce=="function"){
_156.onGenerateOnce();
_156.onGenerateOnce=null;
}
};
HTMLArea.loadStyle=function(_157,_158){
var url=_editor_url||"";
if(typeof _158!="undefined"){
url+="plugins/"+_158+"/";
}
url+=_157;
if(/^\//.test(_157)){
url=_157;
}
var head=document.getElementsByTagName("head")[0];
var link=document.createElement("link");
link.rel="stylesheet";
link.href=url;
head.appendChild(link);
};
HTMLArea.loadStyle(typeof _editor_css=="string"?_editor_css:"htmlarea.css");
HTMLArea.prototype.debugTree=function(){
var ta=document.createElement("textarea");
ta.style.width="100%";
ta.style.height="20em";
ta.value="";
function debug(_162,str){
for(;--_162>=0;){
ta.value+=" ";
}
ta.value+=str+"\n";
}
function _dt(root,_164){
var tag=root.tagName.toLowerCase(),i;
var ns=HTMLArea.is_ie?root.scopeName:root.prefix;
debug(_164,"- "+tag+" ["+ns+"]");
for(i=root.firstChild;i;i=i.nextSibling){
if(i.nodeType==1){
_dt(i,_164+2);
}
}
}
_dt(this._doc.body,0);
document.body.appendChild(ta);
};
HTMLArea.getInnerText=function(el){
var txt="",i;
for(i=el.firstChild;i;i=i.nextSibling){
if(i.nodeType==3){
txt+=i.data;
}else{
if(i.nodeType==1){
txt+=HTMLArea.getInnerText(i);
}
}
}
return txt;
};
HTMLArea.prototype._wordClean=function(){
var _167=this;
var _168={empty_tags:0,mso_class:0,mso_style:0,mso_xmlel:0,orig_len:this._doc.body.innerHTML.length,T:(new Date()).getTime()};
var _169={empty_tags:"Empty tags removed: ",mso_class:"MSO class names removed: ",mso_style:"MSO inline style removed: ",mso_xmlel:"MSO XML elements stripped: "};
function showStats(){
var txt="HTMLArea word cleaner stats: \n\n";
for(var i in _168){
if(_169[i]){
txt+=_169[i]+_168[i]+"\n";
}
}
txt+="\nInitial document length: "+_168.orig_len+"\n";
txt+="Final document length: "+_167._doc.body.innerHTML.length+"\n";
txt+="Clean-up took "+(((new Date()).getTime()-_168.T)/1000)+" seconds";
alert(txt);
}
function clearClass(node){
var newc=node.className.replace(/(^|\s)mso.*?(\s|$)/ig," ");
if(newc!=node.className){
node.className=newc;
if(!(/\S/.test(node.className))){
node.removeAttribute("className");
++_168.mso_class;
}
}
}
function clearStyle(node){
var _172=node.style.cssText.split(/\s*;\s*/);
for(var i=_172.length;--i>=0;){
if((/^mso|^tab-stops/i.test(_172[i]))||(/^margin\s*:\s*0..\s+0..\s+0../i.test(_172[i]))){
++_168.mso_style;
_172.splice(i,1);
}
}
node.style.cssText=_172.join("; ");
}
var _173=null;
if(HTMLArea.is_ie){
_173=function(el){
el.outerHTML=HTMLArea.htmlEncode(el.innerText);
++_168.mso_xmlel;
};
}else{
_173=function(el){
var txt=document.createTextNode(HTMLArea.getInnerText(el));
el.parentNode.insertBefore(txt,el);
HTMLArea.removeFromParent(el);
++_168.mso_xmlel;
};
}
function checkEmpty(el){
if(/^(a|span|b|strong|i|em|font)$/i.test(el.tagName)&&!el.firstChild){
HTMLArea.removeFromParent(el);
++_168.empty_tags;
}
}
function parseTree(root){
var tag=root.tagName.toLowerCase(),i,next;
if((HTMLArea.is_ie&&root.scopeName!="HTML")||(!HTMLArea.is_ie&&(/:/.test(tag)))){
_173(root);
return false;
}else{
clearClass(root);
clearStyle(root);
for(i=root.firstChild;i;i=next){
next=i.nextSibling;
if(i.nodeType==1&&parseTree(i)){
checkEmpty(i);
}
}
}
return true;
}
parseTree(this._doc.body);
this.updateToolbar();
};
HTMLArea.prototype._clearFonts=function(){
var D=this.getInnerHTML();
if(confirm(HTMLArea._lc("Would you like to clear font typefaces?"))){
D=D.replace(/face="[^"]*"/gi,"");
D=D.replace(/font-family:[^;}"']+;?/gi,"");
}
if(confirm(HTMLArea._lc("Would you like to clear font sizes?"))){
D=D.replace(/size="[^"]*"/gi,"");
D=D.replace(/font-size:[^;}"']+;?/gi,"");
}
if(confirm(HTMLArea._lc("Would you like to clear font colours?"))){
D=D.replace(/color="[^"]*"/gi,"");
D=D.replace(/([^-])color:[^;}"']+;?/gi,"$1");
}
D=D.replace(/(style|class)="\s*"/gi,"");
D=D.replace(/<(font|span)\s*>/gi,"");
this.setHTML(D);
this.updateToolbar();
};
HTMLArea.prototype._splitBlock=function(){
this._doc.execCommand("formatblock",false,"div");
};
HTMLArea.prototype.forceRedraw=function(){
this._doc.body.style.visibility="hidden";
this._doc.body.style.visibility="visible";
};
HTMLArea.prototype.focusEditor=function(){
switch(this._editMode){
case "wysiwyg":
try{
if(HTMLArea._someEditorHasBeenActivated){
this.activateEditor();
this._iframe.contentWindow.focus();
}
}
catch(ex){
}
break;
case "textmode":
try{
this._textArea.focus();
}
catch(e){
}
break;
default:
alert("ERROR: mode "+this._editMode+" is not defined");
}
return this._doc;
};
HTMLArea.prototype._undoTakeSnapshot=function(){
++this._undoPos;
if(this._undoPos>=this.config.undoSteps){
this._undoQueue.shift();
--this._undoPos;
}
var take=true;
var txt=this.getInnerHTML();
if(this._undoPos>0){
take=(this._undoQueue[this._undoPos-1]!=txt);
}
if(take){
this._undoQueue[this._undoPos]=txt;
}else{
this._undoPos--;
}
};
HTMLArea.prototype.undo=function(){
if(this._undoPos>0){
var txt=this._undoQueue[--this._undoPos];
if(txt){
this.setHTML(txt);
}else{
++this._undoPos;
}
}
};
HTMLArea.prototype.redo=function(){
if(this._undoPos<this._undoQueue.length-1){
var txt=this._undoQueue[++this._undoPos];
if(txt){
this.setHTML(txt);
}else{
--this._undoPos;
}
}
};
HTMLArea.prototype.disableToolbar=function(_176){
if(this._timerToolbar){
clearTimeout(this._timerToolbar);
}
if(typeof _176=="undefined"){
_176=[];
}else{
if(typeof _176!="object"){
_176=[_176];
}
}
for(var i in this._toolbarObjects){
var btn=this._toolbarObjects[i];
if(_176.contains(i)){
continue;
}
if(typeof (btn.state)!="function"){
continue;
}
btn.state("enabled",false);
}
};
HTMLArea.prototype.enableToolbar=function(){
this.updateToolbar();
};
if(!Array.prototype.contains){
Array.prototype.contains=function(_177){
var _178=this;
for(var i=0;i<_178.length;i++){
if(_177==_178[i]){
return true;
}
}
return false;
};
}
if(!Array.prototype.indexOf){
Array.prototype.indexOf=function(_179){
var _180=this;
for(var i=0;i<_180.length;i++){
if(_179==_180[i]){
return i;
}
}
return null;
};
}
HTMLArea.prototype.updateToolbar=function(_181){
var doc=this._doc;
var text=(this._editMode=="textmode");
var _183=null;
if(!text){
_183=this.getAllAncestors();
if(this.config.statusBar&&!_181){
this._statusBarTree.innerHTML=HTMLArea._lc("Path")+": ";
for(var i=_183.length;--i>=0;){
var el=_183[i];
if(!el){
continue;
}
var a=document.createElement("a");
a.href="javascript:void(0)";
a.el=el;
a.editor=this;
HTMLArea.addDom0Event(a,"click",function(){
this.blur();
this.editor.selectNodeContents(this.el);
this.editor.updateToolbar(true);
return false;
});
HTMLArea.addDom0Event(a,"contextmenu",function(){
this.blur();
var info="Inline style:\n\n";
info+=this.el.style.cssText.split(/;\s*/).join(";\n");
alert(info);
return false;
});
var txt=el.tagName.toLowerCase();
a.title=el.style.cssText;
if(el.id){
txt+="#"+el.id;
}
if(el.className){
txt+="."+el.className;
}
a.appendChild(document.createTextNode(txt));
this._statusBarTree.appendChild(a);
if(i!==0){
this._statusBarTree.appendChild(document.createTextNode(String.fromCharCode(187)));
}
}
}
}
for(var cmd in this._toolbarObjects){
var btn=this._toolbarObjects[cmd];
var _184=true;
if(typeof (btn.state)!="function"){
continue;
}
if(btn.context&&!text){
_184=false;
var _185=btn.context;
var _186=[];
if(/(.*)\[(.*?)\]/.test(_185)){
_185=RegExp.$1;
_186=RegExp.$2.split(",");
}
_185=_185.toLowerCase();
var _187=(_185=="*");
for(var k=0;k<_183.length;++k){
if(!_183[k]){
continue;
}
if(_187||(_183[k].tagName.toLowerCase()==_185)){
_184=true;
for(var ka=0;ka<_186.length;++ka){
if(!eval("ancestors[k]."+_186[ka])){
_184=false;
break;
}
}
if(_184){
break;
}
}
}
}
btn.state("enabled",(!text||btn.text)&&_184);
if(typeof cmd=="function"){
continue;
}
var _190=this.config.customSelects[cmd];
if((!text||btn.text)&&(typeof _190!="undefined")){
_190.refresh(this);
continue;
}
switch(cmd){
case "fontname":
case "fontsize":
if(!text){
try{
var _191=(""+doc.queryCommandValue(cmd)).toLowerCase();
if(!_191){
btn.element.selectedIndex=0;
break;
}
var _192=this.config[cmd];
var _193=0;
for(var j in _192){
if((j.toLowerCase()==_191)||(_192[j].substr(0,_191.length).toLowerCase()==_191)){
btn.element.selectedIndex=_193;
throw "ok";
}
++_193;
}
btn.element.selectedIndex=0;
}
catch(ex){
}
}
break;
case "formatblock":
var _194=[];
for(var _195 in this.config.formatblock){
if(typeof this.config.formatblock[_195]=="string"){
_194[_194.length]=this.config.formatblock[_195];
}
}
var _196=this._getFirstAncestor(this._getSelection(),_194);
if(_196){
for(var x=0;x<_194.length;x++){
if(_194[x].toLowerCase()==_196.tagName.toLowerCase()){
btn.element.selectedIndex=x;
}
}
}else{
btn.element.selectedIndex=0;
}
break;
case "textindicator":
if(!text){
try{
var _197=btn.element.style;
_197.backgroundColor=HTMLArea._makeColor(doc.queryCommandValue(HTMLArea.is_ie?"backcolor":"hilitecolor"));
if(/transparent/i.test(_197.backgroundColor)){
_197.backgroundColor=HTMLArea._makeColor(doc.queryCommandValue("backcolor"));
}
_197.color=HTMLArea._makeColor(doc.queryCommandValue("forecolor"));
_197.fontFamily=doc.queryCommandValue("fontname");
_197.fontWeight=doc.queryCommandState("bold")?"bold":"normal";
_197.fontStyle=doc.queryCommandState("italic")?"italic":"normal";
}
catch(ex){
}
}
break;
case "htmlmode":
btn.state("active",text);
break;
case "lefttoright":
case "righttoleft":
var _198=this.getParentElement();
while(_198&&!HTMLArea.isBlockElement(_198)){
_198=_198.parentNode;
}
if(_198){
btn.state("active",(_198.style.direction==((cmd=="righttoleft")?"rtl":"ltr")));
}
break;
default:
cmd=cmd.replace(/(un)?orderedlist/i,"insert$1orderedlist");
try{
btn.state("active",(!text&&doc.queryCommandState(cmd)));
}
catch(ex){
}
break;
}
}
if(this._customUndo&&!this._timerUndo){
this._undoTakeSnapshot();
var _199=this;
this._timerUndo=setTimeout(function(){
_199._timerUndo=null;
},this.config.undoTimeout);
}
if(0&&HTMLArea.is_gecko){
var s=this._getSelection();
if(s&&s.isCollapsed&&s.anchorNode&&s.anchorNode.parentNode.tagName.toLowerCase()!="body"&&s.anchorNode.nodeType==3&&s.anchorOffset==s.anchorNode.length&&!(s.anchorNode.parentNode.nextSibling&&s.anchorNode.parentNode.nextSibling.nodeType==3)&&!HTMLArea.isBlockElement(s.anchorNode.parentNode)){
try{
s.anchorNode.parentNode.parentNode.insertBefore(this._doc.createTextNode("\t"),s.anchorNode.parentNode.nextSibling);
}
catch(ex){
}
}
}
for(var _201 in this.plugins){
var _202=this.plugins[_201].instance;
if(_202&&typeof _202.onUpdateToolbar=="function"){
_202.onUpdateToolbar();
}
}
};
if(!HTMLArea.is_ie){
HTMLArea.prototype.insertNodeAtSelection=function(_203){
var sel=this._getSelection();
var _205=this._createRange(sel);
sel.removeAllRanges();
_205.deleteContents();
var node=_205.startContainer;
var pos=_205.startOffset;
var _207=_203;
switch(node.nodeType){
case 3:
if(_203.nodeType==3){
node.insertData(pos,_203.data);
_205=this._createRange();
_205.setEnd(node,pos+_203.length);
_205.setStart(node,pos+_203.length);
sel.addRange(_205);
}else{
node=node.splitText(pos);
if(_203.nodeType==11){
_207=_207.firstChild;
}
node.parentNode.insertBefore(_203,node);
this.selectNodeContents(_207);
this.updateToolbar();
}
break;
case 1:
if(_203.nodeType==11){
_207=_207.firstChild;
}
node.insertBefore(_203,node.childNodes[pos]);
this.selectNodeContents(_207);
this.updateToolbar();
break;
}
};
}else{
HTMLArea.prototype.insertNodeAtSelection=function(_208){
return null;
};
}
if(HTMLArea.is_ie){
HTMLArea.prototype.getParentElement=function(sel){
if(typeof sel=="undefined"){
sel=this._getSelection();
}
var _209=this._createRange(sel);
switch(sel.type){
case "Text":
var _210=_209.parentElement();
while(true){
var _211=_209.duplicate();
_211.moveToElementText(_210);
if(_211.inRange(_209)){
break;
}
if((_210.nodeType!=1)||(_210.tagName.toLowerCase()=="body")){
break;
}
_210=_210.parentElement;
}
return _210;
case "None":
return _209.parentElement();
case "Control":
return _209.item(0);
default:
return this._doc.body;
}
};
}else{
HTMLArea.prototype.getParentElement=function(sel){
if(typeof sel=="undefined"){
sel=this._getSelection();
}
var _212=this._createRange(sel);
try{
var p=_212.commonAncestorContainer;
if(!_212.collapsed&&_212.startContainer==_212.endContainer&&_212.startOffset-_212.endOffset<=1&&_212.startContainer.hasChildNodes()){
p=_212.startContainer.childNodes[_212.startOffset];
}
while(p.nodeType==3){
p=p.parentNode;
}
return p;
}
catch(ex){
return null;
}
};
}
HTMLArea.prototype.getAllAncestors=function(){
var p=this.getParentElement();
var a=[];
while(p&&(p.nodeType==1)&&(p.tagName.toLowerCase()!="body")){
a.push(p);
p=p.parentNode;
}
a.push(this._doc.body);
return a;
};
HTMLArea.prototype._getFirstAncestor=function(sel,_213){
var prnt=this._activeElement(sel);
if(prnt===null){
try{
prnt=(HTMLArea.is_ie?this._createRange(sel).parentElement():this._createRange(sel).commonAncestorContainer);
}
catch(ex){
return null;
}
}
if(typeof _213=="string"){
_213=[_213];
}
while(prnt){
if(prnt.nodeType==1){
if(_213===null){
return prnt;
}
if(_213.contains(prnt.tagName.toLowerCase())){
return prnt;
}
if(prnt.tagName.toLowerCase()=="body"){
break;
}
if(prnt.tagName.toLowerCase()=="table"){
break;
}
}
prnt=prnt.parentNode;
}
return null;
};
if(HTMLArea.is_ie){
HTMLArea.prototype._activeElement=function(sel){
if((sel===null)||this._selectionEmpty(sel)){
return null;
}
if(sel.type.toLowerCase()=="control"){
return sel.createRange().item(0);
}else{
var _215=sel.createRange();
var _216=this.getParentElement(sel);
if(_216.innerHTML==_215.htmlText){
return _216;
}
return null;
}
};
}else{
HTMLArea.prototype._activeElement=function(sel){
if((sel===null)||this._selectionEmpty(sel)){
return null;
}
if(!sel.isCollapsed){
if(sel.anchorNode.childNodes.length>sel.anchorOffset&&sel.anchorNode.childNodes[sel.anchorOffset].nodeType==1){
return sel.anchorNode.childNodes[sel.anchorOffset];
}else{
if(sel.anchorNode.nodeType==1){
return sel.anchorNode;
}else{
return null;
}
}
}
return null;
};
}
if(HTMLArea.is_ie){
HTMLArea.prototype._selectionEmpty=function(sel){
if(!sel){
return true;
}
return this._createRange(sel).htmlText==="";
};
}else{
HTMLArea.prototype._selectionEmpty=function(sel){
if(!sel){
return true;
}
if(typeof sel.isCollapsed!="undefined"){
return sel.isCollapsed;
}
return true;
};
}
HTMLArea.prototype._getAncestorBlock=function(sel){
var prnt=(HTMLArea.is_ie?this._createRange(sel).parentElement:this._createRange(sel).commonAncestorContainer);
while(prnt&&(prnt.nodeType==1)){
switch(prnt.tagName.toLowerCase()){
case "div":
case "p":
case "address":
case "blockquote":
case "center":
case "del":
case "ins":
case "pre":
case "h1":
case "h2":
case "h3":
case "h4":
case "h5":
case "h6":
case "h7":
return prnt;
case "body":
case "noframes":
case "dd":
case "li":
case "th":
case "td":
case "noscript":
return null;
default:
break;
}
}
return null;
};
HTMLArea.prototype._createImplicitBlock=function(type){
var sel=this._getSelection();
if(HTMLArea.is_ie){
sel.empty();
}else{
sel.collapseToStart();
}
var rng=this._createRange(sel);
};
HTMLArea.prototype._formatBlock=function(_219){
var _220=this.getAllAncestors();
var _221,x=null;
var _222=null;
var _223=[];
if(_219.indexOf(".")>=0){
_222=_219.substr(0,_219.indexOf(".")).toLowerCase();
_223=_219.substr(_219.indexOf("."),_219.length-_219.indexOf(".")).replace(/\./g,"").replace(/^\s*/,"").replace(/\s*$/,"").split(" ");
}else{
_222=_219.toLowerCase();
}
var sel=this._getSelection();
var rng=this._createRange(sel);
if(HTMLArea.is_gecko){
if(sel.isCollapsed){
_221=this._getAncestorBlock(sel);
if(_221===null){
_221=this._createImplicitBlock(sel,_222);
}
}else{
switch(_222){
case "h1":
case "h2":
case "h3":
case "h4":
case "h5":
case "h6":
case "h7":
_221=[];
var _224=["h1","h2","h3","h4","h5","h6","h7"];
for(var y=0;y<_224.length;y++){
var _226=this._doc.getElementsByTagName(_224[y]);
for(x=0;x<_226.length;x++){
if(sel.containsNode(_226[x])){
_221[_221.length]=_226[x];
}
}
}
if(_221.length>0){
break;
}
case "div":
_221=this._doc.createElement(_222);
_221.appendChild(rng.extractContents());
rng.insertNode(_221);
break;
case "p":
case "center":
case "pre":
case "ins":
case "del":
case "blockquote":
case "address":
_221=[];
var _227=this._doc.getElementsByTagName(_222);
for(x=0;x<_227.length;x++){
if(sel.containsNode(_227[x])){
_221[_221.length]=_227[x];
}
}
if(_221.length===0){
sel.collapseToStart();
return this._formatBlock(_219);
}
break;
}
}
}
};
if(HTMLArea.is_ie){
HTMLArea.prototype.selectNodeContents=function(node,pos){
this.focusEditor();
this.forceRedraw();
var _228;
var _229=typeof pos=="undefined"?true:false;
if(_229&&node.tagName&&node.tagName.toLowerCase().match(/table|img|input|select|textarea/)){
_228=this._doc.body.createControlRange();
_228.add(node);
}else{
_228=this._doc.body.createTextRange();
_228.moveToElementText(node);
}
_228.select();
};
}else{
HTMLArea.prototype.selectNodeContents=function(node,pos){
this.focusEditor();
this.forceRedraw();
var _230;
var _231=typeof pos=="undefined"?true:false;
var sel=this._getSelection();
_230=this._doc.createRange();
if(_231&&node.tagName&&node.tagName.toLowerCase().match(/table|img|input|textarea|select/)){
_230.selectNode(node);
}else{
_230.selectNodeContents(node);
}
sel.removeAllRanges();
sel.addRange(_230);
};
}
if(HTMLArea.is_ie){
HTMLArea.prototype.insertHTML=function(html){
var sel=this._getSelection();
var _232=this._createRange(sel);
this.focusEditor();
_232.pasteHTML(html);
};
}else{
HTMLArea.prototype.insertHTML=function(html){
var sel=this._getSelection();
var _233=this._createRange(sel);
this.focusEditor();
var _234=this._doc.createDocumentFragment();
var div=this._doc.createElement("div");
div.innerHTML=html;
while(div.firstChild){
_234.appendChild(div.firstChild);
}
var node=this.insertNodeAtSelection(_234);
};
}
HTMLArea.prototype.surroundHTML=function(_235,_236){
var html=this.getSelectedHTML();
this.insertHTML(_235+html+_236);
};
if(HTMLArea.is_ie){
HTMLArea.prototype.getSelectedHTML=function(){
var sel=this._getSelection();
var _237=this._createRange(sel);
if(_237.htmlText){
return _237.htmlText;
}else{
if(_237.length>=1){
return _237.item(0).outerHTML;
}
}
return "";
};
}else{
HTMLArea.prototype.getSelectedHTML=function(){
var sel=this._getSelection();
var _238=this._createRange(sel);
return HTMLArea.getHTML(_238.cloneContents(),false,this);
};
}
HTMLArea.prototype.hasSelectedText=function(){
return this.getSelectedHTML()!=="";
};
HTMLArea.prototype._createLink=function(link){
var _239=this;
var _240=null;
if(typeof link=="undefined"){
link=this.getParentElement();
if(link){
while(link&&!/^a$/i.test(link.tagName)){
link=link.parentNode;
}
}
}
if(!link){
var sel=_239._getSelection();
var _241=_239._createRange(sel);
var _242=0;
if(HTMLArea.is_ie){
if(sel.type=="Control"){
_242=_241.length;
}else{
_242=_241.compareEndPoints("StartToEnd",_241);
}
}else{
_242=_241.compareBoundaryPoints(_241.START_TO_END,_241);
}
if(_242===0){
alert(HTMLArea._lc("You need to select some text before creating a link"));
return;
}
_240={f_href:"",f_title:"",f_target:"",f_usetarget:_239.config.makeLinkShowsTarget};
}else{
_240={f_href:HTMLArea.is_ie?_239.stripBaseURL(link.href):link.getAttribute("href"),f_title:link.title,f_target:link.target,f_usetarget:_239.config.makeLinkShowsTarget};
}
this._popupDialog(_239.config.URIs.link,function(_243){
if(!_243){
return false;
}
var a=link;
if(!a){
try{
_239._doc.execCommand("createlink",false,_243.f_href);
a=_239.getParentElement();
var sel=_239._getSelection();
var _241=_239._createRange(sel);
if(!HTMLArea.is_ie){
a=_241.startContainer;
if(!(/^a$/i.test(a.tagName))){
a=a.nextSibling;
if(a===null){
a=_241.startContainer.parentNode;
}
}
}
}
catch(ex){
}
}else{
var href=_243.f_href.trim();
_239.selectNodeContents(a);
if(href===""){
_239._doc.execCommand("unlink",false,null);
_239.updateToolbar();
return false;
}else{
a.href=href;
}
}
if(!(a&&a.tagName.toLowerCase()=="a")){
return false;
}
a.target=_243.f_target.trim();
a.title=_243.f_title.trim();
_239.selectNodeContents(a);
_239.updateToolbar();
},_240);
};
HTMLArea.prototype._insertImage=function(_245){
var _246=this;
var _247=null;
if(typeof _245=="undefined"){
_245=this.getParentElement();
if(_245&&_245.tagName.toLowerCase()!="img"){
_245=null;
}
}
if(_245){
_247={f_base:_246.config.baseHref,f_url:HTMLArea.is_ie?_246.stripBaseURL(_245.src):_245.getAttribute("src"),f_alt:_245.alt,f_border:_245.border,f_align:_245.align,f_vert:_245.vspace,f_horiz:_245.hspace};
}
this._popupDialog(_246.config.URIs.insert_image,function(_248){
if(!_248){
return false;
}
var img=_245;
if(!img){
if(HTMLArea.is_ie){
var sel=_246._getSelection();
var _249=_246._createRange(sel);
_246._doc.execCommand("insertimage",false,_248.f_url);
img=_249.parentElement();
if(img.tagName.toLowerCase()!="img"){
img=img.previousSibling;
}
}else{
img=document.createElement("img");
img.src=_248.f_url;
_246.insertNodeAtSelection(img);
if(!img.tagName){
img=_249.startContainer.firstChild;
}
}
}else{
img.src=_248.f_url;
}
for(var _250 in _248){
var _251=_248[_250];
switch(_250){
case "f_alt":
img.alt=_251;
break;
case "f_border":
img.border=parseInt(_251||"0",10);
break;
case "f_align":
img.align=_251;
break;
case "f_vert":
img.vspace=parseInt(_251||"0",10);
break;
case "f_horiz":
img.hspace=parseInt(_251||"0",10);
break;
}
}
},_247);
};
HTMLArea.prototype._insertTable=function(){
var sel=this._getSelection();
var _252=this._createRange(sel);
var _253=this;
this._popupDialog(_253.config.URIs.insert_table,function(_254){
if(!_254){
return false;
}
var doc=_253._doc;
var _255=doc.createElement("table");
for(var _256 in _254){
var _257=_254[_256];
if(!_257){
continue;
}
switch(_256){
case "f_width":
_255.style.width=_257+_254.f_unit;
break;
case "f_align":
_255.align=_257;
break;
case "f_border":
_255.border=parseInt(_257,10);
break;
case "f_spacing":
_255.cellSpacing=parseInt(_257,10);
break;
case "f_padding":
_255.cellPadding=parseInt(_257,10);
break;
}
}
var _258=0;
if(_254.f_fixed){
_258=Math.floor(100/parseInt(_254.f_cols,10));
}
var _259=doc.createElement("tbody");
_255.appendChild(_259);
for(var i=0;i<_254.f_rows;++i){
var tr=doc.createElement("tr");
_259.appendChild(tr);
for(var j=0;j<_254.f_cols;++j){
var td=doc.createElement("td");
if(_258){
td.style.width=_258+"%";
}
tr.appendChild(td);
td.appendChild(doc.createTextNode("\xa0"));
}
}
if(HTMLArea.is_ie){
_252.pasteHTML(_255.outerHTML);
}else{
_253.insertNodeAtSelection(_255);
}
return true;
},null);
};
HTMLArea.prototype._comboSelected=function(el,txt){
this.focusEditor();
var _262=el.options[el.selectedIndex].value;
switch(txt){
case "fontname":
case "fontsize":
this.execCommand(txt,false,_262);
break;
case "formatblock":
if(!HTMLArea.is_gecko||_262!=="blockquote"){
_262="<"+_262+">";
}
this.execCommand(txt,false,_262);
break;
default:
var _263=this.config.customSelects[txt];
if(typeof _263!="undefined"){
_263.action(this);
}else{
alert("FIXME: combo box "+txt+" not implemented");
}
break;
}
};
HTMLArea.prototype._colorSelector=function(_264){
var _265=this;
if(_264=="hilitecolor"){
if(HTMLArea.is_ie){
_264="backcolor";
}
if(HTMLArea.is_gecko){
try{
_265._doc.execCommand("useCSS",false,false);
}
catch(ex){
}
}
}
this._popupDialog(_265.config.URIs.select_color,function(_266){
if(_266){
_265._doc.execCommand(_264,false,"#"+_266);
}
},HTMLArea._colorToRgb(this._doc.queryCommandValue(_264)));
};
HTMLArea.prototype.execCommand=function(_267,UI,_269){
var _270=this;
this.focusEditor();
_267=_267.toLowerCase();
if(HTMLArea.is_gecko){
try{
this._doc.execCommand("useCSS",false,true);
}
catch(ex){
}
}
switch(_267){
case "htmlmode":
this.setMode();
break;
case "hilitecolor":
case "forecolor":
this._colorSelector(_267);
break;
case "createlink":
this._createLink();
break;
case "undo":
case "redo":
if(this._customUndo){
this[_267]();
}else{
this._doc.execCommand(_267,UI,_269);
}
break;
case "inserttable":
this._insertTable();
break;
case "insertimage":
this._insertImage();
break;
case "about":
this._popupDialog(_270.config.URIs.about,null,this);
break;
case "showhelp":
this._popupDialog(_270.config.URIs.help,null,this);
break;
case "killword":
this._wordClean();
break;
case "cut":
case "copy":
case "paste":
doPastePopup=false;
try{
this._doc.execCommand(_267,UI,_269);
}
catch(e){
if(HTMLArea.is_gecko){
doPastePopup=true;
}
}
if(this.config.killWordOnPaste||doPastePopup){
if(typeof WordPaste=="undefined"){
HTMLArea.loadPlugin("WordPaste",function(){
_270.generate();
});
_270.registerPlugin("WordPaste");
}
if(typeof WordPaste=="function"){
_270.plugins["WordPaste"].instance._buttonPress(doPastePopup);
}
}
break;
case "lefttoright":
case "righttoleft":
if(this.config.changeJustifyWithDirection){
this._doc.execCommand((_267=="righttoleft")?"justifyright":"justifyleft",UI,_269);
}
var dir=(_267=="righttoleft")?"rtl":"ltr";
var el=this.getParentElement();
while(el&&!HTMLArea.isBlockElement(el)){
el=el.parentNode;
}
if(el){
if(el.style.direction==dir){
el.style.direction="";
}else{
el.style.direction=dir;
}
}
break;
default:
try{
this._doc.execCommand(_267,UI,_269);
}
catch(ex){
if(this.config.debug){
alert(e+"\n\nby execCommand("+_267+");");
}
}
break;
}
this.updateToolbar();
return false;
};
HTMLArea.prototype._editorEvent=function(ev){
var _271=this;
var _272=(HTMLArea.is_ie&&ev.type=="keydown")||(!HTMLArea.is_ie&&ev.type=="keypress");
if(typeof _271._textArea["on"+ev.type]=="function"){
_271._textArea["on"+ev.type]();
}
if(HTMLArea.is_gecko&&_272&&ev.ctrlKey&&this._unLink&&this._unlinkOnUndo){
if(String.fromCharCode(ev.charCode).toLowerCase()=="z"){
HTMLArea._stopEvent(ev);
this._unLink();
_271.updateToolbar();
return;
}
}
if(_272){
for(var i in _271.plugins){
var _273=_271.plugins[i].instance;
if(_273&&typeof _273.onKeyPress=="function"){
if(_273.onKeyPress(ev)){
return false;
}
}
}
}
if(_272&&ev.ctrlKey&&!ev.altKey){
var sel=null;
var _274=null;
var key=String.fromCharCode(HTMLArea.is_ie?ev.keyCode:ev.charCode).toLowerCase();
var cmd=null;
var _276=null;
switch(key){
case "a":
if(!HTMLArea.is_ie){
sel=this._getSelection();
sel.removeAllRanges();
_274=this._createRange();
_274.selectNodeContents(this._doc.body);
sel.addRange(_274);
HTMLArea._stopEvent(ev);
}
break;
case "b":
cmd="bold";
break;
case "i":
cmd="italic";
break;
case "u":
cmd="underline";
break;
case "s":
cmd="strikethrough";
break;
case "l":
cmd="justifyleft";
break;
case "e":
cmd="justifycenter";
break;
case "r":
cmd="justifyright";
break;
case "j":
cmd="justifyfull";
break;
case "z":
cmd="undo";
break;
case "y":
cmd="redo";
break;
case "v":
if(HTMLArea.is_ie||_271.config.htmlareaPaste){
cmd="paste";
}
break;
case "n":
cmd="formatblock";
_276=HTMLArea.is_ie?"<p>":"p";
break;
case "0":
cmd="killword";
break;
case "1":
case "2":
case "3":
case "4":
case "5":
case "6":
cmd="formatblock";
_276="h"+key;
if(HTMLArea.is_ie){
_276="<"+_276+">";
}
break;
}
if(cmd){
this.execCommand(cmd,false,_276);
HTMLArea._stopEvent(ev);
}
}else{
if(_272){
if(HTMLArea.is_gecko){
var s=_271._getSelection();
var _277=function(_278,tag){
var _279=_278.nextSibling;
if(typeof tag=="string"){
tag=_271._doc.createElement(tag);
}
var a=_278.parentNode.insertBefore(tag,_279);
HTMLArea.removeFromParent(_278);
a.appendChild(_278);
_279.data=" "+_279.data;
if(HTMLArea.is_ie){
var r=_271._createRange(s);
s.moveToElementText(_279);
s.move("character",1);
}else{
s.collapse(_279,1);
}
HTMLArea._stopEvent(ev);
_271._unLink=function(){
var t=a.firstChild;
a.removeChild(t);
a.parentNode.insertBefore(t,a);
HTMLArea.removeFromParent(a);
_271._unLink=null;
_271._unlinkOnUndo=false;
};
_271._unlinkOnUndo=true;
return a;
};
switch(ev.which){
case 32:
if(s&&s.isCollapsed&&s.anchorNode.nodeType==3&&s.anchorNode.data.length>3&&s.anchorNode.data.indexOf(".")>=0){
var _282=s.anchorNode.data.substring(0,s.anchorOffset).search(/\S{4,}$/);
if(_282==-1){
break;
}
if(this._getFirstAncestor(s,"a")){
break;
}
var _283=s.anchorNode.data.substring(0,s.anchorOffset).replace(/^.*?(\S*)$/,"$1");
var _284=_283.match(HTMLArea.RE_email);
if(_284){
var _285=s.anchorNode;
var _286=_285.splitText(s.anchorOffset);
var _287=_285.splitText(_282);
_277(_287,"a").href="mailto:"+_284[0];
break;
}
RE_date=/[0-9\.]*/;
RE_ip=/(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/;
var mUrl=_283.match(HTMLArea.RE_url);
if(mUrl){
if(RE_date.test(_283)){
if(!RE_ip.test(_283)){
break;
}
}
var _289=s.anchorNode;
var _290=_289.splitText(s.anchorOffset);
var _291=_289.splitText(_282);
_277(_291,"a").href=(mUrl[1]?mUrl[1]:"http://")+mUrl[2];
break;
}
}
break;
default:
if(ev.keyCode==27||(this._unlinkOnUndo&&ev.ctrlKey&&ev.which==122)){
if(this._unLink){
this._unLink();
HTMLArea._stopEvent(ev);
}
break;
}else{
if(ev.which||ev.keyCode==8||ev.keyCode==46){
this._unlinkOnUndo=false;
if(s.anchorNode&&s.anchorNode.nodeType==3){
var a=this._getFirstAncestor(s,"a");
if(!a){
break;
}
if(!a._updateAnchTimeout){
if(s.anchorNode.data.match(HTMLArea.RE_email)&&a.href.match("mailto:"+s.anchorNode.data.trim())){
var _292=s.anchorNode;
var _293=function(){
a.href="mailto:"+_292.data.trim();
a._updateAnchTimeout=setTimeout(_293,250);
};
a._updateAnchTimeout=setTimeout(_293,1000);
break;
}
var m=s.anchorNode.data.match(HTMLArea.RE_url);
if(m&&a.href.match(s.anchorNode.data.trim())){
var _295=s.anchorNode;
var _296=function(){
var m=_295.data.match(HTMLArea.RE_url);
a.href=(m[1]?m[1]:"http://")+m[2];
a._updateAnchTimeout=setTimeout(_296,250);
};
a._updateAnchTimeout=setTimeout(_296,1000);
}
}
}
}
}
break;
}
}
switch(ev.keyCode){
case 13:
if(HTMLArea.is_gecko&&!ev.shiftKey&&this.config.mozParaHandler=="dirty"){
this.dom_checkInsertP();
HTMLArea._stopEvent(ev);
}
break;
case 8:
case 46:
if((HTMLArea.is_gecko&&!ev.shiftKey)||HTMLArea.is_ie){
if(this.checkBackspace()){
HTMLArea._stopEvent(ev);
}
}
break;
}
}
}
if(_271._timerToolbar){
clearTimeout(_271._timerToolbar);
}
_271._timerToolbar=setTimeout(function(){
_271.updateToolbar();
_271._timerToolbar=null;
},250);
};
HTMLArea.prototype.convertNode=function(el,_297){
var _298=this._doc.createElement(_297);
while(el.firstChild){
_298.appendChild(el.firstChild);
}
return _298;
};
if(HTMLArea.is_ie){
HTMLArea.prototype.checkBackspace=function(){
var sel=this._getSelection();
if(sel.type=="Control"){
var elm=this._activeElement(sel);
HTMLArea.removeFromParent(elm);
return true;
}
var _300=this._createRange(sel);
var r2=_300.duplicate();
r2.moveStart("character",-1);
var a=r2.parentElement();
if(a!=_300.parentElement()&&(/^a$/i.test(a.tagName))){
r2.collapse(true);
r2.moveEnd("character",1);
r2.pasteHTML("");
r2.select();
return true;
}
};
}else{
HTMLArea.prototype.checkBackspace=function(){
var self=this;
setTimeout(function(){
var sel=self._getSelection();
var _303=self._createRange(sel);
var SC=_303.startContainer;
var SO=_303.startOffset;
var EC=_303.endContainer;
var EO=_303.endOffset;
var newr=SC.nextSibling;
if(SC.nodeType==3){
SC=SC.parentNode;
}
if(!(/\S/.test(SC.tagName))){
var p=document.createElement("p");
while(SC.firstChild){
p.appendChild(SC.firstChild);
}
SC.parentNode.insertBefore(p,SC);
HTMLArea.removeFromParent(SC);
var r=_303.cloneRange();
r.setStartBefore(newr);
r.setEndAfter(newr);
r.extractContents();
sel.removeAllRanges();
sel.addRange(r);
}
},10);
};
}
HTMLArea.prototype.dom_checkInsertP=function(){
var p,body;
var sel=this._getSelection();
var _309=this._createRange(sel);
if(!_309.collapsed){
_309.deleteContents();
}
this.deactivateEditor();
var SC=_309.startContainer;
var SO=_309.startOffset;
var EC=_309.endContainer;
var EO=_309.endOffset;
if(SC==EC&&SC==body&&!SO&&!EO){
p=this._doc.createTextNode(" ");
body.insertBefore(p,body.firstChild);
_309.selectNodeContents(p);
SC=_309.startContainer;
SO=_309.startOffset;
EC=_309.endContainer;
EO=_309.endOffset;
}
p=this.getAllAncestors();
var _310=null;
body=this._doc.body;
for(var i=0;i<p.length;++i){
if(HTMLArea.isParaContainer(p[i])){
break;
}else{
if(HTMLArea.isBlockElement(p[i])&&!(/body|html/i.test(p[i].tagName))){
_310=p[i];
break;
}
}
}
if(!_310){
var wrap=_309.startContainer;
while(wrap.parentNode&&!HTMLArea.isParaContainer(wrap.parentNode)){
wrap=wrap.parentNode;
}
var _312=wrap;
var end=wrap;
while(_312.previousSibling){
if(_312.previousSibling.tagName){
if(!HTMLArea.isBlockElement(_312.previousSibling)){
_312=_312.previousSibling;
}else{
break;
}
}else{
_312=_312.previousSibling;
}
}
while(end.nextSibling){
if(end.nextSibling.tagName){
if(!HTMLArea.isBlockElement(end.nextSibling)){
end=end.nextSibling;
}else{
break;
}
}else{
end=end.nextSibling;
}
}
_309.setStartBefore(_312);
_309.setEndAfter(end);
_309.surroundContents(this._doc.createElement("p"));
_310=_309.startContainer.firstChild;
_309.setStart(SC,SO);
}
_309.setEndAfter(_310);
var r2=_309.cloneRange();
sel.removeRange(_309);
var df=r2.extractContents();
if(df.childNodes.length===0){
df.appendChild(this._doc.createElement("p"));
df.firstChild.appendChild(this._doc.createElement("br"));
}
if(df.childNodes.length>1){
var nb=this._doc.createElement("p");
while(df.firstChild){
var s=df.firstChild;
df.removeChild(s);
nb.appendChild(s);
}
df.appendChild(nb);
}
if(!(/\S/.test(_310.innerHTML))){
_310.innerHTML="&nbsp;";
}
p=df.firstChild;
if(!(/\S/.test(p.innerHTML))){
p.innerHTML="<br />";
}
if((/^\s*<br\s*\/?>\s*$/.test(p.innerHTML))&&(/^h[1-6]$/i.test(p.tagName))){
df.appendChild(this.convertNode(p,"p"));
df.removeChild(p);
}
var _316=_310.parentNode.insertBefore(df.firstChild,_310.nextSibling);
this.activateEditor();
sel=this._getSelection();
sel.removeAllRanges();
sel.collapse(_316,0);
this.scrollToElement(_316);
};
HTMLArea.prototype.scrollToElement=function(e){
if(HTMLArea.is_gecko){
var top=0;
var left=0;
while(e){
top+=e.offsetTop;
left+=e.offsetLeft;
if(e.offsetParent&&e.offsetParent.tagName.toLowerCase()!="body"){
e=e.offsetParent;
}else{
e=null;
}
}
this._iframe.contentWindow.scrollTo(left,top);
}
};
HTMLArea.prototype.getHTML=function(){
var html="";
switch(this._editMode){
case "wysiwyg":
if(!this.config.fullPage){
html=HTMLArea.getHTML(this._doc.body,false,this);
}else{
html=this.doctype+"\n"+HTMLArea.getHTML(this._doc.documentElement,true,this);
}
break;
case "textmode":
html=this._textArea.value;
break;
default:
alert("Mode <"+this._editMode+"> not defined!");
return false;
}
return html;
};
HTMLArea.prototype.outwardHtml=function(html){
html=html.replace(/<(\/?)b(\s|>|\/)/ig,"<$1strong$2");
html=html.replace(/<(\/?)i(\s|>|\/)/ig,"<$1em$2");
html=html.replace(/<(\/?)strike(\s|>|\/)/ig,"<$1del$2");
html=html.replace("onclick=\"try{if(document.designMode &amp;&amp; document.designMode == 'on') return false;}catch(e){} window.open(","onclick=\"window.open(");
var _319=location.href.replace(/(https?:\/\/[^\/]*)\/.*/,"$1")+"/";
html=html.replace(/https?:\/\/null\//g,_319);
html=html.replace(/((href|src|background)=[\'\"])\/+/ig,"$1"+_319);
html=this.outwardSpecialReplacements(html);
html=this.fixRelativeLinks(html);
if(this.config.sevenBitClean){
html=html.replace(/[^ -~\r\n\t]/g,function(c){
return "&#"+c.charCodeAt(0)+";";
});
}
if(HTMLArea.is_gecko){
html=html.replace(/<script[\s]*src[\s]*=[\s]*['"]chrome:\/\/.*?["']>[\s]*<\/script>/ig,"");
}
return html;
};
HTMLArea.prototype.inwardHtml=function(html){
if(HTMLArea.is_gecko){
html=html.replace(/<(\/?)strong(\s|>|\/)/ig,"<$1b$2");
html=html.replace(/<(\/?)em(\s|>|\/)/ig,"<$1i$2");
}
html=html.replace(/<(\/?)del(\s|>|\/)/ig,"<$1strike$2");
html=html.replace("onclick=\"window.open(","onclick=\"try{if(document.designMode &amp;&amp; document.designMode == 'on') return false;}catch(e){} window.open(");
html=this.inwardSpecialReplacements(html);
var _321=new RegExp("((href|src|background)=['\"])/+","gi");
html=html.replace(_321,"$1"+location.href.replace(/(https?:\/\/[^\/]*)\/.*/,"$1")+"/");
html=this.fixRelativeLinks(html);
return html;
};
HTMLArea.prototype.outwardSpecialReplacements=function(html){
for(var i in this.config.specialReplacements){
var from=this.config.specialReplacements[i];
var to=i;
if(typeof from.replace!="function"||typeof to.replace!="function"){
continue;
}
var reg=new RegExp(from.replace(HTMLArea.RE_Specials,"\\$1"),"g");
html=html.replace(reg,to.replace(/\$/g,"$$$$"));
}
return html;
};
HTMLArea.prototype.inwardSpecialReplacements=function(html){
for(var i in this.config.specialReplacements){
var from=i;
var to=this.config.specialReplacements[i];
if(typeof from.replace!="function"||typeof to.replace!="function"){
continue;
}
var reg=new RegExp(from.replace(HTMLArea.RE_Specials,"\\$1"),"g");
html=html.replace(reg,to.replace(/\$/g,"$$$$"));
}
return html;
};
HTMLArea.prototype.fixRelativeLinks=function(html){
if(typeof this.config.stripSelfNamedAnchors!="undefined"&&this.config.stripSelfNamedAnchors){
var _325=new RegExp(document.location.href.replace(HTMLArea.RE_Specials,"\\$1")+"(#[^'\" ]*)","g");
html=html.replace(_325,"$1");
}
if(typeof this.config.stripBaseHref!="undefined"&&this.config.stripBaseHref){
var _326=null;
if(typeof this.config.baseHref!="undefined"&&this.config.baseHref!==null){
_326=new RegExp(this.config.baseHref.replace(HTMLArea.RE_Specials,"\\$1"),"g");
}else{
_326=new RegExp(document.location.href.replace(/([^\/]*\/?)$/,"").replace(HTMLArea.RE_Specials,"\\$1"),"g");
}
html=html.replace(_326,"");
}
return html;
};
HTMLArea.prototype.getInnerHTML=function(){
if(!this._doc.body){
return "";
}
var html="";
switch(this._editMode){
case "wysiwyg":
if(!this.config.fullPage){
html=this._doc.body.innerHTML;
}else{
html=this.doctype+"\n"+this._doc.documentElement.innerHTML;
}
break;
case "textmode":
html=this._textArea.value;
break;
default:
alert("Mode <"+this._editMode+"> not defined!");
return false;
}
return html;
};
HTMLArea.prototype.setHTML=function(html){
if(!this.config.fullPage){
this._doc.body.innerHTML=html;
}else{
this.setFullHTML(html);
}
this._textArea.value=html;
};
HTMLArea.prototype.setDoctype=function(_327){
this.doctype=_327;
};
HTMLArea._object=null;
HTMLArea.cloneObject=function(obj){
if(!obj){
return null;
}
var _328={};
if(obj.constructor.toString().match(/\s*function Array\(/)){
_328=obj.constructor();
}
if(obj.constructor.toString().match(/\s*function Function\(/)){
_328=obj;
}else{
for(var n in obj){
var node=obj[n];
if(typeof node=="object"){
_328[n]=HTMLArea.cloneObject(node);
}else{
_328[n]=node;
}
}
}
return _328;
};
HTMLArea.checkSupportedBrowser=function(){
if(HTMLArea.is_gecko){
if(navigator.productSub<20021201){
alert("You need at least Mozilla-1.3 Alpha.\nSorry, your Gecko is not supported.");
return false;
}
if(navigator.productSub<20030210){
alert("Mozilla < 1.3 Beta is not supported!\nI'll try, though, but it might not work.");
}
}
return HTMLArea.is_gecko||HTMLArea.is_ie;
};
if(HTMLArea.is_ie){
HTMLArea.prototype._getSelection=function(){
return this._doc.selection;
};
}else{
HTMLArea.prototype._getSelection=function(){
return this._iframe.contentWindow.getSelection();
};
}
if(HTMLArea.is_ie){
HTMLArea.prototype._createRange=function(sel){
return sel.createRange();
};
}else{
HTMLArea.prototype._createRange=function(sel){
this.activateEditor();
if(typeof sel!="undefined"){
try{
return sel.getRangeAt(0);
}
catch(ex){
return this._doc.createRange();
}
}else{
return this._doc.createRange();
}
};
}
HTMLArea._eventFlushers=[];
HTMLArea.flushEvents=function(){
var x=0;
var e=HTMLArea._eventFlushers.pop();
while(e){
try{
if(e.length==3){
HTMLArea._removeEvent(e[0],e[1],e[2]);
x++;
}else{
if(e.length==2){
e[0]["on"+e[1]]=null;
e[0]._xinha_dom0Events[e[1]]=null;
x++;
}
}
}
catch(ex){
}
e=HTMLArea._eventFlushers.pop();
}
};
if(document.addEventListener){
HTMLArea._addEvent=function(el,_330,func){
el.addEventListener(_330,func,true);
HTMLArea._eventFlushers.push([el,_330,func]);
};
HTMLArea._removeEvent=function(el,_332,func){
el.removeEventListener(_332,func,true);
};
HTMLArea._stopEvent=function(ev){
ev.preventDefault();
ev.stopPropagation();
};
}else{
if(document.attachEvent){
HTMLArea._addEvent=function(el,_333,func){
el.attachEvent("on"+_333,func);
HTMLArea._eventFlushers.push([el,_333,func]);
};
HTMLArea._removeEvent=function(el,_334,func){
el.detachEvent("on"+_334,func);
};
HTMLArea._stopEvent=function(ev){
try{
ev.cancelBubble=true;
ev.returnValue=false;
}
catch(ex){
}
};
}else{
HTMLArea._addEvent=function(el,_335,func){
alert("_addEvent is not supported");
};
HTMLArea._removeEvent=function(el,_336,func){
alert("_removeEvent is not supported");
};
HTMLArea._stopEvent=function(ev){
alert("_stopEvent is not supported");
};
}
}
HTMLArea._addEvents=function(el,evs,func){
for(var i=evs.length;--i>=0;){
HTMLArea._addEvent(el,evs[i],func);
}
};
HTMLArea._removeEvents=function(el,evs,func){
for(var i=evs.length;--i>=0;){
HTMLArea._removeEvent(el,evs[i],func);
}
};
HTMLArea.addDom0Event=function(el,ev,fn){
HTMLArea._prepareForDom0Events(el,ev);
el._xinha_dom0Events[ev].unshift(fn);
};
HTMLArea.prependDom0Event=function(el,ev,fn){
HTMLArea._prepareForDom0Events(el,ev);
el._xinha_dom0Events[ev].push(fn);
};
HTMLArea._prepareForDom0Events=function(el,ev){
if(typeof el._xinha_dom0Events=="undefined"){
el._xinha_dom0Events={};
HTMLArea.freeLater(el,"_xinha_dom0Events");
}
if(typeof el._xinha_dom0Events[ev]=="undefined"){
el._xinha_dom0Events[ev]=[];
if(typeof el["on"+ev]=="function"){
el._xinha_dom0Events[ev].push(el["on"+ev]);
}
el["on"+ev]=function(_339){
var a=el._xinha_dom0Events[ev];
var _340=true;
for(var i=a.length;--i>=0;){
el._xinha_tempEventHandler=a[i];
if(el._xinha_tempEventHandler(_339)===false){
el._xinha_tempEventHandler=null;
_340=false;
break;
}
el._xinha_tempEventHandler=null;
}
return _340;
};
HTMLArea._eventFlushers.push([el,ev]);
}
};
HTMLArea.prototype.notifyOn=function(ev,fn){
if(typeof this._notifyListeners[ev]=="undefined"){
this._notifyListeners[ev]=[];
HTMLArea.freeLater(this,"_notifyListeners");
}
this._notifyListeners[ev].push(fn);
};
HTMLArea.prototype.notifyOf=function(ev,args){
if(this._notifyListeners[ev]){
for(var i=0;i<this._notifyListeners[ev].length;i++){
this._notifyListeners[ev][i](ev,args);
}
}
};
HTMLArea._removeClass=function(el,_341){
if(!(el&&el.className)){
return;
}
var cls=el.className.split(" ");
var ar=[];
for(var i=cls.length;i>0;){
if(cls[--i]!=_341){
ar[ar.length]=cls[i];
}
}
el.className=ar.join(" ");
};
HTMLArea._addClass=function(el,_344){
HTMLArea._removeClass(el,_344);
el.className+=" "+_344;
};
HTMLArea._hasClass=function(el,_345){
if(!(el&&el.className)){
return false;
}
var cls=el.className.split(" ");
for(var i=cls.length;i>0;){
if(cls[--i]==_345){
return true;
}
}
return false;
};
HTMLArea._blockTags=" body form textarea fieldset ul ol dl li div "+"p h1 h2 h3 h4 h5 h6 quote pre table thead "+"tbody tfoot tr td th iframe address blockquote";
HTMLArea.isBlockElement=function(el){
return el&&el.nodeType==1&&(HTMLArea._blockTags.indexOf(" "+el.tagName.toLowerCase()+" ")!=-1);
};
HTMLArea._paraContainerTags=" body td th caption fieldset div";
HTMLArea.isParaContainer=function(el){
return el&&el.nodeType==1&&(HTMLArea._paraContainerTags.indexOf(" "+el.tagName.toLowerCase()+" ")!=-1);
};
HTMLArea._closingTags=" a abbr acronym address applet b bdo big blockquote button caption center cite code del dfn dir div dl em fieldset font form frameset h1 h2 h3 h4 h5 h6 i iframe ins kbd label legend map menu noframes noscript object ol optgroup pre q s samp script select small span strike strong style sub sup table textarea title tt u ul var ";
HTMLArea.needsClosingTag=function(el){
return el&&el.nodeType==1&&(HTMLArea._closingTags.indexOf(" "+el.tagName.toLowerCase()+" ")!=-1);
};
HTMLArea.htmlEncode=function(str){
if(typeof str.replace=="undefined"){
str=str.toString();
}
str=str.replace(/&/ig,"&amp;");
str=str.replace(/</ig,"&lt;");
str=str.replace(/>/ig,"&gt;");
str=str.replace(/\xA0/g,"&nbsp;");
str=str.replace(/\x22/g,"&quot;");
return str;
};
HTMLArea.getHTML=function(root,_346,_347){
try{
return HTMLArea.getHTMLWrapper(root,_346,_347);
}
catch(ex){
alert(HTMLArea._lc("Your Document is not well formed. Check JavaScript console for details."));
return _347._iframe.contentWindow.document.body.innerHTML;
}
};
HTMLArea.getHTMLWrapper=function(root,_348,_349,_350){
var html="";
if(!_350){
_350="";
}
switch(root.nodeType){
case 10:
case 6:
case 12:
break;
case 2:
break;
case 4:
html+=(HTMLArea.is_ie?("\n"+_350):"")+"<![CDATA["+root.data+"]]>";
break;
case 5:
html+="&"+root.nodeValue+";";
break;
case 7:
html+=(HTMLArea.is_ie?("\n"+_350):"")+"<?"+root.target+" "+root.data+" ?>";
break;
case 1:
case 11:
case 9:
var _351;
var i;
var _352=(root.nodeType==1)?root.tagName.toLowerCase():"";
if(_348){
_348=!(_349.config.htmlRemoveTags&&_349.config.htmlRemoveTags.test(_352));
}
if(HTMLArea.is_ie&&_352=="head"){
if(_348){
html+=(HTMLArea.is_ie?("\n"+_350):"")+"<head>";
}
var _353=RegExp.multiline;
RegExp.multiline=true;
var txt=root.innerHTML.replace(HTMLArea.RE_tagName,function(str,p1,p2){
return p1+p2.toLowerCase();
});
RegExp.multiline=_353;
html+=txt+"\n";
if(_348){
html+=(HTMLArea.is_ie?("\n"+_350):"")+"</head>";
}
break;
}else{
if(_348){
_351=(!(root.hasChildNodes()||HTMLArea.needsClosingTag(root)));
html+=(HTMLArea.is_ie&&HTMLArea.isBlockElement(root)?("\n"+_350):"")+"<"+root.tagName.toLowerCase();
var _356=root.attributes;
for(i=0;i<_356.length;++i){
var a=_356.item(i);
if(!a.specified&&!(root.tagName.toLowerCase().match(/input|option/)&&a.nodeName=="value")){
continue;
}
var name=a.nodeName.toLowerCase();
if(/_moz_editor_bogus_node/.test(name)){
html="";
break;
}
if(/(_moz)|(contenteditable)|(_msh)/.test(name)){
continue;
}
var _358;
if(name!="style"){
if(typeof root[a.nodeName]!="undefined"&&name!="href"&&name!="src"&&!(/^on/.test(name))){
_358=root[a.nodeName];
}else{
_358=a.nodeValue;
if(HTMLArea.is_ie&&(name=="href"||name=="src")){
_358=_349.stripBaseURL(_358);
}
if(_349.config.only7BitPrintablesInURLs&&(name=="href"||name=="src")){
_358=_358.replace(/([^!-~]+)/g,function(_359){
return escape(_359);
});
}
}
}else{
_358=root.style.cssText;
}
if(/^(_moz)?$/.test(_358)){
continue;
}
html+=" "+name+"=\""+HTMLArea.htmlEncode(_358)+"\"";
}
if(html!==""){
if(_351&&_352=="p"){
html+=">&nbsp;</p>";
}else{
if(_351){
html+=" />";
}else{
html+=">";
}
}
}
}
}
var _360=false;
for(i=root.firstChild;i;i=i.nextSibling){
if(!_360&&i.nodeType==1&&HTMLArea.isBlockElement(i)){
_360=true;
}
html+=HTMLArea.getHTMLWrapper(i,true,_349,_350+"  ");
}
if(_348&&!_351){
html+=(HTMLArea.is_ie&&HTMLArea.isBlockElement(root)&&_360?("\n"+_350):"")+"</"+root.tagName.toLowerCase()+">";
}
break;
case 3:
html=/^script|style$/i.test(root.parentNode.tagName)?root.data:HTMLArea.htmlEncode(root.data);
break;
case 8:
html="<!--"+root.data+"-->";
break;
}
return html;
};
HTMLArea.prototype.stripBaseURL=function(_361){
if(this.config.baseHref===null||!this.config.stripBaseHref){
return _361;
}
var _362=this.config.baseHref.replace(/^(https?:\/\/[^\/]+)(.*)$/,"$1");
var _363=new RegExp(_362);
return _361.replace(_363,"");
};
String.prototype.trim=function(){
return this.replace(/^\s+/,"").replace(/\s+$/,"");
};
HTMLArea._makeColor=function(v){
if(typeof v!="number"){
return v;
}
var r=v&255;
var g=(v>>8)&255;
var b=(v>>16)&255;
return "rgb("+r+","+g+","+b+")";
};
HTMLArea._colorToRgb=function(v){
if(!v){
return "";
}
var r,g,b;
function hex(d){
return (d<16)?("0"+d.toString(16)):d.toString(16);
}
if(typeof v=="number"){
r=v&255;
g=(v>>8)&255;
b=(v>>16)&255;
return "#"+hex(r)+hex(g)+hex(b);
}
if(v.substr(0,3)=="rgb"){
var re=/rgb\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)/;
if(v.match(re)){
r=parseInt(RegExp.$1,10);
g=parseInt(RegExp.$2,10);
b=parseInt(RegExp.$3,10);
return "#"+hex(r)+hex(g)+hex(b);
}
return null;
}
if(v.substr(0,1)=="#"){
return v;
}
return null;
};
HTMLArea.prototype._popupDialog=function(url,_369,init){
Dialog(this.popupURL(url),_369,init);
};
HTMLArea.prototype.imgURL=function(file,_372){
if(typeof _372=="undefined"){
return _editor_url+file;
}else{
return _editor_url+"plugins/"+_372+"/img/"+file;
}
};
HTMLArea.prototype.popupURL=function(file){
var url="";
if(file.match(/^plugin:\/\/(.*?)\/(.*)/)){
var _373=RegExp.$1;
var _374=RegExp.$2;
if(!(/\.html$/.test(_374))){
_374+=".html";
}
url=_editor_url+"plugins/"+_373+"/popups/"+_374;
}else{
if(file.match(/^\/.*?/)){
url=file;
}else{
url=_editor_url+this.config.popupURL+file;
}
}
return url;
};
HTMLArea.getElementById=function(tag,id){
var el,i,objs=document.getElementsByTagName(tag);
for(i=objs.length;--i>=0&&(el=objs[i]);){
if(el.id==id){
return el;
}
}
return null;
};
HTMLArea.prototype._toggleBorders=function(){
var _375=this._doc.getElementsByTagName("TABLE");
if(_375.length!==0){
if(!this.borders){
name="bordered";
this.borders=true;
}else{
name="";
this.borders=false;
}
for(var i=0;i<_375.length;i++){
if(this.borders){
if(HTMLArea.is_gecko){
_375[i].style.display="none";
_375[i].style.display="table";
}
HTMLArea._addClass(_375[i],"htmtableborders");
}else{
HTMLArea._removeClass(_375[i],"htmtableborders");
}
}
}
return true;
};
HTMLArea.addClasses=function(el,_376){
if(el!==null){
var _377=el.className.trim().split(" ");
var ours=_376.split(" ");
for(var x=0;x<ours.length;x++){
var _379=false;
for(var i=0;_379===false&&i<_377.length;i++){
if(_377[i]==ours[x]){
_379=true;
}
}
if(_379===false){
_377[_377.length]=ours[x];
}
}
el.className=_377.join(" ").trim();
}
};
HTMLArea.removeClasses=function(el,_380){
var _381=el.className.trim().split();
var _382=[];
var _383=_380.trim().split();
for(var i=0;i<_381.length;i++){
var _384=false;
for(var x=0;x<_383.length&&!_384;x++){
if(_381[i]==_383[x]){
_384=true;
}
}
if(!_384){
_382[_382.length]=_381[i];
}
}
return _382.join(" ");
};
HTMLArea.addClass=HTMLArea._addClass;
HTMLArea.removeClass=HTMLArea._removeClass;
HTMLArea._addClasses=HTMLArea.addClasses;
HTMLArea._removeClasses=HTMLArea.removeClasses;
HTMLArea._postback=function(url,data,_386){
var req=null;
if(HTMLArea.is_ie){
req=new ActiveXObject("Microsoft.XMLHTTP");
}else{
req=new XMLHttpRequest();
}
var _388="";
for(var i in data){
_388+=(_388.length?"&":"")+i+"="+encodeURIComponent(data[i]);
}
function callBack(){
if(req.readyState==4){
if(req.status==200){
if(typeof _386=="function"){
_386(req.responseText,req);
}
}else{
alert("An error has occurred: "+req.statusText);
}
}
}
req.onreadystatechange=callBack;
req.open("POST",url,true);
req.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
req.send(_388);
};
HTMLArea._getback=function(url,_389){
var req=null;
if(HTMLArea.is_ie){
req=new ActiveXObject("Microsoft.XMLHTTP");
}else{
req=new XMLHttpRequest();
}
function callBack(){
if(req.readyState==4){
if(req.status==200){
_389(req.responseText,req);
}else{
alert("An error has occurred: "+req.statusText);
}
}
}
req.onreadystatechange=callBack;
req.open("GET",url,true);
req.send(null);
};
HTMLArea._geturlcontent=function(url){
var req=null;
if(HTMLArea.is_ie){
req=new ActiveXObject("Microsoft.XMLHTTP");
}else{
req=new XMLHttpRequest();
}
req.open("GET",url,false);
req.send(null);
if(req.status==200){
return req.responseText;
}else{
return "";
}
};
if(typeof dump=="undefined"){
function dump(o){
var s="";
for(var prop in o){
s+=prop+" = "+o[prop]+"\n";
}
var x=window.open("","debugger");
x.document.write("<pre>"+s+"</pre>");
}
}
HTMLArea.arrayContainsArray=function(a1,a2){
var _394=true;
for(var x=0;x<a2.length;x++){
var _395=false;
for(var i=0;i<a1.length;i++){
if(a1[i]==a2[x]){
_395=true;
break;
}
}
if(!_395){
_394=false;
break;
}
}
return _394;
};
HTMLArea.arrayFilter=function(a1,_396){
var _397=[];
for(var x=0;x<a1.length;x++){
if(_396(a1[x])){
_397[_397.length]=a1[x];
}
}
return _397;
};
HTMLArea.uniq_count=0;
HTMLArea.uniq=function(_398){
return _398+HTMLArea.uniq_count++;
};
HTMLArea._loadlang=function(_399){
var url,lang;
if(typeof _editor_lcbackend=="string"){
url=_editor_lcbackend;
url=url.replace(/%lang%/,_editor_lang);
url=url.replace(/%context%/,_399);
}else{
if(_399!="HTMLArea"){
url=_editor_url+"plugins/"+_399+"/lang/"+_editor_lang+".js";
}else{
url=_editor_url+"lang/"+_editor_lang+".js";
}
}
var _400=HTMLArea._geturlcontent(url);
if(_400!==""){
try{
eval("lang = "+_400);
}
catch(ex){
alert("Error reading Language-File ("+url+"):\n"+Error.toString());
lang={};
}
}else{
lang={};
}
return lang;
};
HTMLArea._lc=function(_401,_402,_403){
var ret;
if(_editor_lang=="en"){
if(typeof _401=="object"&&_401.string){
ret=_401.string;
}else{
ret=_401;
}
}else{
if(typeof HTMLArea._lc_catalog=="undefined"){
HTMLArea._lc_catalog=[];
}
if(typeof _402=="undefined"){
_402="HTMLArea";
}
if(typeof HTMLArea._lc_catalog[_402]=="undefined"){
HTMLArea._lc_catalog[_402]=HTMLArea._loadlang(_402);
}
var key;
if(typeof _401=="object"&&_401.key){
key=_401.key;
}else{
if(typeof _401=="object"&&_401.string){
key=_401.string;
}else{
key=_401;
}
}
if(typeof HTMLArea._lc_catalog[_402][key]=="undefined"){
if(_402=="HTMLArea"){
if(typeof _401=="object"&&_401.string){
ret=_401.string;
}else{
ret=_401;
}
}else{
return HTMLArea._lc(_401,"HTMLArea",_403);
}
}else{
ret=HTMLArea._lc_catalog[_402][key];
}
}
if(typeof _401=="object"&&_401.replace){
_403=_401.replace;
}
if(typeof _403!="undefined"){
for(var i in _403){
ret=ret.replace("$"+i,_403[i]);
}
}
return ret;
};
HTMLArea.hasDisplayedChildren=function(el){
var _405=el.childNodes;
for(var i=0;i<_405.length;i++){
if(_405[i].tagName){
if(_405[i].style.display!="none"){
return true;
}
}
}
return false;
};
HTMLArea._loadback=function(src,_407){
var head=document.getElementsByTagName("head")[0];
var evt=HTMLArea.is_ie?"onreadystatechange":"onload";
var _409=document.createElement("script");
_409.type="text/javascript";
_409.src=src;
_409[evt]=function(){
if(HTMLArea.is_ie&&!(/loaded|complete/.test(window.event.srcElement.readyState))){
return;
}
_407();
};
head.appendChild(_409);
};
HTMLArea.collectionToArray=function(_410){
var _411=[];
for(var i=0;i<_410.length;i++){
_411.push(_410.item(i));
}
return _411;
};
if(!Array.prototype.append){
Array.prototype.append=function(a){
for(var i=0;i<a.length;i++){
this.push(a[i]);
}
return this;
};
}
HTMLArea.makeEditors=function(_412,_413,_414){
if(typeof _413=="function"){
_413=_413();
}
var _415={};
for(var x=0;x<_412.length;x++){
var _416=new HTMLArea(_412[x],HTMLArea.cloneObject(_413));
_416.registerPlugins(_414);
_415[_412[x]]=_416;
}
return _415;
};
HTMLArea.startEditors=function(_417){
for(var i in _417){
if(_417[i].generate){
_417[i].generate();
}
}
};
HTMLArea.prototype.registerPlugins=function(_418){
if(_418){
for(var i=0;i<_418.length;i++){
this.setLoadingMessage("Register plugin $plugin","HTMLArea",{"plugin":_418[i]});
this.registerPlugin(eval(_418[i]));
}
}
};
HTMLArea.base64_encode=function(_419){
var _420="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
var _421="";
var chr1,chr2,chr3;
var enc1,enc2,enc3,enc4;
var i=0;
do{
chr1=_419.charCodeAt(i++);
chr2=_419.charCodeAt(i++);
chr3=_419.charCodeAt(i++);
enc1=chr1>>2;
enc2=((chr1&3)<<4)|(chr2>>4);
enc3=((chr2&15)<<2)|(chr3>>6);
enc4=chr3&63;
if(isNaN(chr2)){
enc3=enc4=64;
}else{
if(isNaN(chr3)){
enc4=64;
}
}
_421=_421+_420.charAt(enc1)+_420.charAt(enc2)+_420.charAt(enc3)+_420.charAt(enc4);
}while(i<_419.length);
return _421;
};
HTMLArea.base64_decode=function(_424){
var _425="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
var _426="";
var chr1,chr2,chr3;
var enc1,enc2,enc3,enc4;
var i=0;
_424=_424.replace(/[^A-Za-z0-9\+\/\=]/g,"");
do{
enc1=_425.indexOf(_424.charAt(i++));
enc2=_425.indexOf(_424.charAt(i++));
enc3=_425.indexOf(_424.charAt(i++));
enc4=_425.indexOf(_424.charAt(i++));
chr1=(enc1<<2)|(enc2>>4);
chr2=((enc2&15)<<4)|(enc3>>2);
chr3=((enc3&3)<<6)|enc4;
_426=_426+String.fromCharCode(chr1);
if(enc3!=64){
_426=_426+String.fromCharCode(chr2);
}
if(enc4!=64){
_426=_426+String.fromCharCode(chr3);
}
}while(i<_424.length);
return _426;
};
HTMLArea.removeFromParent=function(el){
if(!el.parentNode){
return;
}
var pN=el.parentNode;
pN.removeChild(el);
return el;
};
HTMLArea.hasParentNode=function(el){
if(el.parentNode){
if(el.parentNode.nodeType==11){
return false;
}
return true;
}
return false;
};
if(HTMLArea.is_ie){
HTMLArea.getOuterHTML=function(_428){
return _428.outerHTML;
};
}else{
HTMLArea.getOuterHTML=function(_429){
return (new XMLSerializer()).serializeToString(_429);
};
}
HTMLArea.findPosX=function(obj){
var _430=0;
if(obj.offsetParent){
while(obj.offsetParent){
_430+=obj.offsetLeft;
obj=obj.offsetParent;
}
}else{
if(obj.x){
_430+=obj.x;
}
}
return _430;
};
HTMLArea.findPosY=function(obj){
var _431=0;
if(obj.offsetParent){
while(obj.offsetParent){
_431+=obj.offsetTop;
obj=obj.offsetParent;
}
}else{
if(obj.y){
_431+=obj.y;
}
}
return _431;
};
HTMLArea.prototype.setLoadingMessage=function(_432,_433,_434){
if(!this.config.showLoading||!document.getElementById("loading_sub_"+this._textArea.name)){
return;
}
var elt=document.getElementById("loading_sub_"+this._textArea.name);
elt.innerHTML=HTMLArea._lc(_432,_433,_434);
};
HTMLArea.prototype.removeLoadingMessage=function(){
if(!this.config.showLoading||!document.getElementById("loading_"+this._textArea.name)){
return;
}
document.body.removeChild(document.getElementById("loading_"+this._textArea.name));
};
HTMLArea.toFree=[];
HTMLArea.freeLater=function(obj,prop){
HTMLArea.toFree.push({o:obj,p:prop});
};
HTMLArea.free=function(obj,prop){
if(obj&&!prop){
for(var p in obj){
HTMLArea.free(obj,p);
}
}else{
if(obj){
try{
obj[prop]=null;
}
catch(ex){
}
}
}
};
HTMLArea.collectGarbageForIE=function(){
HTMLArea.flushEvents();
for(var x=0;x<HTMLArea.toFree.length;x++){
if(!HTMLArea.toFree[x].o){
alert("What is "+x+" "+HTMLArea.toFree[x].o);
}
HTMLArea.free(HTMLArea.toFree[x].o,HTMLArea.toFree[x].p);
}
};
HTMLArea.init();
HTMLArea.addDom0Event(window,"unload",HTMLArea.collectGarbageForIE);

