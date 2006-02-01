<?

/**
 * The frame that contains the image to be edited.
 * @author $Author: paul $
 * @version $Id: editorFrame.php,v 1.1 2005/05/03 15:28:07 paul Exp $
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('Classes/ImageManager.php');
require_once('Classes/ImageEditor.php');

$manager = new ImageManager($IMConfig);
$editor = new ImageEditor($manager);
$imageInfo = $editor->processImage();

?>

<html>
<head>
	<title></title>
<script type="text/javascript">
_backend_url = "<? print $IMConfig['backend_url']; ?>";
</script>

<link href="<? print $IMConfig['base_url'];?>assets/editorFrame.css" rel="stylesheet" type="text/css" />	
<script type="text/javascript" src="<? print $IMConfig['base_url'];?>assets/wz_jsgraphics.js"></script>
<script type="text/javascript" src="<? print $IMConfig['base_url'];?>assets/EditorContent.js"></script>
<script type="text/javascript">

if(window.top)
	HTMLArea = window.top.HTMLArea;

function i18n(str) {
    return HTMLArea._lc(str, 'ImageManager');
};
	
	var mode = "<? echo $editor->getAction(); ?>" //crop, scale, measure

var currentImageFile = "<? if(count($imageInfo)>0) echo rawurlencode($imageInfo['file']); ?>";

<? if ($editor->isFileSaved() == 1) { ?>
	alert(i18n('File saved.'));
<? } else if ($editor->isFileSaved() == -1) { ?>
	alert(i18n('File was not saved.'));
<? } ?>

</script>
<script type="text/javascript" src="<? print $IMConfig['base_url'];?>assets/editorFrame.js"></script>
</head>

<body>
<div id="status"></div>
<div id="ant" class="selection" style="visibility:hidden"><img src="<? print $IMConfig['base_url'];?>img/spacer.gif" width="0" height="0" border="0" alt="" id="cropContent"></div>
<? if ($editor->isGDEditable() == -1) { ?>
	<div style="text-align:center; padding:10px;"><span class="error">GIF format is not supported, image editing not supported.</span></div>
<? } ?>
<table height="100%" width="100%">
	<tr>
		<td>
<? if(count($imageInfo) > 0 && is_file($imageInfo['fullpath'])) { ?>
	<span id="imgCanvas" class="crop"><img src="<? echo $imageInfo['src']; ?>" <? echo $imageInfo['dimensions']; ?> alt="" id="theImage" name="theImage"></span>
<? } else { ?>
	<span class="error">No Image Available</span>
<? } ?>
		</td>
	</tr>
</table>
</body>
</html>
