// Table Operations Plugin for HTMLArea-3.0
// Implementation by Nazarij Dubnytskyj.  Sponsored by NasCreative
//
// htmlArea v3.0 - Copyright (c) 2002 interactivetools.com, inc.
// This notice MUST stay intact for use (see license.txt).
//
// A free WYSIWYG editor replacement for <textarea> fields.
// For full source code and docs, visit http://www.interactivetools.com/
//
// Version 1.0 developed by Nazarij Dubnytskyj for NasCreative.
//
// $Id$

// Object that will encapsulate all the equation operations
function Equation(editor) {
	this.editor = editor;

	var cfg = editor.config;
	var self = this;

	// register the toolbar buttons provided by this plugin
	cfg.registerButton({
	id       : "equation",
	tooltip  : this._lc("Equation Editor"),
	image    : editor.imgURL("equation.gif", "Equation"),
	textMode : false,
	action   : function(editor, id) {
			self.buttonPress(editor, id);
		}
	})
	 cfg.addToolbarElement("equation", "inserthorizontalrule", -1);
};

Equation._pluginInfo = {
	name          : "Equation",
	version       : "1.0",
	developer     : "Nazarij Dubnytskyj",
	developer_url : "",
	c_owner       : "",
	sponsor       : "Nascreative",
	sponsor_url   : "",
	license       : "htmlArea"
};

Equation.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'Equation');
};

Equation.prototype.buttonPress = function(editor, id) {
	var self = this;
	this.editor = editor;

	switch (id) {
		case "equation":
			editor._popupDialog("plugin://Equation/operations", function(params) {
				self.insertOperation(editor,params);
			}, '');
		break;
	}
};

