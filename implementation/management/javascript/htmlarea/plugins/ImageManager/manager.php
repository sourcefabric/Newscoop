<?php
/**
 * The main GUI for the ImageManager.
 * @author $Author: paul $
 * @version $Id: manager.php,v 1.5 2005/04/20 13:58:39 paul Exp $
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('Classes/ImageManager.php');

$manager = new ImageManager($IMConfig);
//$dirs = $manager->getDirs();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 	<link href="assets/manager.css" rel="stylesheet" type="text/css" />	
	<script type="text/javascript" src="assets/popup.js"></script>
	<script type="text/javascript" src="assets/dialog.js"></script>
	<script type="text/javascript">
	/*<![CDATA[*/
		window.resizeTo(600, 460);
	
		if (window.opener) {
			I18N = window.opener.ImageManager.I18N;
		}
	
		//var thumbdir = "<?php echo $IMConfig['thumbnail_dir']; ?>";
		//var base_url = "<?php echo $manager->getBaseURL(); ?>";
	/*]]>*/
	</script>
	<script type="text/javascript" src="assets/manager.js"></script>
	<script>document.write("<title>"+i18n("Insert Image")+"</title>");</script>
</head>
<body>
	<div class="title"><script>document.write(i18n("Insert Image"));</script></div>
	<form action="images.php" id="uploadForm" method="post" enctype="multipart/form-data">
	<fieldset><!--<legend><script>document.write(i18n("Image Manager"));</script></legend>-->
	<div class="dirs">
		<iframe src="images.php?article_id=<?php echo $_REQUEST['article_id']; ?>" name="imgManager" id="imgManager" class="imageFrame" scrolling="auto" title="Image Selection" frameborder="0"></iframe>
	</div>
	</fieldset>
	<!-- image properties -->
		<table class="inputTable">
			<input type="hidden" id="f_url" value="" />
			<input type="hidden" id="f_vert" value="" />
			<input type="hidden" id="f_horiz" value="" />
			<input type="hidden" id="f_border" value="" />
			<input type="hidden" id="f_width" value="" />
			<input type="hidden" id="f_height" value="" />
			<input type="hidden" id="orginal_width" />
			<input type="hidden" id="orginal_height" />
			<tr>
				<td align="right"><label for="f_alt"><script>document.write(i18n("Alt"));</script></label></td>
				<td><input type="text" id="f_alt" class="largelWidth" value="" /></td>
			</tr>		
			<tr>
				<td align="right"><label for="f_caption"><script>document.write(i18n("Caption"));</script></label></td>
				<td><input type="text" id="f_caption" class="largelWidth" value="" /></td>
			</tr>		
			<tr>
				<td align="right"><label for="f_align"><script>document.write(i18n("Align"));</script></label></td>
				<td>
					<select size="1" id="f_align"  title="Positioning of this image">
						<option value="none"><script>document.write(i18n("Not Set"));</script></option>
					  	<option value="left"><script>document.write(i18n("Left"));</script></option>
					  	<option value="right"><script>document.write(i18n("Right"));</script></option>
					  	<option value="middle"><script>document.write(i18n("Middle"));</script></option>
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
			  <!--<button type="button" class="buttons" onclick="return refresh();">Refresh</button>-->
	          <button type="button" class="buttons" onclick="return onOK();"><script>document.write(i18n("OK"));</script></button>
	          <button type="button" class="buttons" onclick="return onCancel();"><script>document.write(i18n("Cancel"));</script></button>
	    </div>
	</form>
</body>
</html>