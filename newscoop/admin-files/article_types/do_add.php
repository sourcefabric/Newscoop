<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
    camp_html_display_error($translator->trans("You do not have the right to add article types.", array(), 'article_types'));
    exit;
}

$f_name = trim(Input::Get('f_name'));

$correct = true;
$created = false;
$errorMsgs = array();

if (empty($f_name)) {
    $correct = false;
    $errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '</B>'.$translator->trans('Name')).'</B>');
}

if ($correct) {
    $valid = ArticleType::IsValidFieldName($f_name);
    if (!$valid) {
        $correct = false;
        $errorMsgs[] = $translator->trans('The $1 field may only contain letters and underscore (_) character.', array('$1' => '</B>'.$translator->trans('Name').'</B>'), 'article_types');
    }
}

if ($correct) {

    $articleType = new ArticleType($f_name);
    if ($articleType->exists()) {
        $correct = false;
        $errorMsgs[] = $translator->trans('The article type $1 already exists.', array('$1' => '<B>'.htmlspecialchars($f_name).'</B>'), 'article_types');
    }

    if ($correct) {
        $created = $articleType->create();
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheService->clearNamespace('article_type');
        camp_html_goto_page("/$ADMIN/article_types/fields/add.php?f_article_type=$f_name");
    }
}

$crumbs = array();
$crumbs[] = array($translator->trans('Configure'), "");
$crumbs[] = array($translator->trans('Article Types'), "/$ADMIN/article_types/");
$crumbs[] = array($translator->trans("Adding new article type", array(), 'article_types'), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
    <TD COLSPAN="2">
        <B> <?php  echo $translator->trans("Adding new article type", array(), 'article_types'); ?> </B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <TD COLSPAN="2">
        <BLOCKQUOTE>
        <?php
        foreach ($errorMsgs as $errorMsg) {
            echo "<li>".$errorMsg."</li>";
        }
        ?>
        </BLOCKQUOTE>
    </TD>
</TR>
<TR>
    <TD COLSPAN="2">
    <DIV ALIGN="CENTER">
    <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/add.php'">
    </DIV>
    </TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
