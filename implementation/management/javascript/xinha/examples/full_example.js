
  /*--------------------------------------:noTabs=true:tabSize=2:indentSize=2:--
    --  Xinha example logic.  This javascript is used to auto-generate examples
    --  as controlled by the options set in full_example-menu.html.  it's called
    --  from full_example-body.html.
    --
    --  $HeadURL: http://svn.xinha.python-hosting.com/trunk/examples/full_example.js $
    --  $LastChangedDate: 2005-02-19 18:13:44 +1300 (Sat, 19 Feb 2005) $
    --  $LastChangedRevision: 20 $
    --  $LastChangedBy: gogo $
    --------------------------------------------------------------------------*/

  var num     = 1;
  if(window.parent && window.parent != window)
  {
    var f = window.parent.menu.document.forms[0];
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

    return config;
  }


  var f = document.forms[0];
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