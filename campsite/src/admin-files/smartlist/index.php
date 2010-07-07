<?php
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");

camp_load_translation_strings("articles");

//
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = 1; //Input::Get('f_language_id', 'int', 0);
if (isset($_SESSION['f_language_selected'])) {
    $f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
    $f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);

// Get all publications
$publications = Publication::GetPublications();
$publicationsNo = is_array($publications) ? sizeof($publications) : 0;
$menuPubTitle = $publicationsNo > 0 ? getGS('All Publications') : getGS('No publications found');

// Get the whole topics tree
$allTopics = Topic::GetTree();

//
$crumbs = array();
$crumbs[] = array(getGS('Content'), '');
$crumbs[] = array(getGS('Article List'), '');
echo camp_html_breadcrumbs($crumbs);
?>

<style type="text/css">
#doc {
    margin-left: 15px;
    width: 963px;
	//width: 98%;
	//margin: 0 auto;
}
#hd {
	width: 700px;
	margin: 0 auto;
}
.yui-skin-sam .yui-dt table {
	width: 100%;
}
.yui-skin-sam .yui-dt th.yui-dt-col-name {
	width: 301px;
}
.yui-skin-sam .yui-dt .yui-dt-col-state1,
.yui-skin-sam .yui-dt .yui-dt-col-state2,
.yui-skin-sam .yui-dt .yui-dt-col-state3 {
	width: 133px;
	text-align: right;
}
#breadcrumbContainer {
    width: 750px;
	height: 28px;
	padding: 8px 0;
	text-align: center;
}
#searchContainer {
    float: left;
    width: 350px;
    font-size: 85%;
}
#dt_input {
    position: static;
    width: 200px;
}
#contentSelectContainer {
	float: left;
	width: 399px;
}
#contentSelectContainer .yui-button button {
	width: 123px;
	*width: 121px;
	_width: 118px;
	font-size: 70%;
	line-height: 1.5;
}

#controlsContainer {
    width: 100%;
    height: 28px;
    padding: 8px 0;
	text-align: center;
}
#filterSelectContainer {
    float: left;
    width: 33%;
    text-align: left;
}
#dataPaginator {
    float: left;
	font-size: 85%;
	width: 34%;
}
#actionSelectContainer {
	float: left;
	width: 33%;
	text-align: right;
}
li.yui-button-selectedmenuitem {
    background: url(./assets/images/checkbox.png) left center no-repeat;
}




#dt-dlg {visibility:hidden;border:1px solid #808080;background-color:#E3E3E3;}
#dt-dlg .hd {text-align:left;font-weight:bold;padding:1em;background:none;background-color:#E3E3E3;border-bottom:0;}
#dt-dlg .ft {text-align:right;padding:.5em;background-color:#E3E3E3;}
#dt-dlg .bd {font-size:85%;height:10em;margin:0 1em;overflow:auto;border:1px solid black;background-color:white;}
#dt-dlg .dt-dlg-pickercol {clear:both;padding:.5em 1em 2.5em;border-bottom:1px solid gray;}
#dt-dlg .dt-dlg-pickerkey {float:left;padding-top:.3em;}
#dt-dlg .dt-dlg-pickerbtns {float:right;}
.yui-skin-sam .mask {
    -moz-opacity: 0.6;
    opacity:.60;
    filter: alpha(opacity=60);
    background-color:#272727;
}

/* Container workarounds for Mac Gecko scrollbar issues */
.yui-panel-container.hide-scrollbars #dt-dlg .bd {
    /* Hide scrollbars by default for Gecko on OS X */
    overflow: hidden;
}
.yui-panel-container.show-scrollbars #dt-dlg .bd {
    /* Show scrollbars for Gecko on OS X when the Panel is visible  */
    overflow: auto;
}
#dt-dlg_c .underlay {overflow:hidden;}
.inprogress {position:absolute;} /* transitional progressive enhancement state */
.yui-dt-liner {white-space:nowrap;}

/* Class for marked rows */
.yui-skin-sam .yui-dt tr.mark,
.yui-skin-sam .yui-dt tr.mark td.yui-dt-asc,
.yui-skin-sam .yui-dt tr.mark td.yui-dt-desc,
.yui-skin-sam .yui-dt tr.mark td.yui-dt-asc,
.yui-skin-sam .yui-dt tr.mark td.yui-dt-desc {
    background-color: #ffdfdf;
}

/* custom styles for this example */
.dnd-class {
	opacity: 0.6;
	filter:alpha(opacity=60);
	color:blue;
	border: 2px solid gray;
}
#articlesTable tr {
 	cursor: pointer;
}

/* calendar */
#cal1Container {
    display:none;
    position:absolute;
    z-index:1;
}
#cal2Container {
    display:none;
    position:absolute;
    z-index:1;
}
#dates {
    display:none;
}
// Topic filtering
#myAutoComplete {
    display:none;
    width:15em; /* set width here or else widget will expand to fit its container */
    padding-bottom:2em;
}
#myInput {
    visibility:hidden;
}
.match {
    font-weight:bold;
}
input.publish-date-single, input.publish-date-range {
    visibility:hidden;
}
div.message {
    padding-top: 10px;
    color: red;
    text-align: center;
    font-weight: bold;
}
</style>

<div id="message" class="message">&nbsp;</div>
<div id="doc" class="yui-skin-sam">
	<div id="hd">
		<div id="breadcrumbContainer">
		    <div id="contentSelectContainer">
			<label id="contentPublicationSelectContainer">
				<input type="button" class="menuButton" id="publication" value="<em><?php p($menuPubTitle); ?></em>">
				<select id="publicationSelect">
				    <?php if ($publicationsNo > 0) { ?>
				    <option value="0"><?php p($menuPubTitle); ?></option>
				    <?php
				              foreach($publications as $tmpPublication) { ?>
                    <option value="<?php echo $tmpPublication->getPublicationId(); ?>"><?php echo $tmpPublication->getName(); ?></option>
					<?php     }
					    } ?>
                </select>
            </label>
            <label id="contentIssueSectionSelectContainer">
				<input type="button" class="menuButton" id="issue" value="<?php putGS('All Issues'); ?>">
				<select id="issueSelect">
					<option value=""><?php putGS('All Issues'); ?></option>
				</select>

				<input type="button" class="menuButton" id="section" value="<?php putGS('All Sections'); ?>">
				<select id="sectionSelect">
				    <option value=""><?php putGS('All Sections'); ?></option>
				</select>
			</label>
			</div>
            <div id="searchContainer">
                <div id="autocomplete">
                    <label for="dt_input">Search Term: </label><input id="dt_input" type="text" value="" />
                    <div id="dt_ac_container"></div>
                </div>
		    </div>
		</div>
    </div>

    <div id="bd">
        <div id="controlsContainer">
            <div id="filterSelectContainer">
                <input type="button" class="menuButton" id="filter_type" value="<?php putGS('Filter by...'); ?>">
                <select id="actionSelect">
                    <option value=""><?php putGS('Filter by...'); ?></option>
                    <option value="author"><?php putGS('Author'); ?></option>
                    <option value="publish_date"><?php putGS('Date'); ?></option>
                    <option value="publish_range"><?php putGS('Date Range'); ?></option>
                    <option value="iduser"><?php putGS('Creator'); ?></option>
                    <option value="language"><?php putGS('Language'); ?></option>
                    <option value="status"><?php putGS('Status'); ?></option>
                    <option value="topic"><?php putGS('Topic'); ?></option>
                    <option value="type"><?php putGS('Type'); ?></option>
                </select>
                <label id="filtermenubutton-container"></label>
                <!--<label id="publish-date-single"></label>//-->
                <input type="button" id="publish-date-single" class="publish-date-single" name="publish-date-single" value="" />
                <input type="button" id="publish-date-range" class="publish-date-range" name="publish-date-range" value="" />
                <div id="cal1Container"></div>
                <div id="cal2Container"></div>
                <div id="dates">
                  <input type="hidden" name="dateTxt" id="dateTxt">
                  <input type="hidden" name="in" id="in">
                  <input type="hidden" name="out" id="out">
                </div>
                <div id="myAutoComplete">
                  <input id="myInput" type="text">
                  <div id="myContainer"></div>
                  <input id="myHidden" type="hidden">
                </div>
            </div>
            <div id="dataPaginator"><!-- The Paginator widget is rendered here --></div>
            <div id="actionSelectContainer">
                <input type="button" id="confColsPushButton" name="colsPushButton" value="<?php putGS('Show/Hide Columns'); ?>" />
                <div id="dt-dlg" class="inprogress">
                    <div class="hd"><?php putGS('Choose which columns you would like to see'); ?></div>
                    <div id="dt-dlg-picker" class="bd"></div>
                </div>
                <input type="button" class="menuButton" id="action" value="<?php putGS('Actions...'); ?>">
                <select id="actionSelect">
                    <option value=""><?php putGS('Actions...'); ?></option>
                    <option value="workflow_publish"><?php putGS('Status: Publish'); ?></option>
                    <option value="workflow_submit"><?php putGS('Status: Submit'); ?></option>
                    <option value="workflow_new"><?php putGS('Status: Set New'); ?></option>
                    <option value="switch_onfrontpage"><?php putGS('Toggle: \'On Front Page\''); ?></option>
                    <option value="switch_onsectionpage"><?php putGS('Toggle: \'On Section Page\''); ?></option>
                    <option value="switch_comments"><?php putGS('Toggle: \'Comments\''); ?></option>
                    <option value="schedule_publish"><?php putGS('Publish Schedule'); ?></option>
                    <option value="unlock"><?php putGS('Unlock'); ?></option>
                    <option value="delete"><?php putGS('Delete'); ?></option>
                    <option value="duplicate"><?php putGS('Duplicate'); ?></option>
                    <option value="duplicate_interactive"><?php putGS('Duplicate to another section'); ?></option>
                    <option value="move"><?php putGS('Move'); ?></option>
                </select>
            </div>
		</div>

		<div id="articlesTable"><!-- The DataTable widget is rendered here --></div>
	</div>
