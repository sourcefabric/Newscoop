HTMLArea.version={"Release":"Trunk","Head":"$HeadURL: http://svn.xinha.python-hosting.com/trunk/htmlarea.js $".replace(/^[^:]*: (.*) \$$/,"$1"),"Date":"$LastChangedDate$".replace(/^[^:]*: ([0-9-]*) ([0-9:]*) ([+0-9]*) \((.*)\) \$/,"$4 $2 $3"),"Revision":"$LastChangedRevision: 421 $".replace(/^[^:]*: (.*) \$$/,"$1"),"RevisionBy":"$LastChangedBy$".replace(/^[^:]*: (.*) \$$/,"$1")};
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
if(!(typeof _editor_skin=="string")){
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
this._editMode="wysiwyg";
this.plugins={};
this._timerToolbar=null;
this._timerUndo=null;
this._undoQueue=new Array(this.config.undoSteps);
this._undoPos=-1;
this._customUndo=true;
this._mdoc=document;
this.doctype="";
this.__htmlarea_id_num=__htmlareas.length;
__htmlareas[this.__htmlarea_id_num]=this;
this._notifyListeners={};
var _3=this._panels={right:{on:true,container:document.createElement("td"),panels:[]},left:{on:true,container:document.createElement("td"),panels:[]},top:{on:true,container:document.createElement("td"),panels:[]},bottom:{on:true,container:document.createElement("td"),panels:[]}};
for(var i in _3){
if(!_3[i].container){
continue;
}
_3[i].div=_3[i].container;
_3[i].container.className="panels "+i;
HTMLArea.freeLater(_3[i],"container");
HTMLArea.freeLater(_3[i],"div");
}
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
HTMLArea.RE_email=/[a-z0-9_]{3,}@[a-z0-9_-]{2,}(\.[a-z0-9_-]{2,})+/i;
HTMLArea.RE_url=/(https?:\/\/)?(([a-z0-9_]+:[a-z0-9_]+@)?[a-z0-9_-]{2,}(\.[a-z0-9_-]{2,}){2,}(:[0-9]+)?(\/\S+)*)/i;
HTMLArea.Config=function(){
var _5=this;
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
this.toolbar=[["popupeditor"],["separator","formatblock","fontname","fontsize","bold","italic","underline","strikethrough"],["separator","forecolor","hilitecolor","textindicator"],["separator","subscript","superscript"],["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],["separator","insertorderedlist","insertunorderedlist","outdent","indent"],["separator","inserthorizontalrule","createlink","insertimage","inserttable"],["separator","undo","redo","selectall","print"],(HTMLArea.is_gecko?[]:["cut","copy","paste","overwrite","saveas"]),["separator","killword","clearfonts","removeformat","toggleborders","splitblock","lefttoright","righttoleft"],["separator","htmlmode","showhelp","about"]];
this.fontname={"&mdash; font &mdash;":"","Arial":"arial,helvetica,sans-serif","Courier New":"courier new,courier,monospace","Georgia":"georgia,times new roman,times,serif","Tahoma":"tahoma,arial,helvetica,sans-serif","Times New Roman":"times new roman,times,serif","Verdana":"verdana,arial,helvetica,sans-serif","impact":"impact","WingDings":"wingdings"};
this.fontsize={"&mdash; size &mdash;":"","1 (8 pt)":"1","2 (10 pt)":"2","3 (12 pt)":"3","4 (14 pt)":"4","5 (18 pt)":"5","6 (24 pt)":"6","7 (36 pt)":"7"};
this.formatblock={"&mdash; format &mdash;":"","Heading 1":"h1","Heading 2":"h2","Heading 3":"h3","Heading 4":"h4","Heading 5":"h5","Heading 6":"h6","Normal":"p","Address":"address","Formatted":"pre"};
this.customSelects={};
function cut_copy_paste(e,_7,_8){
e.execCommand(_7);
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
var _9=this.btnList[i];
if(typeof _9!="object"){
continue;
}
if(typeof _9[1]!="string"){
_9[1][0]=_editor_url+this.imgURL+_9[1][0];
}else{
_9[1]=_editor_url+this.imgURL+_9[1];
}
_9[0]=HTMLArea._lc(_9[0]);
}
};
HTMLArea.Config.prototype.registerButton=function(id,_11,_12,_13,_14,_15){
var _16;
if(typeof id=="string"){
_16=id;
}else{
if(typeof id=="object"){
_16=id.id;
}else{
alert("ERROR [HTMLArea.Config::registerButton]:\ninvalid arguments");
return false;
}
}
if(typeof this.customSelects[_16]!="undefined"){
}
if(typeof this.btnList[_16]!="undefined"){
}
switch(typeof id){
case "string":
this.btnList[id]=[_11,_12,_13,_14,_15];
break;
case "object":
this.btnList[id.id]=[id.tooltip,id.image,id.textMode,id.action,id.context];
break;
}
};
HTMLArea.prototype.registerPanel=function(_17,_18){
if(!_17){
_17="right";
}
var _19=this.addPanel(_17);
if(_18){
_18.drawPanelIn(_19);
}
};
HTMLArea.Config.prototype.registerDropdown=function(_20){
if(typeof this.customSelects[_20.id]!="undefined"){
}
if(typeof this.btnList[_20.id]!="undefined"){
}
this.customSelects[_20.id]=_20;
};
HTMLArea.Config.prototype.hideSomeButtons=function(_21){
var _22=this.toolbar;
for(var i=_22.length;--i>=0;){
var _23=_22[i];
for(var j=_23.length;--j>=0;){
if(_21.indexOf(" "+_23[j]+" ")>=0){
var len=1;
if(/separator|space/.test(_23[j+1])){
len=2;
}
_23.splice(j,len);
}
}
}
};
HTMLArea.Config.prototype.addToolbarElement=function(id,_26,_27){
var _28=this.toolbar;
var a,i,j,o,sid;
var _30=false;
var _31=false;
var _32=0;
var _33=0;
var _34=0;
var _35=false;
var _36=false;
if((id&&typeof id=="object")&&(id.constructor==Array)){
_30=true;
}
if((_26&&typeof _26=="object")&&(_26.constructor==Array)){
_31=true;
_32=_26.length;
}
if(_30){
for(i=0;i<id.length;++i){
if((id[i]!="separator")&&(id[i].indexOf("T[")!=0)){
sid=id[i];
}
}
}else{
sid=id;
}
for(var i=0;!_35&&!_36&&i<_28.length;++i){
a=_28[i];
for(j=0;!_36&&j<a.length;++j){
if(a[i]==sid){
_35=true;
break;
}
if(_31){
for(o=0;o<_32;++o){
if(a[j]==_26[o]){
if(o==0){
_36=true;
j--;
break;
}else{
_34=i;
_33=j;
_32=o;
}
}
}
}else{
if(a[j]==_26){
_36=true;
break;
}
}
}
}
if(!_35){
if(!_36&&_31){
if(_26.length!=_32){
j=_33;
a=_28[_34];
_36=true;
}
}
if(_36){
if(_27==0){
if(_30){
a[j]=id[id.length-1];
for(i=id.length-1;--i>=0;){
a.splice(j,0,id[i]);
}
}else{
a[j]=id;
}
}else{
if(_27<0){
j=j+_27+1;
}else{
if(_27>0){
j=j+_27;
}
}
if(_30){
for(i=id.length;--i>=0;){
a.splice(j,0,id[i]);
}
}else{
a.splice(j,0,id);
}
}
}else{
_28[0].splice(0,0,"separator");
if(_30){
for(i=id.length;--i>=0;){
_28[0].splice(0,0,id[i]);
}
}else{
_28[0].splice(0,0,id);
}
}
}
};
HTMLArea.replaceAll=function(_37){
var tas=document.getElementsByTagName("textarea");
for(var i=tas.length;i>0;(new HTMLArea(tas[--i],_37)).generate()){
}
};
HTMLArea.replace=function(id,_39){
var ta=HTMLArea.getElementById("textarea",id);
return ta?(new HTMLArea(ta,_39)).generate():null;
};
HTMLArea.prototype._createToolbar=function(){
var _41=this;
var _42=document.createElement("div");
this._toolBar=this._toolbar=_42;
_42.className="toolbar";
_42.unselectable="1";
HTMLArea.freeLater(this,"_toolBar");
HTMLArea.freeLater(this,"_toolbar");
var _43=null;
var _44=new Object();
this._toolbarObjects=_44;
this._createToolbar1(_41,_42,_44);
this._htmlArea.appendChild(_42);
return _42;
};
HTMLArea.prototype._setConfig=function(_45){
this.config=_45;
};
HTMLArea.prototype._addToolbar=function(){
this._createToolbar1(this,this._toolbar,this._toolbarObjects);
};
HTMLArea.prototype._createToolbar1=function(_46,_47,_48){
if(_46.config.flowToolbars){
var brk=document.createElement("div");
brk.style.height=brk.style.width=brk.style.lineHeight=brk.style.fontSize="1px";
brk.style.clear="both";
_47.appendChild(brk);
}
function newLine(){
if(typeof tb_row!="undefined"&&tb_row.childNodes.length==0){
return;
}
var _50=document.createElement("table");
_50.border="0px";
_50.cellSpacing="0px";
_50.cellPadding="0px";
if(_46.config.flowToolbars){
if(HTMLArea.is_ie){
_50.style.styleFloat="left";
}else{
_50.style.cssFloat="left";
}
}
_47.appendChild(_50);
var _51=document.createElement("tbody");
_50.appendChild(_51);
tb_row=document.createElement("tr");
_51.appendChild(tb_row);
_50.className="toolbarRow";
}
newLine();
function setButtonStatus(id,_52){
var _53=this[id];
var el=this.element;
if(_53!=_52){
switch(id){
case "enabled":
if(_52){
HTMLArea._removeClass(el,"buttonDisabled");
el.disabled=false;
}else{
HTMLArea._addClass(el,"buttonDisabled");
el.disabled=true;
}
break;
case "active":
if(_52){
HTMLArea._addClass(el,"buttonPressed");
}else{
HTMLArea._removeClass(el,"buttonPressed");
}
break;
}
this[id]=_52;
}
}
function createSelect(txt){
var _56=null;
var el=null;
var cmd=null;
var _58=_46.config.customSelects;
var _59=null;
var _60="";
switch(txt){
case "fontsize":
case "fontname":
case "formatblock":
_56=_46.config[txt];
cmd=txt;
break;
default:
cmd=txt;
var _61=_58[cmd];
if(typeof _61!="undefined"){
_56=_61.options;
_59=_61.context;
if(typeof _61.tooltip!="undefined"){
_60=_61.tooltip;
}
}else{
alert("ERROR [createSelect]:\nCan't find the requested dropdown definition");
}
break;
}
if(_56){
el=document.createElement("select");
el.title=_60;
var obj={name:txt,element:el,enabled:true,text:false,cmd:cmd,state:setButtonStatus,context:_59};
HTMLArea.freeLater(obj);
_48[txt]=obj;
for(var i in _56){
if(typeof (_56[i])!="string"){
continue;
}
var op=document.createElement("option");
op.innerHTML=HTMLArea._lc(i);
op.value=_56[i];
el.appendChild(op);
}
HTMLArea._addEvent(el,"change",function(){
_46._comboSelected(el,txt);
});
}
return el;
}
function createButton(txt){
var el=null;
var btn=null;
switch(txt){
case "separator":
if(_46.config.flowToolbars){
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
var obj={name:txt,element:el,enabled:true,active:false,text:false,cmd:"textindicator",state:setButtonStatus};
HTMLArea.freeLater(obj);
_48[txt]=obj;
break;
default:
btn=_46.config.btnList[txt];
}
if(!el&&btn){
el=document.createElement("a");
el.style.display="block";
el.href="javascript:void(0)";
el.style.textDecoration="none";
el.title=btn[0];
el.className="button";
var obj={name:txt,element:el,enabled:true,active:false,text:btn[2],cmd:btn[3],state:setButtonStatus,context:btn[4]||null};
HTMLArea.freeLater(obj);
_48[txt]=obj;
HTMLArea._addEvent(el,"mouseout",function(){
if(obj.enabled){
with(HTMLArea){
_removeClass(el,"buttonActive");
(obj.active)&&_addClass(el,"buttonPressed");
}
}
});
HTMLArea._addEvent(el,"mousedown",function(ev){
if(obj.enabled){
with(HTMLArea){
_addClass(el,"buttonActive");
_removeClass(el,"buttonPressed");
_stopEvent(is_ie?window.event:ev);
}
}
});
HTMLArea._addEvent(el,"click",function(ev){
if(obj.enabled){
with(HTMLArea){
_removeClass(el,"buttonActive");
if(HTMLArea.is_gecko){
_46.activateEditor();
}
obj.cmd(_46,obj.name,obj);
_stopEvent(is_ie?window.event:ev);
}
}
});
var _66=HTMLArea.makeBtnImg(btn[1]);
var img=_66.firstChild;
el.appendChild(_66);
obj.imgel=img;
obj.swapImage=function(_68){
if(typeof _68!="string"){
img.src=_68[0];
img.style.position="relative";
img.style.top=_68[2]?("-"+(18*(_68[2]+1))+"px"):"-18px";
img.style.left=_68[1]?("-"+(18*(_68[1]+1))+"px"):"-18px";
}else{
obj.imgel.src=_68;
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
var _69=true;
for(var i=0;i<this.config.toolbar.length;++i){
if(!_69){
}else{
_69=false;
}
if(this.config.toolbar[i]==null){
this.config.toolbar[i]=["separator"];
}
var _70=this.config.toolbar[i];
for(var j=0;j<_70.length;++j){
var _71=_70[j];
if(/^([IT])\[(.*?)\]/.test(_71)){
var _72=RegExp.$1=="I";
var _73=RegExp.$2;
if(_72){
_73=HTMLArea._lc(_73);
}
var _74=document.createElement("td");
tb_row.appendChild(_74);
_74.className="label";
_74.innerHTML=_73;
}else{
if(typeof _71!="function"){
var _75=createButton(_71);
if(_75){
var _74=document.createElement("td");
_74.className="toolbarElement";
tb_row.appendChild(_74);
_74.appendChild(_75);
}else{
if(_75==null){
alert("FIXME: Unknown toolbar item: "+_71);
}
}
}
}
}
}
if(_46.config.flowToolbars){
var brk=document.createElement("div");
brk.style.height=brk.style.width=brk.style.lineHeight=brk.style.fontSize="1px";
brk.style.clear="both";
_47.appendChild(brk);
}
return _47;
};
use_clone_img=false;
HTMLArea.makeBtnImg=function(_76,doc){
if(!doc){
doc=document;
}
if(!doc._htmlareaImgCache){
doc._htmlareaImgCache={};
HTMLArea.freeLater(doc._htmlareaImgCache);
}
var _78=null;
if(HTMLArea.is_ie&&((!doc.compatMode)||(doc.compatMode&&doc.compatMode=="BackCompat"))){
_78=doc.createElement("span");
}else{
_78=doc.createElement("div");
_78.style.position="relative";
}
_78.style.overflow="hidden";
_78.style.width="18px";
_78.style.height="18px";
_78.className="buttonImageContainer";
var img=null;
if(typeof _76=="string"){
if(doc._htmlareaImgCache[_76]){
img=doc._htmlareaImgCache[_76].cloneNode();
}else{
img=doc.createElement("img");
img.src=_76;
img.style.width="18px";
img.style.height="18px";
if(use_clone_img){
doc._htmlareaImgCache[_76]=img.cloneNode();
}
}
}else{
if(doc._htmlareaImgCache[_76[0]]){
img=doc._htmlareaImgCache[_76[0]].cloneNode();
}else{
img=doc.createElement("img");
img.src=_76[0];
img.style.position="relative";
if(use_clone_img){
doc._htmlareaImgCache[_76[0]]=img.cloneNode();
}
}
img.style.top=_76[2]?("-"+(18*(_76[2]+1))+"px"):"-18px";
img.style.left=_76[1]?("-"+(18*(_76[1]+1))+"px"):"-18px";
}
_78.appendChild(img);
return _78;
};
HTMLArea.prototype._createStatusBar=function(){
var _79=document.createElement("div");
_79.className="statusBar";
this._statusBar=_79;
HTMLArea.freeLater(this,"_statusBar");
div=document.createElement("span");
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
_79.style.display="none";
}
return _79;
};
HTMLArea.prototype.generate=function(){
var _80=this;
if(typeof Dialog=="undefined"){
HTMLArea._loadback(_editor_url+"dialog.js",function(){
_80.generate();
});
return false;
}
if(typeof HTMLArea.Dialog=="undefined"){
HTMLArea._loadback(_editor_url+"inline-dialog.js",function(){
_80.generate();
});
return false;
}
if(typeof PopupWin=="undefined"){
HTMLArea._loadback(_editor_url+"popupwin.js",function(){
_80.generate();
});
return false;
}
if(_editor_skin!=""){
var _81=false;
var _82=document.getElementsByTagName("head")[0];
var _83=document.getElementsByTagName("link");
for(var i=0;i<_83.length;i++){
if((_83[i].rel=="stylesheet")&&(_83[i].href==_editor_url+"skins/"+_editor_skin+"/skin.css")){
_81=true;
}
}
if(!_81){
var _84=document.createElement("link");
_84.type="text/css";
_84.href=_editor_url+"skins/"+_editor_skin+"/skin.css";
_84.rel="stylesheet";
_82.appendChild(_84);
}
}
var _85=_80.config.toolbar;
for(var i=_85.length;--i>=0;){
for(var j=_85[i].length;--j>=0;){
if(_85[i][j]=="popupeditor"){
if(typeof FullScreen=="undefined"){
HTMLArea.loadPlugin("FullScreen",function(){
_80.generate();
});
return false;
}
_80.registerPlugin("FullScreen");
}
}
}
if(HTMLArea.is_gecko){
switch(_80.config.mozParaHandler){
case "best":
if(typeof EnterParagraphs=="undefined"){
HTMLArea.loadPlugin("EnterParagraphs",function(){
_80.generate();
});
return false;
}
_80.registerPlugin("EnterParagraphs");
break;
case "dirty":
case "built-in":
default:
break;
}
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
var _87=this._framework.table;
this._htmlArea=_87;
HTMLArea.freeLater(this,"_htmlArea");
_87.className="htmlarea";
var _85=this._createToolbar();
this._framework.tb_cell.appendChild(_85);
var _88=document.createElement("iframe");
_88.src=_editor_url+_80.config.URIs["blank"];
this._framework.ed_cell.appendChild(_88);
this._iframe=_88;
this._iframe.className="xinha_iframe";
HTMLArea.freeLater(this,"_iframe");
var _89=this._createStatusBar();
this._framework.sb_cell.appendChild(_89);
var _90=this._textArea;
_90.parentNode.insertBefore(_87,_90);
_90.className="xinha_textarea";
HTMLArea.removeFromParent(_90);
this._framework.ed_cell.appendChild(_90);
if(_90.form){
HTMLArea.prependDom0Event(this._textArea.form,"submit",function(){
_80._textArea.value=_80.outwardHtml(_80.getHTML());
return true;
});
var _91=_90.value;
HTMLArea.prependDom0Event(this._textArea.form,"reset",function(){
_80.setHTML(_80.inwardHtml(_91));
_80.updateToolbar();
return true;
});
}
HTMLArea.prependDom0Event(window,"unload",function(){
_90.value=_80.outwardHtml(_80.getHTML());
return true;
});
_90.style.display="none";
_80.initSize();
_80._iframeLoadDone=false;
HTMLArea._addEvent(this._iframe,"load",function(e){
if(!_80._iframeLoadDone){
_80._iframeLoadDone=true;
_80.initIframe();
}
return true;
});
};
HTMLArea.prototype.initSize=function(){
var _92=this;
var _93=null;
var _94=null;
switch(this.config.width){
case "auto":
_93=this._initial_ta_size.w;
break;
case "toolbar":
_93=this._toolBar.offsetWidth+"px";
break;
default:
_93=/[^0-9]/.test(this.config.width)?this.config.width:this.config.width+"px";
break;
}
switch(this.config.height){
case "auto":
_94=this._initial_ta_size.h;
break;
default:
_94=/[^0-9]/.test(this.config.height)?this.config.height:this.config.height+"px";
break;
}
this.sizeEditor(_93,_94,this.config.sizeIncludesBars,this.config.sizeIncludesPanels);
HTMLArea.addDom0Event(window,"resize",function(e){
_92.sizeEditor();
});
this.notifyOn("panel_change",function(){
_92.sizeEditor();
});
};
HTMLArea.prototype.sizeEditor=function(_95,_96,_97,_98){
this._iframe.style.height="100%";
this._textArea.style.height="100%";
this._iframe.style.width="";
this._textArea.style.width="";
if(_97!=null){
this._htmlArea.sizeIncludesToolbars=_97;
}
if(_98!=null){
this._htmlArea.sizeIncludesPanels=_98;
}
if(_95!=null){
this._htmlArea.style.width=_95;
if(!this._htmlArea.sizeIncludesPanels){
var _99=this._panels.right;
if(_99.on&&_99.panels.length&&HTMLArea.hasDisplayedChildren(_99.div)){
this._htmlArea.style.width=this._htmlArea.offsetWidth+parseInt(this.config.panel_dimensions.right);
}
var _99=this._panels.left;
if(_99.on&&_99.panels.length&&HTMLArea.hasDisplayedChildren(_99.div)){
this._htmlArea.style.width=this._htmlArea.offsetWidth+parseInt(this.config.panel_dimensions.left);
}
}
}
if(_96!=null){
this._htmlArea.style.height=_96;
if(!this._htmlArea.sizeIncludesToolbars){
this._htmlArea.style.height=this._htmlArea.offsetHeight+this._toolbar.offsetHeight+this._statusBar.offsetHeight;
}
if(!this._htmlArea.sizeIncludesPanels){
var _99=this._panels.top;
if(_99.on&&_99.panels.length&&HTMLArea.hasDisplayedChildren(_99.div)){
this._htmlArea.style.height=this._htmlArea.offsetHeight+parseInt(this.config.panel_dimensions.top);
}
var _99=this._panels.bottom;
if(_99.on&&_99.panels.length&&HTMLArea.hasDisplayedChildren(_99.div)){
this._htmlArea.style.height=this._htmlArea.offsetHeight+parseInt(this.config.panel_dimensions.bottom);
}
}
}
_95=this._htmlArea.offsetWidth;
_96=this._htmlArea.offsetHeight;
var _100=this._panels;
var _101=this;
var _102=1;
function panel_is_alive(pan){
if(_100[pan].on&&_100[pan].panels.length&&HTMLArea.hasDisplayedChildren(_100[pan].container)){
_100[pan].container.style.display="";
return true;
}else{
_100[pan].container.style.display="none";
return false;
}
}
if(panel_is_alive("left")){
_102+=1;
}
if(panel_is_alive("top")){
}
if(panel_is_alive("right")){
_102+=1;
}
if(panel_is_alive("bottom")){
}
this._framework.tb_cell.colSpan=_102;
this._framework.tp_cell.colSpan=_102;
this._framework.bp_cell.colSpan=_102;
this._framework.sb_cell.colSpan=_102;
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
var _104=_96-this._toolBar.offsetHeight-this._statusBar.offsetHeight;
if(panel_is_alive("top")){
_104-=parseInt(this.config.panel_dimensions.top);
}
if(panel_is_alive("bottom")){
_104-=parseInt(this.config.panel_dimensions.bottom);
}
this._iframe.style.height=_104+"px";
var _105=_95;
if(panel_is_alive("left")){
_105-=parseInt(this.config.panel_dimensions.left);
}
if(panel_is_alive("right")){
_105-=parseInt(this.config.panel_dimensions.right);
}
this._iframe.style.width=_105+"px";
this._textArea.style.height=this._iframe.style.height;
this._textArea.style.width=this._iframe.style.width;
this.notifyOf("resize",{width:this._htmlArea.offsetWidth,height:this._htmlArea.offsetHeight});
};
HTMLArea.prototype.addPanel=function(side){
var div=document.createElement("div");
div.side=side;
if(side=="left"||side=="right"){
div.style.width=this.config.panel_dimensions[side];
}
HTMLArea.addClasses(div,"panel");
this._panels[side].panels.push(div);
this._panels[side].div.appendChild(div);
this.notifyOf("panel_change",{"action":"add","panel":div});
return div;
};
HTMLArea.prototype.removePanel=function(_108){
this._panels[_108.side].div.removeChild(_108);
var _109=[];
for(var i=0;i<this._panels[_108.side].panels.length;i++){
if(this._panels[_108.side].panels[i]!=_108){
_109.push(this._panels[_108.side].panels[i]);
}
}
this._panels[_108.side].panels=_109;
this.notifyOf("panel_change",{"action":"remove","panel":_108});
};
HTMLArea.prototype.hidePanel=function(_110){
if(_110){
_110.style.display="none";
this.notifyOf("panel_change",{"action":"hide","panel":_110});
}
};
HTMLArea.prototype.showPanel=function(_111){
if(_111){
_111.style.display="";
this.notifyOf("panel_change",{"action":"show","panel":_111});
}
};
HTMLArea.prototype.hidePanels=function(_112){
if(typeof _112=="undefined"){
_112=["left","right","top","bottom"];
}
var _113=[];
for(var i=0;i<_112.length;i++){
if(this._panels[_112[i]].on){
_113.push(_112[i]);
this._panels[_112[i]].on=false;
}
}
this.notifyOf("panel_change",{"action":"multi_hide","sides":_112});
};
HTMLArea.prototype.showPanels=function(_114){
if(typeof _114=="undefined"){
_114=["left","right","top","bottom"];
}
var _115=[];
for(var i=0;i<_114.length;i++){
if(!this._panels[_114[i]].on){
_115.push(_114[i]);
this._panels[_114[i]].on=true;
}
}
this.notifyOf("panel_change",{"action":"multi_show","sides":_114});
};
HTMLArea.objectProperties=function(obj){
var _116=[];
for(var x in obj){
_116[_116.length]=x;
}
return _116;
};
HTMLArea.prototype.editorIsActivated=function(){
try{
if(HTMLArea.is_gecko){
return (this._doc.designMode=="on");
}else{
return (this._doc.body.contentEditable);
}
}
catch(e){
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
catch(e){
}
}else{
if(!HTMLArea.is_gecko&&this._doc.body.contentEditable!=true){
this._doc.body.contentEditable=true;
}
}
HTMLArea._someEditorHasBeenActivated=true;
HTMLArea._currentlyActiveEditor=this;
var _118=this;
this.enableToolbar();
};
HTMLArea.prototype.deactivateEditor=function(){
this.disableToolbar();
if(HTMLArea.is_gecko&&this._doc.designMode!="off"){
try{
this._doc.designMode="off";
}
catch(e){
}
}else{
if(!HTMLArea.is_gecko&&this._doc.body.contentEditable!=false){
this._doc.body.contentEditable=false;
}
}
if(HTMLArea._currentlyActiveEditor!=this){
return;
}
HTMLArea._currentlyActiveEditor=false;
};
HTMLArea.prototype.initIframe=function(){
this.disableToolbar();
var doc=null;
var _119=this;
try{
if(_119._iframe.contentDocument){
this._doc=_119._iframe.contentDocument;
}else{
this._doc=_119._iframe.contentWindow.document;
}
doc=this._doc;
if(!doc){
if(HTMLArea.is_gecko){
setTimeout(function(){
_119.initIframe();
},50);
return false;
}else{
alert("ERROR: IFRAME can't be initialized.");
}
}
}
catch(e){
setTimeout(function(){
_119.initIframe();
},50);
}
HTMLArea.freeLater(this,"_doc");
doc.open();
if(!_119.config.fullPage){
var html="<html>\n";
html+="<head>\n";
html+="<meta http-equiv=\"Content-Type\" content=\"text/html; charset="+_119.config.charSet+"\">\n";
if(typeof _119.config.baseHref!="undefined"&&_119.config.baseHref!=null){
html+="<base href=\""+_119.config.baseHref+"\"/>\n";
}
html+="<style title=\"table borders\">"+".htmtableborders, .htmtableborders td, .htmtableborders th {border : 1px dashed lightgrey ! important;} \n"+"</style>\n";
html+="<style type=\"text/css\">"+"html, body { border: 0px;  background-color: #ffffff; } \n"+"span.macro, span.macro ul, span.macro div, span.macro p {background : #CCCCCC;}\n"+"</style>\n";
if(_119.config.pageStyle){
html+="<style type=\"text/css\">\n"+_119.config.pageStyle+"\n</style>";
}
if(typeof _119.config.pageStyleSheets!=="undefined"){
for(style_i=0;style_i<_119.config.pageStyleSheets.length;style_i++){
if(_119.config.pageStyleSheets[style_i].length>0){
html+="<link rel=\"stylesheet\" type=\"text/css\" href=\""+_119.config.pageStyleSheets[style_i]+"\">";
}
}
}
html+="</head>\n";
html+="<body>\n";
html+=_119.inwardHtml(_119._textArea.value);
html+="</body>\n";
html+="</html>";
}else{
var html=_119.inwardHtml(_119._textArea.value);
if(html.match(HTMLArea.RE_doctype)){
_119.setDoctype(RegExp.$1);
html=html.replace(HTMLArea.RE_doctype,"");
}
}
doc.write(html);
doc.close();
this.setEditorEvents();
};
HTMLArea.prototype.whenDocReady=function(_121){
var _122=this;
if(!this._doc.body){
setTimeout(function(){
_122.whenDocReady(_121);
},50);
}else{
_121();
}
};
HTMLArea.prototype.setMode=function(mode){
if(typeof mode=="undefined"){
mode=((this._editMode=="textmode")?"wysiwyg":"textmode");
}
switch(mode){
case "textmode":
var html=this.outwardHtml(this.getHTML());
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
var html=this.inwardHtml(this.getHTML());
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
var _124=this.plugins[i].instance;
if(_124&&typeof _124.onMode=="function"){
_124.onMode(mode);
}
}
};
HTMLArea.prototype.setFullHTML=function(html){
var _125=RegExp.multiline;
RegExp.multiline=true;
if(html.match(HTMLArea.RE_doctype)){
this.setDoctype(RegExp.$1);
html=html.replace(HTMLArea.RE_doctype,"");
}
RegExp.multiline=_125;
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
var _127=/<html>((.|\n)*?)<\/html>/i;
html=html.replace(_127,"$1");
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
var _128=this;
var doc=this._doc;
_128.whenDocReady(function(){
HTMLArea._addEvents(doc,["mousedown"],function(){
_128.activateEditor();
return true;
});
HTMLArea._addEvents(doc,["keydown","keypress","mousedown","mouseup","drag"],function(_129){
return _128._editorEvent(HTMLArea.is_ie?_128._iframe.contentWindow.event:_129);
});
for(var i in _128.plugins){
var _130=_128.plugins[i].instance;
HTMLArea.refreshPlugin(_130);
}
if(typeof _128._onGenerate=="function"){
_128._onGenerate();
}
});
};
HTMLArea.prototype.registerPlugin=function(){
var _131=arguments[0];
if(_131==null||typeof _131=="undefined"||(typeof _131=="string"&&eval("typeof "+_131)=="undefined")){
return false;
}
var args=[];
for(var i=1;i<arguments.length;++i){
args.push(arguments[i]);
}
return this.registerPlugin2(_131,args);
};
HTMLArea.prototype.registerPlugin2=function(_133,args){
if(typeof _133=="string"){
_133=eval(_133);
}
if(typeof _133=="undefined"){
return false;
}
var obj=new _133(this,args);
if(obj){
var _134={};
var info=_133._pluginInfo;
for(var i in info){
_134[i]=info[i];
}
_134.instance=obj;
_134.args=args;
this.plugins[_133._pluginInfo.name]=_134;
return obj;
}else{
alert("Can't register plugin "+_133.toString()+".");
}
};
HTMLArea.getPluginDir=function(_136){
return _editor_url+"plugins/"+_136;
};
HTMLArea.loadPlugin=function(_137,_138){
if(eval("typeof "+_137)!="undefined"){
if(_138){
_138(_137);
}
return true;
}
var dir=this.getPluginDir(_137);
var _140=_137.replace(/([a-z])([A-Z])([a-z])/g,function(str,l1,l2,l3){
return l1+"-"+l2.toLowerCase()+l3;
}).toLowerCase()+".js";
var _145=dir+"/"+_140;
if(_138){
HTMLArea._loadback(_145,function(){
_138(_137);
});
}else{
document.write("<script type='text/javascript' src='"+_145+"'></script>");
}
return false;
};
HTMLArea._pluginLoadStatus={};
HTMLArea.loadPlugins=function(_146,_147){
var _148=true;
var _149=HTMLArea.cloneObject(_146);
while(_149.length){
var p=_149.pop();
if(typeof HTMLArea._pluginLoadStatus[p]=="undefined"){
HTMLArea._pluginLoadStatus[p]="loading";
HTMLArea.loadPlugin(p,function(_151){
if(eval("typeof "+_151)!="undefined"){
HTMLArea._pluginLoadStatus[_151]="ready";
}else{
HTMLArea._pluginLoadStatus[_151]="failed";
}
});
_148=false;
}else{
switch(HTMLArea._pluginLoadStatus[p]){
case "failed":
case "ready":
break;
case "loading":
default:
_148=false;
break;
}
}
}
if(_148){
return true;
}
if(_147){
setTimeout(function(){
if(HTMLArea.loadPlugins(_146,_147)){
_147();
}
},150);
}
return _148;
};
HTMLArea.refreshPlugin=function(_152){
if(_152&&typeof _152.onGenerate=="function"){
_152.onGenerate();
}
if(_152&&typeof _152.onGenerateOnce=="function"){
_152.onGenerateOnce();
_152.onGenerateOnce=null;
}
};
HTMLArea.loadStyle=function(_153,_154){
var url=_editor_url||"";
if(typeof _154!="undefined"){
url+="plugins/"+_154+"/";
}
url+=_153;
if(/^\//.test(_153)){
url=_153;
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
function debug(_158,str){
for(;--_158>=0;){
ta.value+=" ";
}
ta.value+=str+"\n";
}
function _dt(root,_160){
var tag=root.tagName.toLowerCase(),i;
var ns=HTMLArea.is_ie?root.scopeName:root.prefix;
debug(_160,"- "+tag+" ["+ns+"]");
for(i=root.firstChild;i;i=i.nextSibling){
if(i.nodeType==1){
_dt(i,_160+2);
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
var _163=this,stats={empty_tags:0,mso_class:0,mso_style:0,mso_xmlel:0,orig_len:this._doc.body.innerHTML.length,T:(new Date()).getTime()},stats_txt={empty_tags:"Empty tags removed: ",mso_class:"MSO class names removed: ",mso_style:"MSO inline style removed: ",mso_xmlel:"MSO XML elements stripped: "};
function showStats(){
var txt="HTMLArea word cleaner stats: \n\n";
for(var i in stats){
if(stats_txt[i]){
txt+=stats_txt[i]+stats[i]+"\n";
}
}
txt+="\nInitial document length: "+stats.orig_len+"\n";
txt+="Final document length: "+_163._doc.body.innerHTML.length+"\n";
txt+="Clean-up took "+(((new Date()).getTime()-stats.T)/1000)+" seconds";
alert(txt);
}
function clearClass(node){
var newc=node.className.replace(/(^|\s)mso.*?(\s|$)/ig," ");
if(newc!=node.className){
node.className=newc;
if(!/\S/.test(node.className)){
node.removeAttribute("className");
++stats.mso_class;
}
}
}
function clearStyle(node){
var _166=node.style.cssText.split(/\s*;\s*/);
for(var i=_166.length;--i>=0;){
if(/^mso|^tab-stops/i.test(_166[i])||/^margin\s*:\s*0..\s+0..\s+0../i.test(_166[i])){
++stats.mso_style;
_166.splice(i,1);
}
}
node.style.cssText=_166.join("; ");
}
function stripTag(el){
if(HTMLArea.is_ie){
el.outerHTML=HTMLArea.htmlEncode(el.innerText);
}else{
var txt=document.createTextNode(HTMLArea.getInnerText(el));
el.parentNode.insertBefore(txt,el);
HTMLArea.removeFromParent(el);
}
++stats.mso_xmlel;
}
function checkEmpty(el){
if(/^(a|span|b|strong|i|em|font)$/i.test(el.tagName)&&!el.firstChild){
HTMLArea.removeFromParent(el);
++stats.empty_tags;
}
}
function parseTree(root){
var tag=root.tagName.toLowerCase(),i,next;
if((HTMLArea.is_ie&&root.scopeName!="HTML")||(!HTMLArea.is_ie&&/:/.test(tag))){
stripTag(root);
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
catch(e){
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
HTMLArea.prototype.disableToolbar=function(_169){
if(this._timerToolbar){
clearTimeout(this._timerToolbar);
}
if(typeof _169=="undefined"){
_169=[];
}else{
if(typeof _169!="object"){
_169=[_169];
}
}
for(var i in this._toolbarObjects){
var btn=this._toolbarObjects[i];
if(_169.contains(i)){
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
Array.prototype.contains=function(_170){
var _171=this;
for(var i=0;i<_171.length;i++){
if(_170==_171[i]){
return true;
}
}
return false;
};
}
if(!Array.prototype.indexOf){
Array.prototype.indexOf=function(_172){
var _173=this;
for(var i=0;i<_173.length;i++){
if(_172==_173[i]){
return i;
}
}
return null;
};
}
HTMLArea.prototype.updateToolbar=function(_174){
var doc=this._doc;
var text=(this._editMode=="textmode");
var _176=null;
if(!text){
_176=this.getAllAncestors();
if(this.config.statusBar&&!_174){
this._statusBarTree.innerHTML=HTMLArea._lc("Path")+": ";
for(var i=_176.length;--i>=0;){
var el=_176[i];
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
if(i!=0){
this._statusBarTree.appendChild(document.createTextNode(String.fromCharCode(187)));
}
}
}
}
for(var i in this._toolbarObjects){
var btn=this._toolbarObjects[i];
var cmd=i;
var _177=true;
if(typeof (btn.state)!="function"){
continue;
}
if(btn.context&&!text){
_177=false;
var _178=btn.context;
var _179=[];
if(/(.*)\[(.*?)\]/.test(_178)){
_178=RegExp.$1;
_179=RegExp.$2.split(",");
}
_178=_178.toLowerCase();
var _180=(_178=="*");
for(var k=0;k<_176.length;++k){
if(!_176[k]){
continue;
}
if(_180||(_176[k].tagName.toLowerCase()==_178)){
_177=true;
for(var ka=0;ka<_179.length;++ka){
if(!eval("ancestors[k]."+_179[ka])){
_177=false;
break;
}
}
if(_177){
break;
}
}
}
}
btn.state("enabled",(!text||btn.text)&&_177);
if(typeof cmd=="function"){
continue;
}
var _183=this.config.customSelects[cmd];
if((!text||btn.text)&&(typeof _183!="undefined")){
_183.refresh(this);
continue;
}
switch(cmd){
case "fontname":
case "fontsize":
if(!text){
try{
var _184=(""+doc.queryCommandValue(cmd)).toLowerCase();
if(!_184){
btn.element.selectedIndex=0;
break;
}
var _185=this.config[cmd];
var k=0;
for(var j in _185){
if((j.toLowerCase()==_184)||(_185[j].substr(0,_184.length).toLowerCase()==_184)){
btn.element.selectedIndex=k;
throw "ok";
}
++k;
}
btn.element.selectedIndex=0;
}
catch(e){
}
}
break;
case "formatblock":
var _186=[];
for(var i in this.config["formatblock"]){
if(typeof (this.config["formatblock"][i])=="string"){
_186[_186.length]=this.config["formatblock"][i];
}
}
var _187=this._getFirstAncestor(this._getSelection(),_186);
if(_187){
for(var x=0;x<_186.length;x++){
if(_186[x].toLowerCase()==_187.tagName.toLowerCase()){
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
with(btn.element.style){
backgroundColor=HTMLArea._makeColor(doc.queryCommandValue(HTMLArea.is_ie?"backcolor":"hilitecolor"));
if(/transparent/i.test(backgroundColor)){
backgroundColor=HTMLArea._makeColor(doc.queryCommandValue("backcolor"));
}
color=HTMLArea._makeColor(doc.queryCommandValue("forecolor"));
fontFamily=doc.queryCommandValue("fontname");
fontWeight=doc.queryCommandState("bold")?"bold":"normal";
fontStyle=doc.queryCommandState("italic")?"italic":"normal";
}
}
catch(e){
}
}
break;
case "htmlmode":
btn.state("active",text);
break;
case "lefttoright":
case "righttoleft":
var el=this.getParentElement();
while(el&&!HTMLArea.isBlockElement(el)){
el=el.parentNode;
}
if(el){
btn.state("active",(el.style.direction==((cmd=="righttoleft")?"rtl":"ltr")));
}
break;
default:
cmd=cmd.replace(/(un)?orderedlist/i,"insert$1orderedlist");
try{
btn.state("active",(!text&&doc.queryCommandState(cmd)));
}
catch(e){
}
}
}
if(this._customUndo&&!this._timerUndo){
this._undoTakeSnapshot();
var _188=this;
this._timerUndo=setTimeout(function(){
_188._timerUndo=null;
},this.config.undoTimeout);
}
if(0&&HTMLArea.is_gecko){
var s=this._getSelection();
if(s&&s.isCollapsed&&s.anchorNode&&s.anchorNode.parentNode.tagName.toLowerCase()!="body"&&s.anchorNode.nodeType==3&&s.anchorOffset==s.anchorNode.length&&!(s.anchorNode.parentNode.nextSibling&&s.anchorNode.parentNode.nextSibling.nodeType==3)&&!HTMLArea.isBlockElement(s.anchorNode.parentNode)){
try{
s.anchorNode.parentNode.parentNode.insertBefore(this._doc.createTextNode("\t"),s.anchorNode.parentNode.nextSibling);
}
catch(e){
}
}
}
for(var i in this.plugins){
var _190=this.plugins[i].instance;
if(_190&&typeof _190.onUpdateToolbar=="function"){
_190.onUpdateToolbar();
}
}
};
HTMLArea.prototype.insertNodeAtSelection=function(_191){
if(!HTMLArea.is_ie){
var sel=this._getSelection();
var _193=this._createRange(sel);
sel.removeAllRanges();
_193.deleteContents();
var node=_193.startContainer;
var pos=_193.startOffset;
switch(node.nodeType){
case 3:
if(_191.nodeType==3){
node.insertData(pos,_191.data);
_193=this._createRange();
_193.setEnd(node,pos+_191.length);
_193.setStart(node,pos+_191.length);
sel.addRange(_193);
}else{
node=node.splitText(pos);
var _195=_191;
if(_191.nodeType==11){
_195=_195.firstChild;
}
node.parentNode.insertBefore(_191,node);
this.selectNodeContents(_195);
this.updateToolbar();
}
break;
case 1:
var _195=_191;
if(_191.nodeType==11){
_195=_195.firstChild;
}
node.insertBefore(_191,node.childNodes[pos]);
this.selectNodeContents(_195);
this.updateToolbar();
break;
}
}else{
return null;
}
};
HTMLArea.prototype.getParentElement=function(sel){
if(typeof sel=="undefined"){
sel=this._getSelection();
}
var _196=this._createRange(sel);
if(HTMLArea.is_ie){
switch(sel.type){
case "Text":
var _197=_196.parentElement();
while(true){
var _198=_196.duplicate();
_198.moveToElementText(_197);
if(_198.inRange(_196)){
break;
}
if((_197.nodeType!=1)||(_197.tagName.toLowerCase()=="body")){
break;
}
_197=_197.parentElement;
}
return _197;
case "None":
return _196.parentElement();
case "Control":
return _196.item(0);
default:
return this._doc.body;
}
}else{
try{
var p=_196.commonAncestorContainer;
if(!_196.collapsed&&_196.startContainer==_196.endContainer&&_196.startOffset-_196.endOffset<=1&&_196.startContainer.hasChildNodes()){
p=_196.startContainer.childNodes[_196.startOffset];
}
while(p.nodeType==3){
p=p.parentNode;
}
return p;
}
catch(e){
return null;
}
}
};
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
HTMLArea.prototype._getFirstAncestor=function(sel,_199){
var prnt=this._activeElement(sel);
if(prnt==null){
try{
prnt=(HTMLArea.is_ie?this._createRange(sel).parentElement():this._createRange(sel).commonAncestorContainer);
}
catch(e){
return null;
}
}
if(typeof _199=="string"){
_199=[_199];
}
while(prnt){
if(prnt.nodeType==1){
if(_199==null){
return prnt;
}
if(_199.contains(prnt.tagName.toLowerCase())){
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
HTMLArea.prototype._activeElement=function(sel){
if(sel==null){
return null;
}
if(this._selectionEmpty(sel)){
return null;
}
if(HTMLArea.is_ie){
if(sel.type.toLowerCase()=="control"){
return sel.createRange().item(0);
}else{
var _201=sel.createRange();
var _202=this.getParentElement(sel);
if(_202.innerHTML==_201.htmlText){
return _202;
}
return null;
}
}else{
if(!sel.isCollapsed){
if(sel.anchorNode.childNodes.length>sel.anchorOffset&&sel.anchorNode.childNodes[sel.anchorOffset].nodeType==1){
return sel.anchorNode.childNodes[sel.anchorOffset];
}else{
if(sel.anchorNode.nodeType==1){
return sel.anchorNode;
}else{
return sel.anchorNode.parentNode;
}
}
}
return null;
}
};
HTMLArea.prototype._selectionEmpty=function(sel){
if(!sel){
return true;
}
if(HTMLArea.is_ie){
return this._createRange(sel).htmlText=="";
}else{
if(typeof sel.isCollapsed!="undefined"){
return sel.isCollapsed;
}
}
return true;
};
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
HTMLArea.prototype._formatBlock=function(_205){
var _206=this.getAllAncestors();
var _207=null;
var _208=null;
var _209=[];
if(_205.indexOf(".")>=0){
_208=_205.substr(0,_205.indexOf(".")).toLowerCase();
_209=_205.substr(_205.indexOf("."),_205.length-_205.indexOf(".")).replace(/\./g,"").replace(/^\s*/,"").replace(/\s*$/,"").split(" ");
}else{
_208=_205.toLowerCase();
}
var sel=this._getSelection();
var rng=this._createRange(sel);
var _207=null;
if(HTMLArea.is_gecko){
if(sel.isCollapsed){
_207=this._getAncestorBlock(sel);
if(_207==null){
_207=this._createImplicitBlock(sel,_208);
}
}else{
switch(_208){
case "h1":
case "h2":
case "h3":
case "h4":
case "h5":
case "h6":
case "h7":
_207=[];
var _210=["h1","h2","h3","h4","h5","h6","h7"];
for(var y=0;y<_210.length;y++){
var _212=this._doc.getElementsByTagName(search_tag[y]);
for(var x=0;x<_212.length;x++){
if(sel.containsNode(_212[x])){
_207[_207.length]=_212[x];
}
}
}
if(_207.length>0){
break;
}
case "div":
_207=this._doc.createElement(_208);
_207.appendChild(rng.extractContents());
rng.insertNode(_207);
break;
case "p":
case "center":
case "pre":
case "ins":
case "del":
case "blockquote":
case "address":
_207=[];
var _213=this._doc.getElementsByTagName(_208);
for(var x=0;x<_213.length;x++){
if(sel.containsNode(_213[x])){
_207[_207.length]=_213[x];
}
}
if(_207.length==0){
sel.collapseToStart();
return this._formatBlock(_205);
}
break;
}
}
}
};
HTMLArea.prototype.selectNodeContents=function(node,pos){
this.focusEditor();
this.forceRedraw();
var _214;
var _215=typeof pos=="undefined"?true:false;
if(HTMLArea.is_ie){
if(_215&&node.tagName&&node.tagName.toLowerCase().match(/table|img|input|select|textarea/)){
_214=this._doc.body.createControlRange();
_214.add(node);
}else{
_214=this._doc.body.createTextRange();
_214.moveToElementText(node);
}
_214.select();
}else{
var sel=this._getSelection();
_214=this._doc.createRange();
if(_215&&node.tagName&&node.tagName.toLowerCase().match(/table|img|input|textarea|select/)){
_214.selectNode(node);
}else{
_214.selectNodeContents(node);
}
sel.removeAllRanges();
sel.addRange(_214);
}
};
HTMLArea.prototype.insertHTML=function(html){
var sel=this._getSelection();
var _216=this._createRange(sel);
this.focusEditor();
if(HTMLArea.is_ie){
_216.pasteHTML(html);
}else{
var _217=this._doc.createDocumentFragment();
var div=this._doc.createElement("div");
div.innerHTML=html;
while(div.firstChild){
_217.appendChild(div.firstChild);
}
var node=this.insertNodeAtSelection(_217);
}
};
HTMLArea.prototype.surroundHTML=function(_218,_219){
var html=this.getSelectedHTML();
this.insertHTML(_218+html+_219);
};
HTMLArea.prototype.getSelectedHTML=function(){
var sel=this._getSelection();
var _220=this._createRange(sel);
var _221=null;
if(HTMLArea.is_ie){
_221=_220.htmlText;
}else{
_221=HTMLArea.getHTML(_220.cloneContents(),false,this);
}
return _221;
};
HTMLArea.prototype.hasSelectedText=function(){
return this.getSelectedHTML()!="";
};
HTMLArea.prototype._createLink=function(link){
var _222=this;
var _223=null;
if(typeof link=="undefined"){
link=this.getParentElement();
if(link){
while(link&&!/^a$/i.test(link.tagName)){
link=link.parentNode;
}
}
}
if(!link){
var sel=_222._getSelection();
var _224=_222._createRange(sel);
var _225=0;
if(HTMLArea.is_ie){
if(sel.type=="Control"){
_225=_224.length;
}else{
_225=_224.compareEndPoints("StartToEnd",_224);
}
}else{
_225=_224.compareBoundaryPoints(_224.START_TO_END,_224);
}
if(_225==0){
alert(HTMLArea._lc("You need to select some text before creating a link"));
return;
}
_223={f_href:"",f_title:"",f_target:"",f_usetarget:_222.config.makeLinkShowsTarget};
}else{
_223={f_href:HTMLArea.is_ie?_222.stripBaseURL(link.href):link.getAttribute("href"),f_title:link.title,f_target:link.target,f_usetarget:_222.config.makeLinkShowsTarget};
}
this._popupDialog(_222.config.URIs["link"],function(_226){
if(!_226){
return false;
}
var a=link;
if(!a){
try{
_222._doc.execCommand("createlink",false,_226.f_href);
a=_222.getParentElement();
var sel=_222._getSelection();
var _224=_222._createRange(sel);
if(!HTMLArea.is_ie){
a=_224.startContainer;
if(!/^a$/i.test(a.tagName)){
a=a.nextSibling;
if(a==null){
a=_224.startContainer.parentNode;
}
}
}
}
catch(e){
}
}else{
var href=_226.f_href.trim();
_222.selectNodeContents(a);
if(href==""){
_222._doc.execCommand("unlink",false,null);
_222.updateToolbar();
return false;
}else{
a.href=href;
}
}
if(!(a&&/^a$/i.test(a.tagName))){
return false;
}
a.target=_226.f_target.trim();
a.title=_226.f_title.trim();
_222.selectNodeContents(a);
_222.updateToolbar();
},_223);
};
HTMLArea.prototype._insertImage=function(_228){
var _229=this;
var _230=null;
if(typeof _228=="undefined"){
_228=this.getParentElement();
if(_228&&!/^img$/i.test(_228.tagName)){
_228=null;
}
}
if(_228){
_230={f_base:_229.config.baseHref,f_url:HTMLArea.is_ie?_229.stripBaseURL(_228.src):_228.getAttribute("src"),f_alt:_228.alt,f_border:_228.border,f_align:_228.align,f_vert:_228.vspace,f_horiz:_228.hspace};
}
this._popupDialog(_229.config.URIs["insert_image"],function(_231){
if(!_231){
return false;
}
var img=_228;
if(!img){
if(HTMLArea.is_ie){
var sel=_229._getSelection();
var _232=_229._createRange(sel);
_229._doc.execCommand("insertimage",false,_231.f_url);
img=_232.parentElement();
if(img.tagName.toLowerCase()!="img"){
img=img.previousSibling;
}
}else{
img=document.createElement("img");
img.src=_231.f_url;
_229.insertNodeAtSelection(img);
if(!img.tagName){
img=_232.startContainer.firstChild;
}
}
}else{
img.src=_231.f_url;
}
for(var _233 in _231){
var _234=_231[_233];
switch(_233){
case "f_alt":
img.alt=_234;
break;
case "f_border":
img.border=parseInt(_234||"0");
break;
case "f_align":
img.align=_234;
break;
case "f_vert":
img.vspace=parseInt(_234||"0");
break;
case "f_horiz":
img.hspace=parseInt(_234||"0");
break;
}
}
},_230);
};
HTMLArea.prototype._insertTable=function(){
var sel=this._getSelection();
var _235=this._createRange(sel);
var _236=this;
this._popupDialog(_236.config.URIs["insert_table"],function(_237){
if(!_237){
return false;
}
var doc=_236._doc;
var _238=doc.createElement("table");
for(var _239 in _237){
var _240=_237[_239];
if(!_240){
continue;
}
switch(_239){
case "f_width":
_238.style.width=_240+_237["f_unit"];
break;
case "f_align":
_238.align=_240;
break;
case "f_border":
_238.border=parseInt(_240);
break;
case "f_spacing":
_238.cellSpacing=parseInt(_240);
break;
case "f_padding":
_238.cellPadding=parseInt(_240);
break;
}
}
var _241=0;
if(_237.f_fixed){
_241=Math.floor(100/parseInt(_237.f_cols));
}
var _242=doc.createElement("tbody");
_238.appendChild(_242);
for(var i=0;i<_237["f_rows"];++i){
var tr=doc.createElement("tr");
_242.appendChild(tr);
for(var j=0;j<_237["f_cols"];++j){
var td=doc.createElement("td");
if(_241){
td.style.width=_241+"%";
}
tr.appendChild(td);
td.appendChild(doc.createTextNode("\xa0"));
}
}
if(HTMLArea.is_ie){
_235.pasteHTML(_238.outerHTML);
}else{
_236.insertNodeAtSelection(_238);
}
return true;
},null);
};
HTMLArea.prototype._comboSelected=function(el,txt){
this.focusEditor();
var _245=el.options[el.selectedIndex].value;
switch(txt){
case "fontname":
case "fontsize":
this.execCommand(txt,false,_245);
break;
case "formatblock":
if(!HTMLArea.is_gecko||_245!=="blockquote"){
_245="<"+_245+">";
}
this.execCommand(txt,false,_245);
break;
default:
var _246=this.config.customSelects[txt];
if(typeof _246!="undefined"){
_246.action(this);
}else{
alert("FIXME: combo box "+txt+" not implemented");
}
}
};
HTMLArea.prototype.execCommand=function(_247,UI,_249){
var _250=this;
this.focusEditor();
_247=_247.toLowerCase();
if(HTMLArea.is_gecko){
try{
this._doc.execCommand("useCSS",false,true);
}
catch(e){
}
}
switch(_247){
case "htmlmode":
this.setMode();
break;
case "hilitecolor":
(HTMLArea.is_ie)&&(_247="backcolor");
if(HTMLArea.is_gecko){
try{
_250._doc.execCommand("useCSS",false,false);
}
catch(e){
}
}
case "forecolor":
this._popupDialog(_250.config.URIs["select_color"],function(_251){
if(_251){
_250._doc.execCommand(_247,false,"#"+_251);
}
},HTMLArea._colorToRgb(this._doc.queryCommandValue(_247)));
break;
case "createlink":
this._createLink();
break;
case "undo":
case "redo":
if(this._customUndo){
this[_247]();
}else{
this._doc.execCommand(_247,UI,_249);
}
break;
case "inserttable":
this._insertTable();
break;
case "insertimage":
this._insertImage();
break;
case "about":
this._popupDialog(_250.config.URIs["about"],null,this);
break;
case "showhelp":
this._popupDialog(_250.config.URIs["help"],null,this);
break;
case "killword":
this._wordClean();
break;
case "cut":
case "copy":
case "paste":
doPastePopup=false;
try{
this._doc.execCommand(_247,UI,_249);
}
catch(e){
if(HTMLArea.is_gecko){
doPastePopup=true;
}
}
if(this.config.killWordOnPaste||doPastePopup){
if(typeof WordPaste=="undefined"){
HTMLArea.loadPlugin("WordPaste",function(){
_250.generate();
});
_250.registerPlugin("WordPaste");
}
if(typeof WordPaste=="function"){
_250.plugins["WordPaste"].instance._buttonPress(doPastePopup);
}
}
break;
case "lefttoright":
case "righttoleft":
var dir=(_247=="righttoleft")?"rtl":"ltr";
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
this._doc.execCommand(_247,UI,_249);
}
catch(e){
if(this.config.debug){
alert(e+"\n\nby execCommand("+_247+");");
}
}
}
this.updateToolbar();
return false;
};
HTMLArea.prototype._editorEvent=function(ev){
var _252=this;
var _253=(HTMLArea.is_ie&&ev.type=="keydown")||(!HTMLArea.is_ie&&ev.type=="keypress");
if(typeof _252._textArea["on"+ev.type]=="function"){
_252._textArea["on"+ev.type]();
}
if(HTMLArea.is_gecko&&_253&&ev.ctrlKey&&this._unLink&&this._unlinkOnUndo){
if(String.fromCharCode(ev.charCode).toLowerCase()=="z"){
HTMLArea._stopEvent(ev);
this._unLink();
_252.updateToolbar();
return;
}
}
if(_253){
for(var i in _252.plugins){
var _254=_252.plugins[i].instance;
if(_254&&typeof _254.onKeyPress=="function"){
if(_254.onKeyPress(ev)){
return false;
}
}
}
}
if(_253&&ev.ctrlKey&&!ev.altKey){
var sel=null;
var _255=null;
var key=String.fromCharCode(HTMLArea.is_ie?ev.keyCode:ev.charCode).toLowerCase();
var cmd=null;
var _257=null;
switch(key){
case "a":
if(!HTMLArea.is_ie){
sel=this._getSelection();
sel.removeAllRanges();
_255=this._createRange();
_255.selectNodeContents(this._doc.body);
sel.addRange(_255);
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
if(HTMLArea.is_ie||_252.config.htmlareaPaste){
cmd="paste";
}
break;
case "n":
cmd="formatblock";
_257=HTMLArea.is_ie?"<p>":"p";
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
_257="h"+key;
if(HTMLArea.is_ie){
_257="<"+_257+">";
}
break;
}
if(cmd){
this.execCommand(cmd,false,_257);
HTMLArea._stopEvent(ev);
}
}else{
if(_253){
if(HTMLArea.is_gecko){
var s=_252._getSelection();
var _258=function(_259,tag){
var _260=_259.nextSibling;
if(typeof tag=="string"){
tag=_252._doc.createElement(tag);
}
var a=_259.parentNode.insertBefore(tag,_260);
HTMLArea.removeFromParent(_259);
a.appendChild(_259);
_260.data=" "+_260.data;
if(HTMLArea.is_ie){
var r=_252._createRange(s);
s.moveToElementText(_260);
s.move("character",1);
}else{
s.collapse(_260,1);
}
HTMLArea._stopEvent(ev);
_252._unLink=function(){
var t=a.firstChild;
a.removeChild(t);
a.parentNode.insertBefore(t,a);
HTMLArea.removeFromParent(a);
_252._unLink=null;
_252._unlinkOnUndo=false;
};
_252._unlinkOnUndo=true;
return a;
};
switch(ev.which){
case 32:
if(s&&s.isCollapsed&&s.anchorNode.nodeType==3&&s.anchorNode.data.length>3&&s.anchorNode.data.indexOf(".")>=0){
var _263=s.anchorNode.data.substring(0,s.anchorOffset).search(/\S{4,}$/);
if(_263==-1){
break;
}
if(this._getFirstAncestor(s,"a")){
break;
}
var _264=s.anchorNode.data.substring(0,s.anchorOffset).replace(/^.*?(\S*)$/,"$1");
var m=_264.match(HTMLArea.RE_email);
if(m){
var _266=s.anchorNode;
var _267=_266.splitText(s.anchorOffset);
var _268=_266.splitText(_263);
_258(_268,"a").href="mailto:"+m[0];
break;
}
var m=_264.match(HTMLArea.RE_url);
if(m){
var _266=s.anchorNode;
var _267=_266.splitText(s.anchorOffset);
var _268=_266.splitText(_263);
_258(_268,"a").href=(m[1]?m[1]:"http://")+m[2];
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
if(s.anchorNode.data.match(HTMLArea.RE_email)&&(a.href.match("mailto:"+s.anchorNode.data.trim()))){
var _269=s.anchorNode;
var fn=function(){
a.href="mailto:"+_269.data.trim();
a._updateAnchTimeout=setTimeout(fn,250);
};
a._updateAnchTimeout=setTimeout(fn,250);
break;
}
var m=s.anchorNode.data.match(HTMLArea.RE_url);
if(m&&a.href.match(s.anchorNode.data.trim())){
var _269=s.anchorNode;
var fn=function(){
var m=_269.data.match(HTMLArea.RE_url);
a.href=(m[1]?m[1]:"http://")+m[2];
a._updateAnchTimeout=setTimeout(fn,250);
};
a._updateAnchTimeout=setTimeout(fn,250);
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
if(HTMLArea.is_gecko&&!ev.shiftKey){
if(this.dom_checkBackspace()){
HTMLArea._stopEvent(ev);
}
}else{
if(HTMLArea.is_ie){
if(this.ie_checkBackspace()){
HTMLArea._stopEvent(ev);
}
}
}
break;
}
}
}
if(_252._timerToolbar){
clearTimeout(_252._timerToolbar);
}
_252._timerToolbar=setTimeout(function(){
_252.updateToolbar();
_252._timerToolbar=null;
},250);
};
HTMLArea.prototype.convertNode=function(el,_271){
var _272=this._doc.createElement(_271);
while(el.firstChild){
_272.appendChild(el.firstChild);
}
return _272;
};
HTMLArea.prototype.ie_checkBackspace=function(){
var sel=this._getSelection();
if(HTMLArea.is_ie&&sel.type=="Control"){
var elm=this._activeElement(sel);
HTMLArea.removeFromParent(elm);
return true;
}
var _274=this._createRange(sel);
var r2=_274.duplicate();
r2.moveStart("character",-1);
var a=r2.parentElement();
if(a!=_274.parentElement()&&/^a$/i.test(a.tagName)){
r2.collapse(true);
r2.moveEnd("character",1);
r2.pasteHTML("");
r2.select();
return true;
}
};
HTMLArea.prototype.dom_checkBackspace=function(){
var self=this;
setTimeout(function(){
var sel=self._getSelection();
var _277=self._createRange(sel);
var SC=_277.startContainer;
var SO=_277.startOffset;
var EC=_277.endContainer;
var EO=_277.endOffset;
var newr=SC.nextSibling;
if(SC.nodeType==3){
SC=SC.parentNode;
}
if(!/\S/.test(SC.tagName)){
var p=document.createElement("p");
while(SC.firstChild){
p.appendChild(SC.firstChild);
}
SC.parentNode.insertBefore(p,SC);
HTMLArea.removeFromParent(SC);
var r=_277.cloneRange();
r.setStartBefore(newr);
r.setEndAfter(newr);
r.extractContents();
sel.removeAllRanges();
sel.addRange(r);
}
},10);
};
HTMLArea.prototype.dom_checkInsertP=function(){
var sel=this._getSelection();
var _283=this._createRange(sel);
if(!_283.collapsed){
_283.deleteContents();
}
this.deactivateEditor();
var SC=_283.startContainer;
var SO=_283.startOffset;
var EC=_283.endContainer;
var EO=_283.endOffset;
if(SC==EC&&SC==body&&!SO&&!EO){
p=this._doc.createTextNode(" ");
body.insertBefore(p,body.firstChild);
_283.selectNodeContents(p);
SC=_283.startContainer;
SO=_283.startOffset;
EC=_283.endContainer;
EO=_283.endOffset;
}
var p=this.getAllAncestors();
var _284=null;
var body=this._doc.body;
for(var i=0;i<p.length;++i){
if(HTMLArea.isParaContainer(p[i])){
break;
}else{
if(HTMLArea.isBlockElement(p[i])&&!/body|html/i.test(p[i].tagName)){
_284=p[i];
break;
}
}
}
if(!_284){
var wrap=_283.startContainer;
while(wrap.parentNode&&!HTMLArea.isParaContainer(wrap.parentNode)){
wrap=wrap.parentNode;
}
var _287=wrap;
var end=wrap;
while(_287.previousSibling){
if(_287.previousSibling.tagName){
if(!HTMLArea.isBlockElement(_287.previousSibling)){
_287=_287.previousSibling;
}else{
break;
}
}else{
_287=_287.previousSibling;
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
_283.setStartBefore(_287);
_283.setEndAfter(end);
_283.surroundContents(this._doc.createElement("p"));
_284=_283.startContainer.firstChild;
_283.setStart(SC,SO);
}
_283.setEndAfter(_284);
var r2=_283.cloneRange();
sel.removeRange(_283);
var df=r2.extractContents();
if(df.childNodes.length==0){
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
if(!/\S/.test(_284.innerHTML)){
_284.innerHTML="&nbsp;";
}
p=df.firstChild;
if(!/\S/.test(p.innerHTML)){
p.innerHTML="<br />";
}
if(/^\s*<br\s*\/?>\s*$/.test(p.innerHTML)&&/^h[1-6]$/i.test(p.tagName)){
df.appendChild(this.convertNode(p,"p"));
df.removeChild(p);
}
var _291=_284.parentNode.insertBefore(df.firstChild,_284.nextSibling);
this.activateEditor();
var sel=this._getSelection();
sel.removeAllRanges();
sel.collapse(_291,0);
this.scrollToElement(_291);
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
alert("Mode <"+mode+"> not defined!");
return false;
}
return html;
};
HTMLArea.prototype.outwardHtml=function(html){
html=html.replace(/<(\/?)b(\s|>|\/)/ig,"<$1strong$2");
html=html.replace(/<(\/?)i(\s|>|\/)/ig,"<$1em$2");
html=html.replace(/<(\/?)strike(\s|>|\/)/ig,"<$1del$2");
html=html.replace("onclick=\"try{if(document.designMode &amp;&amp; document.designMode == 'on') return false;}catch(e){} window.open(","onclick=\"window.open(");
var _294=location.href.replace(/(https?:\/\/[^\/]*)\/.*/,"$1")+"/";
html=html.replace(/https?:\/\/null\//g,_294);
html=html.replace(/((href|src|background)=[\'\"])\/+/ig,"$1"+_294);
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
var _296=new RegExp("((href|src|background)=['\"])/+","gi");
html=html.replace(_296,"$1"+location.href.replace(/(https?:\/\/[^\/]*)\/.*/,"$1")+"/");
html=this.fixRelativeLinks(html);
return html;
};
HTMLArea.prototype.outwardSpecialReplacements=function(html){
for(var i in this.config.specialReplacements){
var from=this.config.specialReplacements[i];
var to=i;
if(typeof (from.replace)!="function"||typeof (to.replace)!="function"){
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
if(typeof (from.replace)!="function"||typeof (to.replace)!="function"){
continue;
}
var reg=new RegExp(from.replace(HTMLArea.RE_Specials,"\\$1"),"g");
html=html.replace(reg,to.replace(/\$/g,"$$$$"));
}
return html;
};
HTMLArea.prototype.fixRelativeLinks=function(html){
if(typeof this.config.stripSelfNamedAnchors!="undefined"&&this.config.stripSelfNamedAnchors){
var _300=new RegExp(document.location.href.replace(HTMLArea.RE_Specials,"\\$1")+"(#[^'\" ]*)","g");
html=html.replace(_300,"$1");
}
if(typeof this.config.stripBaseHref!="undefined"&&this.config.stripBaseHref){
var _301=null;
if(typeof this.config.baseHref!="undefined"&&this.config.baseHref!=null){
_301=new RegExp(this.config.baseHref.replace(HTMLArea.RE_Specials,"\\$1"),"g");
}else{
_301=new RegExp(document.location.href.replace(/([^\/]*\/?)$/,"").replace(HTMLArea.RE_Specials,"\\$1"),"g");
}
html=html.replace(_301,"");
}
if(HTMLArea.is_ie){
}
return html;
};
HTMLArea.prototype.getInnerHTML=function(){
if(!this._doc.body){
return "";
}
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
alert("Mode <"+mode+"> not defined!");
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
HTMLArea.prototype.setDoctype=function(_302){
this.doctype=_302;
};
HTMLArea._object=null;
HTMLArea.cloneObject=function(obj){
if(!obj){
return null;
}
var _303=new Object;
if(obj.constructor.toString().match(/\s*function Array\(/)){
_303=obj.constructor();
}
if(obj.constructor.toString().match(/\s*function Function\(/)){
_303=obj;
}else{
for(var n in obj){
var node=obj[n];
if(typeof node=="object"){
_303[n]=HTMLArea.cloneObject(node);
}else{
_303[n]=node;
}
}
}
return _303;
};
HTMLArea.checkSupportedBrowser=function(){
if(HTMLArea.is_gecko){
if(navigator.productSub<20021201){
alert("You need at least Mozilla-1.3 Alpha.\n"+"Sorry, your Gecko is not supported.");
return false;
}
if(navigator.productSub<20030210){
alert("Mozilla < 1.3 Beta is not supported!\n"+"I'll try, though, but it might not work.");
}
}
return HTMLArea.is_gecko||HTMLArea.is_ie;
};
HTMLArea.prototype._getSelection=function(){
if(HTMLArea.is_ie){
return this._doc.selection;
}else{
return this._iframe.contentWindow.getSelection();
}
};
HTMLArea.prototype._createRange=function(sel){
if(HTMLArea.is_ie){
return sel.createRange();
}else{
this.activateEditor();
if(typeof sel!="undefined"){
try{
return sel.getRangeAt(0);
}
catch(e){
return this._doc.createRange();
}
}else{
return this._doc.createRange();
}
}
};
HTMLArea._eventFlushers=[];
HTMLArea.flushEvents=function(){
var x=0;
var e=null;
while(e=HTMLArea._eventFlushers.pop()){
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
catch(e){
}
}
};
HTMLArea._addEvent=function(el,_305,func){
if(HTMLArea.is_ie){
el.attachEvent("on"+_305,func);
}else{
el.addEventListener(_305,func,true);
}
HTMLArea._eventFlushers.push([el,_305,func]);
};
HTMLArea._addEvents=function(el,evs,func){
for(var i=evs.length;--i>=0;){
HTMLArea._addEvent(el,evs[i],func);
}
};
HTMLArea._removeEvent=function(el,_308,func){
if(HTMLArea.is_ie){
el.detachEvent("on"+_308,func);
}else{
el.removeEventListener(_308,func,true);
}
};
HTMLArea._removeEvents=function(el,evs,func){
for(var i=evs.length;--i>=0;){
HTMLArea._removeEvent(el,evs[i],func);
}
};
HTMLArea._stopEvent=function(ev){
if(HTMLArea.is_ie){
try{
ev.cancelBubble=true;
ev.returnValue=false;
}
catch(e){
}
}else{
ev.preventDefault();
ev.stopPropagation();
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
el["on"+ev]=function(_309){
var a=el._xinha_dom0Events[ev];
var _310=true;
for(var i=a.length;--i>=0;){
el._xinha_tempEventHandler=a[i];
if(el._xinha_tempEventHandler(_309)==false){
el._xinha_tempEventHandler=null;
_310=false;
break;
}
el._xinha_tempEventHandler=null;
}
return _310;
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
HTMLArea._removeClass=function(el,_311){
if(!(el&&el.className)){
return;
}
var cls=el.className.split(" ");
var ar=new Array();
for(var i=cls.length;i>0;){
if(cls[--i]!=_311){
ar[ar.length]=cls[i];
}
}
el.className=ar.join(" ");
};
HTMLArea._addClass=function(el,_314){
HTMLArea._removeClass(el,_314);
el.className+=" "+_314;
};
HTMLArea._hasClass=function(el,_315){
if(!(el&&el.className)){
return false;
}
var cls=el.className.split(" ");
for(var i=cls.length;i>0;){
if(cls[--i]==_315){
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
HTMLArea.getHTML=function(root,_316,_317){
try{
return HTMLArea.getHTMLWrapper(root,_316,_317);
}
catch(e){
alert(HTMLArea._lc("Your Document is not well formed. Check JavaScript console for details."));
return _317._iframe.contentWindow.document.body.innerHTML;
}
};
HTMLArea.getHTMLWrapper=function(root,_318,_319,_320){
var html="";
if(!_320){
_320="";
}
switch(root.nodeType){
case 10:
case 6:
case 12:
break;
case 2:
break;
case 4:
html+=(HTMLArea.is_ie?("\n"+_320):"")+"<![CDATA["+root.data+"]]>";
break;
case 5:
html+="&"+root.nodeValue+";";
break;
case 7:
html+=(HTMLArea.is_ie?("\n"+_320):"")+"<?"+root.target+" "+root.data+" ?>";
break;
case 1:
case 11:
case 9:
var _321;
var i;
var _322=(root.nodeType==1)?root.tagName.toLowerCase():"";
if(_318){
_318=!(_319.config.htmlRemoveTags&&_319.config.htmlRemoveTags.test(_322));
}
if(HTMLArea.is_ie&&_322=="head"){
if(_318){
html+=(HTMLArea.is_ie?("\n"+_320):"")+"<head>";
}
var _323=RegExp.multiline;
RegExp.multiline=true;
var txt=root.innerHTML.replace(HTMLArea.RE_tagName,function(str,p1,p2){
return p1+p2.toLowerCase();
});
RegExp.multiline=_323;
html+=txt+"\n";
if(_318){
html+=(HTMLArea.is_ie?("\n"+_320):"")+"</head>";
}
break;
}else{
if(_318){
_321=(!(root.hasChildNodes()||HTMLArea.needsClosingTag(root)));
html+=(HTMLArea.is_ie&&HTMLArea.isBlockElement(root)?("\n"+_320):"")+"<"+root.tagName.toLowerCase();
var _326=root.attributes;
for(i=0;i<_326.length;++i){
var a=_326.item(i);
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
var _328;
if(name!="style"){
if(typeof root[a.nodeName]!="undefined"&&name!="href"&&name!="src"&&!/^on/.test(name)){
_328=root[a.nodeName];
}else{
_328=a.nodeValue;
if(HTMLArea.is_ie&&(name=="href"||name=="src")){
_328=_319.stripBaseURL(_328);
}
if(_319.config.only7BitPrintablesInURLs&&(name=="href"||name=="src")){
_328=_328.replace(/([^!-~]+)/g,function(_329){
return escape(_329);
});
}
}
}else{
_328=root.style.cssText;
}
if(/^(_moz)?$/.test(_328)){
continue;
}
html+=" "+name+"=\""+HTMLArea.htmlEncode(_328)+"\"";
}
if(html!=""){
if(_321&&_322=="p"){
html+=">&nbsp;</p>";
}else{
if(_321){
html+=" />";
}else{
html+=">";
}
}
}
}
}
var _330=false;
for(i=root.firstChild;i;i=i.nextSibling){
if(!_330&&i.nodeType==1&&HTMLArea.isBlockElement(i)){
_330=true;
}
html+=HTMLArea.getHTMLWrapper(i,true,_319,_320+"  ");
}
if(_318&&!_321){
html+=(HTMLArea.is_ie&&HTMLArea.isBlockElement(root)&&_330?("\n"+_320):"")+"</"+root.tagName.toLowerCase()+">";
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
HTMLArea.prototype.stripBaseURL=function(_331){
if(this.config.baseHref==null||!this.config.stripBaseHref){
return (_331);
}
var _332=this.config.baseHref;
_332=_332.replace(/^(https?:\/\/[^\/]+)(.*)$/,"$1");
basere=new RegExp(_332);
return _331.replace(basere,"");
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
function hex(d){
return (d<16)?("0"+d.toString(16)):d.toString(16);
}
if(typeof v=="number"){
var r=v&255;
var g=(v>>8)&255;
var b=(v>>16)&255;
return "#"+hex(r)+hex(g)+hex(b);
}
if(v.substr(0,3)=="rgb"){
var re=/rgb\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)/;
if(v.match(re)){
var r=parseInt(RegExp.$1);
var g=parseInt(RegExp.$2);
var b=parseInt(RegExp.$3);
return "#"+hex(r)+hex(g)+hex(b);
}
return null;
}
if(v.substr(0,1)=="#"){
return v;
}
return null;
};
HTMLArea.prototype._popupDialog=function(url,_338,init){
Dialog(this.popupURL(url),_338,init);
};
HTMLArea.prototype.imgURL=function(file,_341){
if(typeof _341=="undefined"){
return _editor_url+file;
}else{
return _editor_url+"plugins/"+_341+"/img/"+file;
}
};
HTMLArea.prototype.popupURL=function(file){
var url="";
if(file.match(/^plugin:\/\/(.*?)\/(.*)/)){
var _342=RegExp.$1;
var _343=RegExp.$2;
if(!/\.html$/.test(_343)){
_343+=".html";
}
url=_editor_url+"plugins/"+_342+"/popups/"+_343;
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
tables=this._doc.getElementsByTagName("TABLE");
if(tables.length!=0){
if(!this.borders){
name="bordered";
this.borders=true;
}else{
name="";
this.borders=false;
}
for(var ix=0;ix<tables.length;ix++){
if(this.borders){
if(HTMLArea.is_gecko){
tables[ix].style.display="none";
tables[ix].style.display="table";
}
HTMLArea._addClass(tables[ix],"htmtableborders");
}else{
HTMLArea._removeClass(tables[ix],"htmtableborders");
}
}
}
return true;
};
HTMLArea.addClasses=function(el,_345){
if(el!=null){
var _346=el.className.trim().split(" ");
var ours=_345.split(" ");
for(var x=0;x<ours.length;x++){
var _348=false;
for(var i=0;_348==false&&i<_346.length;i++){
if(_346[i]==ours[x]){
_348=true;
}
}
if(_348==false){
_346[_346.length]=ours[x];
}
}
el.className=_346.join(" ").trim();
}
};
HTMLArea.removeClasses=function(el,_349){
var _350=el.className.trim().split();
var _351=[];
var _352=_349.trim().split();
for(var i=0;i<_350.length;i++){
var _353=false;
for(var x=0;x<_352.length&&!_353;x++){
if(_350[i]==_352[x]){
_353=true;
}
}
if(!_353){
_351[_351.length]=_350[i];
}
}
return _351.join(" ");
};
HTMLArea.addClass=HTMLArea._addClass;
HTMLArea.removeClass=HTMLArea._removeClass;
HTMLArea._addClasses=HTMLArea.addClasses;
HTMLArea._removeClasses=HTMLArea.removeClasses;
HTMLArea._postback=function(url,data,_355){
var req=null;
if(HTMLArea.is_ie){
req=new ActiveXObject("Microsoft.XMLHTTP");
}else{
req=new XMLHttpRequest();
}
var _357="";
for(var i in data){
_357+=(_357.length?"&":"")+i+"="+encodeURIComponent(data[i]);
}
function callBack(){
if(req.readyState==4){
if(req.status==200){
if(typeof _355=="function"){
_355(req.responseText,req);
}
}else{
alert("An error has occurred: "+req.statusText);
}
}
}
req.onreadystatechange=callBack;
req.open("POST",url,true);
req.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
req.send(_357);
};
HTMLArea._getback=function(url,_358){
var req=null;
if(HTMLArea.is_ie){
req=new ActiveXObject("Microsoft.XMLHTTP");
}else{
req=new XMLHttpRequest();
}
function callBack(){
if(req.readyState==4){
if(req.status==200){
_358(req.responseText,req);
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
x=window.open("","debugger");
x.document.write("<pre>"+s+"</pre>");
}
}
HTMLArea.arrayContainsArray=function(a1,a2){
var _363=true;
for(var x=0;x<a2.length;x++){
var _364=false;
for(var i=0;i<a1.length;i++){
if(a1[i]==a2[x]){
_364=true;
break;
}
}
if(!_364){
_363=false;
break;
}
}
return _363;
};
HTMLArea.arrayFilter=function(a1,_365){
var _366=[];
for(var x=0;x<a1.length;x++){
if(_365(a1[x])){
_366[_366.length]=a1[x];
}
}
return _366;
};
HTMLArea.uniq_count=0;
HTMLArea.uniq=function(_367){
return _367+HTMLArea.uniq_count++;
};
HTMLArea._loadlang=function(_368){
if(typeof _editor_lcbackend=="string"){
var url=_editor_lcbackend;
url=url.replace(/%lang%/,_editor_lang);
url=url.replace(/%context%/,_368);
}else{
if(_368!="HTMLArea"){
var url=_editor_url+"plugins/"+_368+"/lang/"+_editor_lang+".js";
}else{
var url=_editor_url+"lang/"+_editor_lang+".js";
}
}
var lang;
var _370=HTMLArea._geturlcontent(url);
if(_370!=""){
try{
eval("lang = "+_370);
}
catch(Error){
alert("Error reading Language-File ("+url+"):\n"+Error.toString());
lang={};
}
}else{
lang={};
}
return lang;
};
HTMLArea._lc=function(_371,_372,_373){
var ret;
if(_editor_lang=="en"){
if(typeof _371=="object"&&_371.string){
ret=_371.string;
}else{
ret=_371;
}
}else{
if(typeof HTMLArea._lc_catalog=="undefined"){
HTMLArea._lc_catalog=[];
}
if(typeof _372=="undefined"){
_372="HTMLArea";
}
if(typeof HTMLArea._lc_catalog[_372]=="undefined"){
HTMLArea._lc_catalog[_372]=HTMLArea._loadlang(_372);
}
var key;
if(typeof _371=="object"&&_371.key){
key=_371.key;
}else{
if(typeof _371=="object"&&_371.string){
key=_371.string;
}else{
key=_371;
}
}
if(typeof HTMLArea._lc_catalog[_372][key]=="undefined"){
if(_372=="HTMLArea"){
if(typeof _371=="object"&&_371.string){
ret=_371.string;
}else{
ret=_371;
}
}else{
return HTMLArea._lc(_371,"HTMLArea",_373);
}
}else{
ret=HTMLArea._lc_catalog[_372][key];
}
}
if(typeof _371=="object"&&_371.replace){
_373=_371.replace;
}
if(typeof _373!="undefined"){
for(var i in _373){
ret=ret.replace("$"+i,_373[i]);
}
}
return ret;
};
HTMLArea.hasDisplayedChildren=function(el){
var _375=el.childNodes;
for(var i=0;i<_375.length;i++){
if(_375[i].tagName){
if(_375[i].style.display!="none"){
return true;
}
}
}
return false;
};
HTMLArea._loadback=function(src,_377){
var head=document.getElementsByTagName("head")[0];
var evt=HTMLArea.is_ie?"onreadystatechange":"onload";
var _379=document.createElement("script");
_379.type="text/javascript";
_379.src=src;
_379[evt]=function(){
if(HTMLArea.is_ie&&!/loaded|complete/.test(window.event.srcElement.readyState)){
return;
}
_377();
};
head.appendChild(_379);
};
HTMLArea.collectionToArray=function(_380){
var _381=[];
for(var i=0;i<_380.length;i++){
_381.push(_380.item(i));
}
return _381;
};
if(!Array.prototype.append){
Array.prototype.append=function(a){
for(var i=0;i<a.length;i++){
this.push(a[i]);
}
return this;
};
}
HTMLArea.makeEditors=function(_382,_383,_384){
if(typeof _383=="function"){
_383=_383();
}
var _385={};
for(var x=0;x<_382.length;x++){
var _386=new HTMLArea(_382[x],HTMLArea.cloneObject(_383));
_386.registerPlugins(_384);
_385[_382[x]]=_386;
}
return _385;
};
HTMLArea.startEditors=function(_387){
for(var i in _387){
if(_387[i].generate){
_387[i].generate();
}
}
};
HTMLArea.prototype.registerPlugins=function(_388){
if(_388){
for(var i=0;i<_388.length;i++){
this.registerPlugin(eval(_388[i]));
}
}
};
HTMLArea.base64_encode=function(_389){
var _390="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
var _391="";
var chr1,chr2,chr3;
var enc1,enc2,enc3,enc4;
var i=0;
do{
chr1=_389.charCodeAt(i++);
chr2=_389.charCodeAt(i++);
chr3=_389.charCodeAt(i++);
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
_391=_391+_390.charAt(enc1)+_390.charAt(enc2)+_390.charAt(enc3)+_390.charAt(enc4);
}while(i<_389.length);
return _391;
};
HTMLArea.base64_decode=function(_394){
var _395="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
var _396="";
var chr1,chr2,chr3;
var enc1,enc2,enc3,enc4;
var i=0;
_394=_394.replace(/[^A-Za-z0-9\+\/\=]/g,"");
do{
enc1=_395.indexOf(_394.charAt(i++));
enc2=_395.indexOf(_394.charAt(i++));
enc3=_395.indexOf(_394.charAt(i++));
enc4=_395.indexOf(_394.charAt(i++));
chr1=(enc1<<2)|(enc2>>4);
chr2=((enc2&15)<<4)|(enc3>>2);
chr3=((enc3&3)<<6)|enc4;
_396=_396+String.fromCharCode(chr1);
if(enc3!=64){
_396=_396+String.fromCharCode(chr2);
}
if(enc4!=64){
_396=_396+String.fromCharCode(chr3);
}
}while(i<_394.length);
return _396;
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
HTMLArea.getOuterHTML=function(_398){
if(HTMLArea.is_ie){
return _398.outerHTML;
}else{
return (new XMLSerializer()).serializeToString(_398);
}
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
catch(e){
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

