// By Adam Wright, for The University of Western Australia
//
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function EnterParagraphs(editor) {
  this.editor = editor;
  // Activate only if we're talking to Gecko
  if (HTMLArea.is_gecko)
    this.onKeyPress = this.__onKeyPress;
};

EnterParagraphs._pluginInfo = {
  name          : "EnterParagraphs",
  version       : "1.0",
  developer     : "Adam Wright",
  developer_url : "http://www.hipikat.org/",
  sponsor       : "The University of Western Australia",
  sponsor_url   : "http://www.uwa.edu.au/",
  license       : "htmlArea"
};

// Whitespace Regex
EnterParagraphs.prototype._whiteSpace = /^\s*$/;
// The pragmatic list of which elements a paragraph may not contain, and which may contain a paragraph
EnterParagraphs.prototype._pExclusions = /^(address|blockquote|body|dd|div|dl|dt|fieldset|form|h1|h2|h3|h4|h5|h6|hr|li|noscript|ol|p|pre|table|ul)$/i;
EnterParagraphs.prototype._pContainers = /^(body|del|div|fieldset|form|ins|map|noscript|object|td|th)$/i;
// Elements which may not contain paragraphs, and would prefer a break to being split
EnterParagraphs.prototype._pBreak = /^(address|pre|blockquote)$/i;
// Elements which may not contain children
EnterParagraphs.prototype._permEmpty = /^(area|base|basefont|br|col|frame|hr|img|input|isindex|link|meta|param)$/i;
// Elements which count as content, as distinct from whitespace or containers
EnterParagraphs.prototype._elemSolid = /^(applet|br|button|hr|img|input|table)$/i;
// Elements which should get a new P, before or after, when enter is pressed at either end
EnterParagraphs.prototype._pifySibling = /^(address|blockquote|del|div|dl|fieldset|form|h1|h2|h3|h4|h5|h6|hr|ins|map|noscript|object|ol|p|pre|table|ul|)$/i;
EnterParagraphs.prototype._pifyForced = /^(ul|ol|dl|table)$/i;
// Elements which should get a new P, before or after a close parent, when enter is pressed at either end
EnterParagraphs.prototype._pifyParent = /^(dd|dt|li|td|th|tr)$/i;

// Gecko's a bit lacking in some odd ways...
EnterParagraphs.prototype.insertAdjacentElement = function(ref,pos,el) {

  if ( pos == 'BeforeBegin' ) ref.parentNode.insertBefore(el,ref);
  else if ( pos == 'AfterEnd' ) ref.nextSibling ? ref.parentNode.insertBefore(el,ref.nextSibling) : ref.parentNode.appendChild(el);
  else if ( pos == 'AfterBegin' && ref.firstChild ) ref.insertBefore(el,ref.firstChild);
  else if ( pos == 'BeforeEnd' || pos == 'AfterBegin' ) ref.appendChild(el);
};

// Passes a global parent node or document fragment to forEachNode
EnterParagraphs.prototype.forEachNodeUnder = function (top, fn, ltr, init, parm) {

  // Identify the first and last nodes to deal with
  var start, end;
  if ( top.nodeType == 11 && top.firstChild ) {
    start = top.firstChild;
    end = top.lastChild;
  } else start = end = top;
  while ( end.lastChild ) end = end.lastChild;

  // Pass onto forEachNode
  return this.forEachNode(start, end, fn, ltr, init, parm);
};

// Throws each node into a function
EnterParagraphs.prototype.forEachNode = function (left, right, fn, ltr, init, parm) {

  var xBro = function(elem, ltr) { return ( ltr ? elem.nextSibling : elem.previousSibling ); };
  var xSon = function(elem, ltr) { return ( ltr ? elem.firstChild : elem.lastChild ); };
  var walk, lookup, fnVal, ping = init;

  // Until we've hit the last node
  while ( walk != ltr ? right : left ) {

    // Progress to the next node
    if ( !walk ) walk = ltr ? left : right;
    else {
      if ( xSon(walk,ltr) ) walk = xSon(walk,ltr);
      else {
        if ( xBro(walk,ltr) ) walk = xBro(walk,ltr);
        else {
          lookup = walk;
          while ( !xBro(lookup,ltr) && lookup != (ltr ? right : left) ) lookup = lookup.parentNode;
          walk = ( lookup.nextSibling ? lookup.nextSibling : lookup ) ;
          if ( walk == right ) break;
    }	}	}

    fnVal = fn(this, walk, ping, parm, (walk==(ltr?right:left)));	// Throw this node at the wanted function
    if ( fnVal[0] ) return fnVal[1];								// If this node wants us to return, return pong
    if ( fnVal[1] ) ping = fnVal[1];								// Otherwise, set pong to ping, to pass to the next node
  }
  return false;
};

