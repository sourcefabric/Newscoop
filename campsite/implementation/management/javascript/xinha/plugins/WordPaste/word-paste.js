/** 
 * WordPaste cleans MS Word Documents and also handles the security problem with
 * Mozilla browsers.
 */

function WordPaste(editor, args)
{
    this.editor = editor;
    var wordpaste = this;
    //editor.config.registerButton('wordpaste', this._lc("Remove formatting"), editor.imgURL('pasteword.gif', 'WordPaste'), true, function(e, objname, obj) { wordpaste._buttonPress(null, obj); });

    // See if we can find 'paste' and add wordpaste next to it
    //editor.config.addToolbarElement("wordpaste", "paste", +1);
}

WordPaste._pluginInfo =
{
  name     : "WordPaste",
  version  : "1.0",
  developer: "Paul Baranowski (paul@paulbaranowski.org)",
  developer_url: "http://campware.org/",
  c_owner      : "MDLF, Inc.",
  license      : "htmlArea",
  sponsor      : "MDLF, Inc.",
  sponsor_url  : "http://mdlf.org/"
};

WordPaste.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'WordPaste');
}

WordPaste.prototype._buttonPress = function(doPopup)
{
    var wordpaste = this;
    var editor = wordpaste.editor;
    
    if (doPopup) {
        // Mozilla has a security problem with paste.
        // Popup window to get the text.
        editor._popupDialog( "plugin://WordPaste/get_text", function( html ) 
            {
                if ( !html ) {  
                    //user must have pressed Cancel
                    return false;
                }
                if (editor.config.killWordOnPaste) {
                    html = wordpaste._clean(html);
                }
                editor.insertHTML(html);
                editor._wordClean();
            }, // anonymous function
            null);
    } // if
    else if (editor.config.killWordOnPaste) {
        editor._wordClean();
        var html = editor.getInnerHTML();
        html = wordpaste._clean(html);
        editor.setHTML(html);
    }
}

/**
 * This code started from FCKEditor's CleanAndPaste function. (Thank you FCKEditor!)
 * Its been modified to be much more aggressive with stripping things out...
 */
WordPaste.prototype._clean = function(html) {
    // Remove HTML comments
	html = html.replace(/<!--[\w\s\d@{}:.;,'"%!#_=&|?~()[*+\/\-\]]*-->/gi, "" );
    // Remove all HTML tags
	html = html.replace(/<\/?\s*HTML[^>]*>/gi, "" );
    // Remove all BODY tags
    html = html.replace(/<\/?\s*BODY[^>]*>/gi, "" );
    // Remove all META tags
	html = html.replace(/<\/?\s*META[^>]*>/gi, "" );
    // Remove all TITLE tags & content
	html = html.replace(/<\s*TITLE[^>]*>([^<]*)<\/\s*TITLE\s*>/i, "" );
    // Remove all HEAD tags & content
	html = html.replace(/<\s*HEAD[^>]*>(.|[\n\r\t])*<\/\s*HEAD\s*>/gi, "" );
    // Remove all SPAN tags
	html = html.replace(/<\/?\s*SPAN[^>]*>/gi, "" );
    // Remove all DIV tags
	html = html.replace(/<\/?\s*DIV[^>]*>/gi, "" );
    // Remove all FONT tags
	html = html.replace(/<\/?\s*FONT[^>]*>/gi, "" );
    // Remove all UL tags
	html = html.replace(/<\/?\s*UL[^>]*>/gi, "" );
    // Remove all OL tags
	html = html.replace(/<\/?\s*OL[^>]*>/gi, "" );
    // Remove all LI tags
	html = html.replace(/<\/?\s*LI[^>]*>/gi, "" );
    // Remove all TABLE tags
	html = html.replace(/<\/?\s*TABLE[^>]*>/gi, "" );
    // Remove all TH tags
	html = html.replace(/<\/?\s*TH[^>]*>/gi, "" );
    // Remove all TR tags
	html = html.replace(/<\/?\s*TR[^>]*>/gi, "" );
    // Remove all TD tags
	html = html.replace(/<\/?\s*TD[^>]*>/gi, "" );
    // Remove all HR tags
	html = html.replace(/<\/?\s*HR[^>]*>/gi, "" );
    // Remove all U tags
	html = html.replace(/<\/?\s*U[^>]*>/gi, "" );
    // Remove all STYLE tags & content
	//html = html.replace(/<\/?\s*STYLE[^>]*>(.|[\n\r\t])*<\/\s*STYLE\s*>/gi, "" );
	html = html.replace(/<\s*STYLE[^>]*>(.|[\n\r\t])*<\/\s*STYLE\s*>/gi, "" );
	// Remove Class attributes
	html = html.replace(/<\s*(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3") ;
	// Remove Style attributes
	html = html.replace(/<\s*(\w[^>]*) style="([^"]*)"([^>]*)/gi, "<$1$3") ;
	// Remove Lang attributes
	html = html.replace(/<\s*(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3") ;
	// Remove XML elements and declarations
	html = html.replace(/<\\?\?xml[^>]*>/gi, "") ;
	// Remove Tags with XML namespace declarations: <o:p></o:p>
	html = html.replace(/<\/?\w+:[^>]*>/gi, "") ;
	// Replace the &nbsp;
	html = html.replace(/&nbsp;/, " " );
	
	// Transform <p><br /></p> to <br>
	//html = html.replace(/<\s*p[^>]*>\s*<\s*br\s*\/>\s*<\/\s*p[^>]*>/gi, "<br>");
	html = html.replace(/<\s*p[^>]*><\s*br\s*\/?>\s*<\/\s*p[^>]*>/gi, "<br>");
	
	// Remove <P> 
	html = html.replace(/<\s*p[^>]*>/gi, "");
	
	// Replace </p> with <br>
	html = html.replace(/<\/\s*p[^>]*>/gi, "<br>");
	
	// Remove any <br> at the end
	html = html.replace(/(\s*<br>\s*)*$/, "");
	
	html = html.trim();
	return html;
}
