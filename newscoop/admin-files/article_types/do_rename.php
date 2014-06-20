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
    camp_html_display_error($translator->trans("You do not have the right to rename article types.", array(), 'article_types'));
    exit;
}

$f_oldName = trim(Input::get('f_oldName'));
$f_name = trim(Input::Get('f_name'));

if ($f_oldName == $f_name) {
    camp_html_goto_page("/$ADMIN/article_types/");
}

$correct = true;
$created = false;

$errorMsgs = array();
if (empty($f_name)) {
    $correct = false;
    $errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '</B>'.$translator->trans('Name').'</B>'));
} else {
    $valid = ArticleType::IsValidFieldName($f_name);
    if (!$valid) {
        $correct = false;
        $errorMsgs[] = $translator->trans('The $1 field may only contain letters and underscore (_) character.', array('$1' => '</B>'.$translator->trans('Name').'</B>'), 'article_types');
    }

    if ($correct) {
        $old_articleType = new ArticleType($f_oldName);
        if (!$old_articleType->exists()) {
            $correct = false;
            $errorMsgs[] = $translator->trans('The article type $1 does not exist.', array('$1' => '<B>'.htmlspecialchars($f_oldName).'</B>'), 'article_types');
        }
    }

    if ($correct) {
        $articleType = new ArticleType($f_name);
        if ($articleType->exists()) {
            $correct = false;
            $errorMsgs[] = $translator->trans('The article type $1 already exists.', array('$1' => '<B>'. htmlspecialchars($f_name). '</B>'), 'article_types');
        }
    }

    if ($correct) {
        $old_articleType->rename($f_name);

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheService->clearNamespace('article_type');

        \Zend_Registry::get('container')->getService('dispatcher')
            ->dispatch('article_type.hide', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
                'article_type' => $articleType,
                'old_name' => $f_oldName
            )));

        camp_html_goto_page("/$ADMIN/article_types/");
    }
}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($translator->trans("Rename article type $1", array('$1' => $f_oldName), 'article_types'), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
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
    <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/rename.php?f_name=<?php p($f_oldName); ?>'">
    </DIV>
    </TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