// forEachNode fn: Find a post-insertion node, only if all nodes are empty, or the first content
EnterParagraphs.prototype._fenEmptySet = function (parent, node, pong, getCont, last) {

  // Mark this if it's the first base
  if ( !pong && !node.firstChild ) pong = node;

  // Check for content
  if ( (node.nodeType == 1 && parent._elemSolid.test(node.nodeName)) ||
    (node.nodeType == 3 && !parent._whiteSpace.test(node.nodeValue)) ||
    (node.nodeType != 1 && node.nodeType != 3) ) {

    return new Array(true, (getCont?node:false));
  }

  // Only return the 'base' node if we didn't want content
  if ( last && !getCont ) return new Array(true, pong);
  return new Array(false, pong);
};

// forEachNode fn:
EnterParagraphs.prototype._fenCullIds = function (parent, node, pong, parm, last) {

  // Check for an id, blast it if it's in the store, otherwise add it
  if ( node.id ) pong[node.id] ? node.id = '' : pong[node.id] = true;
  return new Array(false,pong);
};

// Grabs a range suitable for paragraph stuffing
EnterParagraphs.prototype.processSide = function(rng, left) {

  var next = function(element, left) { return ( left ? element.previousSibling : element.nextSibling ); };
  var node = left ? rng.startContainer : rng.endContainer;
  var offset = left ? rng.startOffset : rng.endOffset;
  var roam, start = node;

  // Never start with an element, because then the first roaming node might
  // be on the exclusion list and we wouldn't know until it was too late
  while ( start.nodeType == 1 && !this._permEmpty.test(start.nodeName) ) start = ( offset ? start.lastChild : start.firstChild );

  // Climb the tree, left or right, until our course of action presents itself
  while ( roam = roam ? ( next(roam,left) ? next(roam,left) : roam.parentNode ) : start ) {

    if ( next(roam,left) ) {
      // If the next sibling's on the exclusion list, stop before it
      if ( this._pExclusions.test(next(roam,left).nodeName) ) {
        return this.processRng(rng, left, roam, next(roam,left), (left?'AfterEnd':'BeforeBegin'), true, false);
    } } else {
      // If our parent's on the container list, stop inside it
      if (this._pContainers.test(roam.parentNode.nodeName)) {
        return this.processRng(rng, left, roam, roam.parentNode, (left?'AfterBegin':'BeforeEnd'), true, false);
      }
      // If our parent's on the exclusion list, chop without wrapping
      else if (this._pExclusions.test(roam.parentNode.nodeName)) {
        if (this._pBreak.test(roam.parentNode.nodeName)) {
          return this.processRng(rng, left, roam, roam.parentNode,
                            (left?'AfterBegin':'BeforeEnd'), false, (left?true:false));
        } else {
          return this.processRng(rng, left, (roam = roam.parentNode),
                            (next(roam,left) ? next(roam,left) : roam.parentNode),
              (next(roam,left) ? (left?'AfterEnd':'BeforeBegin') : (left?'AfterBegin':'BeforeEnd')), false, false);
}	}	}	}	};

