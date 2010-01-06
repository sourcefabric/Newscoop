<?php
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");


$sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
if (empty($sessid)) {
    // Error
}

$xrc =& XR_CcClient::Factory($mdefs, true);
$resp = $xrc->ping($sessid);
if (PEAR::isError($resp)) {
    switch ($resp->getCode()) {
        case '805':
            camp_html_goto_page('campcaster_login.php');
            break;
        case '804':
        default:
            camp_html_add_msg(getGS("Unable to reach the storage server."));
            break;
    }
}

require_once($GLOBALS['g_campsiteDir']."/classes/Archive_FileBase.php");

$criteria = array('filetype' => 'all',
                  'operator' => 'and',
                  'limit' => 0,
                  'offset' => 0,
                  'orderby' => 'dc:title',
                  'desc' => false,
                  'conditions' => array()
                 );
$result = Archive_FileBase::SearchFiles($criteria);
if (PEAR::isError($result)) {
    $files = array();
    $filesCount = 0;
    // error
} else {
    $files = $result[1];
    $filesCount = $result[0];
}

$crumbs = array();
$crumbs[] = array(getGS("Content"), "/$ADMIN/filearchive/");
$crumbs[] = array(getGS("File Archive"), "");
echo camp_html_breadcrumbs($crumbs);
?>
<link rel="stylesheet" type="text/css" href="/javascript/yui/build/paginator/assets/skins/sam/paginator.css" />
<link rel="stylesheet" type="text/css" href="/javascript/yui/build/datatable/assets/skins/sam/datatable.css" />

<script type="text/javascript" src="/javascript/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="/javascript/yui/build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/animation/animation-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/paginator/paginator-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/datatable/datatable-min.js"></script>

<p>
<table cellpadding="0" cellspacing="0" class="action_buttons" style="padding-bottom: 5px;">
<tr>
<?php
// TODO: add proper right
if ($g_user->hasPermission('AddImage')) { ?>
  <td>
    <a href="file.php"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0" alt="<?php putGS('Add new file'); ?>" /></a>
  </td>
  <td style="padding-left: 3px;">
    <a href="file.php"><b><?php putGS('Add new file'); ?></b></a>
  </td>
<?php } ?>
</tr>
</table>

<div id="datatable_paginator"></div>
<div id="fileindex"></div>

<!-- YUI Code -->
<script type="text/javascript">
YAHOO.namespace("camp");

// Generates data hash
YAHOO.camp.Data = {
  files: [
  <?php
  foreach ($files as $file) {
  ?>
    {filecheck:"<?php echo $file->getGunId(); ?>", fileicon:"<?php echo $file->getType(); ?>", filename:"<?php echo htmlspecialchars($file->getMetatagValue('title').'_'.$file->getGunId()); ?>", filesize:"<?php echo htmlspecialchars(camp_format_bytes($file->getMetatagValue('filesize'))); ?>", filetype:"<?php echo htmlspecialchars($file->getMetatagValue('format')); ?>", filedate:"<?php echo htmlspecialchars($file->getModifiedTime()); ?>"},
  <?php
  }
  ?>
  ]
};

