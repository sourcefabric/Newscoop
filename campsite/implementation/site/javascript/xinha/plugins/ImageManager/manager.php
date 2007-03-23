<?php
/**
 * The main GUI for the ImageManager.
 * @author $Author$
 * @version $Id$
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('Classes/ImageManager.php');

$manager = new ImageManager($IMConfig);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 	<link href="assets/manager.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../popups/popup.js"></script>
	<script type="text/javascript" src="assets/dialog.js"></script>
	<script type="text/javascript">
	/*<![CDATA[*/
		window.resizeTo(600, 460);
		if(window.opener) {
			HTMLArea = window.opener.HTMLArea;
		}
	/*]]>*/
	</script>
	<script type="text/javascript" src="assets/manager.js"></script>
	<title>Insert Image</title>
</head>
<body>
	<div class="title">Insert Image</div>
	<form action="images.php" id="uploadForm" method="post" enctype="multipart/form-data">
	<fieldset>
	<div class="dirs">
		<iframe src="images.php?article_id=<?php echo $_REQUEST['article_id']; ?>" name="imgManager" id="imgManager" class="imageFrame" scrolling="auto" title="Image Selection" frameborder="0"></iframe>
	</div>
	</fieldset>
	<!-- image properties -->
		<table class="inputTable">
			<input type="hidden" id="f_image_template_id" value="" />
			<input type="hidden" id="f_url" value="" />
			<input type="hidden" id="f_vert" value="" />
			<input type="hidden" id="f_horiz" value="" />
			<input type="hidden" id="f_border" value="" />
			<input type="hidden" id="f_width" value="" />
			<input type="hidden" id="f_height" value="" />
			<input type="hidden" id="orginal_width" />
			<input type="hidden" id="orginal_height" />
			<tr>
				<td align="right"><label for="f_alt">Alt</label></td>
				<td><input type="text" id="f_alt" class="largelWidth" value="" /></td>
			</tr>
			<tr>
				<td align="right"><label for="f_caption">Caption</label></td>
				<td><input type="text" id="f_caption" class="largelWidth" value="" /></td>
			</tr>
			<tr>
				<td align="right"><label for="f_align">Alignment:</label></td>
				<td>
					<select size="1" id="f_align"  title="Positioning of this image">
						<option value="none">Not set</option>
					  	<option value="left">Left</option>
					  	<option value="right">Right</option>
					  	<option value="middle">Middle</option>
	<!--				<option value="texttop"                      >Texttop</option>
					  	<option value="absmiddle"                    >Absmiddle</option>
					  	<option value="baseline" selected="selected" >Baseline</option>
					  	<option value="absbottom"                    >Absbottom</option>
					  	<option value="bottom"                       >Bottom</option>
					  	<option value="top"                          >Top</option>-->
					</select>
				</td>
			</tr>
		</table>
	<!--// image properties -->
		<div style="text-align: right;">
	          <hr />
	          <button type="button" class="buttons" onclick="return onOK();">OK</button>
	          <button type="button" class="buttons" onclick="return onCancel();">Cancel</button>
	    </div>
	</form>
</body>
</html>