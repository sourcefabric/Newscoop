<?php
/**
 * Show a list of images in a long horizontal table.
 * @author $Author: holman $
 * @package ImageManager
 */
$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once($GLOBALS['g_campsiteDir'].'/conf/liveuser_configuration.php');

// Only logged in admin users allowed
if (!$LiveUser->isLoggedIn()) {
    header("Location: /$ADMIN/login.php");
    exit(0);
} else {
    $userId = $LiveUser->getProperty('auth_user_id');
    $userTmp = new User($userId);
    if (!$userTmp->exists() || !$userTmp->isAdmin()) {
        header("Location: /$ADMIN/login.php");
        exit(0);
    }
    unset($userTmp);
}

require_once('config.inc.php');
require_once('classes/ImageManager.php');

$Campsite['IMAGE_BASE_URL'] = str_replace('/javascript/tinymce/plugins/campsiteimage', '', $Campsite['SUBDIR']) . '/images/';
$Campsite['THUMBNAIL_BASE_URL'] = $Campsite['IMAGE_BASE_URL'] . 'thumbnails/';

//default path is /
$relative = '/';
$manager = new ImageManager($IMConfig);
$refreshDir = false;

// Check for any sub-directory request.
// Check that the requested sub-directory exists and valid.
if (isset($_REQUEST['dir'])) {
	$path = rawurldecode($_REQUEST['dir']);
	if ($manager->validRelativePath($path)) {
		$relative = $path;
	}
}

$manager = new ImageManager($IMConfig);
$articleId = (isset($_REQUEST['article_id']) && is_numeric($_REQUEST['article_id']))
    ? $_REQUEST['article_id'] : null;

// Get the list of files and directories
$list = array();
if (!is_null($articleId)) {
    $list = $manager->getFiles($relative, $articleId);
}

if (isset($_REQUEST['image_id'])) {
    $image = $manager->getImageByNumber($articleId, $_REQUEST['image_id']);
    $imageAltOpt = '';
    $imageTitleOpt = '';
    if (isset($_REQUEST['image_alt'])) {
        $imageAltOpt = $_REQUEST['image_alt'];
    }
    if (isset($_REQUEST['image_title'])) {
        $imageTitleOpt = $_REQUEST['image_title'];
    }
    if (isset($_REQUEST['image_alignment'])) {
        $imageAlignOpt = $_REQUEST['image_alignment'];
    }
    if (isset($_REQUEST['image_ratio'])) {
        $imageRatioOpt = $_REQUEST['image_ratio'];
    }
    if (isset($_REQUEST['image_resize_width'])) {
        $imageResizeWidthOpt = $_REQUEST['image_resize_width'];
    }
    if (isset($_REQUEST['image_resize_height'])) {
        $imageResizeHeightOpt = $_REQUEST['image_resize_height'];
    }
}

/* ================= OUTPUT/DRAW FUNCTIONS ======================= */

/**
 * Draw the files in an table.
 */
function drawFiles($list, &$manager)
{
        global $relative, $image;

	foreach($list as $entry => $file)
	{
	    if (isset($image) && is_object($image)) {
	        if ($image->getImageId() == $file['image_object']->getImageId()) {
?>
		<td>
			<table width="100" cellpadding="0" cellspacing="0">
			<tr>
				<td class="block" id="block_<?php echo $file['template_id']; ?>">
		                        <img src="<?php echo $file['image_object']->getThumbnailUrl(); ?>" alt="<?php echo $file['alt']; ?>"/>
		                </td></tr><tr><td class="edit">
		<?php
		if ($file['image']) {
			echo $file['image'][0].'x'.$file['image'][1];
		}
		else {
			echo " ";
		}
		?>
		                </td>
                        </tr></table></td>
        <?php
			}
		} else {
	?>
		<td>
			<table width="100" cellpadding="0" cellspacing="0">
			<tr>
		                <td class="block" id="block_<?php echo $file['template_id']; ?>" onclick="CampsiteImageDialog.select(<?php echo $file['template_id']; ?>, '<?php echo $file['image_object']->getImageUrl(); ?>', '<?php echo $file['alt']; ?>', '<?php echo $file['alt']; ?>');">
		<a href="javascript:;" onclick="CampsiteImageDialog.select(<?php echo $file['template_id']; ?>, '<?php echo $file['image_object']->getImageUrl(); ?>', '<?php echo $file['alt']; ?>', '<?php echo $file['alt']; ?>');" title="<?php echo $file['alt']; ?>"><img src="<?php echo $file['image_object']->getThumbnailUrl(); ?>" alt="<?php echo $file['alt']; ?>"/></a>
		                </td></tr><tr><td class="edit">
		<?php
		if ($file['image']) {
			echo $file['image'][0].'x'.$file['image'][1];
		}
		else {
			echo " ";
		}
		?>
		                </td>
                        </tr></table></td>
	<?php   } ?>
	  <?php
	}//foreach
}//function drawFiles


/**
 * Draw the directory.
 */