</div>



<!-- Combo-handled YUI JS files: -->
<script type="text/javascript" src="../../javascript/yui/build/yuiloader/yuiloader.js"></script>
<script type="text/javascript">
YAHOO.namespace("campsite");
var loader = new YAHOO.util.YUILoader();
loader.insert({
    require: ["fonts","container","dragdrop","menu","button","connection","paginator","datatable","animation","autocomplete","calendar"],
    base: '../../javascript/yui/build/',
    filter: 'debug',
    allowRollup: false,
    onSuccess: function() {
    	var Dom = YAHOO.util.Dom,
    		Event = YAHOO.util.Event,
    		Connect = YAHOO.util.Connect,
    		XHRDataSource = YAHOO.util.XHRDataSource,
    		DDM = YAHOO.util.DragDropMgr,
    		Button = YAHOO.widget.Button,
    		ButtonGroup = YAHOO.widget.ButtonGroup,
    		DataTable = YAHOO.widget.DataTable,
    		Paginator = YAHOO.widget.Paginator,
    		AutoComplete = YAHOO.widget.AutoComplete,
    		CF, // CustomFilter
            RangeDateCalendar,
            SingleDateCalendar;

    	/**
    	 * CF creates a paginated DataTable of Articles data
    	 * which can be filtered by...
    	 */
    	CF = {
    		/**
    		 * The Menu Buttons are stored in these arrays.
    		 */
    		menuButtons: [],
    		submenuButton: [],

    		/**
    		 * Filter settings are stored here. These settings are then
    		 * used to create the query string.
    		 */
    		settings: {
    			publication: '',
    			issue: '',
    			section: '',
    			filter_type: '',
    			filter_input: '',
    		},

    		/**
    		 * Initialize all the Buttons/Menu Buttons and the DataTable.
    		 */
    		init: function () {
    			var myColumnDefs,
    				myDataSource,
    				myConfigs,
    				//
    				pubMenuEls = Dom.getElementsByClassName('menuButton', 'input', 'contentPublicationSelectContainer'),
    				iasMenuEls = Dom.getElementsByClassName('menuButton', 'input', 'contentIssueSectionSelectContainer'),
    				//
    				actionEl = Dom.getElementsByClassName('menuButton', 'input', 'actionSelectContainer');
    				//
    				filterEls = Dom.getElementsByClassName('menuButton', 'input', 'filterSelectContainer');
    				//
    				message = Dom.get('message');

                //
                var oACDS = new YAHOO.util.FunctionDataSource(this.fireDT);
                oACDS.queryMatchContains = true;
                var oAutoComp = new AutoComplete("dt_input","dt_ac_container", oACDS);
                // Do not query until we have at least 3 chars
                oAutoComp.minQueryLength = 0;

    			// Create the select menus
    			Dom.batch(pubMenuEls, this.createMenuButton, "content_pub_menu");
    			Dom.batch(iasMenuEls, this.createMenuButton, "content_ias_menu");
    			Dom.batch(actionEl, this.createMenuButton, "action_menu");
    			Dom.batch(filterEls, this.createFilterByMenuButton);
                //
    			var oConfColsPushButton = new Button("confColsPushButton");

                // Define a custom row formatter function
                var rowCustomHighlighter = function(elTr, oRecord) {
                    if (oRecord.getData('art_lockhighlight')) {
                        Dom.addClass(elTr, 'mark');
                    }
                    return true;
                };

                //
                var articleTitleFormat = function(elLiner, oRecord, oColumn, oData) {
                    if (oRecord.getData('art_islocked')) {
                        elLiner.innerHTML = '<img src="assets/images/art_locked.png" align="absmiddle" alt="'
                            + oRecord.getData('art_lockinfo') + '" title="' + oRecord.getData('art_lockinfo')
                            + '" /> <a href="' + oRecord.getData('art_link') + '">' + oData + '</a>';
                    } else {
                        elLiner.innerHTML = '<a href="' + oRecord.getData('art_link') + '">' + oData + '</a>';
                    }
                };

                // Keep record of the checkbox states to handle column sorting
                var checked = [];

                //
                DataTable.Formatter.check = function (elLiner, oRecord, oColumn, oData) {
                    elLiner.innerHTML = '<input type="checkbox" name="filerow" value="'+ oData + '"' + (checked[oData] ? ' checked="checked">' : '>');
                };

    			// Define the DataTable's columns
    			myColumnDefs = [
    				{key: "art_id", label: "<input id=\"chkall\" name=\"chkall\" value=\"\" type=\"checkbox\">",formatter:"check"},
    				{key: "art_name", label: "<?php putGS('Name'); ?>", formatter:articleTitleFormat, width:300, resizeable:true, sortable:true},
    				{key: "art_type", label: "<?php putGS('Type'); ?>", width:"auto", sortable:true},
    				{key: "art_createdby", label: "<?php putGS('Created by'); ?>", width:"auto", hidden:true},
    				{key: "art_author", label: "<?php putGS('Author'); ?>", width:"auto", sortable:true},
    				{key: "art_status", label: "<?php putGS('Status'); ?>", width:"auto", sortable:true, editor: new YAHOO.widget.RadioCellEditor({radioOptions:["Published","Submitted","New"],disableBtns:true})},
    				{key: "art_ofp", label: "<?php putGS('On Front Page'); ?>", editor: new YAHOO.widget.RadioCellEditor({radioOptions:["Yes","No"],disableBtns:true})},
    				{key: "art_osp", label: "<?php putGS('On Section Page'); ?>", editor: new YAHOO.widget.RadioCellEditor({radioOptions:["Yes","No"],disableBtns:true})},
    				{key: "art_images", label: "<?php putGS('Images'); ?>", formatter:"number", hidden:true},
    				{key: "art_topics", label: "<?php putGS('Topics'); ?>", formatter:"number"},
    				//{key: "art_comments", label: "<?php putGS('Comments'); ?>", formatter:"number", sortable:true, hidden:true},
    				{key: "art_comments", label: "<?php putGS('Comments'); ?>", editor: new YAHOO.widget.RadioCellEditor({radioOptions:["Yes","No"],disableBtns:true}), sortable:true, hidden:true},
    				{key: "art_reads", label: "<?php putGS('Reads'); ?>", formatter:"number", sortable:true},
    				{key: "art_lastmodifieddate", label: "<?php putGS('Last Modified'); ?>", formatter:"date"},
    				{key: "art_publishdate", label: "<?php putGS('Publish Date'); ?>", formatter:"date", hidden:true},
    				{key: "art_creationdate", label: "<?php putGS('Creation Date'); ?>", formatter:"date", hidden:true},
    			];

    			// Create a new DataSource
    			myDataSource = new XHRDataSource("assets/php/dynamicfilter/data.php?");

    			// data.php just happens to use JSON. Let the DataSource
    			// know to expect JSON data.
    			myDataSource.responseType = XHRDataSource.TYPE_JSON;

    			//
    			myDataSource.connXhrMode = "queueRequests";

    			// Define the structure of the DataSource data.
    			myDataSource.responseSchema = {
    				resultsList: "records",
    				fields: [
    					{key: "art_id"},
    					{key: "art_name"},
    					{key: "art_type"},
    					{key: "art_createdby"},
    					{key: "art_author"},
    					{key: "art_status"},
    					{key: "art_ofp"},
    					{key: "art_osp"},
    					{key: "art_images"},
    					{key: "art_topics"},
    					{key: "art_comments"},
    					{key: "art_reads"},
    					{key: "art_lastmodifieddate"},
    					{key: "art_publishdate"},
    					{key: "art_creationdate"},
    					{key: "art_islocked"},
    					{key: "art_lockinfo"},
    					{key: "art_lockhighlight"},
    					{key: "art_link"},
    					{key: "art_languageid"},
    				],
    				metaFields: {
    					totalRecords: "totalRecords"
    				}
    			};

    			// Set the DataTable configuration
    			myConfigs = {
    				initialLoad: false,
    				dynamicData: true,
    				paginator: new Paginator({
    					rowsPerPage: 25,
    					totalRecords: myDataSource.length,
    					containers: 'dataPaginator',
    					template: "{CurrentPageReport} {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} {RowsPerPageDropdown}",
    					pageReportTemplate: "<strong>{startRecord}</strong> - <strong>{endRecord}</strong> of <strong>{totalRecords}</strong>",
    					rowsPerPageOptions: [10,25,50],
    					pageLinks: 5
    				}),
    				sortedBy:{
                        key: 'art_name',
                        dir: DataTable.CLASS_ASC
                    },
                    formatRow: rowCustomHighlighter,

    				// This configuration item is what builds the query string
    				// passed to the DataSource.
    				generateRequest: this.requestBuilder
    			};

    			// Create the DataTable.
    			myDataTable = new DataTable("articlesTable", myColumnDefs, myDataSource, myConfigs);

    			//
    			myDTDrags = {};

                // Set up editing flow
                var highlightEditableCell = function(oArgs) {
                    var elCell = oArgs.target;
                    if(Dom.hasClass(elCell, "yui-dt-editable")) {
                        this.highlightCell(elCell);
                    }
                };

                //
                var triggerSaveEvent = function(oArgs) {
                    var saveEditorHandler = {
                        success: function(o) {},
                        failure: function(o) {}
                    };

                    var sUrl = '/admin/smartlist/assets/dt_actions.php';
                    var postData = "&articleid=" + oArgs.editor.getRecord().getData('art_id')
                        + "&target=" + oArgs.editor.getColumn().getKey()
                        + "&value=" + oArgs.newData;
                        //alert(postData);
                    var request = Connect.asyncRequest('POST', sUrl, saveEditorHandler, postData);
                };

                // Set up editing flow
                var setActionMenuStatus = function(oArgs) {
                    if (myDataTable.getSelectedRows().length > 0) {
                        if (CF.menuButtons[3].get("disabled") == true) {
                            CF.menuButtons[3].getMenu().render();
                            CF.menuButtons[3].set('disabled', false);
                        }
                    } else {
                        CF.menuButtons[3].getMenu().render();
                        CF.menuButtons[3].set('disabled', true);
                    }
                };


                //
                myDataTable.subscribe("cellMouseoverEvent", highlightEditableCell);
                myDataTable.subscribe("cellMouseoutEvent", myDataTable.onEventUnhighlightCell);
                myDataTable.subscribe("cellClickEvent", myDataTable.onEventShowCellEditor);
                myDataTable.subscribe("editorSaveEvent", triggerSaveEvent);


    			// Define an event handler that scoops up the totalRecords which we sent as
    			// part of the JSON data. This is then used to tell the paginator the total records.
    			// This happens after each time the DataTable is updated with new data.
    			myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
    				oPayload.totalRecords = oResponse.meta.totalRecords;
    				return oPayload;
    			};

                // Enable row highlighting
                myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
                myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

                // Enable row selection
                myDataTable.subscribe("rowClickEvent",
                    function(ev) {
                        var target = Event.getTarget(ev);
                        // Unselect row
                        if (myDataTable.isSelected(target)) {
                            myDataTable.unselectRow(target);
                        }
                        // Select row
                        else {
                            myDataTable.selectRow(target);
                        }
                        setActionMenuStatus();
                        myDataTable.checkboxClickEvent.fire;
                    }
                );

                // Custom drag and drop class
                YAHOO.campsite.DDRows = function (id, sGroup, config) {
                    YAHOO.campsite.DDRows.superclass.constructor.call(this, id, sGroup, config);
                    Dom.addClass(this.getDragEl(), 'dnd-class');
                    //this.goingUp = false;
                    //this.lastY = 0;
                };

                // DDRows extends DDProxy
                YAHOO.extend(YAHOO.campsite.DDRows, YAHOO.util.DDProxy, {
                    proxyEl: null,
                    srcEl:null,
                    srcData:null,
                    srcIndex: null,
                    tmpIndex:null,

                    startDrag: function(x, y) {
                        var proxyEl = this.proxyEl = this.getDragEl(),
                            srcEl = this.srcEl = this.getEl();

                        //this.srcData = myDataTable.getRecord(this.srcEl).getData();
                        //this.srcIndex = srcEl.sectionRowIndex;
                        // Make the proxy look like the source element
                        //Dom.setStyle(srcEl, "visibility", "hidden");
                        proxyEl.innerHTML = "<table><tbody>"+srcEl.innerHTML+"</tbody></table>";
                    },

                    endDrag: function(x,y) {
                        //var position,
                            //srcEl = this.srcEl;

                        Dom.setStyle(this.proxyEl, "visibility", "hidden");
                        //Dom.setStyle(srcEl, "visibility", "");
                    },

                    //onDrag: function(e) {
                    //    // Keep track of the direction of the drag for use during onDragOver
                    //    var y = Event.getPageY(e);

                    //    if (y < this.lastY) {
                    //        this.goingUp = true;
                    //    } else if (y > this.lastY) {
                    //        this.goingUp = false;
                    //    }

                    //    this.lastY = y;
                    //},

                    //onDragOver: function(e, id) {
                        // Reorder rows as user drags
                    //    var srcIndex = this.srcIndex,
                    //        destEl = Dom.get(id),
                    //        destIndex = destEl.sectionRowIndex,
                    //        tmpIndex = this.tmpIndex;

                    //    if (destEl.nodeName.toLowerCase() === "tr") {
                    //        if(tmpIndex !== null) {
                    //            myDataTable.deleteRow(tmpIndex);
                    //        } else {
                    //            myDataTable.deleteRow(this.srcIndex);
                    //        }

                    //        myDataTable.addRow(this.srcData, destIndex);
                    //        this.tmpIndex = destIndex;

                    //        DDM.refreshCache();
                    //    }
                    //}
                    
                    onDragDrop: function(e, id) {
                        var destDD = DDM.getDDById(id);
                        // Only if dropping on a valid target
                        if(destDD && destDD.isTarget && this.srcEl) {
                            var	srcEl = this.srcEl,
                            srcIndex = srcEl.sectionRowIndex,
                            destEl = Dom.get(id),
                            destIndex = destEl.sectionRowIndex,
                            srcData = myDataTable.getRecord(srcEl).getData();

                            this.srcEl = null;

                            // Cleanup existing Drag instance
                            myDTDrags[srcEl.id].unreg();
                            delete myDTDrags[srcEl.id];

                            // Move the row to its new position
                            myDataTable.deleteRow(srcIndex);
                            myDataTable.addRow(srcData, destIndex);
                            DDM.refreshCache();
                        }
                    }
                });

                // Create DDRows instances when DataTable is initialized
                myDataTable.subscribe("initEvent", function() {
                    var i, id,
                    allRows = this.getTbodyEl().rows;

                    for(i = 0; i < allRows.length; i++) {
                        id = allRows[i].id;
                        // Clean up any existing Drag instances
                        if (myDTDrags[id]) {
                            myDTDrags[id].unreg();
                            delete myDTDrags[id];
                        }
                        // Create a Drag instance for each row
                        myDTDrags[id] = new YAHOO.campsite.DDRows(id);
                    }
                });

                // Create DDRows instances when new row is added
                myDataTable.subscribe("rowAddEvent",function(e) {
                    var id = e.record.getId();
                    myDTDrags[id] = new YAHOO.campsite.DDRows(id);
                });



                // Shows dialog, creating one when necessary
                var newCols = true;
                var showTableDlg = function(e) {
                    Event.stopEvent(e);

                    if(newCols) {
                        // Populate Dialog
                        // Using a template to create elements for the SimpleDialog
                        var allColumns = myDataTable.getColumnSet().keys;
                        var elPicker = Dom.get("dt-dlg-picker");
                        var elTemplateCol = document.createElement("div");
                        Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                        var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                        Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                        var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                        Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                        var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                        // Create one section in the SimpleDialog for each Column
                        var elColumn, elKey, elButton, oButtonGrp;
                        for(var i=0, l = allColumns.length; i < l; i++) {
                            var oColumn = allColumns[i];

                            // Use the template
                            elColumn = elTemplateCol.cloneNode(true);

                            // Write the Column key
                            elKey = elColumn.firstChild;
                            var oColDef = oColumn.getDefinition();
                            elKey.innerHTML = oColDef.label;

                            // Create a ButtonGroup
                            oButtonGrp = new YAHOO.widget.ButtonGroup({
                                id: "buttongrp"+i,
                                name: oColumn.getKey(),
                                container: elKey.nextSibling
                            });
                            oButtonGrp.addButtons([
                                { label: "<?php putGS('Show'); ?>", value: "Show", checked: ((!oColumn.hidden)), onclick: onclickObj},
                                { label: "<?php putGS('Hide'); ?>", value: "Hide", checked: ((oColumn.hidden)), onclick: onclickObj}
                            ]);

                            elPicker.appendChild(elColumn);
                        }
                        newCols = false;
                    }
                    myDlg.show();
                };
                var hideDlg = function(e) {
                    this.hide();
                };
                var handleButtonClick = function(e, oSelf) {
                    var sKey = this.get("name");
                    if (this.get("value") === "Hide") {
                        // Hides a Column
                        myDataTable.hideColumn(sKey);
                    } else {
                        // Shows a Column
                        myDataTable.showColumn(sKey);
                    }
                };

                // Create the SimpleDialog
                Dom.removeClass("dt-dlg", "inprogress");
                var myDlg = new YAHOO.widget.SimpleDialog("dt-dlg", {
                    width: "30em",
                    visible: false,
                    modal: true,
                    buttons: [
                        { text:"<?php putGS('Close'); ?>",  handler:hideDlg }
                    ],
                    fixedcenter: true,
                    constrainToViewport: true
                });
                myDlg.render();

                // Nulls out myDlg to force a new one to be created
                myDataTable.subscribe("columnReorderEvent", function(){
                    newCols = true;
                    Event.purgeElement("dt-dlg-picker", true);
                    Dom.get("dt-dlg-picker").innerHTML = "";
                }, this, true);

                // Hook up the SimpleDialog to the button
                Event.addListener("confColsPushButton", "click", showTableDlg, this, true);



    			// Store the DataTable and DataSource for use elsewhere in this script.
    			CF.myDataSource = myDataSource;
    			CF.myDataTable = myDataTable;

                // Initial load
    			CF.settings.publication = "0";
    			CF.fireDT(false);
    		},


    		/**
    		 * Create a Menu Button. Once the Menu Button is created, attached a
    		 * selectedMenuItemChange event listener.
    		 * @param {Object} el
    		 * An HTML Input Element used in creating a Menu Button from markup.
    		 */
    		createMenuButton: function (el, context) {
    			var buttonKey = CF.menuButtons.length;
                if (context == "content_ias_menu" || context == "action_menu") {
                    CF.menuButtons[buttonKey] = new Button(el, {
    				    type: 'menu',
    				    menu: Dom.getNextSibling(el),
    				    disabled: true,
    				    lazyloadmenu: false,
    			    });
                } else {
    			    CF.menuButtons[buttonKey] = new Button(el, {
    				    type: 'menu',
    				    menu: Dom.getNextSibling(el)
    			    });
    			}

    			if (context == "content_pub_menu" || context == "content_ias_menu") {
    			    CF.menuButtons[buttonKey].on("selectedMenuItemChange", CF.onContentMenuItemChange);
                } else if (context == "action_menu") {
                    CF.menuButtons[buttonKey].on("selectedMenuItemChange", CF.onActionMenuItemChange);
                }
    		},

    		/**
    		 * Handler for the selectedMenuItemChange event for the Menu Buttons.
    		 * This changes the label on the Menu buttons. Then, stores the value
    		 * of the new selected Menu Item in the CF.settings object. Each of the
    		 * Menu Buttons has an id that corresponds to one of the three United States
    		 * columns in the DataTable. This id is used as the key for the CF.settings.[STATE]
    		 * object. Lastly, fire a request for new data for the DataTable. Pass in a boolean
    		 * false so that the pagination settings are retained.
    		 * @param {Object} e
    		 */
    		onContentMenuItemChange: function (e) {
    			var oMenuItem = e.newValue;
    			this.set("label", ("<em class=\"yui-button-label\">" +
    				oMenuItem.cfg.getProperty("text") + "<\/em>"));
    			CF.settings[this.get('id')] = oMenuItem.value;

                if (this.get('id') == 'publication') {
                    var buttonKey = 1;
                } else if (this.get('id') == 'issue') {
                    var buttonKey = 2;
                }

                var sUrl = '/admin/smartlist/assets/load_filterby_menu.php';
    			if (this.get('id') == 'publication' || this.get('id') == 'issue') {
    			    if (oMenuItem.value > 0) {
                        var loadIssueMenuHandler = {
                            success: function(o) {
                                var issueMenuOptions = o.responseText;
                                if (issueMenuOptions.length > 0) {
                                    issueMenuOptions = issueMenuOptions.split(',');
                                }
                                var aIssueMenuOptions = new Array();
                                for ( var i=0, len=issueMenuOptions.length; i < len; ++i ) {
                                    var issueKeyValue = issueMenuOptions[i].split('|');
                                    aIssueMenuOptions[aIssueMenuOptions.length] = { text: issueKeyValue[1], value: issueKeyValue[0]};
                                }

                                var aItems = CF.menuButtons[buttonKey].getMenu().getItems();
                                if (aItems.length > 0) {
                                    CF.menuButtons[buttonKey].getMenu().clearContent();
                                }
                                if (aIssueMenuOptions.length > 0) {
                                    CF.menuButtons[buttonKey].getMenu().addItems(aIssueMenuOptions);
                                    CF.menuButtons[buttonKey].getMenu().render();
                                    CF.menuButtons[buttonKey].set('disabled', false);
                                    //CF.menuButtons[buttonKey].on("selectedMenuItemChange", CF.onContentMenuItemChange);
                                } else {
                                    CF.menuButtons[buttonKey].set('disabled', true);
                                }
                            },
                            failure: function(o) {}
                        };

                        if (this.get('id') == 'publication') {
                            var postData = "&action=content&publication=" + oMenuItem.value;
                        } else if (this.get('id') == 'issue') {
                            var postData = "&action=content&publication=" + CF.settings['publication'] + "&issue=" + oMenuItem.value;
                        }
                        var request = Connect.asyncRequest('POST', sUrl, loadIssueMenuHandler, postData);
                    } else {
                        var aItems = CF.menuButtons[buttonKey].getMenu().getItems();
                        if (aItems.length > 0) {
                            CF.menuButtons[buttonKey].getMenu().clearContent();
                        }

                        if (buttonKey == 1) {
                            aItems = CF.menuButtons[2].getMenu().getItems();
                            if (aItems.length > 0) {
                                CF.menuButtons[2].getMenu().clearContent();
                            }
                        }

                        CF.menuButtons[2].set("label", ("<em class=\"yui-button-label\">" +
                            "<?php putGS('All Sections'); ?><\/em>"));
                        CF.menuButtons[2].getMenu().render();
                        if (buttonKey == 1) {
                            CF.menuButtons[2].set('disabled', true);
                            CF.menuButtons[buttonKey].set("label", ("<em class=\"yui-button-label\">" +
                                "<?php putGS('All Issues'); ?><\/em>"));
                        }

                        labelText = (buttonKey == 1) ? '<?php putGS('All Publications'); ?>' : '<?php putGS('All Issues'); ?>';
                        this.set("label", ("<em class=\"yui-button-label\">" +
                            labelText + "<\/em>"));
                        CF.settings['section'] = 0;
                        if (this.get('id') == 'publication') {
                            CF.settings['issue'] = 0;
                        }
    			        CF.settings[this.get('id')] = 0;
                        CF.menuButtons[buttonKey].getMenu().render();
                        CF.menuButtons[buttonKey].set('disabled', true);
                    }
                }

    			CF.fireDT(false);
    		},

    		onActionMenuItemChange: function (e) {
                var oMenuItem = e.newValue;
    			this.set("label", ("<em class=\"yui-button-label\">" +
    				oMenuItem.cfg.getProperty("text") + "<\/em>"));
    			CF.settings[this.get('id')] = oMenuItem.value;
    			//
    			var selRows = CF.myDataTable.getSelectedRows();
    			if (selRows == null || selRows.length == 0) {
    			    return false;
    			}

                var deleteHandler = {
                    success: function(o) {
                        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                        var data = eval('(' + json + ')');
                        if (data.success == false) {
                            if (data.error != undefined) {
                                message.innerHTML = '<?php putGS("Error"); ?>' + ': '
                                    + data.error;
                            }
                            return false;
                        }
                        for (x in selRows) {
                            CF.myDataTable.deleteRow(selRows[x]);
                        }
                        message.style.color = 'green';
                        message.innerHTML = data.message;
                    },
                    failure: function(o) {
                        alert('failure');
                    }
                };

                var statusHandler = {
                    success: function(o) {
                        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                        var data = eval('(' + json + ')');
                        if (data.success == false) {
                            if (data.error != undefined) {
                                message.innerHTML = '<?php putGS("Error"); ?>' + ': '
                                    + data.error;
                            }
                            return false;
                        }
                        //
                        switch(actionName) {
                        case 'workflow_publish': newStatus = 'Published'; break;
                        case 'workflow_submit': newStatus = 'Submitted'; break;
                        case 'workflow_new': newStatus = 'New'; break;
                        }
                        // TODO: keep it to set some highligthing?
                        for (x in selRows) {
                            var recordData = CF.myDataTable.getRecord(selRows[x]);
                            CF.myDataTable.updateCell(recordData, 'art_status', newStatus);
                        }
                        message.style.color = 'green';
                        message.innerHTML = data.message;                        
                    },
                    failure: function(o) {
                        alert('failure');
                    }
                };

                var switchHandler = {
                    success: function(o) {
                        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                        var data = eval('(' + json + ')');
                        if (data.success == false) {
                            if (data.error != undefined) {
                                message.innerHTML = '<?php putGS("Error"); ?>' + ': '
                                    + data.error;
                            }
                            return false;
                        }

                        var oColumn;
                        //
                        switch(actionName) {
                        case 'switch_onfrontpage': oColumn = 'art_onfrontpage'; break;
                        case 'switch_onsectionpage': oColumn = 'art_onsectionpage'; break;
                        case 'switch_comments': oColumn = 'art_comments'; break;
                        }
                        // TODO: keep it to set some highligthing?
                        for (x in selRows) {
                            var oRecord = CF.myDataTable.getRecord(selRows[x]);
                            var oldValue = oRecord.getData(oColumn);
                            var oNewValue = (oldValue == 'Yes') ? 'No' : 'Yes';
                            CF.myDataTable.updateCell(oRecord, oColumn, oNewValue);
                        }
                        message.style.color = 'green';
                        message.innerHTML = data.message;                        
                    },
                    failure: function(o) {
                        alert('failure');
                    }
                };

                var unlockHandler = {
                    success: function(o) {
                        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                        var data = eval('(' + json + ')');
                        if (data.success == false) {
                            if (data.error != undefined) {
                                message.innerHTML = '<?php putGS("Error"); ?>' + ': '
                                    + data.error;
                            }
                            return false;
                        }
                        // TODO: Keep this to remove lock styling
                        //for (x in selRows) {
                            //CF.myDataTable.deleteRow(selRows[x]);
                        //}
                        message.style.color = 'green';
                        message.innerHTML = data.message;
                    },
                    failure: function(o) {
                        alert('failure');
                    }
                };

                var duplicateHandler = {
                    success: function(o) {
                        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                        var data = eval('(' + json + ')');
                        if (data.success == false) {
                            if (data.error != undefined) {
                                message.innerHTML = '<?php putGS("Error"); ?>' + ': '
                                    + data.error;
                            }
                            return false;
                        }
                        // TODO: Keep this?
                        //for (x in selRows) {
                            //CF.myDataTable.deleteRow(selRows[x]);
                        //}
                        message.style.color = 'green';
                        message.innerHTML = data.message;
                    },
                    failure: function(o) {
                        alert('failure');
                    }
                };

                var moveHandler = {
                    success: function(o) {
                        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
                        var data = eval('(' + json + ')');
                        if (data.success == false) {
                            if (data.error != undefined) {
                                message.innerHTML = '<?php putGS("Error"); ?>' + ': '
                                    + data.error;
                            }
                            return false;
                        }
                        //
                        if (data.goto != undefined && data.goto.length > 0) {
                            window.location = data.goto;
                        }
                    },
                    failure: function(o) {
                        alert('failure');
                    }
                };


                // } else if (actionName.indexOf('switch') == 0) {
                // var oColumn = 'art_' + actionName.substring(7);


                var sUrl = '/admin/smartlist/assets/dt_actions.php';
                var actionName = oMenuItem.value;
                var postData = '&action=' + actionName;

                switch(actionName) {
                case 'delete':                    
                    handler = deleteHandler;
                    var r = confirm("<?php echo getGS("Are you sure you want to delete the selected articles?"); ?>");
                    if (!r) {
                        return;
                    }
                    break;
                case 'workflow_publish':
                case 'workflow_submit':
                case 'workflow_new':
                    handler = statusHandler;
                    break;
                case 'switch_onfrontpage':
                case 'switch_onsectionpage':
                case 'switch_comments':
                    handler = switchHandler;
                    break;
                case 'unlock':
                    handler = unlockHandler;
                    break;
                case 'duplicate':
                    handler = duplicateHandler;
                    break;
                case 'duplicate_interactive':
                case 'move':
                    handler = moveHandler;
                    postData += '&f_publication_id=' + <?php echo $f_publication_id; ?>
                        + '&f_issue_number=' + <?php echo $f_issue_number; ?>
                        + '&f_section_number=' + <?php echo $f_section_number; ?>
                        + '&f_language_id=' + <?php echo $f_language_id; ?>
                        + '&f_language_selected=' + <?php echo $f_language_selected; ?>;
                    break;
                }

                for (x in selRows) {
                    postData += '&row' + x + '='
                        + encodeURIComponent(CF.myDataTable.getRecord(selRows[x]).getData("art_id"))
                        + '_' + encodeURIComponent(CF.myDataTable.getRecord(selRows[x]).getData("art_languageid"));
                }
                //alert('pD: ' + postData);
                var request = Connect.asyncRequest('POST', sUrl, handler, postData);

    			CF.fireDT(false);
    		},


            cleanUpSingleDateSelection: function () {
                if (SingleDateCalendar.getSelectedDates().length > 0) {
                    SingleDateCalendar.clear();
                }
                Dom.get("publish-date-single").style.visibility = "hidden";
            },


            cleanUpRangeDateSelection: function () {
                if (RangeDateCalendar.getSelectedDates().length > 0) {
                    RangeDateCalendar.clear();
                }
                Dom.get("publish-date-range").style.visibility = "hidden";
            },


            /**
    		 * Create a Menu Button. Once the Menu Button is created, attached a
    		 * selectedMenuItemChange event listener.
    		 * @param {Object} el
    		 * An HTML Input Element used in creating a Menu Button from markup.
    		 */
    		createFilterByMenuButton: function (el) {
    			var buttonKey = CF.menuButtons.length;
    			CF.menuButtons[buttonKey] = new Button(el, {
    				type: 'menu',
    				menu: Dom.getNextSibling(el)
    			});
    			CF.menuButtons[buttonKey].on("selectedMenuItemChange", CF.onFilterBySelectedMenuItemChange);
            },

            // "selectedMenuItemChange" event handler for a Button that will set
            // the Button's "label" attribute to the value of the "text"
            // configuration property of the MenuItem that was clicked.
            onFilterBySelectedMenuItemChange: function (e) {
                var oMenuItem = e.newValue;
                this.set("label", ("<em class=\"yui-button-label\">" +
                    oMenuItem.cfg.getProperty("text") + "<\/em>"));
                CF.settings[this.get('id')] = oMenuItem.value;
                
                var loadSubmenuHandler = {
                    success: function(o) {
                        var buttonKey = 0;
                        var filterMenuOptions = o.responseText;
                        filterMenuOptions = filterMenuOptions.split(',');
                        var aFilterMenuOptions = new Array();
                        for ( var i=0, len=filterMenuOptions.length; i < len; ++i ) {
                            var filterItemData = filterMenuOptions[i].split('|');
                            aFilterMenuOptions[aFilterMenuOptions.length] = { text: filterItemData[1], value: filterItemData[0]};
                        }
                        if (CF.submenuButton.length == 1) {
                            CF.submenuButton[buttonKey].destroy();
                        }
                        CF.submenuButton[buttonKey] = new YAHOO.widget.Button({
                                id: "filter_input",
                                name: "filter_input",
                                type: "menu",
                                label: "<em class=\"yui-button-label\"><?php putGS('Select'); ?> " + oMenuItem.cfg.getProperty("text") + "</em>",
                                menu: aFilterMenuOptions,
                                container: "filtermenubutton-container"
                        });
                        CF.submenuButton[buttonKey].on("selectedMenuItemChange", CF.onFilterSelectedMenuItemChange);
                    },
                    failure: function(o) {}
                };

                if (oMenuItem.value == 'publish_date') {
                    //
                    CF.cleanUpRangeDateSelection();
                    // Clean out filter input
                    if (CF.submenuButton.length == 1) {
                        CF.submenuButton[0].destroy();
                        CF.submenuButton = [];
                        CF.settings['filter_input'] = '';
                    }

                    SingleDateCalendar.show();
                    SingleDateCalendar.hideEvent.subscribe(function() {
                        var selDate = "";
		                if (SingleDateCalendar.getSelectedDates().length > 0) {
                            selDate = SingleDateCalendar.getSelectedDates()[0];
                        }
                        selDate = selDate.getFullYear() + "-" + (selDate.getMonth() + 1) + "-" + selDate.getDate();
                        //
                        //Dom.get("publish-date-single").innerHTML = "<a href=\"#\" id=\"cal2Button\" class=\"filterdate\">" + selDate + "</a>"; 
                        Dom.get("publish-date-single").value = selDate;
                        Dom.get("publish-date-single").style.visibility = "visible";
                        //

                        CF.settings['filter_type'] = oMenuItem.value;
                        CF.settings['filter_input'] = selDate;
                        CF.fireDT(false);
		            });
                } else if (oMenuItem.value == 'publish_range') {
                    //
                    CF.cleanUpSingleDateSelection();
                    // Clean out filter input
                    if (CF.submenuButton.length == 1) {
                        CF.submenuButton[0].destroy();
                        CF.submenuButton = [];
                        CF.settings['filter_input'] = '';
                    }

                    // Show the Calendar when the button is clicked
		            RangeDateCalendar.show();
		            RangeDateCalendar.hideEvent.subscribe(function() {
		                var x, intervalDates = new Array(),
		                    selectedDates = RangeDateCalendar.getSelectedDates();
		                if (Dom.get('in').value && Dom.get('out').value) {
		                    x = selectedDates.length - 1;
		                    intervalDates[0] = selectedDates[0].getFullYear() + "-" + (selectedDates[0].getMonth() + 1) + "-" + selectedDates[0].getDate();
		                    intervalDates[1] = selectedDates[x].getFullYear() + "-" + (selectedDates[x].getMonth() + 1) + "-" + selectedDates[x].getDate();
                        }
                        //
                        Dom.get("publish-date-range").value = "From "
                            + intervalDates[0] + " to "
                            + intervalDates[1];
                        Dom.get("publish-date-range").style.visibility = "visible"; 
                        //
		                CF.settings['filter_type'] = oMenuItem.value;
		                CF.settings['filter_input'] = intervalDates;
                        CF.fireDT(false);
		            });
                } else if (oMenuItem.value == 'topic') {
                    CF.settings['filter_type'] = 'topic';
                    Dom.get("myInput").style.visibility = "visible";
                } else {
                    CF.cleanUpSingleDateSelection();
                    CF.cleanUpRangeDateSelection();
                    var sUrl = '/admin/smartlist/assets/load_filterby_menu.php';
                    var postData = "&action=filterby&filterby=" + oMenuItem.value;
                    var request = Connect.asyncRequest('POST', sUrl, loadSubmenuHandler, postData);
                }
            },


            //
            onFilterSelectedMenuItemChange: function (e) {
                var oMenuItem = e.newValue;
                this.set("label", ("<em class=\"yui-button-label\">" +
                    oMenuItem.cfg.getProperty("text") + "<\/em>"));
                CF.settings[this.get('id')] = oMenuItem.value;
                CF.fireDT(false);
            },


    		/**
    		 * This method is passed into the DataTable's "generateRequest" configuration
    		 * setting overriding the default generateRequest function. This function puts
    		 * together a query string which is passed to the DataSource each time a new
    		 * set of data is requested. All of the custom sorting and filtering options
    		 * added in by this script are gathered up here and inserted into the
    		 * query string.
    		 * @param {Object} oState
    		 * @param {Object} oSelf
    		 * These parameters are explained in detail in DataTable's API
    		 * documentation. It's important to note that oState contains
    		 * a reference to the paginator and the pagination state and
    		 * the column sorting state as well.
    		 */
    		requestBuilder: function (oState, oSelf) {
    			/* We aren't initializing sort and dir variables. If you are
    			using column sorting built into the DataTable, use this
    			set of variable initializers.
    			var sort, dir, startIndex, results; */
    			var startIndex, results;

    			oState = oState || {pagination: null, sortedBy: null};

    			/* If using column sorting built into DataTable, these next two lines
    			will properly set the current _sortedBy_ column and the _sortDirection_
    			sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
    			dir = (oState.sortedBy && oState.sortedBy.dir === DataTable.CLASS_DESC) ? "desc" : "asc"; */
    			startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
    			results = (oState.pagination) ? oState.pagination.rowsPerPage : null;

                params = "&results=" + results +
    					"&startIndex=" + startIndex +
    				    "&query=" + Dom.get('dt_input').value +
    				    "&publication=" + CF.settings.publication +
                        "&filter_type=" + CF.settings.filter_type;
                if (Dom.inDocument('filter_input') || CF.settings.filter_input) {
                    params += "&filter_input=" + CF.settings.filter_input;
                }
                if (Dom.inDocument('issue')) {
                    params += "&issue=" + CF.settings.issue;
                }
                if (Dom.inDocument('section')) {
                    params += "&section=" + CF.settings.section;
                }
                //if (Dom.inDocument('myHidden')) {
                    //params += "&topic=" + Dom.get('myHidden').value;
                //}

    			return params;
    		},

    		/**
    		 * This method is used to fire off a request for new data for the
    		 * DataTable from the DataSource. The new state of the DataTable,
    		 * after the request for new data, will be determined here.
    		 * @param {Boolean} resetRecordOffset
    		 */
    		fireDT: function (resetRecordOffset) {
                var oState = CF.myDataTable.getState(),
                	request,
                	oCallback;

    			/* We don't always want to reset the recordOffset.
    			If we want the Paginator to be set to the first page,
    			pass in a value of true to this method. Otherwise, pass in
    			false or anything falsy and the paginator will remain at the
    			page it was set at before.*/
                if (resetRecordOffset) {
                	oState.pagination.recordOffset = 0;
                }

    			/* If the column sort direction needs to be updated, that may be done here.
    			It is beyond the scope of this example, but the DataTable::sortColumn() method
    			has code that can be used with some modification. */

    			/*
    			This example uses onDataReturnSetRows because that method
    			will clear out the old data in the DataTable, making way for
    			the new data.*/
    			oCallback = {
    			    success : CF.myDataTable.onDataReturnSetRows,
    			    failure : CF.myDataTable.onDataReturnSetRows,
                    argument : oState,
    			    scope : CF.myDataTable
    			};

    			// Generate a query string
                request = CF.myDataTable.get("generateRequest")(oState, CF.myDataTable);

    			// Fire off a request for new data.
    			CF.myDataSource.sendRequest(request, oCallback);
    		},
    	};

    	CF.init();

        <?php
            $topicsListTxt = '';
            $maxBranchLength = 0;
            foreach ($allTopics as $topicBranch) {
                $x = 1;
                $topicAutocompleteName = '';
                foreach ($topicBranch as $topicId => $topicName) {
                    $topicAutocompleteName .= ', tname'.$x++.':"'.trim($topicName->getName($f_language_id)).'"';
                }
                $x = $x - 1;
                if ($x > $maxBranchLength) {
                    $maxBranchLength = $x;
                }
                $topicsListTxt .= '{id:'.$topicId.$topicAutocompleteName."},\n";
            }

            //
            $tnameFields = '';
            $ifTopicMatches = "if (";
            $oResultDataTNameVars = '';
            $matchIndexTNameVars = '';
            $displayTNameVars = '';
            $ifMatchIndex = '';
            $displayTName = '';
            $buildDisplayTName = '';
            for ($i = 1; $i <= $maxBranchLength; $i++) {
                $tnameFields .= '"tname' . $i . '", ';
                $ifTopicMatches .= '(topic.tname'.$i." && (topic.tname".$i.".toLowerCase().indexOf(query) > -1)) || ";
                $oResultDataTNameVars .= 'tname' . $i . ' = oResultData.tname' . $i . ' || "", ';
                $matchIndexTNameVars .= 'tname' . $i . 'MatchIndex = tname' . $i . '.toLowerCase().indexOf(query), ';
                $displayTNameVars .= 'displaytname' . $i .', ';
                $ifMatchIndex .= 'if (tname' . $i . "MatchIndex > -1) {\n"
                    . 'displaytname' . $i . ' = highlightMatch(tname' . $i . ', query, tname' . $i . "MatchIndex);\n"
                    . "} else {\n"
                    . 'displaytname' . $i . ' = tname' . $i . ' ? tname' . $i . " : \"\";\n"
                    //. 'displaytname' . $i . ' = tname' . $i . ";\n"
                    . "}\n";
                $displayTName .= 'displaytname' . $i . ' + " &raquo; " + ';
                $buildDisplayTName .= 'if (displaytname'.$i.") {\n";
                $oDataTNameBuilder .= 'if (oData.tname'.$i.") {\n";
                if ($i == 1) {
                    $buildDisplayTName .= "\t\tdisplaytname = displaytname + displaytname".$i."\n";
                    $oDataTNameBuilder .= "\t\tdisplaytname = displaytname + oData.tname".$i."\n";
                } else {
                    $buildDisplayTName .= "\t\tdisplaytname = displaytname + ' &raquo; ' + displaytname".$i."\n";
                    $oDataTNameBuilder .= "\t\tdisplaytname = displaytname + ' &#187; ' + oData.tname".$i."\n";
                }
                $buildDisplayTName .= "\t}\n";
                $oDataTNameBuilder .= "\t}\n";
            }
            $tnameFields = rtrim($tnameFields, ', ');
            $ifTopicMatches = rtrim($ifTopicMatches, ' || ');
            $oResultDataTNameVars = rtrim($oResultDataTNameVars, ', ');
            $matchIndexTNameVars = rtrim($matchIndexTNameVars, ', ');
            $displayTNameVars = rtrim($displayTNameVars, ', ');
            $displayTName = rtrim($displayTName, ' + " &raquo; " + ');
        ?>

        //
        YAHOO.example.TopicFiltering = function(){
            var topicsList = [
            <?php print($topicsListTxt); ?>
            ];

            // Define a custom search function for the DataSource
            var matchNames = function(sQuery) {
                // Case insensitive matching
                var query = sQuery.toLowerCase(),
                    topic,
                    i=0,
                    l=topicsList.length,
                    matches = [];

                // Match against each name of each topic
                for(; i < l; i++) {
                    topic = topicsList[i];
                    <?php
                    print($ifTopicMatches . ") {\n");
                    ?>
                        matches[matches.length] = topic;
                    }
                }

                return matches;
            };

            // Use a FunctionDataSource
            var oDS = new YAHOO.util.FunctionDataSource(matchNames);
            oDS.responseSchema = {
                fields: ["id", <?php echo $tnameFields; ?>]
            }

            // Instantiate AutoComplete
            var oAC = new AutoComplete("myInput", "myContainer", oDS);
            oAC.useShadow = true;
            oAC.resultTypeList = false;

            // Custom formatter to highlight the matching letters
            oAC.formatResult = function(oResultData, sQuery, sResultMatch) {
                var query = sQuery.toLowerCase(),
                    <?php print($oResultDataTNameVars); ?>,
                    query = sQuery.toLowerCase(),
                    <?php print($matchIndexTNameVars); ?>,
                    <?php print($displayTNameVars); ?>;

                <?php print($ifMatchIndex); ?>

                //var displaytname = <?php print($displayTName); ?>;
                var displaytname = '';
                <?php print($buildDisplayTName); ?>
                return displaytname;
            };

            // Helper function for the formatter
            var highlightMatch = function(full, snippet, matchindex) {
                return full.substring(0, matchindex) +
                    "<span class='match'>" +
                    full.substr(matchindex, snippet.length) +
                    "</span>" +
                    full.substring(matchindex + snippet.length);
            };

            // Define an event handler to populate a hidden form field
            // when an item gets selected and populate the input field
            var myHiddenField = YAHOO.util.Dom.get("myHidden");
            var myHandler = function(sType, aArgs) {
                var myAC = aArgs[0]; // reference back to the AC instance
                var elLI = aArgs[1]; // reference to the selected LI element
                var oData = aArgs[2]; // object literal of selected item's result data

                // update hidden form field with the selected item's ID
                myHiddenField.value = oData.id;

                var displaytname = '';
                <?php print($oDataTNameBuilder); ?>
                myAC.getInputEl().value = decodeURI(displaytname);
                
                CF.settings['filter_input'] = oData.id;
                CF.fireDT(false);
            };
            oAC.itemSelectEvent.subscribe(myHandler);

            return {
                oDS: oDS,
                oAC: oAC
            };
        }();


        //
    	(function() {

    /**
    * IntervalCalendar is an extension of the CalendarGroup designed specifically
    * for the selection of an interval of dates.
    *
    * @namespace YAHOO.example.calendar
    * @module calendar
    * @since 2.5.2
    * @requires yahoo, dom, event, calendar
    */

    /**
    * IntervalCalendar is an extension of the CalendarGroup designed specifically
    * for the selection of an interval of dates, as opposed to a single date or
    * an arbitrary collection of dates.
    * <p>
    * <b>Note:</b> When using IntervalCalendar, dates should not be selected or
    * deselected using the 'selected' configuration property or any of the
    * CalendarGroup select/deselect methods. Doing so will corrupt the internal
    * state of the control. Instead, use the provided methods setInterval and
    * resetInterval.
    * </p>
    * <p>
    * Similarly, when handling select/deselect/etc. events, do not use the
    * dates passed in the arguments to attempt to keep track of the currently
    * selected interval. Instead, use getInterval.
    * </p>
    *
    * @namespace YAHOO.example.calendar
    * @class IntervalCalendar
    * @extends YAHOO.widget.CalendarGroup
    * @constructor
    * @param {String | HTMLElement} container The id of, or reference to, an HTML DIV element which will contain the control.
    * @param {Object} cfg optional The initial configuration options for the control.
    */
    function IntervalCalendar(container, cfg) {
        /**
        * The interval state, which counts the number of interval endpoints that have
        * been selected (0 to 2).
        * 
        * @private
        * @type Number
        */
        this._iState = 0;

        // Must be a multi-select CalendarGroup
        cfg = cfg || {};
        cfg.multi_select = true;

        // Call parent constructor
        IntervalCalendar.superclass.constructor.call(this, container, cfg);

        // Subscribe internal event handlers
        this.beforeSelectEvent.subscribe(this._intervalOnBeforeSelect, this, true);
        this.selectEvent.subscribe(this._intervalOnSelect, this, true);
        this.beforeDeselectEvent.subscribe(this._intervalOnBeforeDeselect, this, true);
        this.deselectEvent.subscribe(this._intervalOnDeselect, this, true);
    }

    /**
    * Default configuration parameters.
    * 
    * @property IntervalCalendar._DEFAULT_CONFIG
    * @final
    * @static
    * @private
    * @type Object
    */
    IntervalCalendar._DEFAULT_CONFIG = YAHOO.widget.CalendarGroup._DEFAULT_CONFIG;

    YAHOO.lang.extend(IntervalCalendar, YAHOO.widget.CalendarGroup, {

        /**
        * Returns a string representation of a date which takes into account
        * relevant localization settings and is suitable for use with
        * YAHOO.widget.CalendarGroup and YAHOO.widget.Calendar methods.
        * 
        * @method _dateString
        * @private
        * @param {Date} d The JavaScript Date object of which to obtain a string representation.
        * @return {String} The string representation of the JavaScript Date object.
        */
        _dateString : function(d) {
            var a = [];
            a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_MONTH_POSITION.key)-1] = (d.getMonth() + 1);
            a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_DAY_POSITION.key)-1] = d.getDate();
            a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_YEAR_POSITION.key)-1] = d.getFullYear();
            var s = this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.DATE_FIELD_DELIMITER.key);
            return a.join(s);
        },

        /**
        * Given a lower and upper date, returns a string representing the interval
        * of dates between and including them, which takes into account relevant
        * localization settings and is suitable for use with
        * YAHOO.widget.CalendarGroup and YAHOO.widget.Calendar methods.
        * <p>
        * <b>Note:</b> No internal checking is done to ensure that the lower date
        * is in fact less than or equal to the upper date.
        * </p>
        * 
        * @method _dateIntervalString
        * @private
        * @param {Date} l The lower date of the interval, as a JavaScript Date object.
        * @param {Date} u The upper date of the interval, as a JavaScript Date object.
        * @return {String} The string representing the interval of dates between and
        *                   including the lower and upper dates.
        */
        _dateIntervalString : function(l, u) {
            var s = this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.DATE_RANGE_DELIMITER.key);
            return (this._dateString(l)
                    + s + this._dateString(u));
        },

        /**
        * Returns the lower and upper dates of the currently selected interval, if an
        * interval is selected.
        * 
        * @method getInterval
        * @return {Array} An empty array if no interval is selected; otherwise an array
        *                 consisting of two JavaScript Date objects, the first being the
        *                 lower date of the interval and the second being the upper date.
        */
        getInterval : function() {
            // Get selected dates
            var dates = this.getSelectedDates();
            if(dates.length > 0) {
                // Return lower and upper date in array
                var l = dates[0];
                var u = dates[dates.length - 1];
                return [l, u];
            }
            else {
                // No dates selected, return empty array
                return [];
            }
        },

        /**
        * Sets the currently selected interval by specifying the lower and upper
        * dates of the interval (in either order).
        * <p>
        * <b>Note:</b> The render method must be called after setting the interval
        * for any changes to be seen.
        * </p>
        * 
        * @method setInterval
        * @param {Date} d1 A JavaScript Date object.
        * @param {Date} d2 A JavaScript Date object.
        */
        setInterval : function(d1, d2) {
            // Determine lower and upper dates
            var b = (d1 <= d2);
            var l = b ? d1 : d2;
            var u = b ? d2 : d1;
            // Update configuration
            this.cfg.setProperty('selected', this._dateIntervalString(l, u), false);
            this._iState = 2;
        },

        /**
        * Resets the currently selected interval.
        * <p>
        * <b>Note:</b> The render method must be called after resetting the interval
        * for any changes to be seen.
        * </p>
        * 
        * @method resetInterval
        */
        resetInterval : function() {
            // Update configuration
            this.cfg.setProperty('selected', [], false);
            this._iState = 0;
        },

        /**
        * Handles beforeSelect event.
        * 
        * @method _intervalOnBeforeSelect
        * @private
        */
        _intervalOnBeforeSelect : function(t,a,o) {
            // Update interval state
            this._iState = (this._iState + 1) % 3;
            if(this._iState == 0) {
                // If starting over with upcoming selection, first deselect all
                this.deselectAll();
                this._iState++;
            }
        },

        /**
        * Handles selectEvent event.
        * 
        * @method _intervalOnSelect
        * @private
        */
        _intervalOnSelect : function(t,a,o) {
            // Get selected dates
            var dates = this.getSelectedDates();
            if(dates.length > 1) {
                /* If more than one date is selected, ensure that the entire interval
                    between and including them is selected */
                var l = dates[0];
                var u = dates[dates.length - 1];
                this.cfg.setProperty('selected', this._dateIntervalString(l, u), false);
            }
            // Render changes
            this.render();
        },

        /**
        * Handles beforeDeselect event.
        * 
        * @method _intervalOnBeforeDeselect
        * @private
        */
        _intervalOnBeforeDeselect : function(t,a,o) {
            if(this._iState != 0) {
                /* If part of an interval is already selected, then swallow up
                    this event because it is superfluous (see _intervalOnDeselect) */
                return false;
            }
        },

        /**
        * Handles deselectEvent event.
        *
        * @method _intervalOnDeselect
        * @private
        */
        _intervalOnDeselect : function(t,a,o) {
            if(this._iState != 0) {
                // If part of an interval is already selected, then first deselect all
                this._iState = 0;
                this.deselectAll();

                // Get individual date deselected and page containing it
                var d = a[0][0];
                var date = YAHOO.widget.DateMath.getDate(d[0], d[1] - 1, d[2]);
                var page = this.getCalendarPage(date);
                if(page) {
                    // Now (re)select the individual date
                    page.beforeSelectEvent.fire();
                    this.cfg.setProperty('selected', this._dateString(date), false);
                    page.selectEvent.fire([d]);
                }
                // Swallow up since we called deselectAll above
                return false;
            }
        }
    });

    YAHOO.namespace("example.calendar");
    YAHOO.example.calendar.IntervalCalendar = IntervalCalendar;
})();

