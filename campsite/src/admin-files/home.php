<?php
require_once($GLOBALS['g_campsiteDir']."/db_connect.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Publication.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Issue.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Section.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Article.php");
require_once($GLOBALS['g_campsiteDir']."/classes/ArticlePublish.php");
require_once($GLOBALS['g_campsiteDir']."/classes/IssuePublish.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Language.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SimplePager.php");

require_once($GLOBALS['g_campsiteDir'].'/classes/Extension/WidgetContext.php');

require_once($GLOBALS['g_campsiteDir'].'/classes/Extension/WidgetContext.php');

require_once LIBS_DIR . '/ArticleList/ArticleList.php';

camp_load_translation_strings("home");
camp_load_translation_strings("articles");
camp_load_translation_strings("api");

$NumDisplayArticles = 20;

$pendingArticles = ArticlePublish::GetFutureActions($NumDisplayArticles);
$pendingIssues = IssuePublish::GetFutureActions($NumDisplayArticles);
$pendingActions = array_merge($pendingArticles, $pendingIssues);
ksort($pendingActions);
$pendingActions = array_slice($pendingActions, 0, $NumDisplayArticles);

$crumbs = array();
$crumbs[] = array(getGS("Home"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>

<?php
$clearCache = Input::Get('clear_cache', 'string', 'no', true);
if ((CampCache::IsEnabled() || CampTemplateCache::factory()) && ($clearCache == 'yes')
        && $g_user->hasPermission('ClearCache')) {
    // Clear cache engine's cache
    CampCache::singleton()->clear('user');
    CampCache::singleton()->clear();
    SystemPref::DeleteSystemPrefsFromCache();

    // Clear compiled templates
    require_once($GLOBALS['g_campsiteDir']."/template_engine/classes/CampTemplate.php");
    CampTemplate::singleton()->clear_compiled_tpl();

    // Clear template cache storage
    if (CampTemplateCache::factory()) CampTemplateCache::factory()->clean();

    $actionMsg = getGS('Campsite cache was cleaned up');
    $res = 'OK';
}

$syncUsers = Input::Get('sync_users', 'string', 'no', true);
if (($syncUsers == 'yes') && $g_user->hasPermission('SyncPhorumUsers')) {
    require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/users/sync_phorum_users.php");
    $actionMsg = getGS('Campsite and Phorum users were synchronized');
    $res = 'OK';
}
?>

<?php if (!empty($actionMsg)) { ?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
<?php if ($res == 'OK') { ?>
    <td class="info_message" align="center">
<?php } else { ?>
    <td class="error_message" align="center">
<?php } ?>
        <?php echo $actionMsg; ?>
    </td>
</tr>
</table>
<?php } ?>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<h1>Dashboard</h1>
<div id="dashboard">

<div class="column">
<?php
    $context = new WidgetContext('dashboard1');
    $context->setVertical()
        ->render();
?>
</div>

<div class="column">
<?php
    $context = new WidgetContext('dashboard2');
    $context->setVertical()
        ->render();
?>
</div>

<div class="column">
<?php
    $context = new WidgetContext('dashboard3');
    $context->setVertical()
        ->render();
?>
</div>

</div><!-- /#dashboard -->

<h1>Widgets</h1>

<div id="repo">
<?php
    $context = new WidgetContext();
    $context->setHorizontal()
        ->render();
?>
</div>
    
<div style="clear: both;"></div>
<script type="text/javascript">
$(document).ready(function() {
    $('.context').widgets({
        default_context: g_default_context,
    });
});
</script>

    <div id="scheduled_actions">
        <h2><?php putGS('Scheduled Publishing'); ?></h2>
        <!-- Scheduled Publishing -->
        <table cellspacing="0" cellpadding="0" class="table_list" id="scheduled_actions">
        <tr class="table_list_header">
            <td><?php putGS("Scheduled Publishing"); ?></td>
            <td><?php putGS("Event(s)"); ?></td>
            <td><?php putGS("Time"); ?></td>
            <td><?php putGS("Publication"); ?></td>
            <td><?php putGS("Issue"); ?></td>
            <td><?php putGS("Section"); ?></td>
        </tr>
        <?php
        if (count($pendingActions) == 0) {
            ?>
            <tr>
            <td colspan="6" class="list_row_odd"><?php putGS("There are no pending items to be published."); ?></td>
            </tr>
            <?php
        }
        // Warning: the next section is a big hack!
        // Hopefully will be fixed in 2.4
        $color = 0;
        foreach ($pendingActions as $action) {
        if ($action["ObjectType"] == "article") {
            $language = new Language($action["IdLanguage"]);
            $pub = new Publication($action["IdPublication"]);
            $issue = new Issue($action["IdPublication"],
                                $action["IdLanguage"],
                                $action["NrIssue"]);
            $section = new Section($action["IdPublication"],
                                    $action["NrIssue"],
                                    $action["IdLanguage"],
                                    $action["NrSection"]);
            $tmpArticle = new Article($action['IdLanguage'], $action['Number']);
            camp_set_article_row_decoration($tmpArticle, $lockInfo, $rowClass, $color);
            ?>
        <tr class="<?php echo $rowClass ?>">
            <td>
                <?php if ($lockInfo) { ?>
                   <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
                <?php } ?>
                <?php putGS("Article"); ?>:
                <?PHP
                if ($g_user->hasPermission('ChangeArticle')) { ?>
                    <a href="/<?php p($ADMIN); ?>/articles/edit.php?f_publication_id=<?php p($action["IdPublication"]); ?>&f_issue_number=<?php p($action["NrIssue"]); ?>&f_section_number=<?php p($action["NrSection"]); ?>&f_article_number=<?php p($action["Number"]); ?>&f_language_id=<?php p($action["IdLanguage"]); ?>&f_language_selected=<?php p($action["IdLanguage"]); ?>">
                    <?PHP
                }
                echo htmlspecialchars($action["Name"]." (".$language->getNativeName().")");
                if ($g_user->hasPermission('ChangeArticle')) {
                    echo "</a>";
                }
                ?>
            </td>
            <td>
                <?PHP
                $displayActions = array();
                if ($action["publish_action"] == 'P') {
                    $displayActions[] = getGS("Publish");
                }
                if ($action["publish_action"] == 'U') {
                    $displayActions[] = getGS("Unpublish");
                }
                if ($action["publish_on_front_page"] == 'S') {
                    $displayActions[] = getGS("Show on front page");
                }
                if ($action["publish_on_front_page"] == 'R') {
                    $displayActions[] = getGS("Remove from front page");
                }
                if ($action["publish_on_section_page"] == 'S') {
                    $displayActions[] = getGS("Show on section page");
                }
                if ($action["publish_on_section_page"] == 'R') {
                    $displayActions[] = getGS("Remove from section page");
                }
                echo implode("<br>", $displayActions)
                ?>
            </td>

            <td>
                <?php echo htmlspecialchars($action["time_action"]); ?>
            </td>

            <td>
                <?php p(htmlspecialchars($pub->getName())); ?>
            </td>

            <td>
                <?php p(htmlspecialchars($issue->getName())); ?>
            </td>

            <td>
                <?php p(htmlspecialchars($section->getName())); ?>
            </td>

        <?PHP
        }
        elseif ($action["ObjectType"] == "issue") {
          ?><tr <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>><?PHP
            //$language = new Language($action["IdLanguage"]);
            $pub = new Publication($action["IdPublication"]);
            ?>
            <td><?php putGS("Issue"); ?>:
                <?PHP
                if ($g_user->hasPermission('ManageIssue')) { ?>
                    <a href="/<?php p($ADMIN); ?>/issues/edit.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>">
                    <?PHP
                }
                echo htmlspecialchars($action["Name"]);
                if ($g_user->hasPermission('ManageIssue')) {
                    echo "</a>";
                }
                ?>
            </td>
            <td><?PHP
            $displayActions = array();
            if ($action["publish_action"] == 'P') {
                $displayActions[] = getGS("Publish");
            }
            if ($action["publish_action"] == 'U') {
                $displayActions[] = getGS("Unpublish");
            }
            if ($action["do_publish_articles"] == 'Y') {
                $displayActions[] = getGS("Publish articles");
            }
            echo implode("<br>", $displayActions)
            ?></td>
            <td>
                <?php
                if ($g_user->hasPermission("Publish")) { ?>
                    <a href="/<?php p($ADMIN); ?>/issues/autopublish.php?Pub=<?php p($action["IdPublication"]); ?>&Issue=<?php p($action["Number"]); ?>&Language=<?php p($action["IdLanguage"]); ?>&event_id=<?php p(urlencode($action["id"])); ?>">
                    <?PHP
                }
                echo htmlspecialchars($action["time_action"]);
                if ($g_user->hasPermission("Publish")) {
                    echo "</a>";
                }
                ?>
            </td>

            <td>
                <?php p(htmlspecialchars($pub->getName())); ?>
            </td>

            <td> -----</td>
            <td> -----</td>
            <?PHP
        }
        ?>
        </tr>
        <?php
        } // for
        ?>
        </table>
        
    </div>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
