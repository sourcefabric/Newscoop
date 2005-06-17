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
    // Remove all SPAN tags
	html = html.replace(/<\/?\s*SPAN[^>]*>/gi, "" );
    // Remove all STYLE tags & content
	html = html.replace(/<\/?\s*STYLE[^>]*>(.|[\n\r\t])*<\/\s*STYLE\s*>/gi, "" );
    // Remove all TITLE tags & content
	html = html.replace(/<\s*TITLE[^>]*>(.|[\n\r\t])*<\/\s*TITLE\s*>/gi, "" );
    // Remove all HEAD tags & content
	html = html.replace(/<\s*HEAD[^>]*>(.|[\n\r\t])*<\/\s*HEAD\s*>/gi, "" );
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
	
	// Transform <P> to <DIV>
	html = html.replace(/<\s*p[^>]*>/gi, "<div>");
	html = html.replace(/<\/\s*p[^>]*>/gi, "</div>");
	
	html = html.trim();
	return html;
}
