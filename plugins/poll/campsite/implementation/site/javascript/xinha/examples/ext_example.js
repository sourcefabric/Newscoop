
  /*--------------------------------------:noTabs=true:tabSize=2:indentSize=2:--
    --  Xinha example logic.  This javascript is used to auto-generate examples
    --  as controlled by the options set in full_example-menu.html.  it's called
    --  from full_example-body.html.
    --
    --  $HeadURL$
    --  $LastChangedDate$
    --  $LastChangedRevision$
    --  $LastChangedBy$
    --------------------------------------------------------------------------*/

  var num     = 1;
  if(window.parent && window.parent != window)
  {
    var f = window.parent.menu.document.forms[0];
    _editor_lang = f.lang.value;
    _editor_skin = f.skin.value;
    num = parseInt(f.num.value);
    if(isNaN(num))
    {
      num = 1;
      f.num.value = 1;
    }
    xinha_plugins = [ ];
    for(var x = 0; x < f.plugins.length; x++)
    {
      if(f.plugins[x].checked) xinha_plugins.push(f.plugins[x].value);
    }
  }

  xinha_editors = [ ]
  for(var x = 0; x < num; x++)
  {
    var ta = 'myTextarea' + x;
    xinha_editors.push(ta);
  }

  xinha_config = function()
  {
    var    config = new HTMLArea.Config();

    if(window.parent && window.parent != window) {
      var f = window.parent.menu.document.forms[0];
      if (f.width) config.width = f.width.value;
      if (f.height) config.height = f.height.value;
      if (f.sizeIncludesBars) config.sizeIncludesBars = f.sizeIncludesBars.value;
      if (f.statusBar) config.statusBar = f.statusBar.value;
      if (f.mozParaHandler) config.mozParaHandler = f.mozParaHandler.value;
      if (f.undoSteps) config.undoSteps = f.undoSteps.value;
      if (f.baseHref) config.baseHref = f.baseHref.value;
      if (f.stripBaseHref) config.stripBaseHref = f.stripBaseHref.value;
      if (f.stripSelfNamedAnchors) config.stripSelfNamedAnchors = f.stripSelfNamedAnchors.value;
      if (f.only7BitPrintablesInURLs) config.only7BitPrintablesInURLs = f.only7BitPrintablesInURLs.value;
      if (f.sevenBitClean) config.sevenBitClean = f.sevenBitClean.value;
      if (f.killWordOnPaste) config.killWordOnPaste = f.killWordOnPaste.value;
      if (f.flowToolbars) config.flowToolbars = f.flowToolbars.value;
      if ((typeof CharacterMap != 'undefined') && (f.CharacterMapMode)) config.CharacterMap.mode = f.CharacterMapMode.value;
      if ((typeof ListType != 'undefined') && (f.ListTypeMode)) config.ListType.mode = f.ListTypeMode.value; 
    }

    if(typeof CSS != 'undefined')
    {
      config.pageStyle = "@import url(custom.css);";
    }

    if(typeof Stylist != 'undefined')
    {
      // We can load an external stylesheet like this - NOTE : YOU MUST GIVE AN ABSOLUTE URL
      //  otherwise it won't work!
      config.stylistLoadStylesheet(document.location.href.replace(/[^\/]*\.html/, 'stylist.css'));

      // Or we can load styles directly
      config.stylistLoadStyles('p.red_text { color:red }');

      // If you want to provide "friendly" names you can do so like
      // (you can do this for stylistLoadStylesheet as well)
      config.stylistLoadStyles('p.pink_text { color:pink }', {'p.pink_text' : 'Pretty Pink'});
    }

    if(typeof DynamicCSS != 'undefined')
    {
      config.pageStyle = "@import url(dynamic.css);";
    }

    if(typeof InsertWords != 'undefined')
    {
      // Register the keyword/replacement list
      var keywrds1 = new Object();
      var keywrds2 = new Object();

      keywrds1['-- Dropdown Label --'] = '';
      keywrds1['onekey'] = 'onevalue';
      keywrds1['twokey'] = 'twovalue';
      keywrds1['threekey'] = 'threevalue';

      keywrds2['-- Insert Keyword --'] = '';
      keywrds2['Username'] = '%user%';
      keywrds2['Last login date'] = '%last_login%';
      config.InsertWords = {
        combos : [ { options: keywrds1, context: "body" },
               { options: keywrds2, context: "li" } ]
      }

    }
    // Path for InsertPicture plugin
    if(typeof InsertPicture != 'undefined') {
      InsertPicture.PicturePath = '/schmal/pictures/';
    }

    return config;
  }


  var f = document.forms[0];
  f.method = 'post';
  f.action = 'ext_example-dest.php';
  f.innerHTML = '';

  var lipsum = document.getElementById('lipsum').innerHTML;

  for(var x = 0; x < num; x++)
  {
    var ta = 'myTextarea' + x;

    var div = document.createElement('div');
    div.className = 'area_holder';

    var txta = document.createElement('textarea');
    txta.id   = ta;
    txta.name = ta;
    txta.value = lipsum;
    txta.style.width="100%";
    txta.style.height="420px";

    div.appendChild(txta);
    f.appendChild(div);
  }
  
  //check submitted values
  var submit = document.createElement('input');
  submit.type = "submit";
  submit.value = "Submit";
  f.appendChild(submit);
  
  var _oldSubmitHandler = null;
  if (document.forms[0].onsubmit != null) {
    _oldSubmitHandler = document.forms[0].onsubmit;
  }
  function frame_onSubmit(){
    if (_oldSubmitHandler != null) {
      _oldSubmitHandler();
    }
  }
  document.forms[0].onsubmit = frame_onSubmit;
