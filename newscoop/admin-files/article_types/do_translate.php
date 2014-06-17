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


$f_type_id = Input::Get('f_type_id');
$f_type_language_id = Input::Get('f_type_language_id', 'int', 0);
$f_type_translation_name = trim(Input::Get('f_type_translation_name'));
$correct = true;
$created = false;

$errorMsgs = array();


if ($f_type_language_id <= 0) {
    $correct = false;
    $errorMsgs[] = $translator->trans('You must choose a language for the article type.', array(), 'article_types');
}

if ($correct) {
    // Translate existing type
    $type = new ArticleType($f_type_id);
    $created = $type->setName($f_type_language_id, $f_type_translation_name);
    if ($created) {
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheService->clearNamespace('article_type');
        
        \Zend_Registry::get('container')->getService('dispatcher')
            ->dispatch('article_type.translate', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
                'article_type' => $type
            )));

        camp_html_goto_page("/$ADMIN/article_types/index.php");
    }
    else {
        $errorMsgs[] = $translator->trans('The translation could not be added.', array(), 'article_types');
    }
}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($translator->trans("Adding new article type", array(), 'article_types'), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
    <TD COLSPAN="2">
        <B> <?php echo $translator->trans("Adding new article type", array(), 'article_types'); ?> </B>
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
    <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/'">
    </DIV>
    </TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