function drawDirs($list, &$manager)
{
	global $relative;

	foreach($list as $path => $dir)
	{ ?>
		<td><table width="100" cellpadding="0" cellspacing="0"><tr><td class="block">
		<a href="images.php?dir=<?php echo rawurlencode($path); ?>" onclick="updateDir('<?php echo $path; ?>')" title="<?php echo $dir['entry']; ?>"><img src="img/folder.gif" height="80" width="80" alt="<?php echo $dir['entry']; ?>" /></a>
		</td></tr><tr>
		<td class="edit">
			<a href="images.php?dir=<?php echo $relative; ?>&amp;deld=<?php echo rawurlencode($path); ?>" title="Trash" onclick="return confirmDeleteDir('<?php echo $dir['entry']; ?>', <?php echo $dir['count']; ?>);"><img src="img/edit_trash.gif" height="15" width="15" alt="Trash"/></a>
			<?php echo $dir['entry']; ?>
		</td>
		</tr></table></td>
	  <?php
	} //foreach
}//function drawDirs


/**
 * No directories and no files.
 */
function drawNoResults()
{
?>
<table width="100%">
  <tr>
    <td class="noResult"><script>document.write(i18n("No Images Found"));</script></td>
  </tr>
</table>
<?php
}

/**
 * No directories and no files.
 */
function drawErrorBase(&$manager)
{
?>
<table width="100%">
  <tr>
    <td class="error">Invalid base directory: <?php echo $manager->config['base_dir']; ?></td>
  </tr>
</table>
<?php
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title>Image List</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="css/imagelist.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="assets/dialog.js"></script>
<script type="text/javascript">
/*<![CDATA[*/

	if(window.top)
		I18N = window.top.I18N;

	function hideMessage()
	{
		var topDoc = window.top.document;
		var messages = topDoc.getElementById('messages');
		if(messages)
			messages.style.display = "none";
	}

	init = function()
	{
		hideMessage();
		var topDoc = window.top.document;

<?php
	//we need to refesh the drop directory list
	//save the current dir, delete all select options
	//add the new list, re-select the saved dir.
	if($refreshDir)
	{
		$dirs = $manager->getDirs();
?>
		var selection = topDoc.getElementById('dirPath');
		var currentDir = selection.options[selection.selectedIndex].text;

		while(selection.length > 0)
		{	selection.remove(0); }

		selection.options[selection.length] = new Option("/","<?php echo rawurlencode('/'); ?>");
		<?php foreach($dirs as $relative=>$fullpath) { ?>
		selection.options[selection.length] = new Option("<?php echo $relative; ?>","<?php echo rawurlencode($relative); ?>");
		<?php } ?>

		for(var i = 0; i < selection.length; i++)
		{
			var thisDir = selection.options[i].text;
			if(thisDir == currentDir)
			{
				selection.selectedIndex = i;
				break;
			}
		}
<?php } ?>
	}

	function editImage(image)
	{
		var url = "editor.php?img="+image;
		Dialog(url, function(param)
		{
			if (!param) // user must have pressed Cancel
				return false;
			else
			{
				return true;
			}
		}, null);
	}

/*]]>*/
</script>
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script type="text/javascript" src="js/campsiteimage.js"></script>
<script type="text/javascript" src="assets/images.js"></script>
</head>

<body>
<?php if ($manager->isValidBase() == false) { drawErrorBase($manager); }
	elseif(count($list[0]) > 0 || count($list[1]) > 0) { ?>
<table>
	<tr>
	<?php drawDirs($list[0], $manager); ?>
	<?php drawFiles($list[1], $manager); ?>
	</tr>
</table>

	<?php
	if (isset($image) && is_object($image)) {
	    if (strpos($_REQUEST['image_id'], '_') === false) {
	        $templateId = $_REQUEST['image_id'];
	    } else {
	        list($templateId, $imageRatio) = explode('_', $_REQUEST['image_id']);
	    }
	    $imageUrl = $image->getImageUrl();
	    if (!empty($imageAltOpt)) {
	        $imageAlt = $imageAltOpt;
	    } else {
	        $imageAlt = $image->getDescription();
	    }
	    if (!empty($imageTitleOpt)) {
	        $imageTitle = $imageTitleOpt;
	    } else {
	        $imageTitle = $image->getDescription();
	    }
	    $imageAlign = (!empty($imageAlignOpt)) ? $imageAlignOpt : '';
	    $imageRatio = (!empty($imageRatioOpt)) ? $imageRatioOpt : '';
        $imageResizeWidth = (!empty($imageResizeWidthOpt)) ? $imageResizeWidthOpt : '';
        $imageResizeHeight = (!empty($imageResizeHeightOpt)) ? $imageResizeHeightOpt : '';
	} else {
	    $firstImage = array_shift($list[1]);
	    if (!empty($firstImage)) {
	        $templateId = $firstImage['template_id'];
            $imageUrl = $firstImage['image_object']->getImageUrl();
            $imageAlt = $firstImage['alt'];
            $imageTitle = $imageAlt;
            $imageAlign = '';
            $imageRatio = '';
            $imageResizeWidth = '';
            $imageResizeHeight = '';
	    }
	}
	?>
	<!-- automatically select the image -->
	<script>
	    CampsiteImageDialog.select(<?php echo $templateId; ?>, '<?php echo $imageUrl; ?>', '<?php echo $imageAlt; ?>', '<?php echo $imageTitle; ?>', '<?php echo $imageAlign; ?>', '<?php echo $imageRatio; ?>', '<?php echo $imageResizeWidth; ?>', '<?php echo $imageResizeHeight; ?>');
	</script>
<?php } else { drawNoResults(); } ?>
</body>
</html>