YAHOO.util.Event.addListener(window, "load", function() {

  YAHOO.camp.FileArchiveDataTable = function() {
    // Extend YUI DataTable which is missing a selectAllRows method
    YAHOO.lang.augmentObject(
      YAHOO.widget.DataTable.prototype, {

        _selectAllTrEls : function() {
          var selectedRowsEven = YAHOO.util.Dom.getElementsByClassName(YAHOO.widget.DataTable.CLASS_EVEN, "tr",this._elTbody);
          YAHOO.util.Dom.addClass( selectedRowsEven , YAHOO.widget.DataTable.CLASS_SELECTED);

          var selectedRowsOdd = YAHOO.util.Dom.getElementsByClassName(YAHOO.widget.DataTable.CLASS_ODD, "tr",this._elTbody);
          YAHOO.util.Dom.addClass( selectedRowsOdd, YAHOO.widget.DataTable.CLASS_SELECTED);
        },

        /* Selects all rows. * * @method selectAllRows */
        selectAllRows : function() {
          // Remove all rows from tracker
          var tracker = this._aSelections || [];
          for(var j=tracker.length- 1; j>-1; j--) {
            if(YAHOO.lang.isString( tracker[j] )){
              tracker.splice( j,1);
            }
          }
          // Update tracker
          this._aSelections = tracker;
          // Update UI
          this._selectAllTrEls();
          // Get all highlighted rows and make yahoo aware they are selected
          var selectedRowsEven = YAHOO.util.Dom.getElementsByClassName(YAHOO.widget.DataTable.CLASS_SELECTED, "tr",this._elTbody);
          for (i=0;i<selectedRowsEven.length; i++){
            this.selectRow(i);
          }
        }
      }
    );
    // End YUI Datatable extension

    // Keep record of the checkbox states to handle column sorting
    var checked = [];

    // Override the built-in formatter
    YAHOO.widget.DataTable.formatLink = function(elLiner, oRecord, oColumn, oData) {
      var file = oData.substring(0, oData.lastIndexOf('_'));
      var gunid = oData.substring(oData.lastIndexOf('_')+1);
      elLiner.innerHTML = "<a href=\"edit.php?gunid=" + gunid + "\">" + file + "</a>";
    };

    YAHOO.widget.DataTable.Formatter.check = function (elLiner, oRecord, oColumn, oData) {
      elLiner.innerHTML = '<input type="checkbox" name="filerow" value="'+ oData + '"' + (checked[oData] ? ' checked="checked">' : '>');
    };

    YAHOO.widget.DataTable.Formatter.icon = function (elLiner, oRecord, oColumn, oData) {
      elLiner.innerHTML = '<img src="/css/filearchive_' + oData + '.png" />';
    };

    // Columns definition
    var myColumnDefs = [
      {key:"filecheck",label:"<input id=\"chkall\" name=\"chkall\" value=\"\" type=\"checkbox\">",formatter:"check"},
      {key:"fileicon", label:"",width:"auto",formatter:"icon"},
      {key:"filename",label:"<?php putGS('Name'); ?>",width:300,resizeable:true,sortable:true,formatter:YAHOO.widget.DataTable.formatLink},
      {key:"filesize",label:"<?php putGS('Size'); ?>",width:"auto",sortable:true},
      {key:"filetype",label:"<?php putGS('Type'); ?>",width:"auto",sortable:true},
      {key:"filedate",label:"<?php putGS('Date Modified'); ?>",width:"auto",sortable:true},
      {key:"fileiuse",label:"<?php putGS('In Use'); ?>"},
      {key:"filedele",label:"<?php putGS('Delete'); ?>"},
    ];

    // Data source definition
    var myDataSource = new YAHOO.util.DataSource(YAHOO.camp.Data.files);
      myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
      myDataSource.responseSchema = {
      fields: [
        {key:"filecheck"},
        {key:"fileicon", parser:"string"},
        {key:"filename", parser:"string"},
        {key:"filesize", parser:"string"},
        {key:"filetype", parser:"string"},
        {key:"filedate", parser:"string"}
      ]
    };

    // Data table configuration
    var myConfigs = {
      sortedBy:{key:"filename",dir:"asc"},
      paginator: new YAHOO.widget.Paginator({
        rowsPerPage: 25,
        totalRecords: myDataSource.length,
        containers: ['datatable_paginator'],
        template: "{CurrentPageReport} {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} {RowsPerPageDropdown}",
        pageReportTemplate: "<strong>{startRecord}</strong> - <strong>{endRecord}</strong> of <strong>{totalRecords}</strong>",
        rowsPerPageOptions: [10,25,50],
        pageLinks: 5
      }),
      draggableColumns:false
    }

    var myDataTable = new YAHOO.widget.DataTable("fileindex", myColumnDefs, myDataSource, myConfigs);

    // Enable row highlighting
    myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
    myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);
    myDataTable.subscribe("initEvent", function() {
      var chkall = YAHOO.util.Dom.get('chkall');
      if (chkall) {
        YAHOO.util.Event.on(chkall,'click',function (e) {
          var checks = document.getElementsByName('filerow');
          var i = 0, l = checks.length;
          for (;i<l;++i) {
            checked[i] = checks[i].checked = this.checked;
          }
          if (this.checked) {
            myDataTable.selectAllRows();
          } else {
            myDataTable.unselectAllRows();
          }
        });
      }
    });

    myDataTable.subscribe("checkboxClickEvent", function(e) {
      var id = parseInt(e.target.value, 10);
      checked[id] = e.target.checked;
      if (!e.target.checked) {
        YAHOO.util.Dom.get('chkall').checked = false;
      }
    });

    // Enable row selection
    myDataTable.subscribe("rowClickEvent",
      function(ev) {
        var target = YAHOO.util.Event.getTarget(ev);
        // Unselect row
        if (myDataTable.isSelected(target)) {
          myDataTable.unselectRow(target);
        }
        // Select row
        else {
          myDataTable.selectRow(target);
        }
        myDataTable.checkboxClickEvent.fire;
      }
    );

    return {
      oDS: myDataSource,
      oDT: myDataTable
    };
  }();
});
</script>
<!-- End YUI Code -->
<?php camp_html_copyright_notice(); ?>