YAHOO.util.Event.onDOMReady(function() {
    var dateTxt = Dom.get("dateTxt"),
        inTxt = YAHOO.util.Dom.get("in"),
        outTxt = YAHOO.util.Dom.get("out"),
        inDate, outDate, interval, today;

    today = new Date();

    dateTxt.value = "";
    inTxt.value = "";
    outTxt.value = "";

    RangeDateCalendar = new YAHOO.example.calendar.IntervalCalendar("cal1Container",
        {pages:2,
         pagedate:today.getMonth() + "/" + today.getFullYear(),
         maxdate:(today.getMonth()+1) + "/" + today.getDate() + "/" + today.getFullYear(),
         title:"Please select a range:",
         close:true});

    RangeDateCalendar.selectEvent.subscribe(function() {
        function PopupDateCalendarClose() {
            RangeDateCalendar.hide();
        }

        interval = this.getInterval();
        if (interval.length == 2) {
            inDate = interval[0];
            inTxt.value = (inDate.getMonth() + 1) + "/" + inDate.getDate() + "/" + inDate.getFullYear();

            if (interval[0].getTime() != interval[1].getTime()) {
                outDate = interval[1];
                outTxt.value = (outDate.getMonth() + 1) + "/" + outDate.getDate() + "/" + outDate.getFullYear();
                setTimeout(PopupDateCalendarClose, 1000);
            } else {
               outTxt.value = "";
            }
        }
    }, RangeDateCalendar, true);
    
    RangeDateCalendar.render();
    Event.addListener("publish-date-range", "click", RangeDateCalendar.show, RangeDateCalendar, true);

    SingleDateCalendar = new YAHOO.widget.Calendar("cal2","cal2Container",
        { title:"Choose a date:",
          maxdate:(today.getMonth()+1) + "/" + today.getDate() + "/" + today.getFullYear(),
          close:true } );

    SingleDateCalendar.selectEvent.subscribe(function() {
        function PopupDateCalendarClose() {
            SingleDateCalendar.hide();
        }
        var selDate;
        if (SingleDateCalendar.getSelectedDates().length > 0) {
            selDate = SingleDateCalendar.getSelectedDates()[0];
        } else {
            return;
        }
        selDate = selDate.getFullYear() + "-" + (selDate.getMonth() + 1) + "-" + selDate.getDate();
        dateTxt.value = selDate;

        setTimeout(PopupDateCalendarClose, 1000);
    }, SingleDateCalendar, true);

    SingleDateCalendar.render();

    Event.addListener("publish-date-single", "click", SingleDateCalendar.show, SingleDateCalendar, true);
    });
}});
</script>
<?php camp_html_copyright_notice(); ?>