// Neighbour and insertion identify where the new node, roam, needs to enter
// the document; landmarks in our selection will be deleted before insertion
EnterParagraphs.prototype.processRng = function(rng, left, roam, neighbour, insertion, pWrap, preBr) {

  var node = left ? rng.startContainer : rng.endContainer;
  var offset = left ? rng.startOffset : rng.endOffset;

  // Define the range to cut, and extend the selection range to the same boundary
  var editor = this.editor;
  var newRng = editor._doc.createRange();
  newRng.selectNode(roam);
  if (left) {
    newRng.setEnd(node, offset);
    rng.setStart(newRng.startContainer, newRng.startOffset);
  } else {
    newRng.setStart(node, offset);
    rng.setEnd(newRng.endContainer, newRng.endOffset);
  }

  // Clone the range and remove duplicate ids it would otherwise produce
  var cnt = newRng.cloneContents();
  this.forEachNodeUnder(cnt, this._fenCullIds, true, this.takenIds, false);

  // Special case, for inserting paragraphs before some blocks when caret is at their zero offset
  var pify, pifyOffset, fill;
  pify = left ? (newRng.endContainer.nodeType == 3 ? true:false) : (newRng.startContainer.nodeType == 3 ? false:true);
  pifyOffset = pify ? newRng.startOffset : newRng.endOffset;
  pify = pify ? newRng.startContainer : newRng.endContainer;

  if ( this._pifyParent.test(pify.nodeName) && pify.parentNode.childNodes.item(0) == pify ) {
    while ( !this._pifySibling.test(pify.nodeName) ) pify = pify.parentNode;
  }

  if ( cnt.nodeType == 11 && !cnt.firstChild ) cnt.appendChild(editor._doc.createElement(pify.nodeName));
  fill = this.forEachNodeUnder(cnt,this._fenEmptySet,true,false,false);

  if ( fill && this._pifySibling.test(pify.nodeName) &&
    ( (pifyOffset == 0) || ( pifyOffset == 1 && this._pifyForced.test(pify.nodeName) ) ) ) {

    roam = editor._doc.createElement('p');
    roam.appendChild(editor._doc.createElement('br'));

    if (left && pify.previousSibling) return new Array(pify.previousSibling, 'AfterEnd', roam);
    else if (!left && pify.nextSibling) return new Array(pify.nextSibling, 'BeforeBegin', roam);
    else return new Array(pify.parentNode, (left?'AfterBegin':'BeforeEnd'), roam);
  }

  // If our cloned contents are 'content'-less, shove a break in them
  if ( fill ) {
    if ( fill.nodeType == 3 ) fill = fill.parentNode;		// Ill-concieved?
    if ( (fill.nodeType == 1 && !this._elemSolid.test()) || fill.nodeType == 11 ) fill.appendChild(editor._doc.createElement('br'));
    else fill.parentNode.insertBefore(editor._doc.createElement('br'),fill);
  }

  // And stuff a shiny new object with whatever contents we have
  roam = (pWrap || (cnt.nodeType == 11 && !cnt.firstChild)) ? editor._doc.createElement('p') : editor._doc.createDocumentFragment();
  roam.appendChild(cnt);
  if (preBr) roam.appendChild(editor._doc.createElement('br'));

  // Return the nearest relative, relative insertion point and fragment to insert
  return new Array(neighbour, insertion, roam);
};

// Called when a key is pressed in the editor
EnterParagraphs.prototype.__onKeyPress = function(ev) {

  // If they've hit enter and shift is up, take it
  if (ev.keyCode == 13 && !ev.shiftKey && this.editor._iframe.contentWindow.getSelection)
    return this.handleEnter(ev);
};

// Handles the pressing of an unshifted enter for Gecko
EnterParagraphs.prototype.handleEnter = function(ev) {

  // Grab the selection and associated range
  var sel = this.editor._getSelection();
  var rng = this.editor._createRange(sel);
  this.takenIds = new Object();

  // Grab ranges for document re-stuffing, if appropriate
  var pStart = this.processSide(rng, true);
  var pEnd = this.processSide(rng, false);

  // Get rid of everything local to the selection
  sel.removeAllRanges();
  rng.deleteContents();

  // Grab a node we'll have after insertion, since fragments will be lost
  var holdEnd = this.forEachNodeUnder(pEnd[2], this._fenEmptySet, true, false, true);

  // Reinsert our carefully chosen document fragments
  if ( pStart ) this.insertAdjacentElement(pStart[0], pStart[1], pStart[2]);
  if ( pEnd.nodeType != 1 ) this.insertAdjacentElement(pEnd[0], pEnd[1], pEnd[2]);

  // Move the caret in front of the first good text element
  if ( this._permEmpty.test(holdEnd.nodeName) ) {
    var prodigal = 0;
    while ( holdEnd.parentNode.childNodes.item(prodigal) != holdEnd ) prodigal++;
    sel.collapse( holdEnd.parentNode, prodigal);
  }
  else sel.collapse(holdEnd, 0);
  this.editor.scrollToElement(holdEnd);
  this.editor.updateToolbar();

  //======================
    HTMLArea._stopEvent(ev);
    return true;
};
