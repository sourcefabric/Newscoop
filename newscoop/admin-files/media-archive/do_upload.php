<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

camp_load_translation_strings("media_archive");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('AddImage')) {
	camp_html_display_error(getGS("You do not have the right to add images."));
	exit;
}

$f_image_url = Input::Get('f_image_url', 'string', '', true);
$nrOfFiles = isset($_POST['uploader_count']) ? $_POST['uploader_count'] : 0;
$f_article_edit = $_POST['f_article_edit'];
$f_publication_id = $_POST['f_publication_id'];
$f_issue_number = $_POST['f_issue_number'];
$f_section_number = $_POST['f_section_number'];
$f_language_id = $_POST['f_language_id'];
$f_language_selected = $_POST['f_language_selected'];
$f_article_number = $_POST['f_article_number'];
$f_place = $_POST['f_place'];

if (empty($f_image_url) && empty($nrOfFiles)) {
	camp_html_add_msg(getGS("You must select an image file to upload."));
	camp_html_goto_page("/$ADMIN/media-archive/add.php");
}

// process image url
if (!empty($f_image_url)) {
    $attributes = array(
        'Description' => '',
        'Photographer' => '',
        'Place' => '',
        'Date' => '',
    );

	if (camp_is_valid_url($f_image_url)) {
		$result = Image::OnAddRemoteImage($f_image_url, $attributes, $g_user->getUserId());
	} else {
		camp_html_add_msg(getGS("The URL you entered is invalid: '$1'", htmlspecialchars($f_image_url)));
	}
}

$images = array();
// process uploaded images
for ($i = 0; $i < $nrOfFiles; $i++) {
    $tmpnameIdx = 'uploader_' . $i . '_tmpname';
    $nameIdx = 'uploader_' . $i . '_name';
    $statusIdx = 'uploader_' . $i . '_status';
    if ($_POST[$statusIdx] == 'done') {
        $result = Image::ProcessFile($_POST[$tmpnameIdx], $_POST[$nameIdx], $g_user->getUserId());
        $images[] = $result;
    }
}

if ($result != NULL) {
    camp_html_add_msg(getGS('"$1" files uploaded.', $nrOfFiles), "ok");
    if ($f_article_edit) {
        require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
        
        foreach ($images as $image) {
            $ImageTemplateId = ArticleImage::GetUnusedTemplateId($f_article_number);
            ArticleImage::AddImageToArticle($image->getImageId(), $f_article_number, $ImageTemplateId);
        }
        
        ?>
        <script type="text/javascript">
        <?php 
            if ($f_place == 1) {
                ?>
                parent.$.fancybox.reload = true;
                document.location = '../image/article/article_number/<?php echo($f_article_number); ?>/language_id/<?php echo($f_language_id); ?>';
                <?php
            }
            else {
                ?>
                try {
                    parent.$.fancybox.reload = true;
                    parent.$.fancybox.message = "<?php putGS("Image added."); ?>";
                    parent.$.fancybox.close();
                } catch (e) {}
                <?php
            }
        ?>
        </script>
        <?php
    }
    else {
        camp_html_goto_page("/$ADMIN/media-archive/multiedit.php");
    }
} else {
    camp_html_add_msg($f_path . DIR_SEP . basename($newFilePath));
    camp_html_goto_page($backLink);
}

?>