Equation.prototype.insertOperation = function(editor,params) {
	var cur_operation=params["op"];

	this.editor = editor;

	switch (cur_operation) {
		case "less_equal":
			editor.insertHTML('<img src="../plugins/Equation/img/dsp_less_equal.gif" style="position:relative; top:4px;">');
		break;
		case "greater_egual":
			editor.insertHTML('<img src="../plugins/Equation/img/dsp_greater_equal.gif" style="position:relative; top:4px;">');
		break;
		case "notequal":
			editor.insertHTML('<img src="../plugins/Equation/img/dsp_notequal.gif" style="position:relative; top:4px;">');
		break;
		case "mul":
			editor.insertHTML('&nbsp;*&nbsp;');
		break;
		case "divide":
			editor.insertHTML(' &divide; ');
		break;
		case "abs_value":
			tstr='<table taglabel="ABS" style="display: inline; vertical-align: middle;" border="0" cellpadding="0" cellspacing="0">';
			tstr+='<tbody>';
			tstr+=' <tr>';
			tstr+='   <td style="font-size: 16px; font-family: times new roman,times,serif;"  type="paren" autosize="absVal" noresize="1" nowrap="nowrap" valign="middle"><b>|</b></td>';
			tstr+='   <td  nowrap="nowrap" valign="bottom"><table  style="display: inline;" border="0" cellpadding="0" cellspacing="0"><tbody ><tr ><td taglabel="CONTENTS" style="vertical-align: middle; padding-top: 0px; text-align: center;" nowrap="nowrap">x</td></tr></tbody></table></td>';
			tstr+='   <td style="font-size: 16px; font-family: times new roman,times,serif;"  type="paren" autosize="absVal" noresize="1" nowrap="nowrap" valign="middle"><b>|</b></td>';
			tstr+=' </tr>';
			tstr+='</tbody></table>';
			editor.insertHTML(tstr);
		break;
		case "parenthesis":
			tstr='<table taglabel="PARENTHESIS" style="display: inline; vertical-align: middle;" border="0" cellpadding="0" cellspacing="0">';
			tstr+='<tbody>';
			tstr+=' <tr>';
			tstr+='   <td style="font-family: times new roman,times,serif; font-size: 32px;" type="paren" autosize="paren" noresize="1" nowrap="nowrap" valign="middle">(</td>';
			tstr+='   <td nowrap="nowrap" valign="middle">x</td>';
			tstr+='   <td style="font-family: times new roman,times,serif; font-size: 32px;" type="paren" autosize="paren" noresize="1" nowrap="nowrap" valign="middle">)</td>';
			tstr+=' </tr>';
			tstr+='</tbody></table>';
			editor.insertHTML(tstr);
		break;
		case "hor_fraction":
			tstr='<table cellpadding="3" cellspacing="0" style="float:left;" taglabel="FRACTION">';
			tstr+='<tr><td align="center" style="border-bottom:1px solid #000;" type="numerator"> x </td></tr>';
			tstr+='<tr><td align="center" type="denominator"> y </td></tr>';
			tstr+='</table>';
			tstr+='<div style="margin:15px 5px 0px 5px;float:left;"> &nbsp; &nbsp; </div>';
			tstr+='<div style="clear:both;"></div><br /><br />';
			editor.insertHTML(tstr);
		break;
		case "diag_fraction":
			tstr='<table taglabel="ABS" style="display: inline; vertical-align: middle;" border="0" cellpadding="0" cellspacing="0">';
			tstr+='<tbody>';
			tstr+=' <tr>';
			tstr+='   <td style="font-size: 16px; font-family: times new roman,times,serif;" type="paren" autosize="diag_fraction" noresize="1" nowrap="nowrap" valign="middle">x</td>';
			tstr+='   <td nowrap="nowrap" valign="bottom"><table  style="display: inline;" border="0" cellpadding="0" cellspacing="0"><tbody ><tr ><td taglabel="CONTENTS" style="vertical-align: middle; padding-top: 0px; text-align: center; font-size: 24px; font-weight:900;" nowrap="nowrap">&nbsp;/&nbsp;</td></tr></tbody></table></td>';
			tstr+='   <td style="font-size: 16px; font-family: times new roman,times,serif;" type="paren" autosize="diag_fraction" noresize="1" nowrap="nowrap" valign="middle">y</td>';
			tstr+=' </tr>';
			tstr+='</tbody></table>';
			editor.insertHTML(tstr);
		break;
		case "square_root":
			tstr='<table style="display: inline; vertical-align: middle;" taglabel="RADICAL" border="0" cellpadding="0" cellspacing="0">';
			tstr+='<tbody>';
			tstr+=' <tr>';
			tstr+='   <td style="padding: 0px 0px 0px 2px; font-family: times new roman,times,serif; font-size: 8pt;" align="right" nowrap="nowrap" valign="bottom">&nbsp;<br><strong style="font-weight: 900; font-family: times new roman,times,serif;">\\</strong></td>';
			tstr+='   <td style="border-top: 2px solid black; border-left: 2px solid black; padding: 2px 3px 1px 5px;" align="center" nowrap="nowrap">&nbsp;x</td>';
			tstr+=' </tr>';
			tstr+='</tbody></table>';
			editor.insertHTML(tstr);
		break;
		case "root":
			tstr='<table style="display: inline; vertical-align: middle;" taglabel="RADICAL" border="0" cellpadding="0" cellspacing="0">';
			tstr+='<tbody>';
			tstr+=' <tr>';
			tstr+='   <td style="padding: 0px 0px 0px 2px; font-family: times new roman,times,serif; font-size: 8pt;" align="right" nowrap="nowrap" valign="bottom">y&nbsp;<br><strong style="font-weight: 900; font-family: times new roman,times,serif;">\\</strong></td>';
			tstr+='   <td style="border-top: 2px solid black; border-left: 2px solid black; padding: 2px 3px 1px 5px;" align="center" nowrap="nowrap">&nbsp;x</td>';
			tstr+=' </tr>';
			tstr+='</tbody></table>';
			editor.insertHTML(tstr);
		break;
	}
};