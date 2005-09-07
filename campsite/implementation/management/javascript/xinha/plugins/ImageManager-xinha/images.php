<?
/**
 * Show a list of images in a long horizontal table.
 * @author $Author: paul $
 * @version $Id: images.php,v 1.1 2005/05/03 15:28:07 paul Exp $
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('ddt.php');
require_once('Classes/ImageManager.php');

// uncomment for debugging

// _ddtOn();

//default path is /
$relative = '/';
$manager = new ImageManager($IMConfig);

//process any file uploads
$manager->processUploads();

$manager->deleteFiles();

$refreshDir = false;
//process any directory functions
if($manager->deleteDirs() || $manager->processNewDir())
	$refreshDir = true;

//check for any sub-directory request
//check that the requested sub-directory exists
//and valid
if(isset($_REQUEST['dir']))
{
	$path = rawurldecode($_REQUEST['dir']);
	if($manager->validRelativePath($path))
		$relative = $path;
}


$manager = new ImageManager($IMConfig);


//get the list of files and directories
$list = $manager->getFiles($relative);


/* ================= OUTPUT/DRAW FUNCTIONS ======================= */

/**
 * Draw the files in an table.
 */
function drawFiles($list, &$manager)
{
	global $relative;
	global $IMConfig;

	foreach($list as $entry => $file) 
	{ 
		?>
		<td>

		<? _ddt( __FILE__, __LINE__, "drawFiles(): relative is '" . $file['relative'] . "' thumbnail '" . $manager->getThumbnail($file['relative']) . "'" ); ?>

		<table width="100" cellpadding="0" cellspacing="0"><tr><td class="block">

		<a href="javascript:;" onclick="selectImage('<? echo $file['relative'];?>', '<? echo $entry; ?>', <? echo $file['image'][0];?>, <? echo $file['image'][1]; ?>);"title="<? echo $entry; ?> - <? echo Files::formatSize($file['stat']['size']); ?>"><img src="<? print $manager->getThumbnail($file['relative']); ?>" alt="<? echo $entry; ?> - <? echo Files::formatSize($file['stat']['size']); ?>"/></a>
		</td></tr><tr><td class="edit">
			<a href="<? print $IMConfig['backend_url']; ?>__function=images&dir=<? echo $relative; ?>&amp;delf=<? echo rawurlencode($file['relative']);?>" title="Trash" onclick="return confirmDeleteFile('<? echo $entry; ?>');"><img src="<? print $IMConfig['base_url'];?>img/edit_trash.gif" height="15" width="15" alt="Trash"/></a><a href="javascript:;" title="Edit" onclick="editImage('<? echo rawurlencode($file['relative']);?>');"><img src="<? print $IMConfig['base_url'];?>img/edit_pencil.gif" height="15" width="15" alt="Edit"/></a>
		<? if($file['image']){ echo $file['image'][0].'x'.$file['image'][1]; } else echo $entry;?>
		</td></tr></table></td> 
	  <? 
	}//foreach
}//function drawFiles


/**
 * Draw the directory.
 */
function drawDirs($list, &$manager) 
{
	global $relative;
   global $IMConfig;

	foreach($list as $path => $dir) 
	{ ?>
		<td><table width="100" cellpadding="0" cellspacing="0"><tr><td class="block">
		<a href="images.php?dir=<? echo rawurlencode($path); ?>" onclick="updateDir('<? echo $path; ?>')" title="<? echo $dir['entry']; ?>"><img src="<? print $IMConfig['base_url'];?>img/folder.gif" height="80" width="80" alt="<? echo $dir['entry']; ?>" /></a>
		</td></tr><tr>
		<td class="edit">
			<a href="images.php?dir=<? echo $relative; ?>&amp;deld=<? echo rawurlencode($path); ?>" title="Trash" onclick="return confirmDeleteDir('<? echo $dir['entry']; ?>', <? echo $dir['count']; ?>);"><img src="<? print $IMConfig['base_url'];?>img/edit_trash.gif" height="15" width="15" alt="Trash"/></a>
			<? echo $dir['entry']; ?>
		</td>
		</tr></table></td>
	  <? 
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
    <td class="noResult">No Images Found</td>
  </tr>
</table>
<?	
}

/**
 * No directories and no files.
 */
function drawErrorBase(&$manager) 
{
?>
<table width="100%">
  <tr>
    <td class="error">Invalid base directory: <? echo $manager->config['images_dir']; ?></td>
  </tr>
</table>
<?	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title>Image List</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="<? print $IMConfig['base_url'];?>assets/imagelist.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
_backend_url = "<? print $IMConfig['backend_url']; ?>";
</script>

<script type="text/javascript" src="<? print $IMConfig['base_url'];?>assets/dialog.js"></script>
<script type="text/javascript">
/*<![CDATA[*/

	if(window.top)
		HTMLArea = window.top.HTMLArea;

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

<? 
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
		
		selection.options[selection.length] = new Option("/","<? echo rawurlencode('/'); ?>");	
		<? foreach($dirs as $relative=>$fullpath) { ?>
		selection.options[selection.length] = new Option("<? echo $relative; ?>","<? echo rawurlencode($relative); ?>");		
		<? } ?>
		
		for(var i = 0; i < selection.length; i++)
		{
			var thisDir = selection.options[i].text;
			if(thisDir == currentDir)
			{
				selection.selectedIndex = i;
				break;
			}
		}		
<? } ?>
	}	

	function editImage(image) 
	{
		var url = "<? print $IMConfig['backend_url']; ?>__function=editor&img="+image;
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
<script type="text/javascript" src="<? print $IMConfig['base_url'];?>assets/images.js"></script>
</head>

<body>
<? if ($manager->isValidBase() == false) { drawErrorBase($manager); } 
	elseif(count($list[0]) > 0 || count($list[1]) > 0) { ?>
<table>
	<tr>
	<? drawDirs($list[0], $manager); ?>
	<? drawFiles($list[1], $manager); ?>
	</tr>
</table>
<? } else { drawNoResults(); } ?>
</body>
</html>
