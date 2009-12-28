<html>
<head>
<meta HTTP-EQUIV="CONTENT-TYPE" content="text/html; charset=utf-8">
</head>
<body>
 <!-- Required CSS -->
<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.5.2/build/treeview/assets/skins/sam/treeview.css">
 
<!-- Dependency source files --> 
<script src = "http://yui.yahooapis.com/2.5.2/build/yahoo/yahoo-min.js" ></script>
<script src = "http://yui.yahooapis.com/2.5.2/build/event/event-min.js" ></script>

<!-- TreeView source file --> 
<script src = "http://yui.yahooapis.com/2.5.2/build/treeview/treeview-min.js" ></script>

<script src="http://yui.yahooapis.com/2.5.2/build/connection/connection-min.js"></script>
  

<div class="yui-skin-sam">
<div id="treeDiv1" align="left"></div>
</div>

<script>

var tree;
var stored_nodes = new Array();
var stored_callbacks = new Array();

function treeInit() {
   tree = new YAHOO.widget.TreeView("treeDiv1");
   tree.setDynamicLoad(loadDataForNode);

   var root = tree.getRoot();

   var myobj = { label: "Blogs", id:"0" } ;
   var tmpNode = new YAHOO.widget.TextNode(myobj, root, false);

   tree.draw();
}

 function loadDataForNode(node, onCompleteCallback) {

    var url;
    var node_id = node.data.id;
    stored_nodes[node_id] = node;
    stored_callbacks[node_id] = onCompleteCallback;
    
    if (node.data.blog_id) {
        url = sUrl + '&node_id=' + node_id + '&f_blog_id=' + node.data.blog_id;  
            
    }
    
    if (node.data.entry_id) {
        url = sUrl + '&node_id=' + node_id + '&f_blogentry_id=' + node.data.entry_id;
        
    }
    
    if (node.data.comment_id) {
        url = sUrl + '&node_id=' + node_id + '&f_blogcomment_id=' + node.data.comment_id;
        
    }
    
    if (node.data.id == 0) {
        url = sUrl + '&node_id=' + node_id;
    }
    
    if (url) {
        // ajax aufrufen
        var transaction = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
    }
 }


treeInit();

var sUrl = '/?tpl=273';
// Passing an example of array of arguments to both
// the success and failure callback handlers.
var args = ['foo','bar'];

var responseSuccess = function(o){
    /* Please see the Success Case section for more
     * details on the response object's properties.
     * o.tId
     * o.status
     * o.statusText
     * o.getResponseHeader[ ]
     * o.getAllResponseHeaders
     * o.responseText
     * o.responseXML
     * o.argument
     */
    
    var blogs;
    var entries;
    var comments;
    var comment;
    
    eval(o.responseText);
    //alert(o.responseText);
    //alert(node_id);

    node = stored_nodes[node_id];
    
    if (blogs) {
         for (var key in blogs) {
             title = blogs[key];
             var myobj = { label: title, blog_id : key } ;
             var tmpNode = new YAHOO.widget.TextNode(myobj, node, false);
         }  
    }
    
    if (entries) {
         for (var key in entries) {
             title = entries[key];
             var myobj = { label: title, entry_id : key } ;
             var tmpNode = new YAHOO.widget.TextNode(myobj, node, false);
         }  
    }
    
    if (comments) {
         for (var key in comments) {
             title = comments[key];
             var myobj = { label: title, comment_id : key } ;
             var tmpNode = new YAHOO.widget.TextNode(myobj, node, false);
         }  
    }

    if (comment) {
        var tmpNode = new YAHOO.widget.HTMLNode(comment, node, true);   
    }
      
    // Be sure to notify the TreeView component when the data load is complete
    stored_callbacks[node_id]();
};

var responseFailure = function(o){
// Access the response object's properties in the
// same manner as listed in responseSuccess( ).
// Please see the Failure Case section and
// Communication Error sub-section for more details on the
// response object's properties.
}

var callback =
{
  success:responseSuccess,
  failure:responseFailure,
  argument:args
};

//var transaction = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback, null);




/**
 * Concatenates the values of a variable into an easily readable string
 * by Matt Hackett [scriptnode.com]
 * @param {Object} x The variable to debug
 * @param {Number} max The maximum number of recursions allowed (keep low, around 5 for HTML elements to prevent errors) [default: 10]
 * @param {String} sep The separator to use between [default: a single space ' ']
 * @param {Number} l The current level deep (amount of recursion). Do not use this parameter: it's for the function's own use
 */
function print_r(x, max, sep, l) {

	l = l || 0;
	max = max || 10;
	sep = sep || ' ';

	if (l > max) {
		return "[WARNING: Too much recursion]\n";
	}

	var
		i,
		r = '',
		t = typeof x,
		tab = '';

	if (x === null) {
		r += "(null)\n";
	} else if (t == 'object') {

		l++;

		for (i = 0; i < l; i++) {
			tab += sep;
		}

		if (x && x.length) {
			t = 'array';
		}

		r += '(' + t + ") :\n";

		for (i in x) {
			try {
				r += tab + '[' + i + '] : ' + print_r(x[i], max, sep, (l + 1));
			} catch(e) {
				return "[ERROR: " + e + "]\n";
			}
		}

	} else {

		if (t == 'string') {
			if (x == '') {
				x = '(empty)';
			}
		}

		r += '(' + t + ') ' + x + "\n";

	}

	return r;

};
var_dump = print_r;



</script>


</body>
</html>